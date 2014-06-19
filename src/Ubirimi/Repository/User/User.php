<?php

namespace Ubirimi\Repository\User;

use Ubirimi\Calendar\Repository\Calendar;
use Ubirimi\Container\UbirimiContainer;
use Ubirimi\PasswordHash;

class User {

    public static function getPermissionRolesByUserId($userId, $resultType = null, $field = null) {
        $query = 'select distinct permission_role_id from permission_role_data where user_id = ?';
        if ($stmt = UbirimiContainer::get()['db.connection']->prepare($query)) {
            $stmt->bind_param("i", $userId);
            $stmt->execute();
            $result = $stmt->get_result();

            $finalResult = null;
            if ($result->num_rows) {
                if ($resultType == 'array') {
                    $resultData = array();
                    while ($group_result = $result->fetch_array(MYSQLI_ASSOC)) {
                        if ($field)
                            $resultData[] = $group_result[$field];
                        else
                            $resultData[] = $group_result;
                    }
                    $finalResult = $resultData;
                } else $finalResult = $result;
            }

            return $finalResult;
        }
    }

    public static function getGroupsByUserId($userId, $resultType = null, $field = null) {
        $query = 'select distinct group_id from group_data where user_id = ?';
        if ($stmt = UbirimiContainer::get()['db.connection']->prepare($query)) {
            $stmt->bind_param("i", $userId);
            $stmt->execute();
            $result = $stmt->get_result();

            $finalResult = null;
            if ($result->num_rows) {
                if ($resultType == 'array') {
                    $resultData = array();
                    while ($group_result = $result->fetch_array(MYSQLI_ASSOC)) {
                        if ($field)
                            $resultData[] = $group_result[$field];
                        else
                            $resultData[] = $group_result;
                    }
                    $finalResult = $resultData;
                } else $finalResult = $result;
            }

            return $finalResult;
        }
    }

    public static function createAdministratorUser($admin_first_name, $admin_last_name, $admin_username, $password, $admin_email, $clientId, $issuesPerPage, $svnAdministratorFlag, $clientAdministratorFlag, $currentDate) {

        $t_hasher = new PasswordHash(8, FALSE);
        $hash = $t_hasher->HashPassword($password);

        $query = "INSERT INTO user(first_name, last_name, username, password, email, " .
                                  "client_id, issues_per_page, svn_administrator_flag, client_administrator_flag, date_created) " .
                        "VALUES ('" . $admin_first_name . "', '" . $admin_last_name . "', '" . $admin_username . "', '" . $hash . "', '" . $admin_email .
                                 "', " . $clientId . ", " . $issuesPerPage . ", " . $svnAdministratorFlag . ", " . $clientAdministratorFlag . ", '" . $currentDate . "')";
        UbirimiContainer::get()['db.connection']->query($query);

        return UbirimiContainer::get()['db.connection']->insert_id;
    }

    public static function add($clientId, $first_name, $last_name, $email, $username, $password, $issuesPerPage, $customerServiceDeskFlag, $currentDate) {
        $query = "INSERT INTO user(client_id, first_name, last_name, email, username, password, issues_per_page, customer_service_desk_flag, date_created) " .
                 "VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";

        $t_hasher = new PasswordHash(8, FALSE);
        $hash = $t_hasher->HashPassword($password);

        if ($stmt = UbirimiContainer::get()['db.connection']->prepare($query)) {

            $stmt->bind_param("isssssiis", $clientId, $first_name, $last_name, $email, $username, $hash, $issuesPerPage, $customerServiceDeskFlag, $currentDate);
            $stmt->execute();

            return array(UbirimiContainer::get()['db.connection']->insert_id, $password);
        }
    }

