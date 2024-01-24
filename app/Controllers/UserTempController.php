<?php

namespace App\Controllers;

use App\Models\UserTmpModel;
use CodeIgniter\RESTful\ResourceController;
use CodeIgniter\Exceptions\PageNotFoundException;
use App\Controllers\Controller;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Firebase\JWT\SignatureInvalidException;
use Firebase\JWT\ExpiredException;
use App\Helpers\UtilHelper;
use App\Entities\PreflightEntity;

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

    //仮登録テーブルへの登録と認証コードを記載したEメールを送信
    public function AddMail()
    {
        //$model = model(UserTmpModel::class);
        $response = [];
        $adr = ""; 
        $ret = 0;
        $token = "";
        //-----------------------------------------------

        $adr = $this->request->getPost('preflight[email]');
        $token = UtilHelper::GenerateToken(64);

        try{
            $ret = $this->ChkEmail($adr);
            switch($ret){
                case 200:
                    //仮登録テーブルへの追加
                    $data = $this->AddEmail($adr);

                    // PreflightEntity生成
                    $preflight = new PreflightEntity([
                        "email" => $adr,
                        "token" => $data["token"],
                    ]);

                    // 署名生成(1時間有効)
                    $response["signature"] = $preflight->createSignature(60*60*1);
                    
                    // 認証コードメール送信
                    $preflight->sendAuthcodeNotice($data["authcode"]);
                    
                    $response["token"] = $data["token"];
                    $response["authcode"] = $data["authcode"];
                    $response["messages"]['message'] = "";
                    break;
                case 201:
                    //$response["info"] = 'メールアドレスの形式が不正です';
                    break;
                case 202:
                    //$response["info"] = '既に本登録済み';
                    break;
            }
            
            $response["status"] = $ret;

            return $this->respond($response);
        }
        catch(DatabaseException $ex){
            // データベース例外
            // [500]
            return $this->fail([
                "status" => 500,
                "message" => "データベースでエラーが発生しました。"
            ], 500);
        }
        catch (\Exception $ex){
            // [500]
            return $this->fail([
                "status" => 500,
                "message" => "予期しない例外が発生しました。"
            ], 500);
        }

        
/*
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
                case 201:
                    //$response["info"] = 'メールアドレスの形式が不正です';
                    break;
                case 202:
                    //$response["info"] = '既に本登録済み';
                    break;
            }

            $response["status"] = $flg;                    
        }
        else{
            $response["status"] = 209;
        }

        return $this->respond($response);
*/
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
            $flg = 201;
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
                $flg = 202;
            }
        }

        return $flg;
    }

    //仮登録テーブルへの追加
    private function AddEmail($iAdr = null)
    {
        //$model = model(UserTmpModel::class);
        $model = new UserTmpModel();
        //$prefix = rand(1000, 9999);
        //$token = uniqid($prefix);
        $response = [];
        //-----------------------------------------------
        
        //認証トークン生成
        $token = UtilHelper::GenerateToken(64);
        // 認証コード生成
        $authcode = UtilHelper::GetRandomNumber(4);

        // 仮登録テーブルに追加
        $response["sql"] = $model->AddUserTmp($token, $iAdr, $authcode);
        $response["token"] = $token;
        $response["authcode"] = $authcode;
        $response["result"] = 0;

        return $response;
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
