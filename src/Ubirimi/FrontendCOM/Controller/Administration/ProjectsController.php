<?php

namespace Ubirimi\FrontendCOM\Controller\Administration;

use Ubirimi\UbirimiController;
use Ubirimi\Util;

class ProjectsController extends UbirimiController
{
    public function indexAction()
    {
        Util::checkSuperUserIsLoggedIn();

        $projects = $this->getRepository('yongo.project.project')->getAll(array('sort_by' => 'project.date_created', 'sort_order' => 'desc'));

        $selectedOption = 'projects';

        return $this->render(__DIR__ . '/../../Resources/views/administration/Projects.php', get_defined_vars());
    }
}
