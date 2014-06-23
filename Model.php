<?php
	require_once("Prototype.php");
	require_once("Table.php");
	require_once("Column.php");
	require_once("_Class.php");
	
	class Model extends Prototype {
		private $table;
		
		public function __construct( $table ) {
			$this->table = $table;
		}

		public function build( $compact=true ) { // Returns String.
			global $nl, $tab, $s;

			$class = new _Class( $this->table->getClassName() );
			
			// Go through each column and model it.
			foreach ( $this->table->getColumns() as $column ) {
				// Get the variable name with first letter lowercased.
				$name = $column->getVariableName();
			
				// Add a member variable.
				$var = new _Variable( $name );
				$var->setVisibility( 'private' );
				$var->setType( $column->getType() );
				$class->addVariable( $var );
				
				// Add setter.
				$set = new _Function( 'set' . ucfirst( $name ));
				$set->addParameter( new _Variable( $name ) );
				$set->setBody( "\$this->$name" . "$s=$s" . "\$$name;" );
				$class->addFunction( $set );
				
				// Add getter.
				$get = new _Function( 'get' . ucfirst( $name ));
				$get->setBody( "return \$this->$name;" );
				$class->addFunction( $get );
			}
			
			// Create functions.
			$insert = new _Function( 'insert' );
			$update = new _Function( 'update' );
			$delete = new _Function( 'delete' );
			$toJSON = new _Function( 'toJSON' );
			
			// Generate SQL-parts.
			$columns = "";
			$values = "";
			$updates = "";
			$json = "";
			$id_var = "";
			$f = true;
			$json_first = true;
			foreach ( $this->table->getColumns() as $column ) {
				// Generate JSON-code.
				if ( $json_first ) {
					$json_first = false;
				} else {
					$json .= ",\n	";
				}
				
				$json .= "'" . $column->getVariableName() . "' => \$this->" . $column->getVariableName();
			
				// Only add non-incrementing columns.
				if ( !$column->isIncrement() ) {
					if ( $f ) {
						$f = false;
					} else {
						$columns .= ", ";
						$values .= ", \" .\n	\"";
						$updates .= ", \" .\n	\"";
					}
					
					$columns .= $column->getName();
					$values .= "'\" . \$this->" . $column->getVariableName() . " . \"'";
					$updates .= $column->getName() . " = '\" . \$this->" . $column->getVariableName() . " . \"'";
				} else {
					$id_var = $column->getVariableName();
				}
			}
			
			// Find the primary value.
			$identification = "";
			$f = true;
			foreach ( $this->table->getPrimarys() as $primary ) {
				if ( $f ) {
					$f = false;
				} else {
					$identification .= " AND \" .\n	\"";
				}
				$identification .= "$primary = '\" . \$this->$primary . \"'";
			}
			
			// Generate insert-body from template.
			$id_row = $id_var === "" ? "" : "\n\n\$this->$id_var = \$db->insert_id;";
			$insert->setBody(
				get_template( 'Model.insert', array(
					$this->table->getTableName(),
					$columns,
					$values,
					$id_row
				), __FILE__, __LINE__)
			);
			$class->addFunction( $insert );
			
			if ( $identification !== "" ) {
				// Generate update-body from template.
				$update->setBody(
					get_template( 'Model.update', array(
						$this->table->getTableName(),
						$updates,
						$identification
					), __FILE__, __LINE__)
				);
				$class->addFunction( $update );
				
				// Generate delete-body from template.
				$delete->setBody(
					get_template( 'Model.delete', array(
						$this->table->getTableName(),
						$identification
					), __FILE__, __LINE__)
				);
				$class->addFunction( $delete );
			}
			
			// Generate toJSON()-body from template.
			$toJSON->setBody(
				get_template( 'Model.toJSON', array(
					$json
				), __FILE__, __LINE__)
			);
			$class->addFunction( $toJSON );
			
			// Build the class and return it.
			return $class->build( $compact );
		}
	}
?>