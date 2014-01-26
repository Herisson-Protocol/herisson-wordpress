<?php require __DIR__."/../header.php"; ?>

    <div id="page">
        <h1>
            <?php echo sprintf(__("%s bookmarks", HERISSON_TD), $title); ?>
        </h1>
        <div id="search">
            <form action="<?php echo $_SERVER['REQUEST_URI']; ?>" method="get">
                Recherche <input type="text" name="search" value="" /><input type="submit" value="OK"/>
            </form>
        </div>

        <?php if (sizeof($bookmarks)) { ?>
        <div id="mybookmarks">
            <?php foreach ($bookmarks as $bookmark) { ?> 
            <div class="bookmark">
                <span class="title">
                    <a href="<?php echo esc_url($bookmark->url); ?>"><?php echo esc_html($bookmark->title); ?></a>
                </span>
                <br/>
                <span class="tag">Tags</span> :
                <span class="tags">
                <?php foreach ($bookmark->getTagsArray() as $tag) { ?>
                    <a href="?tag=<?php echo $tag?>"><?php echo $tag?></a> &nbsp;
                <?php } ?>
                </span>
                <?php if ($bookmark->description) { ?>
                    <p class="description">
                        <?php echo $bookmark->description?>
                    </p>
                <?php } ?>
            </div>
            <?php } ?>
        </div>
        <?php } else { ?>
            <?php echo __("No bookmark", HERISSON_TD); ?>
        <?php } ?>

    </div>

<?php
$this->includePartial(__DIR__."/friends.php", array(
    'friendBookmarks' => $friendBookmarks,
    'friends' => $friends
));
require __DIR__."/../footer.php";

