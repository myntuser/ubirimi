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
use Ubirimi\Answers\Repository\Domain\Domain;
use Ubirimi\SystemProduct;
use Ubirimi\Util;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Ubirimi\UbirimiController;

class AddController extends UbirimiController
{
    public function indexAction(Request $request, SessionInterface $session)
    {
        Util::checkUserIsLoggedInAndRedirect();

        $menuSelectedCategory = 'domains';
        $emptyName = false;

        if ($request->request->has('confirm_new_domain')) {
            $name = Util::cleanRegularInputField($request->request->get('name'));
            $description = Util::cleanRegularInputField($request->request->get('description'));

            if (empty($name)) {
                $emptyName = true;
            }
            if (!$emptyName) {
                $domain = new Domain($session->get('client/id'), $name, $description);
                $currentDate = Util::getServerCurrentDateTime();
                $domainId = $domain->save($session->get('user/id'), $currentDate);

                $this->getLogger()->addInfo('ADD Answers Domain ' . $name, $this->getLoggerContext());

                return new RedirectResponse('/answers/domains');
            }
        }

        $sectionPageTitle = $session->get('client/settings/title_name') . ' / ' . SystemProduct::SYS_PRODUCT_ANSWERS. ' / Create Domain';

        return $this->render(__DIR__ . '/../../Resources/views/domain/Add.php', get_defined_vars());
    }
}