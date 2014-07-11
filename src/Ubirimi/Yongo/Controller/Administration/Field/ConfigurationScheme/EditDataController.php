<?php

namespace Ubirimi\Yongo\Controller\Administration\Field\ConfigurationScheme;

use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Ubirimi\SystemProduct;
use Ubirimi\UbirimiController;
use Ubirimi\Util;
use Ubirimi\Yongo\Repository\Field\FieldConfigurationScheme;
use Ubirimi\Repository\Log;
use Ubirimi\Yongo\Repository\Field\FieldConfiguration;

class EditDataController extends UbirimiController
{
    public function indexAction(Request $request, SessionInterface $session)
    {
        Util::checkUserIsLoggedInAndRedirect();

        $fieldConfigurationSchemeDataId = $request->get('id');
        $fieldConfigurations = FieldConfiguration::getByClientId($session->get('client/id'));
        $fieldConfigurationSchemeData = FieldConfigurationScheme::getDataById($fieldConfigurationSchemeDataId);

        $fieldConfigurationSchemeId = $fieldConfigurationSchemeData['issue_type_field_configuration_id'];
        $fieldConfigurationScheme = FieldConfigurationScheme::getMetaDataById($fieldConfigurationSchemeId);

        if ($fieldConfigurationScheme['client_id'] != $session->get('client/id')) {
            return new RedirectResponse('/general-settings/bad-link-access-denied');
        }

        if ($request->request->has('edit_field_configuration_scheme_data')) {
            $fieldConfigurationId = Util::cleanRegularInputField($request->request->get('field_configuration'));
            $issueTypeId = Util::cleanRegularInputField($request->request->get('issue_type'));

            FieldConfigurationScheme::updateDataById(
                $fieldConfigurationId,
                $fieldConfigurationSchemeId,
                $issueTypeId
            );

            $currentDate = Util::getServerCurrentDateTime();

            Log::add(
                $session->get('client/id'),
                SystemProduct::SYS_PRODUCT_YONGO,
                $session->get('user/id'),
                'UPDATE Yongo Field Configuration Scheme ' . $fieldConfigurationScheme['name'],
                $currentDate
            );

            return new RedirectResponse('/yongo/administration/field-configuration/scheme/edit/' . $fieldConfigurationSchemeId);
        }

        $menuSelectedCategory = 'issue';

        $sectionPageTitle = $session->get('client/settings/title_name') . ' / ' . SystemProduct::SYS_PRODUCT_YONGO_NAME . ' / Update Field Configuration';

        return $this->render(__DIR__ . '/../../../../Resources/views/administration/field/configuration_scheme/EditData.php', get_defined_vars());
    }
}
