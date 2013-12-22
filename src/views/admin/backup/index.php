<div class="wrap">

    <h2><? echo __("Backups", HERISSON_TD); ?></h2>

<? if (sizeof($backups)) { ?>
    <table class="form-table" width="100%" cellspacing="2" cellpadding="5">

        <? foreach ($backups as $backup) { ?>

        <tr valign="top">
            <th scope="row"><? echo __('Import bookmarks', HERISSON_TD); ?>:</th>
            <td style="width: 400px">
                <? echo __('Source', HERISSON_TD); ?> : 
                <select name="import_source">
                    <option value="firefox"><? echo __('Firefox', HERISSON_TD); ?></option>
                </select>
                <br/>
                <? echo __('File', HERISSON_TD); ?> :
                <input type="file" name="import_file" />
            </td>
            <td>
                <input type="submit" value="<? echo __("Import bookmarks", HERISSON_TD); ?>" />
            </td>
        </tr>
        <? } ?>
    </table>
<? } else { ?>
    <? echo __("No backups yet." , HERISSON_TD); ?>
<? } ?>
</div>

