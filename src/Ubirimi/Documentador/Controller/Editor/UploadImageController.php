<?php
    // upload image

    use Ubirimi\Container\UbirimiContainer;
    use Ubirimi\Repository\Documentador\Entity;
    use Ubirimi\Repository\Documentador\EntityAttachment;
    use Ubirimi\SystemProduct;
    use Ubirimi\Util;

    Util::checkUserIsLoggedInAndRedirect();

    $CKEditorFuncNum = $_GET['CKEditorFuncNum'];
    $allowedExtensions = array("gif", "jpeg", "jpg", "png");

    $extension = strtolower(pathinfo($_FILES['upload']['name'], PATHINFO_EXTENSION));

    $entityId = $session->get('current_edit_entity_id');
    $entity = Entity::getById($entityId);
    $spaceId = $entity['space_id'];

    $currentDate = Util::getCurrentDateTime($session->get('client/settings/timezone'));
    $attachmentsBaseFilePath = Util::getAssetsFolder(SystemProduct::SYS_PRODUCT_DOCUMENTADOR, 'attachments');
    if ((($_FILES["upload"]["type"] == "image/gif")
        || ($_FILES["upload"]["type"] == "image/jpeg")
        || ($_FILES["upload"]["type"] == "image/jpg")
        || ($_FILES["upload"]["type"] == "image/pjpeg")
        || ($_FILES["upload"]["type"] == "image/x-png")
        || ($_FILES["upload"]["type"] == "image/png"))
        && in_array($extension, $allowedExtensions)
    ) {
        if ($_FILES["upload"]["error"] > 0) {
            // error

        } else {
            $fileName = $_FILES["upload"]["name"];

            // check if the attachment already exists
            $attachment = EntityAttachment::getByEntityIdAndName($entityId, $fileName);
            if ($attachment) {
                // get the last revision and increment it by one
                $attachmentId = $attachment['id'];
                $revisions = Entity::getRevisionsByAttachmentId($attachmentId);
                $revisionNumber = $revisions->num_rows + 1;

                // create the revision folder
                mkdir($attachmentsBaseFilePath . $spaceId . '/' . $entityId. '/' . $attachmentId . '/' . $revisionNumber);

            } else {
                // add the attachment in the database
                $currentDate = Util::getCurrentDateTime($session->get('client/settings/timezone'));
                $attachmentId = EntityAttachment::add($entityId, $fileName, $currentDate);

                $revisionNumber = 1;

                if (!file_exists($attachmentsBaseFilePath . $spaceId)) {
                    mkdir($attachmentsBaseFilePath . $spaceId);
                }

                if (!file_exists($attachmentsBaseFilePath . $spaceId . '/' . $entityId)) {
                    mkdir($attachmentsBaseFilePath . $spaceId . '/' . $entityId);
                }

                if (!file_exists($attachmentsBaseFilePath . $spaceId . '/' . $entityId . '/' . $attachmentId)) {
                    mkdir($attachmentsBaseFilePath . $spaceId . '/' . $entityId . '/' . $attachmentId);
                }

                mkdir($attachmentsBaseFilePath . $spaceId . '/' . $entityId . '/' . $attachmentId . '/' . $revisionNumber);
            }

            $directoryName = $attachmentsBaseFilePath . $spaceId . '/' . $entityId. '/' . $attachmentId . '/' . $revisionNumber;
            move_uploaded_file($_FILES["upload"]["tmp_name"], $directoryName . '/' . $fileName);

            EntityAttachment::addRevision($attachmentId, $loggedInUserId, $currentDate);

            $attachmentsPath = UbirimiContainer::get()['asset.documentador_entity_attachments'];
            $html = '<html><body><script type="text/javascript">window.parent.CKEDITOR.tools.callFunction("' . $CKEditorFuncNum . '", "/assets/' . $attachmentsPath . $spaceId . '/' . $entityId. '/' . $attachmentId . '/' . $revisionNumber . '/' . $fileName . '");</script></body></html>';
            echo $html;
        }

    } else {
        // invalid file
    }