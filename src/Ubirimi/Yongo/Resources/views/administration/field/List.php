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

use Ubirimi\Container\UbirimiContainer;
use Ubirimi\Util;
use Ubirimi\Yongo\Repository\Field\Field;
use Ubirimi\Yongo\Repository\Screen\Screen;

require_once __DIR__ . '/../_header.php';
?>
<body>

    <?php require_once __DIR__ . '/../_menu.php'; ?>
    <?php Util::renderBreadCrumb('Custom Fields') ?>
    <div class="pageContent">

        <ul class="nav nav-tabs" style="padding: 0px;">
            <li class="active"><a href="/yongo/administration/screens">Custom Fields</a></li>
            <li><a href="/yongo/administration/field-configurations">Field Configurations</a></li>
            <li><a href="/yongo/administration/field-configurations/schemes">Field Configuration Schemes</a></li>
        </ul>

        <table cellspacing="0" border="0" cellpadding="0" class="tableButtons">
            <tr>
                <td><a id="btnNew" href="/yongo/administration/custom-field/add" class="btn ubirimi-btn"><i class="icon-plus"></i> Create Custom Field</a></td>
                <?php if ($fields): ?>
                    <td><a id="btnEditCustomField" href="#" class="btn ubirimi-btn disabled"><i class="icon-edit"></i> Edit</a></td>
                    <td><a id="btnCustomFieldPlaceOnScreens" href="#" class="btn ubirimi-btn disabled">Place on Screens</a></td>
                    <td><a id="btnDeleteCustomField" href="#" class="btn ubirimi-btn disabled"><i class="icon-remove"></i> Delete</a></td>
                <?php endif ?>
            </tr>
        </table>
        <?php if ($fields): ?>
            <table class="table table-hover table-condensed">
                <thead>
                    <tr>
                        <th></th>
                        <th>Name</th>
                        <th>Type</th>
                        <th>Available Contexts</th>
                        <th>Screens</th>
                        <th>Options</th>
                    </tr>
                </thead>

                <?php while ($field = $fields->fetch_array(MYSQLI_ASSOC)): ?>
                    <tr id="table_row_<?php echo $field['id'] ?>">
                        <td width="22">
                            <input type="checkbox" value="1" id="el_check_<?php echo $field['id'] ?>" />
                        </td>
                        <td>
                            <?php
                                echo $field['name'];
                                echo '<div class="smallDescription">' . $field['description'] . '</div>';
                            ?>
                        </td>
                        <td><?php echo $field['type_name'] ?></td>
                        <td>
                            <?php if ($field['all_issue_type_flag']): ?>
                                <div>All Issue Types</div>
                            <?php else: ?>
                                <?php $issueTypes = UbirimiContainer::get()['repository']->get(Field::class)->getIssueTypesFor($field['id']) ?>
                                <div>Used for the following issue types:</div>
                                <ul>
                                <?php while ($type = $issueTypes->fetch_array(MYSQLI_ASSOC)): ?>
                                    <li><?php echo $type['name'] ?></li>
                                <?php endwhile ?>
                                </ul>
                            <?php endif ?>
                            <?php if ($field['all_project_flag']): ?>
                                <div>All Projects</div>
                            <?php else: ?>
                                <?php $projects = UbirimiContainer::get()['repository']->get(Field::class)->getProjectsFor($field['id']) ?>
                                <div>Used for the following projects:</div>
                                <ul>
                                <?php while ($project = $projects->fetch_array(MYSQLI_ASSOC)): ?>
                                    <li><?php echo $project['name'] ?></li>
                                <?php endwhile ?>
                                </ul>
                            <?php endif ?>
                        </td>
                        <td width="500px">
                            <?php $screens = UbirimiContainer::get()['repository']->get(Screen::class)->getByFieldId($clientId, $field['id']) ?>
                            <?php if ($screens): ?>
                                <ul>
                                <?php while ($screen = $screens->fetch_array(MYSQLI_ASSOC)): ?>
                                    <li><a href="/yongo/administration/screen/configure/<?php echo $screen['id'] ?>"><?php echo $screen['name'] ?></a></li>
                                <?php endwhile ?>
                                </ul>
                            <?php else: ?>
                                <div>Not placed on any screen.</div>
                            <?php endif ?>
                        </td>
                        <td>
                            <?php if ($field['type_id'] == Field::CUSTOM_FIELD_TYPE_SELECT_LIST_SINGLE_CODE_ID): ?>
                                <a href="/yongo/administration/custom-fields/define/<?php echo $field['id'] ?>">Options</a>
                            <?php endif ?>
                        </td>
                    </tr>
                <?php endwhile ?>
            </table>

            <input type="hidden" value="type" id="setting_type" />
        <?php else: ?>
            <div class="messageGreen">There are no custom fields defined.</div>
        <?php endif ?>
        <div class="ubirimiModalDialog" id="modalDeleteCustomField"></div>
    </div>
    <?php require_once __DIR__ . '/../_footer.php' ?>
</body>
</html>