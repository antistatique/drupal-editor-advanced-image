<?php

namespace Drupal\Tests\editor_advanced_image\FunctionalJavascript;

use Drupal\FunctionalJavascriptTests\WebDriverTestBase;
use Behat\Mink\Exception\ElementNotFoundException;

/**
 * Has some additional helper methods to make test code more readable.
 */
abstract class UiTestBase extends WebDriverTestBase {

  /**
   * {@inheritdoc}
   */
  protected $defaultTheme = 'starterkit_theme';

  /**
   * Finds field (input, textarea, select) with specified locator.
   *
   * @param string $locator
   *   Input id, name or label.
   *
   * @return \Behat\Mink\Element\NodeElement|null
   *   The input field element.
   */
  public function findField($locator) {
    return $this->getSession()->getPage()->findField($locator);
  }

  /**
   * Finds button with specified locator.
   *
   * @param string $locator
   *   Button id, value or alt.
   *
   * @return \Behat\Mink\Element\NodeElement|null
   *   The button node element.
   */
  public function findButton($locator) {
    return $this->getSession()->getPage()->findButton($locator);
  }

  /**
   * Presses button with specified locator.
   *
   * @param string $locator
   *   Button id, value or alt.
   *
   * @throws \Behat\Mink\Exception\ElementNotFoundException
   */
  public function pressButton($locator) {
    $this->getSession()->getPage()->pressButton($locator);
  }

  /**
   * Click the element with specified selector.
   *
   * @param string $selector_type
   *   The element selector type (CSS, XPath).
   * @param string|array $selector
   *   The element selector. Note: the first found element is used.
   *
   * @return \Behat\Mink\Element\NodeElement|null
   *   The node element.
   *
   * @throws \Behat\Mink\Exception\ElementNotFoundException
   */
  public function clickOnElement($selector_type, $selector) {
    $element = $this->getSession()->getPage()->find($selector_type, $selector);

    if (!$element) {
      if (is_array($selector)) {
        $selector = implode(' ', $selector);
      }
      throw new ElementNotFoundException($this->getSession()->getDriver(), 'element', $selector_type, $selector);
    }

    $element->click();
    return $element;
  }

  /**
   * Fills in field (input, textarea, select) with specified locator.
   *
   * @param string $locator
   *   Input id, name or label.
   * @param string $value
   *   Value.
   *
   * @throws \Behat\Mink\Exception\ElementNotFoundException
   *
   * @see \Behat\Mink\Element\NodeElement::setValue
   */
  public function fillField($locator, $value) {
    $this->getSession()->getPage()->fillField($locator, $value);
  }

  /**
   * Make a screenshot and store it in the public://screenshots uri.
   */
  public function debugScreenshot() {
    $screenshot = \Drupal::root() . '/sites/default/files/simpletest/screenshot-' . time() . '.jpg';
    $this->createScreenshot($screenshot);
    echo "\n" . $screenshot . "\n";
  }

}
