<?php
	require_once("Prototype.php");
	
	class _File extends Prototype {
		private $filename;
		private $path;
		private $content;
		
		public function __construct( $filename, $path="", $content="" ) {
			$this->filename = $filename;
			$this->path		= $path;
			$this->content 	= $content;
		}
		
		public function setFilename( $filename ) { $this->filename = $filename; } // Returns void.
		public function setPath( $path ) 	 	 { $this->path = $path; } 	  	  // Returns void.
		public function setContent( $content )   { $this->content = $content; }   // Returns void.
		
		public function getFilename() { return $this->filename; } // Returns String.
		public function getPath() 	  { return $this->path; } 	  // Returns String.
		public function getContent()  { return $this->content; }  // Returns String.
		
		public function append( $str ) { // Returns this.
			$this->content .= $str;
			return $this;
		}
		
		public function build( $compact=true ) { // Returns String.
			if ($compact) {
				return "<?php " . $this->content . " ?>";
			} else {
				return "<?php" . str_replace("\n", "\n    ", "\n" . $this->content) . "\n?>";
			}
		}
	}