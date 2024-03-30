<?php

namespace App\Controllers;

use CodeIgniter\RESTful\ResourceController;
use CodeIgniter\Exceptions\PageNotFoundException;
use Config\Services;
//use Firebase\JWT\JWT;
//use Firebase\JWT\Key;
//use Firebase\JWT\SignatureInvalidException;
//use Firebase\JWT\ExpiredException;
use App\Models\UserModel;
use App\Models\LikeModel;
use App\Models\CommentModel;
//use App\Libraries\JwtLibrary;

//サインイン制御クラス
class SignInController extends ApiController
{
    // ++++++++++ メンバー ++++++++++

    //認証JWT
    //private $jwtLib;

    // ++++++++++ メソッド ++++++++++

    //コンストラクタ
    public function __construct() {
		  //$this->jwtLib = new JwtLibrary();
	  }

    //デフォルトメソッド
    public function index()
    {
        //
    }

    //サインインチェック
    public function ChkSignIn()
    {
        $model = new UserModel();
        $response = [];
        $data = [];
        //-----------------------------------------------

        if ($this->request->getMethod() === 'post'){
            $email = $this->request->getPost('user[username]');
            $pass = $this->request->getPost('user[passphrase]');

            try{
                //利用者テーブルの検索
                $user = $model->findByUsername($email);

                if (!$user->token || !password_verify($pass, $user->password)){
                    // 該当なし/パスワード不一致
                    // [403]
                    return $this->fail([
                    "status" => 407,
                    "message" => "サインインに失敗しました。メールアドレスかパスワードに誤りがあります。"
                    ], 407);
                }

                // 署名生成(10日間有効)
                $signature = $user->createSignature(60*60*24*10);
                
                //正常
                // [200]
                return $this->respond([
                    "status" => 200,
                    "signature" => $signature
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

    //署名チェック
    public function GuardUser()
    {
        // フォームデータ取得
        $postData = (object)$this->request->getPost();
        // User取得
        $postUser = $postData->user;
        // 認証署名取得
        $signature = @$postUser["signature"];
        
        // 署名検証
        $validated = $this->ValidateUserSignature($signature);
        // 署名検証エラー
        if (intval(@$validated["status"]) !== 200)
        {
            return $this->fail([
                "status" => @$validated["status"],
                "message" => @$validated["message"]
            ], intval(@$validated["status"]));
        }

        try{
            //ユーザー情報
            $user = @$validated["user"];
            $response['user'] = $user;
            
            //ほしいねテーブル検索
            $likemodel = new LikeModel();
            $response['like'] = $likemodel->GetData($user->token);

            //コメントテーブル検索
            $commentmodel = new CommentModel();
            $response['comment'] = $commentmodel->GetData($user->token);

            $response["status"] = @$validated["status"];
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

        return $this->respond($response);
    }
}
