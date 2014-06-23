<?php
	class Column {
		private $name;		// String.
		private $type;		// String.
		private $optional;	// bool.
		private $increment;	// bool.
		private $comment;	// String.
		
		public function __construct( $name, $type, $optional, $increment, $comment ) {
			$this->name = $name;
			$this->type = $type;
			$this->optional = $optional;
			$this->increment = $increment;
			$this->comment = $comment;
		}
		
		public function setName( $name ) 			{ $this->name = $name; } 			// Returns void.
		public function setType( $type ) 			{ $this->type = $type; } 			// Returns void.
		public function setOptional( $optional ) 	{ $this->optional = $optional; } 	// Returns void.
		public function setIncrement( $increment ) 	{ $this->increment = $increment; } 	// Returns void.
		public function setComment( $comment ) 		{ $this->comment = $comment; } 		// Returns void.
		
		public function getName() 		{ return $this->name; } 		// Returns String.
		public function getType() 		{ return $this->type; } 		// Returns String.
		public function isOptional() 	{ return $this->optional; } 	// Returns bool.
		public function isIncrement() 	{ return $this->increment; } 	// Returns bool.
		public function getComment() 	{ return $this->comment; } 		// Returns String.
		
		public static function variableName( $column ) { // Returns String.
			$var = $column;
			
			// Replace _ with capital letter.
			$words = explode( "_", $var );
			$var = "";
			
			foreach ( $words as $word ) {
				$var .= ucfirst( $word );
			}
			
			// Lowercase first letter.
			$var = lcfirst( $var );
			
			// Return the final name.
			return $var;
		}
		
		public function getVariableName() { // Returns String.
			return Column::variableName( $this->name );
		}
	}
	
?>