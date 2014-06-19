<?php

namespace Ubirimi;

use Symfony\Component\HttpFoundation\Cookie;
use Ubirimi\Container\UbirimiContainer;
use Ubirimi\Repository\Client;
use Ubirimi\Repository\Email\Email;
use Ubirimi\Repository\HelpDesk\SLA;
use Ubirimi\Repository\ServerSettings;
use Ubirimi\Repository\SMTPServer;
use Ubirimi\Repository\User\User;
use Ubirimi\Yongo\Repository\Field\Field;
use Ubirimi\Yongo\Repository\Issue\Issue;
use Ubirimi\Yongo\Repository\Issue\IssueAttachment;
use Ubirimi\Yongo\Repository\Permission\GlobalPermission;
use Ubirimi\Yongo\Repository\Permission\Permission;
use ZipArchive;

class Util {

    public static function renderContactSystemAdministrator() {
        echo '<div class="infoBox">Unauthorized access. Please contact the system administrator.</div>';
    }

    public static function userHasClientAdministrationPermission() {
        return UbirimiContainer::get()['session']->get('user/client_administrator_flag');
    }

    public static function userHasYongoAdministrativePermission() {
        $hasYongoGlobalAdministrationPermission = UbirimiContainer::get()['session']->get('user/yongo/is_global_administrator');
        $hasYongoGlobalSystemAdministrationPermission = UbirimiContainer::get()['session']->get('user/yongo/is_global_system_administrator');

        return ($hasYongoGlobalAdministrationPermission || $hasYongoGlobalSystemAdministrationPermission);
    }

    public static function userHasDocumentatorAdministrativePermission() {
        $hasDocumentatorGlobalAdministrationPermission = UbirimiContainer::get()['session']->get('user/documentator/is_global_administrator');
        $hasDocumentatorGlobalSystemAdministrationPermission = UbirimiContainer::get()['session']->get('user/documentator/is_global_system_administrator');

        return ($hasDocumentatorGlobalAdministrationPermission || $hasDocumentatorGlobalSystemAdministrationPermission);
    }

    public static function checkUserIsLoggedIn() {
        return null !== UbirimiContainer::get()['session']->get('user/id');
    }

    public static function signOutAndRedirect() {
        UbirimiContainer::get()['session']->invalidate();

        header('Location: /');
        exit;
    }

    public static function checkUserIsLoggedInAndRedirect() {

        if (!Util::checkUserIsLoggedIn()) {
            Util::signOutAndRedirect();
        }
    }

    public static function checkSuperUserIsLoggedIn() {

        if (!UbirimiContainer::get()['session']->get('user/id')) {
            header('Location: /');
            exit;
        } else if (!UbirimiContainer::get()['session']->get('user/super_user_flag')) {
            header('Location: /');
            exit;
        }
    }

    public static function getAsArray($mysqliResult, $fieldArray) {
        $resultArray = array();
        while ($result = $mysqliResult->fetch_array(MYSQLI_ASSOC)) {
            if (count($fieldArray) > 1) {
                $tempArray = array();
                for ($i = 0; $i < count($fieldArray); $i++) {
                    $tempArray[$fieldArray[$i]] = $result[$fieldArray[$i]];
                }
                $resultArray[] = $tempArray;
            } else {
                $resultArray[] = $result[$fieldArray[0]];
            }
        }

        return $resultArray;
    }

    public static function getCurrentDateTime($timezone = null) {
        if ($timezone)
            date_default_timezone_set($timezone);

        return date("Y-m-d H:i:s");
    }

    public static function renderPaginator($params, $returnType = null) {
        $htmlOutput = '<table width="100%" cellspacing="0" border="0" cellpadding="0">';
        $htmlOutput .= '<tr>';
        $htmlOutput .= '<td align="right">';

        $show_left = false;
        $show_left_left = false;
        $show_right = false;
        $show_right_right = false;
        $page_start = $params['page'] - 5;
        $page_end = $params['page'] + 5;

        if ($params['page'] == $params['count_pages']) {
            $page_start = $params['count_pages'] - 10;
            $page_end = $params['count_pages'];
        }

        if ($page_start <= 0) {
            $page_start = 1;
            $page_end = 10;
        }

        if ($page_start >= 2) $show_left = true;
        if ($page_start >= 3) $show_left_left = true;

        if ($page_end <= ($params['count_pages'] - 1)) $show_right = true;
        if ($page_end <= ($params['count_pages'] - 2)) $show_right_right = true;

        if ($page_end > $params['count_pages']) $page_end = $params['count_pages'];

        if ($show_left_left) {
            $params['page'] = 1;
            $htmlOutput .= LinkHelper::getYongoIssueListPageLink('<<', $params, null, 'nr_page_link');
        }

        if ($show_left) {
            $params['page'] = $page_start - 1;
            $htmlOutput .= LinkHelper::getYongoIssueListPageLink('<', $params, null, 'nr_page_link');
        }

        for ($i = $page_start; $i <= $page_end; $i++) {
            $params['page'] = $i;
            $htmlOutput .= LinkHelper::getYongoIssueListPageLink($i, $params, null, 'nr_page_link');
        }

        if ($show_right) {
            $params['page'] = $page_end + 1;
            $htmlOutput .= LinkHelper::getYongoIssueListPageLink('>', $params, null, 'nr_page_link');
        }

        if ($show_right_right) {
            $params['page'] = $params['count_pages'];
            $htmlOutput .= LinkHelper::getYongoIssueListPageLink('>>', $params, null, 'nr_page_link');
        }

        $htmlOutput .= '</td>';
        $htmlOutput .= '</tr>';
        $htmlOutput .= '</table>';

        if (isset($returnType)) {
            if ($returnType == 'echo')
                echo $htmlOutput;
            else if ($returnType == 'return')
                return $htmlOutput;
        } else {
            echo $htmlOutput;
        }
    }

