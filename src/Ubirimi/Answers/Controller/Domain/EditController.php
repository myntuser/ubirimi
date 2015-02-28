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

namespace Ubirimi\Answers\Controller\Domain;

use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Ubirimi\Agile\Repository\Board\Board;
use Ubirimi\Answers\Repository\Domain\Domain;
use Ubirimi\Repository\General\UbirimiLog;
use Ubirimi\SystemProduct;
use Ubirimi\UbirimiController;
use Ubirimi\Util;
use Ubirimi\Yongo\Repository\Project\YongoProject;

class EditController extends UbirimiController
{
    public function indexAction(Request $request, SessionInterface $session)
    {
        Util::checkUserIsLoggedInAndRedirect();

        $menuSelectedCategory = 'domains';

        $domainId = $request->get('id');
        $domain = $this->getRepository(Domain::class)->getById($domainId);

        if ($domain['client_id'] != $session->get('client/id')) {
            return new RedirectResponse('/general-settings/bad-link-access-denied');
        }

        $emptyName = false;
        $domainExists = false;
        $domainName = $domain['name'];
        $domainDescription = $domain['description'];

        if ($request->request->has('confirm_edit_domain')) {
            $domainName = Util::cleanRegularInputField($request->request->get('name'));
            $domainDescription = Util::cleanRegularInputField($request->request->get('description'));

            if (empty($domainName)) {
                $emptyName = true;
            }

            if (!$emptyName) {

                $date = Util::getServerCurrentDateTime();
                $this->getRepository(Domain::class)->updateMetadata($session->get('client/id'), $domainId, $domainName, $domainDescription, $date);
                $this->getLogger()->addInfo('UPDATE Answers Domain ' . $domainName, $this->getLoggerContext());

                return new RedirectResponse('/answers/domains');
            }
        }

        $sectionPageTitle = $session->get('client/settings/title_name') . ' / ' . SystemProduct::SYS_PRODUCT_ANSWERS. ' / Update Domain';

        return $this->render(__DIR__ . '/../../Resources/views/domain/Edit.php', get_defined_vars());
    }
}
