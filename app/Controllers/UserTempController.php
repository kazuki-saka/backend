<?php

namespace App\Controllers;

use App\Models\UserTmpModel;
use App\Models\UserModel;
use CodeIgniter\RESTful\ResourceController;
use CodeIgniter\Exceptions\PageNotFoundException;
use App\Controllers\Controller;
use Config\Services;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Firebase\JWT\SignatureInvalidException;
use Firebase\JWT\ExpiredException;
use App\Helpers\UtilHelper;
use App\Entities\PreflightEntity;

use CodeIgniter\API\ResponseTrait;
use CodeIgniter\Database\Exceptions\DatabaseException;
use App\Entities\UserEntity;
use App\Entities\TemplateEntity;


//仮登録制御クラス
class UserTempController extends ApiController
{
    use ResponseTrait;

    // ++++++++++ メンバー ++++++++++

    // ++++++++++ メソッド ++++++++++


    //デフォルトメソッド
    public function index()
    {
    /*
        $model = model(UserTmpModel::class);
        $data = [
            'UserTmp' => $model->getUserTmp(),
            'token' => 'tmp user'
        ];
        $response = [];
        //-----------------------------------------------

        //dd($data);

        // レスポンス配列生成
        $response["test"] = $data;

        // [200]
        return $this->respond($response);
    */
    }

    //仮登録テーブルへの登録と認証コードを記載したEメールを送信
    public function AddMail()
    {
        //$model = model(UserTmpModel::class);
        $response = [];
        $adr = ""; 
        $ret = 0;
        $token = "";
        //-----------------------------------------------

        $adr = $this->request->getPost('preflight[email]');
        $token = UtilHelper::GenerateToken(64);

        //$this->echoEx("adr=", $adr);

        try{
            $ret = $this->ChkEmail($adr);

            switch($ret){
                case 200:
                    //仮登録テーブルへの追加
                    $data = $this->AddEmail($adr);

                    // PreflightEntity生成
                    $preflight = new PreflightEntity([
                        "email" => $adr,
                        "token" => $data["token"],
                    ]);

                    // 署名生成(1時間有効)
                    $response["signature"] = $preflight->createSignature(60*60*1);
                    
                    // 認証コードメール送信
                    $preflight->sendAuthcodeNotice($data["authcode"]);
                    
                    $response["token"] = $data["token"];
                    $response["authcode"] = $data["authcode"];
                    $response["messages"]['message'] = "";
                    break;
                case 401:
                    $response["messages"]['message'] = "メールアドレスの形式が不正です";
                    break;
                case 402:
                    $response["messages"]['message'] = "既に本登録済み";
                    break;
            }
            
            $response["status"] = $ret;

            return $this->respond($response);
        }
        catch(DatabaseException $ex){
            // データベース例外
            // [500]
            return $this->fail([
                "status" => 500,
                "message" => "データベースでエラーが発生しました。"
            ], 500);
        }
        catch (\Exception $ex){
            // [500]
            return $this->fail([
                "status" => 500,
                "message" => "予期しない例外が発生しました。"
            ], 500);
        }        
    }

    // 署名、または認証トークンを検証して、該当メールアドレスを返す
    public function LoadPreflight() 
    {
        // フォームデータ取得
        $postData = (object)$this->request->getPost();
        // Preflight取得
        $preflight = $postData->preflight;
        // 認証識別子取得
        $token = @$preflight["token"];
        // 認証署名取得
        $signature = @$preflight["signature"];
      
        //return $signature ? self::_LoadPreflightWithSignature($signature) : self::_LoadPreflightWithToken($token);
        return self::_LoadPreflightWithSignature($signature);
    }
  
    //署名と認証コードのチェック
    public function AuthPreflight()
    {
      // フォームデータ取得
      $postData = (object)$this->request->getPost();
      // Preflight取得
      $postPreflight = $postData->preflight;

      // 認証署名取得
      $signature = @$postPreflight["signature"];
      // 認証コード取得
      $authcode = @$postPreflight["authcode"];

      // Sleep
      sleep(3);

      // 署名検証
      $validated = self::_ValidatePreflightSignature($signature);
      // 署名検証エラー
      if (intval(@$validated["status"]) !== 200)
      {
        return $this->fail([
          "status" => @$validated["status"],
          "message" => @$validated["message"]
        ], @$validated["status"]);
      }

      // Preflight取得
      $preflight = @$validated["preflight"];
      // 認証コード不一致
      if (!password_verify($authcode, $preflight->authcode))
      {
        // [403]
        return $this->fail([
          "status" => 403,
          "message" => "認証コードが一致しません。",
        ], 403);
      }

      // 署名再生成(1時間有効)
      $signature = $preflight->createSignature(60*60*1);

      // [200]
      return $this->respond([
        "status" => 200,
        "signature" => $signature,
      ]);
    }
  
