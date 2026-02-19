<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;

class LoginController extends BaseController
{
    public function index()
    {
        return view('admin/login');
    }

    public function auth()
    {
        $password = $this->request->getPost('password');

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