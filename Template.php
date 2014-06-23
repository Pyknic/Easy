<?php
	function get_template( $name, $params=array(), $file=null, $row=null ) { // Returns String.
		$error = "Template file not found.";
		
		// Append optional information.
		if ( $file !== null ) {
			if ( $row !== null ) {
				$error .= " (line " . $row . " in " . $file . ").";
			} else {
				$error .= " (in " . $file . ").";
			}
		}
		
		// Load contents from file.
		$content = file_get_contents( "templates/$name" ) 
		or die ( $error );
		
		// Make sure content has a length.
		if (!strlen( $content )) {
			die ( $error );
		}
		
		// Replace parameters.
		foreach ( $params as $i => $param ) {
			$content = str_replace( "%$i", $param, $content );
		}
		
		return $content;
	}
?>