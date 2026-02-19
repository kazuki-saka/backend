<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;

class LoginController extends BaseController
{
    public function index()
    {
        log_message('debug', 'LoginController start');
        return view('admin/login');
    }

    public function auth()
    {
        $password = $this->request->getPost('password');
        log_message('debug', 'LoginController password=' . $password);

        if ($password === getenv('admin.password')) {
            session()->set('is_admin', true);
            return redirect()->to('/admin');
        }

        return redirect()->back()->with('error', 'パスワードが違います');
    }

    public function logout()
    {
        session()->destroy();
        return redirect()->to('/admin/login');
    }
}