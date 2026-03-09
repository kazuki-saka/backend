<?php

namespace App\Controllers;

use CodeIgniter\RESTful\ResourceController;
use CodeIgniter\Exceptions\PageNotFoundException;
use App\Models\PrLikeModel;

//PR動画のいいね制御クラス
class PrLikeController extends ApiController
{
    // ++++++++++ メンバー ++++++++++



    // ++++++++++ メソッド ++++++++++

    //ほしいね数の取得
    public function get()
    {
        $fish = $this->request->getGet('fish');
        $model = new PrLikeModel();

        try{
            $row = $model->find($fish);

            return $this->respond([
                'status' => 200,
                'count' => $row ? $row['LikeCnt'] : 0
            ]);
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
    }

    //ほしいね数のアップ
    public function add()
    {
        $fish = $this->request->getPost('fish');
        $model = new PrLikeModel();

        try{
            $row = $model->find($fish);

            if ($row) {
                $newCount = $row['LikeCnt'] + 1;
                $model->update($fish, ['LikeCnt' => $newCount]);
            } else {
                $newCount = 1;
                $model->insert(['FishKind' => $fish, 'LikeCnt' => 1]);
            }

            return $this->respond([
                'status' => 200,
                'count' => $newCount
            ]);
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
    }
}