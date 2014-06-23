<?php
	require_once("Prototype.php");
	
	class _Package extends Prototype {
		private $name;		// String
		private $files; 	// _File[]
		
		public function __construct( $name, $files=array() ) {
			$this->name  = $name;
			$this->files = $files;
		}
		
		public function setName( $name ) 	{ $this->name = $name; } 	// Returns void.
		public function getName() 			{ return $this->name; } 	// Returns String.

		public function append( $file ) { 	// Returns this.
			$this->files[] = $file;
			return $this;
		}
		
		public function getFiles() {		// Returns _File[].
			return $this->files;
		}
		
		public function build( $compact=true ) { // Returns String.
			$zip = new ZipArchive();
			$zip_name = "temp/" . time() . ".zip"; // Zip name
			$zip->open( $zip_name, ZipArchive::CREATE );
			
			foreach ($this->files as $file) {
				$zip->addFromString(
					$file->getPath() . $file->getFilename(), 
					$file->build( $compact )
				);
			}
			
			$zip->close();
			
			return $zip_name;
		}
	}
?>