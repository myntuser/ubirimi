<?php
    use Ubirimi\Repository\HelpDesk\Organization;
    use Ubirimi\Repository\Log;
    use Ubirimi\SystemProduct;
    use Ubirimi\Util;

    Util::checkUserIsLoggedInAndRedirect();

    $emptyName = false;
    $status_exists = false;

    if (isset($_POST['new_organization'])) {
        $name = Util::cleanRegularInputField($_POST['name']);
        $description = Util::cleanRegularInputField($_POST['description']);

        if (empty($name))
            $emptyName = true;

        $organization = Organization::getByName($clientId, mb_strtolower($name));

        if ($organization)
            $status_exists = true;

        if (!$emptyName && !$status_exists) {
            $currentDate = Util::getCurrentDateTime($session->get('client/settings/timezone'));
            Organization::create($clientId, $name, $currentDate);

            Log::add($clientId, SystemProduct::SYS_PRODUCT_HELP_DESK, $loggedInUserId, 'ADD Organization ' . $name, $currentDate);

            header('Location: /helpdesk/administration/organizations');
        }
    }

    $menuSelectedCategory = 'helpdesk_organizations';
    $sectionPageTitle = $session->get('client/settings/title_name') . ' / ' . SystemProduct::SYS_PRODUCT_HELP_DESK_NAME. ' / Create Organization';

    require_once __DIR__ . '/../../../Resources/views/administration/organization/AddOrganization.php';