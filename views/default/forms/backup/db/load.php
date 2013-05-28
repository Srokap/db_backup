<?php

$ov1 = array();
$files = db_backup::getBackupsFileIterator();
foreach ($files as $file) {
	if ($file instanceof DirectoryIterator) {
		$ov1[$file->getFilename()] = $file->getFilename() . ' (' . number_format($file->getSize() / 1024, 2) . 'KB)';
	}
}
krsort($ov1);

$ov2 = array();
$files = db_backup::getUploadedBackupsFileIterator();
foreach ($files as $file) {
	if ($file instanceof DirectoryIterator) {
		$ov2[$file->getFilename()] = $file->getFilename() . ' (' . number_format($file->getSize() / 1024, 2) . 'KB)';
	}
}
krsort($ov2);

echo '<p>' . elgg_echo('db_backup:restore:backup_file');

if ($ov1 || $ov2) {
	echo elgg_view('input/select_optgroups', array(
		'name' => 'backup_file',
		'optgroups' => array (
			elgg_echo('db_backup:backup_file:group:scheduled') => $ov1,
			elgg_echo('db_backup:backup_file:group:uploaded') => $ov2,
		),
	));
	
	echo elgg_view('input/submit', array(
		'name' => 'submit',
		'value' => elgg_echo('db_backup:restore'),
	));
} else {
	echo '<strong>' . elgg_echo('db_backup:backup_file:no_files') . '</strong> ';
}

echo '</p>';
