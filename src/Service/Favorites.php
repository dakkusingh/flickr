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

  /**
   * Returns a list of favorite public photos for the given user.
   *
   * @param string $nsid
   *   NSID of the user whose photoset tags will be returned.
   * @param string $page
   *   Page of results to return.
   *
   * @return array
   *   Response from the flickr method flickr.favorites.getPublicList.
   *   (https://www.flickr.com/services/api/flickr.favorites.getPublicList.html)
   */
  function favoritesGetPublicList($nsid, $page = 1, $other_args = []) {
    $args = [
      'user_id' => $nsid,
      'page' => $page,
    ];

    array_merge($args, $other_args);

    // Set per_page to flickr module default if not specified in $args.
    if (!isset($args['per_page'])) {
      // TODO Expose pager as a setting.
      $args['per_page'] = 6;
    }

    $response = $this->client->request(
      'flickr.favorites.getPublicList',
      $args
    );

    if ($response) {
      return $response['photos'];
    }

    return FALSE;
  }

}
