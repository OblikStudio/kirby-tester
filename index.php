<?php

namespace Oblik\KirbyTester {
    $loader = new Loader();

    // If it's a plugin dependency, `dirPlugin` would be set.
    if ($loader->dirPlugin) {
        $loader->findRoots();
        $loader->init();
    }
}

namespace {
    use Oblik\KirbyTester\Loader;

    /**
     * Attempts to create a Kirby instance with modified roots. This function
     * can be used when the package is used as a site dependency.
     * @param string $input Plugin name or path to tests folder
     */
    function kirbytest(string $input)
    {
        $loader = new Loader();

        if (preg_match('![./\\\]!', $input)) {
            $loader->dirTests = realpath($input);
        } else {
            $loader->setPluginDir($loader->dirRoot . '/site/plugins/' . $input);
        }

        if ($loader->dirTests) {
            $loader->findRoots();
            return $loader->init();
        } else {
            return null;
        }
    }
}
