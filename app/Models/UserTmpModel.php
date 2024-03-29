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
    protected $table = 'cmsb_t_usertmp';

    //更新対象フィールド
    protected $allowedFields = ['email','token','authcode'];

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

    //仮登録テーブルに登録されているか確認
    //（認証トークンで確認）
    public function findByToken(string $token): PreflightEntity
    {
        // 暗号鍵取得
        $key = getenv("database.default.encryption.key");
        // クエリ生成
        $query = $this->db->prepare(static function ($db) 
        {
            $sql = "SELECT *, AES_DECRYPT(`email`, UNHEX(SHA2(?,512))) AS `email` FROM cmsb_t_usertmp WHERE token IS NOT NULL AND token = ?";
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
        // 暗号鍵取得
        $key = getenv("database.default.encryption.key");

        // クエリ生成
        $query = $this->db->prepare(static function ($db) 
        {
            $sql = "SELECT AES_DECRYPT(`email`, UNHEX(SHA2(?,512))) as eml FROM cmsb_t_usertmp HAVING eml IS NOT NULL AND eml = ?";
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
            $sql = "INSERT INTO cmsb_t_usertmp ( `token`, `email`, `authcode`, `title`) VALUES
                                             ( ?, AES_ENCRYPT(?, UNHEX(SHA2(?,512))), ?, ?)";
            return (new Query($db))->setQuery($sql);
        });
    
        // クエリ実行
        $result = $query->execute(
            $iToken,
            $iMailAdr,
            $key,
            password_hash($iAuthCode, PASSWORD_DEFAULT),
            "*"
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
            $sql = "SELECT AES_DECRYPT(`email`, UNHEX(SHA2(?,512))) as eml, rejist_flg FROM cmsb_t_usertmp WHERE token = ?";
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
}

