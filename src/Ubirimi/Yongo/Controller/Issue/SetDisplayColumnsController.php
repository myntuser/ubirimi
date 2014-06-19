<?php
    use Ubirimi\Repository\User\User;
    use Ubirimi\Util;

    Util::checkUserIsLoggedInAndRedirect();
    $data = $_POST['data'];

    User::updateDisplayColumns($loggedInUserId, $data);
    $session->set('user/issues_display_columns', $data);