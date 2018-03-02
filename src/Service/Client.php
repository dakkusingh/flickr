<?php
/**
 * @file
 * Contains Drupal\flickr\Client.
 */

namespace Drupal\flickr\Service;

use Drupal\Core\Config\ConfigFactory;
use GuzzleHttp\Client as GuzzleClient;

/**
 * Service class for Client.
 */
class Client {

  /**
   * @var \Drupal\Core\Config\Config
   */
  protected $config;

  /**
   * Constructor for the Client class.
   */
  public function __construct(ConfigFactory $config) {
    // Get the config.
    $this->config = $config->get('flickr.settings');

    $this->api_uri = $this->config->get('api_uri');
    $this->host_uri = $this->config->get('host_uri');
    $this->api_key = $this->config->get('api_key');
    $this->api_secret = $this->config->get('api_secret');

    $this->client = new GuzzleClient([
      'base_uri' => $this->api_uri
    ]);

  }


  /**
   * @param $method
   * @param array $args
   * @param bool $cacheable
   *
   * @return bool|mixed
   */
  public function request($method, $args, $cacheable = TRUE) {
    // Build the arg_hash.
    $args = $this->buildArgs($args, $method);
    $arg_hash = $this->buildArgHash($args);

    // If we've got a secret, sign the arguments.
    if ($secret = $this->api_secret) {
      $args['api_sig'] = md5($secret . $arg_hash);
    }

    $response = $this->doRequest('', $args);
    // TODO Implement Drupal 8 cache.

    if ($response) {
      return $response;
    }

    return FALSE;
  }

  /**
   * @param $args
   * @param $method
   * @param string $format
   *
   * @return mixed
   */
  private function buildArgs($args, $method, $format = 'json') {
    // Add in additional parameters then sort them for signing.
    $args['api_key'] = $this->api_key;
    $args['method'] = $method;
    $args['format'] = 'json';
    $args['nojsoncallback'] = 1;
    ksort($args);

    return $args;
  }

  /**
   * @param $args
   *
   * @return string
   */
  private function buildArgHash($args) {
    // Build an argument hash API signing (we'll also use it for the cache id).
    $arg_hash = '';

    foreach ($args as $k => $v) {
      $arg_hash .= $k . $v;
    }

    return $arg_hash;
  }

  /**
   * @param $url
   * @param array $parameters
   * @param string $requestMethod
   *
   * @return bool|mixed
   */
  private function doRequest($url, $parameters = [], $requestMethod = 'GET') {
    $response = $this->client->request($requestMethod, $url, ['query' => $parameters]);

    // TODO Error checking can be improved.
    if ($response->getStatusCode() == !200) {
      return FALSE;
    }

    // TODO Add some checking.
    $body = $response->getBody();

    // TODO Add some checking.
    return json_decode((string) $body, TRUE);
  }

}