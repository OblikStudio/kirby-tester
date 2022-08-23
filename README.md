# kirby-tester

Allows you to easily write plugin tests by automatically loading Kirby and
optionally changing its roots.

## ⚠ Deprecated!

You don't need a plugin to change roots. Your plugin repo could double as a test site for the plugin. Check [Monolithic Plugin Setup](https://getkirby.com/docs/cookbook/setup/monolithic-plugin-setup) for details.

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

### Bootstrap hooks

You can use `tests/bootstrap.php` to do various things at different points of
the script execution by defining hooks:

```php
return [
    'beforeLoad' => function () {
        echo 'before Kirby vendor autoload';
    },
    'afterLoad' => function () {
        echo 'after Kirby vendor autoload';
    },
    'beforeInit' => function () {
        echo 'before Kirby instance';
    },
    'afterInit' => function ($kirby) {
        echo 'after Kirby instance';
    }
];
```

By default, hooks are run only when PHP is running from the CLI (i.e. when tests
are running). If you want them to always run, set `always` to `true` inside the
configuration above.
