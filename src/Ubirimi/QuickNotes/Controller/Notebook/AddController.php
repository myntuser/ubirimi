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

namespace Ubirimi\QuickNotes\Controller\Notebook;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Ubirimi\QuickNotes\Repository\Notebook;
use Ubirimi\UbirimiController;
use Ubirimi\Util;

class AddController extends UbirimiController
{
    public function indexAction(Request $request, SessionInterface $session)
    {
        Util::checkUserIsLoggedInAndRedirect();

        $emptyName = false;
        $duplicateName = false;

        $name = Util::cleanRegularInputField($request->request->get('name'));
        $description = Util::cleanRegularInputField($request->request->get('description'));

        if (empty($name)) {
            $emptyName = true;
        }

        $notebookSameName = $this->getRepository(Notebook::class)->getByName($session->get('user/id'), $name);
        if ($notebookSameName) {
            $duplicateName = true;
        }

        if (!$emptyName && !$duplicateName) {
            $currentDate = Util::getServerCurrentDateTime();

            $defaultFlag = 0;
            // get the default notebook
            $defaultNotebook = $this->getRepository(Notebook::class)->getDefaultByUserId($session->get('user/id'));
            if (!$defaultNotebook) {
                $defaultFlag = 1;
            }
            $notebookId = $this->getRepository(Notebook::class)->save($session->get('user/id'), $name, $description, $defaultFlag, $currentDate);

            $this->getLogger()->addInfo('ADD QUICK NOTES notebook ' . $name, $this->getLoggerContext());
        }

        return new Response('');
    }
}
