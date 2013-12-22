<div class="wrap">
    <h2>
        <? echo $page_title; ?>
        <?= __("Importation results from JSON bookmarks", HERISSON_TD); ?>
    </h2>

    <form method="post" action="<?=get_option('siteurl')?>/wp-admin/admin.php?page=herisson_maintenance">
        <input type="hidden" name="action" value="import_submit" />
        <table class="widefat post">
            <tr>
                <th style="width: 50px"><?=__('Add', HERISSON_TD)?></th>
                <th style="width: 50px"><?=__('Status', HERISSON_TD)?></th>
                <th style="width: 80px"><?=__('Private', HERISSON_TD)?></th>
                <th style="width: 50px"><?=__('Icon', HERISSON_TD)?></th>
                <th><?=__('Title', HERISSON_TD)?></th>
            </tr>
    
        <? 
        $i=0;
        foreach ($bookmarks as $bookmark) {
            $i++;
     
            if ($bookmark['url']) { 
                if (herisson_bookmark_check_duplicate($bookmark['url'])) { 
                    $status = array(
                        "code" => __("Duplicate", HERISSON_TD),
                        "message" => __('This bookmark already exist'),
                        "color" => "red",
                        "error"=>1
                    );
                } else if ($options['checkHttpImport']) {
                    $network = new HerissonNetwork();
                    $status = $network->check($bookmark['url']);
                } else {
                    $status = array(
                        "code" => "No&nbsp;check",
                        "message" => "No check has been processed. See options for more information",
                        "color" => "orange",
                        "error"=>0
                    );
                }
            } else {
                $status = array(
                    "code" => "",
                    "message" => "",
                    "color" => "white",
                    "error"=>1
                );
            }
    
            ?>
            <tr>
                <td>
                <? if ($bookmark['url']) { ?>
                    <input type="checkbox" name="bookmarks[<?=$i?>][import]" <? if (!$status['error']) { ?> checked="checked" <? } ?>/>
                <? } ?>
                </td>
      
                <td style="background-color:<?=$status['color']?>">
                    <span title="<?=sprintf(__('HTTP Error %s : %s', HERISSON_TD), $status['code'], $status['message'])?>" style="font-weight:bold; color:black">
                        <?=$status['code']?>
                    </span>
                </td>
    
                <td>
                    <input type="checkbox" name="bookmarks[<?=$i?>][private]" <? if (!$bookmark['is_public']) { ?> checked="checked" <? } ?>/>&nbsp;<?=__('Private?')?>
                </td>
    
                <td>
                    <input type="hidden" name="bookmarks[<?=$i?>][favicon_image]" value="<?=$bookmark['favicon_image']?>" />
                    <input type="hidden" name="bookmarks[<?=$i?>][favicon_url]" value="<?=$bookmark['favicon_url']?>" />
                    <? if ($bookmark['favicon_image']) { ?>
                    <!-- Favicon -->
                    <img src="data:image/png;base64,<?=$bookmark['favicon_image']?>" alt="" />
                    <? } else if ($bookmark['favicon_url']) { ?>
                    <img src="<?=$bookmark['favicon_url']?>" alt="" />
                    <? } ?>
                </td>
        
                <td>
                    <input type="hidden" name="bookmarks[<?=$i?>][url]" value="<?=$bookmark['url']?>"/>
                    <input type="hidden" name="bookmarks[<?=$i?>][title]" value="<?=$bookmark['title']?>"/>
                    <input type="hidden" name="bookmarks[<?=$i?>][description]" value="<?=$bookmark['description']?>"/>
                    <input type="hidden" name="bookmarks[<?=$i?>][tags]" value="<?=$bookmark['tags']?>"/>
                    <? if (isset($bookmark['prefix'])) { ?>
                    <? echo $bookmark['prefix']; ?>
                    <? } ?>
                    <a href="<?=$bookmark['url']?>" target="_blank">
                        <span class="txt" title="<?=$bookmark['title']?>">
                            <?=$bookmark['title']?>
                        </span>
                    </a>
                </td>
            </tr>
    
    <? 
            flush();
        }
        ?>
        </table>
        <input type="submit" value="<?=__('Import theses bookmarks', HERISSON_TD);?>" />
    </form>
</div>
