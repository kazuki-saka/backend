<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\CatchModel;
use App\Models\NotificationLogModel;

class DashboardController extends BaseController
{
    public function index()
    {
        $catchModel = new CatchModel();
        $catchData = $catchModel->GetData();
        
        return view('admin/dashboard', [
            'latestUpdates'  => $catchData[0]->UpdateDate,
        ]);
    }
}