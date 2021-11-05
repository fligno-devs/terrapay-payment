<?php

namespace FlignoDevs\TerrapayPayment;

use FlignoDevs\TerrapayPayment\Models\TerrapayAuthModel;
use GuzzleHttp\Promise\PromiseInterface;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;

/**
 * Class TerrapayPayment
 * @package FlignoDevs\TerrapayPayment
 *
 * @author James Carlo Luchavez
 * @since 2021-11-04
 */
class TerrapayPayment
{
    /**
     * @var string
     */
    protected string $apiKey;

    /**
     * @var string
     */
    protected string $sha256ApiKey;

    /**
     * @var string
     */
    protected string $apiUrl;

    /**
     * @var string
     */
    protected string $environment;

    public const PRODUCTION = 'production';
    public const SANDBOX = 'sandbox';

    public function __construct()
    {
        $this->setEnvironment($this->getEnvironment());
    }

    /**
     * @return string
     */
    public function getEnvironment(): string
    {
        return $this->environment ?? config('terrapay-payment.environment');
    }

    /**
     * @param string $environment
     */
    public function setEnvironment(string $environment): void
    {
        if (strcmp(strtolower(trim($environment)), self::PRODUCTION) === 0) {
            $this->environment = self::PRODUCTION;
        }
        else {
            $this->environment = self::SANDBOX;
        }

        $this->setApiUrl(config('terrapay-payment.' . $this->environment . '.api_url'));
        $this->setApiKey(config('terrapay-payment.' . $this->environment . '.api_key'));

        $this->setSha256ApiKey();
    }

    /**
     * @param string $apiKey
     */
    public function setApiKey(string $apiKey): void
    {
        $this->apiKey = $apiKey;
    }

    /**
     * @return string
     */
    public function getApiKey(): string
    {
        return $this->apiKey;
    }

    /**
     * @param string $apiUrl
     */
    public function setApiUrl(string $apiUrl): void
    {
        $this->apiUrl = $apiUrl;
    }

    /**
     * @return string
     */
    public function getApiUrl(): string
    {
        return $this->apiUrl;
    }

    /**
     */
    public function setSha256ApiKey(): void
    {
        $this->sha256ApiKey = hash('sha256', $this->getApiKey());
    }

    /**
     * @return string
     */
    public function getSha256ApiKey(): string
    {
        if (!$this->sha256ApiKey) {
            $this->setSha256ApiKey();
        }

        return $this->sha256ApiKey;
    }

    /**
     * @param bool $is_post_method
     * @param string $append_url
     * @param array $data
     * @return PromiseInterface|Response
     */
    public function makeRequest(bool $is_post_method = FALSE, string $append_url = '', array $data = [])
    {
        // Prepare URL

        $url = $this->getApiUrl();

        if(empty(trim($append_url)) === FALSE) {
            $url .= '/' . $append_url;
        }

        // Prepare Data

        $data = array_filter_recursive($data);

        // Prepare API Key

        $apiKey = $this->getSha256ApiKey();

        // Prepare HTTP Options

        $httpOptions = [
            'verify' => config('terrapay-payment.verify_ssl'), // Note: Read https://docs.guzzlephp.org/en/stable/request-options.html#verify
        ];

        // Prepare HTTP call

        $response = Http::withOptions($httpOptions)->withHeaders(['Authorization' => $apiKey])->bodyFormat('json');

        // Initiate HTTP call

        if ($is_post_method) {
            $response = $response->post($url, $data);
        }
        else {
            $response = $response->get($url, $data);
        }

        return $response;
    }

    /**
     * Verify Auth
     *
     * To verify if the API key is working properly and is still valid.
     * Partners can use this periodically to check and verify the API key and change it if required.
     *
     * @return TerrapayAuthModel
     */
    public function verifyAuth(): TerrapayAuthModel
    {
        return new TerrapayAuthModel($this->makeRequest(false, 'eig/verifyAuth'));
    }

    /**
     * Modify Auth
     *
     * Modifies the secret key.
     * To do this the existing secret key must be valid and should not have expired.
     * By default, the secret key is set to expire after a period of 60 days.
     *
     * @return TerrapayAuthModel
     */
    public function modifyAuth(): TerrapayAuthModel
    {
        return new TerrapayAuthModel($this->makeRequest(true, 'eig/modifyAuth'));
    }
}
