<?php

declare(strict_types=1);

namespace App\Curl;

/**
 * Provides direct communication with API
 *
 * @since  1.0.0
 * @package App\Curl
 */
class CurlHandler
{

  /**
   * @var string defaultUrl is default server URL for all API requests
   */
  private string $defaultUrl;

  /**
   * Constructor for CurlManager
   *
   * @param  string  $defaultUrl  server URL
   */
  public function __construct(string $defaultUrl)
  {
    $this->defaultUrl = $defaultUrl;
  }

  /**
   * Posts data with HTTP POST
   *
   * @param array $data Data that are being send
   * @param string $endPoint API endpoint
   *
   * @return array Response
   * @throws CurlException
   */
  public function postData(string $endPoint, array $data): array
  {
    return $this->callCurl('POST', $endPoint, $data);
  }

  /**
   * Provides cURL methods
   *
   * @param string $typeOfMethod Type of HTTP method
   * @param string $endPoint API endpoint
   * @param array|null $data Optional for insert data
   *
   * @return array Response
   * @throws CurlException
   */
  private function callCurl(string $typeOfMethod, string $endPoint, array $data = null): array
  {
    $url = $this->defaultUrl.$endPoint;
    $ch = curl_init();

    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $typeOfMethod);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    // if $data is null we call HTTP GET or DELETE
    if ($data !== null) {
      $data = json_encode($data);
      curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
      curl_setopt($ch, CURLOPT_HTTPHEADER,
        array(
          'Content-Type: application/json',
          'Content-Length: '.strlen($data)
        ));
    }
    $response = curl_exec($ch);
    $this->checkErrorCurl($ch);
    curl_close($ch);
    return json_decode($response, true);
  }

  /**
   * Checks cURL communication if any error occurs
   *
   * @param $ch Handles communication
   *
   * @throws CurlException If communication is unstable or HTTP method is wrong
   */
  private function checkErrorCurl($ch): void
  {
    if (!curl_errno($ch)) {
      $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
      if (!$httpCode === 200) {
        throw new CurlException('Unexpected error while calling API, code:'.$httpCode);
      }
    }
  }

  /**
   * Inserts data with HTTP PUT
   *
   * @param array $data Data that are send
   * @param string $endPoint API endpoint
   *
   * @return array Response
   * @throws CurlException
   */
  public function putData(string $endPoint, array $data): array
  {
    return $this->callCurl('PUT', $endPoint, $data);
  }

  /**
   * Gets data with HTTP GET
   *
   * @param string $endPoint API endpoint
   *
   * @return array Response
   * @throws CurlException
   */
  public function getData(string $endPoint): array
  {
    return $this->callCurl('GET', $endPoint);
  }
}
