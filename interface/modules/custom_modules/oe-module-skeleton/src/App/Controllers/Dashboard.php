<?php

/*
 * package   OpenEMR
 * link      http://www.open-emr.org
 * author    Sherwin Gaddis <sherwingaddis@gmail.com>
 * All rights reserved
 * Copyright (c) 2025.
 */

namespace Skeleton\Module\App\Controllers;

use OpenEMR\Common\Twig\TwigContainer;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;
class Dashboard
{
    private TwigContainer $twig;
    private $template;

    public function __construct()
    {
        $this->twig = new TwigContainer(dirname(__DIR__, 3) . '/templates', $GLOBALS['kernel']);
        $this->template = $this->twig->getTwig();
    }
    /**
     * Index page for the dashboard
     *
     * @return string The rendered Twig template as a string
     */
    public function index(): string
    {
        $data = [
            'current_page' => 'Home'
        ];
        return $this->template->render('dashboard/home.twig', $data);
    }
}
