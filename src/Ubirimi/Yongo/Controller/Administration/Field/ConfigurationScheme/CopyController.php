<?php

/*
 *  Copyright (C) 2012-2014 SC Ubirimi SRL <info-copyright@ubirimi.com>
 *
 *  This program is free software; you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License version 2 as
 *  published by the Free Software Foundation.
 *
 *  This program is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  You should have received a copy of the GNU General Public License
 *  along with this program; if not, write to the Free Software
 *  Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA 02110-1301, USA.
 */

namespace Ubirimi\Yongo\Controller\Administration\Field\ConfigurationScheme;

use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Ubirimi\SystemProduct;
use Ubirimi\UbirimiController;
use Ubirimi\Util;
use Ubirimi\Yongo\Repository\Field\FieldConfigurationScheme;


class CopyController extends UbirimiController
{
    public function indexAction(Request $request, SessionInterface $session)
    {
        Util::checkUserIsLoggedInAndRedirect();

        $fieldConfigurationSchemeId = $request->get('id');
        $fieldConfigurationScheme = $this->getRepository(FieldConfigurationScheme::class)->getMetaDataById($fieldConfigurationSchemeId);

        if ($fieldConfigurationScheme['client_id'] != $session->get('client/id')) {
            return new RedirectResponse('/general-settings/bad-link-access-denied');
        }

        $emptyName = false;
        $duplicateName = false;

        if ($request->request->has('copy_field_configuration_scheme')) {
            $name = Util::cleanRegularInputField($request->request->get('name'));
            $description = Util::cleanRegularInputField($request->request->get('description'));

            if (empty($name)) {
                $emptyName = true;
            }

            $duplicateFieldConfigurationScheme = $this->getRepository(FieldConfigurationScheme::class)->getMetaDataByNameAndClientId(
                $session->get('client/id'),
                mb_strtolower($name)
            );

            if ($duplicateFieldConfigurationScheme)
                $duplicateName = true;

            if (!$emptyName && !$duplicateName) {
                $currentDate = Util::getServerCurrentDateTime();

                $copiedFieldConfigurationScheme = new FieldConfigurationScheme(
                    $session->get('client/id'),
                    $name,
                    $description
                );

                $copiedFieldConfigurationSchemeId = $copiedFieldConfigurationScheme->save($currentDate);

                $fieldConfigurationSchemeData = $this->getRepository(FieldConfigurationScheme::class)->getDataByFieldConfigurationSchemeId(
                    $fieldConfigurationSchemeId
                );

                while ($fieldConfigurationSchemeData && $data = $fieldConfigurationSchemeData->fetch_array(MYSQLI_ASSOC)) {
                    $copiedFieldConfigurationScheme->addData(
                        $copiedFieldConfigurationSchemeId,
                        $data['field_configuration_id'],
                        $data['issue_type_id'],
                        $currentDate
                    );
                }

                $this->getLogger()->addInfo('Copy Yongo Field Configuration Scheme ' . $fieldConfigurationScheme['name'], $this->getLoggerContext());

                return new RedirectResponse('/yongo/administration/field-configurations/schemes');
            }
        }

        $menuSelectedCategory = 'issue';

        $sectionPageTitle = $session->get('client/settings/title_name') . ' / ' . SystemProduct::SYS_PRODUCT_YONGO_NAME . ' / Copy Field Configuration Scheme';

        return $this->render(__DIR__ . '/../../../../Resources/views/administration/field/configuration_scheme/Copy.php', get_defined_vars());
    }
}
