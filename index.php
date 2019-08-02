<?php

namespace Oblik\KirbyTester;

function config(string $base, $name = null)
{
    $rootNames = array_keys(include "$base/kirby/config/roots.php");
    $roots = [];

    if ((empty($name) || PHP_SAPI === 'cli')) {
        $nameEnv = getenv('KIRBY_PLUGIN');

        if (is_string($nameEnv)) {
            $name = $nameEnv;
        }
    }

    if (is_string($name)) {
        if (preg_match('!^[.\\/\\\]!', $name)) {
            $dirTests = realpath($name);
        } else {
            $dirTests = "$base/site/plugins/$name/tests";
        }

        $dirRoots = $dirTests . '/roots';
    }

    if (!empty($dirTests)) {
        foreach ($rootNames as $root) {
            $path = $dirRoots . '/' . $root;

            if (file_exists($path)) {
                $roots[$root] = $path;
            }
        }

        $bootstrap = $dirTests . '/bootstrap.php';

        if (file_exists($bootstrap)) {
            include_once $bootstrap;
        }
    }

    return [
        'roots' => $roots
    ];
}
