<?php
/**
 * @file
 * Contains \Drupal\wysiwyg_template\Plugin\CKEditorPlugin\TemplateSelector.
 */

namespace Drupal\wysiwyg_template\Plugin\CKEditorPlugin;

use Drupal\ckeditor\CKEditorPluginBase;
use Drupal\editor\Entity\Editor;

/**
 * Defines a WYSIWYG TemplateSelector selector plugin.
 *
 * @CKEditorPlugin(
 *   id = "templateselector",
 *   label = @Translation("Template selector"),
 *   module = "wysiwyg_template"
 * )
 */
class TemplateSelector extends CKEditorPluginBase {

  /**
   * {@inheritdoc}
   */
  public function getButtons() {
    return [
      'TemplateSelector' => [
        'label' => $this->t('Insert templates'),
        'image' => drupal_get_path('module', 'wysiwyg_template') . '/js/plugins/templateselector/icons/templateselector.png',
      ],
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function getFile() {
    return drupal_get_path('module', 'wysiwyg_template') . '/js/plugins/templateselector/plugin.js';
  }

  /**
   * {@inheritdoc}
   */
  public function getConfig(Editor $editor) {
    // TODO: Implement getConfig() method.
    return [];
  }

  /**
   * {@inheritdoc}
   */
  public function getLibraries(Editor $editor) {
    return [
      'core/drupal.ajax',
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function getDependencies(Editor $editor) {
    return ['templates'];
  }

}
