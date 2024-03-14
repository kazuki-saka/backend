<?php

namespace App\Models;

use CodeIgniter\Model;
use CodeIgniter\Database\Query;
use App\Entities\UserEntity;

//記事Viewテーブル
class ReportViewModel extends Model
{
    // ++++++++++ メンバー ++++++++++

    protected $db;

    //テーブル名
    protected $table = 'cmsb_v_report';

    //暗号化キー
    protected $key;

    protected $returnType = 'array';

    // ++++++++++ メソッド ++++++++++

    //魚種単位で記事を取得（ビューテーブルの方を参照）
    public function GetKindData($iKind, $iMarketFlg)
    {
        // 暗号鍵取得
        $key = getenv("database.default.encryption.key");

/*        
        // クエリ生成
        $query = $this->db->prepare(static function ($db) 
        {
            $sql = "SELECT id, title, detail_m, AES_DECRYPT(`nickname`, UNHEX(SHA2(?,512))) AS nickname, updatedDate 
                FROM cmsb_v_report WHERE fishkind = ?";
            return (new Query($db))->setQuery($sql);
        });

        // クエリ実行
        $result = $query->execute(
            $key,
            $iKind
        );

        foreach ($result->getResult() as $row){
            array_push($data, $row);
        }
*/
        $data = [];
        if ($iMarketFlg == true){
            //市場関係者の記事
            $result = $this->where(['fishkind' => $iKind, 'DeployFlg' => 1, 'report_kbn' => 2])->findAll();
        }
        else{
            //生産者の記事
            $result = $this->where(['fishkind' => $iKind, 'DeployFlg' => 1, 'report_kbn' => 1])->findAll();
        }

        //$result = $this->findAll();
        
        foreach ($result as $row){
            //ユーザー情報読込
            $user = $this->GetNickName($row["token"]);

            if ($user){
                $tmp["id"] = $row["id"];
                $tmp["title"] = $row["title"];
                $tmp["detail_m"] = $row["detail_m"];
                $tmp["nickname"] = $user->nickname;
                $tmp["updatedDate"] = $row["updatedDate"];
                $tmp["like_cnt"] = $row["like_cnt"] ? $row["like_cnt"] :0;
                $tmp["comment_cnt"] = $row["comment_cnt"] ? $row["comment_cnt"] :0;
                $tmp["like_flg"] = false;
                $tmp["comment_flg"] = false;
                if ($row["filePath"] == null){
                    $tmp["imgPath"] = null;
                }
                else{
                    $tmp["imgPath"] = "uploads/report/after/" . $row["filePath"];
                }
                
                array_push($data, $tmp);
            }
        }

        return $data;
    }

    //記事の最新20件分をトピックスとして取得
    public function GetTopics($iKind = null, $iLimit = 20)
    {
        // 暗号鍵取得
        $key = getenv("database.default.encryption.key");

        $data = [];

        if ($iKind != null){
            // クエリ生成
            $query = $this->db->prepare(static function ($db) 
            {
                $sql = "SELECT * FROM cmsb_v_report
                            WHERE fishkind = ? ORDER BY updatedDate DESC LIMIT ?";
                return (new Query($db))->setQuery($sql);
            });
            
            $result = $query->execute(
                $iKind,
                $iLimit
            );
        }
        else{
            // クエリ生成
            //$result = $this->where(['DeployFlg' => 1])->findAll()->orderBy("updatedDate","DESC")->limit(10);
            $result = $this->where(['DeployFlg' => 1])->findAll();

/*            
            $query = $this->db->prepare(static function ($db) 
            {
                $sql = "SELECT * FROM cmsb_v_report
                            ORDER BY updatedDate DESC LIMIT ?";
                return (new Query($db))->setQuery($sql);
            });
            
            $result = $query->execute(
                $iLimit
            );
*/
        }

/*
        foreach ($result->getResult() as $row){
            //ユーザー情報読込
            $user = $this->GetNickName($row->token);

            if ($user){
                $tmp["id"] = $row->id;
                $tmp["title"] = $row->title;
                $tmp["detail_m"] = $row->detail_m;
                $tmp["nickname"] = $user->nickname;
                $tmp["updatedDate"] = $row->updatedDate;
                $tmp["like_cnt"] = $row->like_cnt ? $row->like_cnt :0;
                $tmp["comment_cnt"] = $row->comment_cnt ? $row->comment_cnt :0;
                $tmp["like_flg"] = false;
                $tmp["comment_flg"] = false;
                if ($row->filePath == null){
                    $tmp["imgPath"] = null;
                }
                else{
                    $tmp["imgPath"] = "uploads/report/after/" . $row->filePath;
                }
                
                array_push($data, $tmp);

                if ($cnt >= $iLimit){
                    break;
                }
                $cnt = $cnt + 1;
            }
        }
*/
        $cnt = 0;
        foreach ($result as $row){
            //ユーザー情報読込
            $user = $this->GetNickName($row["token"]);

            if ($user){
                $tmp["id"] = $row["id"];
                $tmp["title"] = $row["title"];
                $tmp["detail_m"] = $row["detail_m"];
                $tmp["nickname"] = $user->nickname;
                $tmp["updatedDate"] = $row["updatedDate"];
                $tmp["like_cnt"] = $row["like_cnt"] ? $row["like_cnt"] :0;
                $tmp["comment_cnt"] = $row["comment_cnt"] ? $row["comment_cnt"] :0;
                $tmp["like_flg"] = false;
                $tmp["comment_flg"] = false;
                if ($row["filePath"] == null){
                    $tmp["imgPath"] = null;
                }
                else{
                    $tmp["imgPath"] = "/report/after/" . $row["filePath"];
                }
                
                array_push($data, $tmp);

                if ($cnt >= $iLimit){
                    break;
                }
                $cnt = $cnt + 1;
            }
        }

        return $data;
    }

    //利用者テーブルからニックネーム取得
    private function GetNickName($iToken)
    {
        // 暗号鍵取得
        $key = getenv("database.default.encryption.key");
   
        $query = $this->db->prepare(static function ($db) 
        {
            $sql = "SELECT token, user_kbn, AES_DECRYPT(`nickname`, UNHEX(SHA2(?,512))) AS nickname
                FROM cmsb_m_user WHERE token = ?";
            return (new Query($db))->setQuery($sql);
        });

        // クエリ実行
        $result = $query->execute(
            $key,
            $iToken
        );

        // レコード取得
        $row = $result->getRow();
        
        return $row;
        //return $row && $row->token ? new UserEntity((array)$row) : new UserEntity(); 
    }

    //記事ID単位で記事を取得（ビューテーブルの方を参照）
    public function GetIdData($iId)
    {
        // 暗号鍵取得
        $key = getenv("database.default.encryption.key");

        // クエリ生成
        $query = $this->db->prepare(static function ($db) 
        {
            $sql = "SELECT num AS id, title, detail_m, AES_DECRYPT(`nickname`, UNHEX(SHA2(?,512))) AS nickname, updatedDate FROM cmsb_v_report WHERE num = ?";
            return (new Query($db))->setQuery($sql);
        });

        // クエリ実行
        $result = $query->execute(
            $key,
            $iId
        );

        $data = $result->getResult();

        return $data;
    }
}