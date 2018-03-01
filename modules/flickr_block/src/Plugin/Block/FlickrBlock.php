<?php
/**
 * @file
 * Contains Drupal\flickr_block\Plugin\Block\FlickrBlock.
 */
namespace Drupal\flickr_block\Plugin\Block;
use Drupal\Core\Block\BlockBase;
use Drupal\Core\Block\BlockPluginInterface;
use Drupal\Core\Form\FormStateInterface;
/**
 * Provides flickr Block.
 *
 * @Block(
 *   id = "flickr_block",
 *   admin_label = @Translation("Flickr block"),
 * )
 */
class FlickrBlock extends BlockBase implements BlockPluginInterface {

}