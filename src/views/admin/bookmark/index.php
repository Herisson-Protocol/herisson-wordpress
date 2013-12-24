
<div class="wrap">
    
    <? include_partial(__DIR__."/../elements/messages.php", array()); ?>
    <span style="float: right">
        <form method="get" action="<? echo get_option('siteurl'); ?>/wp-admin/admin.php?page=herisson_bookmark">
            <input type="hidden" name="page" value="herisson_bookmark" />
            <input type="hidden" name="action" value="search" />
            <input type="text" name="search" value="" placeholder="<? echo __("Search here", HERISSON_TD);?>" />
            <input type="submit" value="<? echo __("Search", HERISSON_TD); ?>" class="button" />
        </form>
    </span>
    <h2>
        <? if (isset($subtitle)) { ?>
            <? echo $subtitle; ?>
        <? } else { ?>
            <? echo __("All bookmarks", HERISSON_TD); ?>
        <? } ?>
        <a href="<? echo get_option('siteurl'); ?>/wp-admin/admin.php?page=herisson_bookmark&action=add&id=0" class="add-new-h2"><? echo __('Add', HERISSON_TD) ?></a>
    </h2>

<? if (sizeof($bookmarks)) { ?>
    <table class="widefat post " cellspacing="0">
        <tr>
            <th></th>
            <th><?=__('Title', HERISSON_TD)?></th>
            <th><?=__('URL', HERISSON_TD)?></th>
            <th><?=__('Tags', HERISSON_TD)?></th>
            <th><?=__('Action', HERISSON_TD)?></th>
        </tr>
<? foreach ($bookmarks as $bookmark) { ?> 
        <tr>
            <td style="width: 30px; vertical-align:baseline">
            <? if ($bookmark->favicon_image) { ?>
                <img src="data:image/png;base64,<?=$bookmark->favicon_image?>" />
            <? } ?>
            </td>
            <td>
                <b>
                    <a href="<?=get_option('siteurl')?>/wp-admin/admin.php?page=herisson_bookmark&action=edit&id=<?=$bookmark->id?>">
                    <? echo $bookmark->title ? $bookmark->title : "Unamed-".$bookmark->$id; ?>
                    </a>
                </b>
            </td>
            <td>
                <a href="<? echo $bookmark->url; ?>">
                    <? echo strlen($bookmark->url) > 80 ? substr($bookmark->url, 0, 80)."&hellip;" : $bookmark->url; ?>
                </a>
            </td>
            <td>
                <? foreach ($bookmark->getTagsArray() as $tag) { ?>
                    <a href="<?=get_option('siteurl')?>/wp-admin/admin.php?page=herisson_bookmark&tag=<?=$tag?>"><?=$tag?></a>, &nbsp; 
                <? } ?>
            </td>
            <td>
                <a href="<?=get_option('siteurl')?>/wp-admin/admin.php?page=herisson_bookmark&action=delete&id=<?=$bookmark->id?>" onclick="if (confirm('<?=__('Are you sure ? ', HERISSON_TD)?>')) { return true; } return false;">
                    <?=__('Delete', HERISSON_TD)?>
                </a>
            </td>
        </tr>
<? } ?>
    </table>
    <? include_partial(__DIR__."/../elements/pagination.php", array(
        'all' => $countAll,
        'max' => sizeof($bookmarks),
        'limit' => $pagination['limit'],
        'offset' => $pagination['offset'],
        ));
        ?>

    <? echo __(sizeof($bookmarks)." bookmarks.", HERISSON_TD); ?>
</div>
<? } else { ?>
    <?php echo __("No bookmark", HERISSON_TD); ?>
<? } ?>
