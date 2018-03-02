<?php
/**
 * @file
 * Contains Drupal\flickr\Galleries.
 */

namespace Drupal\flickr\Service;

use Drupal\flickr\Service\Client;

/**
 * Service class for Flickr Galleries.
 */
class Galleries {

  /**
   * @var \Drupal\flickr\Service\Client
   */
  protected $client;

  /**
   * Constructor for the Flickr Galleries class.
   */
  public function __construct(Client $client) {
    // Flickr Client
    $this->client = $client;
  }

}