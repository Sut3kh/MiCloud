<?php

namespace MiCloud\Librarian;

use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Output\OutputInterface;

class PatternMatcher {

  public const PATTERNS = [
    // Battlestar_Galactica_Season_4_0_\(Disc_1\)_t00.mkv
    '/^(?P<Show>.+)_Season_(?P<Season>\d)_\d_\(Disc_(?P<Disc>\d)\)_t(?P<Title>\d{1,2})\.[^.]+$/i',
  ];

  /**
   * Test all patterns against the given string.
   *
   * @return \MiCloud\Librarian\Episode[]
   */
  public static function testAll(string $subject): array {
    $matches = [];
    foreach (static::PATTERNS as $i => $pattern) {
      $matches[$i] = static::test($subject, $pattern);
    }
    return $matches;
  }

  /**
   * Test a pattern against the given string.
   */
  public static function test(string $subject, string $pattern): ?Episode {
    if (!preg_match($pattern, $subject, $matches)) {
      return NULL;
    }
    $episode = new Episode();
    $episode->Show = str_replace(['.', '_'], ' ', $matches['Show']);
    $episode->Season = (int) $matches['Season'];
    $episode->Disc = (int) $matches['Disc'];
    $episode->Title = (int) $matches['Title'];
    return $episode;
  }

  /**
   * Output an array of pattern results as a table.
   *
   * @param \MiCloud\Librarian\Episode|NULL[] $results
   * @param \Symfony\Component\Console\Output\OutputInterface $output
   * @param bool $filter_empty
   *
   * @return \Symfony\Component\Console\Helper\Table
   */
  public static function toTable(
    array $results,
    OutputInterface $output,
    bool $filter_empty = FALSE
  ): Table {
    $table = new Table($output);
    $table->setHeaders(['#', 'Pattern', 'Episode']);
    foreach ($results as $i => $result) {
      if ($filter_empty && !$result) {
        continue;
      }
      $table->addRow([
        $i,
        PatternMatcher::PATTERNS[$i],
        $result ? json_encode($result, JSON_PRETTY_PRINT) : '-',
      ]);
    }
    return $table;
  }

}
