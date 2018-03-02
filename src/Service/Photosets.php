<?php

namespace Drupal\flickr\Service;

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
    // Flickr Client.
    $this->client = $client;
  }

  /**
   * Gets information about a photoset.
   *
   * @param string $photoset_id
   *   ID of the photoset to get information about.
   *
   * @return array
   *   Response from the flickr method flickr.photosets.getInfo.
   *   (https://www.flickr.com/services/api/flickr.photosets.getInfo.html)
   */
  function photosetsGetInfo($photoset_id) {
    $args = ['photoset_id' => $photoset_id];

    $response = $this->client->request(
      'flickr.photosets.getInfo',
      $args
    );

    if ($response) {
      return $response['photoset'];
    }

    return FALSE;
  }

  /**
   * Returns the photosets belonging to the specified user.
   *
   * @param string $nsid
   *   NSID of the user whose photoset list you want.
   *
   * @return array
   *   Response from the flickr method flickr.photosets.getList.
   *   (https://www.flickr.com/services/api/flickr.photosets.getList.html)
   */
  function photosetsGetList($nsid, $page = NULL, $per_page = NULL) {
    $args = [
      'user_id' => $nsid,
    ];

    if ($page != NULL) {
      $args['page'] = $page;
    }

    if ($per_page != NULL) {
      $args['per_page'] = $per_page;
    }

    $response = $this->client->request(
      'flickr.photosets.getList',
      $args
    );

    if ($response) {
      return $response['photosets']['photoset'];
    }

    return FALSE;
  }

  /**
   * Get the list of photos in a set.
   *
   * @param string $photoset_id
   *   The Flickr photoset ID.
   *
   * @return array
   *   Response from the flickr method flickr.photosets.getPhotos.
   *   (https://www.flickr.com/services/api/flickr.photosets.getPhotos.html)
   */
  function photosetsGetPhotos($photoset_id, $other_args = array(), $page = 1) {
    $args = array(
      'photoset_id' => $photoset_id,
      'page' => $page,
    );

    array_merge($args, $other_args);

    // Set per_page to flickr module default if not specified in $args.
    if (!isset($args['per_page'])) {
      // TODO Expose pager as a setting.
      $args['per_page'] = 6;
    }

    $response = $this->client->request(
      'flickr.photosets.getPhotos',
      $args
    );

    if ($response) {
      return $response['photoset'];
    }

    return FALSE;
  }

}
