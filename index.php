<?php

	ob_start();	//	Output Buffering Satrt

	session_start();

	$pageTitle = "تسجيل الدخول";
	$noNavbar = "";
	$noCopyright = "";

	include "init.php";

	if (isset($_COOKIE["el_mansheya_admin_username"]) && isset($_COOKIE["el_mansheya_admin_hashedPass"])) {

		// Check If The User Exists In Database

		$count = checkSelect("username, password", "users", "username = '" . $_COOKIE["el_mansheya_admin_username"] . "' AND password = '" . $_COOKIE["el_mansheya_admin_hashedPass"] . "'");

		// If Count > 0 Means The Database Contain Record About This User

		if ($count > 0) {

			$_SESSION["AdminUsername"] = $_COOKIE["el_mansheya_admin_username"];	//	Register Session Username

			header("Location: dashboard.php");	//	Redirect To Home Page

			exit();

		}

	}

	// Check If User Comming From HTTP Post Request

	if ($_SERVER["REQUEST_METHOD"] === "POST") {

		$username = filter_var($_POST["username"], FILTER_SANITIZE_STRING);
		$password = filter_var($_POST["password"], FILTER_SANITIZE_STRING);
		$hashedPass = md5($password);

		//	Validate The Form

		$formErrors = array();

		if (empty($username)) {

			$formErrors['user'] = "يجب ادخال <strong>اسم المستخدم</strong>";

		}

		if (empty($password)) {

			$formErrors['pass'] = "يجب ادخال <strong>كلمة المرور</strong>";

		}

		// Check If There's No Error Proceed The Update Operation

		if (empty($formErrors)) {

			// Check If The User Exists In Database

			$count = checkSelect("username, password", "users", "username = '" . $username . "' AND password = '" . $hashedPass . "'");

			// If Count > 0 Means The Database Contain Record About This User

			if ($count > 0) {

				setcookie("el_mansheya_admin_username", $username, time() + 86400 * 30, "/");

				setcookie("el_mansheya_admin_hashedPass", $hashedPass, time() + 86400 * 30, "/");

				$_SESSION["AdminUsername"] = $_COOKIE["el_mansheya_admin_username"];	//	Register Session Username

				header("Location: dashboard.php");	//	Redirect To Contact Page
				
				exit();

			} else {

				$formErrors['incorrect'] = "اسم المستخدم او كلمة المرور <strong>غير صحيحة</strong>";

			}
			
		}

	}

?>

		<div class="index-login w-100 mx-auto">
			<div class="container-login">
				<div class="wrap-login">
					<div class="login-form-title">
						<span class="login-form-title-1">
							تسجيل الدخول
						</span>
					</div>
					<form class="login-form validate-form" action = "<?php echo $_SERVER['PHP_SELF']; ?>" method = "POST">
						<?php 

							if (!empty($formErrors) && ! isset($formErrors['incorrect'])) {

								foreach ($formErrors as $error) {

									echo "<div class = 'alert alert-danger'><i class='fas fa-exclamation-circle'></i> " . $error . "</div>";

								}
								
							}

						?>
						<div class="wrap-input validate-input mb-4" data-validate="يجب ادخال اسم المستخدم">
							<span class="label-input">اسم المستخدم</span>
							<input class="input" type="text" name="username" placeholder = "أدخل اسم المستخدم" autocomplete = "off" />
							<span class="focus-input"></span>
						</div>

						<div class="wrap-input validate-input mb-5" data-validate = "يجب ادخال كلمة المرور">
							<span class="label-input">كلمة المرور</span>
							<div class = "position-relative">
								<input class="input password" type="password" name="password" placeholder="أدخل كلمة المرور" autocomplete = "new-password" />
								<i class="show-pass fas fa-eye"></i>
							</div>
							<span class="focus-input"></span>
						</div>

						<?php

							// Loop Into Error Array And Echo It

							if (!empty($formErrors) && isset($formErrors['incorrect'])) {

								echo "<div class = 'alert alert-danger'><i class='fas fa-exclamation-circle'></i> " . $formErrors['incorrect'] . "</div>";

							}

						?>

						<div class="container-login-form-btn">
							<button class="btn login-form-btn">
								تسجيل الدخول
							</button>
						</div>
					</form>
				</div>
			</div>
		</div>

<?php

	include $templates . "footer.php";

	ob_end_flush();	//	Output Buffering End

?>