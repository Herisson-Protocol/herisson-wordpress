<?

if (!function_exists('herisson_load_doctrine')) {
function herisson_load_doctrine($file) {
 if (!file_exists($file)) {
	 return new WP_Error('error', "Missing file : $file, make sure your processed the generation of Doctrine ORM");
	}
	require_once $file;
}

}

if (!file_exists(HERISSON_BASE_DIR."../doctrine/")) {
	return new WP_Error('error', 'Plugin Doctrine ORM is required to use Herisson : <a href="http://wordpress.org/support/plugin/doctrine">http://wordpress.org/support/plugin/doctrine</a>');
}

$plugin = "WpHerisson";
$tables = array("Bookmarks", "Tags", "BookmarksTags", "Friends", "Types");

herisson_load_doctrine(HERISSON_BASE_DIR."../doctrine/lib/Doctrine.php");

foreach ($tables as $table) {

# herisson_load_doctrine(HERISSON_BASE_DIR."../doctrine/models/${plugin}${table}Table.php");
# herisson_load_doctrine(HERISSON_BASE_DIR."../doctrine/models/${plugin}${table}.php");
# herisson_load_doctrine(HERISSON_BASE_DIR."../doctrine/models/generated/Base${plugin}$table.php");

}

