<?php namespace App\Entities;

use CodeIgniter\Entity\Entity;
use Firebase\JWT\JWT;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;
use App\Helpers\UtilHelper;
use App\Models\TemplatesModel;

class UserEntity extends Entity
{
  /** @var Array Attributes */
  protected $attributes = [
    "username" => null,
    "section" => null,
    "token" => null,
    "user_kbn" => null,
    "shopnm" => null,
    "rejistname" => null,
    "nicknm" => null,
    "dragSortOrder" => null,
    "createdByUserNum" => 1,
    "updatedByUserNum" => 1
  ];
  
  /** @var Array Dates */
  protected $dates = [
    "createdDate", 
    "updatedDate",
  ];
  
  
  public function createSignature(int $lifespan = null): String
  {
    // 署名ぺイロード生成
    $payload = [
      "data" => [
        "user" => (object)["token" => $this->token]
      ],
      "iat" => time(),
      "exp" => time() + ($lifespan ? $lifespan : 60*60*24*7) // 指定がなければ1週間の寿命を与える
    ];
    // 署名生成
    return JWT::encode($payload, getenv("jwt.secret.key"), getenv("jwt.signing.algorithm"));
  }
  
  /**
   * JsonSerializable
   * @todo JSONシリアライズ関数
   */
  public function jsonSerialize(): Array
  {
    return
    [
      "username" => $this->username,
      "section" => $this->user_kbn,
      "token" => $this->token,
      "rejistname" => $this->rejistname,
      "nickname" => $this->nicknm,
      "shopname" => $this->shopnm,
    ];
  }
  
  /**
   * 登録完了通知メール送信関数
   */
  public function sendThanksNotice(string $iPass): void
  {
    // TemplatesModel生成
    $templatesModel = new TemplatesModel();
    // Template取得
    $temlate = $templatesModel->where("num", 1)->first();
    
    // 言語、内部エンコーディングを指定
    mb_language("japanese");
    mb_internal_encoding("UTF-8");
    
    // PHPMailer
    $mailer = new PHPMailer(true);
    
    try 
    {
      require ROOTPATH . "vendor/autoload.php";
      require ROOTPATH . "vendor/phpmailer/phpmailer/language/phpmailer.lang-ja.php";
      
      $mailer->isSMTP();
      $mailer->SMTPAuth = true;
      $mailer->Host = getenv("smtp.default.hostname");
      $mailer->Username = getenv("smtp.default.username");
      $mailer->Password = getenv("smtp.default.password");
      $mailer->Port = intval(getenv("smtp.default.port"));
      $mailer->SMTPSecure = "tls";
      $mailer->CharSet = "utf-8";
      $mailer->Encoding = "base64";
      $mailer->setFrom(getenv("smtp.default.from"), "FUKUI BRAND FISH");
      $mailer->addAddress($this->username);
      $mailer->Subject = $temlate->user_thanks_notice_title; 
      ob_start();
      $mailer->Body = UtilHelper::Br2Nl($temlate->user_thanks_notice_content);
      $mailer->Body = str_replace("%登録名%", $this->rejistname, $mailer->Body);
      $mailer->Body = str_replace("%メールアドレス%", $this->username, $mailer->Body);
      $mailer->Body = str_replace("%パスワード%", $iPass, $mailer->Body);
      ob_clean();

      //$mailer->Subject = $temlate->user_complete_notice_title;
      //$mailer->Body = UtilHelper::Br2Nl($temlate->user_complete_notice_content);
      
      // 本番環境・ステージング環境のみ送信
/*      if (getenv("CI_ENVIRONMENT") === "production")
      {
        $mailer->send();
      }
*/
      $mailer->send();
    }
    catch (Exception $e)
    {
    }
  }

    /**
     * 登録メール連絡送信関数
     */
    public function SendReportNotify(string $iFishKind, string $iTitle): void
    {
        // TemplatesModel生成
        $templatesModel = new TemplatesModel();
        // Template取得
        $temlate = $templatesModel->where("num", 1)->first();

        // 言語、内部エンコーディングを指定
        mb_language("japanese");
        mb_internal_encoding("UTF-8");

        // PHPMailer
        $mailer = new PHPMailer(true);

        try{
            require ROOTPATH . "vendor/autoload.php";
            require ROOTPATH . "vendor/phpmailer/phpmailer/language/phpmailer.lang-ja.php";

            ob_start();
            $body = $temlate->rejist_notify_detail;
            $body = str_replace("%魚種%", UtilHelper::GetFishKindName($iFishKind), $body);
            $body = str_replace("%タイトル%", $iTitle, $body);
            $body = str_replace("%メール%", $this->username, $body);
            $body = str_replace("%登録名%", $this->rejistname, $body);
            ob_clean();

            $mailer->isSMTP();
            $mailer->SMTPAuth = true;
            $mailer->Host = getenv("smtp.default.hostname");
            $mailer->Username = getenv("smtp.default.username");
            $mailer->Password = getenv("smtp.default.password");
            $mailer->Port = intval(getenv("smtp.default.port"));
            $mailer->SMTPSecure = "tls";
            $mailer->CharSet = "utf-8";
            $mailer->Encoding = "base64";
            $mailer->setFrom(getenv("smtp.default.from"), "FUKUI BRAND FISH");
            $mailer->addAddress(getenv("smtp.default.from"));
            $mailer->Subject = $temlate->rejist_notify_title; 
            $mailer->Body = UtilHelper::Br2Nl($body);
 
            $mailer->send();      
        }
        catch (Exception $e)
        {
          
        }
    }
}