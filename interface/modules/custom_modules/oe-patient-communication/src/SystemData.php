<?php

namespace Juggernaut\Module\BulkEmail;

class SystemData
{
   public function countEmails()
   {
      $sql = "SELECT count(*) as addresses FROM `patient_data` WHERE email != '' OR email != null";
      $result = sqlQuery($sql);
      return $result['addresses'];
   }

}
