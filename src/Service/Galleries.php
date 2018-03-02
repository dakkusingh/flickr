<?php

namespace Drupal\flickr\Service;

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
    // Flickr Client.
    $this->client = $client;
  }

  /**
   * Returns info about a given gallery.
   *
   * @param string $id
   *   NSID of the gallery whose photos you want.
   *
   * @return array
   *   Response from the flickr method flickr.gallery.getInfo.
   *   (https://www.flickr.com/services/api/flickr.gallery.getInfo.html)
   */
  public function galleriesGetInfo($id, $other_args = []) {
    $args = ['gallery_id' => $id];
    array_merge($args, $other_args);

    $response = $this->client->request(
      'flickr.galleries.getInfo',
      $args,
      FALSE
    );

    if ($response) {
      return $response['gallery'];
    }

    return FALSE;
  }

  /**
   * Returns a list of photos for a given gallery.
   *
   * @param string $id
   *   ID of the gallery.
   *
   * @return array
   *   Response from the flickr method flickr.galleries.getPhotos.
   *   (https://www.flickr.com/services/api/flickr.galleries.getPhotos.html)
   */
  public function galleriesGetPhotos($id, $page = 1, $other_args = []) {
    $args = [
      'gallery_id' => $id,
      'page' => $page,
    ];
    array_merge($args, $other_args);

    // Set per_page to flickr module default if not specified in $args.
    if (!isset($args['per_page'])) {
      // TODO Expose pager as a setting.
      $args['per_page'] = 6;
    }

    $response = $this->client->request(
      'flickr.galleries.getPhotos',
      $args
    );

    if ($response) {
      return $response['photos'];
    }

    return FALSE;
  }

  /**
   * Returns the galleries curated by the specified user.
   *
   * @param string $nsid
   *   NSID of the user whose photoset list you want.
   *
   * @return array
   *   Response from the flickr method flickr.galleries.getList.
   *   (https://www.flickr.com/services/api/flickr.galleries.getList.html)
   */
  public function galleriesGetList($nsid, $page = 1) {
    $args = [
      'user_id' => $nsid,
      'page' => $page,
    ];

    $response = $this->client->request(
      'flickr.galleries.getList',
      $args
    );

    if ($response) {
      return $response['galleries']['gallery'];
    }

    return FALSE;
  }

}
