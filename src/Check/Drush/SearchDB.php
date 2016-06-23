<?php

namespace SiteAudit\Check\Drush;

use SiteAudit\AuditResponse\AuditResponse;
use SiteAudit\Check\Check;
use SiteAudit\Executor\DoesNotApplyException;

class SearchDB extends Check {
  static public function getNamespace()
  {
    return 'variable/search_db';
  }

  public function check() {
    if (!$this->context->drush->moduleEnabled('search')) {
      throw new DoesNotApplyException();
    }

    // Check if the database is used used to search.
    if ($this->context->drush->moduleEnabled('search_api_db')) {

      // Find out if there are active indexes using the db service class.
      $output = $this->context->drush->sqlQuery("SELECT COUNT(i.machine_name) as count FROM search_api_index i LEFT JOIN search_api_server s ON i.server = s.machine_name WHERE i.status > 0 AND s.class = 'search_api_db_service';");
      if (empty($output)) {
        $number_of_db_indexes = 0;
      }
      else if (count($output) == 1) {
        $number_of_db_indexes = (int) $output[0];
      }
      else {
        $number_of_db_indexes = (int) $output[1];
      }
      $this->setToken('number_of_db_indexes', $number_of_db_indexes);
      $number_of_db_indexes > 1 ? $this->setToken('plural_index', 'es') : $this->setToken('plural_index', '');

      // No database indexes.
      if ($number_of_db_indexes === 0) {
        return AuditResponse::AUDIT_SUCCESS;
      }

      // If the database is in use, find out how many nodes are in it.
      $output = $this->context->drush->sqlQuery('SELECT COUNT(item_id) FROM search_api_db_default_node_index;');
      // There are some differences in running the command on site factory then
      // locally.
      if (count($output) == 1) {
        $nodes_in_search = (int) $output[0];
      }
      else {
        $nodes_in_search = (int) $output[1];
      }
      $this->setToken('nodes_in_search', $nodes_in_search);

      $max_size = (int) $this->getOption('max_size', 50);
      if ($nodes_in_search < $max_size) {
        return AuditResponse::AUDIT_WARNING;
      }
      return AuditResponse::AUDIT_FAILURE;
    }

    return TRUE;

  }
}

