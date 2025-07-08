<?php

use App\Models\Area;
use App\Models\Campus;
use App\Models\College;
use App\Models\Major;
use App\Models\StuClass;
use App\Models\Student;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Modules\Permission\Enums\Scope;


if (!function_exists('is_browser_compatibility')) {
	/**
	 * 检测浏览器是否兼容
	 * @return bool
	 */
	function is_browser_compatibility(): bool
	{
		return (Browser::isChrome() && Browser::browserVersion() >= 100) ||
			(Browser::isFirefox() && Browser::browserVersion() >= 100) ||
			(Browser::isSafari() && Browser::browserVersion() >= 15) ||
			(Browser::isEdge() && Browser::browserVersion() >= 100);
	}
}