    public static function isValidEmail($email) {
        return preg_match("/^[_\.0-9a-zA-Z-]+@([0-9a-zA-Z][0-9a-zA-Z-]+\.)+[a-zA-Z]{2,6}$/i", $email);
    }

    public static function getFormattedDate($date) {
        if (date('j/M/Y', strtotime($date)) == date('j/M/Y')) {
            return 'today ' . date('H:i', strtotime($date));
        }
        else {
            if ((date('j', strtotime($date)) + 1) == date('j') && date('M/Y', strtotime($date)) == date('M/Y')) {
                return 'yesterday';
            }
        }

        return date('j M Y', strtotime($date));
    }

    public static function cleanRegularInputField($value) {
        return strip_tags(trim($value));
    }

    public static function deepInArray($value, $array) {
        foreach ($array as $item) {
            if (!is_array($item)) {
                if ($item == $value) return true;
                else continue;
            }

            if (in_array($value, $item)) return true;
            else if (Util::deepInArray($value, $item)) return true;
        }
        return false;
    }

    public static function deleteDir($dirPath) {
        if (is_dir($dirPath)) {
            if (substr($dirPath, strlen($dirPath) - 1, 1) != '/') {
                $dirPath .= '/';
            }
            $files = glob($dirPath . '*', GLOB_MARK);
            foreach ($files as $file) {
                if (is_dir($file)) {
                    self::deleteDir($file);
                } else {
                    unlink($file);
                }
            }
            rmdir($dirPath);
        }
    }

    public static function manageModalAttachments($issueId, $loggedInUserId, $attachIdsToBeKept) {
        if (null != UbirimiContainer::get()['session']->has('added_attachments_in_screen')) {
            $attIdsSession = UbirimiContainer::get()['session']->get('added_attachments_in_screen');
            $uploadDirectory = Util::getAssetsFolder(SystemProduct::SYS_PRODUCT_YONGO) . $issueId;

            for ($i = 0; $i < count($attIdsSession); $i++) {
                $attachmentId = $attIdsSession[$i];
                $attachment = IssueAttachment::getById($attachmentId);

                if (!in_array($attachmentId, $attachIdsToBeKept)) {

                    // the attachment must be deleted
                    IssueAttachment::deleteById($attachmentId);

                    if (file_exists(Util::getAssetsFolder(SystemProduct::SYS_PRODUCT_YONGO) . $attachment['issue_id'] . '/' . $attachment['id'] . '/' . $attachment['name'])) {
                        unlink(Util::getAssetsFolder(SystemProduct::SYS_PRODUCT_YONGO) . $attachment['issue_id'] . '/' . $attachment['id'] . '/' . $attachment['name']);
                        if (Util::isImage(Util::getExtension($attachment['name']))) {
                            if (file_exists(Util::getAssetsFolder(SystemProduct::SYS_PRODUCT_YONGO) . $attachment['issue_id'] . '/' . $attachment['id'] . '/thumbs/' . $attachment['name'])) {
                                unlink(Util::getAssetsFolder(SystemProduct::SYS_PRODUCT_YONGO) . $attachment['issue_id'] . '/' . $attachment['id'] . '/thumbs/' . $attachment['name']);
                                Util::deleteDir(Util::getAssetsFolder(SystemProduct::SYS_PRODUCT_YONGO) . $attachment['issue_id'] . '/' . $attachment['id'] . '/thumbs');
                            }
                        }
                    }

                    if (file_exists(Util::getAssetsFolder(SystemProduct::SYS_PRODUCT_YONGO) . $attachment['issue_id'] . '/' . $attachment['id'])) {
                        Util::deleteDir(Util::getAssetsFolder(SystemProduct::SYS_PRODUCT_YONGO) . $attachment['issue_id'] . '/' . $attachment['id']);
                    }

                    // deal with the user_ folders
                    if (file_exists(Util::getAssetsFolder(SystemProduct::SYS_PRODUCT_YONGO) . 'user_' . $loggedInUserId . '/' . $attachment['id'] . '/' . $attachment['name'])) {
                        unlink(Util::getAssetsFolder(SystemProduct::SYS_PRODUCT_YONGO) . 'user_' . $loggedInUserId . '/' . $attachment['id'] . '/' . $attachment['name']);
                        if (Util::isImage(Util::getExtension($attachment['name']))) {
                            if (file_exists(Util::getAssetsFolder(SystemProduct::SYS_PRODUCT_YONGO) . 'user_' . $loggedInUserId . '/' . $attachment['id'] . '/thumbs/' . $attachment['name'])) {
                                unlink(Util::getAssetsFolder(SystemProduct::SYS_PRODUCT_YONGO) . 'user_' . $loggedInUserId . '/' . $attachment['id'] . '/thumbs/' . $attachment['name']);
                                Util::deleteDir(Util::getAssetsFolder(SystemProduct::SYS_PRODUCT_YONGO) . 'user_' . $loggedInUserId . '/' . $attachment['id'] . '/thumbs');
                            }
                        }
                    }

                    if (file_exists(Util::getAssetsFolder(SystemProduct::SYS_PRODUCT_YONGO) . 'user_' . $loggedInUserId . '/' . $attachment['id'])) {
                        Util::deleteDir(Util::getAssetsFolder(SystemProduct::SYS_PRODUCT_YONGO) . 'user_' . $loggedInUserId . '/' . $attachment['id']);
                    }

                } else {
                    if (file_exists(Util::getAssetsFolder(SystemProduct::SYS_PRODUCT_YONGO) . 'user_' . $loggedInUserId . '/' . $attachment['id'] . '/' . $attachment['name'])) {
                        if (!file_exists(Util::getAssetsFolder(SystemProduct::SYS_PRODUCT_YONGO) . $issueId)) {
                            // create the moder
                            mkdir(Util::getAssetsFolder(SystemProduct::SYS_PRODUCT_YONGO) . $issueId);
                        }
                        // move the attachment
                        rename(Util::getAssetsFolder(SystemProduct::SYS_PRODUCT_YONGO) . 'user_' . $loggedInUserId . '/' . $attachment['id'], Util::getAssetsFolder(SystemProduct::SYS_PRODUCT_YONGO) . $issueId . '/' . $attachment['id']);

                        // update the attachment
                        IssueAttachment::updateByIdAndIssueId($attachmentId, $issueId);
                    }
                }
            }

            if (file_exists($uploadDirectory) && count(scandir($uploadDirectory)) == 2) {
                Util::deleteDir($uploadDirectory);
            }

            if (file_exists(Util::getAssetsFolder(SystemProduct::SYS_PRODUCT_YONGO) . 'user_' . $loggedInUserId)) {
                Util::deleteDir(Util::getAssetsFolder(SystemProduct::SYS_PRODUCT_YONGO) . 'user_' . $loggedInUserId);
            }

            UbirimiContainer::get()['session']->remove('added_attachments_in_screen');
        }
    }

