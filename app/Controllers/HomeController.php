<?php

namespace App\Controllers;

use CodeIgniter\RESTful\ResourceController;
use CodeIgniter\Exceptions\PageNotFoundException;
use App\Models\CommentModel;
use App\Models\PrModel;
use App\Libraries\JwtLibrary;

//サイトトップの制御クラス
class HomeController extends ApiController
{
    // ++++++++++ メンバー ++++++++++

    //認証JWT
    private $jwtLib;


    // ++++++++++ メソッド ++++++++++

    //コンストラクタ
    public function __construct() {
        $this->jwtLib = new JwtLibrary();
    }
    
    //デフォルトメソッド
    public function index()
    {
        //
    }

    //トップを開いた時の初期表示
    public function View()
    {
        if ($this->request->getMethod() === 'post'){
            $jwt = $this->request->getPost('Home[jwt]');
            //$ukbn = $this->request->getPost('Home[ukbn]');

            $isjwt = $this->jwtLib->is_jwt_valid($jwt);
            
            if ($isjwt){
                //有効な認証JWT                
                $response['status'] = 200;

                //PR動画テーブルから情報取得
                $prmodel = new PrModel();
                $response['pr'] = $prmodel->GetData();
            }
            else{
                //無効な認証JWT
                $response['status'] = 202;
            }

            //$response['status'] = 0;
        }
        else{
            $response['status'] = 209;
        }

        return $this->respond($response);
    }
}
