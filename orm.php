<?

if (!function_exists('herisson_load_doctrine')) {
function herisson_load_doctrine($file) {
#	 echo $file."<br>\n";
 if (!file_exists($file)) {
	 return new WP_Error('error', "Missing file : $file, make sure your processed the generation of Doctrine ORM");
	}
	require_once $file;
}

}

if (!file_exists(HERISSON_BASE_DIR."../doctrine/")) {
	return new WP_Error('error', 'Plugin Doctrine ORM is required to use Herisson : <a href="http://wordpress.org/support/plugin/doctrine">http://wordpress.org/support/plugin/doctrine</a>');
}

$_DoctrinePrefix = "WpHerisson";
$tables = array("Bookmarks", "Tags", "BookmarksTags", "Friends", "Types");


# Source : http://www.flynsarmy.com/2010/02/integrating-doctrine-into-wordpress/
require_once HERISSON_BASE_DIR."../doctrine/lib/Doctrine.php";
spl_autoload_register(array('Doctrine', 'autoload'));

require_once HERISSON_BASE_DIR."../../../wp-config.php";
	$dsn = 'mysql://' . DB_USER .
		':' . DB_PASSWORD .
		'@' . DB_HOST .
		'/' . DB_NAME;
		Doctrine_Manager::connection($dsn, 'default');

#Doctrine::loadModels( HERISSON_BASE_DIR."../doctrine/models/generated" );
#Doctrine::loadModels( HERISSON_BASE_DIR."../doctrine/models" );

#require_once HERISSON_BASE_DIR."../doctrine/doctrine.php";
#require_once HERISSON_BASE_DIR."../doctrine/lib/Doctrine.php";
#require_once HERISSON_BASE_DIR."../doctrine/lib/Doctrine/Query.php";
#herisson_load_doctrine(HERISSON_BASE_DIR."../doctrine/lib/Doctrine.php");

foreach ($tables as $table) {

# echo "Load : $table <br>\n";
 herisson_load_doctrine(HERISSON_BASE_DIR."../doctrine/models/generated/Base${_DoctrinePrefix}$table.php");
 herisson_load_doctrine(HERISSON_BASE_DIR."../doctrine/models/${_DoctrinePrefix}${table}Table.php");
 herisson_load_doctrine(HERISSON_BASE_DIR."../doctrine/models/${_DoctrinePrefix}${table}.php");

}
	// this will allow us to use "mutators"
	Doctrine_Manager::getInstance()->setAttribute(
		Doctrine::ATTR_AUTO_ACCESSOR_OVERRIDE, true);
 
	// this sets all table columns to notnull and unsigned (for ints) by default
	Doctrine_Manager::getInstance()->setAttribute(
		Doctrine::ATTR_DEFAULT_COLUMN_OPTIONS,
		array('notnull' => true, 'unsigned' => true));
 
	// set the default primary key to be named 'id', integer, 4 bytes
	Doctrine_Manager::getInstance()->setAttribute(
		Doctrine::ATTR_DEFAULT_IDENTIFIER_OPTIONS,
		array('name' => 'id', 'type' => 'integer', 'length' => 4));
