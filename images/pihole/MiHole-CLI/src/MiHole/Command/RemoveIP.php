<?php

namespace MiHole\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ConfirmationQuestion;
use MiHole\Hosts;

/**
 * Console Command: add-hostnames.
 */
class RemoveIP extends Command {

  /**
   * {@inheritdoc}
   */
  protected function configure() {
    $this
      ->setName('ip:remove')
      ->setDescription('Removes all hostname DNS entries for an IP.')
      ->addArgument(
        'ip',
        InputArgument::REQUIRED,
        'The IP to remove.'
      )
    ;
  }

  /**
   * {@inheritdoc)
   */
  protected function execute(InputInterface $input, OutputInterface $output)
  {
    // Process Args.
    $ip = $input->getArgument('ip');

    // Load the hosts.
    $hosts = Hosts::load();

    // Get confirmation.
    $q_helper = $this->getHelper('question');
    $question = new ConfirmationQuestion(
      'Are you sure you want to remove "' . $hosts->getHostEntry($ip) . '"? [y/n]: '
    );

    if (!$q_helper->ask($input, $output, $question)) {
      return;
    }

    // Remove the IP.
    $hosts->removeIP($ip);
    $hosts->save();
    $output->writeln("Removed all DNS entries for $ip");

    // Reload dnsmasq.
    exec('pihole restartdns', $null, $retval);
    if ($retval != 0) {
      $output->writeln('<error>Failed to reload DNS</error>');
    }
    else {
      $output->writeln('Reloaded DNS');
    }
  }

}
