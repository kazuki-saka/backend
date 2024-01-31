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

            //利用者テーブルの検索
            $user = $model->findByUsername($email);

            if (!$user->token || !password_verify($pass, $user->password))
            {
                // 該当なし/パスワード不一致
                // [403]
                return $this->fail([
                "status" => 403,
                "message" => "サインインに失敗しました。メールアドレスかパスワードに誤りがあります。"
                ], 403);
            }
      
            // 署名生成
            $signature = $user->createSignature();
            
            //正常
            // [200]
            return $this->respond([
                "status" => 200,
                "signature" => $signature,
            ]);
        }


/*
            $data = $model->ChkUser($email, $pass);
            if ($data['result'] === 0){
                //該当データが無い。メールアドレスかパスワードが間違っている
                $response['status'] = 201;
                return $this->respond($response);
            }
            else{
                //JWT生成
                $headers = array('alg'=>'HS256','typ'=>'JWT');
                $payload = array('token'=>$data['token'], 'exp'=>(time() + 300));
                $response['jwt'] = $this->jwtLib->generate_jwt($headers, $payload);

                //利用者区分
                $response['kbn'] = $data['kbn'];

                //ほしいねテーブル検索
                $likemodel = new LikeModel();
                $response['like'] = $likemodel->GetData($data['token']);

                //コメントテーブル検索
                $commentmodel = new CommentModel();
                $response['comment'] = $commentmodel->GetData($data['token']);
                $response['status'] = 200;
            }
        }
        else{
            $response['status'] = 209;
        }

        return $this->respond($response);
*/
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

      $user = @$validated["user"];

      //$this->echoEx("user->token=", $user->token);

      //ほしいねテーブル検索
      $likemodel = new LikeModel();
      $response['like'] = $likemodel->GetData($user->token);

      //コメントテーブル検索
      $commentmodel = new CommentModel();
      $response['comment'] = $commentmodel->GetData($user->token);
      
      $response["status"] = @$validated["status"];

      return $this->respond($response);
    }

}
