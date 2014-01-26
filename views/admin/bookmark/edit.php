<div class="wrap">
    <?php $this->includePartial(__DIR__."/../elements/messages.php", array()); ?>
    <h2>
    <?php if ($id) { ?>
        <?php echo __("Edit Bookmark", HERISSON_TD); ?>
    <?php } else { ?>
        <?php echo __("Add Bookmark", HERISSON_TD); ?>
    <?php } ?>
    </h2>

    <form method="post" action="<?php echo get_option('siteurl'); ?>/wp-admin/admin.php?page=herisson_bookmark">

<?php
 if ( function_exists('wp_nonce_field') ) wp_nonce_field('bookmark-edit');
 if ( function_exists('wp_referer_field') ) wp_referer_field();
?>

        <?php if ($id) { ?>
        <h3>
            <?php echo __("Bookmark", HERISSON_TD); ?> <?php echo $existing->id; ?>&nbsp;:
            <cite> &laquo;&nbsp;<?php echo $existing->title ?>&nbsp;&raquo;</cite>
        </h3>
        <?php } ?>
    
        <input type="hidden" name="action" value="edit" />
        <input type="hidden" name="page" value="herisson_bookmark" />
        <input type="hidden" name="id" value="<?php echo $existing->id; ?>" />
    
        <table class="form-table" cellspacing="2" cellpadding="5">
    
            <tbody>
    
                <!-- Title -->
                <tr class="form-field">
                    <th valign="top" scope="row">
                        <label for="title-0"><?php echo __("Title", HERISSON_TD); ?>:</label>
                    </th>
                    <td>
                        <input type="text" class="main" id="title-0" name="title" value="<?php echo $existing->title ?>" />
                    </td>
                    <td rowspan="5" style="text-align: center; vertical-align: top">
                    <!--
                    <br/>
                        <b><a href="/wp-admin/admin.php?page=herisson_bookmark&action=view&id='.$existing->id.'&nomenu=1" target="_blank"><?php echo __('View archive', HERISSON_TD) ?></a></b><br/><br/>
                    <br/>
                    -->
                    <?php if ($id) { ?>
                    <?php if (file_exists($existing->getImage()) && filesize($existing->getImage())) { ?>
                        <a href="<?php echo $existing->getImageUrl(); ?>">
                            <img alt="Capture" src="<?php echo $existing->getThumbUrl(); ?>" style="border:0.5px solid black" />
                        </a>
                    <?php } ?>
                    <br/><br/>
                    <b>
                        <a href="<?php echo $existing->getDirUrl(); ?>" target="_blank">
                        <?php echo __('View archive', HERISSON_TD); ?>
                        </a>
                    </b>
                    <br/><br/>
                    <b>
                        <a href="/wp-admin/admin.php?page=herisson_bookmark&action=download&id=<?php echo $existing->id ?>">
                            <img src="<?php echo HERISSON_PLUGIN_URL; ?>/images/ico-download.png" /><br/>
                            <?php echo __('Download', HERISSON_TD); ?>
                        </a>
                    </b>
                     <?php } ?>
                    </td>
                </tr>
            
                <!-- URL -->
                <tr class="form-field">
                    <th valign="top" scope="row">
                        <label for="url-0"><?php echo __("URL", HERISSON_TD); ?>:</label>
                    </th>
                    <td>
                        <?php if ($existing->favicon_image) { ?>
                              <img src="data:image/png;base64,<?php echo $existing->favicon_image; ?>"/>
                        <?php } ?>
                        <input type="text" size="80" class="main" id="url-0" name="url" value="<?php echo $existing->url; ?>" <?php if ($id) { ?> readonly="readonly"<?php } ?> />
                        <?php if ($id) { ?>
                        <br/><small><a href="<?php echo $existing->url; ?>" style="text-decoration:none">Visit <?php echo $existing->url; ?></a></small>
                        <?php } ?>
                    </td>
                </tr>
            
                <!-- Description -->
                <tr class="form-field">
                    <th valign="top" scope="row">
                    <label for="description-0"><?php echo __("Description", HERISSON_TD); ?>:</label>
                    </th>
                    <td>
                    <textarea class="main" id="description-0" name="description"><?php echo $existing->description; ?></textarea>
                    </td>
                </tr>
        
                <?php
                /*
                <!-- Image URL -->
                <tr class="form-field">
                    <th valign="top" scope="row">
                        <label for="image-0"><?php echo __("Book Image URL", HERISSON_TD); ?>:</label>
                    </th>
                    <td>
                        <input type="text" class="main" id="image-0" name="image" value="<?php echo htmlentities($existing->image); ?>" />
                    </td>
                </tr>
                */ ?>

                <?php /*
                <?php if ($id) { ?>
                <!-- Favicon -->
                <tr class="form-field">
                    <th valign="top" scope="row">
                        <label for="url-0"><?php echo __("Favicon", HERISSON_TD); ?>:</label>
                    </th>
                    <td>
                        <input type="text" size="80" class="main" id="url-0" name="favicon_url" value="<?php echo $existing->favicon_url; ?>" readonly="readonly" />
                    </td>
                </tr>
                <?php } ?>
                 */ ?>
        
                <?php if ($id) { ?>
                <!-- Content -->
                <tr class="form-field">
                    <th valign="top" scope="row">
                        <label for="content-0"><?php echo __("HTML Content", HERISSON_TD); ?>:</label>
                    </th>
                    <td>
                        <?php if ($existing->content) { ?>
                        <span class="herisson-success"><?php echo __("Yes", HERISSON_TD); ?></span>
                        <?php } else { ?>
                        <span class="herisson-errors"><?php echo __("No", HERISSON_TD); ?></span>
                        <?php } ?>
                    </td>
                </tr>
                <?php } ?>
        
                <?php if ($id) { ?>
                <!-- Full content -->
                <tr class="form-field">
                    <th valign="top" scope="row">
                        <label for="content-0"><?php echo __("Full content", HERISSON_TD); ?>:</label>
                    </th>
                    <td>
                        <?php if ($existing->hasFullContent()) { ?>
                        <span class="herisson-success"><?php echo __("Yes", HERISSON_TD); ?></span>
                        <?php } else { ?>
                        <span class="herisson-errors"><?php echo __("No", HERISSON_TD); ?></span>
                        <?php } ?>
                    </td>
                </tr>
                <?php } ?>
        
                <?php if ($id) { ?>
                <!-- Type -->
                <tr class="form-field">
                    <th valign="top" scope="row">
                        <label for="type-0"><?php echo __("Type", HERISSON_TD); ?>:</label>
                    </th>
                    <td>
                        <?php echo $existing->content_type; ?>
                    </td>
                </tr>
                <?php } ?>
        
                <?php if ($id) { ?>
                <!-- Archive size -->
                <tr class="form-field">
                    <th valign="top" scope="row">
                        <label for="size-0"><?php echo __("Archive size", HERISSON_TD); ?>:</label>
                    </th>
                    <td>
                        <?php echo \Herisson\Folder::formatSize($existing->dirsize); ?>
                    </td>
                </tr>
                <?php } ?>
        
                <!-- Visibility -->
                <tr class="form-field">
                    <th valign="top" scope="row">
                        <label for="visibility-0"><?php echo __("Visibility", HERISSON_TD); ?>:</label>
                    </th>
                    <td>
                        <select name="is_public" id="visibility-0">
                            <option value="0"<?php echo (!$existing->is_public ? ' selected="selected"' : ''); ?>><?php echo __("Private", HERISSON_TD); ?></option>
                            <option value="1"<?php echo ($existing->is_public ? ' selected="selected"' : ''); ?>><?php echo __("Public", HERISSON_TD); ?></option>
                        </select>
                        <br/><small><?php echo __("<code>Public Visibility</code> enables a bookmark to appear publicly within the herisson page.", HERISSON_TD); ?></small>
                        <br/><small><?php echo __("<code>Private Visibility</code> restricts the visibility of a book to within the administrative interface.", HERISSON_TD); ?></small>
                    </td>
                </tr>
        
                <tr class="form-field">
                    <th valign="top" scope="row">
                        <label for="visibility-0"><?php echo __("Tags", HERISSON_TD); ?>:</label>
                    </th>
                    <td>
                        <script src="/wp-content/plugins/herisson/js/herisson.dev.js" type="text/javascript"></script>
                        <script>
                         jQuery(document).ready(function($) {
                          $('#tagsdiv-post_tag, #categorydiv').children('h3, .handlediv').click(function() {
                           $(this).siblings('.inside').toggle();
                          });
                         });
                        </script>
                    </td>
                </tr>
            </tbody>
        </table>
        <div id="tagsdiv-post_tag" class="postbox">
            <div class="handlediv" title="<?php esc_attr_e('Click to toggle'); ?>"><br /></div>
            <h3>
                <span><?php _e('Tags'); ?></span>
            </h3>
            <div class="inside">
                <div class="tagsdiv" id="post_tag">
                    <div class="jaxtag">
                        <label class="screen-reader-text" for="newtag"><?php _e('Tags'); ?></label>
                        <input type="hidden" name="tags" class="the-tags" id="tags" value="<?php foreach ($tags as $tag) { echo $tag; echo ","; } ?>" />
                        <div class="ajaxtag">
                            <input type="text" name="newtags" class="newtag form-input-tip" size="16" autocomplete="off" value="" />
                            <input type="button" class="button tagadd" value="<?php esc_attr_e('Add'); ?>" tabindex="3" />
                        </div>
                    </div>
                    <div class="tagchecklist" id="tagchecklist">
                    </div>
                </div>
                <p class="tagcloud-link">
                    <a href="#titlediv" class="tagcloud-link" id="link-post_tag">
                        <?php _e('Choose from the most used tags'); ?>
                    </a>
                </p>
            </div>
        </div>
    
        <p class="submit">
            <input class="button" type="submit" value="<?php echo __("Save", HERISSON_TD); ?>" />
        </p>

    </form>

</div>

