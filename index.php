<?php

namespace Oblik\KirbyTester;

/**
 * Returns Kirby roots configuration and includes optional bootstrap script
 * @param string $base Kirby installation root
 * @param string|null $name Plugin name or path to tests folder
 */
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
    }

    if (!empty($dirTests)) {
        foreach ($rootNames as $root) {
            $path = $dirTests . '/roots/' . $root;

            if (file_exists($path)) {
                $roots[$root] = $path;
            }
        }

        $bootstrap = $dirTests . '/bootstrap.php';

        if (file_exists($bootstrap)) {
            include $bootstrap;
        }
    }

    return [
        'roots' => $roots
    ];
}
