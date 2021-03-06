<?php

use Doctrine\ORM\Tools\Setup;
use Doctrine\ORM\EntityManager;

require_once __DIR__ . "/../vendor/autoload.php";

// Create a simple "default" Doctrine ORM configuration
$isDevMode = true;
$config = Setup::createXMLMetadataConfiguration(array(__DIR__."/../config/xml"), $isDevMode);

// database configuration parameters
$conn = array(
    'driver' => 'pdo_sqlite',
    'path' => __DIR__ . '/../var/data.db',
);

// obtaining the entity manager
$entityManager = EntityManager::create($conn, $config);
