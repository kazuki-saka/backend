<?php
namespace App\Controllers;

use CodeIgniter\RESTful\ResourceController;
use CodeIgniter\Exceptions\PageNotFoundException;
use App\Models\ReportViewModel;
use App\Models\TopicsModel;

class ReportListController extends ApiController
{
    // ++++++++++ メンバー ++++++++++


    // ++++++++++ メソッド ++++++++++
    

    //デフォルトメソッド
    public function index()
    {
        //
    }

    //記事一覧を開いた時の初期表示
    public function View()
    {
        // フォームデータ取得
        $signature = (string)$this->request->getPost('user[signature]');
        $fish = (string)$this->request->getPost('user[kind]');
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
        
        $response['status'] = @$validated["status"];

        //記事ビューテーブルから該当の魚種記事を取得
        $kind = 0;
        switch ($fish){
            case "salmon":
                $kind = 1;
                break;
            case "fugu":
                $kind = 2;
                break;
            case "seabream":
                $kind = 3;
                break;
            case "mahata":
                $kind = 4;
        }

        $Repmodel = new ReportViewModel();
        $response['report'] = $Repmodel->GetKindData($kind);

        //トピックステーブルから該当の魚種トピックスを取得
        $topmodel = new TopicsModel();
        $response['topics'] = $topmodel->GetKindData($kind);

        return $this->respond($response);
    }

}