<?php

namespace MiCloud\Librarian\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class MockCommand extends Command {

  protected function configure() {
    $this
      ->setName('mock')
      ->setDescription('Create a mock directory structure of source video files.')
      ->addArgument('destination', InputArgument::REQUIRED)
      ->addOption('show', NULL, InputOption::VALUE_REQUIRED)
      ->addOption('season', NULL, InputOption::VALUE_REQUIRED)
      ->addOption('disc', NULL, InputOption::VALUE_REQUIRED)
      ->addOption('titles', NULL, InputOption::VALUE_REQUIRED)
    ;
  }

  protected function execute(InputInterface $input, OutputInterface $output) {
    $missing_options = array_diff(
      [
        'show',
        'season',
        'disc',
        'titles',
      ],
      array_keys(array_filter($input->getOptions()))
    );
    if ($missing_options) {
      $output->writeln('<error>Missing required option(s): ' . implode(', ', $missing_options) . '</error>');
      return 1;
    }
    $dest_dir = $input->getArgument('destination');
    $show = str_replace(' ', '_', $input->getOption('show'));
    $season = (int) $input->getOption('season');
    $disc = (int) $input->getOption('disc');
    $dest_dir = "${dest_dir}/${show}_Season_${season}_0_(Disc_{$disc})";
    if (!mkdir($dest_dir)) {
      $output->writeln("<error>Failed to create directory '${dest_dir}'");
      return 1;
    }
    for ($i = 0; $i < $input->getOption('titles'); $i++) {
      $t = str_pad($i, 2, '0', STR_PAD_LEFT);
      $file_path = "${dest_dir}/${show}_Season_${season}_0_(Disc_${disc})_t${t}.mkv";
      if (file_exists($file_path)) {
        $output->writeln("<error>File already exists: '${file_path}'");
        return 1;
      }
      if (file_put_contents($file_path, '') === FALSE) {
        $output->writeln("<error>Failed to write: '${file_path}'");
        return 1;
      }
      $output->writeln($file_path);
    }
    return 0;
  }

}
