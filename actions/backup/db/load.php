<?php
$fileName = get_input('backup_file');

try {
	if (db_backup::doRestore($fileName)) {
		system_message(elgg_echo('db_backup:action:restore:ok', array($fileName)));
	} else {
		register_error(elgg_echo('db_backup:action:restore:fail', array(
			db_backup::getErrorCode(),
			db_backup::getErrorMessage()
		)));
	}
} catch (RuntimeException $e) {
	register_error($e->getMessage());
}
