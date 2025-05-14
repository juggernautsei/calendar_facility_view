<?php
/*
 *  package OpenEMR
 *  link    https://www.open-emr.org
 *  author  Sherwin Gaddis <sherwingaddis@gmail.com>
 *  Copyright (c) 2022.
 *  license https://github.com/openemr/openemr/blob/master/LICENSE GNU General Public License 3
 */

namespace Juggernaut\Text\Module\App\Exceptions;

class ViewNotFoundException extends \Exception
{

    public function __construct()
    {
        parent::__construct();
        ViewNotFoundException::__construct();
    }

    protected $message = 'View Not found';
}
