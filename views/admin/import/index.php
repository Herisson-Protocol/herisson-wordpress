
<div class="wrap">
    <?php includePartial(__DIR__."/../elements/messages.php", array()); ?>
    <table class="form-table" width="100%" cellspacing="2" cellpadding="5">
        <tr>
            <td colspan="3">
                <h2><?php echo __('Import', HERISSON_TD); ?></h2>
            </td>
        </tr>
        <form method="post" action="<?php echo get_option('siteurl'); ?>/wp-admin/admin.php?page=herisson_import" enctype="multipart/form-data">
        <input type="hidden" name="action" value="import" />
            <tr valign="top">
                <th scope="row"><?php echo __('Bookmarks file', HERISSON_TD); ?>:</th>
                <td style="width: 500px">
                    <?php echo __('Source', HERISSON_TD); ?> : 
                    <select name="import_format">
                        <?php foreach ($formatList as $format) { ?>
                            <?php if ($format->type == 'file') { ?>
                            <option value="<?php echo $format->keyword; ?>"><?php echo $format->name; ?></option>
                            <?php } ?>
                        <?php } ?>
                    </select>
                    <br/>
                    <?php echo __('File', HERISSON_TD); ?> :
                    <input type="file" name="import_file" />
                </td>
                <td>
                    <input type="submit" class="button" value="<?php echo __("Import bookmarks", HERISSON_TD); ?>" />
                </td>
            </tr>
        </form>

        <?php foreach ($formatList as $format) { ?>
            <?php if ($format->type != 'file') { ?>
        <form method="post" action="<?php echo get_option('siteurl'); ?>/wp-admin/admin.php?page=herisson_import">
         <input type="hidden" name="action" value="import" />
         <input type="hidden" name="import_format" value="<?php echo $format->keyword; ?>" />
            <tr valign="top">
                <th scope="row">
                    <?php echo $format->name; ?>:
                </th>
                <td>
                    <?php echo $format->getForm(); ?>
                </td>
                <td>
              <input type="submit" class="button" value="<?php echo __("Import bookmarks", HERISSON_TD); ?>" />
                </td>
            </tr>
        </form>
            <?php } ?>
        <?php } ?>

        <tr>
            <td colspan="3">
                <h2>
                    <?php echo __('Export', HERISSON_TD); ?>
                </h2>
            </td>
        </tr>
        <form method="post" action="<?php echo get_option('siteurl'); ?>/wp-admin/admin.php?page=herisson_import">
            <input type="hidden" name="action" value="export" />
            <input type="hidden" name="nomenu" value="1" />
            <tr valign="top">
                <th scope="row">
                    <?php echo __('Options', HERISSON_TD); ?>:
                </th>
                <td style="width: 500px">
                    <?php echo __("Include private bookmarks : ", HERISSON_TD); ?> 
                    <input type="radio" name="private" value="1" /> Yes
                    &nbsp;&nbsp;&nbsp;
                    <input type="radio" name="private" value="0" checked="checked" /> No
                </td>
            </tr>
            <tr valign="top">
                <th scope="row">
                    <?php echo __('File format', HERISSON_TD); ?>:
                </th>
                <td>
                    <select name="export_format">
                        <?php foreach ($formatList as $format) { ?>
                            <?php if ($format->type == 'file') { ?>
                            <option value="<?php echo $format->keyword; ?>"><?php echo $format->name; ?></option>
                            <?php } ?>
                        <?php } ?>
                    </select>

                </td>
                <td>
                    <input type="submit" class="button" value="<?php echo __("Export", HERISSON_TD); ?>" />
                </td>
            </tr>
        </form>
    </table>
</div>
