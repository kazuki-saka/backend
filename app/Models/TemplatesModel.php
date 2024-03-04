<?php namespace App\Models;

use CodeIgniter\Model;

class TemplatesModel extends Model
{
  protected $db;
  protected $table = "cmsb_m_mailtemplates";
  protected $primaryKey = "num";
  protected $returnType = "App\Entities\TemplateEntity";
  protected $allowedFields = [
    "preflight_authcode_notice_title",
    "preflight_authcode_notice_content",
    "user_complete_notice_title",
    "user_complete_notice_content",
    "inquiry_title",
    "inquiry_detail",
    "title",
  ];
  protected $skipValidation = false;
  protected $useSoftDeletes = false;
  protected $useTimestamps = true;
  protected $createdField  = "createdDate";
  protected $updatedField  = "updatedDate";
}