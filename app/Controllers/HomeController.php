<?php

namespace App\Controllers;

use CodeIgniter\RESTful\ResourceController;
use CodeIgniter\Exceptions\PageNotFoundException;
use App\Models\CommentModel;
use App\Models\PrModel;
use App\Models\ReportViewModel;
use App\Models\CatchModel;
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

    //最新の漁獲量の取得
public function latest()
{
    log_message('debug', 'HomeController latest');

    try {
        $model = new CatchModel();

        // 最新1件を取得
        $latest = $model->getLatest();

        if (!$latest) {
            return $this->response->setJSON([
                'status' => 404,
                'message' => 'データが存在しません'
            ]);
        }

        // JSON 形式に整形
        $items = [
            [
                'fish' => '若狭ふぐ',
                'volume' => $latest['Torafugu']
            ],
            [
                'fish' => '敦賀真鯛',
                'volume' => $latest['Madai']
            ],
            [
                'fish' => '若狭まはた',
                'volume' => $latest['Mahata']
            ],
        ];

        return $this->response->setJSON([
            'status' => 200,
            'date' => $latest['CreateDate'],
            'items' => $items
        ]);

    } catch (\Exception $e) {

        log_message('error', $e->getMessage());

        return $this->response->setJSON([
            'status' => 500,
            'message' => '予期しない例外が発生しました'
        ]);
    }
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
