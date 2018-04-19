<?php
namespace App\Services;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\ConnectException;
use GuzzleHttp\Exception\RequestException;

/**
 * Class BaseService
 *
 * @package App\Services
 */
class BaseService
{
    /**
     * @var Client
     */
    protected $guzzleClient;

    /**
     * @var string
     */
    public $baseUrl = '';

    /**
     * UsersService constructor.
     * @param Client $guzzleClient
     */
    public function __construct(Client $guzzleClient)
    {
        $this->guzzleClient = $guzzleClient;
    }

    /**
     * @param $url
     * @param $data
     * @param null $jwtToken
     * @return array
     */
    protected function post($url, $data, $jwtToken = null, $headers = [], $jsonBody = false)
    {
        return $this->guzzleRequest('POST', $url, $data, $jwtToken, $headers, $jsonBody);
    }

    protected function patch($url, $data, $jwtToken = null, $headers = [], $jsonBody = false)
    {
        return $this->guzzleRequest('PATCH', $url, $data, $jwtToken, $headers, $jsonBody);
    }

    protected function put($url, $data, $jwtToken = null, $headers = [], $jsonBody = false)
    {
        return $this->guzzleRequest('PUT', $url, $data, $jwtToken, $headers, $jsonBody );
    }

    /**
     * @param $url
     * @param $data
     * @param null $jwtToken
     * @return array
     */
    protected function get($url, $data = [], $jwtToken = null, $headers = [])
    {
        return $this->guzzleRequest('GET', $url, $data, $jwtToken, $headers);
    }

    /**
     * @param $method
     * @param $url
     * @param $data
     * @param $jwtToken
     * @param array $headers
     * @return array
     */
    protected function guzzleRequest($method, $url, $data, $jwtToken, $headers = [], $jsonBody = false)
    {
        if($jsonBody) {
            $dataKey = 'json';
        } else if($method === 'GET') {
            $dataKey = 'query';
        } else {
            $dataKey = 'form_params';
        }

        if (isset($jwtToken)) {
            $headers['Authorization'] = 'Bearer ' . $jwtToken;
        }

        try {
            $response = $this->guzzleClient->request(
                $method,
                $url,
                [
                    $dataKey  => $data,
                    'headers'  => $headers
                ]
            );

            return ['success' => true, 'response' => $response];
        } catch ( ConnectException $exception ) {
            return ['success' => false, 'response' => 'Connection exception', 'exception' => $exception];
        } catch ( \Exception $exception ) {
            $exceptionBody = json_decode($exception->getResponse()->getBody(), true);

            if(is_array($exceptionBody) && array_key_exists('errors', $exceptionBody))
            {
                $errors = $exceptionBody['errors'];
            } else {
                $errors = false;
            }

            return ['success' => false, 'response' => $exceptionBody, 'exception' => $exception, 'errors' => $errors];
        }
    }
}
