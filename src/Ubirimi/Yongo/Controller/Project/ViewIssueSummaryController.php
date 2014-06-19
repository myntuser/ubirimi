<?php

namespace Ubirimi\Yongo\Controller\Project;

use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Ubirimi\Repository\Client;
use Ubirimi\Repository\User\User;
use Ubirimi\SystemProduct;
use Ubirimi\UbirimiController;use Ubirimi\Util;
use Ubirimi\Yongo\Repository\Issue\Issue;
use Ubirimi\Yongo\Repository\Permission\GlobalPermission;
use Ubirimi\Yongo\Repository\Permission\Permission;
use Ubirimi\Yongo\Repository\Project\Project;

class ViewIssueSummaryController extends UbirimiController
{
    public function indexAction(Request $request, SessionInterface $session)
    {
        if (Util::checkUserIsLoggedIn()) {
            $loggedInUserId = $session->get('user/id');
            $clientId = $session->get('client/id');
            $clientSettings = $session->get('client/settings');
        } else {
            $loggedInUserId = null;
            $clientId = Client::getClientIdAnonymous();
            $clientSettings = Client::getSettings($clientId);
        }

        $projectId = $request->get('id');
        $project = Project::getById($projectId);

        if ($project['client_id'] != $clientId) {
            return new RedirectResponse('/general-settings/bad-link-access-denied');
        }

        $issueQueryParameters = array('project' => $projectId, 'resolution' => array(-2));
        $issues = Issue::getByParameters($issueQueryParameters, $loggedInUserId);

        $count = 0;
        $stats_priority = array();
        $stats_type = array();
        $stats_status = array();
        $stats_assignee = array();

        if ($issues) {
            $count = $issues->num_rows;
            // group them by priority
            while ($issues && $issue = $issues->fetch_array(MYSQLI_ASSOC)) {
                if (!isset($stats_priority[$issue['priority']])) {
                    $stats_priority[$issue['priority']] = array($issue['priority_name'] => 0);
                }
                $stats_priority[$issue['priority']][$issue['priority_name']]++;
            }

            // group them by type
            $issues->data_seek(0);
            $stats_type = array();
            while ($issues && $issue = $issues->fetch_array(MYSQLI_ASSOC)) {
                if (!isset($stats_type[$issue['type']])) {
                    $stats_type[$issue['type']] = array($issue['type_name'] => 0);
                }
                $stats_type[$issue['type']][$issue['type_name']]++;
            }

            // group them by status
            $issues->data_seek(0);
            $stats_status = array();
            while ($issues && $issue = $issues->fetch_array(MYSQLI_ASSOC)) {
                if (!isset($stats_status[$issue['status']])) {
                    $stats_status[$issue['status']] = array($issue['status_name'] => 0);
                }
                $stats_status[$issue['status']][$issue['status_name']]++;
            }

            // group them by assignee
            $issues->data_seek(0);
            $stats_assignee = array();
            while ($issues && $issue = $issues->fetch_array(MYSQLI_ASSOC)) {
                if (!isset($stats_assignee[$issue['assignee']])) {
                    $userName = $issue['ua_first_name'] . ' ' . $issue['ua_last_name'];
                    $stats_assignee[$issue['assignee']] = array($userName => 0);
                }
                $userName = $issue['ua_first_name'] . ' ' . $issue['ua_last_name'];
                $stats_assignee[$issue['assignee']][$userName]++;
            }
        }

        $issueQueryParameters = array('project' => $projectId, 'resolution' => array(-2), 'component' => -1);
        $issues = Issue::getByParameters($issueQueryParameters, $loggedInUserId);
        $countUnresolvedWithoutComponent = 0;
        if ($issues)
            $countUnresolvedWithoutComponent = $issues->num_rows;

        $components = Project::getComponents($projectId);
        $stats_component = array();
        while ($components && $component = $components->fetch_array(MYSQLI_ASSOC)) {
            $issueQueryParameters = array('project' => $projectId, 'component' => $component['id']);
            $issues = Issue::getByParameters($issueQueryParameters, $loggedInUserId);
            if ($issues)
                $stats_component[$component['name']] = array($component['id'], $issues->num_rows);
        }

        $menuSelectedCategory = 'project';

        $hasGlobalAdministrationPermission = User::hasGlobalPermission($clientId, $loggedInUserId, GlobalPermission::GLOBAL_PERMISSION_YONGO_ADMINISTRATORS);
        $hasGlobalSystemAdministrationPermission = User::hasGlobalPermission($clientId, $loggedInUserId, GlobalPermission::GLOBAL_PERMISSION_YONGO_SYSTEM_ADMINISTRATORS);
        $hasAdministerProjectsPermission = Client::getProjectsByPermission($clientId, $loggedInUserId, Permission::PERM_ADMINISTER_PROJECTS);

        $hasAdministerProject = $hasGlobalSystemAdministrationPermission || $hasGlobalAdministrationPermission || $hasAdministerProjectsPermission;

        $sectionPageTitle = $clientSettings['title_name'] . ' / ' . SystemProduct::SYS_PRODUCT_YONGO_NAME . ' / ' . $project['name'] . ' / Issue Summary';

        return $this->render(__DIR__ . '/../../Resources/views/project/ViewIssuesSummary.php', get_defined_vars());
    }
}