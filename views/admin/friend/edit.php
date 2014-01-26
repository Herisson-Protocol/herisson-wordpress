
<div class="wrap">
    <?php $this->includePartial(__DIR__."/../elements/messages.php", array()); ?>
    <h2>
    <?php if ($id) { ?>
        <?php echo __("Edit Friend", HERISSON_TD); ?>
    <?php } else { ?>
        <?php echo __("Add Friend", HERISSON_TD); ?>
    <?php } ?>
    </h2>

    <form method="post" action="<?php echo get_option('siteurl'); ?>/wp-admin/admin.php?page=herisson_friend">

<?php
    if ( function_exists('wp_nonce_field') ) wp_nonce_field('friend-edit');
    if ( function_exists('wp_referer_field') ) wp_referer_field();
?>

        <?php /*
        <div class="book-image">
                        <img style="float:left; margin-right: 10px;" id="book-image-0" alt="Book Cover" src="<?php echo $existing->image; ?>" />
        </div>
        */ ?>

        <?php if ($id) { ?>
        <h3>
            <?php echo __("Friend", HERISSON_TD); ?> <?php echo $existing->id; ?>&nbsp;:
            <cite> &laquo;&nbsp;<?php echo $existing->name; ?>&nbsp;&raquo;</cite>
        </h3>
        <?php } ?>

        <table class="form-table" cellspacing="2" cellpadding="5">

            <input type="hidden" name="action" value="edit" />
            <input type="hidden" name="page" value="herisson_friend" />
            <input type="hidden" name="id" value="<?php echo $existing->id; ?>" />

            <tbody>
                    

                <!-- Alias -->
                <tr class="form-field">
                    <th valign="top" scope="row">
                        <label for="name-0"><?php echo __("Alias", HERISSON_TD); ?>:</label>
                    </th>
                    <td>
                        <input type="text" class="main" id="alias-0" name="alias" value="<?php echo $existing->alias; ?>" />
                    </td>
                </tr>

                <?php if ($id) { ?>
                <!-- Sitename -->
                <tr class="form-field">
                    <th valign="top" scope="row">
                        <label for="name-0"><?php echo __("Official name", HERISSON_TD); ?>:</label>
                    </th>
                    <td>
                        <i><?php echo $existing->name; ?></i>
                    </td>
                </tr>
                <?php } ?>

                <!-- URL -->
                <tr class="form-field">
                    <th valign="top" scope="row">
                        <label for="url-0"><?php echo __("URL", HERISSON_TD); ?>:</label>
                    </th>
                    <td>
                        <input type="text" size="80" class="main" id="url-0" name="url" value="<?php echo $existing->url; ?>" />
                        <?php if ($id) { ?>
                        <br/>
                        <small>
                            <a href="<?php echo $existing->url; ?>" style="text-decoration:none">Visit <?php echo $existing->url; ?></a>
                        </small>
                        <?php } ?>
                    </td>
                </tr>


                <?php if ($id != 0) { ?>
                <!-- Active -->
                <tr class="form-field">
                    <th valign="top" scope="row">
                        <label for="url-0"><?php echo __("Active", HERISSON_TD); ?>:</label>
                    </th>
                    <td>
                        <?php echo $existing->is_active ; ?> | <?php echo $existing->b_youwant; ?> | <?php echo $existing->b_wantsyou; ?>
                        <?php if ($existing->is_active) { ?>
                        <p class="herisson-success">
                            <?php echo __("This friend is active and considered as a Herisson site", HERISSON_TD); ?>
                        </p>
                        <?php } else if ($existing->b_youwant) { ?>
                        <p class="herisson-warnings">
                            <?php echo __("This friend needs to validate your request", HERISSON_TD); ?>
                        </p>
                        <?php } else { ?>
                        <p class="herisson-errors">
                            <?php echo __("This friend is inactive. Maybe it is not a Herisson site", HERISSON_TD); ?>
                        </p>
                        <?php } ?>
                    </td>
                </tr>
                <?php } ?>

            </tbody>
        </table>

        <p class="submit">
            <input class="button" type="submit" value="<?php echo __("Save", HERISSON_TD); ?>" />
        </p>

    </form>

</div>
