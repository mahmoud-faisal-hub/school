<?php

	function lang($phrase) {

		static $lang = array (

			"Home"		=>		"الصفحة الرئيسية",
			"add"		=>		"إضافة",
			"update"	=>		"تعديل",
			"delete"	=>		"حذف"

		);

		return $lang[$phrase];

	}