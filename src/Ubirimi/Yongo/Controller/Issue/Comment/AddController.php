<?php

namespace Ubirimi\Yongo\Controller\Issue\Comment;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Ubirimi\Container\UbirimiContainer;
use Ubirimi\Event\LogEvent;
use Ubirimi\Event\UbirimiEvents;
use Ubirimi\SystemProduct;
use Ubirimi\UbirimiController;use Ubirimi\Util;
use Ubirimi\Yongo\Event\IssueEvent;
use Ubirimi\Yongo\Event\YongoEvents;
use Ubirimi\Yongo\Repository\Issue\Issue;
use Ubirimi\Yongo\Repository\Issue\IssueComment;

class AddController extends UbirimiController
{
    public function indexAction(Request $request, SessionInterface $session)
    {
        Util::checkUserIsLoggedInAndRedirect();

        $loggedInUserId = $session->get('user/id');

        $issueId = $request->request->get('id');
        $content = $request->request->get('content');

        $date = Util::getCurrentDateTime($session->get('client/settings/timezone'));

        $issue = Issue::getByParameters(array('issue_id' => $issueId), $loggedInUserId);

        IssueComment::add($issueId, $session->get('user/id'), $content, $date);

        $issueEvent = new IssueEvent($issue, null, IssueEvent::STATUS_UPDATE, $content);
        $issueLogEvent = new LogEvent(SystemProduct::SYS_PRODUCT_YONGO, 'ADD Yongo issue comment ' . $issue['project_code'] . '-' . $issue['nr']);

        UbirimiContainer::get()['dispatcher']->dispatch(UbirimiEvents::LOG, $issueLogEvent);
        UbirimiContainer::get()['dispatcher']->dispatch(YongoEvents::YONGO_ISSUE_COMMENT_EMAIL, $issueEvent);

        return new Response('');
    }
}