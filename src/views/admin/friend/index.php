    <div class="wrap">
        <? echo herisson_messages(); ?>
        <h1>
            <? echo __("All friends", HERISSON_TD); ?>
            <a href="<? echo get_option('siteurl'); ?>/wp-admin/admin.php?page=herisson_friends&action=add&id=0" class="add-new-h2"><? echo __('Add', HERISSON_TD); ?></a>
        </h1>

        <?
            include_partial(__DIR__."/list.php", array(
                "friends" => $actives,
                "title" => __("Active friends", HERISSON_TD),
            ));
            include_partial(__DIR__."/list.php", array(
                "friends" => $youwant,
                "title" => __("Waiting for friend approval", HERISSON_TD),
            ));
            include_partial(__DIR__."/list.php", array(
                "friends" => $wantsyou,
                "title" => __("Asking your permission", HERISSON_TD),
            ));
            include_partial(__DIR__."/list.php", array(
                "friends" => $errors,
                "title" => __("Others", HERISSON_TD),
            ));
        ?>
    </div>

