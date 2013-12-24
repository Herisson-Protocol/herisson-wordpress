
<div class="wrap">
    <?php includePartial(__DIR__."/../elements/messages.php", array()); ?>
    <h2><?php echo $title; ?></h2>
    <?php if (sizeof($friends)) { ?>
    <table class="widefat post " cellspacing="0">
        <tr>
            <th style="width: 10%"><?php echo __('Alias', HERISSON_TD)?></th>
            <th style="width: 15%"><?php echo __('Official name', HERISSON_TD)?></th>
            <th style="width: 25%"><?php echo __('URL', HERISSON_TD)?></th>
            <th style="width:  5%"><?php echo __('Active', HERISSON_TD)?></th>
            <th style="width:  5%"><?php echo __('Pending', HERISSON_TD)?></th>
            <th style="width:  5%"><?php echo __('Need your validation', HERISSON_TD)?></th>
            <th style="width: 10%"><?php echo __('Action', HERISSON_TD)?></th>
        </tr>
            <?php foreach ($friends as $friend) { ?>
        <tr>
            <td>
                <b>
                    <a href="<?php echo get_option('siteurl')?>/wp-admin/admin.php?page=herisson_friend&action=edit&id=<?php echo $friend->id?>">
                        <?php echo $friend->alias ? $friend->alias : 'Unnamed-'.$friend->id; ?>
                    </a>
                </b>
           </td>
            <td>
                <?php echo $friend->name; ?>
            </td>
            <td>
                <a href="<?php echo $friend->url; ?>" target="friend"><?php echo $friend->url; ?></a>
            </td>
            <td>
                <?php echo $friend->is_active?>
            </td>
            <td>
                <?php echo $friend->b_youwant?>
            </td>
            <td>
                <?php echo $friend->b_wantsyou?>
            </td>
            <td>
                <a href="<?php echo get_option('siteurl')?>/wp-admin/admin.php?page=herisson_friend&action=delete&id=<?php echo $friend->id?>" onclick="if (confirm('<?php echo esc_js(__('Are you sure ? ', HERISSON_TD))?>')) { return true; } return false;">
                    <?php echo __('Delete', HERISSON_TD)?>
                </a>
                <?php if ($friend->b_wantsyou) { ?>
                <a href="<?php echo get_option('siteurl')?>/wp-admin/admin.php?page=herisson_friend&action=approve&id=<?php echo $friend->id?>" onclick="if (confirm('<?php echo esc_js(__('Are you sure ? ', HERISSON_TD))?>')) { return true; } return false;">
                    <?php echo __('Approve', HERISSON_TD)?>
                </a>
                <?php } ?>
            </td>
        </tr>
        <?php } ?>
    </table>
    <?php echo __(sizeof($friends)." friends.", HERISSON_TD); ?>
    <?php } else { ?>
        <?php echo __("No friend", HERISSON_TD); ?>
    <?php } ?>
    <br />
</div>