    public static function checkKeyAndValueInArray($key, $value, $fieldDataArray) {
        foreach ($fieldDataArray as $item)
            if (isset($item[$key]) && $item[$key] == $value)
                return $item;
        return false;
    }

    public static function getIssueHistory($issueId = null, $userId = null) {

        $query = '(select \'history_event\' as source, ' .
            'issue_history.date_created, ' .
            'issue_history.field as field, ' .
            'issue_history.old_value as old_value, ' .
            'issue_history.new_value as new_value, ' .
            'null as content, ' .
            'user.id as user_id, user.first_name, user.last_name, ' .
            'yongo_issue.nr as nr, ' .
            'project.code as code, ' .
            'yongo_issue.id as issue_id ' .
            'from issue_history ' .
            'left join user on user.id = issue_history.by_user_id ' .
            'left join yongo_issue on yongo_issue.id = issue_history.issue_id ' .
            'left join project on project.id = yongo_issue.project_id ' .
            'where ';

        if ($issueId) $query .= ' issue_history.issue_id = ' . $issueId . ' ';
        if ($userId) $query .= ' issue_history.by_user_id = ' . $userId . ' ';

        $query .= 'order by date_created desc, user_id) ';

        if ($stmt = UbirimiContainer::get()['db.connection']->prepare($query)) {

            $stmt->execute();
            $result = $stmt->get_result();
            if ($result->num_rows)
                return $result;
            else
                return null;
        }
    }

    public static function getProjectHistory($projectId, $helpdeskFlag = 0) {

        $queryWherePart = ' ';
        if ($helpdeskFlag) {
            $queryWherePart = ' and yongo_issue.helpdesk_flag = 1 ';
        }
        // issue created events
        $query = '(select ' .
            "'event_created' as event, " .
            'yongo_issue.date_created, ' .
            'null as field, ' .
            'null as new_value, ' .
            'user.id as user_id, user.first_name, user.last_name, ' .
            'yongo_issue.nr as nr, ' .
            'project.code as code, ' .
            'yongo_issue.id as issue_id, ' .
            'null as comment_content, ' .
            'user.avatar_picture ' .
            'from yongo_issue ' .
            'left join user on user.id = yongo_issue.user_reported_id ' .
            'left join project on project.id = yongo_issue.project_id ' .
            'where project.id = ? ' .
            $queryWherePart .
            'and yongo_issue.date_created BETWEEN (CURRENT_TIMESTAMP() - INTERVAL 1 MONTH) AND CURRENT_TIMESTAMP() ' .
            'order by date_created desc) ';

        // issue commented events
        $query .= 'UNION ' .

            '(select ' .
            "'event_commented' as event, " .
            'issue_comment.date_created, ' .
            'null as field, ' .
            'null as new_value, ' .
            'user.id as user_id, user.first_name, user.last_name, ' .
            'yongo_issue.nr as nr, ' .
            'project.code as code, ' .
            'yongo_issue.id as issue_id, ' .
            'issue_comment.content as comment_content, ' .
            'user.avatar_picture ' .
            'from yongo_issue ' .
            'left join issue_comment on yongo_issue.id = issue_comment.issue_id ' .
            'left join user on user.id = issue_comment.user_id ' .
            'left join project on project.id = yongo_issue.project_id ' .
            'where project.id = ? ' .
            $queryWherePart .
            'and issue_comment.issue_id is not null ' .
            'and issue_comment.date_created BETWEEN (CURRENT_TIMESTAMP() - INTERVAL 1 MONTH) AND CURRENT_TIMESTAMP() ' .
            'order by date_created desc, user_id) ';

        // issue history events
        $query .= 'UNION ' .

            '(select ' .
            "'event_history' as event, " .
            'issue_history.date_created, ' .
            'issue_history.field as field, ' .
            'issue_history.new_value, ' .
            'user.id as user_id, user.first_name as first_name, user.last_name as last_name, ' .
            'yongo_issue.nr as nr, ' .
            'project.code as code, ' .
            'yongo_issue.id as issue_id, ' .
            'null as comment_content, ' .
            'user.avatar_picture ' .
            'from yongo_issue ' .
            'left join issue_history on issue_history.issue_id = yongo_issue.id ' .
            'left join user on user.id = issue_history.by_user_id ' .
            'left join project on project.id = yongo_issue.project_id ' .
            'where project.id = ? ' .
            $queryWherePart .
            'and issue_history.issue_id is not null ' .
            'and issue_history.date_created BETWEEN (CURRENT_TIMESTAMP() - INTERVAL 1 MONTH) AND CURRENT_TIMESTAMP() ' .
            'order by date_created desc, user_id) ' .
            'order by date_created desc';

        if ($stmt = UbirimiContainer::get()['db.connection']->prepare($query)) {
            $stmt->bind_param("iii", $projectId, $projectId, $projectId);

            $stmt->execute();
            $result = $stmt->get_result();
            if ($result->num_rows)
                return $result;
            else
                return null;
        }
    }

