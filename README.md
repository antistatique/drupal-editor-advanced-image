# Editor Advanced Image

Enhances the image Balloon in CKEditor 4 & CKEditor 5.

## Features

Allows to define the following attributes on an Image:
- title
- class
- id

## Standard usage scenario for CKEditor 4

1. Install the module.
1. Open the "Text formats and editor" admin page [/admin/config/content/formats](/admin/config/content/formats).

   If the "Limit allowed HTML tags and correct faulty HTML" filter is disabled
   you dont have anything to do with this text format.

   Otherwise, add the "title", "class" and/or "id" attributes to
   the "allowed HTML tags" field.
   (only those whitelisted will show up in the dialog).

## Standard usage scenario for CKEditor 5

1. Install the module.
1. Open the "Text formats and editor" admin page [/admin/config/content/formats](/admin/config/content/formats).

    Use the "Editor Advanced Image" configuration form to enable any attributes.
   Only those whitelisted here will be visible in CKEditor 5 Balloon.

## Which version should I use?

| Drupal Core | CKEditor | Editor Advanced Image |
|:-----------:|:--------:|:---------------------:|
|    8.7.x    |   4.x    |          1.x          |
|    8.8.x    |   4.x    |     8.x-2.0-beta1     |
|     9.x     |   4.x    |        8.x-2.1        |
|     9.x     |   5.x    |        8.x-2.1        |
|    10.x     |   4.x    |        8.x-2.1        |
|    10.x     |   5.x    |         3.0.x         |

## Dependencies

The Drupal 10 version of Editor Advanced Image requires
[Editor](https://www.drupal.org/project/editor) and
[CKEditor 5](https://ckeditor.com/ckeditor-5/).

The Drupal 8 & Drupal 9 versions of Editor Advanced Image requires
[Editor](https://www.drupal.org/project/editor) and
[CKEditor 4](https://www.drupal.org/project/ckeditor).

## Supporting organizations

This project is sponsored by Antistatique. We are a Swiss Web Agency,
Visit us at [www.antistatique.net](https://www.antistatique.net) or
[Contact us](mailto:info@antistatique.net).
