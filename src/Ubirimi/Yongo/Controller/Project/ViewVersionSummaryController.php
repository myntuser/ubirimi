<?php

namespace Ubirimi\Yongo\Controller\Project;

use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Ubirimi\Repository\Client;
use Ubirimi\SystemProduct;
use Ubirimi\UbirimiController;use Ubirimi\Util;
use Ubirimi\Yongo\Repository\Project\Project;
use Ubirimi\Yongo\Repository\Issue\Issue;

class ViewVersionSummaryController extends UbirimiController
{
    public function indexAction(Request $request, SessionInterface $session)
    {
        if (Util::checkUserIsLoggedIn()) {
            $loggedInUserId = $session->get('user/id');
            $clientId = $session->get('client/id');
            $clientSettings = $session->get('client/settings');
        } else {
            $clientId = Client::getClientIdAnonymous();
            $loggedInUserId = null;
            $clientSettings = Client::getSettings($clientId);
        }

        $versionId = $request->get('id');
        $version = Project::getVersionById($versionId);
        $projectId = $version['project_id'];
        $project = Project::getById($projectId);

        if ($project['client_id'] != $clientId) {
            return new RedirectResponse('/general-settings/bad-link-access-denied');
        }

        $menuSelectedCategory = 'project';

        $sectionPageTitle = $clientSettings['title_name'] . ' / ' . SystemProduct::SYS_PRODUCT_YONGO_NAME . ' / Version: ' . $version['name'] . ' / Summary';
        $issuesResult = Issue::getByParameters(array('project' => $projectId,
            'resolution' => array(-2),
            'page' => 1,
            'version' => array($versionId),
            'issues_per_page' => 10), $loggedInUserId);
        $issues = $issuesResult[0];

        $issuesResultUpdatedRecently = Issue::getByParameters(array('project' => $projectId,
            'resolution' => array(-2),
            'page' => 1,
            'issues_per_page' => 10,
            'sort' => 'updated',
            'version' => array($versionId),
            'sort_order' => 'desc'), $loggedInUserId);
        $issuesUpdatedRecently = $issuesResultUpdatedRecently[0];

        return $this->render(__DIR__ . '/../../Resources/views/project/ViewVersionSummary.php', get_defined_vars());
    }
}