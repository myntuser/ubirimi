<?php

namespace Ubirimi\FrontendCOM\Controller\Administration;

use Ubirimi\UbirimiController;
use Ubirimi\Util;
use Ubirimi\Repository\Documentador\Space;

class SpaceController extends UbirimiController
{
    public function indexAction()
    {
        Util::checkSuperUserIsLoggedIn();

        $spaces = Space::getAll(array('sort_by' => 'documentator_space.date_created', 'sort_order' => 'desc'));

        $selectedOption = 'spaces';

        return $this->render(__DIR__ . '/../../Resources/views/administration/Space.php', get_defined_vars());
    }
}