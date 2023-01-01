<?php

	ob_start();	//	Output Buffering Start

	session_start();

	if (isset($_SESSION["AdminUsername"])) {

		$do = isset($_GET['do']) ? $_GET['do'] : "Manage";

		if ($do != "UserSearch" && $do != "Insert" && $do != "Update") {

			$pageTitle = "بيانات الأعضاء";

			$pageActive = basename(__FILE__);

		} else {

			$noNavbar = "";

		}

		include "init.php";

		$userPowers = getSelect("group_id", "users", "username ='" . $_SESSION["AdminUsername"] . "'", 1, "ASC", false);

		$powers = $userPowers["group_id"];
		
		if ($powers == 1) {

			// Start Manage Page

			if ($do === "Manage") {	// Manage Members Page 

				if (!isset($_GET['currentPage'])) {

					$currentPage = 1;

				} else {

					$currentPage = $_GET['currentPage'];

				}

				$countUsers = checkSelect("*", "users");

				$currentPage = filter_var($currentPage, FILTER_SANITIZE_NUMBER_INT);

				$maxItems = 10;

				if (! filter_var($currentPage, FILTER_VALIDATE_INT) || $currentPage > ceil($countUsers / $maxItems)) {

					$currentPage = 1;

				}

				$paginationData = setPagination($countUsers, $maxItems, $currentPage);

				$users = getSelect("*", "users", 1, 1, "ASC LIMIT " . $paginationData['srow'] . " , " . $maxItems);

				if (! empty($users)) {

	?>
					<div class = "container text-right users">
						<h1 class = "text-center">بيانات الأعضاء</h1>
						<form class = "SearchForm ajax-form" action = "?do=UserSearch" method = "POST" enctype = "multipart/form-data">
							<!--Start Student Code Field-->
							<div class = "form-group row justify-content-md-center">
								<div class = "col-md-4 pl-md-0 search">
									<input type = "search" name = "search" placeholder = "بحث"  class = "form-control searchUser ajax-type rounded-right" autocomplete = "off" />
									<button type = "submit" class = "btn bg-transparent p-0 position-absolute"><i class = "fas fa-search"></i></button>
								</div>
								<div class = "col-md-2 pr-md-0">
									<select class="form-control rounded-left userSearchBy ajax-select" id="selectstatus" name = "searchtype" data-size="5">
										<option value = "0" selected>الاسم</option>
										<option value = "1">اسم المستخدم</option>
										<option value = "2">البريد الإلكترونى</option>
										<option value = "3">تاريخ التسجيل</option>
										<option value = "4">صلاحيات المستخدم</option>
									</select>
								</div>
							</div>
							<!--End Student Code Field-->
						</form>
						<div class = "main-data ajax-val">
							<div class = "table-responsive">
								<table class="main-table text-center table table-bordered">
									<thead class="thead-dark">
										<tr>
											<th scope="col">الصورة</th>
											<th scope="col">الاسم</th>
											<th scope="col">اسم المستخدم</th>
											<th scope="col">البريد الإلكترونى</th>
											<th scope="col" style = "min-width: 150px;">تاريخ التسجيل</th>
											<th scope="col">صلاحيات المستخدم</th>
											<th scope="col">أدوات التحكم</th>
										</tr>
									</thead>
									<tbody>
										<?php

											foreach ($users as $user) {
												
										?>

												<tr>
													<th scope="row">
														<?php 
															echo "<img class = 'img-fluid show_image' data_edit = '?do=EditImage&id=" . $user['id'] . "' data_delete = '?do=DeleteImage&id=" . $user['id'] . "' data-toggle='modal' data-target='.show_image_modal' src = 'data\uploads\users_images\\"; 
																if (!empty($user["image"])) {

																	if (file_exists("data/uploads/users_images/" . $user['image'])) {

																		echo $user["image"];

																	} else {

																		echo "default.png";

																	}

																} else {

																	echo "default.png";
																	
																}
															echo "'>";
														?>	
													</th>
													<th scope="row"><?php echo $user["full_name"]; ?></th>
													<th scope="row"><?php echo $user["username"]; ?></th>
													<th scope="row"><?php echo $user["email"]; ?></th>
													<th scope="row"><?php if ($user["date"] != "0000-00-00" && $user["date"] != "1970-01-01") { echo str_replace('-', ' / ', arabicNumbers(date('d-m-Y', strtotime($user["date"])))); } ?></th>
													<th scope="row"><?php if ($user["group_id"] == 1) { echo "مدير"; } else if ($user["group_id"] == 2) { echo "مساعد مدير"; } else if ($user["group_id"] == 3) { echo "أنشطة / مخالفات"; } else if ($user["group_id"] == 4) { echo "مراجع"; } ?></th>
													<th scope="row">
														<a href="?do=Edit&id=<?php echo $user['id']; ?>" class="btn btn-sm btn-success"><i class="fas fa-edit"></i> تعديل</a> 
														<a href="?do=Delete&id=<?php echo $user['id']; ?>" class="btn btn-sm btn-danger confirm" data-toggle="modal" data-target="#delete-confirm"><i class="fas fa-trash-alt"></i> حذف</a>
														<div class="btn-group">
															<button type="button" class="btn btn-sm btn-info dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
																<i class="fas fa-cogs"></i> خيارات أخرى
															</button>
															<div class="dropdown-menu text-right">
																<a class="dropdown-item" href="?do=UserHistory&id=<?php echo $user['id']; ?>&currentPage=1">تاريخ التعديل</a>
															</div>
														</div>
													</th>
												</tr>

										<?php

											}
										
										?>
									</tbody>
								</table>
								<div class="modal fade show_image_modal" style = "max-height: 95%;" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
									<div class="modal-dialog modal-lg modal-dialog-centered" style = "height: 95%; margin-bottom: 0">
										<div class="modal-content" style = "max-height: 100%;">
											<div class = "position-absolute" style = "top: 0; right:0; width: 100%; padding: 10px">
												<button type="button" class="close ml-0 text-white" data-dismiss="modal" aria-label="Close">
													<span aria-hidden="true">&times;</span>
												</button>
											</div>
											<div class="modal-body text-right bg-dark rounded" style = "max-height: 100%;">
												<div class = "position-absolute" style = "top: 0; right: 0; width: 100%; padding: 15px; background: linear-gradient(rgba(0, 0, 0, .5), rgba(0,0,0,0)); height: 85px;">
													<a href="" class="btn btn-sm btn-success edit"><i class="fas fa-edit"></i> تعديل</a> 
													<a href="" class="btn btn-sm btn-danger confirm delete" data-toggle="modal" data-target="#delete-confirm"><i class="fas fa-trash-alt"></i> حذف</a>
													<button type="button" class="close text-danger position-absolute" style = "top: 15; left: 15;" data-dismiss="modal" aria-label="Close">
														<i class="far fa-times-circle"></i>
													</button>
												</div>
												<img class = 'img-fluid mx-auto d-block' src = '' style = "height: 100%;" />
											</div>
										</div>
									</div>
								</div>
								<form action = "" method = "POST" class = "confirm-modal">	
									<!-- Modal -->
									<div class="modal fade" id="delete-confirm" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
										<div class="modal-dialog modal-dialog-centered" role="document">
											<div class="modal-content">
												<div class="modal-header pl-0">
													<h5 class="modal-title" id="exampleModalLongTitle">حذف</h5>
													<button type="button" class="close ml-0" data-dismiss="modal" aria-label="Close">
														<span aria-hidden="true">&times;</span>
													</button>
												</div>
												<div class="modal-body text-right">هل أنت متأكد أنك تريد الحذف؟</div>
												<div class="modal-footer">
													<button type="submit" class="btn btn-primary delete-confirm-true">حذف</button>
													<button type="button" class="btn btn-secondary" data-dismiss="modal">إلغاء</button>
												</div>
											</div>
										</div>
									</div>
								</form>
							</div>
							<a href = "?do=Add"  class = "btn btn-primary"><i class="fas fa-plus"></i> إضافة عضو</a>
							<footer class = "my-2">
								<?php

									// Call The Pagination Bar
							
									$paging_info = getPagination($paginationData);

								?>
							</footer>
						</div>
					</div>

<?php

				} else {

					echo "<div class = 'alert alert-warning container'><i class='fas fa-exclamation-circle'></i> لا يوجد أعضاء هل تريد <a href = '?do=Add'  class = 'badge badge-pill badge-primary'><i class='fas fa-plus'></i> إضافة طالب</a> جديد؟</div>";

				}

			} else if ($do === "Add") { // Add Page

?>

				<h1 class = "text-center">إضافة عضو جديد</h1>
				<div class = "container text-right">
					<form class = "ajax-form" action = "?do=Insert" method = "POST" enctype = "multipart/form-data">
						<!--Start Username Field-->
						<div class = "form-group row justify-content-md-center">
							<label class = "col-md-2 control-label">اسم المستخدم</label>
							<div class = "col-md-4">
								<input type = "text" name = "username" class = "form-control" autocomplete = "off" placeholder = "اسم المستخدم" required />
							</div>
						</div>
						<!--End Username Field-->
						<!--Start Password Field-->
						<div class = "form-group row justify-content-md-center">
							<label class = "col-md-2 control-label">كلمة المرور</label>
							<div class = "col-md-4">
								<input type = "password" name = "password" class = "form-control password" autocomplete = "new-password" placeholder = "كلمة المرور" required />
								<i class="show-pass fas fa-eye" style = "left: 45px;"></i>
							</div>
						</div>
						<!--End Password Field-->
						<!--Start Full Name Field-->
						<div class = "form-group row justify-content-md-center">
							<label class = "col-md-2 control-label">اسم العضو</label>
							<div class = "col-md-4">
								<input type = "text" name = "full_name" class = "form-control" autocomplete = "off" placeholder = "اسم العضو" required />
							</div>
						</div>
						<!--End Full Name Field-->
						<!--Start Email Field-->
						<div class = "form-group row justify-content-md-center">
							<label class = "col-md-2 control-label">البريد الإلكترونى</label>
							<div class = "col-md-4">
								<input type = "text" name = "email" class = "form-control" autocomplete = "off" placeholder = "البريد الإلكترونى" />
							</div>
						</div>
						<!--End Email Field-->
						<!--Start Group ID Select-->
						<div class="form-group row justify-content-md-center">
							<label class = "col-md-2 control-label" for="selectstatus">صلاحيات المستخدم</label>
							<div class = "col-md-4">
								<select class="form-control" id="selectstatus" name = "group_id" data-size="5">
									<option value = "1">مدير</option>
									<option value = "2">مساعد مدير</option>
									<option value = "3">أنشطة / مخالفات</option>
									<option value = "4" selected>مراجع</option>
								</select>
							</div>
						</div>
						<!--End Group ID Select-->
						<!--Start Image Field-->
						<div class = "form-group row justify-content-md-center">
							<label class = "col-md-2 control-label">صورة الطالب</label>
							<div class = "col-md-4">
								<div class="custom-file text-left">
									<input type="file" name = "image" class="custom-file-input" id="memberImage" accept="image/*" aria-describedby="inputGroupFileAddon03">
									<label class="custom-file-label" for="memberImage">اختر صورة العضو</label>
								</div>
							</div>
						</div>
						<!--End Image Field-->
						<!--Start Submit Button-->
						<div class = "form-group row justify-content-md-center text-left">
							<div class = "col-md-6">
								<button type = "submit" class = "btn btn-primary"><i class="fas fa-plus"></i> إضافة عضو جديد</button>
							</div>
						</div>
						<!--End Submit Button-->
					</form>
					<div class = "ajax-val"></div>
				</div>

<?php

			} else if ($do === "Insert") {	//	Insert Page

				if ($_SERVER['REQUEST_METHOD'] === "POST") {
					
					// Upload Variables

					$imageName 	= $_FILES['image']['name'];
					$imageSize 	= $_FILES['image']['size'];
					$imageTmp 	= $_FILES['image']['tmp_name'];
					$imageType 	= $_FILES['image']['type'];

					// List Of Allowed File Typed To Upload

					$imageAllowedExtensions = array("jpeg", "jpg", "png", "bmp");

					// Get Image Extention

					$extention = explode(".", $imageName);

					$imageExtension = strtolower(end($extention));
					
					// Get Variables From  The Form

					$username = filter_var($_POST["username"], FILTER_SANITIZE_STRING);
					$password = filter_var($_POST["password"], FILTER_SANITIZE_STRING);
					$full_name = filter_var($_POST["full_name"], FILTER_SANITIZE_STRING);
					$email = $_POST["email"];
					$group_id = filter_var($_POST["group_id"], FILTER_SANITIZE_NUMBER_INT);
					
					echo "<div class = 'container text-right'>";

						//	Validate The Form

						$formErrors = array();

						if (empty($username)) {

							$formErrors[] = "اسم المستخدم لا يجب أن يكون <strong>فارغ</strong>";

						}

						if (empty($password)) {

							$formErrors[] = "كلمة المرور لا يجب أن تكون فارغة <strong>فارغ</strong>";

						}

						if (empty($full_name)) {

							$formErrors[] = "اسم العضو لا يجب أن يكون  <strong>فارغ</strong>";

						}
						
						if (! empty($imageName) && ! in_array($imageExtension, $imageAllowedExtensions)) {

							$formErrors[] = "صيغة هذا الملف <strong>غير مسموح بها</strong> الرجاء اختيار صورة فقط";

						}

						if ($imageSize > 4194304) {

							$formErrors[] = "لا يمكنك ادخال صورة اكبر من <strong>4MB</strong>";

						}

						// Loop Into Error Array And Echo It

						foreach ($formErrors as $error) {

							echo "<div class = 'alert alert-danger'>" . $error . "</div>";

						}

						// Check If There's No Error Proceed The Update Operation

						if (empty($formErrors)) {

							if (! empty($imageName)) {

								$image = rand(0, 10000000000) . '_' . $imageName;

								$path = "data/uploads/users_images";

								move_uploaded_file($imageTmp, "$path/$image");

							} else {

								$image = "";

							}
							
							$password = md5($password);
								
							// Get Who Added

							$who_added = getSelect("full_name", "users", "username = '" . $_SESSION["AdminUsername"] . "'", 1, "ASC", false);

							// Check If This Activity Exists Or Not

							$isUserExist = checkSelect("*", "users", "username = '" . $username . "'");

							if ($isUserExist === 0) {

								// Insert Into Database With This Info

								$stmt = $con->prepare("INSERT INTO 
															users(username, password, email, full_name, image, date, group_id, who_added)
														VALUES 
															(?, ?, ?, ?, ?, '" . date("Y-m-d h:i:s") . "', ?, ?)");

								$stmt->execute(array($username, $password, $email, $full_name, $image, $group_id, $who_added['full_name']));

								if ($stmt->rowCount() > 0) {

									// Get User ID

									$userid = getSelect("id", "users", "username = '" . $_SESSION["AdminUsername"] . "'", 1, "ASC", false);

									// Get User ID

									$addedUserId = getSelect("id", "users", "username = '" . $username . "'", 1, "ASC", false);

									// Add To The Update History

									$stmt = $con->prepare("INSERT INTO 
																data_updates(category, action, date, updater_id, updated_id)
															VALUES 
																('users', 'add', '" . date("Y-m-d h:i:s") . "', ?, ?)");

									$stmt->execute(array( $userid['id'], $addedUserId['id'] ));

								}
								
								// Echo Success Message

								echo "<div class = 'alert alert-success'>تم إضافة العضو بنجاح</div>";

							} else {
								
								echo "<div class = 'alert alert-warning'>تم إضافة هذا العضو من قبل</div>";
		
							}
							
						}

					echo "</div>";

				} else {

					redirect("<div class = 'alert alert-danger'>لا يمكنك تصفح هذه الصفحة مباشرةً</div>");

				}

			} else if ($do === "Edit") {	//	Edit Page 

				// Check If Get Request Phone ID Is Numeric & Get The Integer Value Of It

				$id = isset($_GET["id"]) && is_numeric(filter_var($_GET["id"], FILTER_SANITIZE_NUMBER_INT)) ? intval($_GET["id"]) : 0;

				// The Row Count

				$count = checkSelect("*", "users", "id = " . $id);

				// If There Is Such ID Show The Form

				if ($count > 0) {

					$user = getSelect("*", "users", "id = " . $id, 1, "ASC", false);

?>

					<h1 class = "text-center">تعديل بيانات العضو</h1>
					<?php

						if ($user['image'] != "") {

							if (file_exists("data/uploads/users_images/" . $user['image'])) {

								echo '<img src="data\uploads\users_images\\' . $user['image'] . '" class="img-fluid img-thumbnail rounded-circle mx-auto d-block mb-4" style = "width: 150; height: 170;">';

							}

						}

					?>
					<div class = "container text-right">
						<form class = "ajax-form" action = "?do=Update" method = "POST" enctype = "multipart/form-data">
							<input type = "hidden" name = "id" value = "<?php echo $id; ?>" />
							<!--Start Username Field-->
							<div class = "form-group row justify-content-md-center">
								<label class = "col-md-2 control-label">اسم المستخدم</label>
								<div class = "col-md-4">
									<input type = "text" name = "username" class = "form-control" autocomplete = "off" placeholder = "اسم المستخدم" required value = "<?php echo $user['username']; ?>" required />
								</div>
							</div>
							<!--End Username Field-->
							<!--Start Password Field-->
							<div class = "form-group row justify-content-md-center">
								<label class = "col-md-2 control-label">كلمة المرور</label>
								<div class = "col-md-4">
									<input type = "password" name = "password" class = "form-control password" autocomplete = "new-password" placeholder = "كلمة المرور" />
									<i class="show-pass fas fa-eye" style = "left: 25px;"></i>
								</div>
							</div>
							<!--End Password Field-->
							<!--Start Full Name Field-->
							<div class = "form-group row justify-content-md-center">
								<label class = "col-md-2 control-label">اسم العضو</label>
								<div class = "col-md-4">
									<input type = "text" name = "full_name" class = "form-control" autocomplete = "off" placeholder = "اسم العضو" value = "<?php echo $user['full_name']; ?>" required />
								</div>
							</div>
							<!--End Full Name Field-->
							<!--Start Email Field-->
							<div class = "form-group row justify-content-md-center">
								<label class = "col-md-2 control-label">البريد الإلكترونى</label>
								<div class = "col-md-4">
									<input type = "text" name = "email" class = "form-control" autocomplete = "off" placeholder = "البريد الإلكترونى" value = "<?php echo $user['email']; ?>" />
								</div>
							</div>
							<!--End Email Field-->
							<!--Start Group ID Select-->
							<div class="form-group row justify-content-md-center">
								<label class = "col-md-2 control-label" for="selectstatus">صلاحيات المستخدم</label>
								<div class = "col-md-4">
									<select class="form-control" id="selectstatus" name = "group_id" data-size="5">
										<option value = "1" <?php if ($user["group_id"] == 1) { echo "selected"; } ?>>مدير</option>
										<option value = "2" <?php if ($user["group_id"] == 2) { echo "selected"; } ?>>مساعد مدير</option>
										<option value = "3" <?php if ($user["group_id"] == 3) { echo "selected"; } ?>>أنشطة / مخالفات</option>
										<option value = "4" <?php if ($user["group_id"] == 4) { echo "selected"; } ?>>مراجع</option>
									</select>
								</div>
							</div>
							<!--End Group ID Select-->
							<!--Start Image Field-->
							<div class = "form-group row justify-content-md-center">
								<label class = "col-md-2 control-label">صورة الطالب</label>
								<div class = "col-md-4">
									<div class="custom-file text-left">
										<input type="file" name = "image" class="custom-file-input" id="memberImage" accept="image/*" aria-describedby="inputGroupFileAddon03">
										<label class="custom-file-label" for="memberImage">
											<?php
												if ($user['image'] == "") {

													echo "لا يوجد صورة للعضو قم بإختيار صورة له";

												} else {

													echo $user['image'];

												}
											?>
										</label>
									</div>
								</div>
							</div>
							<!--End Image Field-->
							<!--Start Submit Button-->
							<div class = "form-group row justify-content-md-center text-left">
								<div class = "col-md-6">
									<button type = "submit" class = "btn btn-primary"><i class="fas fa-plus"></i> تعديل العضو</button>
								</div>
							</div>
							<!--End Submit Button-->
						</form>
						<div class = "ajax-val"></div>
					</div>

<?php

				// If There Is No Such ID Show Error Message

				} else {

					redirect("<div class = 'alert alert-danger'>هذا الطالب غير موجود</div>", 3, "users.php", "صفحة بيانات الأعضاء");

				}

			} else if ($do === "Update") {	//	Update Page

				if ($_SERVER['REQUEST_METHOD'] === "POST") {

					// Check If Post Request Student ID Is Numeric & Get The Integer Value Of It

					$id = isset($_POST["id"]) && is_numeric(filter_var($_POST["id"], FILTER_SANITIZE_NUMBER_INT)) ? intval($_POST["id"]) : 0;

					// The Row Count

					$count = checkSelect("*", "users", "id = " . $id);

					// If There Is Such ID Show The Form

					if ($count > 0) {

						echo "<div class = 'container text-right'>";

							// Upload Variables

							$imageName 	= $_FILES['image']['name'];
							$imageSize 	= $_FILES['image']['size'];
							$imageTmp 	= $_FILES['image']['tmp_name'];
							$imageType 	= $_FILES['image']['type'];

							// List Of Allowed File Typed To Upload

							$imageAllowedExtensions = array("jpeg", "jpg", "png", "bmp");

							// Get Image Extention

							$extention = explode(".", $imageName);

							$imageExtension = strtolower(end($extention));

							// Get Variables From  The Form

							$username = filter_var($_POST["username"], FILTER_SANITIZE_STRING);
							$password = filter_var($_POST["password"], FILTER_SANITIZE_STRING);
							$full_name = filter_var($_POST["full_name"], FILTER_SANITIZE_STRING);
							$email = filter_var($_POST["email"], FILTER_SANITIZE_EMAIL);
							$group_id = filter_var($_POST["group_id"], FILTER_SANITIZE_NUMBER_INT);
						
							//	Validate The Form

							$formErrors = array();

							if ( empty($username) ) {

								$formErrors[] = "اسم المستخدم لا يجب أن يكون  <strong>فارغ</strong>";

							}

							if ( empty($full_name) ) {

								$formErrors[] = "اسم العضو لا يجب أن يكون  <strong>فارغ</strong>";

							}
							
							if (! empty($imageName) && ! in_array($imageExtension, $imageAllowedExtensions)) {

								$formErrors[] = "صيغة هذا الملف <strong>غير مسموح بها</strong> الرجاء اختيار صورة فقط";

							}

							if ($imageSize > 4194304) {

								$formErrors[] = "لا يمكنك ادخال صورة اكبر من <strong>4MB</strong>";

							}

							// Loop Into Error Array And Echo It

							foreach ($formErrors as $error) {

								echo "<div class = 'alert alert-danger'>" . $error . "</div>";

							}

							// Check If There's No Error Proceed The Update Operation

							if (empty($formErrors)) {

								if (! empty($imageName)) {

									$image = rand(0, 10000000000) . '_' . $imageName;

									move_uploaded_file($imageTmp, "data\uploads\users_images\\" . $image);

								} else {

									$image = "";

								}

								// Check If This User Exists Or Not

								$isExist = checkSelect("username", "users", "username = '" . $username . "' AND id != " . $id);

								if ($isExist === 0) {

									// Get Student Image Name

									$getImage = getSelect("image", "users", "id = " . $id, 1, "ASC", false);

									$getImage = $getImage['image'];
									
									if (empty($password)) {
										
										$getPass = getSelect("password", "users", "username = '" . $username . "'", 1, "ASC", false);
										
										$password = $getPass["password"];
										
									} else {
										
										$password = md5($password);
										
									}

									// Update The Database With This Info

									if ($image == "") {

										$stmt = $con->prepare("UPDATE users SET username = ?, password = ?, full_name = ?, email = ?, group_id = ? WHERE id = ?");
									
										$stmt->execute(array($username, $password, $full_name, $email, $group_id, $id));

									} else {

										$stmt = $con->prepare("UPDATE users SET username = ?, password = ?, full_name = ?, email = ?, group_id = ?, image = ? WHERE id = ?");
									
										$stmt->execute(array($username, $password, $full_name, $email, $group_id, $image, $id));

									}
									
									if ($stmt->rowCount() > 0) {

										if ($image != "") {

											if (! empty($getImage)) {

												$path = "data/uploads/users_images";

												if (file_exists("$path/$getImage")) {

													unlink("$path/$getImage");

												}

											}

										}

										// Get Userid

										$userid = getSelect("id", "users", "username = '" . $_SESSION["AdminUsername"] . "'", 1, "ASC", false);

										// Add To The Update History

										$stmt = $con->prepare("INSERT INTO 
																	data_updates(category, action, date, updater_id, updated_id)
																VALUES 
																	('users', 'update', '" . date("Y-m-d h:i:s") . "', ?, ?)");

										$stmt->execute(array($userid['id'], $id));

									}

									// Echo Success Message

									echo "<div class = 'alert alert-success'>تم تعديل بيانات هذا العضو بنجاح</div>";

								} else {

									echo "<div class = 'alert alert-danger'>هذا العضو موجود بالفعل</div>";

								}
								
							}

						echo "</div>";

					// If There Is No Such ID Show Error Message

					} else {

						echo "<div class = 'alert alert-danger'>هذا العضو غير موجود</div>";

					}

				} else {

					redirect("<div class = 'alert alert-danger'>لا يمكنك تصفح هذه الصفحة مباشرةً</div>");

				}

			} else if ($do === "Delete") {	// Delete Page

				// Check If Get Request Phone ID Is Numeric & Get The Integer Value Of It

				$id = isset($_GET["id"]) && is_numeric($_GET["id"]) ? intval($_GET["id"]) : 0;

				echo "<h1 class = 'text-center'>حذف بيانات العضو</h1>";
				echo "<div class = 'container text-right'>";

					// Get User Image Name

					$image = getSelect("image", "users", "id = " . $id, 1, "ASC", false);

					$image = $image['image'];

					// Check If This Student Exists Or Not

					$isExist = checkSelect("id", "users", "id = " . $id);

					// If There Is Such ID Show The Form

					if ($isExist > 0) { 

						// Delete Student

						$stmt = $con->prepare("DELETE FROM users WHERE id = :id");

						$stmt->bindParam(":id", $id);

						$stmt->execute();
						
						if ($stmt->rowCount() > 0) {

							if (! empty($image)) {

								$path = "data/uploads/users_images";

								if (file_exists("$path/$image")) {

									unlink("$path/$image");

								}

							}

							// Add To The Update History

							$stmt = $con->prepare("DELETE FROM 
														data_updates
													WHERE 
														updated_id = ? AND (category = 'users' OR category = 'users_images')");

							$stmt->execute(array($id));

						}
						
						redirect("<div class = 'alert alert-success'>تم حذف بيانات العضو بنجاح</div>", 3, "users.php", "صفحة بيانات الأعضاء");
							
					} else {

						// If There Is No Such ID Show Error Message

						redirect("<div class = 'alert alert-danger'>هذا العضو غير موجود</div>", 3, "users.php", "صفحة بيانات الأعضاء");

					}

				echo "</div>";

			} else if ($do === "EditImage") {

				// Check If Get Request Phone ID Is Numeric & Get The Integer Value Of It

				$id = isset($_GET["id"]) && is_numeric(filter_var($_GET["id"], FILTER_SANITIZE_NUMBER_INT)) ? intval($_GET["id"]) : 0;

				// The Row Count

				$count = checkSelect("*", "users", "id = " . $id);

				// If There Is Such ID Show The Form

				if ($count > 0) {

					$user = getSelect("*", "users", "id = " . $id, 1, "ASC", false);

?>

					<h1 class = "text-center">تعديل صورة العضو</h1>
					<?php

						if ($user['image'] != "") {

							if (file_exists("data/uploads/users_images/" . $user['image'])) {

								echo '<img src="data\uploads\users_images\\' . $user['image'] . '" class="img-fluid img-thumbnail rounded-circle mx-auto d-block mb-4" style = "width: 150; height: 170;">';

							}

						}

					?>
					<div class = "container text-right">
						<form action = "?do=UpdateImage" method = "POST" enctype = "multipart/form-data">
							<input type = "hidden" name = "id" value = "<?php echo $id; ?>" />
							<!--Start Image Field-->
							<div class = "form-group row justify-content-md-center">
								<label class = "col-md-2 control-label">صورة الطالب</label>
								<div class = "col-md-4">
									<div class="custom-file text-left">
										<input type="file" name = "image" class="custom-file-input" id="memberImage" accept="image/*" aria-describedby="inputGroupFileAddon03">
										<label class="custom-file-label" for="memberImage">
											<?php
												if ($user['image'] == "") {

													echo "لا يوجد صورة للعضو قم بإختيار صورة له";

												} else {

													echo $user['image'];

												}
											?>
										</label>
									</div>
								</div>
							</div>
							<!--End Image Field-->
							<!--Start Submit Button-->
							<div class = "form-group row justify-content-md-center text-left">
								<div class = "col-md-6">
									<button type = "submit" class = "btn btn-primary"><i class="fas fa-plus"></i> تعديل الصورة</button>
								</div>
							</div>
							<!--End Submit Button-->
						</form>
					</div>

<?php

				// If There Is No Such ID Show Error Message

				} else {

					redirect("<div class = 'alert alert-danger'>هذا الطالب غير موجود</div>", 3, "users.php", "صفحة بيانات الأعضاء");

				}

			} else if ($do === "UpdateImage") {	//	Update Page

				if ($_SERVER['REQUEST_METHOD'] === "POST") {

					// Check If Post Request Student ID Is Numeric & Get The Integer Value Of It

					$id = isset($_POST["id"]) && is_numeric(filter_var($_POST["id"], FILTER_SANITIZE_NUMBER_INT)) ? intval($_POST["id"]) : 0;

					// The Row Count

					$count = checkSelect("*", "users", "id = " . $id);

					// If There Is Such ID Show The Form

					if ($count > 0) {

						echo "<h1 class = 'text-center'>تعديل صورة العضو</h1>";
						echo "<div class = 'container text-right'>";

							// Upload Variables

							$imageName 	= $_FILES['image']['name'];
							$imageSize 	= $_FILES['image']['size'];
							$imageTmp 	= $_FILES['image']['tmp_name'];
							$imageType 	= $_FILES['image']['type'];

							// List Of Allowed File Typed To Upload

							$imageAllowedExtensions = array("jpeg", "jpg", "png", "bmp");

							// Get Image Extention

							$extention = explode(".", $imageName);

							$imageExtension = strtolower(end($extention));
						
							//	Validate The Form

							$formErrors = array();

							if (! empty($imageName) && ! in_array($imageExtension, $imageAllowedExtensions)) {

								$formErrors[] = "صيغة هذا الملف <strong>غير مسموح بها</strong> الرجاء اختيار صورة فقط";

							}

							if ($imageSize > 4194304) {

								$formErrors[] = "لا يمكنك ادخال صورة اكبر من <strong>4MB</strong>";

							}

							// Loop Into Error Array And Echo It

							foreach ($formErrors as $error) {

								echo "<div class = 'alert alert-danger'>" . $error . "</div>";

							}

							// Check If There's No Error Proceed The Update Operation

							if (empty($formErrors)) {

								// Get Student Image Name

								$getImage = getSelect("image", "users", "id = " . $id, 1, "ASC", false);

								$getImage = $getImage['image'];

								if (! empty($imageName)) {

									$image = rand(0, 10000000000) . '_' . $imageName;

									$path = "data/uploads/users_images";

									move_uploaded_file($imageTmp, "$path/$image");

								} else {

									$image = "";

								}

								// Update The Database With This Info

								if ($image != "") {

									$stmt = $con->prepare("UPDATE users SET image = ? WHERE id = ?");
								
									$stmt->execute(array($image, $id));

								}
								
								if ($stmt->rowCount() > 0) {

									if ($image != "") {

										if (! empty($getImage)) {

											$path = "data/uploads/users_images";

											if (file_exists("$path/$getImage")) {

												unlink("$path/$getImage");

											}

										}

									}

									// Get Userid

									$userid = getSelect("id", "users", "username = '" . $_SESSION["AdminUsername"] . "'", 1, "ASC", false);

									// Add To The Update History

									$stmt = $con->prepare("INSERT INTO 
																data_updates(category, action, date, updater_id, updated_id)
															VALUES 
																('users_images', 'update', '" . date("Y-m-d h:i:s") . "', ?, ?)");

									$stmt->execute(array($userid['id'], $id));

								}

								// Echo Success Message

								$theMsg = "<div class = 'alert alert-success'>تم تعديل صورة العضو بنجاح</div>";

								redirect($theMsg, 3, "users.php", "صفحة بيانات الأعضاء");
								
							}

						echo "</div>";

					// If There Is No Such ID Show Error Message

					} else {

						redirect("<div class = 'alert alert-danger'>هذا العضو غير موجود</div>", 3, "users.php", "صفحة بيانات الأعضاء");

					}

				} else {

					redirect("<div class = 'alert alert-danger'>لا يمكنك تصفح هذه الصفحة مباشرةً</div>");

				}

			} else if ($do === "DeleteImage") {	// Delete Page

				// Check If Post Request Student ID Is Numeric & Get The Integer Value Of It

				$id = isset($_GET["id"]) && is_numeric(filter_var($_GET["id"], FILTER_SANITIZE_NUMBER_INT)) ? intval($_GET["id"]) : 0;

				// The Row Count

				$count = checkSelect("*", "users", "id = " . $id);

				// If There Is Such ID Show The Form

				if ($count > 0) {

					echo "<h1 class = 'text-center'>حذف صورة الطالب</h1>";
					echo "<div class = 'container text-right'>";

						// Get Student Image Name

						$getImage = getSelect("image", "users", "id = " . $id, 1, "ASC", false);

						$getImage = $getImage['image'];

						// Update The Database With This Info

						if ($getImage != "") {

							$stmt = $con->prepare("UPDATE users SET image = '' WHERE id = ?");
						
							$stmt->execute(array($id));

							if ($stmt->rowCount() > 0) {

								if ($getImage != "") {

									$path = "data/uploads/users_images";

									if (file_exists("$path/$getImage")) {

										unlink("$path/$getImage");

									}

								}

								// Get Userid

								$userid = getSelect("id", "users", "username = '" . $_SESSION["AdminUsername"] . "'", 1, "ASC", false);

								// Add To The Update History

								$stmt = $con->prepare("DELETE FROM 
															data_updates
														WHERE 
															updated_id = ? AND category = 'users_images'");

								$stmt->execute(array($id));

							}

						}
						

						// Echo Success Message

						$theMsg = "<div class = 'alert alert-success'>تم حذف صورة العضو بنجاح</div>";

						redirect($theMsg, 3, "users.php", "صفحة بيانات الأعضاء");

					echo "</div>";

				// If There Is No Such ID Show Error Message

				} else {

					redirect("<div class = 'alert alert-danger'>هذا العضو غير موجود</div>", 3, "users.php", "صفحة بيانات الأعضاء");

				}

			} else if ($do === "UserHistory") {	// Student History Page

				// Check If Get Request Phone ID Is Numeric & Get The Integer Value Of It

				$id = isset($_GET["id"]) && is_numeric($_GET["id"]) ? intval($_GET["id"]) : 0;

				// Check If This User Exists Or Not

				$isExist = checkSelect("id", "users", "id = " . $id);

				// If There Is Such ID Show The Form

				if ($isExist != 0) {

					// Check If This Student Exists Or Not

					$isExist = checkSelect("updated_id, category", "data_updates", "updated_id = " . $id . " AND category = 'users'");

					// If There Is Such ID Show The Form

					if ($isExist != 0) {
						
						if (!isset($_GET['currentPage'])) {

							$currentPage = 1;

						} else {

							$currentPage = $_GET['currentPage'];

						}

						$countStudents = checkSelect("*", "data_updates", "updated_id = " . $id . " AND category = 'users'");

						$currentPage = filter_var($currentPage, FILTER_SANITIZE_NUMBER_INT);

						$maxItems = 10;

						if (! filter_var($currentPage, FILTER_VALIDATE_INT) || $currentPage > ceil($countStudents / $maxItems)) {

							$currentPage = 1;

						}

						$paginationData = setPagination($countStudents, $maxItems, $currentPage);

						$histories = getSelect("*", "data_updates", "updated_id = " . $id . " AND category = 'users'", "date", "DESC LIMIT " . $paginationData['srow'] . " , " . $maxItems);

?>

						<div class = "container text-right">
							<h1 class = "text-center">تاريخ العضو</h1>				
							<?php

								foreach ($histories as $history) {

									$updaterName = getSelect("full_name", "users", "id = " . $history['updater_id'], 1, "ASC", false);

							?>
									<div class="card mb-2">
										<div class="card-header">
											<?php echo lang($history['action']); ?>
										</div>
										<div class="card-body">
											<blockquote class="blockquote mb-0">
												<p>تم <?php echo lang($history['action']); echo " هذا العضو بواسطة " . getWord($updaterName['full_name'], 3); ?></p>
												<footer class="blockquote-footer">بتاريخ <?php echo str_replace('-', ' / ', arabicNumbers(date('d-m-Y', strtotime($history["date"])))) . " توقيت " . arabicNumbers(date('h:i', strtotime($history["date"]))); ?></footer>
											</blockquote>
										</div>
									</div>
							<?php

								}

							?>		
						</div>
						<footer class = "my-2">
							<?php

								// Call The Pagination Bar
						
								$paging_info = getPagination($paginationData);

							?>
						</footer>

<?php 				

					} else {
						
						echo "<div class = 'alert alert-warning container'><i class='fas fa-exclamation-circle'></i> لا يوجد تاريخ تعديل لهذا العضو</div>";

					}
					
				} else {

					redirect("<div class = 'alert alert-danger'>هذا العضو غير موجود</div>", 3, "users.php", "صفحة بيانات الأعضاء");

				}

			} else if ($do === "UserSearch") { // Activities Search Page

				$search = filter_var($_POST['search'], FILTER_SANITIZE_STRING);
				$searchtype = isset($_POST["searchtype"]) && is_numeric(filter_var($_POST["searchtype"], FILTER_SANITIZE_NUMBER_INT)) ? intval($_POST["searchtype"]) : 1;
				
				if (!isset($_GET['currentPage'])) {

					$currentPage = 1;

				} else {

					$currentPage = $_GET['currentPage'];

				}

				$searchColumn = "";

				if ($searchtype == 0) {

					$searchColumn = "full_name";

				} else if ($searchtype == 1) {
					
					$searchColumn = "username";
					
				} else if ($searchtype == 2) {

					$searchColumn = "email";

				} else if ($searchtype == 3) {

					$searchColumn = "date";

				} else if ($searchtype == 4) {

					$searchColumn = "group_id";

				} else {

					$searchColumn = "full_name";

				}

				if (!empty($search) || $search != "") {
					
					$stmt = $con->prepare("SELECT 
												* 
											FROM 
												users 
											WHERE 
												" . $searchColumn . " LIKE '%" . $search . "%'");

					$stmt->execute();

					$countActivities = $stmt->rowCount();

				} else {

					$stmt = $con->prepare("SELECT 
												* 
											FROM 
												users 
											");

					$stmt->execute();

					$countActivities = $stmt->rowCount();
				
				}

				$currentPage = filter_var($currentPage, FILTER_SANITIZE_NUMBER_INT);

				$maxItems = 10;

				if (! filter_var($currentPage, FILTER_VALIDATE_INT) || $currentPage > ceil($countActivities / $maxItems)) {

					$currentPage = 1;

				}

				$paginationData = setPagination($countActivities, $maxItems, $currentPage);

				if (!empty($search) || $search != "") {
					
					$stmt = $con->prepare("
											SELECT 
												* 
											FROM 
												users 
											WHERE 
												" . $searchColumn . " 
											LIKE '%" . $search . "%'
											ORDER BY id ASC
											LIMIT " . $paginationData['srow'] . " , " . $maxItems . "
										");

					$stmt->execute();

					$users = $stmt->fetchAll();

				} else {
					
					$stmt = $con->prepare("
											SELECT 
												* 
											FROM 
												users 
											ORDER BY id ASC
											LIMIT " . $paginationData['srow'] . " , " . $maxItems . "
										");

					$stmt->execute();

					$users = $stmt->fetchAll();

				}

				if (! empty($users)) {

	?>
					
					<div class = "table-responsive">
						<table class="main-table text-center table table-bordered">
							<thead class="thead-dark">
								<tr>
									<th scope="col">الصورة</th>
									<th scope="col">الاسم</th>
									<th scope="col">اسم المستخدم</th>
									<th scope="col">البريد الإلكترونى</th>
									<th scope="col" style = "min-width: 150px;">تاريخ التسجيل</th>
									<th scope="col">صلاحيات المستخدم</th>
									<th scope="col">أدوات التحكم</th>
								</tr>
							</thead>
							<tbody>
								<?php

									foreach ($users as $user) {
										
								?>

										<tr>
											<th scope="row">
												<?php 
													echo "<img class = 'img-fluid show_image' data_edit = '?do=EditImage&id=" . $user['id'] . "' data_delete = '?do=DeleteImage&id=" . $user['id'] . "' data-toggle='modal' data-target='.show_image_modal' src = 'data/uploads/users_images/"; 
														if (!empty($user["image"])) {

															if (file_exists("data/uploads/users_images/" . $user['image'])) {

																echo $user["image"];

															} else {

																echo "default.png";

															}

														} else {

															echo "default.png";
															
														}
													echo "'>";
												?>
											</th>
											<th scope="row"><?php echo $user["full_name"]; ?></th>
											<th scope="row"><?php echo $user["username"]; ?></th>
											<th scope="row"><?php echo $user["email"]; ?></th>
											<th scope="row"><?php if ($user["date"] != "0000-00-00" && $user["date"] != "1970-01-01") { echo str_replace('-', ' / ', arabicNumbers(date('d-m-Y', strtotime($user["date"])))); } ?></th>
											<th scope="row"><?php if ($user["group_id"] == 1) { echo "مدير"; } else if ($user["group_id"] == 2) { echo "مساعد مدير"; } else if ($user["group_id"] == 3) { echo "أنشطة / مخالفات"; } else if ($user["group_id"] == 4) { echo "مراجع"; } ?></th>
											<th scope="row">
												<a href="?do=Edit&id=<?php echo $user['id']; ?>" class="btn btn-sm btn-success"><i class="fas fa-edit"></i> تعديل</a> 
												<a href="?do=Delete&id=<?php echo $user['id']; ?>" class="btn btn-sm btn-danger confirm" data-toggle="modal" data-target="#delete-confirm"><i class="fas fa-trash-alt"></i> حذف</a>
												<div class="btn-group">
													<button type="button" class="btn btn-sm btn-info dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
														<i class="fas fa-cogs"></i> خيارات أخرى
													</button>
													<div class="dropdown-menu text-right">
														<a class="dropdown-item" href="?do=UserHistory&id=<?php echo $user['id']; ?>&currentPage=1">تاريخ التعديل</a>
													</div>
												</div>
											</th>
										</tr>

								<?php

									}
								
								?>
							</tbody>
						</table>
						<div class="modal fade show_image_modal" style = "max-height: 95%;" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
							<div class="modal-dialog modal-lg modal-dialog-centered" style = "height: 95%; margin-bottom: 0">
								<div class="modal-content" style = "max-height: 100%;">
									<div class = "position-absolute" style = "top: 0; right:0; width: 100%; padding: 10px">
										<button type="button" class="close ml-0 text-white" data-dismiss="modal" aria-label="Close">
											<span aria-hidden="true">&times;</span>
										</button>
									</div>
									<div class="modal-body text-right bg-dark rounded" style = "max-height: 100%;">
										<div class = "position-absolute" style = "top: 0; right: 0; width: 100%; padding: 15px; background: linear-gradient(rgba(0, 0, 0, .5), rgba(0,0,0,0)); height: 85px;">
											<a href="" class="btn btn-sm btn-success edit"><i class="fas fa-edit"></i> تعديل</a> 
											<a href="" class="btn btn-sm btn-danger confirm delete" data-toggle="modal" data-target="#delete-confirm"><i class="fas fa-trash-alt"></i> حذف</a>
											<button type="button" class="close text-danger position-absolute" style = "top: 15; left: 15;" data-dismiss="modal" aria-label="Close">
												<i class="far fa-times-circle"></i>
											</button>
										</div>
										<img class = 'img-fluid mx-auto d-block' src = '' style = "height: 100%;" />
									</div>
								</div>
							</div>
						</div>
						<form action = "" method = "POST" class = "confirm-modal">	
							<!-- Modal -->
							<div class="modal fade" id="delete-confirm" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
								<div class="modal-dialog modal-dialog-centered" role="document">
									<div class="modal-content">
										<div class="modal-header pl-0">
											<h5 class="modal-title" id="exampleModalLongTitle">حذف</h5>
											<button type="button" class="close ml-0" data-dismiss="modal" aria-label="Close">
												<span aria-hidden="true">&times;</span>
											</button>
										</div>
										<div class="modal-body text-right">هل أنت متأكد أنك تريد حذف هذا العضو؟</div>
										<div class="modal-footer">
											<button type="submit" class="btn btn-primary delete-confirm-true">حذف</button>
											<button type="button" class="btn btn-secondary" data-dismiss="modal">إلغاء</button>
										</div>
									</div>
								</div>
							</div>
						</form>
					</div>
					<a href = "?do=Add"  class = "btn btn-primary"><i class="fas fa-plus"></i> إضافة عضو</a>
					<footer class = "my-2">
						<?php

							// Call The Pagination Bar
					
							$paging_info = getPagination($paginationData);

						?>
					</footer>

	<?php

				} else {
					
	?>

					<div class = 'alert alert-warning container'><i class='fas fa-exclamation-circle'></i> لا يوجد أعضاء بهذه المواصفات  هل تريد <a href = '?do=Add'  class = 'badge badge-pill badge-primary'><i class='fas fa-plus'></i> إضافة عضو</a> جديد؟</div>

	<?php
					
				}

			} else {

				header("Location: users.php");

			}

			if ($do != "UserSearch" && $do != "Insert" && $do != "Update") {

				include $templates . "footer.php";

			}
		
		} else {
		
			header("Location: dashboard.php");
			
		}

	} else {

		header("Location: index.php");

		exit();

	}

	ob_end_flush();	//	Output Buffering End

?>