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

use Ubirimi\Util;

require_once __DIR__ . '/../../../../Answers/Resources/views/_header.php';
?>
<body>
    <?php require_once __DIR__ . '/../../../../Answers/Resources/views/_menu.php'; ?>
    <?php Util::renderBreadCrumb('Manage Domains') ?>
    <div class="pageContent">

        <table cellspacing="0" border="0" cellpadding="0" class="tableButtons">
            <tr>
                <td><a id="btnNew" href="/answers/domain/add" class="btn ubirimi-btn"><i class="icon-plus"></i> Create New Domain</a></td>
                <td><a id="btnEditDomain" href="#" class="btn ubirimi-btn disabled"><i class="icon-edit"></i> Edit</a></td>
                <td><a id="btnDeleteDomain" href="#" class="btn ubirimi-btn disabled"><i class="icon-remove"></i> Delete</a></td>
            </tr>
        </table>
        <?php if ($domains): ?>
            <table class="table table-hover table-condensed">
                <thead>
                    <tr>
                        <th></th>
                        <th>Name</th>
                        <th>Description</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($domain = $domains->fetch_array(MYSQLI_ASSOC)): ?>
                        <tr id="table_row_<?php echo $domain['id'] ?>">
                            <td width="22">
                                <input type="checkbox" value="1" id="el_check_<?php echo $domain['id'] ?>"/>
                            </td>
                            <td>
                                <div><a href="/agile/board/plan/<?php echo $domain['id'] ?>"><?php echo $domain['name'] ?></a></div>
                            </td>
                            <td>
                                <div><?php echo $domain['description'] ?></div>
                            </td>
                        </tr>
                    <?php endwhile ?>
                </tbody>
            </table>
        <?php else: ?>
            <div class="infoBox">There are no domains defined.</div>
        <?php endif ?>
    </div>

    <div class="ubirimiModalDialog" id="modalDeleteDomain"></div>
    <?php require_once __DIR__ . '/../../../../Answers/Resources/views/_footer.php' ?>
</body>