<?php

/**
 * @file
 * Contains \Drupal\wysiwyg_template\Form\WysiwygTemplateOverview.
 */

namespace Drupal\wysiwyg_template\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Render\Element;

class WysiwygTemplateOverview extends FormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'wysiwyg_template_overview';
  }

  public function buildForm(array $form, \Drupal\Core\Form\FormStateInterface $form_state) {
    $templates = db_select('wysiwyg_templates', 't')
      ->fields('t')
      ->orderBy('t.weight')
      ->execute()
      ->fetchAll();

    $form['wysiwyg_templates'] = ['#tree' => TRUE];

    if (empty($templates)) {
      // @FIXME
// l() expects a Url object, created from a route name or external URI.
// $form['info_empty'] = array(
//       '#markup' => t('No templates available.') . ' ' . l(t('Add you first template.'), 'admin/config/content/wysiwyg-templates/add'),
//       '#weight' => 10,
//     );

    }
    else {
      $order = 0;
      foreach ($templates as $template) {
        $form['wysiwyg_templates'][$template->name]['#template'] = (object) [
          'name' => $template->name,
          'title' => $template->title,
          'description' => $template->description,
          'weight' => $order,
          'content_types' => wysiwyg_template_load_template_content_types($template->name),
        ];
        $form['wysiwyg_templates'][$template->name]['#weight'] = $order;
        $form['wysiwyg_templates'][$template->name]['weight'] = [
          '#type' => 'textfield',
          '#title' => t('Weight for @title', [
            '@title' => $template->title
            ]),
          '#title_display' => 'invisible',
          '#size' => 4,
          '#default_value' => $order,
          '#attributes' => [
            'class' => [
              'template-weight'
              ]
            ],
        ];
        $order++;
      }

      $form['actions'] = ['#type' => 'actions'];
      $form['actions']['submit'] = [
        '#type' => 'submit',
        '#value' => t('Save order'),
        '#submit' => [
          'wysiwyg_template_overview_submit'
          ],
      ];
    }

    return $form;
  }

  public function submitForm(array &$form, \Drupal\Core\Form\FormStateInterface $form_state) {
    foreach ($form_state->getValue(['wysiwyg_templates']) as $name => $template_values) {
      db_update('wysiwyg_templates')
        ->fields(['weight' => $template_values['weight']])
        ->condition('name', $name)
        ->execute();
    }
    drupal_set_message(t('The Wysiwyg template settings have been updated.'));
  }

}
