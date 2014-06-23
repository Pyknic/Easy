<?php
	require_once( 'Column.php' );

	class Table {
		// Table information.
		private $tableName; 					// String
		private $db_prefix; 					// String
		
		// Cached queries.
		private $columns; 						// [] => Column
		private $indexes; 						// [] => String
		private $uniques; 						// [] => String
		private $primarys; 						// [] => String
		private $foreigns; 						// [COLUMN_NAME][var|table|column] => String
		
		public function __construct( $tableName, $db_prefix = "" ) {
			global $db, $db_name;
		
			// Store table information.
			$this->tableName = $tableName;
			$this->db_prefix = $db_prefix;
			
			$this->columns = array();
			$this->indexes = array();
			$this->uniques = array();
			$this->primarys = array();
			$this->foreigns = array();

			// Execute index query.
			$indexQuery = $db->query( "SHOW INDEX FROM $tableName" ) or die ( $db->error );
			
			while ( $index = $indexQuery->fetch_assoc() ) {
				if ($index['Key_name'] === "PRIMARY") {
					$this->primarys[] = $index['Column_name'];
				} else if ($index['Non_unique'] === "0") {
					$this->uniques[] = $index['Column_name'];
				} else {
					$this->indexes[] = $index['Column_name'];
				}
			}

			// Execute Foreign-key-query.
			$foreignQuery = $db->query( 
				"SELECT TABLE_SCHEMA,TABLE_NAME,COLUMN_NAME,CONSTRAINT_NAME,
				REFERENCED_TABLE_NAME,REFERENCED_COLUMN_NAME FROM information_schema.KEY_COLUMN_USAGE WHERE
				TABLE_SCHEMA = '$db_name' AND TABLE_NAME = '$tableName' AND REFERENCED_TABLE_NAME <> 'NULL';" 
			) or die( $db->error );

			while ( $f = $foreignQuery->fetch_assoc() ) {
				$foreigns[$f['COLUMN_NAME']] = array(
					'var'    => $f['COLUMN_NAME'], 
					'table'  => $f['REFERENCED_TABLE_NAME'], 
					'column' => $f['REFERENCED_COLUMN_NAME']
				);
			}
			
			// Execute Column query.
			$columnQuery = $db->query( 
				"SELECT COLUMN_NAME AS name, " .
				"		DATA_TYPE AS type, " .
				"		IS_NULLABLE AS optional, " .
				"		EXTRA AS increment, " .
				"		COLUMN_COMMENT AS comment " .
				"FROM information_schema.COLUMNS WHERE " .
				"	TABLE_SCHEMA = '$db_name' AND " .
				"	TABLE_NAME = '$tableName'" 
			) or die ( $db->error );

			while ( $c = $columnQuery->fetch_assoc() ) {
				$this->columns[] = new Column(
					$c['name'],
					$c['type'],
					$c['optional'] === "YES",
					$c['increment'] === "auto_increment",
					$c['comment']
				);
			}
		}
		
		public function getTableName() { return $this->tableName; }		// Returns String.
		public function getDBPrefix() { return $this->db_prefix; }		// Returns String.
		
		public function getColumns() { return $this->columns; }			// Returns [] => Column.
		public function getIndexes() { return $this->indexes; }			// Returns [] => String.
		public function getUniques() { return $this->uniques; }			// Returns [] => String.
		public function getPrimarys() { return $this->primarys; }		// Returns [] => String.
		public function getForeigns() { return $this->foreigns; }		// Returns [COLUMN_NAME][var|table|column] => String.
		
		public function getClassName() { // Returns String.
			// Take table name.
			$class = $this->tableName;
			
			// Remove prefix.
			if ( $this->db_prefix !== ""
			&& strpos( $class, $this->db_prefix ) === 0 ) {
				$class = substr( $class, strlen( $this->db_prefix ));
			}
			
			// Replace _ with capital letter.
			$words = explode( "_", $class );
			$class = "";
			
			foreach ( $words as $word ) {
				$class .= ucfirst( $word );
			}
			
			// Return the final name.
			return $class;
		}
		
		public function getPrimaryName() { // Returns String.
			$name = "";
			
			$f = true;
			foreach ( $this->primarys as $primary ) {
				if ( $f ) {
					$f = false;
				} else {
					$name .= "And";
				}
				
				$name .= ucfirst(Column::variableName( $primary ));
			}
			
			return $name;
		}
	}
?>