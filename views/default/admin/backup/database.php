<?php

if (db_backup::validateDataDir()) {

	$dependencies = db_backup::checkDependencies();
	
	$body = '';
	
	$body .= '<ul>';
	if ($dependencies) {
		$body .= '<li class="elgg-message elgg-state-success">' . elgg_echo('db_backup:cli:dependencies:ok') . '</li>';
	} else {
		$body .= '<li class="elgg-message elgg-state-error">' . elgg_echo('db_backup:cli:dependencies:fail') . '</li>';
	}
	$body .= '</ul>';
	
	$body .= '<pre>';
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

} else {
	$body = '<ul>';
	$body .= '<li class="elgg-message elgg-state-error">' 
		. elgg_echo('db_backup:dir:validate:fail', array(db_backup::getDataDir())) . '</li>';
	$body .= '</ul>';
	echo $body;
}
