<?php

namespace Drupal\flickr\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;

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

    $form['flickr'] = [
      '#type' => 'fieldset',
      '#title' => $this->t('Flickr Settings'),
      '#description' => $this->t('The following settings connect Flickr module with external APIs.'),
    ];

    $form['flickr']['api_uri'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Flickr API URL'),
      '#default_value' => $config->get('api_uri'),
    ];

    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $this->config('flickr.settings')
      ->set('api_uri', $form_state->getValue('api_uri'))
      ->save();

    parent::submitForm($form, $form_state);
  }

}
