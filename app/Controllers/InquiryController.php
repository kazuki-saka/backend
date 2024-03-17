<?php

namespace App\Controllers;

use CodeIgniter\RESTful\ResourceController;
use CodeIgniter\Exceptions\PageNotFoundException;
use Config\Services;
use App\Models\UserModel;
use App\Entities\InquiryEntity;

use CodeIgniter\API\ResponseTrait;
use CodeIgniter\Database\Exceptions\DatabaseException;
use App\Entities\UserEntity;
use App\Entities\TemplateEntity;

//問い合わせ制御クラス
class InquiryController extends ApiController
{
    // ++++++++++ メンバー ++++++++++


    // ++++++++++ メソッド ++++++++++

    //デフォルトメソッド
    public function index()
    {
        //
    }
    
  //問い合わせ時の店名・登録名を取得
    public function View()
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

        $user = @$validated["user"];
        try{
            $usermodel = new UserModel();
            $response['user'] = $usermodel->GetUserData($user->token);
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

    //問い合わせ時のメール送信
    public function Send()
    {
        // フォームデータ取得
        $signature = (string)$this->request->getPost('inquiry[signature]');
        $shopname = (string)$this->request->getPost('inquiry[shopname]');
        $rejistname = (string)$this->request->getPost('inquiry[rejistname]');
        $postcode = (string)$this->request->getPost('inquiry[postcode]');
        $address = (string)$this->request->getPost('inquiry[address]');
        $fishkind = (string)$this->request->getPost('inquiry[kind]');
        $detail = (string)$this->request->getPost('inquiry[detail]');

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

        $user = @$validated["user"];

        try{
            $inquiry = new InquiryEntity([
                "shopname" => $shopname,
                "rejistname" => $rejistname,
                "postcode" => $postcode,
                "address" => $address,
                "detail" => $detail,
                "fishkind" => $fishkind
            ]);

            $inquiry->SendInquiry();
            $response["status"] = 200;
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