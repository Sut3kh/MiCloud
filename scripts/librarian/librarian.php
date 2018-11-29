<?php

require __DIR__ . '/vendor/autoload.php';

use MiCloud\Librarian\Command\MockCommand;
use MiCloud\Librarian\Command\MoveCommand;
use MiCloud\Librarian\Command\TestCommand;
use Symfony\Component\Console\Application;

$app = new Application();
$app->addCommands([
  new TestCommand(),
  new MockCommand(),
  new MoveCommand(),
]);
/** @noinspection PhpUnhandledExceptionInspection */
$app->run();
