<?php
    use Ubirimi\LinkHelper;
    use Ubirimi\SystemProduct;
    use Ubirimi\Util;

    require_once __DIR__ . '/../_header.php';
?>
<body>

    <?php require_once __DIR__ . '/../_menu.php'; ?>

    <div class="pageContent">
        <?php
            $title = $user['first_name'] . ' ' . $user['last_name'] . ' > Activity';
            Util::renderBreadCrumb($title);
        ?>
        <?php if (Util::checkUserIsLoggedIn()): ?>
            <table cellspacing="0" border="0" cellpadding="0" class="tableButtons">
                <tr>
                    <td><a id="btnUserChangePassword" href="#" class="btn ubirimi-btn">Change Password</a></td>
                </tr>
            </table>
        <?php endif ?>

        <div class="messageGreen" id="userDataUpdated"></div>

        <ul class="nav nav-tabs" style="padding: 0px;">
            <li><?php echo LinkHelper::getUserProfileLink($userId, SystemProduct::SYS_PRODUCT_DOCUMENTADOR, 'Summary', '') ?></li>
            <li><a href="/documentador/user/favourites/<?php echo $userId ?>">Favourites</a></li>
            <li class="active"><a href="/documentador/user/activity/<?php echo $userId ?>">Activity</a></li>
        </ul>

        <div style="height: 4px"></div>
        <?php if ($activities): ?>
            <table>
                <?php while ($activity = $activities->fetch_array(MYSQLI_ASSOC)): ?>
                    <tr>
                        <td>
                            <?php if ($activity['action'] == 'created'): ?>
                                <?php echo LinkHelper::getDocumentatorPageLink($activity['id'], $activity['name']) . ' created on ' . Util::getFormattedDate($activity['date']) ?>
                            <?php elseif ($activity['action'] == 'created_space'): ?>
                                <a href="/documentador/pages/<?php echo $activity['id'] ?>"><?php echo $activity['name'] ?></a> created on <?php echo Util::getFormattedDate($activity['date']) ?>
                            <?php elseif ($activity['action'] == 'edited'): ?>
                                <?php echo LinkHelper::getDocumentatorPageLink($activity['id'], $activity['name']) . ' updated on ' . Util::getFormattedDate($activity['date']) ?>
                            <?php elseif ($activity['action'] == 'comment'): ?>
                                <?php echo LinkHelper::getDocumentatorPageLink($activity['id'], $activity['name']) . ' commented on ' . Util::getFormattedDate($activity['date']) ?>
                            <?php endif ?>
                        </td>
                    </tr>
                <?php endwhile ?>
            </table>
        <?php else: ?>
            <div class="infoBox">There is no activity for this user.</div>
        <?php endif ?>
        <input type="hidden" id="user_id" value="<?php echo $userId ?>"/>
        <div id="modalChangePassword"></div>
    </div>
    <?php require_once __DIR__ . '/../_footer.php' ?>
</body>