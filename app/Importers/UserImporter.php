<?php

namespace App\Importers;


use App\Services\UserService;
use Modules\Importexport\Importers\CollectionImporter;

class UserImporter extends CollectionImporter
{
	public function store(array $row, array $extra): void
	{

		$userService = new UserService();

		$department_ids = [];
		$role_ids = [];

		if ($row['department']) {
			$department_ids[] = $extra['departmentMap'][$row['department']] ?? 0;
		}
		unset($row['department']);

		if ($row['role']) {
			$role_ids[] = $extra['roleMap'][$row['role']] ?? 0;
		}
		unset($row['role']);

		if (!isset($row['password']) || !strlen($row['password'])) {
			$row['password'] = substr($row['phone'], 5);
		}

		list($user, $error) = $userService->editUser($row, $role_ids, $department_ids);
	}
}