    public static function createZip($files = array(), $destination, $overwrite = false) {
        //if the zip file already exists and overwrite is false, return false
        if (file_exists($destination) && !$overwrite) {

            return false;
        }

        //vars
        $valid_files = array();
        //if files were passed in...
        if (is_array($files)) {
            //cycle through each file
            foreach ($files as $file) {
                //make sure the file exists
                if (file_exists($file)) {
                    $valid_files[] = $file;
                }
            }
        }

        //if we have good files...
        if (count($valid_files)) {

            //create the archive
            $zip = new ZipArchive();
            if ($zip->open($destination, $overwrite ? ZIPARCHIVE::OVERWRITE : ZIPARCHIVE::CREATE) !== true) {
                return false;
            }
            //add the files
            foreach ($valid_files as $file) {
                $zip->addFile($file, basename($file));
            }
            $zip->close();

            return file_exists($destination);
        } else {
            return false;
        }
    }

    public static function getCountries() {
        $query = 'select id, name from sys_country';
        if ($stmt = UbirimiContainer::get()['db.connection']->prepare($query)) {

            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows)
                return $result;
            else
                return false;
        }
    }

    public static function updatePasswordForUserId($userId) {
        $query = 'update user set password = ? where id = ? limit 1';

        $pass = Util::randomPassword(8);

        $t_hasher = new PasswordHash(8, FALSE);
        $hash = $t_hasher->HashPassword($pass);

        if ($stmt = UbirimiContainer::get()['db.connection']->prepare($query)) {
            $stmt->bind_param("si", $hash, $userId);
            $stmt->execute();
        }

        return $pass;
    }

    public static function randomPassword($numchar) {
        $word = "a,b,c,d,e,f,g,h,i,j,k,l,m,1,2,3,4,5,6,7,8,9,0,A,B,C,D,E,F,G,H,I,J,K,L,M,N,O,P,Q,R,S,T,U,V,W,X,Y,Z";
        $array = explode(",",$word);
        shuffle($array);
        $newstring = implode($array, "");

        return substr($newstring, 0, $numchar);
    }

    public static function updatePasswordForClientAdministrator($userId) {
        $query = 'update user set password = ? where id = ? and client_administrator_flag = 1 limit 1';

        $pass = Util::randomPassword(8);

        $t_hasher = new PasswordHash(8, FALSE);
        $hash = $t_hasher->HashPassword($pass);

        if ($stmt = UbirimiContainer::get()['db.connection']->prepare($query)) {
            $stmt->bind_param("si", $hash, $userId);
            $stmt->execute();
        }

        return $pass;
    }

    public static function checkEmailAddressExistenceWithinClient($address, $userId, $clientId) {
        $query = 'select id, email from user where LOWER(email) = LOWER(?) and id != ? and client_id = ?';

        if ($stmt = UbirimiContainer::get()['db.connection']->prepare($query)) {
            $stmt->bind_param("sii", $address, $userId, $clientId);

            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows) {
                return $result->fetch_array();
            }
        }
    }

    public static function checkEmailAddressExistence($address, $userId = null, $clientId = null) {
        $query = 'select id, email from user where LOWER(email) = LOWER(?)';
        if ($userId) $query .= ' and id != ?';

        if ($clientId) $query .= ' and client_id != ' . $clientId;

        if ($stmt = UbirimiContainer::get()['db.connection']->prepare($query)) {
            if ($userId)
                $stmt->bind_param("si", $address, $userId);
            else
                $stmt->bind_param("s", $address);

            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows)
                return $result->fetch_array();
        }

        $query = 'select id, contact_email from client where LOWER(contact_email) = LOWER(?) ';
        if ($clientId) $query .= ' and id != ?';

        if ($stmt = UbirimiContainer::get()['db.connection']->prepare($query)) {
            if ($clientId) $stmt->bind_param("si", $address, $clientId);
            else
                $stmt->bind_param("s", $address);

            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows)
                return $result->fetch_array();
        }

        return false;
    }

    public static function hasNoErrors($errors) {
        foreach ($errors as $key => $value)
            if ($value)
                return false;

        return true;
    }

    public static function renderTableHeader($parameters, $columns) {

        // prepare the data
        foreach ($parameters as $key => $value) {
            if (is_array($value))
                $parameters[$key] = implode('|', $value);
            else if ($value === null) {
                unset($parameters[$key]);
            }
        }

        if (isset($parameters['sort_order'])) {
            switch ($parameters['sort_order']) {
                case null:
                    $parameters['sort_order'] = 'asc';
                    break;
                case 'asc':
                    $parameters['sort_order'] = 'desc';
                    break;
                case 'desc':
                    $parameters['sort_order'] = 'asc';
                    break;
            }
        }
        $content = '<thead>';
        $content .= '<tr>';
        if (isset($parameters['render_checkbox']) && $parameters['render_checkbox']) {
            if (isset($parameters['checkbox_in_header'])) {
                $content .= '<th align="center"><input type="checkbox" value="1" name="header_checkbox" id="header_checkbox" /></th>';
            } else {
                $content .= '<th></th>';
            }
        }

        foreach ($columns as $column) {
            $class = null;

            if ($column == 'settings_menu') {
                continue;
            }

            if (isset($parameters['sort']) && $parameters['sort'] == $column) $class = 'cellHeaderSelected';
            $URLParameters = '';
            if (isset($parameters['page'])) {
                $URLParameters .= '?page=' . $parameters['page'];

                if (isset($parameters['sort'])) {
                    $URLParameters .= '&sort=' . $column . '&order=' . $parameters['sort_order'];
                }
            }

            if (isset($parameters['filter'])) $URLParameters .= '&filter=' . $parameters['filter'];
            if (isset($parameters['component'])) $URLParameters .= '&component=' . $parameters['component'];
            if (isset($parameters['version'])) $URLParameters .= '&version=' . $parameters['version'];
            if (isset($parameters['project'])) $URLParameters .= '&project=' . $parameters['project'];
            if (isset($parameters['priority'])) $URLParameters .= '&priority=' . $parameters['priority'];
            if (isset($parameters['type'])) $URLParameters .= '&type=' . $parameters['type'];
            if (isset($parameters['status'])) $URLParameters .= '&status=' . $parameters['status'];
            if (isset($parameters['resolution'])) $URLParameters .= '&resolution=' . $parameters['resolution'];
            if (isset($parameters['assignee'])) $URLParameters .= '&assignee=' . $parameters['assignee'];
            if (isset($parameters['reporter'])) $URLParameters .= '&reporter=' . $parameters['reporter'];
            if (isset($parameters['search_query'])) $URLParameters .= '&search_query=' . $parameters['search_query'];
            if (isset($parameters['summary_flag'])) $URLParameters .= '&summary_flag=' . $parameters['summary_flag'];
            if (isset($parameters['description_flag'])) $URLParameters .= '&description_flag=' . $parameters['description_flag'];
            if (isset($parameters['comments_flag'])) $URLParameters .= '&comments_flag=' . $parameters['comments_flag'];
            if (isset($parameters['for_queue'])) $URLParameters .= '&for_queue=' . $parameters['for_queue'];

            $columnWidth = null;
            $align = null;
            if ($column == 'priority') {
                $columnName = 'P';
                $columnWidth = 20;
                $align = 'align="center"';
            } elseif (substr($column, 0, 4) == 'sla_') {
                $slaColumn = SLA::getById(str_replace('sla_', '', $column));
                $columnName = $slaColumn['name'];
            } else {
                $columnName = str_replace("_", ' ', ucfirst($column));
            }
            if (isset($parameters['sort']) && $parameters['sort']) {

                $linkHeader = '<a class="linkInHeader" href="' . $parameters['link_to_page'] . $URLParameters . '">' . $columnName . '</a>';
            } else
                $linkHeader = '<span class="linkInHeader">' . $columnName . '</span>';

            $columnWidthHTML = '';
            if ($columnWidth)
                $columnWidthHTML = ' width="' . $columnWidth . '" ';

            $content .= '<th ' . $align;
            if ($class) {
                $content .= ' class="' . $class . '"';
            }
            $content .=  $columnWidthHTML . '>';
            $content .= $linkHeader;
            $content .= '</th>';
        }

        $content .= '</tr>';
        $content .= '</thead>';
        return $content;
    }

    public static function renderIssueTables($params, $columns) {
        $htmlOutput = '';
        $htmlOutput .= '<div>';
        $issues = $params['issues'];

        $atLeastOneIssueRendered = false;
        if ($issues && $issues->num_rows) {
            $htmlOutput .= '<table width="100%" id="table_list" class="table table-hover table-condensed">';
            if (isset($params['show_header']) && $params['show_header']) {
                $htmlOutput .= Util::renderTableHeader($params, $columns);
            } else {
                $htmlOutput .= '<tr></tr>'; // hack ca sa mearga selectul pe prima linie
            }
            $disabledText = '';
            if (isset($params['checkbox_disabled']) && $params['checkbox_disabled'])
                $disabledText = 'disabled="disabled"';

            while ($issue = $issues->fetch_array(MYSQLI_ASSOC)) {
                $atLeastOneIssueRendered = true;
                $htmlOutput .= '<tr';
                if ($params['render_checkbox']) {
                    $htmlOutput .= ' id="table_row_' . $issue['id'];
                }
                $htmlOutput .= '">';

                if ($params['render_checkbox']) {
                    $htmlOutput .= '<td width="22">';
                    $checkId = 'el_check_' . $issue['id'];

                    $htmlOutput .= '<input ' . $disabledText . ' type="checkbox" value="1" id="' . $checkId . '"/>';
                    if (array_key_exists('in_backlog', $params))
                        $htmlOutput .= '<input type="hidden" value="1" id="backlog_' . $checkId . '"/>';
                    else
                        $htmlOutput .= '<input type="hidden" value="0" id="backlog_' . $checkId . '"/>';

                    $htmlOutput .= '</td>';
                }

                for ($indexColumn = 0; $indexColumn < count($columns); $indexColumn++) {
                    if ($columns[$indexColumn] == 'code') {
                        $htmlOutput .= '<td class="issuePC">';
                        $htmlOutput .= '<img title="' . $issue['type'] . ' - ' . $issue['issue_type_description'] . '" height="16px" src="/yongo/img/issue_type/' . $issue['issue_type_icon_name'] . '" /> ';
                        $htmlOutput .= '<a href="/yongo/issue/' . $issue['id'] . '">' . $issue['project_code'] . '-' . $issue['nr'] . '</a>';
                        $htmlOutput .= "</td>\n";
                    }

                    if ($columns[$indexColumn] == 'summary')
                        $htmlOutput .= '<td class="issueSummary"><a href="/yongo/issue/' . $issue['id'] . '">' . htmlentities($issue['summary']) . "</a></td>\n";

                    if ($columns[$indexColumn] == 'priority') {
                        $htmlOutput .= '<td class="issuePriority">' . "\n";
                        $htmlOutput .= '<img title="' . $issue['priority'] . ' - ' . $issue['issue_priority_description'] . '" height="16px" src="/yongo/img/issue_priority/' . $issue['issue_priority_icon_name'] . '" />';
                        $htmlOutput .= "</td>\n";
                    }

                    if ($columns[$indexColumn] == 'status')
                        $htmlOutput .= '<td class=" issueStatus">' . $issue['status_name'] . '</td>';
                    if ($columns[$indexColumn] == 'date_created')
                        $htmlOutput .= '<td class="issueDC">' . Util::getFormattedDate($issue['date_created']) . "</td>\n";
                    if ($columns[$indexColumn] == 'date_updated') {
                        $htmlOutput .= '<td class="issueDU">';
                        if ($issue['date_updated'])
                            $htmlOutput .= Util::getFormattedDate($issue['date_updated']);
                        $htmlOutput .= '</td>';
                    }
                    if ($columns[$indexColumn] == 'reporter')
                        $htmlOutput .= '<td class="issueUR">' . LinkHelper::getUserProfileLink($issue[Field::FIELD_REPORTER_CODE], SystemProduct::SYS_PRODUCT_YONGO, $issue['ur_first_name'], $issue['ur_last_name']) . '</td>';

                    if ($columns[$indexColumn] == 'assignee') {
                        $htmlOutput .= '<td class="issueUA">' . LinkHelper::getUserProfileLink($issue[Field::FIELD_ASSIGNEE_CODE], SystemProduct::SYS_PRODUCT_YONGO, $issue['ua_first_name'], $issue['ua_last_name']) . '</td>';
                    }
                    if ($columns[$indexColumn] == 'resolution') {
                        $htmlOutput .= '<td>' . $issue['resolution_name'] . '</td>';
                    }
                    if ($columns[$indexColumn] == 'updated') {
                        $htmlOutput .= '<td>' . $issue['date_updated'] . '</td>';
                    }
                    if ($columns[$indexColumn] == 'created') {
                        $htmlOutput .= '<td>' . $issue['date_created'] . '</td>';
                    }
                }

                $htmlOutput .= '</tr>';
            }

            $htmlOutput .= '</table>';
        }
        $htmlOutput .= '</div>';

        echo $htmlOutput;
        return $atLeastOneIssueRendered;
    }

    public static function renderComponentStatSection($statsSection, $count, $title, $type = null, $projectId = null, $componentId = null, $versionId = null, $urlPrefix) {
        ?>
        <table width="100%" cellpadding="2">
            <tr>
                <td colspan="3" class="sectionDetail"><span class="sectionDetailSimple headerPageText"><?php echo $title ?></span></td>
            </tr>
            <?php if (!$count): ?>
                <tr>
                    <td>No Issues</td>
                </tr>
            <?php endif ?>

            <?php foreach ($statsSection as $key => $value): ?>
                <tr>
                    <td width="180px">
                        <?php
                            switch ($type) {
                                case 'assignee':
                                    echo LinkHelper::getYongoIssueListPageLink(key($value), array('page' => 1, 'assignee' => $key, 'link_to_page' => $urlPrefix, 'sort' => 'created', 'sort_order' => 'desc', 'resolution' => '-2', 'project' => $projectId, 'component' => $componentId, 'fix_version' => $versionId));
                                    break;
                                case 'type':
                                    echo LinkHelper::getYongoIssueListPageLink(key($value), array('page' => 1, 'link_to_page' => $urlPrefix, 'sort' => 'created', 'resolution' => '-2', 'project' => $projectId, 'sort_order' => 'desc', 'type' => $key, 'component' => $componentId, 'fix_version' => $versionId));
                                    break;
                                case 'priority':
                                    echo LinkHelper::getYongoIssueListPageLink(key($value), array('page' => 1, 'link_to_page' => $urlPrefix, 'sort' => 'created', 'resolution' => '-2', 'project' => $projectId, 'sort_order' => 'desc', 'priority' => $key, 'component' => $componentId, 'fix_version' => $versionId));
                                    break;
                                case 'status':
                                    echo LinkHelper::getYongoIssueListPageLink(key($value), array('page' => 1, 'link_to_page' => $urlPrefix, 'sort' => 'created', 'resolution' => -2, 'status' => $key, 'project' => $projectId, 'sort_order' => 'desc', 'component' => $componentId, 'fix_version' => $versionId));
                                    break;
                            }
                        ?>
                    </td>
                    <td width="30" align="right"><?php echo $value[key($value)] ?></td>
                    <?php $perc = round($value[key($value)] / $count * 100) ?>
                    <td valign="bottom">
                        <div style="margin-top: 5px; margin-right: 4px; float:left; background-color: #56A5EC; height: 18px; width: <?php echo($perc * 4) ?>px"></div>
                        <div style="float: left"><?php echo $perc ?>%</div>
                    </td>
                </tr>
            <?php endforeach ?>
        </table>
    <?php
    }

    public static function getSubdomain() {
        $httpHost = $_SERVER['SERVER_NAME'];
        $parameters = explode(".", $httpHost);

        return array_shift($parameters);
    }

    public static function runsOnLocalhost() {

        return in_array('lan', explode('.', $_SERVER['HTTP_HOST']));
    }

    /**
     * Modifies a string to remove all non ASCII characters and spaces.
     */

    public static function slugify($text) {
        // replace non letter or digits by -
        $text = preg_replace('~[^\\pL\d]+~u', '-', $text);

        // trim
        $text = trim($text, '-');

        // transliterate
        if (function_exists('iconv')) {
            $text = iconv('utf-8', 'us-ascii//TRANSLIT', $text);
        }

        // lowercase
        $text = mb_strtolower($text);

        // remove unwanted characters
        $text = preg_replace('~[^-\w]+~', '', $text);

        if (empty($text)) {
            return 'n-a';
        }

        return $text;
    }

    public static function array_column($array, $column) {
        $ret = array();
        foreach ($array as $row)
            $ret[] = $row[$column];

        return $ret;
    }

    public static function searchQueryNotEmpty($getSearchParameters) {
        $paramsToSearch = array('search_query', 'summary_flag', 'description_flag', 'comments_flag', 'project',
            'assignee', 'reporter', 'type', 'status', 'priority', 'component',
            'fix_version', 'affects_version', 'resolution');

        for ($i = 0; $i < count($paramsToSearch); $i++) {
            if (isset($getSearchParameters[$paramsToSearch[$i]]) && $getSearchParameters[$paramsToSearch[$i]] != null)
                return true;
        }

        return false;
    }

    public static function validateUsername($username) {
        return preg_match('/^[A-Za-z0-9_]{1,35}$/', $username);
    }

    public static function renderBreadCrumb($htmlBreadCrumb, $iconRight = null, $link = null) {
        $html = '<table width="100%" class="headerPageBackground">';
            $html .= '<tr>';
                $html .= '<td>';
                    $html .= '<div class="headerPageText">' . $htmlBreadCrumb . '</div>';
                $html .= '</td>';
                if ($iconRight) {
                    $html .= '<td align="right">';
                        if ($iconRight == 'help')
                            $html .= '<a target="_blank" href="' . $link . '"><img src="/img/help_browser.png" /></a>';
                    $html .= '</td>';
                }
            $html .= '</tr>';
        $html .= '</table>';

        echo $html;
    }

    public static function transformLogTimeToMinutes($timeString, $hoursPerDay, $daysPerWeek) {

        $timeCodes = array('w', 'd', 'h', 'm');
        $amount = '';
        $totalMinutes = 0;
        $size = strlen($timeString);

        for ($i = 0; $i < $size; $i++) {

            if (!in_array($timeString[$i], $timeCodes)) {
                $amount .= $timeString[$i];
            } else {

                switch ($timeString[$i]) {
                    case 'w':
                        $totalMinutes += $amount * $daysPerWeek * $hoursPerDay * 60;
                        break;
                    case 'd':
                        $totalMinutes += $amount * $hoursPerDay * 60;
                        break;
                    case 'h':
                        $totalMinutes += $amount * 60;
                        break;
                    case 'm':
                        $totalMinutes += $amount;
                        break;
                }

                $amount = '';
            }
        }

        return $totalMinutes;
    }

    public static function transformTimeToString($minutes, $hoursPerDay, $daysPerWeek, $format = 'long') {
        $seconds = $minutes * 60;

        $weeks = floor($seconds / 3600 / $hoursPerDay / $daysPerWeek);
        $days = floor($seconds / 60 / 60 / $hoursPerDay) - ($weeks * $daysPerWeek);

        $hours = floor($seconds / 60 / 60) - ($weeks * $daysPerWeek * $hoursPerDay) - $days * $hoursPerDay;
        $minutes = ($seconds / 60) % 60;

        $timeStringData = array();
        if ($weeks > 0) {
            if ($weeks > 1)
                $timeStringData[] = $weeks . ' weeks';
            else
                $timeStringData[] = $weeks . ' week';
        }
        if ($days > 0) {
            if ($days > 1)
                $timeStringData[] = $days . ' days';
            else
                $timeStringData[] = $days . ' day';
        }
        if ($hours > 0) {
            if ($hours > 1)
                $timeStringData[] = $hours . ' hours';
            else
                $timeStringData[] = $hours . ' hour';
        }
        if ($minutes > 0) {
            if ($minutes > 1)
                $timeStringData[] = $minutes . ' minutes';
            else
                $timeStringData[] = $minutes . ' minute';
        }

        if ($format == 'short') {
            for ($i = 0; $i < count($timeStringData); $i++) {
                $timeStringData[$i] = str_replace(array('weeks', 'week'), 'w', $timeStringData[$i]);
                $timeStringData[$i] = str_replace(array('days', 'day'), 'd', $timeStringData[$i]);
                $timeStringData[$i] = str_replace(array('hours', 'hour'), 'h', $timeStringData[$i]);
                $timeStringData[$i] = str_replace(array('minutes', 'minute'), 'm', $timeStringData[$i]);
                $timeStringData[$i] = str_replace(' ', '', $timeStringData[$i]);
            }
        }

        return implode(', ', $timeStringData);
    }

    public static function getHttpHost() {
        $httpHOST = $_SERVER['HTTP_HOST'];
        if (Util::runsOnLocalhost()) {
            $urlPrefix = 'http://';
        } else if (!empty($_SERVER['HTTPS'])) {
            $urlPrefix = 'https://';
        } else {
            $urlPrefix = 'http://';
        }
        
        if (isset($urlData['scheme']))
            $urlPrefix = '';
        $httpHOST = $urlPrefix . $httpHOST;

        return $httpHOST;
    }

    public static function removeDirectory($path) {
        if (is_dir($path)) {
            $objects = scandir($path);
            foreach ($objects as $object) {
                if ($object != "." && $object != "..") {
                    if (filetype($path . "/" . $object) == "dir") {
                        Util::removeDirectory($path . "/" . $object);
                    } else {
                        unlink($path . "/" . $object);
                    }
                }
                reset($objects);
                rmdir($path);
            }
        }
    }

    public static function getExtension($str) {
        $i = strrpos($str, ".");
        if (!$i) {
            return "";
        }

        $l = strlen($str) - $i;

        return strtolower(substr($str, $i + 1, $l));
    }

    public static function isImage($extension) {

        return in_array($extension, array('png', 'gif', 'jpg', 'jpeg', 'bmp'));
    }

    public static function get_include_contents($filename) {
        global $clientId;
        global $session;
        if (is_file($filename)) {
            ob_start();
            require_once $filename;
            return ob_get_clean();
        }
        return false;
    }

    public static function renderMaintenanceMessage() {
        // get the server settings
        $serverSettings = ServerSettings::get();

        if ($serverSettings) {
            echo '<div class="warningMessage">' . $serverSettings['maintenance_server_message'] . '</div>';
        }
    }

    public static function getUbirimiSMTPSettings($type = 'accounts') {
        switch ($type) {
            case 'accounts':
                return array('smtp_protocol' => SMTPServer::PROTOCOL_SECURE_SMTP,
                             'email_prefix' => '',
                             'username' => 'accounts@ubirimi.com',
                             'from_address' => 'accounts@ubirimi.com',
                             'password' => 'scumpamea',
                             'hostname' => 'smtp.gmail.com',
                             'tls_flag' => true,
                             'port' => 587);
                break;

            case 'contact':
                return array('smtp_protocol' => SMTPServer::PROTOCOL_SECURE_SMTP,
                             'email_prefix' => '',
                             'username' => 'contact@ubirimi.com',
                             'from_address' => 'contact@ubirimi.com',
                             'password' => 'scumpamea',
                             'hostname' => 'smtp.gmail.com',
                             'tls_flag' => true,
                             'port' => 587);
                break;
        }
    }

    public static function getUbirmiMailer($type = 'accounts') {
        return Email::getMailer(self::getUbirimiSMTPSettings($type));
    }

    public static function getAssetsFolder($productId, $context = null) {
        switch ($productId) {
            case SystemProduct::SYS_PRODUCT_GENERAL_SETTINGS:
                if ($context == 'user_avatars') {
                    return UbirimiContainer::get()['asset.user_avatar'];
                }
                break;

            case SystemProduct::SYS_PRODUCT_YONGO:
                return UbirimiContainer::get()['asset.root_folder'] . UbirimiContainer::get()['asset.yongo_issue_attachments'];
                break;

            case SystemProduct::SYS_PRODUCT_DOCUMENTADOR:
                if ($context == 'attachments') {
                    return UbirimiContainer::get()['asset.root_folder'] . UbirimiContainer::get()['asset.documentador_entity_attachments'];
                } else if ($context == 'filelists') {
                    return UbirimiContainer::get()['asset.root_folder'] . UbirimiContainer::get()['asset.documentador_filelists_attachments'];
                }
                break;
        }
    }

    public static function drawYongoProjectCalendar($projectId, $month, $year, $userId) {

        /* draw table */
        $calendar = '<table cellpadding="0" cellspacing="0" class="calendar">';

        /* table headings */
        $headings = array('Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday');
        $calendar .= '<tr>';
            $calendar .= '<td class="calendar-day-head">' . implode('</td><td class="calendar-day-head">', $headings) . '</td>';
        $calendar .= '</tr>';

        /* days and weeks vars now ... */
        $running_day = date('w', mktime(0, 0, 0, $month, 1, $year));
        $days_in_month = date('t', mktime(0, 0, 0, $month, 1, $year));
        $days_in_this_week = 1;
        $day_counter = 0;
        $dates_array = array();

        /* row for week one */
        $calendar .= '<tr class="calendar-row">';

        /* print "blank" days until the first of the current week */
        for ($x = 0; $x < $running_day; $x++) {
            $calendar .= '<td class="calendar-day-np"> </td>';
            $days_in_this_week++;
        }

        /* keep going with days.... */
        for ($list_day = 1; $list_day <= $days_in_month; $list_day++) {
            $calendar .= '<td class="calendar-day" valign="top">';
            /* add in the day number */
            $calendar .= '<div class="day-number">' . $list_day . '</div>';
            $dayNr = $list_day;
            if ($list_day < 10)
                $dayNr = '0' . $list_day;
            $date = $year . '-' . $month . '-' . $dayNr;

            /** QUERY THE DATABASE FOR AN ENTRY FOR THIS DAY !!  IF MATCHES FOUND, PRINT THEM !! **/
            $issueQueryParameters = array('date_due' => $date, 'project' => $projectId);

            $issues = Issue::getByParameters($issueQueryParameters, $userId);
            if ($issues) {
                $calendar .= '<div>Issues: <a href="/yongo/issue/search?project=' . $projectId . '&date_due_after=' . $date . '&date_due_before=' . $date . '">' . $issues->num_rows . '</a></div>';

                while ($issue = $issues->fetch_array(MYSQLI_ASSOC)) {
                    $calendar .= '<a class="calendar-issue-box" href="' . LinkHelper::getYongoIssueViewLinkJustHref($issue['id']) . '">';
                        $calendar .= '<span style="width: 19px; margin-right: 4px; margin-bottom: 4px; display: inline-block; background-color: ' . $issue['priority_color'] . '">&nbsp;</span>';
                        $calendar .= '<div style="display: none" class="calendar-issue-box-content">';
                            $calendar .= '<a href="/yongo/issue/' . $issue['id'] . '">' . $issue['project_code'] . '-' . $issue['nr'] . '</a> ' . $issue['summary'];
                            $calendar .= '<div>' . str_replace("\n", "<br />", $issue['description']) . '</div>';
                        $calendar .= '</div>';
                    $calendar .= '</a>';
                }
            }

            $calendar .= str_repeat('<p> </p>', 2);

            $calendar .= '</td>';
            if ($running_day == 6) {
                $calendar .= '</tr>';
                if (($day_counter + 1) != $days_in_month) {
                    $calendar .= '<tr class="calendar-row">';
                }
                $running_day = -1;
                $days_in_this_week = 0;
            }
            $days_in_this_week++;
            $running_day++;
            $day_counter++;
        }

        /* finish the rest of the days in the week */

        if ($days_in_this_week < 8 && (8 - $days_in_this_week != 7)) {
            for ($x = 1; $x <= (8 - $days_in_this_week); $x++) {
                $calendar .= '<td class="calendar-day-np"> </td>';
            }
        }

        /* final row */
        $calendar .= '</tr>';

        /* end the table */
        $calendar .= '</table>';

        /* all done, return result */
        return $calendar;
    }

    public static function getTemplate($templatePath, $data) {
        $tpl = UbirimiContainer::get()['savant'];
        $tpl->assign($data);

        return $tpl->fetch($templatePath, 'text/html');
    }

    public static function includePartial($partialPath, array $variables) {
        ob_start();

        extract($variables);

        require_once $partialPath;

        ob_end_flush();
    }
}