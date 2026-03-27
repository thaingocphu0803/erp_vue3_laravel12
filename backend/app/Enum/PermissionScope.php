<?php

namespace App\Enum;

enum PermissionScope: string
{
    case ALL = 'ALL';
	case DEPT = 'DEPT';
	case OWN = 'OWN';
}
