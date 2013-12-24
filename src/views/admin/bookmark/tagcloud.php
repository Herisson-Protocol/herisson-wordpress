<?  foreach ($tags as $tag) { ?>

    <a href="#" class="tag-link-<? echo $tag->id; ?>" title="<? echo $tag->c; ?> sujets" style="font-size: <? echo ( 10+$tag->c*log(2)); ?>pt"><? echo $tag->name; ?></a>&nbsp; 
<? } ?>


