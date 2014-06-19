<?php
    use Ubirimi\LinkHelper;
    use Ubirimi\Repository\Documentador\Space;
    use Ubirimi\Repository\User\User;
    use Ubirimi\SystemProduct;
    use Ubirimi\Util;

    $styleSelectedMenu = 'style="background-color: #EEEEEE;';

//    $loggedInUserId = null;
    if (Util::checkUserIsLoggedIn()) {

        $hasAdministrationPermission = Util::userHasDocumentatorAdministrativePermission();
        $spaces = Space::getWithAdminPermissionByUserId($clientId, $loggedInUserId);
    }

    if (!isset($menuSelectedCategory)) {
        $menuSelectedCategory = null;
    }

    Util::renderMaintenanceMessage();
?>

<table width="100%" border="0" cellpadding="0" cellspacing="0" bgcolor="#003466">
    <tr>
        <?php if (Util::checkUserIsLoggedIn()): ?>
            <td width="12px"></td>
            <td style="height: 40px" valign="middle">
                <?php require_once __DIR__ . '/../../../Resources/views/productTopBar.php' ?>
            </td>
        <?php else: ?>
            <td width="12px"></td>
            <td valign="middle" width="34px" align="center">
                <img style="vertical-align: middle" src="/img/site/bg.logo.png" height="30px"/>
            </td>
            <td style="border-right: 1px #9c9c9c solid; color: #ffffff; font-size: 18px;" width="90px" align="center"><a style="color: #ffffff; text-decoration: none;" href="#">Ubirimi</a></td>

            <td style="background-color: #6A8EB2; border-right: 1px #9c9c9c solid;" width="182px" class="product-menu" align="center"><div><a href="/documentador/dashboard/spaces">Documentador</a></div></td>
        <?php endif ?>
        <td style="padding-right: 12px;">
            <table align="right" border="0" cellpadding="0" cellspacing="0" >
                <tr>
                    <?php if (Util::checkUserIsLoggedIn()): ?>
                        <td style="height:44px;" id="menu_top_user" width="58px" align="center" class="product-menu">
                            <span>
                                <img src="<?php echo User::getUserAvatarPicture($session->get('user'), 'small') ?>" title="<?php echo $session->get('user/first_name') . ' ' . $session->get('user/last_name') ?>" height="33px" style="vertical-align: middle" />
                            </span>
                            <span class="arrow" style="top: 12px;"></span>
                            &nbsp;
                        </td>
                        <?php if ($hasAdministrationPermission || $spaces): ?>
                            <td style="height:44px; vertical-align: middle; border-left: 1px #9c9c9c solid;" width="170px" class="product-menu" align="center" valign="middle">
                                <a href="/documentador/administration" title="Documentador Administration">

                                    <div style="margin-top: 0px;">Administration</div>
                                </a>
                            </td>
                        <?php endif ?>
                    <?php else: ?>
                        <td style="height:44px; vertical-align: middle; border-left: 1px #9c9c9c solid;" width="120px" class="product-menu" align="center" valign="middle">
                            <a href="/documentador/administration" title="Sign In">
                                <div style="margin-top: 0px;"><a href="<?php echo Util::getHttpHost() ?>">Sign In</a></div>
                            </a>
                        </td>
                    <?php endif ?>
                </tr>
            </table>
        </td>
    </tr>
</table>

<table width="100%" cellspacing="0" cellpadding="0" border="0" bgcolor="#6A8EB2" style="padding-left: 12px; padding-right: 12px;">
    <tr>
        <td>
            <table border="0" cellpadding="0" cellspacing="0" width="100%">
                <tr>
                    <td width="120px" class="menuItemBasic <?php if ($menuSelectedCategory == 'documentator') echo 'menuItemSelected'; else echo 'menuItem' ?>" id="menuDocumentator" style="cursor: pointer;">
                        <span>Documentador</span>
                        <span class="<?php if ($menuSelectedCategory == 'documentator') echo 'arrowSelected'; else echo 'arrow' ?>"></span>
                        &nbsp;
                    </td>
                    <td>&nbsp;</td>
                    <td align="right">
                        <?php if (Util::checkUserIsLoggedIn()): ?>
                            <?php

                                $spaces = Space::getByClientId($clientId);
                            ?>
                            <?php if ($spaces): ?>
                                <input type="button" id="btnDocumentatorCreate" value="Create" />
                            <?php endif ?>
                            <input id="documentator_quick_search" type="text" style="height: 15px; font-style: italic;" value="Quick Search" name="search" />
                        <?php endif ?>
                    </td>
                </tr>
            </table>
        </td>
    </tr>
</table>

<div style="border-left: 1px solid #c0c0c0;" id="contentMenuDocumentator"></div>
<input type="hidden" value="<?php echo $menuSelectedCategory ?>" id="menu_selected" />

<?php if ($loggedInUserId): ?>
    <div style="display: none;" id="contentUserHome">
        <table class="tableMenu" width="100%" cellpadding="0" cellspacing="0" border="0">
            <tr>
                <td><div><?php echo LinkHelper::getUserProfileLink($loggedInUserId, SystemProduct::SYS_PRODUCT_DOCUMENTADOR, 'Profile', '', 'linkSubMenu') ?></div></td>
            </tr>
            <tr>
                <td>
                    <span style="border-bottom: 1px solid #BBBBBB; margin-bottom: 4px; padding-bottom: 4px; display: block;"></span>
                </td>
            </tr>
            <tr>
                <td><div><a class="linkSubMenu" href="/sign-out">Sign out</a></div></td>
            </tr>
        </table>
    </div>
<?php endif ?>

<div id="modalSendFeedback"></div>
<div id="modalNewPage"></div>
<div id="modalNewPageDetails"></div>
<div id="topMessageBox" align="center" class="topMessageBox"></div>