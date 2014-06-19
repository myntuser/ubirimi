<?php

namespace Ubirimi\FrontendCOM\Controller\Account;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Ubirimi\UbirimiController;
use Ubirimi\Repository\Client;
use Ubirimi\Util;

class ProfileSaveController extends UbirimiController
{
    public function indexAction(Request $request, SessionInterface $session)
    {
        Util::checkUserIsLoggedInAndRedirect();

        $countries = Util::getCountries();

        $errors = array(
            'empty_company_name' => false,
            'empty_contact_email' => false,
            'contact_email_not_valid' => false,
            'contact_email_already_exists' => false,
            'empty_address_1' => false,
            'empty_city' => false,
            'empty_district' => false
        );

        $companyName = Util::cleanRegularInputField($_POST['company_name']);
        $contactEmail = Util::cleanRegularInputField($_POST['contact_email']);
        $address1 = Util::cleanRegularInputField($_POST['address_1']);
        $address2 = Util::cleanRegularInputField($_POST['address_2']);
        $city = Util::cleanRegularInputField($_POST['city']);
        $district = Util::cleanRegularInputField($_POST['district']);
        $country = Util::cleanRegularInputField($_POST['country']);

        if (empty($companyName)) {
            $errors['empty_company_name'] = true;
        }

        if (empty($contactEmail)) {
            $errors['empty_contact_email'] = true;
        }
        else if (!Util::isValidEmail($contactEmail)) {
            $errors['contact_email_not_valid'] = true;
        }

        if (empty($address1)) {
            $errors['empty_address_1'] = true;
        }

        if (empty($city)) {
            $errors['empty_city'] = true;
        }

        if (empty($district)) {
            $errors['empty_district'] = true;
        }

        if (Util::hasNoErrors($errors)) {
            Client::updateById($session->get('client/id'),
                $companyName,
                $address1,
                $address2,
                $city,
                $district,
                $contactEmail);

            $session->set('profile_updated', true);
            $clientData = Client::getById($session->get('client/id'));
        }
        else {
            $session->remove('profile_updated');
        }

        return $this->render(__DIR__ . '/../../Resources/views/account/_profileForm.php', get_defined_vars());
    }
}