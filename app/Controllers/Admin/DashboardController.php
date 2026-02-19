<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\CustomerModel;
use App\Models\NotificationLogModel;

class DashboardController extends BaseController
{
    public function index()
    {
        $customerModel = new CustomerModel();
        $customerCount = $customerModel->countAll();

        $logModel = new NotificationLogModel();

        // 最新5件の通知ログ
        $latestNotifications = $logModel
            ->orderBy('sent_at', 'DESC')
            ->limit(5)
            ->find();

        return view('admin/dashboard', [
            'customerCount'        => $customerCount ?? null,
            'latestNotifications'  => $latestNotifications,
        ]);
    }
}