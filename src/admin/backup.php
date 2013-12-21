<?php
/**
 * The admin interface for managing and editing backups.
 * @package herisson
 */

function herisson_backup_actions() {

    $action = param('action');
    switch ($action) {
    case 'backup': herisson_backup_backup();
        break;
    default: herisson_backup_list();
    }

}

function herisson_backup_list() {

    $backups = Doctrine_Query::create()
        ->from('WpHerissonBackups')
        ->execute();

    require __DIR__."/views/backup-list.php";

}


