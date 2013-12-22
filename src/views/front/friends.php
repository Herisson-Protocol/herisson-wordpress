<? if (sizeof($friends)) { ?>
    <h2>
        <? echo __("Friend's bookmarks", HERISSON_TD); ?>
    </h2>
    <? foreach ($friends as $friend) { ?>
    <div class="friend">
        <? echo $friend->name; ?>'s bookmarks<br/>
        <? $bookmarks = $friend->retrieveBookmarks($_GET); ?>
        <? if (sizeof($bookmarks)) { ?>
            <? foreach ($bookmarks as $bookmark) { ?>
     <div class="bookmark">
      <a href="<?=$bookmark['url']?>"><?=$bookmark['title']?></a><? echo $bookmark['description'] ? $bookmark['description'] : ''; ?><br>
     </div>
            <? } ?>
        <? } else { ?>
            <? echo __("No bookmark", HERISSON_TD); ?>
        <? } ?>
    </div>
    <? } ?>
<? } else { ?>
    <? echo __("No friend", HERISSON_TD); ?>
<? } ?>

