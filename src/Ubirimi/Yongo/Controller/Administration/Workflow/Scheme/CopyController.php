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

namespace Ubirimi\Yongo\Controller\Administration\Workflow\Scheme;

use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Ubirimi\SystemProduct;
use Ubirimi\UbirimiController;
use Ubirimi\Util;
use Ubirimi\Yongo\Repository\Workflow\WorkflowScheme;

class CopyController extends UbirimiController
{
    public function indexAction(Request $request, SessionInterface $session)
    {
        Util::checkUserIsLoggedInAndRedirect();

        $workflowSchemeId = $request->get('id');
        $workflowScheme = $this->getRepository(WorkflowScheme::class)->getMetaDataById($workflowSchemeId);

        if ($workflowScheme['client_id'] != $session->get('client/id')) {
            return new RedirectResponse('/general-settings/bad-link-access-denied');
        }

        $emptyName = false;
        $duplicateName = false;

        if ($request->request->has('copy_workflow_scheme')) {
            $name = Util::cleanRegularInputField($request->request->get('name'));
            $description = Util::cleanRegularInputField($request->request->get('description'));

            if (empty($name)) {
                $emptyName = true;
            }

            $workflowSchemeAlreadyExisting = $this->getRepository(WorkflowScheme::class)->getByClientIdAndName(
                $session->get('client/id'),
                mb_strtolower($name)
            );

            if ($workflowSchemeAlreadyExisting) {
                $duplicateName = true;
            }

            if (!$emptyName && !$workflowSchemeAlreadyExisting) {
                $copiedWorkflowScheme = new WorkflowScheme($session->get('client/id'), $name, $description);

                $currentDate = Util::getServerCurrentDateTime();
                $copiedWorkflowSchemeId = $copiedWorkflowScheme->save($currentDate);

                $workflowSchemeData = $this->getRepository(WorkflowScheme::class)->getDataById($workflowSchemeId);

                while ($workflowSchemeData && $data = $workflowSchemeData->fetch_array(MYSQLI_ASSOC)) {
                    $copiedWorkflowScheme->addData($copiedWorkflowSchemeId, $data['workflow_id'], $currentDate);
                }

                $this->getLogger()->addInfo('Copy Yongo Workflow Scheme ' . $workflowScheme['name'], $this->getLoggerContext());

                return new RedirectResponse('/yongo/administration/workflows/schemes');
            }
        }

        $menuSelectedCategory = 'issue';

        $sectionPageTitle = $session->get('client/settings/title_name') . ' / ' . SystemProduct::SYS_PRODUCT_YONGO_NAME . ' / Copy Workflow Scheme';

        return $this->render(__DIR__ . '/../../../../Resources/views/administration/workflow/scheme/Copy.php', get_defined_vars());
    }
}
