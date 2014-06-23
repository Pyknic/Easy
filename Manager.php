<?php
	require_once("Prototype.php");
	require_once("Table.php");
	require_once("Column.php");
	require_once("_Class.php");
	require_once("_Variable.php");

	class Manager extends Prototype {
		private $table;
		
		public function __construct( $table ) {
			$this->table = $table;
		}

		public function build( $compact=true ) { // Returns String.
			global $nl, $tab, $s;
		
			$class = _Class::singleton( $this->table->getClassName() . "Mgr" );
			
			// Create local cache.
			$cacheName = 'cache';
			$cache = new _Variable( $cacheName );
			$cache->setVisibility( 'private' );
			$cache->setDefaultValue( 'array()' );
			$class->addVariable( $cache );
			
			// Prepare parameters for select-template.
			$columns = "";
			$sets = "";
			$f = true;
			foreach ( $this->table->getColumns() as $column ) {
				if ( $f ) {
					$f = false;
				} else {
					$columns .= ", ";
					$sets .= ";\n";
				}
				
				$columns .= $column->getName();
				$sets .= "\$model->set" . ucfirst($column->getVariableName()) . "(\$row['" . $column->getName() . "']);";
			}
			
			// Generate static help-function.
			$help = new _Function( 'fromRow' );
			$help->addParameter( new _Variable( 'row' ));
			$help->setStatic( true );
			$help->setBody(
				get_template( 'Manager.fromRow', array(
					$this->table->getClassName(),
					$sets
				), __FILE__, __LINE__)
			);
			$class->addFunction( $help );

			// Generate select function from template.
			$select = new _Function( 'select' );
			$select->addParameter( new _Variable( 'selector' ));
			$select->addParameter( new _Variable( 'value' ));
			$select->addParameter( new _Variable( 'order', 'null' ));
			$select->setBody(
				get_template( 'Manager.select', array(
					$columns,
					$this->table->getTableName(),
					$this->table->getClassName()
				), __FILE__, __LINE__)
			);
			$class->addFunction( $select );

			// Generate select-functions for all primarys and uniques.
			// (These will return a single model.)
			foreach ( array_merge(
				$this->table->getPrimarys(),
				$this->table->getUniques()
			) as $index ) {
				$this->generateSelectFunction( $index, $class, true, $columns, $sets );
			}
			
			// Generate select-functions for other indexes.
			// (These will return an array of models.)
			foreach ( $this->table->getIndexes() as $index ) {
				$this->generateSelectFunction( $index, $class, false, $columns, $sets );
			}
			
			// Build the class and return it.
			return $class->build( $compact );
		}
		
		private function generateSelectFunction( $index, $class, $unique, $columns, $sets ) {
			// Name the function and local variable.
			$variableName = Column::variableName( $index );
			$cacheName 	  = 'cacheBy' . ucfirst( $variableName );
			$functionName = 'selectBy' . ucfirst( $variableName );
			
			// Generate the cache-variable.
			$cache = new _Variable( $cacheName );
			$cache->setVisibility( 'private' );
			$cache->setDefaultValue( 'array()' );
			$class->addVariable( $cache );
			
			// Generate the select-function.
			$select = new _Function( $functionName );
			$select->addParameter( new _Variable( $variableName ));
			$select->setBody(
				get_template( 'Manager.selectBy' . ($unique ? 'Unique' : 'Index'), array(
					$cacheName,						// %0
					$variableName,					// %1
					$columns,						// %2
					$this->table->getTableName(),	// %3
					$index,							// %4
					$this->table->getClassName()	// %5
				), __FILE__, __LINE__)
			);
			$class->addFunction( $select );
		}
	}
?>