# kirby-modules
**WARNING: This is still in early alpha. Use at your own discretion.**

`kirby-modules` is a custom field for Kirby which allows you to divide a page into modules. A module can be repeated multiple times on a single page as well as re-used between multiple page blueprints.

A similar concept would be a more advanced version of Kirby’s structure field or [ACF](https://www.advancedcustomfields.com/add-ons/repeater-field/) for Wordpress.

`kirby-modules` also allows a module definition to contain other modules to further modularise the page content into small reusable chunks.


## Installation
Download the [zip](https://github.com/frekw/kirby-modules/archive/master.zip) and move the contents of the `plugins` directory to `site/plugins` and the contents of the `fields` directory into `site/fields`.

Since `kirby-modules` consists of both a plugin and a field, installation via `git submodule add` is unfortunately not possible.

## Motivation
Often when working with content – whether it’s a one pager or an article – you want to separate the styling from the content as much as possible.

Decoupling the content from the presentation (e.g not using inline images) makes it easier to make future updates to the presentation without modifying the underlying content as well as   exposing the content via an API for use in other places, such as a mobile app, without needing to resort to parsing HTML on the consuming side.

By modularising the content it also becomes very natural to implement each part of the content as smaller modules which is beneficial for maintenance and code re-use.

## Creating modules
A module consists of a three parts:
1. A blueprint specifying the fields. The blueprint resides in `site/blueprints/modules/module-name.php`
2. A template (and possibly controller) responsible for rendering of the module. The template resides in `site/modules/module-name/template.php`
3. CSS and Javascript. These reside in `site/modules/module-name/assets/[css|js]`.

A module may also have an optional `options` field controlling options that can be used to e.g modify how the module is rendered.

`site/blueprints/module/hero.php`
```yaml
type: Hero
fields:
  title:
    label: Title
    type: text

  subtitle:
    label: Subtitle
    type: textarea

  image:
    label: Image
    type: selector


options:
  position:
    type: select
    required: true
    label: Text Position
    options:
      left: Left
      right: Right
```

`site/modules/hero/template.php`
```php
<div class=“hero hero—align-<?php echo $module->options->position->value() ?>”
     <?php if($module->image->value()): ?>style=“background-image:url(<?php echo $module->image->toFile()->url() ?>);”<?php endif; ?>>
  <h1><?php echo $module->title->value() ?></h1>
  <?php if(!$module->subtitle->empty()): ?>
    <?php echo $module->subtitle->kirbytext() ?>
  <?php endif; ?>
</div>

```

 definition as well as its own template and controller logic to make it as isolated as possible.

### Using a module
You use a module by adding a `modules` field to a blueprint, such as:

```yaml
fields:
  title:
    label: Title
    type:  text
  body:
    label: Body
    type:  textarea

  page_modules:
    label: Modules
    type: modules

    modules:
      types:
        - Hero
```

The `modules` field allows you to specify what modules the blueprint may contain.

### Nesting modules
You can nest modules by adding a `modules` field to the module’s field definition.

### Field helpers
`kirby-modules` adds the `modules` field helper that will render all modules for a modules field with their corresponding controller and template.

### Assets
`kirby-modules` can combine all javascripts and stylesheets for all modules on demand.

To use this, modify `site/config/config.php` to contain

```php
c::set(‘modules.assets’, true);
```

and use them by adding them to your template:

```php
  <?php echo css(‘modules.css’) ?>
  <?php echo js(‘modules.js’) ?>

```

## Known issues and limitations
- Module field validation
- Change tracking

## Contributing
Feature requests are unlikely to be implemented but pull requests with new features or bug fixes are welcome!
