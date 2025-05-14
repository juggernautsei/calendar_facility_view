<?php

/*
 * @package openemr
 * @link https://www.open-emr.org
 * @author Sherwin Gaddis <sherwingaddis@gmail.com>
 * @copyright (c) 2023
 * @license All Rights Reserved
 */

namespace OpenEMR\Modules\Clover;

use OpenEMR\Common\Logging\SystemLogger;
use OpenEMR\Core\Kernel;
use OpenEMR\Events\Billing\Payments\DeletePayment;
use OpenEMR\Events\Billing\Payments\PostFrontPayment;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class Bootstrap
{
    const CLOVER_URL = 'https://api.clover.com/';
    /**
     * @var EventDispatcherInterface The object responsible for sending and subscribing to events through the OpenEMR system
     */
    private $eventDispatcher;


    /**
     * @var SystemLogger
     */
    private SystemLogger $logger;

    public function __construct(EventDispatcher $dispatcher, ?Kernel $kernel = null)
    {
        if (empty($kernel)) {
            $kernel = new Kernel();
        }
        $this->eventDispatcher = $dispatcher;
    }

    public function subscribeToEvents(): void
    {
        $this->deletePatientPayment();
        $this->postFrontPayment();
    }

    public function deletePatientPayment(): void
    {
        $this->eventDispatcher->addListener(DeletePayment::ACTION_DELETE_PAYMENT, [$this, 'refundPayment']);
    }

    public function postFrontPayment(): void
    {
        $this->eventDispatcher->addListener(PostFrontPayment::ACTION_POST_FRONT_PAYMENT, [$this, 'postPaymentJavascript']);
    }

    public function refundPayment(DeletePayment $event): void
    {
        $paymentId = $event->getPaymentId();
        $deletePayment = new Controller\RefundPayment();
        $isCloverPayment = $deletePayment->checkIfCloverPayment($paymentId);
        if ($isCloverPayment) {
            $deletePayment->refundPayment($paymentId);
        }
    }

    public function postPaymentJavascript(PostFrontPayment $event): void
    {
        ?>
    <script>
        const buttonDiv = document.getElementById('button-group');
        const cloverButton = document.createElement('button');
        cloverButton.setAttribute('type', 'button');
        cloverButton.setAttribute('class', 'btn btn-warning');
        cloverButton.setAttribute('onclick', 'clover()');
        cloverButton.innerHTML = '<?php echo xla('Clover'); ?>';
        buttonDiv.appendChild(cloverButton);

        function clover() {
            let amount = $('input[name=form_paytotal]').val();
            let url = "/interface/modules/custom_modules/oe-module-clover/public/patientPaymentPage.php?amount=" + amount;
            dlgopen(url, "_blank", 800, 500, "", "Clover", {
                buttons: [
                    {text: '<?php echo xla('Close'); ?>', close: true, style: 'default btn-sm'},
                ],
                onClosed: 'refreshme',
                allowResize: true,
                allowDrag: true,
                type: 'iframe'
            });
        }
    </script>
<?php
    }
}
