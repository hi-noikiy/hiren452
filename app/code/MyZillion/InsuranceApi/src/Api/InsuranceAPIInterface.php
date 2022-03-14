<?php

namespace MyZillion\InsuranceApi\Api;

/**
 * Insurance API Interface
 *
 * @author Serfe SA <info@serfe.com>
 */
interface InsuranceAPIInterface
{
    const PRODUCTION_API_URL = 'https://api.myzillion.com/';
    const STAGING_API_URL = 'https://sandbox.api.myzillion.com/';
    const OFFER_ENDPOINT = 'ecommerce/v1/offer';
    const ORDER_POST_ENDPOINT = 'ecommerce/v1/order';

    /**
     * Wrapper to call /v1/offer endpoint
     *  More info about this enpdoint: https://staging.api.myzillion.com/swagger/index.html#/Offer/createOffer
     *   - https://gist.github.com/zillion-integrations/8e3a7d2e4328554dff6eb2defa308743
     *
     * @param array $data       Data that will be send to the endpoint
     * @return array            Endpoint response
     */
    public function getOffer($data);

    /**
     * Wrapper to call /pos/v1/order endpoint
     *  More info about this enpdoint: https://gist.github.com/zillion-integrations/09545fd84c307d30992c4ecaf351b835
     *
     * @param array $data       Data that will be send to the endpoint
     * @return array            Endpoint response
     */
    public function postOrder($data);
}
