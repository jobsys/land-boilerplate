<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use Modules\Permission\Services\PermissionService;
use Modules\Starter\Http\Controllers\BaseController;

class BaseManagerController extends BaseController
{
    protected User $login_user;
    protected int $login_user_id;

    public function __construct(PermissionService $service)
    {

        $this->middleware(function ($request, $next) use ($service) {
            if (!auth()->check()) {
                if ($request->ajax() || $request->wantsJson()) {
                    return response('', 401);
                } else {
                    return response()->redirectTo(route('page.login'));
                }
            }

            $this->login_user = auth()->user();
            $this->login_user_id = $this->login_user->id;

            $route_name = Route::current()->getName();

            $permissions = $service->getAllPermissions();

            if ($permissions->contains('name', $route_name) && !$this->login_user->can($route_name)) {
                /*if($request->header('X-Inertia')){
                    Inertia::setRootView('manager');
                    return Inertia::render('PageError', ['status' => 403]);
                }*/
                return response('', 403);
            }

            return $next($request);
        });

        $this->middleware('dataScope.setup:' . session('user_role'));

        $this->middleware(function ($request, $next) {
            Inertia::setRootView('manager');
            return $next($request);
        });
    }
}
