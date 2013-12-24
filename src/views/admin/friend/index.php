<div class="wrap">

    <?php includePartial(__DIR__."/../elements/messages.php", array()); ?>
    <h1>
        <?php echo __("All friends", HERISSON_TD); ?>
        <a href="<?php echo get_option('siteurl'); ?>/wp-admin/admin.php?page=herisson_friend&action=add&id=0" class="add-new-h2"><?php echo __('Add', HERISSON_TD); ?></a>
    </h1>

    <?php
        includePartial(__DIR__."/list.php", array(
            "friends" => $actives,
            "title" => __("Active friends", HERISSON_TD),
        ));
        includePartial(__DIR__."/list.php", array(
            "friends" => $youwant,
            "title" => __("Waiting for friend approval", HERISSON_TD),
        ));
        includePartial(__DIR__."/list.php", array(
            "friends" => $wantsyou,
            "title" => __("Asking your permission", HERISSON_TD),
        ));
        includePartial(__DIR__."/list.php", array(
            "friends" => $errors,
            "title" => __("Others", HERISSON_TD),
        ));
    ?>
</div>

