<?php

namespace Ubirimi;

use Composer\Script\Event;
use Ubirimi\Container\UbirimiContainer;
use Ubirimi\Service\ClientService;
use Ubirimi\Service\ConfigService;
use Ubirimi\Service\DatabaseConnectorService;
use Ubirimi\ServiceProvider\UbirimiCoreServiceProvider;

class Install {

    public static function install(Event $event) {

        /* parse .properties file and make them available in the container */
        $configsApplication = ConfigService::process(__DIR__ . '/../app/config/app.properties');
        $configsDatabase = ConfigService::process(__DIR__ . '/../app/config/db.properties');

        $configs = array_merge($configsApplication, $configsDatabase);

        /* register global configs to the container */
        UbirimiContainer::loadConfigs($configs);
        UbirimiContainer::register(new UbirimiCoreServiceProvider());

        $io = $event->getIO();

        $adminFirstName = $io->ask("Administrator First Name: ");
        $adminLastName = $io->ask("Administrator Last Name: ");
        $adminUsername = $io->ask("Administrator Username: ");
        $adminPassword = $io->ask("Administrator Password: ");
        $adminEmail = $io->ask("Administrator Password (again): ");
        $baseURL = $io->ask("Base URL (Ex: http://ubirimi.company.com): ");

        $clientData = array('data' => json_encode(array(
            'adminFirstName' => $adminFirstName,
            'adminLastName' => $adminLastName,
            'adminUsername' => $adminUsername,
            'adminPass' => $adminPassword,
            'adminEmail' => $adminEmail,
            'baseURL' => $baseURL
        )));

        UbirimiContainer::get()['client']->add($clientData);

        exit;
    }
}