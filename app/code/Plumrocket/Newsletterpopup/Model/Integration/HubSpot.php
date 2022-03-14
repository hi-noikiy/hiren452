<?php
/**
 * Plumrocket Inc.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the End-user License Agreement
 * that is available through the world-wide-web at this URL:
 * http://wiki.plumrocket.net/wiki/EULA
 * If you are unable to obtain it through the world-wide-web, please
 * send an email to support@plumrocket.com so we can send you a copy immediately.
 *
 * @package     Plumrocket_Newsletterpopup
 * @copyright   Copyright (c) 2018 Plumrocket Inc. (http://www.plumrocket.com)
 * @license     http://wiki.plumrocket.net/wiki/EULA  End-user License Agreement
 */

namespace Plumrocket\Newsletterpopup\Model\Integration;

/**
 * Class HubSpot
 * Integration for https://www.hubspot.com
 */
class HubSpot extends \Plumrocket\Newsletterpopup\Model\AbstractIntegration
{
    /**
     * Identifier of integration
     */
    const INTEGRATION_ID = 'hubspot';

    /**
     * {@inheritdoc}
     */
    public function initFromSystemConfig()
    {
        parent::initFromSystemConfig();
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getIntegrationId()
    {
        return self::INTEGRATION_ID;
    }

    /**
     * @return bool
     */
    public function canUseGeneralContactList()
    {
        return true;
    }

    /**
     * Add email to list
     * If list not specified $listIds will be get from magento configuration
     *
     * @param $email
     * @param null $data
     * @param null $listIds
     * @return array|bool
     */
    public function addContactToList($email, $listIds, $data = null)
    {
        if (null === $listIds) {
            return false;
        }

        $email = trim($email);
        $listIds = is_array($listIds) ? $listIds : [(string)$listIds];

        if (! empty($email) && ! empty($listIds)) {
            $contactId = $this->addContact($email, $data);

            if ($contactId) {
                $result = [];

                foreach ($listIds as $listId) {
                    if ($this->canSkipContactList($listId)) {
                        continue;
                    }

                    $requestData = [
                        'vids' => [$contactId],
                    ];
                    $apiResult = $this->callAPIMethod(
                        '/contacts/v1/lists/' . urlencode($listId) . '/add',
                        $requestData,
                        'POST'
                    );

                    $responseData = $apiResult ? array_merge($apiResult['updated'], $apiResult['discarded']) : [];

                    if (in_array($contactId, $responseData)) {
                        $result[] = $listId;
                    } else {
                        $this->logFailAddContactToList($apiResult, $email, $listId);
                    }
                }

                return ! empty($result) ? [$contactId => $result] : false;
            }
        }

        return false;
    }

    /**
     * Add contact to service
     *
     * @param $email
     * @param null $data
     * @return bool|string
     */
    public function addContact($email, $data = null)
    {
        if (! empty($email)) {
            $properties = $this->preparePropertiesForContact($data);
            $properties[] = [
                'property' => 'email',
                'value' => (string)$email,
            ];

            $apiResult = $this->callAPIMethod(
                '/contacts/v1/contact',
                ['properties' => $properties],
                'POST'
            );

            if (! empty($apiResult['vid'])) {
                return (string)$apiResult['vid'];
            }

            if (! empty($apiResult['status'])
                && ($apiResult['status'] == 'error')
                && ! empty($apiResult['identityProfile']['vid'])
            ) {
                return (string)$apiResult['identityProfile']['vid'];
            } else {
                $this->logFailAddContact($apiResult, $email);
            }
        }

        return false;
    }

    /**
     * @param $email
     * @param null $data
     * @return array|bool|mixed|string
     */
    public function addContactToSelectedLists($email, $data = null)
    {
        try {
            $lists = $this->getSelectedLists();

            return ! empty($lists)
                ? $this->addContactToList($email, $lists, $data)
                : $this->addContact($email, $data);
        } catch (\Exception $e) {
            $this->logErrorAddContactToSelectedLists($email, $e->getMessage());
        }

        return false;
    }

    /**
     * Retrieve array of lists
     *
     * @return array
     */
    public function getAllLists()
    {
        if (null === $this->allLists) {
            $this->allLists = [];
            $apiResult = $this->callAPIMethod('/contacts/v1/lists/static', [
                'count' => 250,
            ]);

            if ($apiResult
                && ! empty($apiResult['lists'])
                && is_array($apiResult['lists'])
            ) {
                foreach ($apiResult['lists'] as $list) {
                    $this->allLists[$list['listId']] = $this->prepareListLabel($list);
                    $this->listSubscribersCount[$list['listId']] = $this->prepareSubscribersCount($list);
                }
            } else {
                $this->logFailGetAllLists($apiResult);
            }
        }

        return $this->allLists;
    }

    /**
     * Retrieve data of account
     *
     * @return bool|array
     */
    public function getAccountInfo()
    {
        return $this->callAPIMethod('/integrations/v1/me');
    }

    /**
     * {@inheritdoc}
     */
    public function callAPIResource($url, $params = null, $method = "GET", $encodeParams = null)
    {
        $method = mb_strtoupper($method);
        $params = ! empty($params) && is_array($params) ? $params : [];

        if ("GET" === $method) {
            $url .= '&' . http_build_query($params);
            $params = null;
        }

        if (null === $encodeParams) {
            $encodeParams = self::DATA_FORMAT_JSON;
        }

        return $this->sendRequestByCurl($url, $params, $encodeParams);
    }

    /**
     * Prepare API URL and params by API method
     *
     * @param $apiMethodName
     * @param null $params
     * @param string $method
     * @return mixed
     */
    public function callAPIMethod($apiMethodName, $params = null, $method = "GET")
    {
        $this->setApiEndpoint($apiMethodName);
        $url = $this->getBaseApiUrl() . '?hapikey=' . $this->getApiKey();
        $params = ! empty($params) && is_array($params) ? $params : [];

        return $this->callAPIResource($url, $params, $method);
    }

    /**
     * Retrieve prepared label for list
     *
     * @param $list
     * @return \Magento\Framework\Phrase|string
     */
    private function prepareListLabel($list)
    {
        return ! empty($list['name']) ? (string)$list['name'] : __('Unknown List');
    }

    /**
     * Retrieve prepared subscribers count for list
     *
     * @param $list
     * @return int
     */
    private function prepareSubscribersCount($list)
    {
        return ! empty($list['metaData']['size']) ? (int)$list['metaData']['size'] : 0;
    }

    /**
     * @param $data
     * @return array
     */
    private function preparePropertiesForContact($data)
    {
        $result = [];
        $data = $this->prepareDataForContact($data);

        foreach ($data as $property => $value) {
            if ('email' == mb_strtolower($property)) {
                continue;
            }

            $result[] = [
                'property' => $property,
                'value' => (string)$value,
            ];
        }

        return $result;
    }
}
