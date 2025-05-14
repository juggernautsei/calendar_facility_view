<?php

/*
 * @package OpenEMR
 *
 * @author Sherwin Gaddis <sherwingaddis@gmail.com>
 * Copyright (c) 2023.
 * @license "All rights reserved"
 */

namespace Juggernaut\Modules\Jitsi8x8Telehealth;

class BuildProviderProfile
{
    public function providerFetchDetails(): bool|array|null
    {
        return sqlQuery("SELECT `email`, `fname`, `lname` FROM `users` WHERE `id` = ?", [$_SESSION['authUserID']]);
    }
}
