<?php
namespace App\Controllers;

use App\Models\UserTmpModel;
use App\Models\UserModel;
use CodeIgniter\RESTful\ResourceController;
use CodeIgniter\Exceptions\PageNotFoundException;
use App\Helpers\UtilHelper;


//本登録制御クラス
class UserRegistController extends ApiController
{
    // ++++++++++ メンバー ++++++++++

    
    // ++++++++++ メソッド ++++++++++

    
    //トークンが正しいか、仮登録テーブルで確認
    public function ChkToken()
    {
        $model = new UserTmpModel();
        $response = [];
        $preflight = [];
        $token = '';
        //-----------------------------------------------

        $preflight['email'] = '';
        $preflight['token'] = '';
        if ($this->request->getMethod() === 'post'){
            $token = $this->request->getPost('preflight[token]');
            //echo 'token=' + $token;
            $data = $model->GetUserMail($token);

            if ($data['result'] == 0){
                //テーブルに存在しないトークン
                $response['status'] = 201;
            }
            else{
                if ($data['regist_flg'] == 1){
                    //既に本登録済み
                    $response['status'] = 202;
                }
                else{
                    $preflight['email'] = $data['email'];
                    $preflight['token'] = $token;
                    $response['status'] = 200;
                }
            }
        }
        else{
            $response["status"] = 209;
        }
        
        $response['preflight'] = $preflight;
        return $this->respond($response);
    }

    //利用者本登録
    public function Regist()
    {
        $model = new UserModel();
        $modelTmp = new UserTmpModel();
        $response = [];
        $data = [];
        $complete = [];
        $token = "";
        $ret = 0;
        $IsExist = false;
        //-----------------------------------------------

        $postData = (object)$this->request->getPost();
        $postUser = $postData->user;

        $IsExist = $model->IsUser($postUser['username']);
        if ($IsExist === true){
            //既に本登録済み
            return $this->fail([
                "status" => 409,
                "message" => "既に登録されているメールアドレスです。"
              ], 409);
        }

        //利用者テーブルに追加
        $complete['email'] = $postUser['username'];
        $complete['pass'] = $postUser['passphrase'];
        $complete['token'] = UtilHelper::GenerateToken(64);
        $complete['section'] = $postUser['section'];
        $complete['name'] = $postUser['viewname'];
        $complete['personal'] = $postUser['personal'];

        try{
            $model = new UserModel();
            $ret = $model->AddUser($complete);

            // UserEntity取得
            $user = $model->findByToken($complete['token']);

            // 利用者登録登録完了メール送信
            $user->sendThanksNotice();

            // [200]
            return $this->respond([
                "status" => 200,
            ]);
        }
        catch(DatabaseException $e){
            // データベース例外
            return $this->fail([
                "status" => 500,
                "message" => "データベースでエラーが発生しました。"
              ], 500);
            }
        catch (\Exception $e){
            echo("ex=");
            echo($e);
            // その他例外
            return $this->fail([
                "status" => 500,
                "message" => "予期しない例外が発生しました。"
              ], 500);
        }


/*            
        if ($this->request->getMethod() === 'post'){
            
            
            $data = $modelTmp->GetUserMail($token);
            if ($data['result'] == 0){
                //テーブルに存在しないトークン
                $response['status'] = 201;
            }
            else{
                if ($data['regist_flg'] == 1){
                    //既に本登録済み
                    $response['status'] = 202;
                }
                else{
                    //仮登録テーブルの該当レコードを本登録済みに更新
                    $modelTmp->UpdateRegistFlg($token);

                    //利用者テーブルに追加
                    $complete['email'] = $data['email'];
                    $complete['pass'] = $this->request->getPost('complete[pass]');
                    $complete['token'] = $this->MakeToken();
                    $complete['section'] = $this->request->getPost('complete[section]');
                    $complete['name'] = $this->request->getPost('complete[name]');
                    $complete['tel'] = $this->request->getPost('complete[tel]');
                    $model = new UserModel();
                    $ret = $model->AddUser($complete);
                    if ($ret == 1){
                        //サンクスメール送信
                        $this->SendMail($data['email'], $complete);
                        $response['status'] = 200;                        
                    }
                    else{
                        //本登録テーブルの追加に失敗
                        $response['status'] = 203;
                    }
                }
            }
        }
        else{
            $response['status'] = 9;
        }

        return $this->respond($response);
*/
    }

    //認証トークンの取得
    private function MakeToken()
    {
        $prefix = rand(1000, 9999);
        $token = uniqid($prefix);

        return $token;
    }

/*
    //サンクスメール送信（本登録時）
    private function SendMail($iAdr, $iData)
    {
        $email = \Config\Services::email();
        //-----------------------------------------------

        $email->setFrom('sakak499@gmail.com');
        $email->setTo($iAdr);
        $email->setSubject("会員登録ありがとう御座います");

        $template = view("EmailTemplate", $iData);
        $email->setMessage($template);

        //サンクスメール送信
        if ($email->send()) {
            //echo '会員登録しました。';
        }
        else{
            $data = $email->printDebugger(['headers']);
            print_r($data);
        }

        return $email;
    }
*/
}