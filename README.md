# Moon - AutoLoader
A very simple Autoloader

[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/moon-php/autoloader/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/moon-php/autoloader/?branch=master)
[![Code Coverage](https://scrutinizer-ci.com/g/moon-php/autoloader/badges/coverage.png?b=master)](https://scrutinizer-ci.com/g/moon-php/autoloader/?branch=master)
[![Build Status](https://scrutinizer-ci.com/g/moon-php/autoloader/badges/build.png?b=master)](https://scrutinizer-ci.com/g/moon-php/autoloader/build-status/master)

## Introduction
Autoloader is a standalone component incredibly easy.

It's a minimal autoloader with support for PSR4 and PSR0.

It also support a map autoloader.


## Usage

The autoloader has 2 different classes, one for PSR4/0 and another one for manual mapping.

Both has register and unregister methods for create or destroy an autoloader instance.
 
#### PSRAutoloader

    $autoloader = new PsrAutoloader();
    $autoloader->addNamespace('JohnSmith\\Container\\', 'vendor/JohnSmith/Container/src');
    $autoloader->addNamespace('JohnSmith\\Logger\\', 'vendor/JohnSmith/Logger/src');
    $autoloader->addNamespace('MarkBuzz\\Router\\', 'vendor/MarkBuzz/Router/src//MarkBuzz/Router/', PsrAutoloader::PSR0);
    $autoloader->register(); // For enable this autoloader 
    
    // Now you can create object without require all the files
    $container = new JohnSmith\Container\Container();
    $logger = new JohnSmith\Logger\StaticLogger();
    
    $autoloader->unregister(); // For disable this autoloader

#### MapAutoloader

    $autoloader = new MapAutoloader();
    $autoloader->addNamespace('JohnSmith\\Package\\Class', 'vendor/JohnSmith/Package/main/common/mainClass.php');
    $autoloader->register(); // For enable this autoloader
    
    // Now you can create object without require all the files
    $container = new JohnSmith\Package\Class();
    
    $autoloader->unregister(); // For disable this autoloader
