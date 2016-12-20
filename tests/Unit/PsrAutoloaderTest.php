<?php

namespace Moon\Autoloader;

class PsrAutoloaderTest extends \PHPUnit_Framework_TestCase
{
    public function testRegister()
    {
        $numberOfAutoloader = count(spl_autoload_functions());
        $autoloader = new PsrAutoloader();
        $autoloader->register();
        $this->assertEquals($numberOfAutoloader + 1, count(spl_autoload_functions()));
        $autoloader->unregister();
    }

    public function testUnregister()
    {
        $numberOfAutoloader = count(spl_autoload_functions());
        $autoloader = new PsrAutoloader();
        $autoloader->register();
        $autoloader->unregister();
        $this->assertEquals(count(spl_autoload_functions()), $numberOfAutoloader);
    }

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

    public function testLoadClass()
    {
        $autoloader = new PsrAutoloader();
        $autoloader->addNamespace('Foo\\Oof\\', 'tests/Unit/Vendor/Foo/Oof', PsrAutoloader::PSR0);
        $autoloader->addNamespace('Bar\\Rab\\', 'tests/Unit/Vendor/Bar/Rab');
        $autoloader->addNamespace('Fuz\\Zuf\\', 'tests/Unit/Vendor/Fuz/Zuf');
        $autoloader->register();

        $this->assertInstanceOf(\Bar\Rab\Smile::class, new \Bar\Rab\Smile());
        $this->assertInstanceOf(\Foo\Oof\Alien::class, new \Foo\Oof\Alien());
        $this->assertInstanceOf(\Foo\Oof\Sub_One_Two_File::class, new \Foo\Oof\Sub_One_Two_File());

        $autoloader->unregister();
    }
}