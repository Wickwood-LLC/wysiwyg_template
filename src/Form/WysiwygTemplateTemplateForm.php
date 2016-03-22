<?php

/**
 * @file
 * Contains \Drupal\wysiwyg_template\Form\WysiwygTemplateTemplateForm.
 */

namespace Drupal\wysiwyg_template\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Render\Element;

class WysiwygTemplateTemplateForm extends FormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'wysiwyg_template_template_form';
  }

  public function buildForm(array $form, \Drupal\Core\Form\FormStateInterface $form_state, $template = NULL) {
    if (!$form_state->get(['confirm_delete'])) {
      // Rebuild the form to confirm term deletion.
      $form['name'] = [
        '#type' => 'value',
        '#value' => $form_state->getValue([
          'name'
          ]),
      ];
      $form['delete'] = ['#type' => 'value', '#value' => TRUE];
      return confirm_form($form, t('Are you sure you want to delete the template %title?', [
        '%title' => $form_state->getValue([
          'title'
          ])
        ]), 'admin/content/wysiwyg-template', t('This action cannot be undone.'), t('Delete'), t('Cancel'));
    }

    if (!empty($template)) {
      // Add the current values as defaults to the form, if editing an existing item.
      $form_state->setValue([], $template);
    }

    $content_types = [];
    foreach (node_type_get_types() as $content_type) {
      $content_types[$content_type->type] = $content_type->name;
    }

    $form = [];
    $form['#attributes']['enctype'] = 'multipart/form-data';
    $form['title'] = [
      '#type' => 'textfield',
      '#title' => t('Template Name'),
      '#default_value' => !$form_state->getValue([
        'title'
        ]) ? $form_state->getValue(['title']) : '',
      '#description' => t('Select a name for this template.'),
      '#maxlength' => 80,
      '#required' => TRUE,
    ];
    $form['name'] = [
      '#type' => 'machine_name',
      '#maxlength' => 32,
      '#machine_name' => [
        'exists' => 'wysiwyg_template_name_exists',
        'source' => [
          'title'
          ],
      ],
      '#description' => t('A unique machine-readable name for this template. It must only contain lowercase letters, numbers, and underscores.'),
    ];
    $form['description'] = [
      '#type' => 'textfield',
      '#title' => t('Template Description'),
      '#default_value' => !$form_state->getValue([
        'description'
        ]) ? $form_state->getValue(['description']) : '',
      '#description' => t('A description to be shown with the template.'),
    ];
    $form['weight'] = [
      '#type' => 'textfield',
      '#title' => t('Weight'),
      '#default_value' => !$form_state->getValue([
        'weight'
        ]) ? $form_state->getValue(['weight']) : 0,
      '#description' => t('The weight of this template for the sort order in lists.'),
    ];
    $form['content_types'] = [
      '#type' => 'checkboxes',
      '#title' => t('Available for content types'),
      '#options' => $content_types,
      '#default_value' => !$form_state->getValue([
        'content_types'
        ]) ? array_keys($form_state->getValue(['content_types'])) : [],
      '#description' => t('If you select no content type, this template will be available for all content types.'),
      '#access' => (count($content_types) > 1),
    ];
    // load image if it has one
    $image = '';
    if (!$form_state->getValue(['fid']) && $form_state->getValue(['fid'])) {
      $image_uri = file_load($form_state['values']['fid'])->get([]);
      if ($image_uri) {
        // @FIXME
// theme() has been renamed to _theme() and should NEVER be called directly.
// Calling _theme() directly can alter the expected output and potentially
// introduce security issues (see https://www.drupal.org/node/2195739). You
// should use renderable arrays instead.
// 
// 
// @see https://www.drupal.org/node/2195739
// $image = theme('image_style', array(
//         'style_name' => 'wysiwyg_template_thumbnail',
//         'path' => $image_uri
//       ));

      }
    }
    $form['template_image'] = [
      '#type' => 'file',
      '#title' => t('Choose a file'),
      '#size' => 22,
      '#description' => t('A image to be shown with the template.'),
      '#prefix' => $image,
    ];
    $form['template_image_fid'] = [
      '#type' => 'hidden',
      '#default_value' => !$form_state->getValue([
        'fid'
        ]) ? $form_state->getValue(['fid']) : 0,
    ];
    // add delete button if it has an image
    if (!$form_state->getValue([
      'fid'
      ]) && $form_state->getValue(['fid'])) {
      $form['template_image_delete'] = [
        '#type' => 'checkbox',
        '#title' => t('Delete the Template image.'),
      ];
    }
    $form['body'] = [
      '#type' => 'text_format',
      '#title' => t('HTML Template'),
      '#rows' => 10,
      '#format' => !$form_state->getValue([
        'format'
        ]) ? $form_state->getValue(['format']) : filter_default_format(),
      '#default_value' => !$form_state->getValue([
        'body'
        ]) ? $form_state->getValue(['body']) : '',
      '#required' => TRUE,
    ];
    if (\Drupal::moduleHandler()->moduleExists('token')) {
      $form['token_help'] = [
        '#theme' => 'token_tree',
        '#token_types' => [
          'node',
          'custom',
        ],
        '#global_types' => TRUE,
      ];
      // @FIXME
      // theme() has been renamed to _theme() and should NEVER be called directly.
      // Calling _theme() directly can alter the expected output and potentially
      // introduce security issues (see https://www.drupal.org/node/2195739). You
      // should use renderable arrays instead.
      // 
      // 
      // @see https://www.drupal.org/node/2195739
      // $form['body']['#title'] = theme('token_help', 'node');

    }
    $form['submit'] = [
      '#type' => 'submit',
      '#value' => t('Save'),
    ];

    if (!empty($template)) {
      if ($form_state->getValue(['name'])) {
        $form['name']['#default_value'] = $form_state->getValue(['name']);
        $form['name']['#disabled'] = TRUE;
        $form['name']['#value'] = $form_state->getValue(['name']);
      }

      // If it's an existing template, offer a delete button.
      $form['delete'] = [
        '#type' => 'submit',
        '#value' => t('Delete'),
      ];
    }
    return $form;
  }

  public function submitForm(array &$form, \Drupal\Core\Form\FormStateInterface $form_state) {
    //if we're deleting the template
    if ($form_state->get(['clicked_button', '#id']) == 'edit-delete') {
      //show the confirmation
      $form_state->setRebuild(TRUE);
      $form_state->set(['confirm_delete'], TRUE);
      return;
    }
    // delete confirmation provided
    if (!$form_state->getValue(['delete'])) {
      if ($form_state->getValue(['delete']) === TRUE) {
        // delete image if one was uploaded
        if ($form_state->getValue(['template_image_fid'])) {
          file_delete($form_state->getValue(['template_image_fid']));
        }
        wysiwyg_template_delete_template($form_state->getValue(['name']));
        drupal_set_message(t('The template has been deleted.'));
        $form_state->set(['redirect'], 'admin/config/content/wysiwyg-templates');
        return;
      }
    }
    // drop image if selected and checked
    if (!$form_state->getValue(['template_image_delete']) && $form_state->getValue(['template_image_delete'])) {
      file_delete(file_load($form_state->getValue(['template_image_fid'])));
      // unset the fid previously used
      $form_state->setValue(['template_image_fid'], 0);
    }
    // prepare file if needed
    $filepath = 'public://wysiwyg_template_images/';
    file_prepare_directory($filepath, FILE_CREATE_DIRECTORY);
    // save the image, validate it against file_validate_extensions
    $file = file_save_upload('template_image', [
      'file_validate_extensions' => [
        'jpg png gif jpeg'
        ]
      ], $filepath);
    if ($file) {
      // set status to permanent
      $file->status = FILE_STATUS_PERMANENT;
      $file = file_save($file);
      if ($file) {
        $form_state->setValue(['fid'], $file->fid);
        // delete previous file if it had one
        if ($form_state->getValue(['template_image_fid'])) {
          file_delete(file_load($form_state->getValue(['template_image_fid'])));
        }
      }
    }
    else {
      $form_state->setValue(['fid'], $form_state->getValue(['template_image_fid']));
    }
    // save the template
    // Flatten body field data.
    $template = $form_state->getValues();
    $template['body'] = $form_state->getValue(['body', 'value']);
    $template['format'] = $form_state->getValue(['body', 'format']);
    if (wysiwyg_template_save_template($template)) {
      drupal_set_message(t('The template has been saved.'));
    }
    else {
      drupal_set_message(t('There was an error saving the template to the database.'));
    }
    // redirect back to the overview page
    $form_state->set(['redirect'], 'admin/config/content/wysiwyg-templates');
  }

}
