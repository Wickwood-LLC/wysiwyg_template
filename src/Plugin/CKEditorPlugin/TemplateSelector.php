<?php

namespace Drupal\wysiwyg_template\Plugin\CKEditorPlugin;

use Drupal\ckeditor\CKEditorPluginBase;
use Drupal\Core\Url;
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
  public function getButtons(): array {
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
  public function getConfig(Editor $editor): array {
    // Using 'dummy' as entity type and bundle as the config will be overwritten
    // with specific values in @see wysiwyg_template_editor_js_settings_alter().
    return [
      'templates_files' => [Url::fromRoute('wysiwyg_template.list_js.type', [
        'entity_type' => 'dummy',
        'bundle' => 'dummy',
      ])->toString()],
      'templates_replaceContent' => FALSE,
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function getLibraries(Editor $editor): array {
    return [
      'core/drupal.ajax',
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function getDependencies(Editor $editor): array {
    return ['templates'];
  }

}
