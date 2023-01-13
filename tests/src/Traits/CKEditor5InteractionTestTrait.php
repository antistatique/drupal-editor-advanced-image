<?php

namespace Drupal\Tests\editor_advanced_image\Traits;

/**
 * Provides methods to test CKEditor 5 interactions.
 *
 * This trait is meant to be used only by functional JavaScript test classes.
 * This trait is inspired from
 * \Drupal\Tests\ckeditor5\FunctionalJavascript\CKEditor5TestBase.
 */
trait CKEditor5InteractionTestTrait {

  /**
   * Trigger a keyup event on the selected element.
   *
   * @param string $selector
   *   The css selector for the element.
   * @param string $key
   *   The keyCode.
   */
  protected function triggerKeyUp(string $selector, string $key) {

    $script = <<<JS
(function (selector, key) {
  const btn = document.querySelector(selector);
    btn.dispatchEvent(new KeyboardEvent('keydown', { key }));
    btn.dispatchEvent(new KeyboardEvent('keyup', { key }));
})('{$selector}', '{$key}')

JS;

    $options = [
      'script' => $script,
      'args' => [],
    ];

    $this->getSession()->getDriver()->getWebDriverSession()->execute($options);
  }

  /**
   * Gets the titles of the vertical tabs in the given container.
   *
   * @param string $container_selector
   *   The container in which to look for vertical tabs.
   * @param bool $visible_only
   *   (optional) Whether to restrict to only the visible vertical tabs. TRUE by
   *   default.
   *
   * @return string[]
   *   The titles of all vertical tabs menu items, restricted to only
   *   visible ones by default.
   *
   * @throws \LogicException
   */
  private function getVerticalTabs(string $container_selector, bool $visible_only = TRUE): array {
    $page = $this->getSession()->getPage();

    // Ensure the container exists.
    $container = $page->find('css', $container_selector);
    if ($container === NULL) {
      throw new \LogicException('The given container should exist.');
    }

    // Make sure that the container selector contains exactly one Vertical Tabs
    // UI component.
    $vertical_tabs = $container->findAll('css', '.vertical-tabs');
    if (count($vertical_tabs) != 1) {
      throw new \LogicException('The given container should contain exactly one Vertical Tabs component.');
    }

    $vertical_tabs = $container->findAll('css', '.vertical-tabs__menu-item');
    $vertical_tabs_titles = [];
    foreach ($vertical_tabs as $vertical_tab) {
      if ($visible_only && !$vertical_tab->isVisible()) {
        continue;
      }
      $title = $vertical_tab->find('css', '.vertical-tabs__menu-item-title')->getHtml();
      // When retrieving visible vertical tabs, mark the selected one.
      if ($visible_only && $vertical_tab->hasClass('is-selected')) {
        $title = "➡️$title";
      }
      $vertical_tabs_titles[] = $title;
    }
    return $vertical_tabs_titles;
  }

}
