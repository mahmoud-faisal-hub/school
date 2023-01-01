<?php

	function lang($phrase) {

		static $lang = array (

			"Home"		=>		"Home"

		);

		return $lang[$phrase];

	}