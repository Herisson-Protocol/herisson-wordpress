
<div class="wrap">
    <? include_partial(__DIR__."/../elements/messages.php", array()); ?>
    <h2><? echo $title; ?></h2>
    <?  if (sizeof($friends)) { ?>
    <table class="widefat post " cellspacing="0">
        <tr>
            <th style="width: 10%"><?=__('Alias', HERISSON_TD)?></th>
            <th style="width: 15%"><?=__('Official name', HERISSON_TD)?></th>
            <th style="width: 25%"><?=__('URL', HERISSON_TD)?></th>
            <th style="width:  5%"><?=__('Active', HERISSON_TD)?></th>
            <th style="width:  5%"><?=__('Pending', HERISSON_TD)?></th>
            <th style="width:  5%"><?=__('Need your validation', HERISSON_TD)?></th>
            <th style="width: 10%"><?=__('Action', HERISSON_TD)?></th>
        </tr>
        <? foreach ($friends as $friend) { ?> 
        <tr>
            <td>
                <b>
                    <a href="<?=get_option('siteurl')?>/wp-admin/admin.php?page=herisson_friend&action=edit&id=<?=$friend->id?>">
                        <? echo $friend->alias ? $friend->alias : 'Unnamed-'.$friend->id; ?>
                    </a>
                </b>
           </td>
            <td>
                <? echo $friend->name; ?>
            </td>
            <td>
                <a href="<? echo $friend->url; ?>" target="friend"><? echo $friend->url; ?></a>
            </td>
            <td>
                <?=$friend->is_active?>
            </td>
            <td>
                <?=$friend->b_youwant?>
            </td>
            <td>
                <?=$friend->b_wantsyou?>
            </td>
            <td>
                <a href="<?=get_option('siteurl')?>/wp-admin/admin.php?page=herisson_friend&action=delete&id=<?=$friend->id?>" onclick="if (confirm('<?=esc_js(__('Are you sure ? ', HERISSON_TD))?>')) { return true; } return false;">
                    <?=__('Delete', HERISSON_TD)?>
                </a>
                <? if ($friend->b_wantsyou) { ?>
                <a href="<?=get_option('siteurl')?>/wp-admin/admin.php?page=herisson_friend&action=approve&id=<?=$friend->id?>" onclick="if (confirm('<?=esc_js(__('Are you sure ? ', HERISSON_TD))?>')) { return true; } return false;">
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
</div>
