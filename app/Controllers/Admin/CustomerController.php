<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\CustomerModel;
use App\Models\TokenModel;
use App\Models\DownloadLogModel;
use App\Libraries\MailService;

//管理者画面用　顧客制御
class CustomerController extends BaseController
{
    //初期表示
    public function index()
    {
        log_message('debug', 'CustomerController start');

        //ソートキー
        $sort  = $this->request->getGet('sort') ?? 'CustomerNo';
        $order = $this->request->getGet('order') ?? 'asc';

        // ソート許可カラム
        $allowedSort = [
            'CustomerNo',
            'CutomerName',
            'MailAdr',
            'DownloadDate',
            'MailSendFlg',
        ];

        if (!in_array($sort, $allowedSort)) {
            $sort = 'CustomerNo';
        }

        if (!in_array($order, ['asc', 'desc'])) {
            $order = 'asc';
        }

        $model = new CustomerModel();

        //検索キー
        $customerNumber = $this->request->getGet('customer_number');
        $customerName   = $this->request->getGet('customer_name');

        if (empty($customerNumber) && empty($customerName)) {
            log_message('debug', 'CustomerController 検索無し');
            $customerNumber = '';
            $customerName = '';
        }
        elseif (!empty($customerNumber) && empty($customerName)){
            log_message('debug', 'CustomerController 顧客番号で検索');
            $customerName = '';
        }
        elseif (empty($customerNumber) && !empty($customerName)){
            log_message('debug', 'CustomerController 顧客名で検索');
            $customerNumber = '';
        }

        $tmpCustomers = $model->getAllSearchNo($customerNumber, $sort, $order);
        if ($customerName !== ''){
            $customers = [];
            foreach ($tmpCustomers as $customer){
                if (str_contains($customer['CutomerName'], $customerName)){
                    array_push($customers, $customer);
                }
            }
        }
        else{
            $customers = $tmpCustomers;
        }
        
        return view('admin/customers/index', [
            'customers' => $customers,
            'sort'      => $sort,
            'order'     => $order,
        ]);
    }

    //編集ボタン
    public function edit($customerNo)
    {
        $model = new CustomerModel();
        $customer = $model->getCustomer($customerNo);

        return view('admin/customers/edit', [
            'customer' => $customer,
        ]);
    }

    //更新ボタン
    public function update($customerNo)
    {
        $model = new CustomerModel();

        $data = [
            'CustomerNo'    => $customerNo,
            'CutomerName'   => $this->request->getPost('CutomerName'),
            'CutomerName2'  => $this->request->getPost('CutomerName2'),
            'MailAdr'       => $this->request->getPost('MailAdr'),
            'MailAdrSub1'   => $this->request->getPost('MailAdrSub1'),
            'MailAdrSub2'   => $this->request->getPost('MailAdrSub2'),
            'MailAdrSub3'   => $this->request->getPost('MailAdrSub3'),
            'MailSendFlg'   => $this->request->getPost('MailSendFlg'),
        ];

        $model->registCustomer($data);

        return redirect()->to('/admin/customers')->with('message', '更新しました');
    }

    //メール再送信
    public function resend($customerNo)
    {
        $model = new CustomerModel();
        $customer = $model->getCustomer($customerNo);

        if (!$customer) {
            return redirect()->to('/admin/customers')->with('error', '顧客が見つかりません');
        }

        // 送信不可の場合はガード
        if ($customer['MailSendFlg'] == 0) {
            return redirect()->to('/admin/customers')->with('error', 'この顧客はメール送信不可です');
        }

        $tokenModel = new TokenModel();
        $token = $tokenModel->getToken($customerNo);

        $mailService = new MailService();
        $result = $mailService->resendInvoiceMail($customer, $token);

        if ($result['success']){
            return redirect()->to('/admin/customers')->with('message', '通知メールを再送しました');
        } else {
            return redirect()->to('/admin/customers')->with('error', 'メール送信に失敗しました');
        }
    }    

    public function create()
    {
        return view('admin/customers/create');
    }

    public function store()
    {
        log_message('debug', 'CustomerController store start');
        $model = new CustomerModel();

        $data = [
            'CustomerNo'   => $this->request->getPost('CustomerNo'),
            'CutomerName'  => $this->request->getPost('CutomerName'),
            'CutomerName2' => $this->request->getPost('CutomerName2'),
            'MailSendFlg'  => $this->request->getPost('MailSendFlg'),
            'MailAdr'      => $this->request->getPost('MailAdr'),
            'MailAdrSub1'  => $this->request->getPost('MailAdrSub1'),
            'MailAdrSub2'  => $this->request->getPost('MailAdrSub2'),
            'MailAdrSub3'  => $this->request->getPost('MailAdrSub3'),
        ];

        $model->registCustomer($data);
        //$model->insert($data);

        return redirect()->to('/admin/customers')->with('message', '顧客を追加しました');
    }

    public function delete($customerNo)
    {
        $model = new CustomerModel();

        $model->where('CustomerNo', $customerNo)->delete();

        return redirect()->to('/admin/customers')->with('message', '顧客を削除しました');
    }
}
