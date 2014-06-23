<?php
	require_once("_Type.php");
	require_once("_Function.php");
	
	class _Interface extends _Type {
		private $name;				  // String
		private $parentInterface;	  // _Interface
		private $functions = array(); // _Function[]
		
		public function __construct( $name ) {
			$this->name = $name;
		}
		
		public function getName() { // Returns string.
			return $this->name;
		}
		
		public function getParent() { // Returns _Interface.
			return $this->parentInterface;
		}
		
		public function setName( $name ) { // Returns void.
			$this->name = $name;
		}
		
		public function setParent( $parent ) { // Returns void.
			$this->parentInterface = $parent;
		}
		
		public function addFunction( $function ) { // Returns void.
			$this->functions[] = $function;
		}
		
		public function build( $compact=true ) { // Returns string.
			$result = "interface " . $this->name;
			if ($this->parentInterface !== null) {
				$result .= " extends " . $this->parentInterface->getName();
			}
			
			if (!$compact) { $result .= " "; }
			
			$result .= "{";
			if (!$compact) {
				$result .= "\n";
			}
			
			foreach ($this->functions as $function) {
				if (!$function->hasBody()) {
					$result .= $function->build();
					if (!$compact) { $result .= "\n"; }
				}
			}
			
			if (!$compact) {
				$result = str_replace("\n", "\n    ", $result);
			}
			
			$result .= "}";
			return $result;
		}
	}
?>