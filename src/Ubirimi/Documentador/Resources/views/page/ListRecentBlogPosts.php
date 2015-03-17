<?php

use Ubirimi\Container\UbirimiContainer;
use Ubirimi\Documentador\Repository\Entity\Entity;
use Ubirimi\Documentador\Repository\Entity\EntityType;
use Ubirimi\LinkHelper;
use Ubirimi\Util;

require_once __DIR__ . '/../_header.php';
?>
<body>
    <?php require_once __DIR__ . '/../_menu.php'; ?>
    <?php
        $breadCrumb = '<a href="/documentador/spaces" class="linkNoUnderline">Spaces</a> > ' . $space['name'] . ' > ' .
            '<a class="linkNoUnderline" href="/documentador/blog/recent/' . $spaceId . '">Recent Blog Posts</a>';

        Util::renderBreadCrumb($breadCrumb);
    ?>

    <div class="doc-left-side">
        <div><a href="/documentador/pages/<?php echo $spaceId ?>"><img src="/documentador/img/pages.png" /> <b>Pages</b></a></div>
        <div><img src="/documentador/img/rss.png" /> <b>Blog</b></div>

        <div>
            <?php
                $blogPages = UbirimiContainer::get()['repository']->get(Entity::class)->getBlogTreeNavigation($pagesInSpace);
                foreach ($blogPages as $year => $data) {
                    echo '<img style="vertical-align: middle;" src="/documentador/img/arrow_down.png" />' . $year . '<br />';
                    foreach ($data as $month => $pages) {
                        echo '&nbsp;&nbsp;&nbsp;&nbsp; <img style="vertical-align: middle;" src="/documentador/img/arrow_down.png" /> ' . $month;
                        foreach ($pages as $page) {
                            echo '<div>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&bullet; ' . LinkHelper::getDocumentadorPageLink($page['id'], $page['name']) . '</div>';
                        }
                    }
                }
            ?>
        </div>
    </div>

    <div class="pageContent" style="overflow: hidden; margin-left: 285px">
        <?php $pagesInSpace->data_seek(0); ?>
        <?php while ($page = $pagesInSpace->fetch_array(MYSQLI_ASSOC)): ?>
            <?php echo $page['name'] ?>
        <?php endwhile ?>
    </div>
    <?php require_once __DIR__ . '/../_footer.php' ?>
</body>