
<div class="wrap">
    <?php $this->includePartial(__DIR__."/../elements/messages.php", array()); ?>
    <?php if (Herisson\Message::i()->hasSuccess()) { ?>
    <p class="herisson-success">
        <?php echo __("Maintenance has been done, here are the results after the maintenance operation. Some of the errors may not be fixable.", HERISSON_TD)?>
    </p>
    <?php } ?>

    <?php 
$items = array(
    'favicon'       => array(
        'text'      => __("Favicon", HERISSON_TD),
        'option'    => 'spiderOptionFavicon',
    ),
    'html_content'  => array(
        'text'      => __("HTML content", HERISSON_TD),
        'option'    => 'spiderOptionTextOnly',
    ),
    'full_content'  => array(
        'text'      => __("Full content", HERISSON_TD),
        'option'    => 'spiderOptionFullPage',
    ),
    'screenshot'    => array(
        'text'      => __("Screenshot", HERISSON_TD),
        'option'    => 'spiderOptionScreenshot',
    ),

);
   ?>


    <h1><?php echo __("Maintenance", HERISSON_TD)?></h1>

    <table class="widefat post" id="maintenance">
        <tr>
            <th style="width: 30%"><?php echo __('What is missing', HERISSON_TD); ?></th>
            <th style="width: 100px"><?php echo __('Missing items', HERISSON_TD); ?></th>
            <th style="width: 100px"><?php echo __('Total bookmarks', HERISSON_TD); ?></th>
            <th style="width: 200px"><?php echo __(sprintf('Will be fixed according to<br/><a href="%s/wp-admin/admin.php?page=herisson_option">current configuration</a>',get_option('siteurl')), HERISSON_TD); ?></th>
        </tr>
    <?php foreach ($stats as $stat=>$nb) { ?>
        <tr>
            <td><?php echo $items[$stat]['text']; ?></td>
            <td style="text-align: center">
            <?php
            $ratio = round($nb/$total,2);
            $_type = 'success';
            if ($ratio > 0.7) {
                $_type = "errors";
            } else if ($ratio > 0.3) {
                $_type = "warnings";
            }
            ?>
                <span class="herisson-<?php echo $_type; ?>"> 
                <?php echo $nb; ?>
                </span>
            </td>
            <td style="text-align: center"><?php echo $total; ?></td>
            <td style="text-align: center">
               <?php if ($options[$items[$stat]['option']]) { ?>
                <p class="herisson-success"><?php echo __('Yes', HERISSON_TD); ?></p>
                <?php } else { ?>
                <p class="herisson-errors"><?php echo __('No', HERISSON_TD); ?></p>
                <?php } ?>
            </td>
        </tr>
    <?php } ?>
    </table>
<!--
        <tr>V
            <td><?php echo __("HTML Content", HERISSON_TD)?></td>
        </tr>
        <tr>
            <td><?php echo __("Full Content", HERISSON_TD)?></td>
        </tr>
        <tr>
            <td><?php echo __("Screenshot", HERISSON_TD)?></td>
        </tr>

        <p>
            <?php echo __("Bookmarks with no favicon URL", HERISSON_TD)?> : 
            <?php echo sizeof($bookmarks_no_favicon_url)?> / <?php echo sizeof($bookmarks_all)?>
        </p>

        <h2><?php echo __("Favicon images", HERISSON_TD)?></h2>
        <p>
            <?php echo __("Bookmarks with no favicon Image", HERISSON_TD)?> :
            <?php echo sizeof($bookmarks_no_favicon_image)?> / <?php echo sizeof($bookmarks_all)?>
        </p>

        <h2><?php echo __("Contents", HERISSON_TD)?></h2>
        <p>
            <?php echo __("Bookmarks with no content", HERISSON_TD)?> :
            <?php echo sizeof($bookmarks_no_content)?> / <?php echo sizeof($bookmarks_all)?>
        </p>

        <h2><?php echo __("Screenshots", HERISSON_TD)?></h2>
        <p>
            <?php echo __("Bookmarks with no screenshots", HERISSON_TD)?> : 
            <?php echo sizeof($bookmarks_no_content_image)?> / <?php echo sizeof($bookmarks_all)?>
        </p>
-->

<p>
    <b style="color:red">
        <?php echo __("Warning, this operation can take several minutes (especially for screenshots). If you stop it during the process it's ok and you can do another maintenance operation to finish the maintenance.", HERISSON_TD);?>
    </b>
</p>
<form method="post" action="<?php echo get_option('siteurl')?>/wp-admin/admin.php?page=herisson_maintenance">
    <input type="hidden" name="action" value="index" />
    <input type="hidden" name="maintenance" value="on" />
    <input type="submit" class="button" value="<?php echo __('Start maintenance'); ?>" />
</form>
</div>
