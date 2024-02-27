<?php
namespace App\Controllers;

use CodeIgniter\RESTful\ResourceController;
use CodeIgniter\Exceptions\PageNotFoundException;
use App\Models\ReportViewModel;
use App\Models\CommentModel;
use App\Models\LikeModel;
use App\Models\ReportModel;
use App\Models\TopicsModel;


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
        $signature = (string)$this->request->getPost('user[signature]');
        $id = (string)$this->request->getPost('report[id]');
        $kind = (string)$this->request->getPost('report[kind]');

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
        $response['report'] = [];
        $response['comment'] = [];
        $response['topics'] = [];
        $response["likenum"] = 0;

        try{
            if ($kind == 1){
                //トピックステーブルから該当の記事を取得
                $topicmodel = new TopicsModel();
                $response['topics'] = $topicmodel->GetIdData($id);
            }
            else{
                //記事テーブルから該当の記事を取得
                $Repmodel = new ReportModel();
                $response['report'] = $Repmodel->GetData($id);

                //該当記事のコメント一覧を取得
                $Commodel = new CommentModel();
                $response['comment'] = $Commodel->GetIdData($id);

                //該当記事のほしいね数を取得
                $LikeModel = new LikeModel();
                $response["likenum"] = $LikeModel->GetIdData($id);
            }
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
        $user = @$validated["user"];
        $token = $user->token;

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
        $user = @$validated["user"];
        $token = $user->token;

        try{
            //$this->echoEx("token=", $token);
            //$this->echoEx("id=", $id);
            //$this->echoEx("comment=", $comment);

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
        $title = (string)$this->request->getPost('report[title]');
        $kind = (string)$this->request->getPost('report[kind]');
        $detail = (string)$this->request->getPost('report[detail]');

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
        $user = @$validated["user"];
        $token = $user->token;

        try{
    
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
