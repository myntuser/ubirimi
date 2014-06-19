<?php
    use Ubirimi\Repository\User\User;
    use Ubirimi\Util;

    Util::checkUserIsLoggedInAndRedirect();

    $settings = User::getYongoSettings($loggedInUserId);
?>

<table width="100%">
    <tr>
        <td width="280">Issues per page</td>
        <td>
            <input id="user_issues_per_page" size="5" type="text" value="<?php echo $settings['issues_per_page'] ?>" name="issues_per_page" />
        </td>
    </tr>
    <tr>
        <td>Notify users of their own changes?</td>
        <td>
            <select id="user_notify_own_changes" class="inputTextCombo" style="width: 60px" name="notify_own_changes">
                <option <?php if ($settings['notify_own_changes_flag']) echo 'selected="selected"' ?> value="1">YES</option>
                <option <?php if (!$settings['notify_own_changes_flag']) echo 'selected="selected"' ?> value="0">NO</option>
            </select>
        </td>
    </tr>
</table>