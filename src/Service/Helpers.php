<?php

namespace Drupal\flickr\Service;

use Drupal\flickr_api\Service\Photos;
use Drupal\flickr_api\Service\Helpers as FlickrApiHelpers;
use Drupal\flickr_api\Service\Photosets;

/**
 * Service class for Flickr Helpers.
 */
class Helpers {

  /**
   * Constructor for the Flickr API Groups class.
   */
  public function __construct(Photos $photos, FlickrApiHelpers $flickrApiHelpers, Photosets $photosets) {
    // Flickr API Photos.
    $this->photos = $photos;

    // Flickr API Helpers.
    $this->flickrApiHelpers = $flickrApiHelpers;

    // Flickr API Photosets.
    $this->photosets = $photosets;
  }

  public function themePhoto($photo, $size) {
    $img = [
      '#theme' => 'image',
      '#style_name' => NULL,
      '#uri' => $this->flickrApiHelpers->photoImgUrl($photo, $size),
      '#alt' => $photo['title']['_content'],
      '#title' => $photo['title']['_content'],
      // '#width' => $photo_size['height'],
      //            '#height' => $photo_size['width'],
      //            '#attributes' => array('class' => $attributes['class']),.
    ];

    $photoimg = [
      '#theme' => 'flickr_photo',
      '#photo' => $img,
      '#photo_page_url' => $photo['urls']['url'][0]['_content'],
      // '#size' => $config['size'],
      //            '#attribs' => $attribs,
      //            '#min_title' => $config['mintitle'],
      //            '#min_metadata' => $config['minmetadata'],.
    ];

    return $photoimg;
  }

  public function themePhotos($photos, $size) {
    $album = '';
    foreach ($photos as $photo) {
      $flickrPhoto = $this->photos->photosGetInfo($photo['id']);
      $photoimg = $this->themePhoto($flickrPhoto, $size);
      $album .= render($photoimg);
    }
    return $album;
  }

}