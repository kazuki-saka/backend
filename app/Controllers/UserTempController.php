<?php

namespace App\Controllers;

use App\Models\UserTmpModel;
use CodeIgniter\RESTful\ResourceController;
use CodeIgniter\Exceptions\PageNotFoundException;
use App\Controllers\Controller;

//仮登録制御クラス
class UserTempController extends ApiController
{
    // ++++++++++ メンバー ++++++++++

    // ++++++++++ メソッド ++++++++++


    //デフォルトメソッド
    public function index()
    {
        $model = model(UserTmpModel::class);
        $data = [
            'UserTmp' => $model->getUserTmp(),
            'token' => 'tmp user'
        ];
        $response = [];
        //-----------------------------------------------

        //dd($data);

        // レスポンス配列生成
        $response["test"] = $data;

        // [200]
        return $this->respond($response);
    }

    //仮登録テーブルへの登録
    public function AddMail()
    {
        //$model = model(UserTmpModel::class);
        $model = new UserTmpModel();
        $flg = 0;
        $response = [];
        $adr = ""; 
        //-----------------------------------------------

        if ($this->request->getMethod() === 'post'){
            $adr = $this->request->getPost('preflight[email]');

            $flg = $this->ChkEmail($adr);
            switch($flg){
                case 200:
                    //仮登録テーブルへの追加
                    $token = $this->AddEmail($adr);

                    //サンクスメール送信
                    $this->SendMailTemp($adr, $token);
                    break;
                case 1:
                    //$response["info"] = 'メールアドレスの形式が不正です';
                    break;
                case 2:
                    //$response["info"] = '既に本登録済み';
                    break;
            }

            $response["status"] = $flg;                    
        }
        else{
            $response["status"] = 9;
        }

        return $this->respond($response);
    }

    //メールアドレスと正しいかチェック
    private function IsMail($iAdr)
    {
        if (preg_match('/^[a-z0-9._+^~-]+@[a-z0-9.-]+$/i', $iAdr)) {
            return true;
        }
        else{
            return false;
        }
    }

    //仮登録からメールアドレスで情報取得
    // result = 0:登録可能　1:本登録済み　2:メールアドレスが不正　3:仮登録済みで本登録無し（これはOKで良いか？）
    private function ChkEmail($iAdr = null)
    {
        $flg = 0;
        //-----------------------------------------------

        //メールアドレス形式確認
        if ($this->IsMail($iAdr) == false){
            $flg = 1;
        }
        else{
            //$model = model(UserTmpModel::class);
            $model = new UserTmpModel();
            $data['UserTmp'] = $model->getUserTmp($iAdr);
            //-----------------------------------------------

            //一意のメールアドレスか確認
            if (empty($data['UserTmp'])) {
                //登録可能メールアドレス
                $flg = 200;
            }
            else{
                //既に本登録済み
                $flg = 2;
            }
        }

        return $flg;
    }

    //仮登録テーブルへの追加
    private function AddEmail($iAdr = null)
    {
        //$model = model(UserTmpModel::class);
        $model = new UserTmpModel();
        $prefix = rand(1000, 9999);
        $token = uniqid($prefix);
        $response = [];
        //-----------------------------------------------
        
        $response['sql'] = $model->AddUserTmp($token, $iAdr);
        $response['token'] = $token;
        $response["result"] = 0;

        return $token;
    }

    //トークンからEメール取得
    public function GetMailAdr($iToken)
    {
        $model = model(UserTmpModel::class);
        $response = [];
        $data['UserTmp'] = $model->GetUserMail($iToken);
        //-----------------------------------------------

        $response['email'] = $data['UserTmp']['email'];

        return $this->respond($response);
    }

    //サンクスメール送信（仮登録時）
    private function SendMailTemp($iAdr, $iToken)
    {
        $email = \Config\Services::email();
        //-----------------------------------------------

        $email->setFrom('sakak499@gmail.com');
        $email->setTo($iAdr);
        $email->setSubject("仮登録ありがとう御座います");

        $data = [
            'token' => $iToken
        ];

        $template = view("EmailTemplateTemp", $data);
        $email->setMessage($template);

        //サンクスメール送信
        if ($email->send()) {
            //echo '仮登録しました。';
        }
        else{
            $data = $email->printDebugger(['headers']);
            print_r($data);
        }

        return $email;
    }
}
