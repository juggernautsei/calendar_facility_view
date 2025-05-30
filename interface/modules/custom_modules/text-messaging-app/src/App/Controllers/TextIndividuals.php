<?php
/*
 *  package OpenEMR
 *  link    https://www.open-emr.org
 *  author  Sherwin Gaddis <sherwingaddis@gmail.com>
 *  Copyright (c) 2022.
 *  license https://github.com/openemr/openemr/blob/master/LICENSE GNU General Public License 3
 */

namespace Juggernaut\Text\Module\App\Controllers;

use Juggernaut\Text\Module\App\Exceptions\ViewNotFoundException;
use Juggernaut\Text\Module\App\View;

class TextIndividuals
{
    /**
     * @throws ViewNotFoundException
     */
    public function index(): string
    {
        return (new View('individuals/index'))->render();
    }
}
