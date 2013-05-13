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

echo elgg_view_module('aside', elgg_echo('db_backup:restore'), 
	elgg_view_form('backup/db/load'));

$body = '<p><br />' . elgg_view('output/url', array(
	'href' => elgg_add_action_tokens_to_url(elgg_normalize_url('action/backup/db/save')),
	'text' => elgg_echo('db_backup:button:quick'),
	'class' => 'elgg-button elgg-button-submit',
)) . '</p>';
echo elgg_view_module('aside', elgg_echo('db_backup:backup'), $body);
