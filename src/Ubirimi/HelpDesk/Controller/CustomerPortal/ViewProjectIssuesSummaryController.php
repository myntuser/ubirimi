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

namespace Ubirimi\HelpDesk\Controller\CustomerPortal;

use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Ubirimi\SystemProduct;
use Ubirimi\UbirimiController;
use Ubirimi\Util;
use Ubirimi\Yongo\Repository\Issue\Issue;
use Ubirimi\Yongo\Repository\Project\YongoProject;

class ViewProjectIssuesSummaryController extends UbirimiController
{
    public function indexAction(Request $request, SessionInterface $session)
    {
        Util::checkUserIsLoggedInAndRedirect();

        $clientSettings = $session->get('client/settings');

        $projectId = $request->get('id');
        $project = $this->getRepository(YongoProject::class)->getById($projectId);

        if ($project['client_id'] != $session->get('client/id')) {
            return new RedirectResponse('/general-settings/bad-link-access-denied');
        }

        $issueQueryParameters = array(
            'project' => $projectId,
            'resolution' => array(-2),
            'helpdesk_flag' => 1
        );

        $issues = $this->getRepository(Issue::class)->getByParameters(
            $issueQueryParameters,
            $session->get('user/id'),
            null,
            $session->get('user/id')
        );

        $count = 0;
        $statsPriority = array();
        $statsType = array();
        $statsStatus = array();
        $statsAssignee = array();

        if ($issues) {
            $count = $issues->num_rows;
            // group them by priority
            while ($issues && $issue = $issues->fetch_array(MYSQLI_ASSOC)) {
                if (!isset($statsPriority[$issue['priority']])) {
                    $statsPriority[$issue['priority']] = array($issue['priority_name'] => 0);
                }
                $statsPriority[$issue['priority']][$issue['priority_name']]++;
            }

            // group them by type
            $issues->data_seek(0);
            $statsType = array();
            while ($issues && $issue = $issues->fetch_array(MYSQLI_ASSOC)) {
                if (!isset($statsType[$issue['type']])) {
                    $statsType[$issue['type']] = array($issue['type_name'] => 0);
                }
                $statsType[$issue['type']][$issue['type_name']]++;
            }

            // group them by status
            $issues->data_seek(0);
            $statsStatus = array();
            while ($issues && $issue = $issues->fetch_array(MYSQLI_ASSOC)) {
                if (!isset($statsStatus[$issue['status']])) {
                    $statsStatus[$issue['status']] = array($issue['status_name'] => 0);
                }
                $statsStatus[$issue['status']][$issue['status_name']]++;
            }

            // group them by assignee
            $issues->data_seek(0);
            $statsAssignee = array();
            while ($issues && $issue = $issues->fetch_array(MYSQLI_ASSOC)) {
                if (!isset($statsAssignee[$issue['assignee']])) {
                    $userName = $issue['ua_first_name'] . ' ' . $issue['ua_last_name'];
                    $statsAssignee[$issue['assignee']] = array($userName => 0);
                }
                $userName = $issue['ua_first_name'] . ' ' . $issue['ua_last_name'];
                $statsAssignee[$issue['assignee']][$userName]++;
            }
        }

        $issueQueryParameters = array(
            'project' => $projectId,
            'resolution' => array(-2),
            'component' => -1,
            'helpdesk_flag' => 1
        );

        $issues = $this->getRepository(Issue::class)->getByParameters(
            $issueQueryParameters,
            $session->get('user/id'),
            null,
            $session->get('user/id')
        );

        $countUnresolvedWithoutComponent = 0;
        if ($issues) {
            $countUnresolvedWithoutComponent = $issues->num_rows;
        }

        $components = $this->getRepository(YongoProject::class)->getComponents($projectId);
        $statsComponent = array();
        while ($components && $component = $components->fetch_array(MYSQLI_ASSOC)) {
            $issueQueryParameters = array(
                'project' => $projectId,
                'component' => $component['id'],
                'helpdesk_flag' => 1
            );

            $issues = $this->getRepository(Issue::class)->getByParameters(
                $issueQueryParameters,
                $session->get('user/id'),
                null,
                $session->get('user/id')
            );

            if ($issues)
                $statsComponent[$component['name']] = array($component['id'], $issues->num_rows);
        }

        $menuSelectedCategory = 'project';
        $menuProjectCategory = 'issues';

        $sectionPageTitle = $clientSettings['title_name']
            . ' / ' . SystemProduct::SYS_PRODUCT_HELP_DESK
            . ' / ' . $project['name']
            . ' / Issue Summary';

        return $this->render(__DIR__ . '/../../Resources/views/customer_portal/ViewProjectIssuesSummary.php', get_defined_vars());
    }
}