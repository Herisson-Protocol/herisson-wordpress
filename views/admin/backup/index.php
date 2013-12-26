<div class="wrap">

    <h2><?php echo __("Backups", HERISSON_TD); ?></h2>

<?php if (sizeof($backups)) { ?>
    <table class="form-table" width="100%" cellspacing="2" cellpadding="5">

        <?php foreach ($backups as $backup) { ?>

        <tr valign="top">
            <th scope="row"><?php echo __('Import bookmarks', HERISSON_TD); ?>:</th>
            <td style="width: 400px">
                <?php echo __('Source', HERISSON_TD); ?> : 
                <select name="import_source">
                    <option value="firefox"><?php echo __('Firefox', HERISSON_TD); ?></option>
                </select>
                <br/>
                <?php echo __('File', HERISSON_TD); ?> :
                <input type="file" name="import_file" />
            </td>
            <td>
                <input type="submit" class="button" value="<?php echo __("Import bookmarks", HERISSON_TD); ?>" />
            </td>
        </tr>
        <?php } ?>
    </table>
<?php } else { ?>
    <?php echo __("No backups yet.", HERISSON_TD); ?>
<?php } ?>
</div>

