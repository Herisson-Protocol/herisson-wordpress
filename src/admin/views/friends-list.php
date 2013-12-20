    <div class="wrap">
        <? echo herisson_messages(); ?>
        <h1>
            <? echo __("All friends", HERISSON_TD); ?>
            <a href="<? echo get_option('siteurl'); ?>/wp-admin/admin.php?page=herisson_friends&action=add&id=0" class="add-new-h2"><? echo __('Add', HERISSON_TD); ?></a>
        </h1>

        <?
            herisson_friend_list_active();
            herisson_friend_list_youwant();
            herisson_friend_list_wantsyou();
            herisson_friend_list_error();
        ?>
    </div>

