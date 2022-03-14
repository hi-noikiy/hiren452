<?php
/**
 * LoginApi
 * PHP version 5
 *
 * @category Class
 * @package  SplititSdkClient
 * @author   Swagger Codegen team
 * @link     https://github.com/swagger-api/swagger-codegen
 */

/**
 * splitit-web-api-public-sdk
 *
 * No description provided (generated by Swagger Codegen https://github.com/swagger-api/swagger-codegen)
 *
 * OpenAPI spec version: 1.0.0
 * 
 * Generated by: https://github.com/swagger-api/swagger-codegen.git
 * Swagger Codegen version: 2.4.12
 */

/**
 * NOTE: This class is auto generated by the swagger code generator program.
 * https://github.com/swagger-api/swagger-codegen
 * Do not edit the class manually.
 */

namespace SplititSdkClient\Api;

use GuzzleHttp\Client;
use GuzzleHttp\ClientInterface;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Psr7\MultipartStream;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\RequestOptions;
use SplititSdkClient\ApiException;
use SplititSdkClient\Model\RequestHeader;
use SplititSdkClient\Configuration;
use SplititSdkClient\HeaderSelector;
use SplititSdkClient\ObjectSerializer;

/**
 * LoginApi Class Doc Comment
 *
 * @category Class
 * @package  SplititSdkClient
 * @author   Splitit
 * @link     https://github.com/Splitit/Splitit.SDKs
 */
class LoginApi
{
    /**
     * @var ClientInterface
     */
    protected $client;

    /**
     * @var Configuration
     */
    protected $config;

    /**
     * @var string
     */
    protected $sessionId;

    protected $culture;

    /**
     * @var HeaderSelector
     */
    protected $headerSelector;

    /**
     * @param Configuration   $config
     * @param string   $sessionId
     * @param HeaderSelector  $selector
     */
    public function __construct(
        Configuration $config = null,
        string $sessionId = null,
        HeaderSelector $selector = null
    ) {
        $this->client = new Client();
        $this->config = $config ?: Configuration::production();
        $this->sessionId = $sessionId;
        $this->headerSelector = $selector ?: new HeaderSelector();
    }

    /**
     * @return Configuration
     */
    public function getConfig()
    {
        return $this->config;
    }

    public function setCulture($culture)
    {
        $this->culture = $culture;
    }

    protected function injectSessionRequestHeaders($request)
    {
        if (!is_null($this->sessionId)){
            $requestHeader = new RequestHeader();

            if (!is_null($this->sessionId)){
                $requestHeader->setSessionId($this->sessionId);
            }

            if (!is_null($this->config->getApiKey())){
                $requestHeader->setApiKey($this->config->getApiKey());
            }

            if (!is_null($this->config->getTouchPoint())){
                $requestHeader->setTouchPoint($this->config->getTouchPoint());
            }

            if (!is_null($this->culture)){
                $requestHeader->setCultureName($this->culture);
            }

            $request->offsetSet('request_header', $requestHeader);
        }
    }

    /**
     * Operation loginPost
     *
     * @param  \SplititSdkClient\Model\LoginRequest $request request (required)
     *
     * @throws \SplititSdkClient\ApiException on non-2xx response
     * @throws \InvalidArgumentException
     * @return \SplititSdkClient\Model\LoginResponse
     */
    public function loginPost($request)
    {
        list($response) = $this->loginPostWithHttpInfo($request);
        return $response;
    }

    /**
     * Operation loginPostWithHttpInfo
     *
     * @param  \SplititSdkClient\Model\LoginRequest $request (required)
     *
     * @throws \SplititSdkClient\ApiException on non-2xx response
     * @throws \InvalidArgumentException
     * @return array of \SplititSdkClient\Model\LoginResponse, HTTP status code, HTTP response headers (array of strings)
     */
    public function loginPostWithHttpInfo($request)
    {
        $returnType = '\SplititSdkClient\Model\LoginResponse';
        $request = $this->loginPostRequest($request);

        try {
            $options = $this->createHttpClientOption();
            try {
                $response = $this->client->send($request, $options);
            } catch (RequestException $e) {
                throw new ApiException(
                    "[{$e->getCode()}] {$e->getMessage()}",
                    $e->getCode(),
                    $e->getResponse() ? $e->getResponse()->getHeaders() : null,
                    $e->getResponse() ? $e->getResponse()->getBody()->getContents() : null
                );
            }

            $statusCode = $response->getStatusCode();

            if ($statusCode < 200 || $statusCode > 299) {
                throw new ApiException(
                    sprintf(
                        '[%d] Error connecting to the API (%s)',
                        $statusCode,
                        $request->getUri()
                    ),
                    $statusCode,
                    $response->getHeaders(),
                    $response->getBody()
                );
            }

            $responseBody = $response->getBody();
            if ($returnType === '\SplFileObject') {
                $content = $responseBody; //stream goes to serializer
            } else {
                $content = $responseBody->getContents();
                if ($returnType !== 'string') {
                    $content = json_decode($content);
                }
            }

            $result = ObjectSerializer::deserialize($content, $returnType, []);

            if (!$result->getResponseHeader()->getSucceeded()){
                throw ApiException::splitit($result->getResponseHeader());
            }

            return [
                $result,
                $response->getStatusCode(),
                $response->getHeaders()
            ];


        } catch (ApiException $e) {
            switch ($e->getCode()) {
                case 200:
                    $data = ObjectSerializer::deserialize(
                        $e->getResponseBody(),
                        '\SplititSdkClient\Model\LoginResponse',
                        $e->getResponseHeaders()
                    );
                    $e->setResponseObject($data);
                    break;
            }
            throw $e;
        }
    }

