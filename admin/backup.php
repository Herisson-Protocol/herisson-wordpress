<?php
/**
 * The admin interface for managing and editing backups.
 * @package herisson
 */

function herisson_backup_actions() {

 $action = param('action');
 switch ($action) {
	 case 'backup': herisson_backup_backup();
		break;
  default: herisson_backup_list();
	}

}

function herisson_backup_list() {

    echo '
	<div class="wrap">

		<h2>' . __("Backup", HERISSONTD) . '</h2>

		<table class="form-table" width="100%" cellspacing="2" cellpadding="5">

  <tr><td colspan="3">
   <h3>'.__('Import',HERISSONTD).'</h3>
		</td></tr>
		<form method="post" action="' . get_option('siteurl') . '/wp-admin/admin.php?page=herisson_backup">
		 <input type="hidden" name="action" value="import" />
			<tr valign="top">
				<th scope="row">' . __('Import bookmarks', HERISSONTD) . ':</th>
				<td style="width: 400px">
				 '.__('Source',HERISSONTD).' : 
				 <select name="import_source">
					 <option value="firefox">'.__('Firefox',HERISSONTD).'</option>
					</select><br/>
					'.__('File',HERISSONTD).' : <input type="file" name="import_file" />
				</td>
				<td>
  			<input type="submit" value="' . __("Import bookmarks", HERISSONTD) . '" />
				</td>
			</tr>
		</form>

		<form method="post" action="' . get_option('siteurl') . '/wp-admin/admin.php?page=herisson_backup">
		 <input type="hidden" name="action" value="import_delicious" />
			<tr valign="top">
				<th scope="row">' . __('Import Delicious bookmarks', HERISSONTD) . ':</th>
				<td style="width: 200px">
					'.__('Login',HERISSONTD).' :<input type="text" name="username_delicious" /><br/>
					'.__('Password',HERISSONTD).' :<input type="password" name="password_delicious" /><br/>
					'.__("Theses informations are not stored by this plugin.",HERISSONTD).'
				</td>
				<td>
  			<input type="submit" value="' . __("Import bookmarks", HERISSONTD) . '" />
				</td>
			</tr>
		</form>

  <tr><td colspan="3">
   <h3>'.__('Export',HERISSONTD).'</h3>
		</td></tr>

  <tr><td colspan="3">
   <h3>'.__('Maintenance',HERISSONTD).'</h3>
		</td></tr>
		<form method="post" action="' . get_option('siteurl') . '/wp-admin/admin.php?page=herisson_backup">
		 <input type="hidden" name="action" value="maintenance" />
			<tr valign="top">
				<th scope="row">' . __('Check Maintenance', HERISSONTD) . ':</th>
				<td>
					
				</td>
				<td>
  			<input type="submit" value="' . __("Start maintenance checks", HERISSONTD) . '" />
				</td>
			</tr>
		</form>
		</table>



	</div>
	';

}