    public static function deleteById($userId) {

        // delete yongo related entities
        $query = 'delete from issue_comment where user_id = ' . $userId;
        UbirimiContainer::get()['db.connection']->query($query);

        $query = 'delete from issue_attachment where user_id = ' . $userId;
        UbirimiContainer::get()['db.connection']->query($query);

        $query = 'delete from permission_scheme_data where user_id = ' . $userId;
        UbirimiContainer::get()['db.connection']->query($query);

        $query = 'delete from notification_scheme_data where user_id = ' . $userId;
        UbirimiContainer::get()['db.connection']->query($query);

        $query = 'update project set lead_id = NULL where lead_id = ' . $userId;
        UbirimiContainer::get()['db.connection']->query($query);

        $query = 'update project_component set leader_id = NULL where leader_id = ' . $userId;
        UbirimiContainer::get()['db.connection']->query($query);

        $query = 'delete from group_data where user_id = ' . $userId;
        UbirimiContainer::get()['db.connection']->query($query);

        $query = 'delete from project_role_data where user_id = ' . $userId;
        UbirimiContainer::get()['db.connection']->query($query);

        $query = 'delete from permission_role_data where default_user_id = ' . $userId;
        UbirimiContainer::get()['db.connection']->query($query);

        // delete calendar related entities

        Calendar::deleteByUserId($userId);

        // todo: delete documentador related entities

        $query = 'delete from user where id = ' . $userId . ' LIMIT 1';
        UbirimiContainer::get()['db.connection']->query($query);

        // todo: delete svn related entities

    }

    public static function getById($Id) {
        $query = "SELECT user.id, user.client_id, password, first_name, last_name, email, username, user.date_created, user.avatar_picture, " .
                 "issues_per_page, notify_own_changes_flag, client_administrator_flag, customer_service_desk_flag " .
            "FROM user " .
            "WHERE user.id = ?";

        if ($stmt = UbirimiContainer::get()['db.connection']->prepare($query)) {
            $stmt->bind_param("i", $Id);
            $stmt->execute();
            $result = $stmt->get_result();
            if ($result->num_rows)
                return $result->fetch_array(MYSQLI_ASSOC);
            else
                return null;
        }
    }

    public static function updateById($userId, $first_name, $last_name, $email, $username, $issuesPerPage = null, $clientAdministratorFlag = 0, $customerServiceDeskFlag, $date) {
        $query = 'UPDATE user SET ' .
                 'first_name = ?, last_name = ?, email = ?, username = ?, client_administrator_flag = ?, customer_service_desk_flag = ?, date_updated = ? ';

        if ($issuesPerPage)
            $query .= ', issues_per_page = ? ';

        $query .= 'WHERE id = ? ' .
                  'LIMIT 1';

        if ($stmt = UbirimiContainer::get()['db.connection']->prepare($query)) {
            if ($issuesPerPage)
                $stmt->bind_param("ssssiisii", $first_name, $last_name, $email, $username, $clientAdministratorFlag, $customerServiceDeskFlag, $date, $issuesPerPage, $userId);
            else
                $stmt->bind_param("ssssiisi", $first_name, $last_name, $email, $username, $clientAdministratorFlag, $customerServiceDeskFlag, $date, $userId);

            $stmt->execute();
        }
    }

    public static function checkUserInProjectRoleId($userId, $projectId, $roleId) {
        $query = "SELECT project_role_data.id, project_role_data.user_id " .
            "FROM project_role_data " .
            "WHERE permission_role_id = ? " .
            "AND project_id = ? " .
            "AND user_id = ?";

        if ($stmt = UbirimiContainer::get()['db.connection']->prepare($query)) {
            $stmt->bind_param("iii", $roleId, $projectId, $userId);
            $stmt->execute();
            $result = $stmt->get_result();
            if ($result->num_rows)
                return $result;
            else
                return null;
        }
    }

    public static function getGroupsForUserIdAndRoleId($userId, $projectId, $roleId, $groupIds) {

        $queryCondition = '';
        if (!empty($groupIds)) {
            $queryCondition = " OR group_id IN (" . implode(', ', $groupIds) . ')';
        }
        $query = "SELECT project_role_data.id, project_role_data.user_id, group.id as group_id, group.name as group_name " .
            "FROM project_role_data " .
            "left join `group` on `group`.id = project_role_data.group_id " .
            "WHERE permission_role_id = ? " .
            "AND project_id = ? " .
            "AND (user_id = ? " . $queryCondition . ') ' .
            "and project_role_data.group_id is not null";

        if ($stmt = UbirimiContainer::get()['db.connection']->prepare($query)) {
            $stmt->bind_param("iii", $roleId, $projectId, $userId);
            $stmt->execute();
            $result = $stmt->get_result();
            if ($result->num_rows)
                return $result;
            else
                return null;
        }
    }

