<?php

namespace Juggernaut\Module\HealthScribe\Controllers;

class AwsCredentialManager
{
    public function areCredentialsSaved(): bool|array|null
    {
        $sql = "SELECT * FROM `module_aws_healthscribe_settings`";
        return sqlQuery($sql);
    }
}
