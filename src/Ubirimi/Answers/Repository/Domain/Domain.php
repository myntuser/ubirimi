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

namespace Ubirimi\Answers\Repository\Domain;

use Ubirimi\Container\UbirimiContainer;

class Domain
{
    public $name;
    public $description;
    public $clientId;

    function __construct($clientId = null, $name = null, $description = null) {
        $this->clientId = $clientId;
        $this->name = $name;
        $this->description = $description;

        return $this;
    }

    public function save($userCreatedId, $currentDate) {
        $query = "INSERT INTO ans_domain(client_id, name, description, user_created_id, date_created) VALUES (?, ?, ?, ?, ?)";

        $stmt = UbirimiContainer::get()['db.connection']->prepare($query);

        $stmt->bind_param("issis", $this->clientId, $this->name, $this->description, $userCreatedId, $currentDate);
        $stmt->execute();

        $domainId = UbirimiContainer::get()['db.connection']->insert_id;

        return $domainId;
    }

    public function getByClientId($clientId, $resultType = null) {
        $query = "select * " .
            "from ans_domain " .
            "where ans_domain.client_id = ?";

        $stmt = UbirimiContainer::get()['db.connection']->prepare($query);
        $stmt->bind_param("i", $clientId);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result->num_rows) {
            if ($resultType == 'array') {
                $resultArray = array();
                while ($board = $result->fetch_array(MYSQLI_ASSOC)) {
                    $resultArray[] = $board;
                }

                return $resultArray;
            } else
                return $result;
        } else
            return null;
    }

    public function updateMetadata($clientId, $boardId, $name, $description, $date) {
        $query = "update ans_domain set name = ?, description = ?, date_updated = ? where client_id = ? and id = ? limit 1";

        $stmt = UbirimiContainer::get()['db.connection']->prepare($query);
        $stmt->bind_param("sssii", $name, $description, $date, $clientId, $boardId);
        $stmt->execute();
    }

    public function getAll($filters = null) {
        $query = "select * " .
            "from ans_domain ";

        if (empty($filters['sort_by'])) {
            $query .= ' order by ans_domain.id';
        } else {
            $query .= " order by " . $filters['sort_by'] . ' ' . $filters['sort_order'];
        }

        $stmt = UbirimiContainer::get()['db.connection']->prepare($query);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result->num_rows) {
            return $result;
        } else {
            return null;
        }
    }
}