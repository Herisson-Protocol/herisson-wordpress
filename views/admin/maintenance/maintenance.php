<?php if (isset($bookmarks_errors) && sizeof($bookmarks_errors)) { ?>
<div style="width: 1000px; height: 300px; overflow:scroll; ">
<?php
    foreach ($bookmarks_errors as $b) {
        $b->maintenance();
        $b->captureFromUrl();
        $b->save();
    }
    ?>
</div>

<p class="herisson-success">
    <?php echo __("Maintenance has been done, here are the results after the maintenance operation. Some of the errors may not be fixable.", HERISSON_TD)?>
</p>
<?php } ?>

<h1><?php echo __("Maintenance", HERISSON_TD)?></h1>
<h2><?php echo __("Favicon url", HERISSON_TD)?></h2>
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


<p>
    <b style="color:red">
        <?php echo __("Warning, this operation can take several minutes (especially for screenshots). If you stop it during the process it's ok and you can do another maintenance operation to finish the maintenance.", HERISSON_TD);?>
    </b>
</p>
<form method="post" action="<?php echo get_option('siteurl')?>/wp-admin/admin.php?page=herisson_maintenance">
    <input type="hidden" name="action" value="maintenance" />
    <input type="hidden" name="maintenance" value="on" />
    <input type="submit" class="button" value="Correct theses errors" />
</form>