    public static function checkProjectRole($userId, $projectId, $roleId, $groupIds) {
        $query = "SELECT project_role_data.id, project_role_data.user_id, group.id as group_id, group.name as group_name " .
                    "FROM project_role_data " .
                    "left join `group` on `group`.id = project_role_data.group_id " .
                    "WHERE permission_role_id = ? " .
                        "AND project_id = ? " .
                        "AND (user_id = ? OR group_id IN (" . implode(', ', $groupIds) . ')) ' .
                    "order by project_role_data.user_id";

        if ($stmt = UbirimiContainer::get()['db.connection']->prepare($query)) {
            $stmt->bind_param("iii", $roleId, $projectId, $userId);
            $stmt->execute();
            $result = $stmt->get_result();
            if ($result->num_rows)
                return $result;
            else
                return null;
        }
    }

    public static function getByClientId($clientId, $helpDeskFlag = 0) {

        $query = "SELECT user.*, help_organization.name as organization_name " .
            "FROM user " .
            'left join help_organization_user on help_organization_user.user_id = user.id ' .
            'left join help_organization on help_organization.id = help_organization_user.help_organization_id ' .
            "WHERE user.client_id = ? " .
            'and customer_service_desk_flag = ' . $helpDeskFlag . ' ' .
            "order by id asc";

        if ($stmt = UbirimiContainer::get()['db.connection']->prepare($query)) {
            $stmt->bind_param("i", $clientId);
            $stmt->execute();
            $result = $stmt->get_result();
            if ($result->num_rows)
                return $result;
            else
                return null;
        }
    }

