<?php

declare(strict_types=1);

namespace Moon\Autoloader\Unit;

use Moon\Autoloader\MapAutoloader;
use PHPUnit\Framework\TestCase;

class MapAutoloaderTest extends TestCase
{
    /**
     * Test that autoload has been registered.
     */
    public function testRegister(): void
    {
        $numberOfAutoloader = \count(\spl_autoload_functions() ?: []);
        $autoloader = new MapAutoloader();
        $autoloader->register();
        $this->assertEquals($numberOfAutoloader + 1, \count(\spl_autoload_functions() ?: []));
        $autoloader->unregister();
    }

    /**
     * Test that autoload has been unregistered.
     */
    public function testUnregister(): void
    {
        $numberOfAutoloader = \count(\spl_autoload_functions() ?: []);
        $autoloader = new MapAutoloader();
        $autoloader->register();
        $autoloader->unregister();
        $this->assertEquals(\count(\spl_autoload_functions() ?: []), $numberOfAutoloader);
    }

    /**
     * Test that namespaces is added.
     */
    public function testAddNamespace(): void
    {
        $autoloader = new MapAutoloader();
        $autoloader->addNamespace('BaseExample\\', 'tests/Unit/Vendor/Map/Sub/SubTwo/BaseExample.php');
        $autoloader->addNamespace('\\Sample\\', 'tests/Unit/Vendor/Map/Sub/SubThree/Sample.php');
        $autoloader->register();

        $reflectionClass = new \ReflectionClass($autoloader);
        $namespaces = $reflectionClass->getProperty('namespaces');
        $namespaces->setAccessible(true);
        $namespacesValue = $namespaces->getValue($autoloader);

        $this->assertEquals('tests/Unit/Vendor/Map/Sub/SubThree/Sample.php', $namespacesValue['Sample']);
        $this->assertEquals('tests/Unit/Vendor/Map/Sub/SubTwo/BaseExample.php', $namespacesValue['BaseExample']);

        $autoloader->unregister();
    }

    /**
     * Test that classes can be loaded after been registered.
     */
    public function testLoadClass()
    {
        $autoloader = new MapAutoloader();
        $autoloader->addNamespace('BaseExample', 'tests/Unit/Vendor/Map/Sub/SubTwo/BaseExample.php');
        $this->assertTrue($autoloader->loadClass(\BaseExample::class));
    }

    /**
     * Test that classes return false if can't be loaded.
     */
    public function testLoadClassReturnFalse()
    {
        $autoloader = new MapAutoloader();
        $this->assertFalse($autoloader->loadClass(\BaseExample::class));
    }
}
