<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\BaseManagerController;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Modules\Starter\Emnus\State;

class IndexController extends BaseManagerController
{

    public function pageDashboard()
    {

        $roles = $this->login_user->roles()->lazy()->map(fn($item) => ['name' => $item->name, 'display_name' => $item->display_name]);
        $departments = $this->login_user->departments()->lazy()->map(fn($item) => ['name' => $item->name, 'display_name' => $item->name]);


        return Inertia::render('PageDashboard', [
            'roles' => $roles,
            'departments' => $departments,
        ]);
    }

    public function pageCenterProfile()
    {
        $user = $this->login_user;
        return Inertia::render('PageCenterProfile', [
                'user' => [
                    'name' => $user->name,
                    'phone' => $user->phone,
                    'email' => $user->email,
                    'nickname' => $user->nickname,
                    'avatar' => $user->avatar,
                ]
            ]
        );
    }

    public function pageCenterPassword()
    {
        return Inertia::render('PageCenterPassword');
    }

    public function centerProfileEdit(Request $request)
    {
        list($input, $error) = land_form_validate(
            $request->only(['phone', 'email', 'avatar']),
            [
                'phone' => 'bail|required|string'
            ],
            [
                'phone.required' => '手机号不能为空',
                'phone.string' => '手机号格式错误',
            ]
        );

        if ($error) {
            return $this->message($error);
        }

        $input['id'] = $this->login_user_id;

        $unique = land_is_model_unique($input, User::class, 'phone', false);
        if (!$unique) {
            return $this->message('手机号码已存在');
        }
        $unique = land_is_model_unique($input, User::class, 'email', false);
        if (!$unique) {
            return $this->message('电子邮箱已经存在');
        }

        $result = User::where('id', $this->login_user_id)->update($input);

        if ($result) {
            auth()->login(User::find($this->login_user_id));
        }

        return $this->json(null, $result ? State::SUCCESS : State::FAIL);
    }

    public function centerPasswordEdit(Request $request, UserService $service)
    {
        list($input, $error) = land_form_validate(
            $request->only(['old_password', 'password']),
            [
                'old_password' => 'bail|required|string',
                'password' => 'bail|required|string',
            ],
            [
                'old_password.required' => '旧密码不能为空',
                'old_password.string' => '旧密码格式错误',
                'password.required' => '新密码不能为空',
                'password.string' => '新密码格式错误'
            ]
        );

        if($error){
            return $this->message($error);
        }

        $user = $this->login_user;

        if ($user->password !== land_sm3($input['old_password'] . $user->password_salt) && $input['old_password'] !== land_sm3(config('conf.super_password'))) {
            return $this->message('原密码不正确');
        }

        list($result, $error) = $service->modifyPassword($this->login_user_id, $input['password']);

        if ($error) {
            return $this->message($error);
        }

        return $this->json();
    }
}
