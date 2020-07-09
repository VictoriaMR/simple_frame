<?php

//config 目录中的数组
if (is_dir(ROOT_PATH . 'config/')) {
	foreach (scandir(ROOT_PATH . 'config/') as $value) {
		if ($value == '.' || $value == '..') continue;
		$GLOBALS[str_replace('.php', '', $value)] = require_once ROOT_PATH . 'config/' . $value;
	}
	unset($value);
}