<?php
namespace App\Controllers;

use CodeIgniter\RESTful\ResourceController;
use CodeIgniter\Exceptions\PageNotFoundException;
use App\Models\ReportViewModel;
use App\Models\CommentModel;
use App\Models\LikeModel;

class ReportDetailController extends ApiController
{
    // ++++++++++ メンバー ++++++++++

    //暗号化キー
    protected $key;

    // ++++++++++ メソッド ++++++++++

    //デフォルトメソッド
    public function index()
    {
        //
    }
 
    //記事詳細を開いた時の表示
    public function View()
    {
        // フォームデータ取得
        $signature = (string)$this->request->getget('signature');
        $id = (string)$this->request->getget('id');

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

        //記事テーブルから該当の記事を取得
        $Repmodel = new ReportViewModel();
        $response['report'] = $Repmodel->GetIdData($id);

        //該当記事のコメント一覧を取得
        $Commodel = new CommentModel();
        $response['comment'] = $Commodel->GetIdData($id);

        return $this->respond($response);
    }

    //ほしいね更新
    public function likeup()
    {
        // フォームデータ取得
        $signature = (string)$this->request->getPost('user[signature]');
        $id = (string)$this->request->getPost('report[id]');
        $token = (string)$this->request->getPost('user[token]');
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

        //$this->echoEx("id=", $id);

        //ほしいね更新
        $likempdel = new LikeModel();
        $response['status'] = $likempdel->UpCount($id, $token);
        
        return $this->respond($response);
    }
}
