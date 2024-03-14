<?php
namespace App\Controllers;

use App\Models\UserModel;
use CodeIgniter\RESTful\ResourceController;
use CodeIgniter\Exceptions\PageNotFoundException;
use App\Helpers\UtilHelper;


//本登録制御クラス
class UserRegistController extends ApiController
{
    // ++++++++++ メンバー ++++++++++

    
    // ++++++++++ メソッド ++++++++++

    //利用者本登録
    public function Regist()
    {
        $model = new UserModel();
        $response = [];
        $complete = [];
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
        $complete['shopname'] = $postUser['shopname'];
        $complete['viewname'] = $postUser['viewname'];
        $complete['personal'] = $postUser['personal'];

        try{
            $model = new UserModel();
            $ret = $model->AddUser($complete);

            // UserEntity取得
            $user = $model->findByToken($complete['token']);

            // 利用者登録登録完了メール送信
            $user->sendThanksNotice($postUser['passphrase']);

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
            // その他例外
            return $this->fail([
                "status" => 500,
                "message" => "予期しない例外が発生しました。"
              ], 500);
        }
    }
}