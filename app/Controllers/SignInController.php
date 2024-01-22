<?php

namespace App\Controllers;

use CodeIgniter\RESTful\ResourceController;
use CodeIgniter\Exceptions\PageNotFoundException;
use App\Models\UserModel;
use App\Models\LikeModel;
use App\Models\CommentModel;

//サインイン制御クラス
class SignInController extends ApiController
{
    // ++++++++++ メンバー ++++++++++

    // ++++++++++ メソッド ++++++++++

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
            $email = $this->request->getPost('signin[email]');
            $pass = $this->request->getPost('signin[pass]');

            //利用者テーブルの検索
            $data = $model->ChkUser($email, $pass);
            if ($data['result'] === 0){
                //該当データが無い。メールアドレスかパスワードが間違っている
                $response['status'] = 201;
                return $this->respond($response);
            }
            else{
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
    }
}
