<?php

namespace Oblik\KirbyTester;

use Kirby;

class Loader
{
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
        if (!$this->isKirbyLoaded) {
            require $this->dirRoot . '/kirby/bootstrap.php';
        }

        $instance = new Kirby([
            'roots' => $this->roots
        ]);
        
        // Make sure to include the bootstrap script after the instance is
        // created so it can use kirby() to get access to it.
        if ($bootstrap = realpath($this->dirTests . '/bootstrap.php')) {
            include_once $bootstrap;
        }

        return $instance;
    }
}