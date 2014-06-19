<?php

namespace Ubirimi\Yongo\Controller\Issue\Comment;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Ubirimi\UbirimiController;use Ubirimi\Util;
use Ubirimi\Yongo\Repository\Issue\IssueComment;
use Ubirimi\Yongo\Repository\Permission\Permission;
use Ubirimi\Yongo\Repository\Project\Project;

class ViewController extends UbirimiController
{
    public function indexAction(Request $request, SessionInterface $session)
    {
        $Id = $request->request->get('id');
        $loggedInUserId = $session->get('user/id');

        if (false === Util::checkUserIsLoggedIn()) {
            $loggedInUserId = null;
        }

        $projectData = Project::getByIssueId($Id);
        $comments = IssueComment::getByIssueId($Id);

        $hasAddCommentsPermission = Project::userHasPermission($projectData['id'], Permission::PERM_ADD_COMMENTS, $loggedInUserId);
        $hasDeleteAllComments = Project::userHasPermission($projectData['id'], Permission::PERM_DELETE_ALL_COMMENTS, $loggedInUserId);
        $hasDeleteOwnComments = Project::userHasPermission($projectData['id'], Permission::PERM_DELETE_OWN_COMMENTS, $loggedInUserId);
        $hasEditAllComments = Project::userHasPermission($projectData['id'], Permission::PERM_EDIT_ALL_COMMENTS, $loggedInUserId);
        $hasEditOwnComments = Project::userHasPermission($projectData['id'], Permission::PERM_EDIT_OWN_COMMENTS, $loggedInUserId);

        $actionButtonsFlag = true;

        return $this->render(__DIR__ . '/../../../Resources/views/issue/comment/View.php', get_defined_vars());
    }
}