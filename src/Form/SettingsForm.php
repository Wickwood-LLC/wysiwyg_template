<?php

namespace Drupal\wysiwyg_template\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\File\FileSystemInterface;
use Drupal\Core\Messenger\MessengerInterface;
use Symfony\Component\Finder\Finder;

/**
 * WYSIWYG Template settings.
 */
class SettingsForm extends ConfigFormBase {
  /** @var string Config settings */
  const SETTINGS = 'wysiwyg_template.settings';

  const CSS_FILE_DIRECTORY = 'public://mce';

  const EDITOR_CSS_FILE_NAME = 'editor.css';

  const VIEW_CSS_FILE_NAME = 'view.css';

  public function getFormId() {
    return 'wysiwyg_template_settings';
  }

  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames() {
    return [
      static::SETTINGS,
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $config = $this->config(static::SETTINGS);

    $form['editor_css'] = [
      '#type' => 'textarea',
      '#title' => $this->t('Editor CSS'),
      '#default_value' => $config->get('editor_css'),
      '#description' => $this->t('CSS to be used for WYSIWYG templates in editor.'),
    ];

    $form['view_css'] = [
      '#type' => 'textarea',
      '#title' => $this->t('View CSS'),
      '#default_value' => $config->get('view_css'),
      '#description' => $this->t('CSS to be used when WYSIWYG template are being used on content view pages.'),
    ];

    return parent::buildForm($form, $form_state);
  }

  public function validateForm(array &$form, FormStateInterface $form_state) {
    $directory = static::CSS_FILE_DIRECTORY;
    if (! \Drupal::service('file_system')->prepareDirectory($directory, FileSystemInterface::CREATE_DIRECTORY | FileSystemInterface::MODIFY_PERMISSIONS)) {
      $form_state->setErrorByName('editor_css', t('CSS content cannot be written to file.'));
    }
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    // Retrieve the configuration
     $this->configFactory->getEditable(static::SETTINGS)
    // Set the submitted editor CSS setting
    ->set('editor_css', $form_state->getValue('editor_css'))
    ->set('view_css', $form_state->getValue('view_css'))
    ->save();

    if ($new_editor_css_file_name = static::regenerateCSSFile('editor', $form_state->getValue('editor_css'))) {
      \Drupal::state()->set('wysiwyg_template.editor_css_file', $new_editor_css_file_name);
      $message = $this->t('Editor CSS has been written to file "@file."', ['@file'=> $new_editor_css_file_name]);
      $status = MessengerInterface::TYPE_STATUS;
    }
    else {
      $message = $this->t('Editor CSS could not be written to file.');
      $status = MessengerInterface::TYPE_ERROR;
    }
    \Drupal::messenger()->addMessage($message, $status);

    if (\Drupal::service('file_system')->saveData($form_state->getValue('view_css'), static::getViewCSSFilePath(), FileSystemInterface::EXISTS_REPLACE)) {
      $message = $this->t('View CSS has been written to file.');
      $status = MessengerInterface::TYPE_STATUS;
    }
    else {
      $message = $this->t('View CSS could not be written to file.');
      $status = MessengerInterface::TYPE_ERROR;
    }
    \Drupal::messenger()->addMessage($message, $status);

    parent::submitForm($form, $form_state);
  }

  public static function  getViewCSSFilePath() {
    return self::CSS_FILE_DIRECTORY . '/' . self::VIEW_CSS_FILE_NAME;
  }

  public static function regenerateCSSFile($prefix, $content) {
    $new_file_name = $prefix . '-' . time() . '.css';
    /** @var \Drupal\Core\File\FileUrlGeneratorInterface $file_url_generator */
    $file_url_generator = \Drupal::service('file_url_generator');
    // $css_file_directory_path = $file_url_generator->generateString(self::CSS_FILE_DIRECTORY);
    $css_file_directory_path = \Drupal::service('file_system')->realpath(self::CSS_FILE_DIRECTORY);
    $finder = new Finder();
    $finder->name($prefix . '-*.css');
    foreach ($finder->in($css_file_directory_path) as $file) {
      unlink($file->getPathname());
    }
    
    if (\Drupal::service('file_system')->saveData($content, self::CSS_FILE_DIRECTORY . '/' . $new_file_name, FileSystemInterface::EXISTS_REPLACE)) {
      return $new_file_name;
    }
  }
}