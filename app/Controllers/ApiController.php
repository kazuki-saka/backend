<?php namespace App\Controllers;
use CodeIgniter\RESTful\ResourceController;
use CodeIgniter\API\ResponseTrait;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Config\Services;
use App\Models\DEventModel;
class ApiController extends ResourceController
{
  // Trait
  use ResponseTrait;
  /** @var Format */
  protected $format = "json";
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