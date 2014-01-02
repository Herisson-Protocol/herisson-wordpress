<?php

$nb_pages = $limit ? ceil($all/$limit) : 1;

if ($nb_pages > 1) {
?>
<div class="pagination">
<?php for ($i_page=1; $i_page<=$nb_pages; $i_page++) {
    $_offset = intval(($i_page-1)*$limit);
    $url = get_option('siteurl').$_SERVER['REQUEST_URI'];
    $url = preg_replace("/&offset=\d*/", "", $url);
    ?>
    <?php if ($offset == $_offset) { ?>
        <span class="active" style="font-weight: bold;">
            <?php echo $i_page; ?>
        </span>
    <?php } else { ?>
        <span class="inactive">
            <a href="<?php echo $url?>&offset=<?php echo $_offset; ?>">
                <?php echo $i_page; ?>
            </a>
        </span>
    <?php } ?>
<?php } ?>
</div>
<?php } ?>
