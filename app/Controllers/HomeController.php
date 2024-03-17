<?php

namespace App\Controllers;

use CodeIgniter\RESTful\ResourceController;
use CodeIgniter\Exceptions\PageNotFoundException;
use App\Models\CommentModel;
use App\Models\PrModel;
use App\Models\ReportViewModel;
use App\Libraries\JwtLibrary;

//サイトトップの制御クラス
class HomeController extends ApiController
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

    //トップを開いた時の初期表示
    public function View()
    {
        if ($this->request->getMethod() === 'get'){
            $response = [];
            return $this->respond($response);
        }

        // フォームデータ取得
        $signature = (string)$this->request->getPost('user[signature]');
        $offset = (integer)$this->request->getPost('user[offset]');
        //$this->echoEx("getData=", $signature);

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
            $response['status'] = @$validated["status"];

            //レポートビューテーブルかトピックスを取得
            $Repmodel = new ReportViewModel();
            $response['topics'] = $Repmodel->GetTopics();    
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
