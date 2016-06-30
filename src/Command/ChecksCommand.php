<?php

namespace SiteAudit\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Helper\TableSeparator;
use SiteAudit\Check\Registry;


class ChecksCommand extends Command {

  /**
   * @inheritdoc
   */
  protected function configure() {
    $this
      ->setName('checks:list')
      ->setDescription('Show all checks available.')
      ;
  }

  /**
   * @inheritdoc
   */
  protected function execute(InputInterface $input, OutputInterface $output)
  {
    $map = Registry::load();

    $rows = array();
    foreach ($map as $class => $info) {
      $rows[] = array(
        'title' => $info->title,
        'class' => $class,
        'description' => wordwrap($info->description),
      );
      $rows[] = new TableSeparator();
    }
    array_pop($rows);
    $table = new Table($output);
    $table
        ->setHeaders(array('Title', 'Class', 'Description'))
        ->setRows($rows);

    $table->render();
  }

}
