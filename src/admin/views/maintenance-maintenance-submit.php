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

