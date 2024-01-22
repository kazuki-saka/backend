<?php

namespace App\Models;

use CodeIgniter\Model;
use CodeIgniter\Database\Query;

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
                        AES_ENCRYPT(?, UNHEX(SHA2(?,512))),
                        ?,
                        AES_ENCRYPT(?, UNHEX(SHA2(?,512))),
                        AES_ENCRYPT(?, UNHEX(SHA2(?,512))),
                        AES_ENCRYPT(?, UNHEX(SHA2(?,512))))";
            return (new Query($db))->setQuery($sql);
        });
     
        // クエリ実行
        $result = $query->execute(
            $iData['email'],
            $key,
            $iData['pass'],
            $key,
            $iData['token'],
            $iData['section'],
            $key,
            $iData['name'],
            $key,
            $iData['tel'],
            $key
        );
        
        return $result;
    }

    //利用者テーブルに登録されているか確認
    public function ChkUser($iMail, $iPass)
    {
        // 暗号鍵取得
        $key = getenv("database.default.encryption.key");
        
        // クエリ生成
        $query = $this->db->prepare(static function ($db) 
        {
            $sql = "SELECT AES_DECRYPT(`email`, UNHEX(SHA2(?,512))) as eml, AES_DECRYPT(`password`, UNHEX(SHA2(?,512))) as pass, token as tok FROM m_user
                HAVING eml = ? AND pass = ?";
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
        }
 
        return $result1;
    }
}