<?php

namespace Drupal\wysiwyg_template\Plugin\Filter;

use Drupal\filter\FilterProcessResult;
use Drupal\filter\Plugin\FilterBase;

/**
 * Provides a filter to load CSS required by the templates.
 *
 * @Filter(
 *   id = "wysiwyg_template_load_css",
 *   title = @Translation("Load WYSIWYG Template CSS"),
 *   description = @Translation("Wysiwyg templates may depend on CSS which are stored in settings. Those CSS may be required to load for content build with this format."),
 *   type = Drupal\filter\Plugin\FilterInterface::TYPE_TRANSFORM_REVERSIBLE,
 *   weight = 0
 * )
 */
class TemplateCSS extends FilterBase {

  /**
   * {@inheritdoc}
   */
  public function process($text, $langcode): FilterProcessResult {
    $processed_text = new FilterProcessResult($text);
    $processed_text->addAttachments(['library' => ['wysiwyg_template/view-template']]);
    return $processed_text;
  }

}
