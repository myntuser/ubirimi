<?php
use Ubirimi\Util;

require_once __DIR__ . '/../../../../QuickNotes/Resources/views/_header.php';
?>

<body>
    <?php require_once __DIR__ . '/../../../../QuickNotes/Resources/views/_menu.php'; ?>
    <?php
        Util::renderBreadCrumb('Quick Notes > All Notes');
    ?>

    <div class="pageContent">

        <table cellspacing="0" border="0" cellpadding="0" class="tableButtons">
            <tr>
                <td>
                    <input id="btnCreateNotebook" class="btn ubirimi-btn" type="button" value="Create Notebook" />
                    <input id="btnCreateNote" class="btn ubirimi-btn" type="button" value="Create Note" />
                </td>
                <td>
                    <div class="btn-group" >
                        <a href="#" class="btn ubirimi-btn dropdown-toggle"
                           data-toggle="dropdown">View Options <span class="caret"></span></a>

                        <ul class="dropdown-menu pull-left">
                            <li><a href="/yongo/issue/printable-list">Snippets</a></li>
                            <li><a href="/yongo/issue/printable-list-full-content">List</a></li>
                        </ul>
                    </div>
                </td>
            </tr>
        </table>


        <table id="contentNotesList" width="100%" cellpadding="0" cellspacing="0">
            <tr>
                <td width="10%" valign="top" style="border-right: 1px solid #c6c6c6; border-top: 1px solid #DDDDDD;">
                    <div style="height: 100%; overflow: auto">
                        <?php require_once __DIR__ . '/../_notebook_list_section.php' ?>
                    </div>
                </td>
                <?php if ('snippets' == $viewType): ?>
                    <td width="350px" valign="top" style="border-right: 1px solid #c6c6c6; border-top: 1px solid #DDDDDD;">
                        <div style="height: 100%; overflow: auto">
                            <?php require_once __DIR__ . '/../_note_list_section.php' ?>
                        </div>
                    </td>
                    <td width="75%" valign="top">
                        <?php require_once __DIR__ . '/../_note_button_bar.php' ?>
                        <?php require_once __DIR__ . '/../_note_section.php' ?>
                    </td>
                <?php else: ?>
                    <td valign="top">
                        <table id="qn_headerListNotes" class="table table-hover table-condensed">
                            <thead>
                                <tr>
                                    <th width="50%">Title</th>
                                    <th width="30%">Tags</th>
                                    <th width="10%">Created</th>
                                    <th width="10%">Updated</th>
                                </tr>
                            </thead>
                        </table>
                        <div style="height: 40%; overflow: auto" id="qn_list_notes_view">
                            <table class="table table-hover table-condensed">
                                <tbody>
                                <?php foreach ($notes as $currentNote): ?>
                                    <tr class="noteListView" id="table_row_<?php echo $currentNote['id'] ?>">
                                        <td width="50%"><?php echo $currentNote['summary'] ?></td>
                                        <td width="30%"><?php  ?></td>
                                        <td width="10%"><?php echo substr($currentNote['date_created'], 0, 10); ?></td>
                                        <td width="10%"><?php echo substr($currentNote['date_updated'], 0, 10) ?></td>
                                    </tr>
                                <?php endforeach ?>
                                </tbody>
                            </table>
                        </div>
                        <div id="qn_note_list_content" style="border-top: 4px solid #b9b9b9">

                        </div>
                    </td>
                <?php endif ?>
            </tr>
        </table>

        <div class="ubirimiModalDialog" id="modalDeleteNote"></div>
        <?php require_once __DIR__ . '/../../../../QuickNotes/Resources/views/_footer.php' ?>
        <input type="hidden" value="<?php echo $notebookId ?>" id="notebook_id" />
        <?php if (isset($noteId)): ?>
            <input type="hidden" value="<?php echo $noteId ?>" id="note_id" />
        <?php endif ?>
        <?php if (isset($tagId)): ?>
            <input type="hidden" value="<?php echo $tagId ?>" id="tag_id" />
        <?php endif ?>
        <input type="hidden" id="view_qn_entity" value="note_tag" />
    </div>
</body>