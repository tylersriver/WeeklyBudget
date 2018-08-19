<?php

class UserController
{
    public function login()
    {
        $password = (isset($_POST['password'])) ? $_POST['password'] : '';
        $username = (isset($_POST['username'])) ? $_POST['username'] : '';

        // attempt login
        $userInfo = User::GetOne([
            'username' => $username,
        ]);

        // handle invalid login
        if(empty($userInfo) or !password_verify($password, $userInfo['password'])) {
            $_GET['error'] = 'Invalid Username or Password';
            call('pages', 'login');
            return;
        }

        // Set session
        $_SESSION['login_user'] = $username;
        call('pages', 'overview');
    }

    public function logout()
    {
        session_destroy();
        $_GET['error'] = 'Logout successful';
        call('pages', 'login');
    }
}