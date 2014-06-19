<?php
    use Ubirimi\Container\UbirimiContainer;
    use Ubirimi\SystemProduct;
    use Ubirimi\Util;
    use Ubirimi\Yongo\Repository\Issue\Issue;
    use Ubirimi\Yongo\Repository\Project\Project;
    use Ubirimi\Yongo\Repository\Workflow\Workflow;

    Util::checkUserIsLoggedInAndRedirect();

    $issueId = $_GET['id'];
    $issueQueryParameters = array('issue_id' => $issueId);
    $issue = Issue::getByParameters($issueQueryParameters, $loggedInUserId);
    $projectId = $issue['issue_project_id'];
    $issueProject = Project::getById($projectId);

    // before going further, check to is if the issue project belongs to the client
    if ($clientId != $issueProject['client_id']) {
        header('Location: /general-settings/bad-link-access-denied');
        die();
    }

    $session->set('selected_product_id', SystemProduct::SYS_PRODUCT_YONGO);
    $errorNoNewSubtaskIssueTypeSelected = false;
    if (isset($_POST['move_issue_step_1_subtask'])) {

        // keep the new sub task issue types
        $newSubtaskIssueTypeId = null;
        foreach ($_POST as $key => $value) {
            if (substr($key, 0, 23) == 'new_subtask_issue_type_') {
                $oldSubtaskIssueTypeId = str_replace('new_subtask_issue_type_', '', $key);
                $newSubtaskIssueTypeId = $_POST[$key];
                UbirimiContainer::get()['session']->set('move_issue/sub_task_new_issue_type', array($oldSubtaskIssueTypeId, $newSubtaskIssueTypeId));
            }
        }

        // check if step 2 is necessary
        $newWorkflow = Project::getWorkflowUsedForType(UbirimiContainer::get()['session']->get('move_issue/new_project'), UbirimiContainer::get()['session']->get('move_issue/new_type'));
        $newStatuses = Workflow::getLinkedStatuses($newWorkflow['id']);

        $step2Necessary = true;
        while ($newStatuses && $status = $newStatuses->fetch_array(MYSQLI_ASSOC)) {
            if ($status['linked_issue_status_id'] == $issue['status']) {
                $step2Necessary = false;
            }
        }

        if ($newSubtaskIssueTypeId) {
            if ($step2Necessary) {
                header('Location: /yongo/issue/move/status/' . $issueId);
            } else {
                UbirimiContainer::get()['session']->set('move_issue/new_status', $issue['status']);
                header('Location: /yongo/issue/move/fields/' . $issueId);
            }
        } else {
            $errorNoNewSubtaskIssueTypeSelected = true;
        }
    }
    $sectionPageTitle = $session->get('client/settings/title_name') . ' / ' . SystemProduct::SYS_PRODUCT_YONGO_NAME . ' / Move Issue - ' . $issue['project_code'] . '-' . $issue['nr'] . ' ' . $issue['summary'];

    $menuSelectedCategory = 'issue';

    $oldSubtaskIssueType = UbirimiContainer::get()['session']->get('move_issue/sub_task_old_issue_type');
    $newSubtaskIssueType = Project::getSubTasksIssueTypes(UbirimiContainer::get()['session']->get('move_issue/new_project'));

    require_once __DIR__ . '/../../../Resources/views/issue/move/MoveStep1Subtask.php';