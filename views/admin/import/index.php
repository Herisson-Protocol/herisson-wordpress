<script type="text/javascript">
jQuery(document).ready(function() {
    // On each export button, add a click event
    jQuery(".exportButton").click(function(event) {
        var form = event.target.form;
        // Move all exportOptions input to the same form as the exportButton clicked.
        jQuery("input[name*=exportOptions]").each(function(){
            jQuery(this).clone().appendTo(event.target.form);
        });
    });
});
</script>
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
                            <?php if ($format->type == 'file' && $format->doImport()) { ?>
                            <option value="<?php echo $format->keyword; ?>"><?php echo $format->name; ?></option>
                            <?php } ?>
                        <?php } ?>
                    </select>
                    <br/>
                    <?php echo __('File', HERISSON_TD); ?> :
                    <input type="file" name="import_file" />
                </td>
                <td>
                    <input type="submit" class="button importButton" value="<?php echo __("Import", HERISSON_TD); ?>" />
                </td>
            </tr>
        </form>

        <?php foreach ($formatList as $format) { ?>
            <?php if ($format->type != 'file' && $format->doImport()) { ?>
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
              <input type="submit" class="button importButton" value="<?php echo __("Import", HERISSON_TD); ?>" />
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
        <form action="/" method="post">
            <tr valign="top">
                <th scope="row">
                    <?php echo __('Options', HERISSON_TD); ?>:
                </th>
                <td style="width: 500px">
                    <?php echo __("Include private bookmarks : ", HERISSON_TD); ?> 
                    <input type="radio" name="exportOptions[private]" value="1" /> Yes
                    &nbsp;&nbsp;&nbsp;
                    <input type="radio" name="exportOptions[private]" value="0" checked="checked" /> No
                    <br/><br/>
                    <label>
                        <?php echo __('Keyword (optional)', HERISSON_TD); ?>:<br/>
                        <input type="text" name="exportOptions[keyword]" placeholder="Add a keyword to be more specific" style="width: 300px" />
                    </label>
                    <br/>
                </td>
            </tr>
        </form>
        <form method="post" id="exportFile" action="<?php echo get_option('siteurl'); ?>/wp-admin/admin.php?page=herisson_import">
            <input type="hidden" name="nomenu" value="1" />
            <input type="hidden" name="action" value="export" />
            <tr valign="top">
                <th scope="row">
                    <?php echo __('File format', HERISSON_TD); ?>:
                </th>
                <td>
                    <select name="export_format">
                        <?php foreach ($formatList as $format) { ?>
                            <?php if ($format->type == 'file'  && $format->doExport()) { ?>
                            <option value="<?php echo $format->keyword; ?>"><?php echo $format->name; ?></option>
                            <?php } ?>
                        <?php } ?>
                    </select>

                </td>
                <td>
                    <input type="submit" class="button exportButton" value="<?php echo __("Export", HERISSON_TD); ?>" />
                </td>
            </tr>
        </form>

        <?php foreach ($formatList as $format) { ?>
            <?php if ($format->type != 'file' && $format->doExport()) { ?>
        <form method="post" id="export<?php echo ucfirst($format->keyword); ?>action="<?php echo get_option('siteurl'); ?>/wp-admin/admin.php?page=herisson_import">
            <input type="hidden" name="nomenu" value="1" />
            <input type="hidden" name="action" value="export" />
            <input type="hidden" name="import_format" value="<?php echo $format->keyword; ?>" />
            <tr valign="top">
                <th scope="row">
                    <?php echo $format->name; ?>:
                </th>
                <td>
                    <?php echo $format->getForm(); ?>
                </td>
                <td>
                    <input type="submit" class="button exportButton" value="<?php echo __("Export", HERISSON_TD); ?>" />
                </td>
            </tr>
        </form>
            <?php } ?>
        <?php } ?>

    </table>
</div>
