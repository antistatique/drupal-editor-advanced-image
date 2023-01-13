<?php

declare(strict_types = 1);

namespace Drupal\Tests\editor_advanced_image\Kernel\CKEditor4To5Upgrade;

use Drupal\Tests\ckeditor5\Kernel\CKEditor4to5UpgradeCompletenessTest as Real;
use Drupal\KernelTests\KernelTestBase;

// phpcs:ignoreFile

if (class_exists(Real::class)) {
  class CKEditor4to5UpgradeCompletenessTest extends Real {}
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
