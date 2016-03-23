<?php
/**
 * @file
 * Contains \Drupal\wysiwyg_template\Plugin\CKEditorPlugin\Templates.
 */

namespace Drupal\wysiwyg_template\Plugin\CKEditorPlugin;

use Drupal\ckeditor\CKEditorPluginInterface;
use Drupal\Core\Plugin\PluginBase;
use Drupal\editor\Entity\Editor;

/**
 * Defines the CKEditor Templates plugin.
 *
 * No buttons are exposed for this plugin, it is only here so it gets properly
 * loaded by the Drupal-specific TemplateSelector plugin.
 *
 * @CKEditorPlugin(
 *   id = "templates",
 *   label = @Translation("Template selector"),
 *   module = "wysiwyg_template"
 * )
 */
class Templates extends PluginBase implements CKEditorPluginInterface {

  /**
   * {@inheritdoc}
   */
  public function isInternal() {
    return FALSE;
  }

  /**
   * {@inheritdoc}
   */
  public function getDependencies(Editor $editor) {
    return [];
  }

  /**
   * {@inheritdoc}
   */
  public function getLibraries(Editor $editor) {
    return [];
  }

  /**
   * {@inheritdoc}
   */
  public function getFile() {
    // @todo This location is hard-coded and should be more flexible.
    // @see https://www.drupal.org/node/2693151
    return \Drupal::request()->getBaseUrl() . '/libraries/templates/plugin.js';
  }

  /**
   * {@inheritdoc}
   */
  public function getConfig(Editor $editor) {
    return [];
  }

}
