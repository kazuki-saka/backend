<?php

namespace App\Models;

use CodeIgniter\Model;
use CodeIgniter\Database\Query;
use App\Entities\UserEntity;

//仮登録テーブル制御クラス
class UserModel extends Model
{
    // ++++++++++ メンバー ++++++++++

    protected $db;

    //テーブル名
    protected $table = 'm_user';

    //更新対象フィールド
    protected $allowedFields = ['email','password','token','user_kbn', 'fish_code', 'rejist_name', 'tel', 'fax', 'address', 'representation'];

    //暗号化キー
    protected $key;
    protected $skipValidation = false;
    protected $useSoftDeletes = false;
    protected $useTimestamps = true;
  
    // ++++++++++ メソッド ++++++++++
    
    //利用者テーブルへの追加
    public function AddUser($iData)
    {
        // 暗号鍵取得
        $key = getenv("database.default.encryption.key");

        // クエリ生成        
        $query = $this->db->prepare(static function ($db) 
        {
            $sql = "INSERT INTO m_user ( `email`, `password`, `token`, `user_kbn`, `rejist_name`, `tel`) 
                VALUES ( AES_ENCRYPT(?, UNHEX(SHA2(?,512))),
                        ?,
                        ?,
                        ?,
                        AES_ENCRYPT(?, UNHEX(SHA2(?,512))),
                        AES_ENCRYPT(?, UNHEX(SHA2(?,512))))";
            return (new Query($db))->setQuery($sql);
        });
     
        // クエリ実行
        $result = $query->execute(
            $iData['email'],
            $key,
            password_hash($iData["pass"], PASSWORD_DEFAULT),
            $iData['token'],
            $iData['section'],
            $iData['name'],
            $key,
            $iData['personal']['phonenumber'],
            $key
        );
        
        return $result;
    }

    //利用者テーブルに登録されているか確認
    //（メールアドレスで確認）
    public function findByUsername($username)
    {
      // 暗号鍵取得
      $key = getenv("database.default.encryption.key");
      // クエリ生成
      $query = $this->db->prepare(static function ($db) 
      {
        $sql = "SELECT *, AES_DECRYPT(`email`, UNHEX(SHA2(?,512))) AS `username`, AES_DECRYPT(`rejist_name`, UNHEX(SHA2(?,512))) AS `personal`
                 FROM m_user  HAVING username = ?";
        return (new Query($db))->setQuery($sql);
      });
      // クエリ実行
      $result = $query->execute(
        $key,
        $key,
        $username
      );
      // レコード取得
      $row = $result->getRow();
      
      return $row && $row->token ? new UserEntity((array)$row) : new UserEntity();
    }
  
/*
    //利用者テーブルに登録されているか確認
    public function ChkUser($iMail, $iPass)
    {
        // 暗号鍵取得
        $key = getenv("database.default.encryption.key");
        
        // クエリ生成
        $query = $this->db->prepare(static function ($db) 
        {
            $sql = "SELECT AES_DECRYPT(`email`, UNHEX(SHA2(?,512))) as eml,
                            AES_DECRYPT(`password`, UNHEX(SHA2(?,512))) as pass,
                            ukbn,
                            token as tok
                FROM m_user HAVING eml = ? AND pass = ?";
            return (new Query($db))->setQuery($sql);
        });

        // クエリ実行
        $result = $query->execute(
            $key,
            $key,
            $iMail,
            $iPass
        );
        
        $data = $result->getRow();
        if ($data == null){
            //対象のEメールアドレスが存在しない
            $result1['result'] = 0;
        }
        else{
            $result1['result'] = 1;
            $result1['token'] = $data->tok;
            $result1['kbn'] = $data->ukbn;
        }
 
        return $result1;
    }
*/

    //利用者テーブルに登録されているか確認
    public function IsUser($iMail)
    {
        // 暗号鍵取得
        $key = getenv("database.default.encryption.key");
        
        // クエリ生成
        $query = $this->db->prepare(static function ($db) 
        {
            $sql = "SELECT AES_DECRYPT(`email`, UNHEX(SHA2(?,512))) as eml
                    FROM m_user HAVING eml = ?";
            return (new Query($db))->setQuery($sql);
        });

        // クエリ実行
        $result = $query->execute(
            $key,
            $iMail
        );
        
        $data = $result->getRow();
        if ($data == null){
            //対象のEメールアドレスが存在しない
            $ret = false;
        }
        else{
            $ret = true;
        }
 
        return $ret;
    }

    //利用者テーブルに登録されているか確認
    //（認証トークンで確認）
    public function findByToken($token)
    {
      // 暗号鍵取得
      $key = getenv("database.default.encryption.key");
      // クエリ生成
      $query = $this->db->prepare(static function ($db) 
      {
        $sql = "SELECT *, AES_DECRYPT(`email`, UNHEX(SHA2(?,512))) AS `username`, AES_DECRYPT(`password`, UNHEX(SHA2(?,512))) AS `personal`
                 FROM m_user WHERE token IS NOT NULL AND token = ?";
        return (new Query($db))->setQuery($sql);
      });
      // クエリ実行
      $result = $query->execute(
        $key,
        $key,
        $token
      );

      // レコード取得
      $row = $result->getRow();
      
      return $row && $row->token ? new UserEntity((array)$row) : new UserEntity();
    }
  
}