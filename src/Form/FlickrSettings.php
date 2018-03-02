<?php

/**
 * @file
 * Contains Drupal\flickr\Form\FlickrSettings.
 */

namespace Drupal\flickr\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Link;
use Drupal\Core\Url;

/**
 * Implements the Flickr Settings form controller.
 *
 * @see \Drupal\Core\Form\FormBase
 */
class FlickrSettings extends ConfigFormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'flickr_settings';
  }

  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames() {
    return [
      'flickr.settings',
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $config = $this->config('flickr.settings');

    $form['credentials'] = [
      '#type' => 'fieldset',
      '#title' => t('OAuth Settings'),
    ];

    $form['credentials']['help'] = [
      '#type' => '#markup',
      '#markup' => t('API Key from Flickr. Get an API Key at @link.',
        [
          '@link' => Link::fromTextAndUrl('https://www.flickr.com/services/apps/create/apply',
          Url::fromUri('https://www.flickr.com/services/apps/create/apply'))->toString()
        ]
      ),
    ];

    $form['credentials']['flickr_api_key'] = [
      '#type' => 'textfield',
      '#title' => t('API Key'),
      '#default_value' => $config->get('flickr_api_key'),
    ];

    $form['credentials']['flickr_api_secret'] = [
      '#type' => 'textfield',
      '#title' => t('API key secret'),
      '#default_value' => $config->get('flickr_api_secret'),
    ];

    $form['flickr'] = [
      '#type' => 'fieldset',
      '#title' => t('Flickr Settings'),
      '#description' => t('The following settings connect Flickr module with external APIs.'),
    ];

    $form['flickr']['host_uri'] = [
      '#type' => 'textfield',
      '#title' => t('Flickr URL'),
      '#default_value' => $config->get('host_uri'),
    ];

    $form['flickr']['api_uri'] = [
      '#type' => 'textfield',
      '#title' => t('Flickr API URL'),
      '#default_value' => $config->get('api_uri'),
    ];

    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $this->config('flickr.settings')
      ->set('flickr_api_key', $form_state->getValue('flickr_api_key'))
      ->set('flickr_api_secret', $form_state->getValue('flickr_api_secret'))
      ->set('host_uri', $form_state->getValue('host_uri'))
      ->set('api_uri', $form_state->getValue('api_uri'))
      ->save();

    parent::submitForm($form, $form_state);
  }

}