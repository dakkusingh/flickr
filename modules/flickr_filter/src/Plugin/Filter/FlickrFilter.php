<?php

namespace Drupal\flickr_filter\Plugin\Filter;
use Drupal\Component\Utility\Xss;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\filter\FilterProcessResult;
use Drupal\filter\Plugin\FilterBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\flickr\Service\Helpers;
use Symfony\Component\DependencyInjection\ContainerInterface;

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
class FlickrFilter extends FilterBase implements ContainerFactoryPluginInterface {

  /**
   * @var \Drupal\flickr\Service\Helpers
   */
  protected $helpers;

  /**
   * FlickrFilter constructor.
   *
   * @param array $configuration
   * @param $plugin_id
   * @param $plugin_definition
   * @param \Drupal\flickr\Service\Helpers $helpers
   */
  public function __construct(array $configuration, $plugin_id, $plugin_definition, Helpers $helpers) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);
    $this->helpers = $helpers;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->get('flickr.helpers')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function process($text, $langcode) {
    $text = preg_replace_callback('/\[flickr-photo:(.+?)\]/', 'self::callbackPhoto', $text);
    $text = preg_replace_callback('/\[flickr-photoset:(.+?)\]/', 'self::callbackPhotosets', $text);
    //    $text = preg_replace_callback('/\[flickr-group:(.+?)\]/', 'flickr_filter_callback_group', $text);
    //    $text = preg_replace_callback('/\[flickr-gallery:(.+?)\]/', 'flickr_filter_callback_gallery', $text);
    //    $text = preg_replace_callback('/\[flickr-user:(.+?)\]/', 'flickr_filter_callback_album', $text);
    //    $text = preg_replace_callback('/\[flickr-favorites:(.+?)\]/', 'flickr_filter_callback_favorites', $text);.
    return new FilterProcessResult($text);
  }

  /**
   * {@inheritdoc}
   */
  public function settingsForm(array $form, FormStateInterface $form_state) {
    $sizes = $this->helpers->flickrApiHelpers->photoSizes();
    foreach ($sizes as $key => $size) {
      $options[$key] = $size['description']->render();
    }

    $form['flickr_filter_default_size'] = [
      '#type' => 'select',
      '#title' => $this->t('Default size for single photos'),
      '#default_value' => $this->settings['flickr_filter_default_size'],
      '#options' => $options,
      '#description' => $this->t("A default Flickr size to use if no size is specified, for example [flickr-photo:id=3711935987].<br />TAKE CARE, the c size (800px) is missing on Flickr images uploaded before March 1, 2012!"),
    ];

    $form['flickr_filter_heading'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Wrap the photoset title in an HTML heading tag (only for the text filter)'),
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
  private function callbackPhoto($matches) {
    list($config, $attribs) = $this->helpers->splitConfig($matches[1]);

    if (isset($config['id'])) {

      if ($photo = $this->helpers->photos->photosGetInfo($config['id'])) {
        if (!isset($config['size'])) {
          $config['size'] = $this->settings['flickr_filter_default_size'];
        }

        switch ($config['size']) {
          case "x":
          case "y":
            drupal_set_message(t("Do not use a slideshow for a single image."), 'error');
            $config['size'] = $this->settings['flickr_filter_default_size'];
            break;
        }

        $sizes = $this->helpers->flickrApiHelpers->photoSizes();
        $photoSizes = $this->helpers->photos->photosGetSizes($photo['id']);

        if ($this->helpers->flickrApiHelpers->inArrayR($sizes[$config['size']]['label'], $photoSizes)) {
          $photoimg = $this->helpers->themePhoto($photo, $config['size']);
          return render($photoimg);
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
   * Filter callback for a user or set.
   */
  public function callbackPhotosets($matches) {
    list($config, $attribs) = $this->helpers->splitConfig($matches[1]);

    if (!isset($attribs['class'])) {
      $attribs['class'] = NULL;
    }

    if (!isset($attribs['style'])) {
      $attribs['style'] = NULL;
    }

    if (!isset($config['size'])) {
      $config['size'] = NULL;
    }

    if (!isset($config['num'])) {
      $config['num'] = NULL;
    }

    if (!isset($config['sort'])) {
      $config['sort'] = 'unsorted';
    }

    switch ($config['sort']) {
      case 'taken':
        $config['sort'] = 'date-taken-desc';
        break;

      case 'posted':
        $config['sort'] = 'date-posted-desc';
        break;
    }

    $photosetPhotos = $this->helpers->photosets->photosetsGetPhotos(
      $config['id'],
      [
        'per_page' => $config['num'],
        'extras' => 'date_upload,date_taken,license,geo,tags,views,media',
        'media' => 'photos',
      ],
      1
    );

    $photos = $this->helpers->themePhotos($photosetPhotos['photo'], $config['size']);
    return $photos;
  }

}
