<?php
	require_once("_Type.php");
	require_once("_Interface.php");
	require_once("_Function.php");
	require_once("_Variable.php");
	
	class _Class extends _Type {
		private $name;					// String
		private $parentClass;			// _Class
		private $interfaces = array();	// _Interface[]
		private $functions  = array();	// _Function[]
		private $variables  = array();	// _Variable[]
		
		public function __construct( $name ) {
			$this->name = $name;
		}
		
		public function getName() { // Returns string.
			return $this->name;
		}
		
		public function getParent() { // Returns _Class.
			return $this->parentClass;
		}
		
		public function setName( $name ) { // Returns void.
			$this->name = $name;
		}
		
		public function setParent( $parent ) { // Returns void.
			$this->parentClass = $parent;
		}
		
		public function addInterface( $interface ) { // Returns void.
			$this->interfaces[] = $interface;
		}
		
		public function addFunction( $function ) { // Returns void.
			$this->functions[ $function->getName() ] = $function;
		}

		public function addVariable( $variable ) { // Returns void.
			$this->variables[ $variable->getName() ] = $variable;
		}
		
		public function hasFunction( $functionName ) { // Returns bool.
			return isset( $this->functions[ $functionName ] );
		}
		
		public function hasVariable( $variableName ) { // Returns bool.
			return isset( $this->variables[ $variableName ] );
		}
		
		public static function singleton( $name ) { 
			$class = new _Class( $name );
			
			// Generate the instance holder.
			$inst = new _Variable( 'inst' );
			$inst->setVisibility( 'private' );
			$inst->setDefaultValue( 'null' );
			$inst->setStatic( true );
			$class->addVariable( $inst );
			
			// Generate the constructor.
			$constructor = new _Function( '__construct' );
			$constructor->setVisibility( 'private' );
			$constructor->setBody( '' );
			$class->addFunction( $constructor );
			
			// Generate the handle function.
			$handle = new _Function( 'inst' );
			$handle->setStatic( true );
			$handle->setBody( 
				"if ($name::\$inst === null) {\n" .
				"	$name::\$inst = new $name();\n" .
				"}\n\n" .
				"return $name::\$inst;"
			);
			$class->addFunction( $handle );
			
			return $class;
		}
	
		public function build( $compact=true ) { // Returns string.
			$part = "";
		
			if (!$compact) {
				$part .= "/**\n * " . $this->name . "\n */\n";
			}
		
			$result = "class " . $this->name;
			if ($this->parentClass !== null) {
				$result .= " extends " . $this->parentClass->getName();
			}
			
			if (count($this->interfaces) > 0) {
				$result .= " implements ";
				$first = true;
				foreach ($this->interfaces as $interface) {
					if ($first) {$first = false;} else {
						$result .= ",";
						if (!$compact) {
							$result .= " ";
						}
					}
					$result .= $interface->getName();
				}
			}
			
			if (!$compact) { $result .= " "; }
			
			$result .= "{";
			if (!$compact) {
				$result .= "\n";
			}
			
			foreach ($this->variables as $variable) {
				$result .= $variable->build( $compact );
				if (!$compact) { $result .= "\n"; }
			}
			
			if (!$compact) { $result .= "\n"; }
			
			$first = true;
			foreach ($this->functions as $function) {
				if ( $first ) { $first = false; } else if (!$compact) { $result .= "\n"; }
				$result .= $function->build( $compact );
				if (!$compact) { $result .= "\n"; }
			}
			
			if (!$compact) {
				$result = $part . str_replace("\n", "\n    ", $result) . "\n";
			}
			
			$result .= "}";
			return $result;
		}
	}
?>