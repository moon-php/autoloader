<?php

namespace Moon\Autoloader\Unit;

use Moon\Autoloader\PsrAutoloader;
use PHPUnit\Framework\TestCase;

class PsrAutoloaderTest extends TestCase
{
    /**
     * Test that autoload has been registered
     */
    public function testRegister()
    {
        $numberOfAutoloader = count(spl_autoload_functions());
        $autoloader = new PsrAutoloader();
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
        $autoloader = new PsrAutoloader();
        $autoloader->register();
        $autoloader->unregister();
        $this->assertEquals(count(spl_autoload_functions()), $numberOfAutoloader);
    }

    /**
     * Test that namespaces is added
     */
    public function testAddNamespace()
    {
        $autoloader = new PsrAutoloader();
        $autoloader->addNamespace('Foo\\Oof\\', 'tests/Unit/Vendor/Foo/Oof', PsrAutoloader::PSR0);
        $autoloader->addNamespace('Bar\\Rab\\', 'tests/Unit/Vendor/Bar/Rab');
        $autoloader->addNamespace('Fuz\\Zuf\\', 'tests/Unit/Vendor/Fuz/Zuf');
        $autoloader->register();

        $reflectionClass = new \ReflectionClass($autoloader);
        $namespaces = $reflectionClass->getProperty('namespaces');
        $namespaces->setAccessible(true);
        $namespacesValue = $namespaces->getValue($autoloader);

        $this->assertArrayHasKey(PsrAutoloader::PSR0, $namespacesValue);
        $this->assertArrayHasKey(PsrAutoloader::PSR4, $namespacesValue);
        $this->assertArrayHasKey('Foo\\Oof', $namespacesValue[PsrAutoloader::PSR0]);
        $this->assertArrayHasKey('Bar\\Rab', $namespacesValue[PsrAutoloader::PSR4]);
        $this->assertEquals('tests/Unit/Vendor/Fuz/Zuf/', $namespacesValue[PsrAutoloader::PSR4]['Fuz\\Zuf'][0]);

        $autoloader->unregister();
    }

    /**
     * Test that classes can be loaded after been added
     */
    public function testLoadClass()
    {
        $autoloader = new PsrAutoloader();
        $autoloader->addNamespace('Foo\\Oof\\', 'tests/Unit/Vendor/Foo/Oof', PsrAutoloader::PSR0);
        $autoloader->addNamespace('Bar\\Rab\\', 'tests/Unit/Vendor/Bar/Rab');
        $autoloader->addNamespace('Fuz\\Zuf\\', 'tests/Unit/Vendor/Fuz/Zuf');
        $this->assertTrue($autoloader->loadClass(\Bar\Rab\Smile::class));
        $this->assertTrue($autoloader->loadClass(\Foo\Oof\Alien::class));
        $this->assertTrue($autoloader->loadClass(\Foo\Oof\Sub_One_Two_File::class));
    }

    /**
     * Test that classes return false when can't be added
     */
    public function testLoadClassReturnFalse()
    {
        $autoloader = new PsrAutoloader();
        $this->assertFalse($autoloader->loadClass(\Bar\Rab\Smile::class));
        $autoloader->addNamespace('Foo\\Oof\\', 'tests/Unit/Vendor/Foo/Oof', PsrAutoloader::PSR0);
        $this->assertFalse($autoloader->loadClass(\Foo\Oof\AlienNotExists::class));
    }
}