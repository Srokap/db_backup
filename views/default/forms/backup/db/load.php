<?php
$ov = array();
$files = db_backup::getBackupsFileIterator();
foreach ($files as $file) {
	if ($file instanceof DirectoryIterator) {
		$ov[$file->getFilename()] = $file->getFilename() . ' (' . number_format($file->getSize() / 1024, 2) . 'KB)';
	}
}
krsort($ov);

echo '<p>' . elgg_echo('db_backup:restore:backup_file');

echo elgg_view('input/dropdown', array(
	'name' => 'backup_file',
	'options_values' => $ov,
));

echo elgg_view('input/submit', array(
	'name' => 'submit',
	'value' => elgg_echo('db_backup:restore'),
));

echo '</p>';
