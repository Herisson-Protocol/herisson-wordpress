<div class="wrap">
    <? include_partial(__DIR__."/../elements/messages.php",array()); ?>
    <h2>
    <? if ($id) { ?>
        <? echo __("Edit Bookmark", HERISSON_TD); ?>
    <? } else { ?>
        <? echo __("Add Bookmark", HERISSON_TD); ?>
    <? } ?>
    </h2>

    <form method="post" action="<? echo get_option('siteurl'); ?>/wp-admin/admin.php?page=herisson_bookmark">

<?
 if ( function_exists('wp_nonce_field') ) wp_nonce_field('bookmark-edit');
 if ( function_exists('wp_referer_field') ) wp_referer_field();
?>

        <? if ($id) { ?>
        <h3>
            <? echo __("Bookmark", HERISSON_TD); ?> <? echo $existing->id; ?>&nbsp;:
            <cite> &laquo;&nbsp;<? echo $existing->title ?>&nbsp;&raquo;</cite>
        </h3>
        <? } ?>
    
        <input type="hidden" name="action" value="edit" />
        <input type="hidden" name="page" value="herisson_bookmark" />
        <input type="hidden" name="id" value="<? echo $existing->id; ?>" />
    
        <table class="form-table" cellspacing="2" cellpadding="5">
    
            <tbody>
    
                <!-- Title -->
                <tr class="form-field">
                    <th valign="top" scope="row">
                        <label for="title-0"><? echo __("Title", HERISSON_TD); ?>:</label>
                    </th>
                    <td>
                        <input type="text" class="main" id="title-0" name="title" value="<? echo $existing->title ?>" />
                    </td>
                    <td rowspan="5" style="text-align: center; vertical-align: top">
                    <!--
                    <br/>
                        <b><a href="/wp-admin/admin.php?page=herisson_bookmark&action=view&id='.$existing->id.'&nomenu=1" target="_blank"><? echo __('View archive',HERISSON_TD) ?></a></b><br/><br/>
                    <br/>
                    -->
                    <? if ($id) { ?>
                    <b>
                        <a href="/wp-admin/admin.php?page=herisson_bookmark&action=download&id=<? echo $existing->id ?>">
                            <img src="<? echo HERISSON_PLUGIN_URL; ?>/images/ico-download.png" /><br/>
                            <? echo __('Download',HERISSON_TD); ?>
                        </a>
                    </b>
                    <br/><br/>
                    <b>
                        <a href="<? echo $existing->getDirUrl(); ?>" target="_blank">
                        <? echo __('View archive',HERISSON_TD); ?>
                        </a>
                    </b>
                    <br/><br/>
                        <? if (file_exists($existing->getImage()) && filesize($existing->getImage()))  { ?>
                            <b><? echo __('Capture',HERISSON_TD); ?></b><br/>
                            <a href="<? echo $existing->getImageUrl(); ?>">
                                <img alt="Capture" src="<? echo $existing->getThumbUrl(); ?>" style="border:0.5px solid black" />
                            </a>
                        <? } ?>
                     <? } ?>
                    </td>
                </tr>
            
                <!-- URL -->
                <tr class="form-field">
                    <th valign="top" scope="row">
                        <label for="url-0"><? echo __("URL", HERISSON_TD); ?>:</label>
                    </th>
                    <td>
                        <input type="text" size="80" class="main" id="url-0" name="url" value="<? echo $existing->url; ?>" <? if ($id) { ?> readonly="readonly"<? } ?> />
                        <br/><small><a href="<? echo $existing->url; ?>" style="text-decoration:none">Visit <? echo $existing->url; ?></a></small>
                    </td>
                </tr>
            
                <!-- Description -->
                <tr class="form-field">
                    <th valign="top" scope="row">
                    <label for="description-0"><? echo __("Description", HERISSON_TD); ?>:</label>
                    </th>
                    <td>
                    <textarea class="main" id="description-0" name="description"><? echo $existing->description; ?></textarea>
                    </td>
                </tr>
        
                <?/*
                <!-- Image URL -->
                <tr class="form-field">
                    <th valign="top" scope="row">
                        <label for="image-0"><? echo __("Book Image URL", HERISSON_TD); ?>:</label>
                    </th>
                    <td>
                        <input type="text" class="main" id="image-0" name="image" value="<? echo htmlentities($existing->image); ?>" />
                    </td>
                </tr>
                */ ?>
            
                <? if ($id) { ?>
                <!-- Favicon -->
                <tr class="form-field">
                    <th valign="top" scope="row">
                        <label for="url-0"><? echo __("Favicon", HERISSON_TD); ?>:</label>
                    </th>
                    <td>
                    <? if ($existing->favicon_image) { ?>
                          <img src="data:image/png;base64,<? echo $existing->favicon_image; ?>"/>
                    <? } ?>
                        <input type="text" size="80" class="main" id="url-0" name="url" value="<? echo $existing->favicon_url; ?>" readonly="readonly" />
                    </td>
                </tr>
                <? } ?>
        
                <? if ($id) { ?>
                <!-- Content -->
                <tr class="form-field">
                    <th valign="top" scope="row">
                        <label for="content-0"><? echo __("Content", HERISSON_TD); ?>:</label>
                    </th>
                    <td>
                        <? echo ($existing->content ? '<span class="herisson-success"><? echo __("Yes", HERISSON_TD); ?></span>' : '<span class="herisson-errors"><? echo __("No", HERISSON_TD); ?></span>'); ?>
                    </td>
                </tr>
                <? } ?>
        
                <? if ($id) { ?>
                <!-- Type -->
                <tr class="form-field">
                    <th valign="top" scope="row">
                        <label for="type-0"><? echo __("Type", HERISSON_TD); ?>:</label>
                    </th>
                    <td>
                        <? echo $existing->content_type; ?>
                    </td>
                </tr>
                <? } ?>
        
                <? if ($id) { ?>
                <!-- Archive size -->
                <tr class="form-field">
                    <th valign="top" scope="row">
                        <label for="size-0"><? echo __("Archive size", HERISSON_TD); ?>:</label>
                    </th>
                    <td>
                        <? echo format_size($existing->dirsize); ?>
                    </td>
                </tr>
                <? } ?>
        
                <!-- Visibility -->
                <tr class="form-field">
                    <th valign="top" scope="row">
                        <label for="visibility-0"><? echo __("Visibility", HERISSON_TD); ?>:</label>
                    </th>
                    <td>
                        <select name="is_public" id="visibility-0">
                            <option value="0"<? echo (!$existing->is_public ? ' selected="selected"' : ''); ?>><? echo __("Private", HERISSON_TD); ?></option>
                            <option value="1"<? echo ($existing->is_public ? ' selected="selected"' : ''); ?>><? echo __("Public", HERISSON_TD); ?></option>
                        </select>
                        <br/><small><? echo __("<code>Public Visibility</code> enables a bookmark to appear publicly within the herisson page.", HERISSON_TD); ?></small>
                        <br/><small><? echo __("<code>Private Visibility</code> restricts the visibility of a book to within the administrative interface.", HERISSON_TD); ?></small>
                    </td>
                </tr>
        
                <tr class="form-field">
                    <th valign="top" scope="row">
                        <label for="visibility-0"><? echo __("Tags", HERISSON_TD); ?>:</label>
                    </th>
                    <td>
                        <script src="/wp-content/plugins/herisson/js/herisson.dev.js" type="text/javascript"></script>
                        <script>
                         jQuery(document).ready(function($) {
                          $('#tagsdiv-post_tag, #categorydiv').children('h3, .handlediv').click(function(){
                           $(this).siblings('.inside').toggle();
                          });
                         });
                        </script>
                    </td>
                </tr>
            </tbody>
        </table>
        <div id="tagsdiv-post_tag" class="postbox">
            <div class="handlediv" title="<?php esc_attr_e( 'Click to toggle' ); ?>"><br /></div>
            <h3>
                <span><?php _e('Tags'); ?></span>
            </h3>
            <div class="inside">
                <div class="tagsdiv" id="post_tag">
                    <div class="jaxtag">
                        <label class="screen-reader-text" for="newtag"><?php _e('Tags'); ?></label>
                        <input type="hidden" name="tags" class="the-tags" id="tags" value="<? foreach ($tags as $tag) { echo $tag; echo ","; } ?>" />
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
            <input class="button" type="submit" value="<? echo __("Save", HERISSON_TD); ?>" />
        </p>

    </form>

</div>

