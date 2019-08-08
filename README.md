# kirby-tester

Allows you to easily write plugin tests by automatically loading Kirby and
optionally changing its roots.

## Installation

Install the package in either your plugin folder (recommended), or your site folder:

```
composer require oblik/kirby-tester --dev
```

## Usage

The utility expects you to have the following directory setup in your plugin:

```
my-plugin
└── tests
    ├── roots
    │   ├── blueprints
    │   ├── config
    │   ├── content
    │   ├── templates
    │   └── ...
    └── bootstrap.php
```

Any valid root folders in `tests/roots` will be used as Kirby roots. If a
`tests/bootstrap.php` script is found, it will be included as well after Kirby
has been loaded. This is can be useful to run some initializations.

### As a plugin dependency

When installed inside your plugin's `vendor` folder, the tester will
automatically figure out in which plugin it is and where the Kirby root is. It
will also load Kirby (via its bootstrap script) and create a new Kirby instance
based on your folder setup.

If you wish to also use the panel with the modified Kirby roots, you can change
your site's _index.php_ like that:

```php
require 'site/plugins/my-plugin/vendor/autoload.php';
echo kirby()->render();
```

**Note:** If you also have [PHPUnit](https://phpunit.de/) installed, you can run
tests right away. Since PHPUnit runs the Composer autoloader, this package will
be loaded, which will also load Kirby.

### As a site dependency

When installed inside your site's `vendor` folder, you need to use the global
`kirbytest()` function provided to you:

#### `kirbytest(string $input)`

- `@param $input` can be either a plugin name or a path to your _tests_ folder
  - if it's a plugin name, the _tests_ folder path will be
    `site/plugins/$input/tests`
  - if it's a relative or absolute path, it should point to the _tests_ folder
    itself
- `@returns` a Kirby instance so and you can chain its `render()` method;
  returns `null` if no _tests_ folder is found

In your site's _index.php_, you can use it like this:

```php
require 'kirby/bootstrap.php';
echo kirbytest('my-plugin')->render();
```
