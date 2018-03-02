<?php
/**
 * @file
 * Contains Drupal\flickr\People.
 */

namespace Drupal\flickr\Service;

use Drupal\flickr\Service\Client;

/**
 * Service class for Flickr People.
 */
class People {

  /**
   * @var \Drupal\flickr\Service\Client
   */
  protected $client;

  /**
   * Constructor for the Flickr People class.
   */
  public function __construct(Client $client) {
    // Flickr Client
    $this->client = $client;
  }

}