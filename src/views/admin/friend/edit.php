
<div class="wrap">
    <? echo herisson_messages(); ?>
    <h2>
    <? if ($id) { ?>
        <? echo __("Edit Friend", HERISSON_TD); ?>
    <? } else { ?>
        <? echo __("Add Friend", HERISSON_TD); ?>
    <? } ?>
    </h2>

    <!--<form method="post" action="<? echo get_option('siteurl'); ?>/wp-content/plugins/herisson/admin/action-friend-edit.php">-->
    <form method="post" action="<? echo get_option('siteurl'); ?>/wp-admin/admin.php?page=herisson_friends">
            <!-- ?page=herisson_friends&action=submitedit&id=<? echo $id; ?>"> -->

<?
    if ( function_exists('wp_nonce_field') ) wp_nonce_field('friend-edit');
    if ( function_exists('wp_referer_field') ) wp_referer_field();
?>

        <? /*
        <div class="book-image">
                        <img style="float:left; margin-right: 10px;" id="book-image-0" alt="Book Cover" src="<? echo $existing->image; ?>" />
        </div>
        */ ?>

        <? if ($id) { ?>
        <h3>
            <? echo __("Friend", HERISSON_TD); ?> <? echo $existing->id; ?>:<cite> &laquo;&nbsp;<? echo $existing->name; ?>&nbsp;&raquo;</cite>
        </h3>
        <? } ?>

        <table class="form-table" cellspacing="2" cellpadding="5">

            <input type="hidden" name="action" value="edit" />
            <input type="hidden" name="page" value="herisson_friends" />
            <input type="hidden" name="id" value="<? echo $existing->id; ?>" />

            <tbody>
                    

                <!-- Alias -->
                <tr class="form-field">
                    <th valign="top" scope="row">
                        <label for="name-0"><? echo __("Alias", HERISSON_TD); ?>:</label>
                    </th>
                    <td>
                        <input type="text" class="main" id="alias-0" name="alias" value="<? echo $existing->alias; ?>" />
                    </td>
                </tr>

                <!-- Sitename -->
                <tr class="form-field">
                    <th valign="top" scope="row">
                        <label for="name-0"><? echo __("Official name", HERISSON_TD); ?>:</label>
                    </th>
                    <td>
                        <i><? echo $existing->name; ?></i>
                    </td>
                </tr>

                <!-- URL -->
                <tr class="form-field">
                    <th valign="top" scope="row">
                        <label for="url-0"><? echo __("URL", HERISSON_TD); ?>:</label>
                    </th>
                    <td>
                        <input type="text" size="80" class="main" id="url-0" name="url" value="<? echo $existing->url; ?>" />
                        <br/><small><a href="<? echo $existing->url; ?>" style="text-decoration:none">Visit <? echo $existing->url; ?></a></small>
                    </td>
                </tr>


                <? if ($id != 0) { ?>
                <!-- Active -->
                <tr class="form-field">
                    <th valign="top" scope="row">
                        <label for="url-0"><? echo __("Active", HERISSON_TD); ?>:</label>
                    </th>
                    <td>
                        <? echo $existing->is_active ; ?> | <? echo $existing->b_youwant; ?> | <? echo $existing->b_wantsyou; ?>
                        <? if ($existing->is_active) { ?>
                        <p class="herisson-success">
                            <? echo __("This friend is active and considered as a Herisson site", HERISSON_TD); ?>
                        </p>
                        <? } else if ($existing->b_youwant) { ?>
                        <p class="herisson-warnings">
                            <? echo __("This friend needs to validate your request", HERISSON_TD) ; ?>
                        </p>
                        <? } else { ?>
                        <p class="herisson-errors">
                            <? echo __("This friend is inactive. Maybe it is not a Herisson site", HERISSON_TD); ?>
                        </p>
                        <? } ?>
                    </td>
                </tr>
                <? } ?>

            </tbody>
        </table>

        <p class="submit">
            <input class="button" type="submit" value="<? echo __("Save", HERISSON_TD); ?>" />
        </p>

    </form>

</div>
