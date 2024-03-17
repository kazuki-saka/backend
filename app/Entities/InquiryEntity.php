<?php namespace App\Entities;

use CodeIgniter\Entity\Entity;
use Firebase\JWT\JWT;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;
use App\Helpers\UtilHelper;
use App\Models\TemplatesModel;

//問い合わせ用エントリー
class InquiryEntity extends Entity
{
    // ++++++++++ メンバー ++++++++++


    // ++++++++++ メソッド ++++++++++

    /**
     * JsonSerializable
     * @todo JSONシリアライズ関数
     */
    public function jsonSerialize(): Array
    {
      return
      [
        "shopname" => $this->shopname,
        "rejistname" => $this->rejistname,
        "postcode" => $this->postcode,
        "adsress" => $this->adsress,
        "detail" => $this->detail,
        "fishkind" => $this->fishkind
      ];
    }

    /**
     * 問い合わせメール送信関数
     */
    public function SendInquiry(): void
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
            $body = $temlate->inquiry_content;
            $body = str_replace("%魚種%", $this->fishkind, $body);
            $body = str_replace("%店名%", $this->shopname, $body);
            $body = str_replace("%登録名%", $this->rejistname, $body);
            $body = str_replace("%郵便番号%", $this->postcode, $body);
            $body = str_replace("%住所%", $this->address, $body);
            $body = str_replace("%問い合わせ内容%", $this->detail, $body);
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
            $mailer->addAddress(getenv("smtp.default.from"));    //宛先未定。仮に自分から自分へ送信
            $mailer->Subject = $temlate->inquiry_title; 
            $mailer->Body = UtilHelper::Br2Nl($body);

            $mailer->send();      
        }
        catch (Exception $e)
        {
          
        }

    }

}