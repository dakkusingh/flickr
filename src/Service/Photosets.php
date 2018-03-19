<?php

namespace Drupal\flickr\Service;

use Drupal\flickr_api\Service\Photosets as FlickrApiPhotosets;
use Drupal\flickr_api\Service\Helpers as FlickrApiHelpers;

/**
 * Service class for Flickr Photosets.
 */
class Photosets {

  /**
   * Constructor for the Flickr API Groups class.
   */
  public function __construct(FlickrApiPhotosets $flickrApiPhotosets,
                              Photos $photos,
                              Helpers $helpers,
                              FlickrApiHelpers $flickrApiHelpers) {
    // Flickr API Photosets.
    $this->flickrApiPhotosets = $flickrApiPhotosets;

    // Flickr Photos.
    $this->photos = $photos;

    // Flickr Helpers.
    $this->helpers = $helpers;

    // Flickr API Helpers.
    $this->flickrApiHelpers = $flickrApiHelpers;
  }

  /**
   * @param $photos
   * @param $title
   *
   * @return array
   */
  public function themePhotoset($photos, $title) {
    return [
      '#theme' => 'flickr_photoset',
      '#photos' => $photos,
      '#title' => $title,
      '#attached' => [
        'library' => [
          'flickr/flickr.stylez',
        ],
      ],
    ];
  }

}
