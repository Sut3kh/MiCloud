#!/usr/bin/env php
<?php
/**
 * Main entrypoint for the MiHole CLI.
 */

// Use composer's autoloader.
require __DIR__.'/vendor/autoload.php';

use Symfony\Component\Console\Application;
use MiCloud\Command\AddHostnames;
use MiCloud\Command\RemoveHostnames;
use MiCloud\Command\RemoveIP;

// Init the Symfony Console Application.
$app = new Application('MiHole CLI', '1.0.0');

// Register commands.
$app->add(new AddHostnames());
$app->add(new RemoveHostnames());
$app->add(new RemoveIP());

// Run the App.
$app->run();
