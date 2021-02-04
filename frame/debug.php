<?php

namespace frame;

class Debug
{
	public static function init()
	{
        // 获取基本信息
        $runtime = number_format(microtime(true) - APP_TIME_START, 10, '.', '');
        $reqs    = $runtime > 0 ? number_format(1 / $runtime, 2) : '∞';
        $mem     = number_format((memory_get_usage() - APP_MEMORY_START) / 1024, 2);
        $uri = implode(' ', [
            $_SERVER['SERVER_PROTOCOL'],
            $_SERVER['REQUEST_METHOD'],
            $_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'],
        ]);
        $info = get_included_files();
        $fileMem = 0;
        foreach ($info as $key => $file) {
            $temp = number_format(filesize($file) / 1024, 2);
            $fileMem += $temp;
            $info[$key] .= ' ( ' . $temp . ' KB )';
        }
        $base = [
            '请求信息' => date('Y-m-d H:i:s', $_SERVER['REQUEST_TIME']) . ' ' . $uri,
            '运行时间' => number_format((float) $runtime, 6) . 's [ 吞吐率：' . $reqs . ' req/s ]',
            '查询信息' => empty($GLOBALS['exec_sql']) ? 0 : count($GLOBALS['exec_sql']) . ' 条',
            '内存消耗' => $mem . ' KB',
            '文件加载' => count($info) . ' 个',
            '文件总值' => $fileMem . ' KB',
        ];
        $config = [
            'file' => '',
            'tabs' => ['base' => '基本', 'file' => '文件', 'sql' => 'SQL'],
        ];
        $trace = [];
        foreach ($config['tabs'] as $name => $title) {
            $name = strtolower($name);
            switch ($name) {
                case 'base': // 基本信息
                    $trace[$title] = $base;
                    break;
                case 'file': // 文件信息
                    $trace[$title] = $info;
                    break;
                case 'sql': // 文件信息
                    $trace[$title] = $GLOBALS['exec_sql'] ?? '';
                    break;
                default: // 调试信息
                    break;
            }
        }
        assign('trace', $trace);
        assign('runtime', $runtime);
        return view('frame/pagetrace', false);
	}

    public static function runlog($msg = '')
    {
        $now = date('Y-m-d H:i:s');
        $destination = ROOT_PATH.'runtime'.DS.date('Ym').DS.date('d').'.log';

        if (!is_file($destination)) {
            $path = dirname($destination);
            if (!is_dir($path)) {
                mkdir($path, 0770, true);
            }
        }
        // 获取基本信息
        if (isset($_SERVER['HTTP_HOST'])) {
            $current_uri = ' uri:' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
        } else {
            $current_uri = ' cmd:' . implode(' ', $_SERVER['argv']);
        }
        $runtime    = number_format(microtime(true) - APP_TIME_START, 10,'.','');
        $reqs       = $runtime > 0 ? number_format(1 / $runtime, 2,'.','') : '∞';
        $time_str   = '[Time：' . number_format($runtime, 6) . 's] [QPS：' . $reqs . 'req/s]';
        $memory_use = number_format((memory_get_usage() - APP_MEMORY_START) / 1024, 2,'.','');
        $memory_str = ' [MEM：' . $memory_use . 'kb]';
        $file_load  = ' [Files：' . count(get_included_files()) . ']';
        $info   = $time_str . $memory_str . $file_load . PHP_EOL;
        $server = isset($_SERVER['SERVER_ADDR']) ? $_SERVER['SERVER_ADDR'] : '0.0.0.0';
        $remote = isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : '0.0.0.0';
        $message = '';
        if ($msg == '') {
            $message = error_get_last()['message'] ?? '';
        } else {
            $message = preg_replace('/\s(?=\s)/', '\\1', $msg).PHP_EOL;
        }
        return error_log('[runtime] '.$now.' [server addr] '.$server.' [remote addr] '.$remote.$current_uri.PHP_EOL.$info.$message.'---------------------------------------------------------------'.PHP_EOL, 3, $destination);
    }
}