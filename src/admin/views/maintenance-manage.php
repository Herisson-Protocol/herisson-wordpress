
<div class="wrap">

    <h2>
        <? echo __("Import, export, maintenance", HERISSON_TD); ?>
    </h2>

    <table class="form-table" width="100%" cellspacing="2" cellpadding="5">

    <tr>
        <td colspan="3">
            <h3><? echo __('Import', HERISSON_TD); ?></h3>
        </td>
    </tr>
    <form method="post" action="<? echo get_option('siteurl'); ?>/wp-admin/admin.php?page=herisson_maintenance" enctype="multipart/form-data">
    <input type="hidden" name="action" value="import" />
        <tr valign="top">
            <th scope="row"><? echo __('Import bookmarks', HERISSON_TD); ?>:</th>
            <td style="width: 400px">
                <? echo __('Source', HERISSON_TD); ?> : 
                <select name="import_source">
                    <option value="firefox"><? echo __('Firefox', HERISSON_TD); ?></option>
                    <option value="json"><? echo __('JSON', HERISSON_TD); ?></option>
                </select>
                <br/>
                <? echo __('File', HERISSON_TD); ?> :
                <input type="file" name="import_file" />
            </td>
            <td>
                <input type="submit" value="<? echo __("Import bookmarks", HERISSON_TD); ?>" />
            </td>
        </tr>
        </form>

        <form method="post" action="<? echo get_option('siteurl'); ?>/wp-admin/admin.php?page=herisson_maintenance">
         <input type="hidden" name="action" value="import_delicious" />
            <tr valign="top">
                <th scope="row">
                    <? echo __('Import Delicious bookmarks', HERISSON_TD); ?>:
                </th>
                <td style="width: 200px">
                    <? echo __('Login', HERISSON_TD); ?> :<input type="text" name="username_delicious" /><br/>
                    <? echo __('Password', HERISSON_TD); ?> :<input type="password" name="password_delicious" /><br/>
                    <? echo __("Theses informations are not stored by this plugin.", HERISSON_TD); ?>
                </td>
                <td>
              <input type="submit" value="<? echo __("Import bookmarks", HERISSON_TD); ?>" />
                </td>
            </tr>
        </form>

        <tr>
            <td colspan="3">
                <h3>
                    <? echo __('Export', HERISSON_TD); ?>
                </h3>
            </td>
        </tr>
        <form method="post" action="<? echo get_option('siteurl'); ?>/wp-admin/admin.php?page=herisson_maintenance">
            <input type="hidden" name="action" value="export" />
            <input type="hidden" name="nomenu" value="1" />
            <tr valign="top">
                <th scope="row">
                    <? echo __('Export all bookmarks', HERISSON_TD); ?>:
                </th>
                <td>
                    <h4>
                        <? echo __("Format", HERISSON_TD); ?>
                    </h4>
                    <input type="radio" name="format" value="json" checked="checked" /> JSON
                    &nbsp;&nbsp;&nbsp;
                    <input type="radio" name="format" value="csv" /> CSV
                    &nbsp;&nbsp;&nbsp;
                    <input type="radio" name="format" value="Firefox" /> Firefox

                    <h4><? echo __("Options", HERISSON_TD); ?> </h4>
                    <? echo __("Include private bookmarks : ", HERISSON_TD); ?> 
                    <input type="radio" name="private" value="1" /> Yes
                    &nbsp;&nbsp;&nbsp;
                    <input type="radio" name="private" value="0" checked="checked" /> No
                </td>
                <td>
                    <input type="submit" value="<? echo __("Export", HERISSON_TD); ?>" />
                </td>
            </tr>
        </form>

        <tr>
            <td colspan="3">
                <h3>
                    <? echo __('Maintenance', HERISSON_TD); ?>
                </h3>
            </td>
        </tr>
        <form method="post" action="<? echo get_option('siteurl'); ?>/wp-admin/admin.php?page=herisson_maintenance">
            <input type="hidden" name="action" value="maintenance" />
            <tr valign="top">
                <th scope="row">
                    <? echo __('Check Maintenance', HERISSON_TD); ?>:
                </th>
                <td>
                    
                </td>
                <td>
                    <input type="submit" value="<? echo __("Start maintenance checks", HERISSON_TD); ?>" />
                </td>
            </tr>
        </form>
    </table>
</div>

