ARG BASE_IMAGE_TAG=9.5
FROM wengerk/drupal-for-contrib:${BASE_IMAGE_TAG}

# Disable deprecation notice as CKEditor4 module will throw an exception until we remove support of Drupal 9.
ENV SYMFONY_DEPRECATIONS_HELPER=disabled

# Install drupal/ckeditor (CKEditor 4) as we keep supporting CK4 & CK5.
ENV COMPOSER_ALLOW_SUPERUSER=1
RUN COMPOSER_MEMORY_LIMIT=-1 composer require "drupal/ckeditor"

# Register the Drupal and DrupalPractice Standard with PHPCS.
RUN ./vendor/bin/phpcs --config-set installed_paths \
    `pwd`/vendor/drupal/coder/coder_sniffer

# Copy the Analyzer definition files to ease run.
COPY phpcs.xml.dist phpmd.xml ./

# Download & install PHPMD.
RUN set -eux; \
  curl -LJO https://phpmd.org/static/latest/phpmd.phar; \
  chmod +x phpmd.phar; \
  mv phpmd.phar /usr/bin/phpmd

# Download & install PHPCPD.
RUN set -eux; \
  curl -LJO https://phar.phpunit.de/phpcpd.phar; \
  chmod +x phpcpd.phar; \
  mv phpcpd.phar /usr/bin/phpcpd

# Download & install PhpDeprecationDetector.
RUN set -eux; \
  \
  apt-get update; \
  apt-get install -y \
   libbz2-dev \
  ; \
  \
  docker-php-ext-install bz2; \
  \
  curl -LJO https://github.com/wapmorgan/PhpDeprecationDetector/releases/download/2.0.24/phpcf-2.0.24.phar; \
  chmod +x phpcf-2.0.24.phar; \
  mv phpcf-2.0.24.phar /usr/bin/phpdd
