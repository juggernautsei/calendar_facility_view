<?php

/**
 *  package OpenEMR
 *  link    https://www.open-emr.org
 *  author  Sherwin Gaddis <sherwingaddis@gmail.com>
 *  Copyright (c) 2023.
 *  All Rights Reserved
 */
namespace Juggernaut\Text\Module\App\Controllers;

use OpenEMR\Events\PatientDemographics\RenderEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class NavBarSubscriber implements EventSubscriberInterface
{
    public static function getSubscribedEvents(): array
    {
        return [
            RenderEvent::EVENT_RENDER_POST_PAGELOAD => 'renderButtonDexcomPostLoad'
        ];
    }

    public function renderButtonDexcomPostLoad(): void
    {
        ?>
        <script>
            let navbar2 = document.querySelector('#myNavbar');
            let ele2 = document.createElement("div");
            ele2.id = "customepatientnav2";
            ele2.innerHTML = "<button class='btn btn-success ml-2 mr-2' id='dexcomreadings'><?php echo xlt("Dexcom") ?></button>";
            navbar2.appendChild(ele2);

            document
                .getElementById('dexcomreadings')
                .addEventListener('click', function (e){
                    e.preventDefault();
                    dexcomReadings();
                });

            function dexcomReadings(){
                let url = '/interface/modules/custom_modules/oe-module-dexcom/public/index.php';
                top.restoreSession();
                window.open(
                    url
                );
                return false;
            }
        </script>
        <?php
    }
}


