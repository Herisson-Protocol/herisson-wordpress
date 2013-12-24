<div class="wrap">
    <h2>
        <?php if (isset($page_title)) { ?>
            <?php echo $page_title; ?>
        <?php } ?>
        <?php echo  __("Importation results from JSON bookmarks", HERISSON_TD); ?>
    </h2>

    <form method="post" action="<?php echo get_option('siteurl')?>/wp-admin/admin.php?page=herisson_maintenance">
        <input type="hidden" name="action" value="importValidate" />
        <table class="widefat post">
            <tr>
                <th style="width: 50px"><?php echo __('Add', HERISSON_TD)?></th>
                <th style="width: 50px"><?php echo __('Status', HERISSON_TD)?></th>
                <th style="width: 80px"><?php echo __('Private', HERISSON_TD)?></th>
                <th style="width: 50px"><?php echo __('Icon', HERISSON_TD)?></th>
                <th><?php echo __('Title', HERISSON_TD)?></th>
            </tr>
    
        <?php 
        $i=0;
        foreach ($bookmarks as $bookmark) {
            $i++;
     
            if ($bookmark['url']) { 
                if (WpHerissonBookmarksTable::checkDuplicate($bookmark['url'])) { 
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
                <?php if ($bookmark['url']) { ?>
                    <input type="checkbox" name="bookmarks[<?php echo $i?>][import]" <?php if (!$status['error']) { ?> checked="checked" <?php } ?>/>
                <?php } ?>
                </td>
      
                <td style="background-color:<?php echo $status['color']?>">
                    <span title="<?php echo sprintf(__('HTTP Error %s : %s', HERISSON_TD), $status['code'], $status['message'])?>" style="font-weight:bold; color:black">
                        <?php echo $status['code']?>
                    </span>
                </td>
    
                <td>
                    <input type="checkbox" name="bookmarks[<?php echo $i?>][private]" <?php if (!$bookmark['is_public']) { ?> checked="checked" <?php } ?>/>&nbsp;<?php echo __('Private?')?>
                </td>
    
                <td>
                    <input type="hidden" name="bookmarks[<?php echo $i?>][favicon_image]" value="<?php echo $bookmark['favicon_image']?>" />
                    <input type="hidden" name="bookmarks[<?php echo $i?>][favicon_url]" value="<?php echo $bookmark['favicon_url']?>" />
                    <?php if ($bookmark['favicon_image']) { ?>
                    <!-- Favicon -->
                    <img src="data:image/png;base64,<?php echo $bookmark['favicon_image']?>" alt="" />
                    <?php } else if ($bookmark['favicon_url']) { ?>
                    <img src="<?php echo $bookmark['favicon_url']?>" alt="" />
                    <?php } ?>
                </td>
        
                <td>
                    <input type="hidden" name="bookmarks[<?php echo $i?>][url]" value="<?php echo $bookmark['url']?>"/>
                    <input type="hidden" name="bookmarks[<?php echo $i?>][title]" value="<?php echo $bookmark['title']?>"/>
                    <input type="hidden" name="bookmarks[<?php echo $i?>][description]" value="<?php echo $bookmark['description']?>"/>
                    <input type="hidden" name="bookmarks[<?php echo $i?>][tags]" value="<?php echo $bookmark['tags']?>"/>
                    <?php if (isset($bookmark['prefix'])) { ?>
                    <?php echo $bookmark['prefix']; ?>
                    <?php } ?>
                    <a href="<?php echo $bookmark['url']?>" target="_blank">
                        <span class="txt" title="<?php echo $bookmark['title']?>">
                            <?php echo $bookmark['title']?>
                        </span>
                    </a>
                </td>
            </tr>
    
            <?php 
            flush();
        }
        ?>
        </table>
        <input type="submit" class="button" value="<?php echo __('Import theses bookmarks', HERISSON_TD);?>" />
    </form>
</div>
