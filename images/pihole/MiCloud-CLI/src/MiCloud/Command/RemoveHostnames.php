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
class RemoveHostnames extends Command {

  /**
   * {@inheritdoc}
   */
  protected function configure() {
    $this
      ->setName('host:remove')
      ->setDescription('Removes hostnames from a IP DNS entry.')
      ->setHelp("This command removes hostnames from dnsmasq."
        . "\nMultiple hostnames can be specified i.e."
        . "\nadd-hostnames 1.2.3.4 'test.com www.test.com'"
      )
      ->setAliases(['remove'])
      ->addArgument(
        'ip',
        InputArgument::REQUIRED,
        'The IP to remove the hostname from.'
      )
      ->addArgument(
        'hostnames',
        InputArgument::REQUIRED,
        'The hostname(s) to remove.'
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

    // Remove the hostnames.
    $hosts = Hosts::load();
    $hosts->removeHostnames($ip, $hostnames);
    $hosts->save();
    $output->writeln("Removed DNS entry $ip " . implode(' ', $hostnames));

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
