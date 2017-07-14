<?php

declare(strict_types=1);

namespace Moon\Autoloader;

class MapAutoloader
{
    /**
     * @var array $namespaces
     */
    private $namespaces = [];

    /**
     * Map a namespace to a file
     *
     * @param string $namespace Namespace for the vendor
     * @param string $file File .php to autoload
     */
    public function addNamespace(string $namespace, string $file): void
    {
        $namespace = trim($namespace, '\\');
        $this->namespaces[$namespace] = $file;
    }

    /**
     * Require the mapped namespace if exists
     *
     * @param string $class Class name to search
     *
     * @return bool
     */
    public function loadClass(string $class): bool
    {
        if (isset($this->namespaces[$class]) && file_exists($this->namespaces[$class])) {
            require "{$this->namespaces[$class]}";

            return true;
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