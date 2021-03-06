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

namespace Ubirimi\Yongo\Repository\Issue;

use Ubirimi\Container\UbirimiContainer;

class IssueLinkType
{
    public function getByClientId($clientId) {
        $query = 'select * from yongo_issue_link_type where client_id = ?';

        $stmt = UbirimiContainer::get()['db.connection']->prepare($query);
        $stmt->bind_param("i", $clientId);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows)
            return $result;
        else
            return false;
    }

    public function getById($linkTypeId) {
        $query = 'select * from yongo_issue_link_type where id = ? limit 1';

        $stmt = UbirimiContainer::get()['db.connection']->prepare($query);
        $stmt->bind_param("i", $linkTypeId);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows)
            return $result->fetch_array(MYSQLI_ASSOC);
        else
            return false;
    }

    public function add($clientId, $name, $outwardDescription, $inwardDescription, $date) {
        $query = "INSERT INTO yongo_issue_link_type(client_id, name, outward_description, inward_description, date_created) " .
                 "VALUES (?, ?, ?, ?, ?)";

        $stmt = UbirimiContainer::get()['db.connection']->prepare($query);
        $stmt->bind_param("issss", $clientId, $name, $outwardDescription, $inwardDescription, $date);
        $stmt->execute();
    }

    public function getByNameAndClientId($clientId, $name, $linkTypeId = null) {
        $query = 'select * from yongo_issue_link_type where LOWER(name) = ? and client_id = ?';
        if ($linkTypeId)
            $query .= ' and id != ' . $linkTypeId;

        $stmt = UbirimiContainer::get()['db.connection']->prepare($query);
        $stmt->bind_param("si", $name, $clientId);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows)
            return $result;
        else
            return false;
    }

    public function update($linkTypeId, $name, $outwardDescription, $inwardDescription, $date) {
        $query = "update yongo_issue_link_type set name = ?, outward_description = ?, inward_description = ?, date_updated = ? " .
                 "where id = ? " .
                 "limit 1";

        $stmt = UbirimiContainer::get()['db.connection']->prepare($query);
        $stmt->bind_param("ssssi", $name, $outwardDescription, $inwardDescription, $date, $linkTypeId);
        $stmt->execute();
    }

    public function deleteById($sourceLinkTypeId) {
        $query = "delete from yongo_issue_link_type where id = ? limit 1";

        $stmt = UbirimiContainer::get()['db.connection']->prepare($query);
        $stmt->bind_param("i", $sourceLinkTypeId);
        $stmt->execute();
    }

    public function addLink($issueId, $linkTypeId, $type, $linkedIssues, $date) {
        for ($i = 0; $i < count($linkedIssues); $i++) {
            $query = "INSERT INTO yongo_issue_link(parent_issue_id, issue_link_type_id, link_type, child_issue_id, date_created) " .
                "VALUES (?, ?, ?, ?, ?)";

            $stmt = UbirimiContainer::get()['db.connection']->prepare($query);
            $childIssueId = $linkedIssues[$i];
            $stmt->bind_param("iisis", $issueId, $linkTypeId, $type, $childIssueId, $date);
            $stmt->execute();
        }
    }

    public function getLinksByParentId($issueId) {
        $query = 'select yongo_issue.id, yongo_issue.summary, yongo_issue.nr, yongo_issue_link_type.outward_description as outward_description, yongo_issue_link_type.inward_description as inward_description, ' .
            'yongo_issue_type.id as issue_type_id, yongo_issue_type.icon_name as issue_type_icon_name, yongo_issue_type.description as issue_type_description, yongo_issue_type.name as type, ' .
            'yongo_issue_priority.id as priority_id, yongo_issue_priority.color as priority_color, yongo_issue_priority.icon_name as issue_priority_icon_name, ' .
            'yongo_issue_priority.description as issue_priority_description, yongo_issue_priority.name as priority, ' .
            'yongo_issue_status.id as status, yongo_issue_status.name as status_name, ' .
            'yongo_issue_link.id as issue_link_id, yongo_issue_link.link_type, "parent" as view ' .
            'from yongo_issue_link ' .
            'left join yongo_issue_link_type on yongo_issue_link_type.id = yongo_issue_link.issue_link_type_id ' .
            'left join yongo_issue on yongo_issue.id = yongo_issue_link.child_issue_id ' .
            'LEFT join yongo_issue_priority on yongo_issue.priority_id = yongo_issue_priority.id ' .
            'LEFT join yongo_issue_type on yongo_issue.type_id = yongo_issue_type.id ' .
            'LEFT JOIN yongo_issue_status on yongo_issue.status_id = yongo_issue_status.id ' .
            'where parent_issue_id = ? ' .

            'UNION ' .

            'select yongo_issue.id, yongo_issue.summary, yongo_issue.nr, yongo_issue_link_type.outward_description as outward_description, yongo_issue_link_type.inward_description as inward_description, ' .
            'yongo_issue_type.id as issue_type_id, yongo_issue_type.icon_name as issue_type_icon_name, yongo_issue_type.description as issue_type_description, yongo_issue_type.name as type, ' .
            'yongo_issue_priority.id as priority_id, yongo_issue_priority.color as priority_color, yongo_issue_priority.icon_name as issue_priority_icon_name, ' .
            'yongo_issue_priority.description as issue_priority_description, yongo_issue_priority.name as priority, ' .
            'yongo_issue_status.id as status, yongo_issue_status.name as status_name, ' .
            'yongo_issue_link.id as issue_link_id, yongo_issue_link.link_type, "child" as view ' .
            'from yongo_issue_link ' .
            'left join yongo_issue_link_type on yongo_issue_link_type.id = yongo_issue_link.issue_link_type_id ' .
            'left join yongo_issue on yongo_issue.id = yongo_issue_link.parent_issue_id ' .
            'LEFT join yongo_issue_priority on yongo_issue.priority_id = yongo_issue_priority.id ' .
            'LEFT join yongo_issue_type on yongo_issue.type_id = yongo_issue_type.id ' .
            'LEFT JOIN yongo_issue_status on yongo_issue.status_id = yongo_issue_status.id ' .
            'where child_issue_id = ?';

        $stmt = UbirimiContainer::get()['db.connection']->prepare($query);
        $stmt->bind_param("ii", $issueId, $issueId);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows)
            return $result;
        else
            return false;
    }

    public function getByLinkTypeId($linkTypeId) {
        $query = 'select id ' .
            'from yongo_issue_link ' .
            'where issue_link_type_id = ?';

        $stmt = UbirimiContainer::get()['db.connection']->prepare($query);
        $stmt->bind_param("i", $linkTypeId);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows)
            return $result;
        else
            return false;
    }

    public function updateLinkTypeId($sourceLinkTypeId, $targetLinkTypeId) {
        $query = 'update yongo_issue_link set issue_link_type_id = ? where issue_link_type_id = ?';

        $stmt = UbirimiContainer::get()['db.connection']->prepare($query);
        $stmt->bind_param("ii", $targetLinkTypeId, $sourceLinkTypeId);
        $stmt->execute();
    }

    public function deleteLinksByLinkTypeId($sourceLinkTypeId) {
        $query = 'delete from yongo_issue_link where issue_link_type_id = ?';

        $stmt = UbirimiContainer::get()['db.connection']->prepare($query);
        $stmt->bind_param("i", $sourceLinkTypeId);
        $stmt->execute();
    }

    public function deleteLinkById($Id) {
        $query = 'delete from yongo_issue_link where id = ? limit 1';

        $stmt = UbirimiContainer::get()['db.connection']->prepare($query);
        $stmt->bind_param("i", $Id);
        $stmt->execute();
    }
}
