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

 $backups = Doctrine_Query::create()
  ->from('WpHerissonBackups')
  ->execute();

    echo '
	<div class="wrap">

		<h2>' . __("Backups", HERISSON_TD) . '</h2>

		<table class="form-table" width="100%" cellspacing="2" cellpadding="5">
';
 foreach ($backups as $backup) {

  echo '
			<tr valign="top">
				<th scope="row">' . __('Import bookmarks', HERISSON_TD) . ':</th>
				<td style="width: 400px">
				 '.__('Source',HERISSON_TD).' : 
				 <select name="import_source">
					 <option value="firefox">'.__('Firefox',HERISSON_TD).'</option>
					</select><br/>
					'.__('File',HERISSON_TD).' : <input type="file" name="import_file" />
				</td>
				<td>
  			<input type="submit" value="' . __("Import bookmarks", HERISSON_TD) . '" />
				</td>
			</tr>
			';

  } ?>
		</table>



	</div>
	
<?

}


