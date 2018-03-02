<?php
/**
 * @file
 * Contains Drupal\flickr\Photos.
 */

namespace Drupal\flickr\Service;

use Drupal\flickr\Service\FlickrClient;

/**
 * Service class for Flickr Photos.
 */
class Photos {

  /**
   * @var \Drupal\flickr\Service\FlickrClient
   */
  protected $client;

  /**
   * Constructor for the 500px Photos class.
   */
  public function __construct(FlickrClient $client) {
    // Flickr Client
    $this->client = $client;
  }

  /**
   * Get information about a photo.
   *
   * @param string $photo_id
   *   ID of the photo to get info about.
   *
   * @return array
   *   Response from the flickr method flickr.photos.getInfo..
   *   (https://www.flickr.com/services/api/flickr.photos.getInfo.html)
   */
  public function flickrPhotosGetInfo($photo_id) {
    $response = $this->client->flickrRequest(
      'flickr.photos.getInfo',
      ['photo_id' => $photo_id]
    );

    if ($response) {
      return $response['photo'];
    }

    return FALSE;
  }

  /**
   * Returns the available sizes for a photo.
   *
   * @param string $photo_id
   *   ID of the photo to get the available sizes of.
   *
   * @return array
   *   Response from the flickr method flickr.photos.getSizes..
   *   (https://www.flickr.com/services/api/flickr.photos.getSizes.html)
   */
  public function flickrPhotosGetSizes($photo_id) {
    $response = $this->client->flickrRequest(
      'flickr.photos.getSizes',
      ['photo_id' => $photo_id]
    );

    if ($response) {
      return $response['sizes']['size'];
    }

    return FALSE;
  }

  /**
   * Return a list of photos matching some criteria.
   *
   * @param string $nsid
   *   NSID of the user whose photoset tags will be returned.
   * @param string $page
   *   Page of results to return.
   *
   * @return array
   *   Response from the flickr method flickr.photos.search.
   *   (https://www.flickr.com/services/api/flickr.photos.search.html)
   */
  function flickrPhotosSearch($nsid, $page = 1, $other_args = array()) {

    $args = [
      'page' => $page,
      'user_id' => $nsid,
    ];

    array_merge($args, $other_args);

    // Set per_page to flickr module default if not specified in $args.
    if (!isset($args['per_page'])) {
      // TODO Expose pager as a setting.
      $args['per_page'] = 6;
    }

    $response = $this->client->flickrRequest(
      'flickr.photos.search',
      $args
    );

    if ($response) {
      return $response['photos'];
    }

    return FALSE;
  }

}
