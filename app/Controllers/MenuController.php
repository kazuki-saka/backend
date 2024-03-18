<?php

namespace App\Controllers;

use CodeIgniter\RESTful\ResourceController;
use CodeIgniter\Exceptions\PageNotFoundException;
use App\Models\CommentModel;
use App\Models\LikeModel;
use App\Models\ReportViewModel;
use App\Libraries\JwtLibrary;

//メニューの制御クラス
class MenuController extends ApiController
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

    //メニューからほしいねを選択した時の表示
    public function GetLikeList()
    {
        // フォームデータ取得
        $signature = (string)$this->request->getPost('user[signature]');

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
            $user = @$validated["user"];

            //ビューテーブルから自分がほしいねをした記事を取得
            $Repmodel = new ReportViewModel();
            $response['likereports'] = $Repmodel->GetMyLikeReport($user->token);    
            $response['status'] = @$validated["status"];
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
