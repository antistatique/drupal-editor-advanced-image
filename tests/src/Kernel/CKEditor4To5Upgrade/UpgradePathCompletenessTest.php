<?php

declare(strict_types = 1);

namespace Drupal\Tests\editor_advanced_image\Kernel\CKEditor4To5Upgrade;

use Drupal\Tests\ckeditor5\Kernel\CKEditor4to5UpgradeCompletenessTest as Real;
use Drupal\KernelTests\KernelTestBase;

// phpcs:ignoreFile

if (class_exists(Real::class)) {
  class CKEditor4to5UpgradeCompletenessTest extends Real {

    /**
     * Tests that the test-only CKEditor 4 module does not have an upgrade path.
     */
    public function testButtonsWithTestOnlyModule(): void {
      $this->enableModules(['ckeditor_test']);
      $this->cke4PluginManager = $this->container->get('plugin.manager.ckeditor.plugin');

      $this->expectException(\OutOfBoundsException::class);

      // Since Drupal 10.0.x the Plugin name has changed LlamaCSS vs Llama.
      $this->expectExceptionMessageMatches('/^No upgrade path found for the "Llama(CSS)?" button\.$/');
      $this->testButtons();
    }

  }
}
else {
  class CKEditor4to5UpgradeCompletenessTest extends KernelTestBase {

    public function testImpossible() {
      $this->markTestSkipped();
    }

  }
}

/**
 * @covers \Drupal\editor_advanced_image\Plugin\CKEditor4To5Upgrade\EditorAdvancedImage
 *
 * @group editor_advanced_image
 * @group editor_advanced_image_kernel
 * @group editor_advanced_image_ckeditor5
 * @group ckeditor5
 *
 * @internal
 *
 * @requires module ckeditor5
 * @requires function Drupal\Tests\ckeditor5\Kernel\CKEditor4to5UpgradeCompletenessTest::setUp
 */
class UpgradePathCompletenessTest extends CKEditor4to5UpgradeCompletenessTest {

  /**
   * {@inheritdoc}
   */
  protected static $modules = ['editor_advanced_image'];

}
