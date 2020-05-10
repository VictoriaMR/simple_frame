<?php

//config 目录中的数组
$configDir = ROOT_PATH . 'config/';
if (is_dir($configDir)) {
	foreach (scandir($configDir) as $value) {
		if ($value == '.' || $value == '..') continue;
		$GLOBALS[str_replace('.php', '', $value)] = require_once $configDir . $value;
	}
}