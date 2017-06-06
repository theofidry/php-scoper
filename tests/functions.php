<?php

declare(strict_types=1);

namespace Humbug\PhpScoper;

use Symfony\Component\Filesystem\Filesystem;

/**
 * Creates a temporary directory.
 *
 * @param string $namespace The directory path in the system's temporary
 *                          directory.
 * @param string $className The name of the test class.
 *
 * @return string The path to the created directory.
 */
function makeTempDir(string $namespace, string $className): string
{
    if (false !== ($pos = strrpos($className, '\\'))) {
        $shortClass = substr($className, $pos + 1);
    } else {
        $shortClass = $className;
    }

    // Usage of realpath() is important if the temporary directory is a
    // symlink to another directory (e.g. /var => /private/var on some Macs)
    // We want to know the real path to avoid comparison failures with
    // code that uses real paths only
    $systemTempDir = str_replace('\\', '/', realpath(sys_get_temp_dir()));
    $basePath = $systemTempDir.'/'.$namespace.'/'.$shortClass;

    while (false === @mkdir($tempDir = escape_path($basePath.rand(10000, 99999)), 0777, true)) {
        // Run until we are able to create a directory
    }

    return $tempDir;
}

function escape_path(string $path): string
{
    return str_replace('/', DIRECTORY_SEPARATOR, $path);
}

//TODO: https://github.com/humbug/php-scoper/pull/19/files#r118838268
function remove_dir(string $path)
{
    $path = escape_path($path);

    if (defined('PHP_WINDOWS_VERSION_BUILD')) {
        exec(sprintf('rd /s /q %s', escapeshellarg($path)));
    } else {
        $filesystem = new Filesystem();
        $filesystem->remove($path);
    }
}