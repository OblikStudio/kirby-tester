<?php

namespace Oblik\KirbyTester;

use Kirby;

$loaded = class_exists('Kirby', false);
$dirRoot = explode(DIRECTORY_SEPARATOR . 'site' . DIRECTORY_SEPARATOR . 'plugins', __DIR__)[0];
$dirPlugin = explode(DIRECTORY_SEPARATOR . 'vendor', __DIR__)[0];
$dirTests = "$dirPlugin/tests";

$roots = [];

if (!empty($dirTests)) {
    $rootNames = array_keys(include "$dirRoot/kirby/config/roots.php");

    foreach ($rootNames as $root) {
        $path = $dirTests . '/roots/' . $root;

        if (file_exists($path)) {
            $roots[$root] = $path;
        }
    }
}

if (!$loaded) {
    require $dirRoot . '/kirby/bootstrap.php';
}

new Kirby([
    'roots' => $roots
]);
