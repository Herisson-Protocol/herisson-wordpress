<div class="wrap">
    <h2>
        <?php echo  sprintf(__("Importation results from : %", HERISSON_TD), $format->name); ?>
    </h2>

    <form method="post" action="<?php echo get_option('siteurl')?>/wp-admin/admin.php?page=herisson_import">
        <input type="hidden" name="action" value="importValidate" />
        <table class="widefat post" id="importList">
            <tr>
                <th style="width: 50px"><?php echo __('Add', HERISSON_TD)?></th>
                <th style="width: 100px"><?php echo __('Status', HERISSON_TD)?></th>
                <th style="width: 60px"><?php echo __('Privacy', HERISSON_TD)?></th>
                <th><?php echo __('Title', HERISSON_TD)?></th>
            </tr>
    
        <?php 
        $i=0;
        foreach ($bookmarks as $bookmark) {
            $i++;
     
            if ($bookmark['url']) { 
                if (WpHerissonBookmarksTable::checkDuplicate($bookmark['url'])) { 
                    $status = array(
                        "message" => __("Duplicate", HERISSON_TD),
                        "title" => __('This bookmark already exists'),
                        "color" => "#d97b7b",
                        "error"=>1
                    );
                } else if ($options['checkHttpImport']) {
                    $network = new HerissonNetwork();
                    $status = $network->check($bookmark['url']);
                    if ($status['error']) {
                        $status['color'] = '#d97b7b';
                    } else {
                        $status['color'] = '#63a94b';
                    }
                } else {
                    $status = array(
                        "message" => "No&nbsp;check",
                        "title" => "No check has been processed. See options for more information",
                        "color" => "#ffb73d",
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
                    <!--
                    <input type="checkbox" name="bookmarks[<?php echo $i?>][import]" <?php if (!$status['error']) { ?> checked="checked" <?php } ?>/>
-->
                    <div class="switch switch-block switch-add" id="switch-add-<?php echo $i; ?>">
                        <input type="checkbox" name="bookmarks[<?php echo $i; ?>][import]" class="switch-checkbox" id="add-<?php echo $i; ?>" value="1" <?php if (!$status['error']) { ?> checked="checked" <?php } ?> />
                        <label class="switch-label" for="add-<?php echo $i; ?>">
                            <div class="switch-inner"></div>
                            <div class="switch-switch"></div>
                        </label>
                    </div>
                <?php } ?>
                </td>
      
                <td style="text-align: center">
                    <span title="<?php echo sprintf(__('HTTP Code %s : %s', HERISSON_TD), $status['code'], $status['title'])?>" style="font-weight:bold; color:white; background-color:<?php echo $status['color']?>; padding: 3px; font-size: 12px; white-space: nowrap">
                        <?php echo $status['message']?>
                    </span>
                </td>
    
                <td>
<!--
                    <input type="checkbox" name="bookmarks[<?php echo $i?>][private]" <?php if (isset($bookmark['is_public']) && ! $bookmark['is_public']) { ?> checked="checked" <?php } ?>/>&nbsp;<?php echo __('Private?')?>
-->
                    <div class="switch switch-block switch-privacy" id="switch-privacy-<?php echo $i; ?>">
                        <input type="checkbox" name="bookmarks[<?php echo $i; ?>][private]" class="switch-checkbox" id="privacy-<?php echo $i; ?>" value="1" <?php if (isset($bookmark['is_public']) && ! $bookmark['is_public']) { ?> checked="checked" <?php } ?> />
                        <label class="switch-label" for="privacy-<?php echo $i; ?>">
                            <div class="switch-inner"></div>
                            <div class="switch-switch"></div>
                        </label>
                    </div>
                </td>
    
                <td>
                    <?php if (isset($bookmark['favicon_image'])) { ?>
                    <!-- Favicon -->
                    <input type="hidden" name="bookmarks[<?php echo $i?>][favicon_image]" value="<?php echo $bookmark['favicon_image']?>" />
                    <img src="data:image/png;base64,<?php echo $bookmark['favicon_image']?>" alt="" />
                    <?php } else if (isset($bookmark['favicon_url'])) { ?>
                    <input type="hidden" name="bookmarks[<?php echo $i?>][favicon_url]" value="<?php echo $bookmark['favicon_url']?>" />
                    <img src="<?php echo $bookmark['favicon_url']?>" alt="" />
                    <?php } ?>
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
            if ($i % 10 == 0) {
                flush();
            }
        }
        ?>
        </table>
        <input type="submit" class="button" value="<?php echo __('Import theses bookmarks', HERISSON_TD);?>" />
    </form>
</div>
