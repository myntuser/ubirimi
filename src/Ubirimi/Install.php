<?php

namespace Ubirimi;

use Composer\Script\Event;
use Ubirimi\Container\UbirimiContainer;

class Install {

    public static function install(Event $event) {
        $io = $event->getIO();

        $domain = $io->ask("Domain: ");
        $adminFirstName = $io->ask("Administrator First Name: ");
        $adminLastName = $io->ask("Administrator Last Name: ");
        $adminUsername = $io->ask("Administrator Username: ");
        $adminPassword = $io->ask("Administrator Password: ");
        $adminEmail = $io->ask("Administrator Password (again): ");
        $baseURL = $io->ask("Base URL (Ex: http://ubirimi.company.com): ");

        $cliendData = array(
            'companyDomain' => $domain,
            'adminFirstName' => $adminFirstName,
            'adminLastName' => $adminLastName,
            'adminUsername' => $adminUsername,
            'adminPass' => $adminPassword,
            'adminEmail' => $adminEmail,
            'baseURL' => $baseURL
        );

        UbirimiContainer::get()['client']->add($cliendData);

        exit;
    }
}