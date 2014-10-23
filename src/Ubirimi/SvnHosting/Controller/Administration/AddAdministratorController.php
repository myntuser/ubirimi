<?php

namespace Ubirimi\SVNHosting\Controller\Administration;

use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Ubirimi\SvnHosting\Repository\Repository;
use Ubirimi\SystemProduct;
use Ubirimi\UbirimiController;
use Ubirimi\Util;

class AddAdministratorController extends UbirimiController
{
    public function indexAction(Request $request, SessionInterface $session)
    {
        Util::checkUserIsLoggedInAndRedirect();

        $clientId = $session->get('client/id');

        $menuSelectedCategory = 'svn';
        $session->set('selected_product_id', SystemProduct::SYS_PRODUCT_SVN_HOSTING);
        $regularUsers = $this->getRepository('ubirimi.user.user')->getNotSVNAdministrators($clientId);
        $noUsersSelected = false;

        if ($request->request->has('confirm_new_svn_administrator')) {
            $users = $request->request->get('user');

            if ($users) {
                Repository::addAdministrator($users);

                return new RedirectResponse('/svn-hosting/administration/administrators');
            } else {
                $noUsersSelected = true;
            }
        }

        return $this->render(__DIR__ . '/../../Resources/views/administration/AddAdministrator.php', get_defined_vars());
    }
}