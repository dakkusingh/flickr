<?php

namespace Drupal\flickr_filter\Plugin\Filter;
use Drupal\Component\Utility\Xss;
use Drupal\filter\FilterProcessResult;
use Drupal\filter\Plugin\FilterBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Provides a filter to insert Flickr photo.
 *
 * @Filter(
 *   id = "flickr_filter",
 *   title = @Translation("Embed Flickr photo"),
 *   description = @Translation("Allow users to embed a picture from Flickr website in an editable content area."),
 *   type = Drupal\filter\Plugin\FilterInterface::TYPE_MARKUP_LANGUAGE,
 *   settings = {
 *     "flickr_filter_imagesize" = 200,
 *   },
 * )
 */
class FlickrFilter extends FilterBase {

  /**
   * {@inheritdoc}
   */
  public function process($text, $langcode) {
    $new_text = preg_replace_callback('/\[flickr-photo:(.+?)\]/', 'self::flickr_filter_callback_photo', $text);

    // $text = preg_replace_callback('/\[flickr-photoset:(.+?)\]/', 'flickr_filter_callback_album', $text);
    //    $text = preg_replace_callback('/\[flickr-group:(.+?)\]/', 'flickr_filter_callback_group', $text);
    //    $text = preg_replace_callback('/\[flickr-gallery:(.+?)\]/', 'flickr_filter_callback_gallery', $text);
    //    $text = preg_replace_callback('/\[flickr-user:(.+?)\]/', 'flickr_filter_callback_album', $text);
    //    $text = preg_replace_callback('/\[flickr-favorites:(.+?)\]/', 'flickr_filter_callback_favorites', $text);.
    return new FilterProcessResult($new_text);
  }

  /**
   * {@inheritdoc}
   */
  public function settingsForm(array $form, FormStateInterface $form_state) {
    $form['flickr_filter_default_size'] = [
      '#type' => 'select',
      '#title' => $this->t('Default size for single photos'),
      '#default_value' => $this->settings['flickr_filter_default_size'],
      // TODO use standard sizes.
      '#options' => [
        's' => $this->t('s: 75 px square'),
        't' => $this->t('t: 100 px on longest side'),
        'q' => $this->t('q: 150 px square'),
        'm' => $this->t('m: 240 px on longest side'),
        'n' => $this->t('n: 320 px on longest side (!)'),
        '-' => $this->t('-: 500 px on longest side'),
        'z' => $this->t('z: 640 px on longest side'),
        'c' => $this->t('c: 800 px on longest side (!)'),
        'b' => $this->t('b: 1024 px on longest side'),
      ],
      '#description' => $this->t("A default Flickr size to use if no size is specified, for example [flickr-photo:id=3711935987].<br />TAKE CARE, the c size (800px) is missing on Flickr images uploaded before March 1, 2012!"),
    ];

    $form['flickr_filter_heading'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Wrap the album title in an HTML heading tag (only for the text filter)'),
      '#required' => TRUE,
      '#default_value' => $this->settings['flickr_filter_heading'],
      '#description' => $this->t("Use 'p' for no style, e.g. 'h3' for a heading or 'none' to not display an album title."),
      '#size' => 4,
      '#maxlength' => 4,
    ];

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function tips($long = FALSE) {
    if ($long) {
      return $this->t(
        'Embed Flickr photos using @embed. Values for imagesize is optional, if left off the default values configured on the %filter input filter will be used',
        [
          '@embed' => '[flickr-photo:id=<photo_id>, size=<imagesize>]',
          '%filter' => 'Embed Flickr photo',
        ]
      );
    }
    else {
      return $this->t('Embed Flickr photo using @embed', ['@embed' => '[flickr-photo:id=<photo_id>, size=<imagesize>]']);
    }
  }

  /**
   * Filter callback for a photo.
   */
  private function flickr_filter_callback_photo($matches) {
    list($config, $attribs) = $this->flickr_filter_split_config($matches[1]);

    if (isset($config['id'])) {

      if ($photo = \Drupal::service('flickr_api.photos')->photosGetInfo($config['id'])) {
        if (!isset($config['size'])) {
          $config['size'] = $this->settings['flickr_filter_default_size'];
        }
        // If (!isset($config['mintitle'])) {
        //          $config['mintitle'] = $this->settings['flickr_title_suppress_on_small'];
        //        }
        //        if (!isset($config['minmetadata'])) {
        //          $config['minmetadata'] = $this->settings['flickr_metadata_suppress_on_small'];
        //        }.
        switch ($config['size']) {
          case "x":
          case "y":
            drupal_set_message(t("Do not use a slideshow for a single image."), 'error');
            $config['size'] = $this->settings['flickr_filter_default_size'];
            break;
        }

        $sizes = \Drupal::service('flickr_api.helpers')->photoSizes();
        $photoSizes = \Drupal::service('flickr_api.photos')->photosGetSizes($photo['id']);

        if (\Drupal::service('flickr_api.helpers')->inArrayR($sizes[$config['size']]['label'], $photoSizes)) {
          $photoUrl = \Drupal::service('flickr_api.helpers')->photoImgUrl($photo, $config['size']);

          $photoimg = [
            '#theme' => 'image',
            '#style_name' => NULL,
            '#uri' => $photoUrl,
            '#alt' => $photo['title']['_content'],
            '#title' => $photo['title']['_content'],
          // '#width' => $photo_size['height'],
          //            '#height' => $photo_size['width'],
          //            '#attributes' => array('class' => $attributes['class']),.
          ];

          $img = [
            '#theme' => 'flickr_photo',
            '#photo' => $photoimg,
            '#photo_page_url' => $photo['urls']['url'][0]['_content'],
          // '#size' => $config['size'],
          //            '#attribs' => $attribs,
          //            '#min_title' => $config['mintitle'],
          //            '#min_metadata' => $config['minmetadata'],.
          ];

          return render($img);
        }
        else {
          // Generate an "empty" image of the requested size containing a message.
          $string = $sizes[$config['size']]['description'];
          preg_match("/\d*px/", $string, $matches);
          return '<span class="flickr-wrap" style="width: ' . $matches[0] . '; height: ' . $matches[0] . '; border:solid 1px;"><span class="flickr-empty">' . t('The requested image size is not available for this photo on Flickr (uploaded when this size was not offered yet). Try another size or re-upload this photo on Flickr.') . '</span></span>';
        }

      }
    }
    return '';
  }

  /**
   * Parse parameters to the fiter from a format like:
   * id=26159919@N00, size=m,num=9,class="something",style="float:left;border:1px"
   * into an associative array with two sub-arrays. The first sub-array are
   * parameters for the request, the second are HTML attributes (class and style).
   */
  public function flickr_filter_split_config($string) {
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

}
