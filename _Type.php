<?php
	require_once("Prototype.php");

	abstract class _Type extends Prototype {
		public abstract function setName( $name );	// Returns void.
		public abstract function getName( );		// Returns String.
	}
?>