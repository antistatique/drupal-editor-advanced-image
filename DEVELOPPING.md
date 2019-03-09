# Developing on Editor Advanced Image

* Issues should be filed at
https://www.drupal.org/project/issues/editor_advanced_image
* Pull requests can be made against
https://github.com/antistatique/drupal-editor-advanced-image/pulls

## 📦 Repositories

Drupal repo

  ```
  git remote add drupal git@git.drupal.org:project/editor_advanced_image.git
  ```

Github repo
  ```
  git remote add github https://github.com/antistatique/drupal-editor-advanced-image.git
  ```

## 🔧 Prerequisites

First of all, you need to have the following tools installed globally
on your environment:

  * drush
  * Latest dev release of Drupal 8.x.

## 🏆 Tests

Editor Advanced Image use WebDriverTestBase to test
Javascript web-based behaviors and interactions.

For tests you need a working database connection and for browser tests
your Drupal installation needs to be reachable via a web server.
Copy the phpunit config file:

  ```bash
  $ cd core
  $ cp phpunit.xml.dist phpunit.xml
  ```

You must provide `SIMPLETEST_BASE_URL`, Eg. `http://localhost`.
You must provide `SIMPLETEST_DB`,
Eg. `sqlite://localhost/build/editor_advanced_image.sqlite`.

Start PhantomJS:

  ```bash
  phantomjs --ssl-protocol=any --ignore-ssl-errors=true \
  vendor/jcalderonzumba/gastonjs/src/Client/main.js 8510 1024 768&
  ```

Run the javascript functional tests:

  ```bash
  # You must be on the drupal-root folder - usually /web.
  $ cd web
  $ SIMPLETEST_DB="sqlite://localhost//tmp/editor_advanced_image.sqlite" \
  SIMPLETEST_BASE_URL='http://d8.dev' \
  ../vendor/bin/phpunit -c core --testsuite functional-javascript \
  --group editor_advanced_image
  ```

Debug using

  ```bash
  # You must be on the drupal-root folder - usually /web.
  $ cd web
  $ SIMPLETEST_DB="sqlite://localhost//tmp/editor_advanced_image.sqlite" \
  SIMPLETEST_BASE_URL='http://d8.dev' \
  ../vendor/bin/phpunit -c core --testsuite functional-javascript \
  --group editor_advanced_image \
  --printer="\Drupal\Tests\Listeners\HtmlOutputPrinter" --stop-on-error
  ```

You must provide a `BROWSERTEST_OUTPUT_DIRECTORY`,
Eg. `/path/to/webroot/sites/simpletest/browser_output`.

## 🚔 Check Javascript best practices

You need to run `yarn` before using ESLint. Then run the commmand:

  ```bash
  ./node_modules/.bin/eslint ./
  ```

## 🚔 Check Drupal coding standards & Drupal best practices

You need to run composer before using PHPCS. Then register the Drupal
and DrupalPractice Standard with PHPCS:

  ```bash
  $ ./vendor/bin/phpcs --config-set installed_paths \
  `pwd`/vendor/drupal/coder/coder_sniffer
  ```

### Command Line Usage

Check Drupal coding standards:

  ```bash
  ./vendor/bin/phpcs --standard=Drupal --colors \
  --extensions=php,module,inc,install,test,profile,theme,css,info,md \
  --ignore=*/vendor/*,*/node_modules/* ./
  ```

Check Drupal best practices:

  ```bash
  ./vendor/bin/phpcs --standard=DrupalPractice --colors \
  --extensions=php,module,inc,install,test,profile,theme,css,info,md \
  --ignore=*/vendor/*,*/node_modules/* ./
  ```

Automatically fix coding standards

  ```bash
  ./vendor/bin/phpcbf --standard=Drupal --colors \
  --extensions=php,module,inc,install,test,profile,theme,css,info \
  --ignore=*/vendor/*,*/node_modules/* ./
  ```

### Improve global code quality using PHPCPD & PHPMD

Add requirements if necessary using `composer`:

  ```bash
  composer require --dev 'phpmd/phpmd:^2.6' 'sebastian/phpcpd:^3.0'
  ```

Detect overcomplicated expressions & Unused parameters, methods, properties

  ```bash
  ./vendor/bin/phpmd ./web/modules/custom text ./phpmd.xml
  ```

Copy/Paste Detector

  ```bash
  ./vendor/bin/phpcpd ./web/modules/custom
  ```

### Enforce code standards with git hooks

Maintaining code quality by adding the custom post-commit hook to yours.

  ```bash
  cat ./scripts/hooks/post-commit >> ./.git/hooks/post-commit
  ```
