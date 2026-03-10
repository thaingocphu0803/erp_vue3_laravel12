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
