<?php

namespace App\Enum;

enum Table: string
{
	case DEPARTMENT = 'departments';
	case PERMISSION = 'permissions';
	case ROLE = 'roles';
	case POSITION = 'positions';
	case EMPLOYEE = 'employees';
}
