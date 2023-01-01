<?php

	$pageTitle = "Loging Out...";

	if (isset($_COOKIE['el_mansheya_admin_username']) && isset($_COOKIE['el_mansheya_admin_hashedPass'])) {

	    unset($_COOKIE['el_mansheya_admin_username']);

	    unset($_COOKIE['el_mansheya_admin_hashedPass']);

	    setcookie('el_mansheya_admin_username', null, time() - 1, '/');

	    setcookie('el_mansheya_admin_hashedPass', null, time() - 1, '/');

	}

	session_start();

	session_unset();

	session_destroy();

	header("Location: index.php");

	exit();