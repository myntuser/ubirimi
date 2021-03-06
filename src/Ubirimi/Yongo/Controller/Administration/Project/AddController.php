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

namespace Ubirimi\Yongo\Controller\Administration\Project;

use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Ubirimi\Container\UbirimiContainer;
use Ubirimi\Entity\Yongo\Project as ProjectEntity;
use Ubirimi\Repository\General\UbirimiClient;
use Ubirimi\SystemProduct;
use Ubirimi\UbirimiController;
use Ubirimi\Util;
use Ubirimi\Yongo\Repository\Field\FieldConfigurationScheme;
use Ubirimi\Yongo\Repository\Issue\IssueTypeScheme;
use Ubirimi\Yongo\Repository\Issue\IssueTypeScreenScheme;
use Ubirimi\Yongo\Repository\Notification\NotificationScheme;
use Ubirimi\Yongo\Repository\Permission\PermissionScheme;
use Ubirimi\Yongo\Repository\Project\ProjectCategory;
use Ubirimi\Yongo\Repository\Project\YongoProject;
use Ubirimi\Yongo\Repository\Workflow\WorkflowScheme;

class AddController extends UbirimiController
{
    public function indexAction(Request $request, SessionInterface $session)
    {
        Util::checkUserIsLoggedInAndRedirect();

        $leadUsers = $this->getRepository(UbirimiClient::class)->getUsers($session->get('client/id'));
        $forHelpDesk = $request->query->has('helpdesk') ? true : false;

        $emptyName = false;
        $duplicateName = false;
        $emptyCode = false;
        $duplicateCode = false;

        $issueTypeScheme = $this->getRepository(IssueTypeScheme::class)->getByClientId($session->get('client/id'), 'project');
        $issueTypeScreenScheme = $this->getRepository(IssueTypeScreenScheme::class)->getByClientId($session->get('client/id'));
        $fieldConfigurationSchemes = $this->getRepository(FieldConfigurationScheme::class)->getByClient($session->get('client/id'));
        $workflowScheme = $this->getRepository(WorkflowScheme::class)->getMetaDataByClientId($session->get('client/id'));
        $permissionScheme = $this->getRepository(PermissionScheme::class)->getByClientId($session->get('client/id'));
        $notificationScheme = $this->getRepository(NotificationScheme::class)->getByClientId($session->get('client/id'));
        $projectCategories = $this->getRepository(ProjectCategory::class)->getAll($session->get('client/id'));

        if ($request->request->has('confirm_new_project')) {

            $forHelpDesk = $request->query->has('helpdesk') ? 1 : 0;
            $name = Util::cleanRegularInputField($request->request->get('name'));
            $code = strtoupper(Util::cleanRegularInputField($request->request->get('code')));
            $description = Util::cleanRegularInputField($request->request->get('description'));

            $issueTypeSchemeId = $request->request->get('issue_type_scheme');
            $issueTypeScreenSchemeId = $request->request->get('yongo_issue_type_screen_scheme');
            $issueTypeFieldConfigurationSchemeId = $request->request->get('field_configuration_scheme');
            $workflowSchemeId = $request->request->get('workflow_scheme');
            $permissionSchemeId = $request->request->get('permission_scheme');
            $notificationSchemeId = $request->request->get('notification_scheme');
            $leadId = Util::cleanRegularInputField($request->request->get('lead'));
            $projectCategoryId = Util::cleanRegularInputField($request->request->get('project_category'));

            if (-1 == $projectCategoryId) {
                $projectCategoryId = null;
            }

            if (empty($name)) {
                $emptyName = true;
            }

            if (empty($code)) {
                $emptyCode = true;
            } else {
                $projectExists = $this->getRepository(YongoProject::class)->getByCode(mb_strtolower($code), null, $session->get('client/id'));
                if ($projectExists)
                    $duplicateCode = true;
            }
            $projectExists = $this->getRepository(YongoProject::class)->getByName(mb_strtolower($name), null, $session->get('client/id'));
            if ($projectExists)
                $duplicateName = true;

            if (!$emptyName && !$emptyCode && !$duplicateName && !$duplicateCode) {

                $currentDate = Util::getServerCurrentDateTime();

                $project = new ProjectEntity();
                $project->setClientId($session->get('client/id'));
                $project->setName($name);
                $project->setCode($code);
                $project->setDescription($description);
                $project->setIssueTypeSchemeId($issueTypeSchemeId);
                $project->setIssueTypeScreenSchemeId($issueTypeScreenSchemeId);
                $project->setIssueTypeFieldConfigurationId($issueTypeFieldConfigurationSchemeId);
                $project->setWorkflowSchemeId($workflowSchemeId);
                $project->setPermissionSchemeId($permissionSchemeId);
                $project->setNotificationSchemeId($notificationSchemeId);
                $project->setLeadId($leadId);
                $project->setProjectCategoryId($projectCategoryId);
                $project->setHelpDeskDeskEnabledFlag($forHelpDesk);
                $project->setDateCreated($currentDate);

                $projectId = UbirimiContainer::get()['project']->add($project, $session->get('user/id'));

                $session->set('selected_project_id', $projectId);

                $this->getLogger()->addInfo('ADD Yongo Project ' . $name, $this->getLoggerContext());

                if ($forHelpDesk) {
                    return new RedirectResponse('/helpdesk/all');
                } else {
                    return new RedirectResponse('/yongo/administration/projects');
                }
            }
        }

        $menuSelectedCategory = 'project';
        $sectionPageTitle = $session->get('client/settings/title_name') . ' / ' . SystemProduct::SYS_PRODUCT_YONGO_NAME . ' / Create Project';

        return $this->render(__DIR__ . '/../../../Resources/views/administration/project/Add.php', get_defined_vars());
    }
}