    public static function hasGlobalPermission($clientId, $userId, $globalPermissionId) {
        $query = 'SELECT user.id as user_id, user.first_name, user.last_name ' .
            'from sys_permission_global_data ' .
            'left join `group_data` on `group_data`.group_id = sys_permission_global_data.group_id ' .
            'left join user on user.id = group_data.user_id ' .
            'where sys_permission_global_data.client_id = ? and ' .
            'sys_permission_global_data.sys_permission_global_id = ? and ' .
            'group_data.user_id = ? and ' .
            'user.id is not null ' .

            ' UNION ' .

            'SELECT user.id as user_id, user.first_name, user.last_name ' .
            'from sys_permission_global_data ' .
            'left join user on user.id = sys_permission_global_data.user_id ' .
            'where sys_permission_global_data.client_id = ? and ' .
            'sys_permission_global_data.sys_permission_global_id = ? and ' .
            'sys_permission_global_data.user_id = ? and ' .
            'user.id is not null ';

        if ($stmt = UbirimiContainer::get()['db.connection']->prepare($query)) {
            $stmt->bind_param("iiiiii", $clientId, $globalPermissionId, $userId, $clientId, $globalPermissionId, $userId);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows)
                return $result;
            else
                return null;
        }
    }

    public static function deleteGroupsByUserId($userId) {
        $query = 'delete from group_data where user_id = ? ';
        if ($stmt = UbirimiContainer::get()['db.connection']->prepare($query)) {
            $stmt->bind_param("i", $userId);
            $stmt->execute();
        }
    }

    public static function addGroups($userId, $assigned_user_groups) {
        $query = 'insert into group_data(group_id, user_id) values ';

        for ($i = 0; $i < count($assigned_user_groups); $i++)
            $query .= '(' . $assigned_user_groups[$i] . ' ,' . $userId . '), ';

        $query = substr($query, 0, strlen($query) - 2);

        UbirimiContainer::get()['db.connection']->query($query);
    }

    public static function updatePassword($userId, $hash) {
        $query = 'UPDATE user SET password = ? where id = ? limit 1';

        if ($stmt = UbirimiContainer::get()['db.connection']->prepare($query)) {
            $stmt->bind_param("si", $hash, $userId);
            $stmt->execute();
        }
    }

    public static function getByUsernameAndBaseURL($username, $baseURL) {
        $query = 'SELECT username, user.id, email, first_name, last_name, client_id, issues_per_page, password,
                         super_user_flag, client.company_domain, svn_administrator_flag, client_administrator_flag ' .
            'FROM user ' .
            'left join client on client.id = user.client_id ' .
            "WHERE username = ? " .
            "and client.base_url = ? " .
            "LIMIT 1";

        if ($stmt = UbirimiContainer::get()['db.connection']->prepare($query)) {

            $stmt->bind_param("ss", $username, $baseURL);
            $stmt->execute();
            $result = $stmt->get_result();
            if ($result->num_rows)
                return $result->fetch_array(MYSQLI_ASSOC);
            else
                return null;
        }
    }

    public static function getByUsernameAndClientId($username, $clientId, $resultColumn = null, $userId = null) {
        $query = 'SELECT username, user.id, email, first_name, last_name, client_id, issues_per_page, password,
                         super_user_flag, svn_administrator_flag, client_administrator_flag, avatar_picture, issues_display_columns ' .
                 'FROM user ' .
                 "WHERE LOWER(username) = ? " .
                 "and client_id = ? and customer_service_desk_flag = 0 ";

        if ($userId) {
            $query .= 'and user.id != ? ';
        }

        $query .= "LIMIT 1";

        if ($stmt = UbirimiContainer::get()['db.connection']->prepare($query)) {

            $username = mb_strtolower($username);
            if ($userId) {
                $stmt->bind_param("sii", $username, $clientId, $userId);
            } else {
                $stmt->bind_param("si", $username, $clientId);
            }

            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows) {
                $data = $result->fetch_array(MYSQLI_ASSOC);
                if ($resultColumn) {
                    return $data[$resultColumn];
                } else {
                    return $data;
                }
            } else
                return null;
        }
    }

    public static function getCustomerByEmailAddressAndClientId($username, $clientId) {
        $query = 'SELECT user.id, email, first_name, last_name, client_id, password, avatar_picture, issues_display_columns ' .
                 'FROM user ' .
                 "WHERE email = ? and customer_service_desk_flag = 1 and client_id = ? " .
                 "LIMIT 1";

        if ($stmt = UbirimiContainer::get()['db.connection']->prepare($query)) {

            $stmt->bind_param("si", $username, $clientId);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows) {
                $data = $result->fetch_array(MYSQLI_ASSOC);
                return $data;
            } else
                return null;
        }
    }

    public static function getUserByUsername($username) {
        $query = 'SELECT username, id, email, first_name, last_name, client_id, issues_per_page, password,
                    super_user_flag, svn_administrator_flag, client_administrator_flag ' .
                 'FROM user ' .
                 "WHERE username = ? " .
                 "LIMIT 1";

        if ($stmt = UbirimiContainer::get()['db.connection']->prepare($query)) {

            $stmt->bind_param("s", $username);
            $stmt->execute();
            $result = $stmt->get_result();
            if ($result->num_rows)
                return $result->fetch_array(MYSQLI_ASSOC);
            else
                return null;
        }
    }

    public static function getUserByUsernameAndAdministrator($username) {
        $query = 'SELECT username, id, email, first_name, last_name, client_id, issues_per_page, password, super_user_flag, svn_administrator_flag ' .
            'FROM user ' .
            "WHERE username = ? and client_administrator_flag = 1 and customer_service_desk_flag = 0 " .
            "LIMIT 1";

        if ($stmt = UbirimiContainer::get()['db.connection']->prepare($query)) {

            $stmt->bind_param("s", $username);
            $stmt->execute();
            $result = $stmt->get_result();
            if ($result->num_rows)
                return $result->fetch_array(MYSQLI_ASSOC);
            else
                return null;
        }
    }

    public static function update($username, $password, $email, $firstName, $lastName, $userId) {
        $query = 'UPDATE user SET
                        password = ?,
                        username = ?,
                        email = ?,
                        first_name = ?,
                        last_name = ?
                    where id = ? limit 1';

        $t_hasher = new PasswordHash(8, FALSE);
        $hash = $t_hasher->HashPassword($password);

        if ($stmt = UbirimiContainer::get()['db.connection']->prepare($query)) {
            $stmt->bind_param("sssssi", $hash, $username, $email, $firstName, $lastName, $userId);
            $stmt->execute();
        }
    }

    public static function getAll($filters = array()) {
        $query = 'select user.id, user.first_name, user.last_name, user.username, user.date_created, user.email, client_administrator_flag, ' .
                 'client.company_name as client_company_name ' .
                 'from user ' .
                 'left join client on client.id = user.client_id ' .
                 'where 1 = 1';

        if (!empty($filters['today'])) {
            $query .= " and DATE(user.date_created) = DATE(NOW())";
        }

        if (empty($filters['sort_by'])) {
            $query .= ' order by client.id';
        } else {
            $query .= " order by " . $filters['sort_by'] . ' ' . $filters['sort_order'];
        }

        if (isset($filters['limit'])) {
            $query .= ' limit ' . $filters['limit'];
        }
        if ($stmt = UbirimiContainer::get()['db.connection']->prepare($query)) {

            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows)
                return $result;
            else
                return false;
        }
    }

    public static function getYongoSettings($userId) {
        $query = 'select issues_per_page, notify_own_changes_flag ' .
            'from user ' .
            'where id = ? ' .
            'limit 1';

        if ($stmt = UbirimiContainer::get()['db.connection']->prepare($query)) {
            $stmt->bind_param("i", $userId);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows)
                return $result->fetch_array(MYSQLI_ASSOC);
            else
                return false;
        }
    }

    public static function updatePreferences($userId, $parameters) {
        $query = 'UPDATE user SET ';

        $values = array();
        $values_ref = array();
        $valuesType = '';
        for ($i = 0; $i < count($parameters); $i++) {
            $query .= $parameters[$i]['field'] .= ' = ?, ';
            $values[] = $parameters[$i]['value'];
            $valuesType .= $parameters[$i]['type'];
        }
        $query = substr($query, 0, strlen($query) - 2) . ' ' ;

        $query .= 'WHERE id = ? ' .
            'LIMIT 1';
        $values[] = $userId;
        $valuesType .= 'i';

        if ($stmt = UbirimiContainer::get()['db.connection']->prepare($query)) {

            foreach ($values as $key => $value)
                $values_ref[$key] = &$values[$key];

            if ($valuesType != '')
                call_user_func_array(array($stmt, "bind_param"), array_merge(array($valuesType), $values_ref));
            $stmt->execute();
            $result = $stmt->get_result();
        }
    }

    public static function getNotSVNAdministrators($clientId) {
        $query = 'SELECT user.* FROM user WHERE client_id = ? and svn_administrator_flag = 0 and customer_service_desk_flag = 0';

        if ($stmt = UbirimiContainer::get()['db.connection']->prepare($query)) {

            $stmt->bind_param("i", $clientId);
            $stmt->execute();
            $result = $stmt->get_result();
            if ($result->num_rows)
                return $result;
            else
                return null;
        }
    }

    public static function getByEmailAddressAndBaseURL($address, $baseURL) {

        $query = 'SELECT username, user.id, email, first_name, last_name, client_id, issues_per_page, password, ' .
                  'super_user_flag, client.company_domain, svn_administrator_flag ' .
            'FROM user ' .
            'left join client on client.id = user.client_id ' .
            "WHERE email = ? " .
            "and client.base_url = ? " .
            "LIMIT 1";

        if ($stmt = UbirimiContainer::get()['db.connection']->prepare($query)) {

            $stmt->bind_param("ss", $address, $baseURL);
            $stmt->execute();
            $result = $stmt->get_result();
            if ($result->num_rows)
                return $result->fetch_array(MYSQLI_ASSOC);
            else
                return null;
        }
    }

    public static function getCustomerByEmailAddressAndBaseURL($address, $baseURL) {

        $query = 'SELECT user.id, email, first_name, last_name, client_id, password, ' .
                  'client.company_domain ' .
            'FROM user ' .
            'left join client on client.id = user.client_id ' .
            "WHERE email = ? " .
                "and user.customer_service_desk_flag = 1 " .
                "and client.base_url = ? " .
            "LIMIT 1";

        if ($stmt = UbirimiContainer::get()['db.connection']->prepare($query)) {

            $stmt->bind_param("ss", $address, $baseURL);
            $stmt->execute();
            $result = $stmt->get_result();
            if ($result->num_rows)
                return $result->fetch_array(MYSQLI_ASSOC);
            else
                return null;
        }
    }

    public static function getIssueSecurityLevelsBySecuritySchemeId($issue, $loggedInUserId) {

        $securityLevelId = $issue['security_level'];
        $projectId = $issue['issue_project_id'];
        $issueId = $issue['id'];

        // 1. user in security scheme level data
        $query =
            'SELECT issue_security_scheme_level_data.id ' .
                'from issue_security_scheme_level_data ' .
                'where issue_security_scheme_level_data.issue_security_scheme_level_id = ? and ' .
                'issue_security_scheme_level_data.user_id = ? ' .
                // 2. group - user in security scheme level data
                'UNION DISTINCT ' .
                'SELECT issue_security_scheme_level_data.id ' .
                'from issue_security_scheme_level_data ' .
                'left join `group` on group.id = issue_security_scheme_level_data.group_id ' .
                'left join `group_data` on group_data.group_id = `group`.id ' .
                'left join user on user.id = group_data.user_id ' .
                'where issue_security_scheme_level_data.issue_security_scheme_level_id = ? and ' .
                'issue_security_scheme_level_data.user_id = ? ' .
                // 3. permission role in security scheme level data - user
                'UNION DISTINCT ' .
                'SELECT issue_security_scheme_level_data.id ' .
                'from issue_security_scheme_level_data ' .
                'left join project_role_data on project_role_data.permission_role_id = issue_security_scheme_level_data.permission_role_id ' .
                'left join user on user.id = project_role_data.user_id ' .
                'where issue_security_scheme_level_data.issue_security_scheme_level_id = ? and ' .
                'issue_security_scheme_level_data.user_id = ? ' .
                // 4. permission role in security scheme level data - group
                'UNION DISTINCT ' .
                'SELECT issue_security_scheme_level_data.id ' .
                'from issue_security_scheme_level_data ' .
                'left join project_role_data on project_role_data.permission_role_id = issue_security_scheme_level_data.permission_role_id ' .
                'left join `group` on group.id = project_role_data.group_id ' .
                'left join `group_data` on group_data.group_id = `group`.id ' .
                'left join user on user.id = group_data.user_id ' .
                'where issue_security_scheme_level_data.issue_security_scheme_level_id = ? and ' .
                'issue_security_scheme_level_data.user_id = ? ' .
                // 5. current_assignee in security scheme level data
                'UNION DISTINCT ' .
                'SELECT issue_security_scheme_level_data.id ' .
                'from issue_security_scheme_level_data ' .
                'left join yongo_issue on yongo_issue.id = ? ' .
                'left join user on user.id = yongo_issue.user_assigned_id ' .
                'where issue_security_scheme_level_data.issue_security_scheme_level_id = ? and ' .
                'issue_security_scheme_level_data.current_assignee is not null and ' .
                'yongo_issue.user_assigned_id is not null and ' .
                'user.id = ? ' .
                // 6. reporter in security scheme level data
                'UNION DISTINCT ' .
                'SELECT issue_security_scheme_level_data.id ' .
                'from issue_security_scheme_level_data ' .
                'left join yongo_issue on yongo_issue.id = ? ' .
                'left join user on user.id = yongo_issue.user_reported_id ' .
                'where issue_security_scheme_level_data.issue_security_scheme_level_id = ? and ' .
                'issue_security_scheme_level_data.reporter is not null and ' .
                'yongo_issue.user_reported_id is not null and ' .
                'user.id = ? ' .
                // 7. project_lead in security scheme level data

                'UNION DISTINCT ' .
                'SELECT issue_security_scheme_level_data.id ' .
                'from issue_security_scheme_level_data ' .
                'left join project on project.id = ? ' .
                'left join user on user.id = project.lead_id ' .
                'where issue_security_scheme_level_data.issue_security_scheme_level_id = ? and ' .
                'issue_security_scheme_level_data.project_lead is not null and ' .
                'project.lead_id is not null and ' .
                'user.id = ?';

        if ($stmt = UbirimiContainer::get()['db.connection']->prepare($query)) {
            $stmt->bind_param("iiiiiiiiiiiiiiiii", $securityLevelId, $loggedInUserId, $securityLevelId, $loggedInUserId, $securityLevelId, $loggedInUserId, $securityLevelId, $loggedInUserId, $issueId, $securityLevelId, $loggedInUserId, $issueId, $securityLevelId, $loggedInUserId, $projectId, $securityLevelId, $loggedInUserId);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows)
                return true;
            else
                return false;
        }
    }

    public static function getYongoActivityStream($userId) {
        $query = '(SELECT issue_history.field, issue_history.old_value, issue_history.new_value, issue_history.date_created, ' .
                 'user.id as user_id, user.first_name, user.last_name, ' .
                 'yongo_issue.nr, yongo_issue.id as issue_id, ' .
                 'project.code, "issue_history" as activity_type ' .
            'FROM issue_history ' .
            'left join user on user.id = issue_history.by_user_id ' .
            'left join yongo_issue on yongo_issue.id = issue_history.issue_id ' .
            'left join project on project.id = yongo_issue.project_id ' .
            "WHERE issue_history.by_user_id = ? " .
            "order by date_created desc) " .

            "UNION " .

            '(SELECT null, null, null, yongo_issue.date_created, ' .
                'user.id as user_id, user.first_name, user.last_name, ' .
                'yongo_issue.nr, yongo_issue.id as issue_id, ' .
                'project.code, "issue_creation" as activity_type ' .
                'from yongo_issue ' .
                'left join project on project.id = yongo_issue.project_id ' .
                'left join user on user.id = yongo_issue.user_reported_id ' .
                "WHERE yongo_issue.user_reported_id = ? " .
                "order by yongo_issue.date_created desc) " .

            "order by date_created desc";

//            echo $query;

        if ($stmt = UbirimiContainer::get()['db.connection']->prepare($query)) {

            $stmt->bind_param("ii", $userId, $userId);
            $stmt->execute();
            $result = $stmt->get_result();
            if ($result->num_rows)
                return $result;
            else
                return null;
        }
    }

    public static function getDocumentatorActivityStream($userId) {
        // created pages
        $query = 'select documentator_entity.name, documentator_entity.id, \'created\' as action, documentator_entity.date_created as date ' .
                 'from documentator_entity ' .
                 'where documentator_entity.user_created_id = ? ';

        // created spaces
        $query .= ' UNION ' .
            'select documentator_space.name, documentator_space.id, \'created_space\' as action, documentator_space.date_created as date ' .
            'from documentator_space ' .
            'where documentator_space.user_created_id = ? ';

        // edited pages
        $query .= ' UNION ' .
                  'select documentator_entity.name, documentator_entity.id, \'edited\' as action, documentator_entity_revision.date_created as date ' .
                  'from documentator_entity_revision ' .
                  'left join documentator_entity on documentator_entity.id = documentator_entity_revision.entity_id ' .
                  'where documentator_entity_revision.user_id = ? ';

        // comments
        $query .= ' UNION ' .
                  'select documentator_entity.name, documentator_entity.id, \'comment\' as action, documentator_entity_comment.date_created as date ' .
                  'from documentator_entity_comment ' .
                  'left join documentator_entity on documentator_entity.id = documentator_entity_comment.documentator_entity_id ' .
                  'where documentator_entity_comment.user_id = ? ';

        $query .= 'order by date desc';

        if ($stmt = UbirimiContainer::get()['db.connection']->prepare($query)) {

            $stmt->bind_param("iiii", $userId, $userId, $userId, $userId);
            $stmt->execute();
            $result = $stmt->get_result();
            if ($result->num_rows)
                return $result;
            else
                return null;
        }
    }

    public static function getByEmailAddressAndIsClientAdministrator($emailAddress) {
        $query = 'select email, id from user where client_administrator_flag = 1 and LOWER(email) = ? limit 1';

        if ($stmt = UbirimiContainer::get()['db.connection']->prepare($query)) {
            $stmt->bind_param("s", $emailAddress);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows)
                return $result->num_rows;
            else {
                return null;
            }
        }
    }

    public static function getUserByClientIdAndEmailAddress($clientId, $email) {
        $query = 'select email, id from user where client_id = ? and LOWER(email) = ? limit 1';

        if ($stmt = UbirimiContainer::get()['db.connection']->prepare($query)) {
            $stmt->bind_param("is", $clientId, $email);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows)
                return $result->num_rows;
            else {
                return null;
            }
        }
    }

    public static function updateAvatar($avatar, $userId) {
        $query = 'UPDATE user SET avatar_picture = ? where id = ? limit 1';

        if ($stmt = UbirimiContainer::get()['db.connection']->prepare($query)) {
            $stmt->bind_param("si", $avatar, $userId);
            $stmt->execute();
        }
    }

    public static function getUserAvatarPicture($user, $size = null) {

        if (null !==  $user['avatar_picture'] && !empty($user['avatar_picture'])) {
            $pictureData = pathinfo($user['avatar_picture']);
            $fileName = $pictureData['filename'];
            $extension = $pictureData['extension'];

            if ($size == 'small') {
                return '/assets' . UbirimiContainer::get()['asset.user_avatar'] . $user['id'] . '/' . $fileName . '_33.' . $extension;
            } else if ($size == 'big') {
                return '/assets' . UbirimiContainer::get()['asset.user_avatar'] . $user['id'] . '/' . $fileName . '_150.' . $extension;
            }
        }

        return '/img/small_user.png';
    }

    public static function updateDisplayColumns($userId, $data) {
        $query = 'UPDATE user SET issues_display_columns = ? where id = ? limit 1';

        if ($stmt = UbirimiContainer::get()['db.connection']->prepare($query)) {
            $stmt->bind_param("si", $data, $userId);
            $stmt->execute();
        }
    }
}