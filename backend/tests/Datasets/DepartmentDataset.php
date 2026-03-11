<?php

dataset('valid_department_create',[
	'Full request data' => [['name' => 'Department','code' => 'OKSSS']],
	'Missing code' => [['name' => 'Department2','code' => null]],
]);

dataset('invalid_department_create', [
	'Missing name' => [['name'=>null, 'code'=>null],['name']],
	'The name exceeds the maximum length' => [['name'=>'Learning new programming concepts takes time and patience. Practicing every day helps developers improve their skills and write cleaner, more efficient code.', 'code'=>null],['name']],
	'The name exceeds the Regex' => [['name'=>'Department @', 'code'=>null],['name']],
	'The code exceeds the maximum length' => [['name'=>'Department 2', 'code'=>'Learning new programming concepts takes time and patience. Practicing every day helps developers improve their skills and write cleaner, more efficient code.'],['code']]
]);

dataset('valid_filter_department_index', [
	'Full filter data' => [['page'=> 1, 'itemsPerPage'=> 10, 'search' => 'A', 'sortOrder'=>'asc', 'sortKey'=>'name', 'status'=>'A']],
	'Default filter' =>[['page'=> 1, 'itemsPerPage'=> 10]],
	'Add search filter' =>[['page'=> 1, 'itemsPerPage'=> 10, 'search'=> 'H' ]],
	'Add sort filter' =>[['page'=> 1, 'itemsPerPage'=> 10, 'sortOrder'=>'desc', 'sortKey'=>'name']],
	'Add status filter' =>[['page'=> 1, 'itemsPerPage'=> 10, 'status'=>'X']],
]);

dataset('invalid_filter_department_index', [
	'Only page' => [['page'=> 1], ['itemsPerPage']],
	'Only itemsPerPage' => [['itemsPerPage'=> 10], ['page']],
	'Incorrect format' => [['page'=> 'A', 'itemsPerPage'=> 'A'], ['page', 'itemsPerPage']],
	'Incorrect itemsPerPage range'=>[['itemsPerPage'=> 50], ['itemsPerPage']],
	'Incorrect status range'=>[['page'=> 1, 'itemsPerPage'=> 5, 'status'=>'D'], ['status']],
	'Incorrect sortOrder range'=>[['page'=> 1, 'itemsPerPage'=> 5, 'sortOrder'=>'D'], ['sortOrder']]
]);
