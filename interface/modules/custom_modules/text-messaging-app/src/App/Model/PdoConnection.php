<?php

/*
 * package   OpenEMR
 * link      https://open-emr.org
 * author    Sherwin Gaddis <sherwingaddis@gmail.com>
 * Copyright (c) 2024.  Sherwin Gaddis <sherwingaddis@gmail.com>
 */

namespace Juggernaut\Text\Module\App\Model;

use PDO;
use PDOException;

class PdoConnection
{
    /**
     * @var PdoConnection|null
     * Singleton instance
     * The purpose of this PDO is for inbound text messages. The inbound message for multisites need to be able to connect to the correct database.
     * This is a singleton class to ensure that only one connection is made to the correct database.
     * The webhook has the site ID in the URL. This class will use that site ID to connect to the correct database.
     */
    private static ?PdoConnection $instance = null; // Singleton instance
    private ?PDO $pdo = null;
    public string $site;

    // Private constructor to prevent direct instantiation
    private function __construct()
    {
    }

    // Public static method to get the singleton instance
    public static function getInstance(): PdoConnection
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    // Retrieve the database name
    public function retrieveDb(): string
    {
        global $sqlconf;
        require_once dirname(__FILE__, 8) . "/sites/" . $this->site . "/sqlconf.php";
        return text($sqlconf["dbase"]);
    }

    // Get the PDO connection
    public function pdoConn(): PDO
    {
        if ($this->pdo === null) {
            global $sqlconf;
            require_once dirname(__FILE__, 8) . "/sites/" . $this->site . "/sqlconf.php";

            try {
                $this->pdo = new PDO(
                    "mysql:host=localhost;dbname={$sqlconf['dbase']}",
                    $sqlconf["login"],
                    $sqlconf['pass'],
                    [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION] // Enable exceptions for errors
                );
            } catch (PDOException $e) {
                error_log('Connection failed: ' . $e->getMessage());
                throw $e; // Re-throw exception if necessary
            }
        }

        return $this->pdo;
    }

    // Private clone method to prevent cloning
    private function __clone()
    {
    }

    // Private unserialize method to prevent unserializing
    public function __wakeup()
    {
    }
}
