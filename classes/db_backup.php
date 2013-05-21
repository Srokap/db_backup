<?php
class db_backup {
	
	/**
	 * Fired on system init
	 */
	static function init() {
		elgg_register_event_handler('pagesetup', 'system', array(__CLASS__, 'pagesetup'));
		
		elgg_register_plugin_hook_handler('register', "menu:admin_control_panel", array(__CLASS__, 'handlerAdminButtonsMenuRegister'));
		
		elgg_register_action('backup/db/save', 
			elgg_get_config('pluginspath') . __CLASS__ . '/actions/backup/db/save.php', 'admin');
		elgg_register_action('backup/db/load', 
			elgg_get_config('pluginspath') . __CLASS__ . '/actions/backup/db/load.php', 'admin');
	}
	
	/**
	 * Fired on system pagesetup
	 */
	static function pagesetup() {
		$parent_id = 'administer_utilities';
		$section = 'administer';
		
		// make sure parent is registered
		if ($parent_id && !elgg_is_menu_item_registered('page', $parent_id)) {
			elgg_register_admin_menu_item($section, $parent_id);
		}
		
		elgg_register_menu_item('page', array(
			'name' => 'backup/database',
			'href' => 'admin/backup/database',
			'text' => elgg_echo('admin:backup:database'),
			'context' => 'admin',
			'parent_name' => $parent_id,
			'section' => $section,
		));
	}
	
	/**
	 * Fired on admin_control_panel menu registration
	 * 
	 * @param string $hook
	 * @param string $type
	 * @param array $menu
	 * @param mixed $params
	 * @return array
	 */
	static function handlerAdminButtonsMenuRegister($hook, $type, $menu, $params) {
		elgg_trigger_plugin_hook('register', "menu:$menu_name", $vars, $menu);
		
		$menu[] = ElggMenuItem::factory(array(
			'name' => 'backup/db/save',
			'href' => elgg_add_action_tokens_to_url('action/backup/db/save'),
			'text' => elgg_echo('db_backup:button:quick'),
			'class' => 'elgg-button elgg-button-action',
		));
		return $menu;
	}
	
	/**
	 * @var string
	 */
	protected static $fileName;
	
	/**
	 * @var int
	 */
	protected static $errorCode;
	
	/**
	 * @var string
	 */
	protected static $errorMessage;
	
	/**
	 * @return string
	 */
	public static function getLastBackupFileName() {
		return self::$fileName;
	}
	
	/**
	 * @throws RuntimeException
	 * @return bool
	 */
	static function doBackup() {
		if (!self::checkDependencies()) {
			throw new RuntimeException("Missing CLI tools access");
		}
		
		$time = time();
		
		$prefix = elgg_get_config('dbname') ? elgg_get_config('dbname') : 'db_backup';
		self::$fileName = "$prefix-" . gmdate("Y-m-d-H-i-s", $time) . '.sql';
		$filePath = self::getDataDir() . self::$fileName;
		
		$cmd = "mysqldump -u " . escapeshellcmd(elgg_get_config('dbuser')) 
			. (elgg_get_config('dbpass') ? " -p" . escapeshellcmd(elgg_get_config('dbpass')) : '') 
			. " -h " . escapeshellcmd(elgg_get_config('dbhost')) 
			. " " . escapeshellcmd(elgg_get_config('dbname')) 
			. " > " . escapeshellcmd($filePath);

		self::$errorMessage = self::execSystemCommand($cmd, self::$errorCode);

		if (self::$errorCode != 0) {
			unlink($filePath);
		}

		return self::$errorCode == 0;
	}
	
	/**
	 * @throws RuntimeException
	 * @return bool
	 */
	static function doRestore($fileName) {
		if (!self::checkDependencies()) {
			throw new RuntimeException("Missing CLI tools access");
		}

		$fileName = str_replace(array('../', '/'), array('', ''), $fileName);
		$filePath = self::getDataDir() . $fileName;

		if (!file_exists($filePath)) {
			self::$errorCode = -1;
			self::$errorMessage = "Source file does not exist!";
			return false;
		}

		$cmd = "mysql -u " . escapeshellcmd(elgg_get_config('dbuser')) 
			. (elgg_get_config('dbpass') ? " -p" . escapeshellcmd(elgg_get_config('dbpass')) : '') 
			. " -h " . escapeshellcmd(elgg_get_config('dbhost')) 
			. " " . escapeshellcmd(elgg_get_config('dbname')) 
			. " < " . escapeshellcmd($filePath);
		
		self::$errorMessage = self::execSystemCommand($cmd, self::$errorCode);
		
		return self::$errorCode == 0;
	}
	
	/**
	 * Executes system command when possible
	 * 
	 * @param string $cmd
	 * @param int $return_code
	 */
	static function execSystemCommand($cmd, &$return_code) {
	
		if (!function_exists('proc_open') || !function_exists('proc_close')) {
			throw new RuntimeException("Process execution functions are missing!");
		}
		
		$proc = proc_open($cmd, array(
			0 => array('pipe', 'r'),// stdin
			1 => array('pipe', 'w'),// stdout
			2 => array('pipe', 'w'),// stderr
		), $pipes);
	
		if ($proc===false) {
			throw new IOException("Error while calling proc_open");
		}
	
		$output = stream_get_contents($pipes[1]).' '.stream_get_contents($pipes[2]);
	
		$return_code = proc_close($proc);
	
		return $output;
	}
	
	/**
	 * @return bool
	 */
	static function checkDependencies() {
		db_backup::execSystemCommand('mysqldump -V', $code1);
		db_backup::execSystemCommand('mysql -V', $code2);
		return $code1 == 0 && $code2 == 0;
	}
	
	/**
	 * @return string
	 */
	static function getDataDir() {
		return elgg_get_config('dataroot') . 'db_backup/';
	}
	
	/**
	 * @return int
	 */
	static function getErrorCode() {
		return self::$errorCode;
	}
	
	/**
	 * @return string
	 */
	static function getErrorMessage() {
		return self::$errorMessage;
	}
	
	/**
	 * @return bool
	 */
	static function validateDataDir() {
		$path = self::getDataDir();
		if (!is_dir($path) && !mkdir($path, 0777, true)) {
			return false;
		}
		return is_writable($path);
	}
	
	/**
	 * Returns iterator over existing backup files
	 * 
	 * @return RegexIterator
	 */
	static function getBackupsFileIterator() {
		$i = new DirectoryIterator(self::getDataDir());
		$i = new RegexIterator($i, "/.*\.sql/");
		return $i;
	}
	
}