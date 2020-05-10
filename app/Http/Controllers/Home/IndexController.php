<?php

namespace App\Http\Controllers\Home;

/**
 * 默认入口控制器
 */

class IndexController
{
	public function index()
	{
		\App::make('App\Http\Controllers\Home\IndexController');
		\App::make('App\Http\Controllers\Home\IndexController');
		dd(\Router::analyze_params());
		echo '<div style="margin:0 auto;width:500px;"><h1>Welcome to om frame!</h1>
					<p>If you see this page, the om frame is successfully installed and
					working. Further configuration is required.</p>
					<p>For online documentation and support please refer to
					<a href="https://github.com/VictoriaMR/onlyMySQL-Frame">onlyMySQL-Frame</a>.<br/>

					<p><em>Thank you for using om frame.</em></p></div>';
	}
}