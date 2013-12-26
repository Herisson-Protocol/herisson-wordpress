<?php  foreach ($tags as $tag) { ?>

    <a href="#" class="tag-link-<?php echo $tag->id; ?>" title="<?php echo $tag->c; ?> sujets" style="font-size: <?php echo ( 10+$tag->c*log(2)); ?>pt"><?php echo $tag->name; ?></a>&nbsp; 
<?php } ?>


