<?php
    use Ubirimi\Repository\Documentador\Entity;
    use Ubirimi\Util;

    Util::checkUserIsLoggedInAndRedirect();
    $revisionId = $_POST['id'];
    $pageId = $_POST['entity_id'];
    $page = Entity::getById($pageId);
    $revision = Entity::getRevisionsByPageIdAndRevisionId($pageId, $revisionId);

    $date = Util::getCurrentDateTime($session->get('client/settings/timezone'));
    Entity::addRevision($pageId, $loggedInUserId, $page['content'], $date);

    Entity::updateContent($pageId, $revision['content']);