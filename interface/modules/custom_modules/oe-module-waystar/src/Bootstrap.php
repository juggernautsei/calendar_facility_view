<?php




namespace Juggernaut\OpenEMR\Modules\WayStar;

use OpenEMR\Core\Kernel;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class Bootstrap
{
    const MODULE_INSTALLATION_PATH = "/interface/modules/custom_modules/";

    /**
     * @var EventDispatcherInterface The object responsible for sending and subscribing to events through the OpenEMR system
     */
    private EventDispatcherInterface $eventDispatcher;

    /**
     * @param EventDispatcher $eventDispatcher
     * @param mixed $kernel
     */
    public function __construct(EventDispatcher $dispatcher, ?Kernel $kernel = null)
    {
        if (empty($kernel)) {
            $kernel = new Kernel();
        }
        $this->eventDispatcher = $dispatcher;
    }

    public function subscribeToEvents()
    {
    }
}
