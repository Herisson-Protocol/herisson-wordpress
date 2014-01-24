<?php if (sizeof($friends)) { ?>
    <h2>
        <?php echo __("Friend's bookmarks", HERISSON_TD); ?>
    </h2>
    <?php foreach ($friends as $friend) { ?>
    <div class="friend">
        <?php echo $friend->name; ?>'s bookmarks<br/>
        <?php if (sizeof($friendBookmarks[$friend->id])) { ?>
            <?php foreach ($friendBookmarks[$friend->id] as $bookmark) { ?>
     <div class="bookmark">
      <a href="<?php echo $bookmark['url']?>"><?php echo $bookmark['title']?></a><?php echo $bookmark['description'] ? $bookmark['description'] : ''; ?><br>
     </div>
            <?php } ?>
        <?php } else { ?>
            <?php echo __("No bookmark", HERISSON_TD); ?>
        <?php } ?>
    </div>
    <?php } ?>
<?php } else { ?>
    <?php echo __("No friend", HERISSON_TD); ?>
<?php } ?>

