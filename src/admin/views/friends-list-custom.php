
<h2><? echo $title; ?></h2>
<?  if (sizeof($friends)) { ?>
<table class="widefat post " cellspacing="0">
    <tr>
        <th><?=__('Alias', HERISSON_TD)?></th>
        <th><?=__('Official name', HERISSON_TD)?></th>
        <th><?=__('URL', HERISSON_TD)?></th>
        <th>Active, youwant, wantsyou</th>
        <th><?=__('Action', HERISSON_TD)?></th>
    </tr>
    <? foreach ($friends as $friend) { ?> 
    <tr>
        <td>
            <b>
                <a href="<?=get_option('siteurl')?>/wp-admin/admin.php?page=herisson_friends&action=edit&id=<?=$friend->id?>">
                    <? echo $friend->alias ? $friend->alias : 'Unnamed-'.$friend->id; ?>
                </a>
            </b>
       </td>
        <td>
            <? echo $friend->name; ?>
        </td>
        <td>
            <a href="<? echo $friend->url; ?>"><? echo $friend->url; ?></a>
        </td>
        <td>
            <?=$friend->is_active?> | <?=$friend->b_youwant?> | <?=$friend->b_wantsyou?>
        </td>
        <td>
            <a href="<?=get_option('siteurl')?>/wp-admin/admin.php?page=herisson_friends&action=delete&id=<?=$friend->id?>" onclick="if (confirm('<?=__('Are you sure ? ', HERISSON_TD)?>')) { return true; } return false;">
                <?=__('Delete', HERISSON_TD)?>
            </a>
            <? if ($friend->b_wantsyou) { ?>
            <a href="<?=get_option('siteurl')?>/wp-admin/admin.php?page=herisson_friends&action=approve&id=<?=$friend->id?>" onclick="if (confirm('<?=__('Are you sure ? ', HERISSON_TD)?>')) { return true; } return false;">
                <?=__('Approve', HERISSON_TD)?>
            </a>
            <? } ?>
        </td>
    </tr>
    <? } ?>
</table>
<? echo __(sizeof($friends)." friends.", HERISSON_TD); ?>
<? } else { ?>
    <? echo __("No friend", HERISSON_TD); ?>
<? } ?>
<br />

