<?php
try {
	if (db_backup::doBackup()) {
		system_message(elgg_echo('db_backup:action:backup:ok', array(db_backup::getLastBackupFileName())));
	} else {
		register_error(elgg_echo('db_backup:action:backup:fail', array(
			db_backup::getErrorCode(),
			db_backup::getErrorMessage()
		)));
	}
} catch (RuntimeException $e) {
	register_error($e->getMessage());
}
