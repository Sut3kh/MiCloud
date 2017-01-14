<?php

namespace MiCloud\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use MiCloud\Hosts;

/**
 * Console Command: add-hostnames.
 */
class AddHostnames extends Command {

  /**
   * {@inheritdoc}
   */
  protected function configure() {
    $this
      ->setName('host:add')
      ->setDescription('Adds a DNS entry to map hostnames to an IP.')
      ->setHelp("This command adds hostnames to dnsmasq."
        . "\nMultiple hostnames can be specified i.e."
        . "\nadd-hostnames 1.2.3.4 'test.com www.test.com'"
      )
      ->setAliases(['add'])
      ->addArgument(
        'ip',
        InputArgument::REQUIRED,
        'The IP to map the hostnames to.'
      )
      ->addArgument(
        'hostnames',
        InputArgument::REQUIRED,
        'The hostname(s) to map to the IP.'
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
    $hostnames = explode(' ', $input->getArgument('hostnames'));

    // Add the hostnames.
    $hosts = Hosts::load();
    $hosts->addHostnames($ip, $hostnames);
    $hosts->save();
    $output->writeln("Added DNS entry $ip " . implode(' ', $hostnames));

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
