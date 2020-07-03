# Editor Advanced Image

Enhances the inline-image Dialog in D8 CKEditor.

## Features

Allows to define the following attributes:

- title
- class
- id

By using Editor Advanced Image, you will also be able to:

- define a default class for every images on an Editor
  (such `img-fluid` or `img-responsive`).

## Standard usage scenario

1. Install the module.
1. Open the "Text formats and editor" admin page admin/config/content/formats.

    If the "Limit allowed HTML tags and correct faulty HTML" filter is disabled
    you dont have anything to do with this text format.

    Otherwise, add the "title", "class" and/or "id" attributes to
    the "allowed HTML tags" field.
    (only those whitelisted will show up in the dialog).

## Versions

The version `8.x-1.x` is not compatible with Drupal `8.8.x`.

Drupal `8.8.x` brings some breaking change with tests and so you
must upgrade to `8.x-2.x` version of **Editor Advanced Image**.

## Which version should I use?

|Drupal Core|EAI         |
|:---------:|:----------:|
|8.7.x      |1.x         |
|8.8.x      |2.x         |
|9.x        |2.x         |

## Dependencies

The Drupal 8 & Drupal 9 version of Editor Advanced Image requires nothing !
Feel free to use it.

## Supporting organizations

This project is sponsored by Antistatique. We are a Swiss Web Agency,
Visit us at [www.antistatique.net](https://www.antistatique.net) or
[Contact us](mailto:info@antistatique.net).
