<?php
namespace App\Controllers;

use CodeIgniter\RESTful\ResourceController;
use CodeIgniter\Exceptions\PageNotFoundException;
use App\Models\ReportViewModel;
use App\Models\CommentModel;
use App\Models\LikeModel;
use App\Models\ReportModel;


//記事詳細制御クラス
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

        try{
            //記事テーブルから該当の記事を取得
            $Repmodel = new ReportViewModel();
            $response['report'] = $Repmodel->GetIdData($id);

            //該当記事のコメント一覧を取得
            $Commodel = new CommentModel();
            $response['comment'] = $Commodel->GetIdData($id);
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

        try{
            //ほしいね更新
            $likempdel = new LikeModel();
            $response['status'] = $likempdel->UpCount($id, $token);
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

    //コメント登録
    public function RejistComment()
    {
        $signature = (string)$this->request->getPost('user[signature]');
        $token = (string)$this->request->getPost('user[token]');
        $id = (string)$this->request->getPost('report[id]');
        $comment = (string)$this->request->getPost('report[comment]');

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

        try{
            $this->echoEx("token=", $token);
            $this->echoEx("id=", $id);
            $this->echoEx("comment=", $comment);

            //コメント登録
            $commentmodel = new CommentModel();
            $response['status'] = $commentmodel->Rejist($id, $token, $comment);

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

    //生産者による投稿
    public function RejistReport()
    {
        $signature = (string)$this->request->getPost('user[signature]');
        $token = (string)$this->request->getPost('user[token]');
        $title = (string)$this->request->getPost('report[title]');
        $fish = (string)$this->request->getPost('report[kind]');
        $detail = (string)$this->request->getPost('report[detail]');
        $flg = (string)$this->request->getPost('report[upflg]');

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
        try{
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
    
            //記事の登録
            $reportmodel = new ReportModel();

            //$this->echoEx("kind=", $kind);
            $id = $reportmodel->GetNewId($kind);

            //$this->echoEx("token=", $token);
            //$this->echoEx("id=", $id);
            //$this->echoEx("title=", $title);
            //$this->echoEx("comment=", $detail);

            $response['status'] = $reportmodel->Rejist($id, $kind, $title, $detail, $token);
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
