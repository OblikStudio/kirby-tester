<?php

namespace Oblik\KirbyTester;

use Kirby;

class Loader
{
    private $config = [
        'always' => false
    ];
    public $isKirbyLoaded = false;
    public $dirVendor;
    public $dirRoot;
    public $dirPlugin;
    public $dirTests;
    public $roots;

    public static function findDir($path, $search)
    {
        do {
            if (realpath($path . $search)) {
                return $path;
            }

            $path = dirname($path);
        } while ($path !== dirname($path));

        return false;
    }

    function __construct()
    {
        $this->isKirbyLoaded = class_exists('Kirby', false);
        $this->dirVendor = $this::findDir(__FILE__, '/autoload.php');

        if ($this->dirVendor) {
            $this->dirRoot = $this::findDir($this->dirVendor, '/kirby/bootstrap.php');
            $vendorParent = dirname($this->dirVendor);

            if ($this->dirRoot !== $vendorParent) {
                $this->setPluginDir($vendorParent);
            }
        }
    }

    private function hook($name, $args = []) {
        $cli = PHP_SAPI === 'cli';
        $hook = $this->config[$name] ?? null;
        
        if (is_callable($hook) && ($cli || $this->config['always'] === true)) {
            call_user_func_array($hook, $args);
        }
    }

    public function setPluginDir(string $path)
    {
        $this->dirPlugin = $path;

        if (file_exists($tests = $this->dirPlugin . '/tests')) {
            $this->dirTests = $tests;
        }
    }

    public function findRoots()
    {
        $allRoots = @include $this->dirRoot . '/kirby/config/roots.php';

        if (is_array($allRoots)) {
            $this->roots = null;

            foreach (array_keys($allRoots) as $name) {
                $root = $this->dirTests . '/roots/' . $name;

                if (file_exists($root)) {
                    $this->roots[$name] = $root;
                }
            }
        }
    }

    public function init()
    {
        if ($bootstrap = realpath($this->dirTests . '/bootstrap.php')) {
            $config = include_once $bootstrap;

            if (is_array($config)) {
                $this->config = array_replace_recursive($this->config, $config);
            }
        }

        if (!$this->isKirbyLoaded) {
            $this->hook('beforeLoad');
            require $this->dirRoot . '/kirby/bootstrap.php';
            $this->hook('afterLoad');
        }

        $this->hook('beforeInit');
        $instance = new Kirby([
            'roots' => $this->roots
        ]);
        $this->hook('afterInit', [$instance]);

        return $instance;
    }
}
