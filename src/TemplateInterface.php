<?php

/**
 * @file
 * Contains \Drupal\wysiwyg_template\TemplateInterface.
 */

namespace Drupal\wysiwyg_template;

use Drupal\Core\Config\Entity\ConfigEntityInterface;

/**
 * Provides an interface for defining Template entities.
 */
interface TemplateInterface extends ConfigEntityInterface {

  /**
   * Gets the template description.
   *
   * @return string
   *   The template description.
   */
  public function getDescription();

  /**
   * Gets the template body.
   *
   * @return string
   *   The template HTML body.
   */
  public function getBody();

  /**
   * Gets the text format.
   *
   * @return string
   *   The text format for the body.
   */
  public function getFormat();

  /**
   * Gets the template weight.
   *
   * @return int
   *   The template weight.
   */
  public function getWeight();

}
