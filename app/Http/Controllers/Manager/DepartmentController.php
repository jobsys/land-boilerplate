<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\BaseManagerController;
use App\Models\Department;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Modules\Starter\Emnus\State;

class DepartmentController extends BaseManagerController
{
    public function pageDepartment()
    {
        return Inertia::render('PageDepartment');
    }

    public function edit(Request $request)
    {
        [$input, $error] = land_form_validate(
            $request->only(['id', 'parent_id', 'principal_id', 'name', 'description', 'is_active', 'sort_order']),
            ['name' => 'bail|required|string'],
            ['name' => '部门名称'],
        );

        if ($error) {
            return $this->message($error);
        }

        $input['parent_id'] = $input['parent_id'] ?? null;

        if (! $input['parent_id'] && ! $this->login_user->isSuperAdmin()) {
            return $this->json(null, State::NOT_ALLOWED);
        } elseif (! $this->login_user->isSuperAdmin()) {
            //不能访问父级就无法创建子级
            $parent_exists = Department::authorise()->where('id', $input['parent_id'])->exists();
            if (! $parent_exists) {
                return $this->json(null, State::NOT_ALLOWED);
            }
        }

        $input['creator_id'] = $this->login_user_id;

        if (isset($input['id']) && $input['id']) {
            $exists = Department::where('parent_id', $input['parent_id'] ?? null)->where('name', $input['name'])
                ->where('id', '<>', $input['id'])->exists();
        } else {
            $exists = Department::where('parent_id', $input['parent_id'] ?? null)->where('name', $input['name'])->exists();
        }

        if ($exists) {
            return $this->message('存在同级同名部门');
        }

        $result = Department::updateOrCreate(['id' => $input['id'] ?? null], $input);

        return $this->json(null, $result ? State::SUCCESS : State::FAIL);
    }

    public function items(Request $request)
    {
		$items = Department::authorise()->whereNull('parent_id')->orderBy('sort_order', 'DESC')->get();

		$items = land_get_closure_tree($items);

        return $this->json($items);
    }

    public function item(Request $request, $id)
    {
        $item = Department::authorise()->find($id);

        if (! $item) {
            return $this->json(null, State::NOT_FOUND);
        }

        log_access('查看部门详情', $item);

        return $this->json($item);
    }

    public function delete(Request $request)
    {

        $id = $request->input('id');

        $exists = Department::where('parent_id', $id)->exists();

        if ($exists) {
            return $this->message('该部门下拥有子部门，请先删除子部门');
        }

        $item = Department::authorise()->find($id);
        if (! $item) {
            return $this->json(null, State::NOT_FOUND);
        }

        $result = $item->delete();

        return $this->json(null, $result ? State::SUCCESS : State::FAIL);
    }
}
