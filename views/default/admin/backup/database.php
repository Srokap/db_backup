<?php

// var_dump(db_backup::checkDependencies());

// var_dump(db_backup::validateDataDir());

$body = '<pre>';
$body .= trim(db_backup::execSystemCommand('mysqldump -V', $code));
$body .= '</pre>';
$body .= '<pre>';
$body .= trim(db_backup::execSystemCommand('mysql -V', $code));
$body .= '</pre>';

echo elgg_view_module('aside', elgg_echo('db_backup:cli'), $body);

$ov = array();
$files = db_backup::getBackupsFileIterator();
foreach ($files as $file) {
	if ($file instanceof DirectoryIterator) {
		$ov[$file->getPathname()] = $file->getFilename() . ' (' . number_format($file->getSize() / 1024, 2) . 'KB)';
// 		var_dump($file);
	}
}
ksort($ov);
var_dump($ov);
echo elgg_view('input/dropdown', array(
	'name' => 'backup_file',
	'options_values' => $ov,
));