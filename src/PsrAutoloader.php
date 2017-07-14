<?php

declare(strict_types=1);

namespace Moon\Autoloader;

class PsrAutoloader
{
    const PSR0 = 'psr0';
    const PSR4 = 'psr4';

    private $namespaces = [
        self::PSR4 => [],
        self::PSR0 => []
    ];

    /**
     * Map a PSR namespace to a directory
     *
     * @param string $namespace Namespace for the vendor
     * @param string $directory Main directory for map the vendor classes
     * @param string $psr Specify only if is a PSR0
     */
    public function addNamespace(string $namespace, string $directory, string $psr = self::PSR4): void
    {
        // Clean namespace and directory
        $namespace = trim($namespace, '\\');
        $directory = rtrim($directory, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR;

        // If is not set the namespace create it
        if (!isset($this->namespaces[$psr][$namespace])) {
            $this->namespaces[$psr][$namespace] = [];
        }

        // Add the directory to the namespace
        $this->namespaces[$psr][$namespace][] = $directory;
    }

    /**
     * Require the mapped namespace if exists
     *
     * @param string $class Class name to search
     * @param null $pos Start position for search into the class name
     *
     * @return bool
     */
    public function loadClass(string $class, int $pos = null): bool
    {
        // If the position is null use the class length
        $pos = $pos ?: strlen($class);

        // Try to search if exists a namespace
        $namespace = substr($class, 0, $pos);

        // Set the namespace where search into and if is a PSR0
        if (isset($this->namespaces[self::PSR4][$namespace])) {
            $namespaces = $this->namespaces[self::PSR4];
        } elseif (isset($this->namespaces[self::PSR0][$namespace])) {
            $namespaces = $this->namespaces[self::PSR0];
            $isPsr0 = true;
        }

        // If doesn't exist pass a new position and search recursively
        if (!isset($namespaces)) {
            // Get the next \
            $pos = strrpos($namespace, '\\', 1);

            // Return if a new possible namespace isn't found
            if ($pos === false) {
                return false;
            }

            // Look for the class by a new position
            return $this->loadClass($class, $pos);
        }

        // Get the filename from the analyzed namespace
        $filename = substr($class, strlen($namespace) + 1);

        // If is a PSR0 replace the '_' with the '/'
        if (isset($isPsr0)) {
            $filename = str_replace('_', DIRECTORY_SEPARATOR, $filename);
        }

        // Search the file into each directory added to the namespace
        foreach ($namespaces[$namespace] as $key => $directory) {
            // If exists require it
            if (file_exists("$directory$filename.php")) {
                require "$directory$filename.php";

                return true;
            }
        }

        return false;
    }

    /**
     * Register the autoloader
     *
     * @param bool $prepend
     * @return bool
     */
    public function register($prepend = false): bool
    {
        return spl_autoload_register([$this, 'loadClass'], true, $prepend);
    }

    /**
     * Unregister the autoloader
     *
     * @return bool
     */
    public function unregister(): bool
    {
        return spl_autoload_unregister([$this, 'loadClass']);
    }
}