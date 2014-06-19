<?php

namespace Ubirimi\Repository\Group;
use Ubirimi\Container\UbirimiContainer;
use Ubirimi\SystemProduct;

class Group {

    public static function getByName($clientId, $name) {
        $query = 'select * from `group` where client_id = ? and name = ? limit 1';

        if ($stmt = UbirimiContainer::get()['db.connection']->prepare($query)) {
            $stmt->bind_param("is", $clientId, $name);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows)
                return $result->fetch_array(MYSQLI_ASSOC);
            else
                return false;
        }
    }

    public static function addDefaultYongoGroups($clientId, $date) {
        $query = "INSERT INTO `group`(client_id, sys_product_id, name, description, date_created) VALUES (?, ?, ?, ?, ?), (?, ?, ?, ?, ?), (?, ?, ?, ?, ?)";

        if ($stmt = UbirimiContainer::get()['db.connection']->prepare($query)) {
            $group_name_1 = 'Administrators';
            $group_name_2 = 'Developers';
            $group_name_3 = 'Users';
            $group_descr_1 = 'The users in this group will have all the privileges';
            $group_descr_2 = 'The users in this group will have some privileges';
            $group_descr_3 = 'The users in this group will have basic privileges';

            $productId = SystemProduct::SYS_PRODUCT_YONGO;
            $stmt->bind_param("iisssiisssiisss", $clientId, $productId, $group_name_1, $group_descr_1, $date, $clientId, $productId, $group_name_2, $group_descr_2, $date, $clientId, $productId, $group_name_3, $group_descr_3, $date);

            $stmt->execute();
        }
    }

    public static function getByNameAndProductId($clientId, $productId, $name, $groupId = null) {
        $query = 'select id, name from `group` where client_id = ? and sys_product_id = ? and lower(name) = ?';
        if ($groupId)
            $query .= ' and id != ' . $groupId;

        if ($stmt = UbirimiContainer::get()['db.connection']->prepare($query)) {
            $stmt->bind_param("iis", $clientId, $productId, $name);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows)
                return $result->fetch_array(MYSQLI_ASSOC);
            else
                return false;
        }
    }

    public static function getByUserIdAndProductId($userId, $productId) {
        $query = 'select group.name, group.id ' .
            'from group_data ' .
            'left join `group` on group.id = group_data.group_id ' .
            'where group_data.user_id = ? and ' .
            '`group`.sys_product_id = ? ' .
            'order by `group`.name';

        if ($stmt = UbirimiContainer::get()['db.connection']->prepare($query)) {
            $stmt->bind_param("ii", $userId, $productId);
            $stmt->execute();
            $result = $stmt->get_result();
            if ($result->num_rows)
                return $result;
            else
                return null;
        }
    }

    public static function getByClientIdAndProductId($clientId, $productId) {

        $query = 'SELECT * FROM `group` where client_id = ? and sys_product_id = ? order by `group`.name';

        if ($stmt = UbirimiContainer::get()['db.connection']->prepare($query)) {
            $stmt->bind_param("ii", $clientId, $productId);
            $stmt->execute();
            $result = $stmt->get_result();
            if ($result->num_rows)
                return $result;
            else
                return null;
        }
    }

    public static function getByClientId($clientId) {

        $query = 'SELECT * FROM `group` where client_id = ?';

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

    public static function addData($groupId, $userArray, $currentDate) {
        $query = 'insert into group_data(group_id, user_id, date_created) values ';

        for ($i = 0; $i < count($userArray); $i++)
            $query .= '(' . $groupId . ' ,' . $userArray[$i] . ",'" . $currentDate . "'), ";

        $query = substr($query, 0, strlen($query) - 2);
        UbirimiContainer::get()['db.connection']->query($query);
    }

    public static function add($clientId, $productId, $name, $description, $currentDate) {
        $query = "INSERT INTO `group`(client_id, sys_product_id, name, description, date_created) VALUES (?, ?, ?, ?, ?)";

        if ($stmt = UbirimiContainer::get()['db.connection']->prepare($query)) {
            $stmt->bind_param("iisss", $clientId, $productId, $name, $description, $currentDate);
            $stmt->execute();
        }
    }

    public static function getMetadataById($Id) {
        $query = 'SELECT ' .
            'id, name, description, client_id ' .
            'FROM `group` ' .
            'where id = ?';

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

    public static function updateById($Id, $name, $description, $date) {
        $query = "update `group` set name = ?, description = ?, date_updated = ? where id = ?";

        if ($stmt = UbirimiContainer::get()['db.connection']->prepare($query)) {
            $stmt->bind_param("sssi", $name, $description, $date, $Id);
            $stmt->execute();
        }
    }

    public static function getDataByGroupId($groupId) {
        $query = 'select group_data.id, group_data.user_id, user.first_name, user.last_name ' .
            'from group_data ' .
            'left join user on user.id = group_data.user_id ' .
            'where group_data.group_id = ?';

        if ($stmt = UbirimiContainer::get()['db.connection']->prepare($query)) {
            $stmt->bind_param("i", $groupId);
            $stmt->execute();
            $result = $stmt->get_result();
            if ($result->num_rows)
                return $result;
            else
                return null;
        }
    }

    public static function deleteDataByGroupId($groupId) {
        $query = "SET FOREIGN_KEY_CHECKS = 0;";
        UbirimiContainer::get()['db.connection']->query($query);

        $query = 'DELETE from group_data where group_id = ?';

        if ($stmt = UbirimiContainer::get()['db.connection']->prepare($query)) {
            $stmt->bind_param("i", $groupId);
            $stmt->execute();
        }

        $query = "SET FOREIGN_KEY_CHECKS = 1;";
        UbirimiContainer::get()['db.connection']->query($query);
    }

    public static function deleteByIdForDocumentator($groupId) {
        $query = "SET FOREIGN_KEY_CHECKS = 0;";
        UbirimiContainer::get()['db.connection']->query($query);

        $query = 'delete from group_data where group_id = ?';
        if ($stmt = UbirimiContainer::get()['db.connection']->prepare($query)) {
            $stmt->bind_param("i", $groupId);
            $stmt->execute();
        }

        $query = 'delete from `group` where id = ?';
        if ($stmt = UbirimiContainer::get()['db.connection']->prepare($query)) {
            $stmt->bind_param("i", $groupId);
            $stmt->execute();
        }

        $query = "SET FOREIGN_KEY_CHECKS = 0;";
        UbirimiContainer::get()['db.connection']->query($query);
    }

    public static function deleteByIdForYongo($groupId) {

        $query = "SET FOREIGN_KEY_CHECKS = 0;";
        UbirimiContainer::get()['db.connection']->query($query);

        $query = 'delete from group_data where group_id = ?';
        if ($stmt = UbirimiContainer::get()['db.connection']->prepare($query)) {
            $stmt->bind_param("i", $groupId);
            $stmt->execute();
        }

        $query = 'delete from `group` where id = ?';
        if ($stmt = UbirimiContainer::get()['db.connection']->prepare($query)) {
            $stmt->bind_param("i", $groupId);
            $stmt->execute();
        }

        $query = 'delete from notification_scheme_data where group_id = ?';
        if ($stmt = UbirimiContainer::get()['db.connection']->prepare($query)) {
            $stmt->bind_param("i", $groupId);
            $stmt->execute();
        }

        $query = 'delete from permission_scheme_data where group_id = ?';
        if ($stmt = UbirimiContainer::get()['db.connection']->prepare($query)) {
            $stmt->bind_param("i", $groupId);
            $stmt->execute();
        }

        $query = "SET FOREIGN_KEY_CHECKS = 0;";
        UbirimiContainer::get()['db.connection']->query($query);
    }
}