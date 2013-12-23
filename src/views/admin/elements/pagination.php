<?
$nb_pages = ceil($all/$limit);

if ($nb_pages > 1) {
?>
<div class="pagination">
<? for ($i_page=1; $i_page<=$nb_pages; $i_page++) {
    $_offset = intval(($i_page-1)*$limit);
    $url = get_option('siteurl').$_SERVER['REQUEST_URI'];
    $url = preg_replace("/&offset=\d*/","",$url);
    #"/wp-admin/admin.php?page=herisson_bookmark";
    #print_r($_SERVER);
    ?>
    <? if ($offset == $_offset) { ?>
        <span class="active" style="font-weight: bold;">
            <? echo $i_page; ?>
        </span>
    <? } else { ?>
        <span class="inactive">
            <a href="<?=$url?>&offset=<? echo $_offset; ?>">
                <? echo $i_page; ?>
            </a>
        </span>
    <? } ?>
<? } ?>
</div>
<? } ?>
