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
 * @copyright   Copyright (c) 2019 Plumrocket Inc. (http://www.plumrocket.com)
 * @license     http://wiki.plumrocket.net/wiki/EULA  End-user License Agreement
 */

namespace Plumrocket\Newsletterpopup\Model\Mailchimp;

class Error extends \Magento\Framework\Exception\LocalizedException
{
    /**
     * @var string
     */
    protected $url;
    /**
     * @var string
     */
    protected $title;
    /**
     * @var string
     */
    protected $detail;
    /**
     * @var string
     */
    protected $method;
    /**
     * @var array
     */
    protected $errors;
    /**
     * @var string
     */
    protected $params;

    /**
     * Error constructor.
     *
     * @param        $url
     * @param string $method
     * @param string $params
     * @param string $title
     * @param string $detail
     * @param null   $errors
     */
    public function __construct($url, $method = '', $params = '', $title = '', $detail = '', $errors = null)
    {
        $titleComplete = $title . ' for Api Call: ' . $url;
        parent::__construct(__($titleComplete . ' - ' . $detail));
        $this->url = $url;
        $this->title = $title;
        $this->detail = $detail;
        $this->method = $method;
        $this->errors = $errors;
        $this->params = $params;
    }

    /**
     * @return string
     */
    public function getFriendlyMessage()
    {
        $friendlyMessage = $this->title . ' for Api Call: [' . $this->url . '] using method [' . $this->method . "]\n";
        $friendlyMessage .= "\tDetail: [" . $this->detail . "]\n";
        if (is_array($this->errors)) {
            $errorMessage = '';
            foreach ($this->errors as $error) {
                $field = array_key_exists('field', $error) ? $error['field'] : '';
                $message = array_key_exists('message', $error) ? $error['message'] : '';
                $line = "\t\t field [$field] : $message\n";
                $errorMessage .= $line;
            }
            $friendlyMessage .= "\tErrors:\n" . $errorMessage;
        }
        $lineParams = "\tParams:\n";
        if (is_array($this->params)) {
            if (! empty($this->params)) {
                $lineParams .= "\t\t" . json_encode($this->params);
            } else {
                $lineParams = '';
            }
        } else {
            $lineParams = $this->params;
        }
        $friendlyMessage .= $lineParams;
        return $friendlyMessage;
    }

    /**
     * @return string
     */
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * @return string
     */
    public function getDetail()
    {
        return $this->detail;
    }

    /**
     * @return string
     */
    public function getMethod()
    {
        return $this->method;
    }

    /**
     * @return array|null
     */
    public function getErrors()
    {
        return $this->errors;
    }
}
