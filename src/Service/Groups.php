<?php
/**
 * @file
 * Contains Drupal\flickr\Groups.
 */

namespace Drupal\flickr\Service;

use Drupal\flickr\Service\Client;

/**
 * Service class for Flickr Groups.
 */
class Groups {

  /**
   * @var \Drupal\flickr\Service\Client
   */
  protected $client;

  /**
   * Constructor for the Flickr Groups class.
   */
  public function __construct(Client $client) {
    // Flickr Client
    $this->client = $client;
  }

}