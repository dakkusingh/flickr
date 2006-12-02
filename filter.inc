<?php
// $Id$

function flickr_filter_tips($delta, $format, $long = FALSE) {
  switch ($delta) {
    case 0:
      $output = t('Insert Flickr images: [flickr-photo:id=230452326,size=s] or [flickr-photoset:id=72157594262419167,size=m]. ');
      if ($long) {
        $output .= t('The size parameter can be one of the following:');
        $items = array();
        foreach (flickr_photo_sizes() as $key => $text) {
          $items[] = "$key - $text";
        }
        $output .= theme('item_list', $items);
      }
      return $output;
  }
}

function flickr_filter($op, $delta = 0, $format = -1, $text = '') {
  if ($op == 'list') {
    return array(0 => t('Flickr linker'));
  }

  switch ($delta) {
    case 0:
      switch ($op) {
        case 'description':
          return t('Insert photos or photosets from Flickr without <img> tags: [flickr-photo:id=230452326]');

        case 'no cache':
          // TODO: only return true when testing the filter
          // return TRUE;
          return FALSE;

        case 'prepare':
          return $text;

        case 'process':
          $text = preg_replace_callback('/\[flickr-photo:(.+)\]/', 'flickr_filter_callback_photo', $text);
          $text = preg_replace_callback('/\[flickr-photoset:(.+)\]/', 'flickr_filter_callback_photoset', $text);
          return $text;
      }
      break;
  }
}

/**
 * Parse parameters to the fiter from a format like:
 *   id=26159919@N00, size=m,show = 9
 * into an associative array.
 */
function flickr_filter_split_config($string) {
  $config = array();
  // put each setting on its own line
  $string = str_replace(',', "\n", $string);
  // break them up around commas
  preg_match_all('/([a-zA-Z]+)=([@0-9a-zA-Z]+)/', $string, $parts, PREG_SET_ORDER);
  foreach($parts as $part) {
    // normalize to lower case and remove extra spaces
    $config[strtolower(trim($part[1]))] = trim($part[2]);
  }
  return $config;
}

/**
 * Filter callback for a photo.
 */
function flickr_filter_callback_photo($matches) {
  $config = flickr_filter_split_config($matches[1]);

  $output = '';
  if (isset($config['id'])) {
    $result = flickr_photo_get_info($config['id']);
    $output .= '<span class="flickr-photo">';
    $output .= theme('flickr_photo', $result['photo'], $config['size']);
    $output .= "</span>\n";
  }
  return $output ;
}

/**
 * Filter callback for a photoset.
 */
function flickr_filter_callback_photoset($matches) {
  $config = flickr_filter_split_config($matches[1]);

  $output = '';
  if (isset($config['id'])) {
    $result = flickr_photoset_get_info($config['id']);
    $output .= '<span class="flickr-photoset">';
    $output .= theme('flickr_photoset', $result['photoset'], $result['photoset']['owner'], $config['size']);
    $output .= "</span>\n";
  }
  return $output ;
}
