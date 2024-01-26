<?php

namespace App\Models;

use CodeIgniter\Model;
use CodeIgniter\Database\Query;
use App\Entities\PreflightEntity;

//仮登録テーブル制御クラス
class UserTmpModel extends Model
{
    // ++++++++++ メンバー ++++++++++

    protected $db;

    //テーブル名
    protected $table = 'm_user_tmp';

    //更新対象フィールド
    protected $allowedFields = ['email','token','authcode','rejist_flg'];

    protected $primaryKey = "num";

    //暗号化キー
    protected $key;
    protected $skipValidation = false;
    protected $useSoftDeletes = false;
    protected $useTimestamps = true;

    protected $returnType = "App\Entities\PreflightEntity";

    // ++++++++++ メソッド ++++++++++

    //コンストラクタ
/*
    function __construct()
    {
        $key = getenv("database.default.encryption.key");
    }
*/

public function findByToken(string $token): PreflightEntity
{
    // 暗号鍵取得
    $key = getenv("database.default.encryption.key");
    // クエリ生成
    $query = $this->db->prepare(static function ($db) 
    {
        $sql = "SELECT *, AES_DECRYPT(`email`, UNHEX(SHA2(?,512))) AS `email` FROM m_user_tmp WHERE token IS NOT NULL AND token = ?";
        return (new Query($db))->setQuery($sql);
    });
    // クエリ実行
    $result = $query->execute(
        $key,
        $token
    );
    // レコード取得
    $row = $result->getRow();
    
    return $row && $row->num ? new PreflightEntity((array)$row) : new PreflightEntity();
}

    //Eメールアドレスから情報取得
    public function getUserTmp($iMail = null)
    {
/*
        if ($iMail == null){
            return $this->findAll();
        }

        return $this->where(['email' => $iMail])->first();
*/

        // 暗号鍵取得
        $key = getenv("database.default.encryption.key");

        // クエリ生成
        $query = $this->db->prepare(static function ($db) 
        {
            $sql = "SELECT AES_DECRYPT(`email`, UNHEX(SHA2(?,512))) as eml, rejist_flg as rflg FROM m_user_tmp HAVING eml IS NOT NULL AND eml = ? AND rflg = 1";
            return (new Query($db))->setQuery($sql);
        });

        // クエリ実行
        $result = $query->execute(
            $key,
            $iMail
        );

        $data = $result->getRow();
        if ($data == null){
            $flg = 0;
        }
        else {
            $flg = 1;
        }
        return $flg;
    }

    //仮登録テーブルへの追加
    public function AddUserTmp($iToken, $iMailAdr, $iAuthCode)
    {
        //$data = ['token' => $iToken, 'email' => $iMailAdr, 'rejist_flg' => '0'];
        //-----------------------------------------------
        
        //return $this->insert($data);

        $key = getenv("database.default.encryption.key");
        //-----------------------------------------------

        // クエリ生成        
        $query = $this->db->prepare(static function ($db) 
        {
            $sql = "INSERT INTO m_user_tmp ( `token`, `email`, `authcode`, `rejist_flg`) VALUES
                                             ( ?, AES_ENCRYPT(?, UNHEX(SHA2(?,512))), ?, ?)";
            return (new Query($db))->setQuery($sql);
        });
    
        // クエリ実行
        $result = $query->execute(
            $iToken,
            $iMailAdr,
            $key,
            password_hash($iAuthCode, PASSWORD_DEFAULT),
            0
        );

        return $result;
    }

    //トークンから情報取得
    public function GetUserMail($iToken)
    {
/*
        if ($iToken == null){
            return $this->findAll();
        }

        return $this->where(['token' => $iToken])->first();
*/
        $email = "";
        $data = [];
        //-----------------------------------------------

        // 暗号鍵取得
        $key = getenv("database.default.encryption.key");

        // クエリ生成
        $query = $this->db->prepare(static function ($db) 
        {
            $sql = "SELECT AES_DECRYPT(`email`, UNHEX(SHA2(?,512))) as eml, rejist_flg FROM m_user_tmp WHERE token = ?";
            return (new Query($db))->setQuery($sql);
        });

        // クエリ実行
        $result = $query->execute(
            $key,
            $iToken
        );

        $data['result'] = 0;
        foreach ($result->getResult() as $row){
            $data['email'] = $row->eml;
            $data['regist_flg'] = $row->rejist_flg;
            $data['result'] = 1;
        }

        return $data;
    }

    //本登録済みフラグをONにする
    public function UpdateRegistFlg($iToken)
    {
        $where = [];
        $data = [];
        //-----------------------------------------------

        $where = [ 'token' => $iToken];
        $data = ['regist_flg' => 1];
        //$this->db->update($where, $data);

        // クエリ生成
        $query = $this->db->prepare(static function ($db) 
        {
            $sql = "UPDATE m_user_tmp SET rejist_flg = 1 WHERE token = ?";

            //$sql = "SELECT AES_DECRYPT(`email`, UNHEX(SHA2(?,512))) as eml, rejist_flg FROM m_user_tmp WHERE token = ?";
            return (new Query($db))->setQuery($sql);
        });

        // クエリ実行
        $result = $query->execute(
            $iToken
        );
        
        //$this->db->where('token', $iToken);
        //$this->db->update('m_user_tmp', 2); 

        return $result;
    }
}