    // 署名を検証して、該当メールアドレスを返す
    private function _LoadPreflightWithSignature(string $signature)
    {
      // 署名検証
      $validated = self::_ValidatePreflightSignature($signature);
      // 署名検証エラー
      if (intval(@$validated["status"]) !== 200)
      {
        return $this->fail([
          "status" => @$validated["status"],
          "message" => @$validated["message"]
        ], @$validated["status"]);
      }
      
      // [200]
      return $this->respond([
        "status" => 200,
        "preflight" => @$validated["preflight"]
      ]);
    }

    // 署名からメールアドレス取得
    private function _ValidatePreflightSignature(string $signature)
    {
      // バリデーション生成
      $validation = Services::validation();
/*
      $validation->setRules([
        "signature" => "required",
      ]);
*/
      $validation->setRule("signature", "認証署名", "required");
      $validation->run(["signature" => $signature]);
      // バリデーションエラー
      if (!$validation->run(["signature" => $signature]))
      {
        return [
          "status" => 401,
          "message" => "認証署名の形式が不正です。"
        ];
      }
      
      try
      {
        // 認証署名復元
        $decoded = JWT::decode($signature, new Key(getenv("jwt.secret.key"), getenv("jwt.signing.algorithm")));
        // 認証識別子取得
        $token = $decoded->data->preflight->token;
        
        // PreflightsModel生成
        $model = new UserTmpModel();
        $preflight = $model->findByToken($token);

        // Preflight該当なし
        if (!$preflight->num)
        {
          // [404]
          return [
            "status" => 404,
            "message" => "該当する署名はありません。",
          ];
        }
        
        // [200]
        return [
          "status" => 200,
          "message" => "",
          "preflight" => $preflight
        ];
      }
      // データベース例外
      catch(DatabaseException $e)
      {
        // [500]
        return [
          "status" => 500,
          "message" => "データベースでエラーが発生しました。"
        ];
      }
      // JSON形式例外
      catch (\JsonException $e)
      {
        // [411]
        return [
          "status" => 411,
          "message" => "認証署名のJSON形式が不正です。"
        ];
      }
      // 署名形式例外
      catch (SignatureInvalidException $e)
      {
        // [401]
        return [
          "status" => 401,
          "message" => "認証署名の形式が不正です。"
        ];
      }
      // 有効期限切例外
      catch (ExpiredException $e)
      {
        // [403]
        return [
          "status" => 403,
          "message" => "認証署名の有効期限が過ぎました。"
        ];
      }
      // その他例外
      catch (\Exception $e)
      {
        // [500]
        return [
          "status" => 500,
          "message" => "予期しない例外が発生しました。"
        ];
      }
    }

    //メールアドレスと正しいかチェック
    private function IsMail($iAdr)
    {
        if (preg_match('/^[a-z0-9._+^~-]+@[a-z0-9.-]+$/i', $iAdr)) {
            return true;
        }
        else{
            return false;
        }
    }

    //仮登録からメールアドレスで情報取得
    // result = 0:登録可能　1:本登録済み　2:メールアドレスが不正　3:仮登録済みで本登録無し（これはOKで良いか？）
    private function ChkEmail($iAdr = null)
    {
        $flg = 0;
        //-----------------------------------------------

        //メールアドレス形式確認
        if ($this->IsMail($iAdr) == false){
            $flg = 401;
        }
        else{
            //$model = model(UserTmpModel::class);
            $model = new UserModel();
            $ret = $model->IsUser($iAdr);
            //-----------------------------------------------

            //一意のメールアドレスか確認
            if ($ret === false) {
                //登録可能メールアドレス
                $flg = 200;
            }
            else{
                //既に本登録済み
                $flg = 402;
            }
        }

        return $flg;
    }

    //仮登録テーブルへの追加
    private function AddEmail($iAdr = null)
    {
        $model = new UserTmpModel();
        $response = [];
        //-----------------------------------------------
        
        //認証トークン生成
        $token = UtilHelper::GenerateToken(64);
        // 認証コード生成
        $authcode = UtilHelper::GetRandomNumber(4);

        // 仮登録テーブルに追加
        $response["sql"] = $model->AddUserTmp($token, $iAdr, $authcode);
        $response["token"] = $token;
        $response["authcode"] = $authcode;
        $response["result"] = 0;

        return $response;
    }
  }