    /**
     * Operation loginPostAsync
     *
     * 
     *
     * @param  \SplititSdkClient\Model\LoginRequest $request (required)
     *
     * @throws \InvalidArgumentException
     * @return \GuzzleHttp\Promise\PromiseInterface
     */
    public function loginPostAsync($request)
    {
        return $this->loginPostAsyncWithHttpInfo($request)
            ->then(
                function ($response) {
                    return $response[0];
                }
            );
    }

    /**
     * Operation loginPostAsyncWithHttpInfo
     *
     * 
     *
     * @param  \SplititSdkClient\Model\LoginRequest $request (required)
     *
     * @throws \InvalidArgumentException
     * @return \GuzzleHttp\Promise\PromiseInterface
     */
    public function loginPostAsyncWithHttpInfo($request)
    {
        $returnType = '\SplititSdkClient\Model\LoginResponse';
        $request = $this->loginPostRequest($request);

        return $this->client
            ->sendAsync($request, $this->createHttpClientOption())
            ->then(
                function ($response) use ($returnType) {
                    $responseBody = $response->getBody();
                    if ($returnType === '\SplFileObject') {
                        $content = $responseBody; //stream goes to serializer
                    } else {
                        $content = $responseBody->getContents();
                        if ($returnType !== 'string') {
                            $content = json_decode($content);
                        }
                    }

                    $result = ObjectSerializer::deserialize($content, $returnType, []);

                    if (!$result->getResponseHeader()->getSucceeded()){
                        throw ApiException::splitit($result->getResponseHeader());
                    }

                    return [
                        $result,
                        $response->getStatusCode(),
                        $response->getHeaders()
                    ];

                },
                function ($exception) {
                    $response = $exception->getResponse();
                    $statusCode = $response->getStatusCode();
                    throw new ApiException(
                        sprintf(
                            '[%d] Error connecting to the API (%s)',
                            $statusCode,
                            $exception->getRequest()->getUri()
                        ),
                        $statusCode,
                        $response->getHeaders(),
                        $response->getBody()
                    );
                }
            );
    }

    /**
     * Create request for operation 'loginPost'
     *
     * @param  \SplititSdkClient\Model\LoginRequest $request (required)
     *
     * @throws \InvalidArgumentException
     * @return \GuzzleHttp\Psr7\Request
     */
    protected function loginPostRequest($request)
    {
        // verify the required parameter 'request' is set
        if ($request === null || (is_array($request) && count($request) === 0)) {
            throw new \InvalidArgumentException(
                'Missing the required parameter $request when calling loginPost'
            );
        }

        $resourcePath = '/api/Login';
        $formParams = [];
        $queryParams = [];
        $headerParams = [];
        $httpBody = '';
        $multipart = false;



        // body params
        $_tempBody = null;
        if (isset($request)) {
            $this->injectSessionRequestHeaders($request);
            $_tempBody = $request;
        }

        if ($multipart) {
            $headers = $this->headerSelector->selectHeadersForMultipart(
                ['text/plain', 'application/json', 'text/json']
            );
        } else {
            $headers = $this->headerSelector->selectHeaders(
                ['text/plain', 'application/json', 'text/json'],
                ['application/json-patch+json', 'application/json', 'application/_*+json']
            );
        }

        // for model (json/xml)
        if (isset($_tempBody)) {
            // $_tempBody is the method argument, if present
            $httpBody = $_tempBody;
            
            if($headers['Content-Type'] === 'application/json') {
                // \stdClass has no __toString(), so we should encode it manually
                if ($httpBody instanceof \stdClass) {
                    $httpBody = \GuzzleHttp\json_encode($httpBody);
                }
                // array has no __toString(), so we should encode it manually
                if(is_array($httpBody)) {
                    $httpBody = \GuzzleHttp\json_encode(ObjectSerializer::sanitizeForSerialization($httpBody));
                }
            }
        } elseif (count($formParams) > 0) {
            if ($multipart) {
                $multipartContents = [];
                foreach ($formParams as $formParamName => $formParamValue) {
                    $multipartContents[] = [
                        'name' => $formParamName,
                        'contents' => $formParamValue
                    ];
                }
                // for HTTP post (form)
                $httpBody = new MultipartStream($multipartContents);

            } elseif ($headers['Content-Type'] === 'application/json') {
                $httpBody = \GuzzleHttp\json_encode($formParams);

            } else {
                // for HTTP post (form)
                $httpBody = \GuzzleHttp\Psr7\build_query($formParams);
            }
        }


        $defaultHeaders = [];
        if ($this->config->getUserAgent()) {
            $defaultHeaders['User-Agent'] = $this->config->getUserAgent();
        }

        $headers = array_merge(
            $defaultHeaders,
            $headerParams,
            $headers
        );

        $query = \GuzzleHttp\Psr7\build_query($queryParams);
        return new Request(
            'POST',
            $this->config->getHost() . $resourcePath . ($query ? "?{$query}" : ''),
            $headers,
            $httpBody
        );
    }

    /**
     * Create http client option
     *
     * @throws \RuntimeException on file opening failure
     * @return array of http client options
     */
    protected function createHttpClientOption()
    {
        $options = [];
        if ($this->config->getDebug()) {
            $options[RequestOptions::DEBUG] = fopen($this->config->getDebugFile(), 'a');
            if (!$options[RequestOptions::DEBUG]) {
                throw new \RuntimeException('Failed to open the debug file: ' . $this->config->getDebugFile());
            }
        }

        return $options;
    }
}
