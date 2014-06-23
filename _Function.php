<?php
	require_once("Prototype.php");
	require_once("_Class.php");
	require_once("_Variable.php");
	
	class _Function extends Prototype {
		private $visibility;			// String.
		private $name;					// String.
		private $parameters = array();	// _Variable[].
		private $body;					// String.
		private $isStatic = false;		// bool.
		private $isAbstract = false;	// bool.
		
		public function __construct( $name, $visibility="public" ) {
			$this->name 	  = $name;
			$this->visibility = $visibility;
		}
		
		public function getName() {	// Returns String.
			return $this->name;
		}
		
		public function getVisibility() {	// Returns String.
			return $this->visibility;
		}
		
		public function getBody() {	// Returns String.
			return $this->body;
		}
		
		public function hasBody() { // Returns bool.
			return $this->body !== null;
		}
		
		public function setVisibility( $visibility ) { // Returns void.
			$this->visibility = $visibility;
		}
		
		public function setName( $name ) { // Returns void.
			$this->name = $name;
		}
		
		public function addParameter( $parameter ) { // Returns void.
			$this->parameters[] = $parameter;
		}
		
		public function setBody( $body ) { // Returns void.
			$this->body = $body;
		}
		
		public function setStatic( $static ) {
			$this->isStatic = $static;
		}
		
		public function setAbstract( $abstract ) {
			$this->isAbstract = $abstract;
		}
		
		public function build( $compact=true ) { // Returns String.
			$result = "";
			
			if ($this->visibility !== null) {
				$result .= $this->visibility . " ";
			}

			if ($this->isStatic) {
				$result .= "static" . " ";
			}
			
			if ($this->isAbstract) {
				$result .= "abstract" . " ";
			}

			$result .= "function " . $this->name . "(";
			$first = true;
			foreach ( $this->parameters as $param ) {
				if (!$param->hasDefaultValue()) {
					if ($first) {$first = false;} else {
						$result .= ",";
						if (!$compact) {
							$result .= " ";
						}
					}
					$result .= '$' . $param->getName();
				}
			}
			foreach ( $this->parameters as $param ) {
				if ($param->hasDefaultValue()) {
					if ($first) {$first = false;} else {
						$result .= ",";
						if (!$compact) {
							$result .= " ";
						}
					}
					$result .= '$' . $param->getName();
					
					if ($compact) {
						$result .= "=" . $param->getDefaultValue();
					} else {
						$result .= " = " . $param->getDefaultValue();
					}	
				}
			}
			$result .= ")";
			if ($this->hasBody()) {
				if ($this->body === "") {
					$result .= " { }";
				} else {
					if ($compact) {
						$result .= "{" . $this->body . "}";
					} else {
						$result .= " {\n" . $this->body;
						$result = str_replace("\n", "\n    ", $result);
						$result .= "\n}";
					}
				}
			} else {
				$result .= ";";
			}
			return $result;
		}
	}
?>