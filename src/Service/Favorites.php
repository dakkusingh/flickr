<?php

namespace Drupal\flickr\Service;

/**
 * Service class for Flickr Favorites.
 */
class Favorites {

  /**
   * @var \Drupal\flickr\Service\Client
   */
  protected $client;

  /**
   * Constructor for the Flickr Favorites class.
   */
  public function __construct(Client $client) {
    // Flickr Client.
    $this->client = $client;
  }

}
