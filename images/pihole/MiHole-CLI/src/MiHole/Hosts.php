<?php

namespace MiHole;

/**
 * Wrapper class to read and write the custom hosts file.
 */
class Hosts {

  /**
   * @var string HOSTS_PATH
   *  The filepath to the custom hosts file loaded by dnsmasq.
   */
  const HOSTS_PATH = '/etc/pihole/mihole_hosts';

  /**
   * @var array $hosts
   *  Map of IP addresses and the hostnames to give them.
   *
   *  i.e. $hosts['1.2.3.4'] = ['test.com', 'www.test.com']
   */
  protected $hosts = [];

  /**
   * Add hostname entries to the object.
   *
   * @param string $ip
   * @param string|array $hostnames
   *  Single hostname or an array of multiple.
   */
  public function addHostnames($ip, $hostnames) {
    if (is_string($hostnames)) {
      $hostnames = [$hostnames];
    }

    if (isset($this->hosts[$ip])) {
      $this->hosts[$ip] = array_unique(array_merge($this->hosts[$ip], $hostnames));
    }
    else {
      $this->hosts[$ip] = $hostnames;
    }
  }

  /**
   * Remove hostname entries from the object.
   *
   * @param string $ip
   * @param string|array $hostnames
   *  Single hostname or an array of multiple.
   */
  public function removeHostnames($ip, $hostnames) {
    if (is_string($hostnames)) {
      $hostnames = [$hostnames];
    }

    // Check the IP is listed.
    if (!isset($this->hosts[$ip])) {
      throw new \Exception("$ip is not in the hosts file");
    }

    // Remove the hostnames.
    $new_hostnames = array_diff($this->hosts[$ip], $hostnames);

    // Remove the IP if it was the only hostname.
    if (count($new_hostnames) === 0) {
      $this->removeIP($ip);
    }
    else {
      $this->hosts[$ip] = $new_hostnames;
    }
  }

  /**
   * Remove all hostname entries for an IP from the object.
   *
   * @param string $ip
   */
  public function removeIP($ip) {
    // Check the IP is listed.
    if (!isset($this->hosts[$ip])) {
      throw new \Exception("$ip is not in the hosts file");
    }

    // Remove the IP.
    unset($this->hosts[$ip]);
  }

  /**
   * Get the formatted host entry for an IP.
   *
   * i.e. '1.2.3.4 test.com www.test.com'.
   *
   * @param string $ip
   *
   * @return string
   *
   * @throws OutOfRangeException
   */
  public function getHostEntry($ip) {
    if (!isset($this->hosts[$ip])) {
      throw new \OutOfRangeException("IP not found: $ip");
    }
    return sprintf('%s %s', $ip, implode(' ', $this->hosts[$ip]));
  }

  /**
   * Deserialise a hosts file into this object.
   *
   * @param string $filepath
   *  Filepath to the hosts file.
   *
   * @return bool
   *  FALSE if the file did not exist.
   */
  public function deserialise($filepath) {
    // Check file exists
    if (!file_exists($filepath)) {
      return FALSE;
    }

    // Open hosts file for reading.
    $fp = fopen($filepath, 'r');
    if (!$fp) {
      throw new \Exception(sprintf('Failed to open %s for reading', $filepath));
    }

    // Parse the hosts file into $this->hosts.
    try {
      $line_num = 1;
      while (!feof($fp)) {
        $line = trim(fgets($fp));
        if (strlen($line) == 0) {
          continue;
        }

        // Split the line and check it is complete
        $parts = explode(' ', $line);
        if (count($parts) < 2) {
          fwrite(STDERR, "Warning: ignoring bad hosts line $line_num: $line\n");
          continue;
        }

        // The first part should be the IP, the rest should be the hostnames.
        $ip = array_shift($parts);
        $this->addHostnames($ip, $parts);

        // Increment line counter.
        $line_num++;
      }
    }
    finally {
      fclose($fp);
    }

    return TRUE;
  }

  /**
   * Serialise this object into a file.
   *
   * @param string $filepath
   *  Filepath to the hosts file.
   */
  public function serialise($filepath) {
    // Open hosts file for writing.
    $fp = fopen($filepath, 'w');
    if (!$fp) {
      throw new \Exception(sprintf('Failed to open %s for writing', $filepath));
    }

    // Write the hosts file.
    try {
      foreach ($this->hosts as $ip => $hostnames) {
        fwrite($fp, $this->getHostEntry($ip) . "\n");
      }
    }
    finally {
      fclose($fp);
    }

    return TRUE;
  }

  /**
   * Serialise this object into the custom hosts file (HOSTS_PATH).
   */
  public function save() {
    $this->serialise(static::HOSTS_PATH);
  }

  /**
   * Load the custom hosts file from HOSTS_PATH.
   *
   * @return MiHole\Hosts
   */
  public static function load() {
    $hosts = new static();
    $hosts->deserialise(static::HOSTS_PATH);
    return $hosts;
  }
}
