<?php

namespace Drupal\flickr\Service;

/**
 * Service class for Flickr Tags.
 */
class Tags {

  /**
   * @var \Drupal\flickr\Service\Client
   */
  protected $client;

  /**
   * Constructor for the Flickr Tags class.
   */
  public function __construct(Client $client) {
    // Flickr Client.
    $this->client = $client;
  }

}
