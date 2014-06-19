<?php

namespace Ubirimi\FrontendNET\Controller;

use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Ubirimi\UbirimiController;
use Ubirimi\Util;
use Ubirimi\Repository\Log;
use Ubirimi\SystemProduct;

class SignoutController extends UbirimiController
{
    public function indexAction(Request $request, SessionInterface $session)
    {
        $clientBaseURL = $session->get('client/base_url');
        $date = Util::getCurrentDateTime($session->get('client/settings/timezone'));

        Log::add($session->get('client/id'), SystemProduct::SYS_PRODUCT_GENERAL_SETTINGS, $session->get('client/id'), 'LOG OUT', $date);

        $session->invalidate();

        return new RedirectResponse($clientBaseURL);
    }
}