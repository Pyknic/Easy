<?php
	require_once("Prototype.php");
	require_once("_Class.php");
	
	class _Variable extends Prototype {
		private $visibility;			// String
		private $name;					// String
		private $type;	  				// _Type
		private $defaultValue;			// String
		private $isStatic = false;		// bool
		private $isAbstract = false;	// bool
		
		public function __construct( $name, $defaultValue=null ) {
			$this->name = $name;
			$this->defaultValue = $defaultValue;
		}
		
		public function getName() { // Returns string.
			return $this->name;
		}
		
		public function hasDefaultValue() { // Returns bool.
			return $this->defaultValue !== null;
		}
		
		public function getDefaultValue() { // Returns String.
			return $this->defaultValue;
		}
		
		public function setName( $name ) { // Returns void.
			$this->name = $name;
		}
		
		public function setVisibility( $visibility ) { // Returns void.
			$this->visibility = $visibility;
		}
		
		public function setType( $type ) { // Returns void.
			$this->type = $type;
		}
		
		public function setDefaultValue( $value ) { // Returns void.
			$this->defaultValue = $value;
		}
		
		public function setStatic( $static ) {
			$this->isStatic = $static;
		}
		
		public function setAbstract( $abstract ) {
			$this->isAbstract = $abstract;
		}
		
		public function build( $compact=true ) { // Returns string.
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
			
			$result .= '$' . $this->name;
			
			if ($this->defaultValue !== null) {
				if ($compact) {
					$result .= "=" . $this->defaultValue;
				} else {
					$result .= " = " . $this->defaultValue;
				}
			}
			
			$result .= ";";

			return $result;
		}
	}
?>