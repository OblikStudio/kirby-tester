# kirby-tester

Miniature utility that allows you to easily write tests for Kirby plugins. It
simply changes Kirby roots to those defined in your tests folder.

## Installation

Install the utility in your Kirby installation where you develop your plugins:

```
composer require oblik/kirby-tester
```

## Usage

Change your Kirby root _index.php_ to the following:

```php
use function Oblik\KirbyTester\config;

require 'kirby/bootstrap.php';

echo (new Kirby(config(__DIR__, 'my-plugin')))->render();
```

### `config($base, $name)`

Based on your input, this function will figure out which _tests_ folder to use.
Such a folder should have the following directory structure:

```
/tests
  /roots
    /content
    /blueprints
    /config
    /templates
    ...
  bootstrap.php
```

- `$base` should point to your Kirby installation root folder
- `$name` can be either a plugin name or a path to your tests folder
  - if it's a plugin name, the tests folder path will be
    `$base/site/plugins/$name/tests`
  - if it's a relative or absolute path, it should point to the _tests_ folder
    itself

If `$name` is empty or PHP is run from a CLI, the `KIRBY_PLUGIN` environment
variable will be used. I can have the same values as `$name`.

Any valid root folders in `tests/roots` will be used as Kirby roots. If a
`tests/bootstrap.php` script is found, it will be included as well. This is
useful to run test initializations.

**Note:** You can use the Panel as well because it'll be loaded with the modified roots.

### PHPUnit

The PHPUnit bootstrap script should be the Kirby root _index.php_ you modified
earlier. If your _phpunit.xml_ is in `kirby/site/plugins/my-plugin`, it should
look something like this:

```xml
<phpunit bootstrap="../../../index.php">
  <php>
    <env name="KIRBY_PLUGIN" value="my-plugin" force="true" />
  </php>
</phpunit>
```

Since you'd be running this from the CLI, the `KIRBY_PLUGIN` variable
will overwrite whatever value you set for `$name` in the `config()` function of
your _index.php_.

Read more about PHPUnit configuration
[here](https://phpunit.readthedocs.io/en/8.3/configuration.html#the-env-element).
