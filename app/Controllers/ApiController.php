<?php namespace App\Controllers;
use CodeIgniter\RESTful\ResourceController;
use CodeIgniter\API\ResponseTrait;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Config\Services;
use App\Models\DEventModel;
use App\Models\UserModel;
use App\Helpers\UtilHelper;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Firebase\JWT\SignatureInvalidException;
use Firebase\JWT\ExpiredException;

class ApiController extends ResourceController
{
  // Trait
  use ResponseTrait;
  /** @var Format */
  protected $format = "json";

  //デバッグ用
  public function echoEx($iStr, $iValue)
  {
    if ($iValue){
      UtilHelper::echoEx($iStr, $iValue);
    }else{
      UtilHelper::echoEx($iStr, "(null)");  
    }
  }

  public function EventListJson()
  {
    header("Access-Control-Allow-Origin: *");
    header("Access-Control-Allow-Methods: POST,GET, OPTIONS");
    header("Access-Control-Allow-Headers: *");
    // Response配列生成
    $response = [];
    $response["error"] = true;
    $eventModel = new DEventModel();
    $response["data"] = $eventModel->getRecordsAll();
    // エラーなら500返す
    // ...
    // 200
    return $this->Respond($response);
  }

  //署名の検証
  protected function ValidateUserSignature(string $signature)
  {
    // バリデーション生成
    $validation = Services::validation();
    $validation->setRules([
      "signature" => "required",
    ]);
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
      $token = $decoded->data->user->token;
      // UsersModel生成
      $model = new UserModel();
      // User取得
      $user = $model->findByToken($token);

      // User該当なし
      if (!$user || !$user->token)
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
        "user" => $user,
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
        "message" => "JSONの形式が不正です。"
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
      // [401]
      return [
        "status" => 401,
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

   /**
   * テスト
   */
  public function Test()
  {
    // レスポンス配列生成
    $response = [];
    $response["test"] = "test";
    // [200]
    return $this->respond($response);
  }

}