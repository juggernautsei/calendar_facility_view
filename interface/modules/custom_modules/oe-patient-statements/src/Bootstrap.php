<?php

/*
 *   @package   OpenEMR
 *   @link      http://www.open-emr.org
 *
 *   @author    Sherwin Gaddis <sherwingaddis@gmail.com>
 *   Copyright (c)  Juggernaut Systems Express
 *   @license   https://github.com/openemr/openemr/blob/master/LICENSE GNU General Public License 3
 *
 */

namespace Juggernaut\Module\PatientStatements;

use OpenEMR\Core\Kernel;
use Symfony\Component\EventDispatcher\EventDispatcher;

class Bootstrap
{
    const MODULE_INSTALLATION_PATH = "/interface/modules/custom_modules/";
    private EventDispatcher $eventDispatcher;
    private Kernel $kernel;

    public function __construct(EventDispatcher $eventDispatcher, ?Kernel $kernel = null )
    {
        if ($kernel) {
            $this->kernel = $kernel;
        }
        $this->eventDispatcher = $eventDispatcher;
    }

    public function subscribeToEvents()
    {
    }
}
