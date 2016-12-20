<?php

namespace Moon\Autoloader;

class MapAutoloaderTest extends \PHPUnit_Framework_TestCase
{
    public function testRegister()
    {
        $numberOfAutoloader = count(spl_autoload_functions());
        $autoloader = new MapAutoloader();
        $autoloader->register();
        $this->assertEquals($numberOfAutoloader + 1, count(spl_autoload_functions()));
        $autoloader->unregister();
    }

    public function testUnregister()
    {
        $numberOfAutoloader = count(spl_autoload_functions());
        $autoloader = new MapAutoloader();
        $autoloader->register();
        $autoloader->unregister();
        $this->assertEquals(count(spl_autoload_functions()), $numberOfAutoloader);
    }

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