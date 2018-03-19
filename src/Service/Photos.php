<?php

namespace Drupal\flickr\Service;

use Drupal\flickr_api\Service\Photos as FlickrApiPhotos;
use Drupal\flickr_api\Service\Helpers as FlickrApiHelpers;

/**
 * Service class for Flickr Photos.
 */
class Photos {

  /**
   * Constructor for the Flickr Photos class.
   */
  public function __construct(FlickrApiPhotos $flickrApiPhotos,
                              Helpers $helpers,
                              FlickrApiHelpers $flickrApiHelpers) {
    // Flickr API Photos.
    $this->flickrApiPhotos = $flickrApiPhotos;

    // Flickr Helpers.
    $this->helpers = $helpers;

    // Flickr API Helpers.
    $this->flickrApiHelpers = $flickrApiHelpers;

  }

  /**
   * @param $photo
   * @param $size
   *
   * @return array
   */
  public function themePhoto($photo, $size, $caption = 0) {
    $photoSizes = $this->flickrApiPhotos->photosGetSizes($photo['id']);
    $sizes = $this->flickrApiHelpers->photoSizes();

    if ($this->flickrApiHelpers->inArrayR($sizes[$size]['label'], $photoSizes)) {
      $img = [
        '#theme' => 'image',
        '#style_name' => 'flickr-photo-' . $size,
        '#uri' => $this->flickrApiHelpers->photoImgUrl($photo, $size),
        '#alt' => $photo['title']['_content'] . ' by ' . $photo['owner']['realname'],
        '#title' => $photo['title']['_content'] . ' by ' . $photo['owner']['realname'],
      ];

      $photoimg = [
        '#theme' => 'flickr_photo',
        '#photo' => $img,
        '#caption' => $caption,
        '#photo_page_url' => $photo['urls']['url'][0]['_content'],
        '#style_name' => 'flickr-photo-' . $size,
        '#attached' => [
          'library' => [
            'flickr/flickr.stylez',
          ],
        ],
      ];

      if ($caption == 1) {
        $photoimg['#caption_data'] = $this->themeCaption($photo, $size, $caption);
      }

      return $photoimg;
    }
  }

  /**
   * @param $photos
   * @param $size
   *
   * @return array
   */
  public function themePhotos($photos, $size, $caption = 0) {
    foreach ($photos as $photo) {
      $themedPhotos[] = $this->themePhoto(
        $this->flickrApiPhotos->photosGetInfo($photo['id']),
        $size,
        $caption
      );
    }

    return [
      '#theme' => 'flickr_photos',
      '#photos' => $themedPhotos,
      '#attached' => [
        'library' => [
          'flickr/flickr.stylez',
        ],
      ],
    ];
  }

  public function themeCaption($photo, $size, $caption) {
    return [
      '#theme' => 'flickr_photo_caption',
      '#caption' => $caption,
      '#caption_realname' => $photo['owner']['realname'],
      '#caption_title' => $photo['title']['_content'],
      '#caption_description' => $photo['description']['_content'],
      '#caption_dateuploaded' => $photo['dateuploaded'],
      '#style_name' => 'flickr-photo-' . $size,
      '#photo_size' => $size,
    ];
  }

}
