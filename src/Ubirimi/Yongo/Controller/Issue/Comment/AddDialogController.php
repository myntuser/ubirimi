<?php

namespace Ubirimi\Yongo\Controller\Issue\Comment;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Ubirimi\UbirimiController;

class AddDialogController extends UbirimiController
{
    public function indexAction(Request $request, SessionInterface $session)
    {
        return $this->render(__DIR__ . '/../../../Resources/views/issue/comment/AddDialog.php', get_defined_vars());
    }
}