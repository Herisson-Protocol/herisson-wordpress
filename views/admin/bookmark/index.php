
<div class="wrap">
    
    <?php $this->includePartial(__DIR__."/../elements/messages.php", array()); ?>
    <span style="float: right">
        <form method="get" action="<?php echo get_option('siteurl'); ?>/wp-admin/admin.php?page=herisson_bookmark">
            <input type="hidden" name="page" value="herisson_bookmark" />
            <input type="hidden" name="action" value="search" />
            <input type="text" name="search" value="" placeholder="<?php echo __("Search here", HERISSON_TD);?>" />
            <input type="submit" value="<?php echo __("Search", HERISSON_TD); ?>" class="button" />
        </form>
    </span>
    <h2>
        <?php if (isset($subtitle)) { ?>
            <?php echo $subtitle; ?>
        <?php } else { ?>
            <?php echo __("All bookmarks", HERISSON_TD); ?>
        <?php } ?>
        <a href="<?php echo get_option('siteurl'); ?>/wp-admin/admin.php?page=herisson_bookmark&action=add&id=0" class="add-new-h2"><?php echo __('Add', HERISSON_TD) ?></a>
    </h2>

<?php if (sizeof($bookmarks)) { ?>
    <table class="widefat post " cellspacing="0">
        <tr>
            <th></th>
            <th><?php echo __('Title', HERISSON_TD)?></th>
            <th><?php echo __('URL', HERISSON_TD)?></th>
            <th><?php echo __('Tags', HERISSON_TD)?></th>
            <th><?php echo __('Action', HERISSON_TD)?></th>
        </tr>
<?php foreach ($bookmarks as $bookmark) { ?>
        <tr>
            <td style="width: 30px; vertical-align:baseline">
            <?php if ($bookmark->favicon_image) { ?>
                <img src="data:image/png;base64,<?php echo $bookmark->favicon_image?>" />
            <?php } ?>
            </td>
            <td>
                <b>
                    <a href="<?php echo get_option('siteurl')?>/wp-admin/admin.php?page=herisson_bookmark&action=edit&id=<?php echo $bookmark->id?>">
                    <?php echo $bookmark->title ? $bookmark->title : "Unamed-".$bookmark->$id; ?>
                    </a>
                </b>
            </td>
            <td>
                <a href="<?php echo $bookmark->url; ?>">
                    <?php echo strlen($bookmark->url) > 80 ? substr($bookmark->url, 0, 80)."&hellip;" : $bookmark->url; ?>
                </a>
            </td>
            <td>
                <?php foreach ($bookmark->getTagsArray() as $tag) { ?>
                    <a href="<?php echo get_option('siteurl')?>/wp-admin/admin.php?page=herisson_bookmark&tag=<?php echo $tag?>"><?php echo $tag?></a>, &nbsp; 
                <?php } ?>
            </td>
            <td>
                <a href="<?php echo get_option('siteurl')?>/wp-admin/admin.php?page=herisson_bookmark&action=delete&id=<?php echo $bookmark->id?>" onclick="if (confirm('<?php echo __('Are you sure ? ', HERISSON_TD)?>')) { return true; } return false;">
                    <?php echo __('Delete', HERISSON_TD)?>
                </a>
            </td>
        </tr>
<?php } ?>
    </table>
    <?php $this->includePartial(__DIR__."/../elements/pagination.php", array(
        'all' => $countAll,
        'max' => sizeof($bookmarks),
        'limit' => $pagination['limit'],
        'offset' => $pagination['offset'],
        ));
        ?>

    <?php echo __(sizeof($bookmarks)." bookmarks.", HERISSON_TD); ?>
</div>
<?php } else { ?>
    <?php echo __("No bookmark", HERISSON_TD); ?>
<?php } ?>
