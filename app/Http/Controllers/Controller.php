<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

class Controller extends BaseController
{
	use AuthorizesRequests, ValidatesRequests;


	public function index()
	{
		$view = "index-" . config('conf.customer_identify');
		if (!view()->exists($view)) {
			$view = "index-default";
		}
		return response()->view($view);
	}
}
