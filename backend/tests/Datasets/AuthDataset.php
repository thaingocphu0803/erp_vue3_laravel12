<?php

dataset('invalid_input_data', [
    'empty email' =>   [['email' => '', 'password' => 'Admin123@@'], ['email']],
    'wrong format email' =>   [['email' => 'admin', 'password' => 'Admin123@@'], ['email']],
    'empty password' =>   [['email' => 'admin@gmail.com', 'password' => ''], ['password']],
    'wrong length password' =>   [['email' => 'admin@gmail.com', 'password' => 'Ad123@'], ['password']],
    'only number password' =>   [['email' => 'admin@gmail.com', 'password' => '11111111'], ['password']],
    'lack uppercase password' =>   [['email' => 'admin@gmail.com', 'password' => 'abc123@@'], ['password']],
    'lack specialchar password' =>   [['email' => 'admin@gmail.com', 'password' => 'abc123AD'], ['password']],
    'lack number password' =>   [['email' => 'admin@gmail.com', 'password' => 'ABCabc@@'], ['password']],
]);

dataset('incorrect_credential', [
    'incorrect credential'=>[['email' => 'test@gmail.com', 'password' => 'Test123@@']]
]);


dataset('correct_credential', [
    'correct credential'=>[['email' => 'admin@gmail.com', 'password' => 'Admin123@@']]
]);


dataset('credential_with_rememberMe', [
    'credential with remember token'=>[['email' => 'admin@gmail.com', 'password' => 'Admin123@@', 'rememberMe' => true]]
]);

dataset('credential_without_rememberMe', [
    'credential without remember token'=>[['email' => 'admin@gmail.com', 'password' => 'Admin123@@', 'rememberMe' => false]]
]);
