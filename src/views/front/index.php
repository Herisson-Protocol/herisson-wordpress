<? include("header.php"); ?>

    <div id="page">
        <h1>
            <? echo sprintf(__("%s bookmarks", HERISSON_TD), $title); ?>
        </h1>
        <div id="search">
            <form action="" method="get">
                Recherche <input type="text" name="search" value="" /><input type="submit" value="OK"/>
            </form>
        </div>

        <? if (sizeof($bookmarks)) { ?>
        <div id="mybookmarks">
            <? foreach ($bookmarks as $bookmark) { ?> 
            <div class="bookmark">
                <span class="title">
                    <a href="<?=$bookmark->url; ?>"><?=$bookmark->title?></a>
                </span>
                <br/>
                <span class="tag">Tags</span> :
                <span class="tags">
                <? foreach ($bookmark->getTagsArray() as $tag) { ?>
                    <a href="?tag=<?=$tag?>"><?=$tag?></a> &nbsp;
                <? } ?>
                </span>
                <? if ($bookmark->description) { ?>
                    <p class="description">
                        <?=$bookmark->description?>
                    </p>
                <? } ?>
            </div>
            <? } ?>
        </div>
        <? } else { ?>
            <? echo __("No bookmark", HERISSON_TD); ?>
        <? } ?>

    </div>

<?
	include("friends.php");
	include("footer.php");
