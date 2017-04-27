<?php

namespace Moon\Autoloader\Unit;

use Moon\Autoloader\MapAutoloader;
use PHPUnit\Framework\TestCase;

class MapAutoloaderTest extends TestCase
{
    /**
     * Test that autoload has been registered
     */
    public function testRegister()
    {
        $numberOfAutoloader = count(spl_autoload_functions());
        $autoloader = new MapAutoloader();
        $autoloader->register();
        $this->assertEquals($numberOfAutoloader + 1, count(spl_autoload_functions()));
        $autoloader->unregister();
    }

    /**
     * Test that autoload has been unregistered
     */
    public function testUnregister()
    {
        $numberOfAutoloader = count(spl_autoload_functions());
        $autoloader = new MapAutoloader();
        $autoloader->register();
        $autoloader->unregister();
        $this->assertEquals(count(spl_autoload_functions()), $numberOfAutoloader);
    }

    /**
     * Test that namespaces is added
     */
    public function testAddNamespace()
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
     * Test that classes can be loaded after been registered
     */
    public function testLoadClass()
    {
        $autoloader = new MapAutoloader();
        $autoloader->addNamespace('\\Sample\\', 'tests/Unit/Vendor/Map/Sub/SubThree/Sample.php');
        $autoloader->addNamespace('BaseExample\\', 'tests/Unit/Vendor/Map/Sub/SubTwo/BaseExample.php');
        $autoloader->register();

        $this->assertInstanceOf(\Sample::class, new \Sample());
        $this->assertInstanceOf(\BaseExample::class, new \BaseExample());

        $autoloader->unregister();
    }
}