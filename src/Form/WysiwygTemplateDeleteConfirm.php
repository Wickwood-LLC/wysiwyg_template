<?php

/**
 * @file
 * Contains \Drupal\wysiwyg_template\Form\WysiwygTemplateDeleteConfirm.
 */

namespace Drupal\wysiwyg_template\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Render\Element;

class WysiwygTemplateDeleteConfirm extends FormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'wysiwyg_template_delete_confirm';
  }

  public function buildForm(array $form, \Drupal\Core\Form\FormStateInterface $form_state, $template = NULL) {

    $form['#wysiwyg_template'] = $template;

    return confirm_form($form, t('Are you sure you want to delete the template %title?', [
      '%title' => $template['title']
      ]), isset($_GET['destination']) ? $_GET['destination'] : 'admin/config/content/wysiwyg-templates', t('This action cannot be undone.'), t('Delete'), t('Cancel'));
  }

  public function submitForm(array &$form, \Drupal\Core\Form\FormStateInterface $form_state) {
    if ($form_state->getValue(['confirm'])) {
      wysiwyg_template_delete_template($form['#wysiwyg_template']['name']);
    }

    $form_state->set(['redirect'], 'admin/config/content/wysiwyg-templates');
    return;
  }

}
