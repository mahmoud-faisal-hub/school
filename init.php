<?php

	// Error Reporting

	ini_set('display_errors', 'On');
	error_reporting(E_ALL);

	include "connect.php";

	$sessionUser = "";

	if (isset($_SESSION['user'])) {

		$sessionUser = $_SESSION['user'];

	}

	// Routes

	$templates = "includes/templates/";		//	Templates 	Directory
	$functions = "includes/functions/";		//	Functions 	Directory
	$languages = "includes/languages/";		//	Languages 	Directory
	$library = "includes/library/";			//	Library 	Directory
	$css = "layout/css/";					//	css 	  	Directory
	$js = "layout/js/";						//	js 	  	  	Directory
	$images = "layout/images/";				//	images 	  	Directory

	// Include The Important Files

	include $functions . "functions.php";
	include $languages . "arabic.php";
	include $templates . "header.php";

	if (!isset($noNavbar)) { include $templates . "navbar.php"; }