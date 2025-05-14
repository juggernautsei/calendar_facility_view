<?php

namespace Juggernaut\Module\KPI;

use Twig\Environment;
use Twig\Loader\FilesystemLoader;
use OpenEMR\Common\Twig\TwigContainer;
use OpenEMR\Common\Twig\TwigExtension;
use OpenEMR\Events\Core\TwigEnvironmentEvent;
use OpenEMR\Core\Kernel;
use OpenEMR\Menu\MenuEvent;
use OpenEMR\Services\Globals\GlobalsService;
use OpenEMR\Services\Utils\DateFormatterUtils;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class Bootstrap
{
    const MODULE_INSTALLATION_PATH = "/interface/modules/custom_modules/";
    /**
     * @var EventDispatcherInterface The object responsible for sending and subscribing to events through the OpenEMR system
     */
    private EventDispatcher $eventDispatcher;
    private Kernel $kernel;
    private Environment $twig;

    public function __construct(EventDispatcher $eventDispatcher, ?Kernel $kernel = null)
    {
        if ($kernel) {
            $this->kernel = $kernel;
        }
        $this->eventDispatcher = $eventDispatcher;
        $twig = new TwigContainer($this->getTemplatePath(), $kernel);
        $twigEnv = $twig->getTwig();
        $this->twig = $twigEnv;
    }

    public function subscribeToEvents()
    {
        $this->eventDispatcher->addListener(MenuEvent::MENU_UPDATE, [$this, 'addCustomModuleMenuItem']);
    }

    public function registerMenuItems()
    {
        $this->eventDispatcher->addListener(TwigEnvironmentEvent::EVENT_CREATED, [$this, 'addTemplateOverrideLoader']);
        $this->eventDispatcher->addListener(MenuEvent::class, [$this, 'addCustomModuleMenuItem']);
    }
    private function getTemplatePath()
    {
        return \dirname(__DIR__) . DIRECTORY_SEPARATOR . "templates" . DIRECTORY_SEPARATOR;
    }

    /**
     * @return Environment
     */
    public function getTwig(): Environment
    {
        $twigLoader = new FilesystemLoader($this->paths);
        $twigEnv = new Environment($twigLoader, ['autoescape' => false]);
        $globalsService = new GlobalsService($GLOBALS, [], []);
        $twigEnv->addExtension(new TwigExtension($globalsService, $this->kernel));

        $coreExtension = $twigEnv->getExtension(CoreExtension::class);
        // set our default date() twig render function if no format is specified
        // we set our default date format to be the localized version of our dates and our time formats
        // by default Twig uses 'F j, Y H:i' for the format which doesn't match our OpenEMR dates as configured from the globals
        $dateFormat = DateFormatterUtils::getShortDateFormat() . " " . DateFormatterUtils::getTimeFormat();
        $coreExtension->setDateFormat($dateFormat);

        if ($this->kernel) {
            if ($this->kernel->isDev()) {
                $twigEnv->addExtension(new DebugExtension());
                $twigEnv->enableDebug();
            }
            $event = new TwigEnvironmentEvent($twigEnv);
            $this->kernel->getEventDispatcher()->dispatch($event, TwigEnvironmentEvent::EVENT_CREATED, 10);
        }

        return $twigEnv;
    }

    public function addTemplateOverrideLoader(TwigEnvironmentEvent $event)
    {
        $twig = $event->getTwigEnvironment();
        if ($twig === $this->twig) {
            // we do nothing if its our own twig environment instantiated that we already setup
            return;
        }
        // we make sure we can override our file system directory here.
        $loader = $twig->getLoader();
        if ($loader instanceof FilesystemLoader) {
            $loader->prependPath($this->getTemplatePath());
        }
    }

    public function addCustomModuleMenuItem(MenuEvent $event): MenuEvent
    {
        $menu = $event->getMenu();

        $menuItem = new \stdClass();
        $menuItem->requirement = 0;
        $menuItem->target = 'repimg';
        $menuItem->menu_id = 'kpi0';
        $menuItem->label = xlt("KPI Report");
        $menuItem->url = "/interface/modules/custom_modules/oe-kpi-report/public/index.php";
        $menuItem->children = [];

        /**
         * This defines the Access Control List properties that are required to use this module.
         * Several examples are provided
         */
        $menuItem->acl_req = [];

        /**
         * If you want your menu item to allows be shown then leave this property blank.
         */
        $menuItem->global_req = [];

        foreach ($menu as $item) {
            if ($item->menu_id == 'repimg') {
                $item->children[] = $menuItem;
                break;
            }
        }

        $event->setMenu($menu);

        return $event;
    }
}
