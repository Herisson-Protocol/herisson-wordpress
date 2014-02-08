
<div class="wrap">
    <?php $this->includePartial(__DIR__."/../elements/messages.php", array()); ?>
    
    <h2><?php echo __("Backup my bookmarks", HERISSON_TD); ?></h2>

    <form action="" method="post">
        <input type="hidden" name="action" value="add" />
        <table class="form-table" style="width: 300px" cellspacing="2" cellpadding="5">
            <tr>
                <td><?php echo __('Estimated size', HERISSON_TD); ?></td>
                <td><?php echo $sizeBookmarks; ?></td>
            </tr>
            <tr>
                <td><?php echo __('Number of bookmarks', HERISSON_TD); ?></td>
                <td><?php echo $nbBookmarks; ?></td>
            </tr>
            <tr>
                <td><?php echo __('Friend', HERISSON_TD); ?></td>
                <td>
                    <select name="friend_id">
                        <option value=""><?php echo __("Pick a friend", HERISSON_TD); ?></option>
                        <?php foreach ($friends as $friend) { ?>
                        <option value="<?php echo $friend->id; ?>"><?php echo $friend->name; ?></option>
                        <?php } ?>
                    </select>
                </td>
            </tr>
            <tr>
                <td colspan="2" style="text-align: right">
                    <input type="submit" class="button" value="<?php echo __('Start backup', HERISSON_TD); ?>" />
                </td>
            </tr>
        </table>
    </form>

    <br/>
    <h2><?php echo __("My backups", HERISSON_TD); ?></h2>

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
                    <?php echo $friends[$backup->friend_id]->name; ?>
                </td>
                <td>
                    <?php echo \Herisson\Folder::formatSize($backup->size); ?>
                </td>
                <td>
                    <?php echo $backup->nb?>
                </td>
                <td>
                    <?php echo $backup->creation?>
                </td>
                <td>
                    <a href="<?php echo get_option('siteurl')?>/wp-admin/admin.php?page=herisson_backup&action=download&id=<?php echo $backup->friend_id?>">Download</a>
                    <a href="<?php echo get_option('siteurl')?>/wp-admin/admin.php?page=herisson_backup&action=import&id=<?php echo $backup->friend_id?>">Import</a>
                </td>
            </tr>
            <?php } ?>
        </table>
    <?php } else { ?>
        <?php echo __("No backups yet.", HERISSON_TD); ?>
    <?php } ?>


    <br/>
    <h2><?php echo __("Backup of my friends", HERISSON_TD); ?></h2>

    <?php if (sizeof($localbackups)) { ?>
        <table class="widefat post " cellspacing="0">
            <tr>
                <th style="width: 10%"><?php echo __('Friend', HERISSON_TD)?></th>
                <th style="width: 15%"><?php echo __('Size', HERISSON_TD)?></th>
                <th style="width: 25%"><?php echo __('Filename', HERISSON_TD)?></th>
                <th style="width:  5%"><?php echo __('Date', HERISSON_TD)?></th>
                <th style="width: 10%"><?php echo __('Action', HERISSON_TD)?></th>
            </tr>
                <?php foreach ($localbackups as $localbackup) { ?>
            <tr>
                <td>
                    <?php echo $friends[$localbackup->friend_id]->name; ?>
                </td>
                <td>
                    <?php echo \Herisson\Folder::formatSize($localbackup->size); ?>
                </td>
                <td>
                    <?php echo $localbackup->filename?>
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

