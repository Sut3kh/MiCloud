<?php

namespace MiCloud\Librarian\Command;

use MiCloud\Librarian\PatternMatcher;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class TestCommand extends Command {

  protected function configure() {
    $this
      ->setName('test')
      ->setDescription('Test a filepath against all patterns.')
      ->addArgument('filepath', InputArgument::REQUIRED)
    ;
  }

  protected function execute(InputInterface $input, OutputInterface $output) {
    // Test all patterns.
    $subject = basename($input->getArgument('filepath'));
    $output->writeln("<info>Testing '${subject}'</info>");
    $results = PatternMatcher::testAll($subject);

    // Output as a table.
    PatternMatcher::toTable($results, $output)->render();
  }

}
