<?php /**
 * @file
 * Contains \Drupal\wysiwyg_template\Controller\DefaultController.
 */

namespace Drupal\wysiwyg_template\Controller;

use Drupal\Core\Controller\ControllerBase;

/**
 * Default controller for the wysiwyg_template module.
 */
class DefaultController extends ControllerBase {

  public function wysiwyg_template_list_js($editorName, $contentType) {
    global $base_url;
    //don't cache templates
    drupal_add_http_header('CacheControl', 'no-cache');
    drupal_add_http_header('Expires', '-1');
    drupal_add_http_header('Content-Type', 'text/javascript; charset=UTF-8');
    $templates = wysiwyg_template_load_all($contentType);
    switch ($editorName) {
      case 'tinymce':
        print 'var tinyMCETemplateList = ';
        $outArray = [];
        foreach ($templates as $template) {
          // @FIXME
// url() expects a route name or an external URI.
// $outArray[] = array(
//           $template['title'],
//           url('wysiwyg-templates/' . $editorName . '/load/') . $template['name'],
//           $template['description'],
//         );

        }
        print json_encode($outArray) . ";";
        break;
      case 'ckeditor':
        print "CKEDITOR.addTemplates( 'default', { imagesPath:'" . $base_url . "', templates: ";
        // load the templates into the json array structure
        foreach ($templates as &$template) {
          $template['html'] = $template['body'];
          unset($template['body']);
          unset($template['tid']);
        }
        print json_encode($templates);
        print "});";
        break;
      case 'fckeditor':
        print '<?xml version="1.0" encoding="utf-8" ?>';
        print '<Templates imagesBasePath="">';
        foreach ($templates as $template) {
          print '<Template title="' . \Drupal\Component\Utility\Html::escape($template['title']) . '" image="">';
          print '<Description>' . \Drupal\Component\Utility\Html::escape($template['description']) . '</Description>';
          print '<Html><![CDATA[';
          print $template['body'];
          print ']]></Html></Template>';
        }
        print '</Templates>';
        break;
      case '':
        break;
    }
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
