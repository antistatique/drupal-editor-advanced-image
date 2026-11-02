ARG BASE_IMAGE_TAG=11.4
FROM wengerk/drupal-for-contrib:${BASE_IMAGE_TAG}

# Disable deprecation notice as CKEditor4 module will throw an exception until we remove support of Drupal 9.
# ENV SYMFONY_DEPRECATIONS_HELPER=disabled

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
