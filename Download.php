<?php
	// Credentials
	$db_host = $_POST['db_host'];
	$db_name = $_POST['db_name'];
	$db_user = $_POST['db_user'];
	$db_pass = $_POST['db_pass'];
	$db_prefix = $_POST['db_prefix'];

	// Connect to MySQL
	global $db, $compress, $nl, $tab, $s;
	$db = new mysqli($db_host, $db_user, $db_pass, $db_name);
	
	// Check for errors.
	if ( $db->connect_error ) {
		die ('Connect Error (' . $db->connect_errno . ') ' . $db->connect_error);
	}

	// Escape strings
	$db_host = $db->real_escape_string( $db_host );
	$db_name = $db->real_escape_string( $db_name );
	$db_user = $db->real_escape_string( $db_user );
	$db_pass = $db->real_escape_string( $db_pass );
	$db_prefix = $db->real_escape_string( $db_prefix );
	
	// Requirements.
	require_once( '_Package.php' );
	require_once( '_File.php' );
	require_once( '_Class.php' );
	require_once( '_Function.php' );
	require_once( '_Interface.php' );
	require_once( '_Variable.php' );
	require_once( 'Template.php' );
	require_once( 'Table.php' );
	require_once( 'Model.php' );
	require_once( 'Manager.php' );
	
	
	// Retreive table names.
	$tables = $db->query( "SHOW TABLES" ) or die( $db->error );
	
	$compress = false;
	$nl = "\n";
	$tab = "    ";
	$s = " ";
	if (isset($_POST['compress'])) {
		$compress = true;
		$nl = "";
		$tab = "";
		$s = "";
	}
	
	// Create the package.
	$package = new _Package( $db_name . ".zip" );

	// Generate all the table-classes as files.
	while ( $row = $tables->fetch_row() ) {
		// Create a Table-object that will download all information.
		$tableName = $db->real_escape_string( $row[0] );
		$table = new Table( $tableName, $db_prefix );
		
		$file = new _File( $table->getClassName() . '.php', 'models/' );

		// Generate Manager and Model classes.
		$manager = new Manager( $table );
		$model = new Model( $table );
		
		$file->append( $manager->build( $compress ) . $nl . $nl );
		$file->append( $model->build( $compress ));
		
		// Add to package.
		$package->append( $file );
	}
	
	// Create the main file.
	$main = new _File( $db_name . ".php" );
	$main->append( "require_once('db.php');$nl" );
	
	foreach ( $package->getFiles() as $file ) {
		$main->append( "require_once('" . $file->getPath() . $file->getFilename() . "');$nl" );
	}

	$package->append( $main );
	
	// Create the 'db.php'-file used to connect to the database.
	$db_file = new _File( 'db.php' );
	$db_file->append(
		get_template( 'db', array(
			$db_host, 
			$db_name, 
			$db_user, 
			$db_pass
		), __FILE__, __LINE__ )
	);
	$package->append( $db_file );

	// Ignore any aborts after this point.
	ignore_user_abort( true );
	$zip = $package->build( $compress );
	
	// Send the file.
	header( 'Content-Type: application/zip' );
	header( 'Content-disposition: attachment; filename=' . $package->getName() );
	header( 'Content-Length: ' . filesize($zip) );
	readfile( $zip );
	
	// Remove it.
	unlink( $zip );
?>