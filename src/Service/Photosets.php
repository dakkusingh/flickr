<?php
/**
 * @file
 * Contains Drupal\flickr\Photosets.
 */

namespace Drupal\flickr\Service;

use Drupal\flickr\Service\Client;

/**
 * Service class for Flickr Photosets.
 */
class Photosets {

  /**
   * @var \Drupal\flickr\Service\Client
   */
  protected $client;

  /**
   * Constructor for the Flickr Photosets class.
   */
  public function __construct(Client $client) {
    // Flickr Client
    $this->client = $client;
  }

}