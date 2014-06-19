<?php
    use Ubirimi\Repository\Documentador\Space;
    use Ubirimi\Util;

    Util::checkUserIsLoggedInAndRedirect();
    $spaces = Space::getAllByClientId($clientId, 'array');

    echo json_encode($spaces);