<?php

namespace Drupal\wysiwyg_template\Tests\Form;

use Drupal\Core\Url;
use Drupal\node\Entity\NodeType;
use Drupal\Tests\BrowserTestBase;
use Drupal\wysiwyg_template\Entity\Template;

/**
 * Tests the template CRUD forms.
 *
 * @group wysiwyg_template
 */
class CrudTest extends BrowserTestBase {

  /**
   * Admin user.
   *
   * @var \Drupal\user\UserInterface
   */
  protected $admin;

  /**
   * {@inheritdoc}
   */
  protected static $modules = ['wysiwyg_template', 'filter_test', 'node'];

  /**
   * {@inheritdoc}
   */
  public function setUp(): void {
    parent::setUp();

    $this->admin = $this->drupalCreateUser(['administer wysiwyg templates', 'administer filters']);
    $this->drupalLogin($this->admin);
  }

  /**
   * Tests the CRUD UI.
   */
  public function testCrudUi() {

    // Add.
    $this->drupalGet(Url::fromRoute('entity.wysiwyg_template.collection'));
    // TODO: Drupal Rector Notice: Please delete the following comment after you've made any necessary changes.
    // Verify the assertion: pageTextContains() for HTML responses, responseContains() for non-HTML responses.
    // The passed text should be HTML decoded, exactly as a human sees it in the browser.
    $this->assertSession()->pageTextContains(t('There are no WYSIWYG templates yet.'));
    $this->drupalGet(Url::fromRoute('entity.wysiwyg_template.add_form'));
    // Node type selection should be hidden if there are less than 2 types.
    // TODO: Drupal Rector Notice: Please delete the following comment after you've made any necessary changes.
    // Verify the assertion: pageTextNotContains() for HTML responses, responseNotContains() for non-HTML responses.
    // The passed text should be HTML decoded, exactly as a human sees it in the browser.
    $this->assertSession()->pageTextNotContains(t('Available for content types'));
    $id = strtolower($this->randomMachineName());
    $edit = [
      'id' => $id,
      'label' => $this->randomString(),
      'description' => $this->randomString(),
      'body[value]' => $this->randomString(),
    ];
    $this->submitForm($edit, t('Save'));
    $this->assertSession()->addressEquals(Url::fromRoute('entity.wysiwyg_template.collection'));
    $this->assertSession()->assertEscaped($id);
    $this->assertSession()->assertEscaped($edit['label']);

    /** @var \Drupal\wysiwyg_template_core\TemplateInterface $template */
    $template = Template::load($id);
    $this->assertEquals('filter_test', $template->getFormat());

    // Edit.
    $this->drupalGet($template->toUrl('edit-form'));
    $edit['label'] = $this->randomString(12);
    unset($edit['id']);
    $this->submitForm($edit, t('Save'));
    $this->assertSession()->addressEquals(Url::fromRoute('entity.wysiwyg_template.collection'));
    $this->assertSession()->assertEscaped($id);
    $this->assertSession()->assertEscaped($edit['label']);

    // Delete.
    $this->drupalGet($template->toUrl('delete-form'));
    $this->submitForm([], t('Delete'));
    // TODO: Drupal Rector Notice: Please delete the following comment after you've made any necessary changes.
    // Verify the assertion: pageTextContains() for HTML responses, responseContains() for non-HTML responses.
    // The passed text should be HTML decoded, exactly as a human sees it in the browser.
    $this->assertSession()->pageTextContains(t('There are no WYSIWYG templates yet.'));

    // Add a few node types.
    $type1 = NodeType::create([
      'type' => $this->randomMachineName(),
      'label' => $this->randomString(),
    ]);
    $type1->save();
    $type2 = NodeType::create([
      'type' => $this->randomMachineName(),
      'name' => $this->randomString(),
    ]);
    $type2->save();
    $this->drupalGet(Url::fromRoute('entity.wysiwyg_template.add_form'));
    // Node type selection should be hidden if there are less than 2 types.
    // TODO: Drupal Rector Notice: Please delete the following comment after you've made any necessary changes.
    // Verify the assertion: pageTextContains() for HTML responses, responseContains() for non-HTML responses.
    // The passed text should be HTML decoded, exactly as a human sees it in the browser.
    $this->assertSession()->pageTextContains(t('Available for content types'));
    $id = strtolower($this->randomMachineName());
    $edit = [
      'id' => $id,
      'label' => $this->randomString(),
      'description' => $this->randomString(),
      'body[value]' => $this->randomString(),
      'node_types[' . $type2->id() . ']' => 1,
    ];
    $this->submitForm($edit, t('Save'));
    $template = Template::load($id);
    $this->assertEquals([$type2->id()], $template->getNodeTypes());
  }

}
