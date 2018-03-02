<?php
/**
 * @file
 * Contains Drupal\flickr\FlickrClient.
 */

namespace Drupal\flickr\Service;

use Drupal\Core\Config\ConfigFactory;
use GuzzleHttp\Client;

/**
 * Service class for FlickrClient.
 */
class FlickrClient {

  /**
   * @var \Drupal\Core\Config\Config
   */
  protected $config;

  /**
   * Constructor for the FlickrClient class.
   */
  public function __construct(ConfigFactory $config_factory) {
    // Get the config.
    $this->config = $config_factory->get('flickr.settings');

    $this->api_uri = $this->config->get('api_uri');
    $this->host_uri = $this->config->get('host_uri');
    $this->api_key = $this->config->get('api_key');
    $this->api_secret = $this->config->get('api_secret');

    $this->client = new Client([
      'base_uri' => $this->api_uri
    ]);

  }

  /**
   * Submit a request to Flickr.
   *
   * @param string $method
   *   String method name.
   * @param string $args
   *   Associative array of arguments names and values.
   * @param string $cacheable
   *   Boolean indicating if it's safe cache the results of this request.
   *
   * @return array
   *   an array with the the result of the request, or FALSE on error.
   */
  public function flickrRequest($method, $args, $cacheable = TRUE) {
    // Build the arg_hash.
    $args = $this->flickrBuildArgs($args, $method);
    $arg_hash = $this->flickrBuildArgHash($args);

    // If we've got a secret, sign the arguments.
    if ($secret = $this->api_secret) {
      $args['api_sig'] = md5($secret . $arg_hash);
    }

    $response = $this->flickrDoRequest('', $args);
    // TODO Implement Drupal 8 cache.

    return $response;
  }

  /**
   * @param $args
   * @param $method
   * @param string $format
   *
   * @return mixed
   */
  private function flickrBuildArgs($args, $method, $format = 'json') {
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
  private function flickrBuildArgHash($args) {
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
  public function flickrDoRequest($url, $parameters = [], $requestMethod = 'GET') {
    $response = $this->client->request($requestMethod, $url, ['query' => $parameters]);

    // TODO Error checking can be improved.
    if ($response->getStatusCode() == !200) {
      return FALSE;
    }

    // TODO Add some checking.
    $body = $response->getBody();

    // TODO Add some checking.
    return json_decode((string) $body);
  }

}