<?php

namespace App\Http\Controllers\Home;

use App\Services\ProductService;

/**
 * 默认入口控制器
 */

class IndexController
{
	protected $serivces = null;

	public function __construct(ProductService $serivces)
    {
    	$this->serivces = $serivces;
    }

	public function index()
	{
		// Redis(0)->set('test', 'testText');
		
		echo Redis()->ttl('test');

		return view();
	}
}