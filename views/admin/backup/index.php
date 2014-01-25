<div class="wrap">

    <h2><?php echo __("Backup of my friends", HERISSON_TD); ?></h2>

    <?php if (sizeof($backups)) { ?>
        <table class="widefat post " cellspacing="0">
            <tr>
                <th style="width: 10%"><?php echo __('Friend', HERISSON_TD)?></th>
                <th style="width: 15%"><?php echo __('Size', HERISSON_TD)?></th>
                <th style="width: 25%"><?php echo __('Number of bookmarks', HERISSON_TD)?></th>
                <th style="width:  5%"><?php echo __('Date', HERISSON_TD)?></th>
                <th style="width: 10%"><?php echo __('Action', HERISSON_TD)?></th>
            </tr>
                <?php foreach ($backups as $backup) { ?>
            <tr>
                <td>
                    <?php echo $friends[$backup->friend_id]; ?>
                </td>
                <td>
                    <?php echo $backup->size?>
                </td>
                <td>
                    <?php echo $backup->nb?>
                </td>
                <td>
                    <?php echo $backup->creation?>
                </td>
            </tr>
            <?php } ?>
        </table>
    <?php } else { ?>
        <?php echo __("No backups yet.", HERISSON_TD); ?>
    <?php } ?>


    <h2><?php echo __("My backups", HERISSON_TD); ?></h2>

    <?php if (sizeof($localbackups)) { ?>
        <table class="widefat post " cellspacing="0">
            <tr>
                <th style="width: 10%"><?php echo __('Friend', HERISSON_TD)?></th>
                <th style="width: 15%"><?php echo __('Size', HERISSON_TD)?></th>
                <th style="width: 25%"><?php echo __('Number of bookmarks', HERISSON_TD)?></th>
                <th style="width:  5%"><?php echo __('Date', HERISSON_TD)?></th>
                <th style="width: 10%"><?php echo __('Action', HERISSON_TD)?></th>
            </tr>
                <?php foreach ($localbackups as $localbackup) { ?>
            <tr>
                <td>
                    <?php echo $friends[$localbackup->friend_id]; ?>
                </td>
                <td>
                    <?php echo $localbackup->size?>
                </td>
                <td>
                    <?php echo $localbackup->nb?>
                </td>
                <td>
                    <?php echo $localbackup->creation?>
                </td>
            </tr>
            <?php } ?>
        </table>
    <?php } else { ?>
        <?php echo __("No backups yet.", HERISSON_TD); ?>
    <?php } ?>


</div>

