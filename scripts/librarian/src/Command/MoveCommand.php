<?php

namespace MiCloud\Librarian\Command;

use MiCloud\Librarian\Episode;
use MiCloud\Librarian\PatternMatcher;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ChoiceQuestion;
use Symfony\Component\Console\Question\ConfirmationQuestion;
use Symfony\Component\Finder\Finder;

class MoveCommand extends Command {

  public const DESTINATION = '/media/plex/data/TV';

  protected function configure() {
    $this
      ->setName('move')
      ->setDescription('Attempt to move a video file or directory of files to the right place.')
      ->addArgument('source', InputArgument::REQUIRED)
      ->addArgument('destination', InputArgument::OPTIONAL, '', static::DESTINATION)
      ->addOption('show', NULL, InputOption::VALUE_REQUIRED)
      ->addOption('season', NULL, InputOption::VALUE_REQUIRED)
      ->addOption('episode', NULL, InputOption::VALUE_REQUIRED)
    ;
  }

  protected function execute(InputInterface $input, OutputInterface $output) {
    $missing_options = array_diff(
      ['show', 'season', 'episode'],
      array_keys(array_filter($input->getOptions()))
    );
    if ($missing_options) {
      $output->writeln('<error>Missing required option(s): ' . implode(', ', $missing_options) . '</error>');
      return 1;
    }

    /** @var \Symfony\Component\Console\Helper\QuestionHelper $q_helper */
    $q_helper = $this->getHelper('question');
    $destination_dir = $input->getArgument('destination');
    if ($destination_dir[-1] === '/') {
      $destination_dir = mb_substr($destination_dir, 0, -1);
    }
    $show = $input->getOption('show');
    $season = (int) $input->getOption('season');
    $episode_num = (int) $input->getOption('episode');

    // Scan the directory and build a list of matching episodes.
    /** @var \MiCloud\Librarian\Episode[] $episodes */
    $episodes = [];
    $pattern = NULL;
    $finder = new Finder();
    $finder
      ->in($input->getArgument('source'))
      ->files()
    ;
    foreach ($finder as $file) {
      $file_name = $file->getFilename();

      // If we have not found the pattern yet, try all.
      if (!$pattern) {
        $results = PatternMatcher::testAll($file_name);
        if (!$results) {
          $output->writeln(
            "${file_name} does not match any known patterns.",
            OutputInterface::VERBOSITY_VERBOSE
          );
          continue;
        }
        $output->writeln(
          "${file_name}: " . json_encode($results),
          OutputInterface::VERBOSITY_VERBOSE
        );

        // Filter matches.
        $matches = [];
        foreach ($results as $i => $result) {
          if ($result) {
            if ($result->Show === $show && $result->Season === $season) {
              $matches[$i] = $result;
            }
          }
        }
        if (!$matches) {
          $output->writeln(
            "Ignoring ${file_name}, it has no matches",
            OutputInterface::VERBOSITY_VERBOSE
          );
          continue;
        }

        // Ask if we find multiple matches.
        if (count($matches) > 1) {
          $output->writeln("${file_name} matches multiple patterns:");
          PatternMatcher::toTable($results, $output, TRUE)->render();
          $options = array_keys($matches);
          $question = new ChoiceQuestion('Which match is correct?', $options);
          $answer = $q_helper->ask($input, $output, $question);
          if (!$answer) {
            return 0;
          }
          $pattern = PatternMatcher::PATTERNS[$answer];
          $episode = $matches[$answer];
          $output->writeln(
            "Using pattern '${pattern}'",
            OutputInterface::VERBOSITY_VERBOSE
          );
        }
        else {
          $pattern = PatternMatcher::PATTERNS[key($matches)];
          $episode = reset($matches);
        }
      }
      else {
        $episode = PatternMatcher::test($file_name, $pattern);
        if (!($episode->Show === $show && $episode->Season === $season)) {
          $output->writeln(
            "Skipping ${file_name}",
            OutputInterface::VERBOSITY_VERBOSE
          );
          continue;
        }
      }

      // Set the extension.
      $episode->Ext = $file->getExtension();

      // Add the episode.
      $source = $file->getRealPath();
      if (isset($episodes[$source])) {
        throw new \Exception('Duplicate file');
      }
      $episodes[$source] = $episode;
    }

    // Check we have something to do.
    if (!$episodes) {
      $output->writeln('Cannot find anything to move, try -v for more info.');
      return 0;
    }

    // Sort by season, disc, title.
    uasort($episodes, function (Episode $a, Episode $b) {
      foreach (['Season', 'SeasonPart', 'Disc', 'Title'] as $prop) {
        $cmp = ($a->{$prop} <=> $b->{$prop});
        if ($cmp !== 0) {
          return $cmp;
        }
      }
      return 0;
    });

    // Build the operations.
    $operations = [];
    foreach ($episodes as $source => $episode) {
      // Build the new filename.
      if (isset($episode->Episode)) {
        $episode_num = $episode->Episode;
      }
      $new_name = strtr('{Show}.S{Season}E{Episode}.{Ext}', [
        '{Show}' => str_replace(' ', '.', $episode->Show),
        '{Season}' => str_pad($episode->Season, 2, '0', STR_PAD_LEFT),
        '{Episode}' => str_pad($episode_num++, 2, '0', STR_PAD_LEFT),
        '{Ext}' => $episode->Ext,
      ]);

      // Add the operation.
      $destination = implode('/', [
        $destination_dir,
        $show,
        "Season {$episode->Season}",
        $new_name
      ]);
      if (isset($operations[$destination])) {
        $output->writeln("<error>'${destination}' is already planned to be added but it appeared again from ${source}</error>");
        return 1;
      }
      if (file_exists($destination)) {
        $output->writeln("<error>'${destination}' already exists but would be the target of '${source}'</error>");
        return 1;
      }
      $operations[$destination] = $source;
    }

    // Output the operations.
    $output->writeln('<info>Here is what I plan to do:</info>');
    $table = new Table($output);
    $table->setHeaders([
      'Destination',
      'Source',
    ]);
    $pwd = realpath(getcwd());
    foreach ($operations as $destination => $source) {
      // Truncate if relative.
      if (mb_strrpos($destination, $pwd) === 0) {
        $destination = './' . substr($destination, mb_strlen($pwd) + 1);
      }
      if (mb_strrpos($source, $pwd) === 0) {
        $source = './' . substr($source, mb_strlen($pwd) + 1);
      }
      $table->addRow([$destination, $source]);
    }
    $table->render();

    // Get confirmation.
    $question = new ConfirmationQuestion('Shall I do it? ');
    if (!$q_helper->ask($input, $output, $question)) {
      return 0;
    }

    // Do the moves.
    foreach ($operations as $destination => $source) {
      $dir = dirname($destination);
      if (!file_exists($dir)) {
        mkdir($dir, 0777, TRUE);
      }
      if (!rename($source, $destination)) {
        $output->writeln("<error>Failed to move '${source}' -> '${destination}'");
        return 1;
      }
    }

    return 0;
  }

}
