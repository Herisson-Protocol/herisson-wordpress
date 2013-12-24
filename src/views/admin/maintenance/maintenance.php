<? if (isset($bookmarks_errors) && sizeof($bookmarks_errors)) { ?>
<div style="width: 1000px; height: 300px; overflow:scroll; ">
<?
    foreach ($bookmarks_errors as $b) {
        $b->maintenance();
        $b->captureFromUrl();
        $b->save();
    }
    ?>
</div>

<p class="herisson-success">
    <?=__("Maintenance has been done, here are the results after the maintenance operation. Some of the errors may not be fixable.", HERISSON_TD)?>
</p>
<? } ?>

<h1><?=__("Maintenance", HERISSON_TD)?></h1>
<h2><?=__("Favicon url", HERISSON_TD)?></h2>
<p>
    <?=__("Bookmarks with no favicon URL", HERISSON_TD)?> : 
    <?=sizeof($bookmarks_no_favicon_url)?> / <?=sizeof($bookmarks_all)?>
</p>

<h2><?=__("Favicon images", HERISSON_TD)?></h2>
<p>
    <?=__("Bookmarks with no favicon Image", HERISSON_TD)?> :
    <?=sizeof($bookmarks_no_favicon_image)?> / <?=sizeof($bookmarks_all)?>
</p>

<h2><?=__("Contents", HERISSON_TD)?></h2>
<p>
    <?=__("Bookmarks with no content", HERISSON_TD)?> :
    <?=sizeof($bookmarks_no_content)?> / <?=sizeof($bookmarks_all)?>
</p>

<h2><?=__("Screenshots", HERISSON_TD)?></h2>
<p>
    <?=__("Bookmarks with no screenshots", HERISSON_TD)?> : 
    <?=sizeof($bookmarks_no_content_image)?> / <?=sizeof($bookmarks_all)?>
</p>


<p>
    <b style="color:red">
        <?=__("Warning, this operation can take several minutes (especially for screenshots). If you stop it during the process it's ok and you can do another maintenance operation to finish the maintenance.", HERISSON_TD);?>
    </b>
</p>
<form method="post" action="<?=get_option('siteurl')?>/wp-admin/admin.php?page=herisson_maintenance">
    <input type="hidden" name="action" value="maintenance" />
    <input type="hidden" name="maintenance" value="on" />
    <input type="submit" value="Correct theses errors" />
</form>
