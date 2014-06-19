<?php
    use Ubirimi\Util;
    use Ubirimi\Yongo\Repository\Field\Field;
    use Ubirimi\Yongo\Repository\Issue\IssueEvent;
    use Ubirimi\Yongo\Repository\Issue\Issue;
    use Ubirimi\Yongo\Repository\Permission\Permission;
    use Ubirimi\Yongo\Repository\Project\Project;
    use Ubirimi\Yongo\Repository\Workflow\Workflow;

    Util::checkUserIsLoggedInAndRedirect();
    $issueId = $_POST['id'];

    $statuses = $_POST['statuses'];
    $statusesIds = explode('_', $statuses);

    $parameters = array('parent_id' => $issueId);
    $childrenIssue = Issue::getByParameters($parameters);

    while ($issue = $childrenIssue->fetch_array(MYSQLI_ASSOC)) {
        if (!in_array($issue['status'], $statusesIds)) {
            echo 'no';
            die();
        }
    }

    $issue = Issue::getByParameters(array('issue_id' => $issueId), $loggedInUserId);
    if (in_array($issue['status'], $statusesIds)) {
        echo 'no';
        die();
    }

    $projectId = $issue['issue_project_id'];
    $workflowUsed = Project::getWorkflowUsedForType($projectId, $issue[Field::FIELD_ISSUE_TYPE_CODE]);

    $step = Workflow::getStepByWorkflowIdAndStatusId($workflowUsed['id'], $issue[Field::FIELD_STATUS_CODE]);
    $workflowActions = Workflow::getTransitionsForStepId($workflowUsed['id'], $step['id']);

    $data = array();
    while ($workflowActions && $workflowStep = $workflowActions->fetch_array(MYSQLI_ASSOC)) {
        if (!in_array($workflowStep['status'], $statusesIds))
            continue;

        $workflowDataId = $workflowStep['id'];

        $transitionEvent = IssueEvent::getEventByWorkflowDataId($workflowDataId);
        $hasEventPermission = false;

        switch ($transitionEvent['code']) {

            case IssueEvent::EVENT_ISSUE_CLOSED_CODE:
                $hasEventPermission = Project::userHasPermission($projectId, Permission::PERM_CLOSE_ISSUE, $loggedInUserId);
                break;

            case IssueEvent::EVENT_ISSUE_REOPENED_CODE:

            case IssueEvent::EVENT_ISSUE_RESOLVED_CODE:

            $hasEventPermission = Project::userHasPermission($projectId, Permission::PERM_RESOLVE_ISSUE, $loggedInUserId);
                break;

            case IssueEvent::EVENT_ISSUE_WORK_STARTED_CODE:
            case IssueEvent::EVENT_ISSUE_WORK_STOPPED_CODE:

            $hasEventPermission = Project::userHasPermission($projectId, Permission::PERM_EDIT_ISSUE, $loggedInUserId);
                break;
            case IssueEvent::EVENT_GENERIC_CODE:
                $hasEventPermission = true;
                break;
        }

        $canBeExecuted = Workflow::checkConditionsByTransitionId($workflowStep['id'], $loggedInUserId, $issue);
        $value = array();

        if ($hasEventPermission && $canBeExecuted) {
            $value['transition_name'] = $workflowStep['transition_name'];
            if ($workflowStep['screen_id'])
                $value['screen'] = 1;
            else
                $value['screen'] = 0;
            $value['step_id_from'] = $step['id'];
            $value['step_id_to'] = $workflowStep['workflow_step_id_to'];
            $value['workflow_id'] = $workflowUsed['id'];
            $value['project_id'] = $projectId;
            $data[] = $value;
        }
    }

    echo json_encode($data);