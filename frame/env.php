<?php
/**
 * 配置env参数到 getenv()
 */
if (is_file(ROOT_PATH . '.env')) {
    $env = parse_ini_file(ROOT_PATH . '.env', true);
    foreach ($env as $key => $value) {
        $name = strtoupper($key);
        if (is_array($value)) {
            foreach ($val as $k => $v) {
                putenv($name . '_' . strtoupper($k). '='. $v);
            }
        } else {
            putenv($name. '='. $value);
        }
    }
}