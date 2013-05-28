<?php
$english = array(
	'admin:backup' => 'Backup',
	'admin:backup:database' => 'Database backup',
	'db_backup:cli' => 'CLI tools',
	'db_backup:cli:dependencies:ok' => 'All dependencies are met.',
	'db_backup:cli:dependencies:fail' => 'There are missing dependencies. This plugin may not work correctly!',
	'db_backup:backup' => 'Backup',
	'db_backup:dir:validate:fail' => 'Unable to create data directory, or it\'s not writable. Path: %s',
	'db_backup:restore' => 'Restore',
	'db_backup:restore:backup_file' => 'Choose backup file to restore from: ',
	'db_backup:button:quick' => 'Backup DB',
	'db_backup:action:backup:ok' => 'Successfully backed up database to file: %s',
	'db_backup:action:backup:fail' => 'There was error while making backup. Error %d: %s',
	'db_backup:action:restore:ok' => 'Successfully restored database from file: %s',
	'db_backup:action:restore:fail' => 'There was error while restoring database. Error %d: %s',
	'db_backup:backup_file:group:scheduled' => 'Backups created locally',
	'db_backup:backup_file:group:uploaded' => 'Uploaded backup files',
	'db_backup:backup_file:no_files' => 'No backup files found',
);
add_translation('en', $english);