<?php /**
 * @file
 * Contains \Drupal\wysiwyg_template\Controller\TemplateController.
 */

namespace Drupal\wysiwyg_template\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\node\NodeTypeInterface;
use Drupal\wysiwyg_template\Entity\Template;
use Symfony\Component\HttpFoundation\Response;

/**
 * Default controller for the wysiwyg_template module.
 */
class TemplateController extends ControllerBase {

  /**
   * A list of available templates based on the content type.
   *
   * @param \Drupal\node\NodeTypeInterface $node_type
   *   (optional) The content type. If set, only templates available for this
   *   node type will be returned.
   *
   * @return \Symfony\Component\HttpFoundation\Response
   *   The template callback JS.
   *
   * @see https://www.drupal.org/node/2693221
   */
  public function listJson(NodeTypeInterface $node_type = NULL) {
    $templates = [
      // @todo Support images.
      'imagesPath' => FALSE,
    ];
    /** @var \Drupal\wysiwyg_template\TemplateInterface $template */
    foreach (Template::loadMultiple() as $template) {
      $json_template = new \stdClass();
      $json_template->title = $template->label();
      // @todo Images.
      // @see https://www.drupal.org/node/2692469
      $json_template->description = $template->getDescription();
      $json_template->html = $template->getBody();

      $templates['templates'][] = $json_template;
    }

    $templates = json_encode($templates);

    $script = <<<"EOL"
CKEDITOR.addTemplates( 'default', $templates);
EOL;

    $response = new Response($script);
    $response->headers->set('Content-Type', 'text/javascript');
    return $response;
  }

  public function wysiwyg_template_html_print($body, $editorName) {
    //don't cache templates
    drupal_add_http_header('CacheControl', 'no-cache');
    drupal_add_http_header('Expires', '-1');
    drupal_add_http_header('Content-Type', 'text/javascript; charset=UTF-8');
    switch ($editorName) {
      case 'tinymce':
        print $body;
        break;
    }
  }

}
