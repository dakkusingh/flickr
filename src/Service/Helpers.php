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


  /**
   * Parse parameters to the fiter from a format like:
   * id=26159919@N00, size=m,num=9,class="something",style="float:left;border:1px"
   * into an associative array with two sub-arrays. The first sub-array are
   * parameters for the request, the second are HTML attributes (class and style).
   */
  public function splitConfig($string) {
    $config = [];
    $attribs = [];

    // Put each setting on its own line.
    $string = str_replace(',', "\n", $string);

    // Break them up around the equal sign (=).
    preg_match_all('/([a-zA-Z_.]+)=([-@\/0-9a-zA-Z :;_.\|\%"\'&°]+)/', $string, $parts, PREG_SET_ORDER);

    foreach ($parts as $part) {
      // Normalize to lowercase and remove extra spaces.
      $name = strtolower(trim($part[1]));
      $value = htmlspecialchars_decode(trim($part[2]));

      // Remove undesired but tolerated characters from the value.
      $value = str_replace(str_split('"\''), '', $value);

      if ($name == 'style' || $name == 'class') {
        $attribs[$name] = $value;
      }
      else {
        $config[$name] = $value;
      }
    }

    return [$config, $attribs];
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