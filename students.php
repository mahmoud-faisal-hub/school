<?php

	ob_start();	//	Output Buffering Start

	session_start();

	if (isset($_SESSION["AdminUsername"])) {

		$do = isset($_GET['do']) ? $_GET['do'] : "Manage";

		if ($do != "StudentsSearch" && $do != "PhonesSearch" && $do != "sActivitySearch" && $do != "Insert" && $do != "Update" && $do != "InsertPhone" && $do != "UpdatePhone" && $do != "InsertActivity" && $do != "UpdateActivity") {

			$pageTitle = "بيانات الطلاب";

			$pageActive = basename(__FILE__);

		} else {

			$noNavbar = "";

		}

		include "init.php";

		// Get The User Powers

		$userPowers = getSelect("group_id", "users", "username ='" . $_SESSION["AdminUsername"] . "'", 1, "ASC", false);

		$powers = $userPowers["group_id"];

		// Start Manage Page

		if ($do === "Manage") {	// Manage Members Page 

			if (!isset($_GET['currentPage'])) {

				$currentPage = 1;

			} else {

				$currentPage = $_GET['currentPage'];

			}

			$countStudents = checkSelect("*", "students");

			$currentPage = filter_var($currentPage, FILTER_SANITIZE_NUMBER_INT);

			$maxItems = 10;

			if (! filter_var($currentPage, FILTER_VALIDATE_INT) || $currentPage > ceil($countStudents / $maxItems)) {

				$currentPage = 1;

			}

			$paginationData = setPagination($countStudents, $maxItems, $currentPage);

			$students = getSelect("*", "students", 1, "grade, class, name", "ASC LIMIT " . $paginationData['srow'] . " , " . $maxItems);

			if (! empty($students)) {

?>

				<div class = "main">
					<div class = "container text-right">
						<h1 class = "text-center">بيانات الطلاب</h1>
						<form class = "ajax-form" action = "?do=StudentsSearch" method = "POST" enctype = "multipart/form-data">
							<!--Start Student Code Field-->
							<div class = "form-group row justify-content-md-center">
								<div class = "col-md-4 pl-md-0 search">
									<input type = "search" name = "search" placeholder = "بحث"  class = "form-control rounded-right ajax-type" autocomplete = "off" />
									<button type = "submit" class = "btn bg-transparent p-0 position-absolute"><i class = "fas fa-search"></i></button>
								</div>
								<div class = "col-md-2 pr-md-0">
								    <select class="form-control rounded-left ajax-select" id="selectstatus" name = "searchtype" data-size="5">
										<option value = "0">كود الطالب</option>
										<option value = "1" selected>اسم الطالب</option>
										<option value = "2">السنة الدراسية</option>
										<option value = "3">الفصل</option>
										<option value = "4">اللغة الثانية	</option>
										<option value = "5">الرقم القومى	</option>
										<option value = "6">العنوان</option>
										<option value = "7">تاريخ الميلاد	</option>
										<option value = "8">وظيفة الأب	</option>
										<?php if ($powers <= 3) { ?><option value = "9">أضيف بواسطة	</option><?php } ?>
								    </select>
								</div>
							</div>
							<div class = "form-group row justify-content-md-center">
								<div class="col-md-6 custom-control custom-checkbox mr-sm-2 text-md-center">
							    	<input type="checkbox" name = "showall" value = "1" class="custom-control-input showAll" id="customControlAutosizing">
							    	<label class="custom-control-label" for="customControlAutosizing">إظهار الكل</label>
							    </div>
							</div>
							<!--End Student Code Field-->
						</form>
						<div class = "main-data ajax-val">
							<button class = "btn btn-secondary" data-toggle="modal" data-target="#print-options"><i class="fas fa-print"></i> طباعة</button>
							<div class = "table-responsive printData">
								<table class="main-table text-center table table-bordered">
									<thead class="thead-dark">
										<tr>
											<td colspan = "100%" class = "header-print" style="border: none !important;"></td>
										</tr>
										<tr>
											<td colspan = "100%" class = "header-print" style="border: none !important;">
												<p>محافظة الجيزة <br> إدارة الهرم التعليمية <br> مدرسة منشأة البكارى الثانوية (بنين)</p>
												<h1 class = "text-center">بيانات الطلاب</h1>
											</td>
										</tr>
										<tr>
											<th class = "no-print" scope="col">صحيفة الطالب</th>
											<th scope="col">كود الطالب</th>
											<th scope="col">الصورة</th>
											<th scope="col" style = "min-width: 175px;">الإسم</th>
											<th scope="col">السنة الدراسية</th>
											<th scope="col">الفصل</th>
											<th scope="col" style = "min-width: 100px;">اللغة الثانية</th>
											<th scope="col">الرقم القومى</th>
											<th scope="col" style = "min-width: 190px; max-width: 300px;">العنوان</th>
											<th scope="col" style = "min-width: 150px;">تاريخ الميلاد</th>
											<th scope="col" style = "min-width: 90px;">وظيفة الأب</th>			
											<?php if ($powers <= 2) { ?><th class = "no-print" scope="col" style = "min-width: 175px;">أضيف بواسطة</th><?php } ?>
											<th class = "no-print" scope="col">أدوات التحكم</th> 
										</tr>
									</thead>
									<tbody>
										<?php

											foreach ($students as $student) {
												
										?>
												<tr>		
													<th scope="row" class = "no-print"><a href = "students-profiles.php?id=<?php echo $student['id']; ?>" class = "btn btn-info">اختر</a></th>
													<th scope="row"><?php echo $student["code"]; ?></th>
													<th scope="row">
														<?php
															echo "<img class = 'img-fluid' src = 'data\uploads\students_images\\"; 
																if (!empty($student["image"])) {

																	if (file_exists("data/uploads/students_images/" . $student['image'])) {

																		echo $student["image"];

																	} else {

																		echo "default.png";

																	}

																} else {

																	echo "default.png";
																	
																}
															echo "'>";
														?>												
													</th>
													<th scope="row"><?php echo $student["name"]; ?></th>
													<th scope="row"><?php echo $student["grade"]; ?></th>
													<th scope="row"><?php echo $student["class"]; ?></th>
													<th scope="row"><?php echo $student["second_language"]; ?></th>
													<th scope="row"><?php echo $student["national_id"]; ?></th>
													<th scope="row"><?php echo $student["address"]; ?></th>
													<th scope="row"><?php if ($student["birth_date"] != "0000-00-00" && $student["birth_date"] != "1970-01-01") { echo str_replace('-', ' / ', arabicNumbers(date('d-m-Y', strtotime($student["birth_date"])))); } ?></th>
													<th scope="row"><?php echo $student["father_job"]; ?></th>
													<?php if ($powers <= 4) { ?>
															<?php if ($powers <= 2) { ?><th class = "no-print" scope="row"><?php echo $student["who_added"]; ?></th><?php } ?>
															<th scope="row" class = "no-print">
																<?php if ($powers <= 2) { ?>
																	<a href="?do=Edit&id=<?php echo $student['id']; ?>" class="btn btn-sm btn-success"><i class="fas fa-edit"></i> تعديل</a> 
																	<a href="?do=Delete&id=<?php echo $student['id']; ?>" class="btn btn-sm btn-danger confirm" data-toggle="modal" data-target="#delete-confirm"><i class="fas fa-trash-alt"></i> حذف</a>
																<?php } ?>
																<div class="btn-group">
																  <button type="button" class="btn btn-sm btn-info dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
																    <i class="fas fa-cogs"></i> خيارات <?php if ($powers <= 2) { echo "أخرى"; } ?>
																  </button>
																  <div class="dropdown-menu text-right">
																    <a class="dropdown-item" href="?do=Phones&id=<?php echo $student['id']; ?>&currentPage=1">هواتف الطالب</a>
																    <?php if ($powers <= 3) { ?>	
																    	<a class="dropdown-item" href="?do=AddActivity&id=<?php echo $student['id']; ?>&category=Activities">إضافة نشاط</a>
																    	<a class="dropdown-item" href="?do=AddActivity&id=<?php echo $student['id']; ?>&category=Irregularities">إضافة مخالفة</a>
																    <?php } ?>
																    <div class="dropdown-divider"></div>
																    <a class="dropdown-item" href="?do=Activities&id=<?php echo $student['id']; ?>&category=Activities">أنشطة الطالب</a>
																    <a class="dropdown-item" href="?do=Activities&id=<?php echo $student['id']; ?>&category=Irregularities">مخالفات الطالب</a>
																    <?php if ($powers <= 2) { ?>
																    	<a class="dropdown-item" href="?do=StudentHistory&id=<?php echo $student['id']; ?>&currentPage=1">تاريخ التعديل</a>
																  	<?php } ?>
																  </div>
																</div>
															</th>
													<?php } ?>
												</tr>
										<?php

											}
										
										?>
									</tbody>
									<tfoot>
										<tr>
											<td colspan="100%" class = "footer-print" style="border: none !important;">
												<div class = "row text-center">
													<div class = "col-6 test">
														وكيل شئون طلاب
													</div>
													<div class = "col-6 test">
														مدير مدرسة
													</div>
												</div>
											</td>
										</tr>
									</tfoot>
								</table>
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
												<div class="modal-body text-right">هل أنت متأكد أنك تريد حذف هذا الطالب؟</div>
												<div class="modal-footer">
													<button type="submit" class="btn btn-primary delete-confirm-true">حذف</button>
													<button type="button" class="btn btn-secondary" data-dismiss="modal">إلغاء</button>
												</div>
											</div>
										</div>
									</div>
								</form>
								<!-- Modal -->
								<div class="modal fade" id="print-options" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
									<div class="modal-dialog modal-dialog-centered" role="document">
										<div class="modal-content">
											<div class="modal-header pl-0">
												<h5 class="modal-title" id="exampleModalLongTitle">طباعة</h5>
												<button type="button" class="close ml-0" data-dismiss="modal" aria-label="Close">
													<span aria-hidden="true">&times;</span>
												</button>
											</div>
											<div class="modal-body text-right">
												<!--Start Student Code Field-->
												<div class = "form-group row">
													<label class = "col-md-3 control-label">عنوان الصفحة</label>
													<div class = "col-md-9">
														<input type = "text" name = "header" class = "form-control" autocomplete = "off" placeholder = "عنوان الصفحة" />
													</div>
												</div>
												<!--End Student Code Field-->
												<!--Start Name Field-->
												<div class = "form-group row">
													<label class = "col-md-3 control-label">اسم الطالب</label>
													<div class = "col-md-9">
														<input type = "text" name = "name" class = "form-control" autocomplete = "off" placeholder = "اسم الطالب" />
													</div>
												</div>
												<!--End Name Field-->
											</div>
											<div class="modal-footer">
												<button class = "btn btn-primary print" data-toggle="modal" data-target="#print-options"><i class="fas fa-print"></i> طباعة</button>
												<button type="button" class="btn btn-secondary" data-dismiss="modal">إلغاء</button>
											</div>
										</div>
									</div>
								</div>
							</div>
							<?php if ($powers <= 2) { ?> <a href = "?do=Add"  class = "btn btn-primary"><i class="fas fa-plus"></i> إضافة طالب</a> <?php } ?>
							<footer class = "my-2">
								<?php

									// Call The Pagination Bar
							
									$paging_info = getPagination($paginationData);

								?>
							</footer>
						</div>
					</div>
				</div>

<?php

			} else {

				echo "<div class = 'alert alert-warning container'><i class='fas fa-exclamation-circle'></i> لا يوجد طلاب هل تريد <a href = '?do=Add'  class = 'badge badge-pill badge-primary'><i class='fas fa-plus'></i> إضافة طالب</a> جديد؟</div>";

			}

		} else if ($do === "Add") { // Add Page

			if ($powers <= 2) {

?>

				<h1 class = "text-center">إضافة طالب جديد</h1>
				<div class = "container text-right">
					<form class = "ajax-form" action = "?do=Insert" method = "POST" enctype = "multipart/form-data">
						<!--Start Student Code Field-->
						<div class = "form-group row justify-content-md-center">
							<label class = "col-md-2 control-label">الكود</label>
							<div class = "col-md-4">
								<input type = "number" name = "code" class = "form-control" required = "required" autocomplete = "off" placeholder = "كود الطالب" />
							</div>
						</div>
						<!--End Student Code Field-->
						<!--Start Name Field-->
						<div class = "form-group row justify-content-md-center">
							<label class = "col-md-2 control-label">اسم الطالب</label>
							<div class = "col-md-4">
								<input type = "text" name = "name" class = "form-control" required = "required" autocomplete = "off" placeholder = "اسم الطالب" />
							</div>
						</div>
						<!--End Name Field-->
						<!--Start Grade Field-->
						<div class = "form-group row justify-content-md-center">
							<label class = "col-md-2 control-label">السنة الدراسية</label>
							<div class = "col-md-4">
								<input type = "text" name = "grade" class = "form-control" required = "required" autocomplete = "off" placeholder = "السنة الدراسية" />
							</div>
						</div>
						<!--End Grade Field-->
						<!--Start Class Field-->
						<div class = "form-group row justify-content-md-center">
							<label class = "col-md-2 control-label">الفصل</label>
							<div class = "col-md-4">
								<input type = "text" name = "class" class = "form-control" required = "required" autocomplete = "off" placeholder = "الفصل" />
							</div>
						</div>
						<!--End Class Field-->
						<!--Start Second Language Field-->
						<div class = "form-group row justify-content-md-center">
							<label class = "col-md-2 control-label">اللغة الثانية</label>
							<div class = "col-md-4">
							 	<input type = "text" name = "secondLanguage" class = "form-control" autocomplete = "off" placeholder = "اللغة الثانية" />
							</div>
						</div>
						<!--End Second Language Field-->
						<!--Start National ID Field-->
						<div class = "form-group row justify-content-md-center">
							<label class = "col-md-2 control-label">الرقم القومى</label>
							<div class = "col-md-4">
								<input type = "text" name = "nationalid" class = "form-control" autocomplete = "off" placeholder = "الرقم القومى" />
							</div>
						</div>
						<!--End National ID Field-->
						<!--Start address Field-->
						<div class = "form-group row justify-content-md-center">
							<label class = "col-md-2 control-label">العنوان</label>
							<div class = "col-md-4">
								<input type = "text" name = "address" class = "form-control" autocomplete = "off" placeholder = "العنوان" />
							</div>
						</div>
						<!--End address Field-->
						<!--Start Birthdate Field-->
						<div class = "form-group row justify-content-md-center">
							<label class = "col-md-2 control-label">تاريخ الميلاد</label>
							<div class="start_date input-group col-md-4">
							    <div class="input-group-append">
							      <span class="far fa-calendar-alt input-group-text" aria-hidden="true "></span>
							    </div>
							    <input type="text" name = "birthdate" class="form-control date" autocomplete = "off" placeholder = "تاريخ الميلاد" >
							</div>
						</div>
						<!--End Birthdate Field-->
						<!--Start Father Job Field-->
						<div class = "form-group row justify-content-md-center">
							<label class = "col-md-2 control-label">وظيفة الأب</label>
							<div class = "col-md-4">
								<input type = "text" name = "fatherjob" class = "form-control" autocomplete = "off" placeholder = "وظيفة الأب" />
							</div>
						</div>
						<!--End Father Job Field-->
						<!--Start Image Field-->
						<div class = "form-group row justify-content-md-center">
							<label class = "col-md-2 control-label">صورة الطالب</label>
							<div class = "col-md-4">
								<div class="custom-file text-left">
									<input type="file" name = "image" class="custom-file-input" id="memberImage" accept="image/*" aria-describedby="inputGroupFileAddon03">
									<label class="custom-file-label" for="memberImage">اختر صورة الطالب</label>
								</div>
							</div>
						</div>
						<!--End Image Field-->
						<!--Start Submit Button-->
						<div class = "form-group row justify-content-md-center text-left">
							<div class = "col-md-6">
	              				<button type = "submit" class = "btn btn-primary"><i class="fas fa-plus"></i> إضافة الطالب</button>
							</div>
						</div>
						<!--End Submit Button-->
					</form>
					<div class = "ajax-val"></div>
				</div>

<?php

			} else {

				header("Location: students.php");

			}

		} else if ($do === "Insert") {	//	Insert Page

			if ($powers <= 2) {

				if ($_SERVER['REQUEST_METHOD'] === "POST") {

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

						$code = filter_var($_POST["code"], FILTER_SANITIZE_NUMBER_INT);
						$name = filter_var($_POST["name"], FILTER_SANITIZE_STRING);
						$grade = filter_var($_POST["grade"], FILTER_SANITIZE_STRING);
						$class = filter_var($_POST["class"], FILTER_SANITIZE_STRING);
						$secondLanguage = filter_var($_POST["secondLanguage"], FILTER_SANITIZE_STRING);
						$nationalid = filter_var($_POST["nationalid"], FILTER_SANITIZE_STRING);
						$address = filter_var($_POST["address"], FILTER_SANITIZE_STRING);
						$birthdate = filter_var(date('Y-m-d', strtotime($_POST["birthdate"])), FILTER_SANITIZE_STRING);
						$fatherjob = filter_var($_POST["fatherjob"], FILTER_SANITIZE_STRING);

						//	Validate The Form

						$formErrors = array();

						if (empty($code)) {

							$formErrors[] = "يجب ادخال  <strong>كود الطالب</strong>";

						}

						if (empty($name)) {

							$formErrors[] = "يجب ادخال <strong>اسم الطالب</strong>";

						}

						if (empty($grade)) {

							$formErrors[] = "يجب ادخال <strong>سنة الطالب الدراسية</strong>";

						}

						if (empty($class)) {

							$formErrors[] = "يجب ادخال <strong>فصل الطالب</strong>";

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

								$path = "data/uploads/students_images";

								move_uploaded_file($imageTmp, "$path/$image");

							} else {

								$image = "";

							}

							// Check If This User Exists Or Not

							$isExist = checkSelect("name", "students", "name = '" . $name . "'");

							if ($isExist === 0) {

								// Get Who Added

								$who_added = getSelect("full_name", "users", "username = '" . $_SESSION["AdminUsername"] . "'", 1, "ASC", false);

								// Insert Into Database With This Info

								$stmt = $con->prepare("INSERT INTO 
															students(code, name, grade, class, second_language, national_id, address, birth_date, father_job, image, who_added)
														VALUES 
															(:code, :name, :grade, :class, :second_language, :national_id, :address, :birth_date, :father_job, :image, :who_added)");

								$stmt->execute(array(

									"code"				=>	$code,
									"name"				=>	$name,
									"grade"				=>	$grade,
									"class"				=>	$class,
									"second_language"	=>	$secondLanguage,
									"national_id"		=>	$nationalid,
									"address"			=>	$address,
									"birth_date"		=>	$birthdate,
									"father_job"		=>	$fatherjob,
									"image"				=>	$image,
									"who_added"			=>	$who_added['full_name']

								));

								if ($stmt->rowCount() > 0) {

									// Get User ID

									$userid = getSelect("id", "users", "username = '" . $_SESSION["AdminUsername"] . "'", 1, "ASC", false);

									// Get Student ID

									$studentid = getSelect("id", "students", "name = '" . $name . "'", 1, "ASC", false);

									// Add To The Update History

									$stmt = $con->prepare("INSERT INTO 
																data_updates(category, action, date, updater_id, updated_id)
															VALUES 
																('students', 'add', '" . date("Y-m-d h:i:s") . "', ?, ?)");

									$stmt->execute(array($userid['id'], $studentid['id']));

								}
								
								// Echo Success Message

								echo "<div class = 'alert alert-success'>تم إضافة الطالب بنجاح</div>";

							} else {

								echo "<div class = 'alert alert-danger'>هذا الطالب موجود بالفعل</div>";

							}
							
						}

					echo "</div>";

				} else {

					redirect("<div class = 'alert alert-danger'>لا يمكنك تصفح هذه الصفحة مباشرةً</div>");

				}

			} else {

				header("Location: students.php");

			}

		} else if ($do === "Edit") {	//	Edit Page 

			if ($powers <= 2) {

				// Check If Get Request Student ID Is Numeric & Get The Integer Value Of It

				$id = isset($_GET["id"]) && is_numeric(filter_var($_GET["id"], FILTER_SANITIZE_NUMBER_INT)) ? intval($_GET["id"]) : 0;

				// The Row Count

				$count = checkSelect("*", "students", "id = " . $id);

				// If There Is Such ID Show The Form

				if ($count > 0) {

					$studentData = getSelect("*", "students", "id = " . $id, 1, "ASC", false);

?>

					<h1 class = "text-center">تعديل بيانات الطالب</h1>
					<?php

						if ($studentData['image'] != "") {

							if (file_exists("data/uploads/students_images/" . $studentData['image'])) {

								echo '<img src="data\uploads\students_images\\' . $studentData['image'] . '" class="img-fluid img-thumbnail rounded-circle mx-auto d-block mb-4" style = "width: 150; height: 170;">';

							}
							
						}

					?>
					<div class = "container text-right">
						<form class = "ajax-form" action = "?do=Update" method = "POST" enctype = "multipart/form-data">
							<input type = "hidden" name = "id" value = "<?php echo $id; ?>" />
							<!--Start Student Code Field-->
							<div class = "form-group row justify-content-md-center">
								<label class = "col-md-2 control-label">الكود</label>
								<div class = "col-md-4">
									<input type = "number" name = "code" class = "form-control" value = "<?php echo $studentData['code']; ?>" required = "required" autocomplete = "off" placeholder = "كود الطالب" />
								</div>
							</div>
							<!--End Student Code Field-->
							<!--Start Name Field-->
							<div class = "form-group row justify-content-md-center">
								<label class = "col-md-2 control-label">اسم الطالب</label>
								<div class = "col-md-4">
									<input type = "text" name = "name" class = "form-control" value = "<?php echo $studentData['name']; ?>" required = "required" autocomplete = "off" placeholder = "اسم الطالب" />
								</div>
							</div>
							<!--End Name Field-->
							<!--Start Grade Field-->
							<div class = "form-group row justify-content-md-center">
								<label class = "col-md-2 control-label">السنة الدراسية</label>
								<div class = "col-md-4">
									<input type = "text" name = "grade" class = "form-control" value = "<?php echo $studentData['grade']; ?>" required = "required" autocomplete = "off" placeholder = "السنة الدراسية" />
								</div>
							</div>
							<!--End Grade Field-->
							<!--Start Class Field-->
							<div class = "form-group row justify-content-md-center">
								<label class = "col-md-2 control-label">الفصل</label>
								<div class = "col-md-4">
									<input type = "text" name = "class" class = "form-control" value = "<?php echo $studentData['class']; ?>" required = "required" autocomplete = "off" placeholder = "الفصل" />
								</div>
							</div>
							<!--End Class Field-->
							<!--Start Second Language Field-->
							<div class = "form-group row justify-content-md-center">
								<label class = "col-md-2 control-label">اللغة الثانية</label>
								<div class = "col-md-4">
								 	<input type = "text" name = "secondLanguage" class = "form-control" value = "<?php echo $studentData['second_language']; ?>" autocomplete = "off" placeholder = "اللغة الثانية" />
								</div>
							</div>
							<!--End Second Language Field-->
							<!--Start National ID Field-->
							<div class = "form-group row justify-content-md-center">
								<label class = "col-md-2 control-label">الرقم القومى</label>
								<div class = "col-md-4">
									<input type = "text" name = "nationalid" class = "form-control" value = "<?php echo $studentData['national_id']; ?>" autocomplete = "off" placeholder = "الرقم القومى" />
								</div>
							</div>
							<!--End National ID Field-->
							<!--Start address Field-->
							<div class = "form-group row justify-content-md-center">
								<label class = "col-md-2 control-label">العنوان</label>
								<div class = "col-md-4">
									<input type = "text" name = "address" class = "form-control" value = "<?php echo $studentData['address']; ?>" autocomplete = "off" placeholder = "العنوان" />
								</div>
							</div>
							<!--End address Field-->
							<!--Start Birthday Field-->
							<div class = "form-group row justify-content-md-center">
								<label class = "col-md-2 control-label">تاريخ الميلاد</label>
								<div class="start_date input-group col-md-4">
								    <div class="input-group-append">
								      <span class="far fa-calendar-alt input-group-text" aria-hidden="true "></span>
								    </div>
								    <input type="text" name = "birthdate" class="form-control date" value = "<?php echo date('d-m-Y', strtotime($studentData['birth_date'])); ?>" autocomplete = "off" placeholder = "تاريخ الميلاد" >
								</div>
							</div>
							<!--End Birthday Field-->
							<!--Start Father Job Field-->
							<div class = "form-group row justify-content-md-center">
								<label class = "col-md-2 control-label">وظيفة الأب</label>
								<div class = "col-md-4">
									<input type = "text" name = "fatherjob" class = "form-control" value = "<?php echo $studentData['father_job']; ?>" autocomplete = "off" placeholder = "وظيفة الأب" />
								</div>
							</div>
							<!--End Father Job Field-->
							<!--Start Image Field-->
							<div class = "form-group row justify-content-md-center">
								<label class = "col-md-2 control-label">صورة الطالب</label>
								<div class = "col-md-4">
									<div class="custom-file text-left">
										<input type="file" name = "image" class="custom-file-input" id="memberImage" accept="image/*" aria-describedby="inputGroupFileAddon03" value = "<?php echo $studentData['image']; ?>">
										<label class="custom-file-label" for="memberImage">
											<?php
												if ($studentData['image'] == "") {

													echo "لا يوجد صورة للطالب الرجاء قم بإختيار صورة له";

												} else {

													echo $studentData['image'];

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
		              				<button type = "submit" class = "btn btn-primary"><i class="fas fa-edit"></i> تعديل بيانات الطالب</button>
								</div>
							</div>
							<!--End Submit Button-->
						</form>
						<div class = "ajax-val"></div>
					</div>

<?php

				// If There Is No Such ID Show Error Message

				} else {

					redirect("<div class = 'alert alert-danger'>هذا الطالب غير موجود</div>", 3, "students.php", "صفحة بيانات الطلاب");

				}

			} else {

				header("Location: students.php");

			}

		} else if ($do === "Update") {	//	Update Page

			if ($powers <= 2) {

				if ($_SERVER['REQUEST_METHOD'] === "POST") {

					// Check If Post Request Student ID Is Numeric & Get The Integer Value Of It

					$id = isset($_POST["id"]) && is_numeric(filter_var($_POST["id"], FILTER_SANITIZE_NUMBER_INT)) ? intval($_POST["id"]) : 0;

					// The Row Count

					$count = checkSelect("*", "students", "id = " . $id);

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

							$code = filter_var($_POST["code"], FILTER_SANITIZE_NUMBER_INT);
							$name = filter_var($_POST["name"], FILTER_SANITIZE_STRING);
							$grade = filter_var($_POST["grade"], FILTER_SANITIZE_STRING);
							$class = filter_var($_POST["class"], FILTER_SANITIZE_STRING);
							$secondLanguage = filter_var($_POST["secondLanguage"], FILTER_SANITIZE_STRING);
							$nationalid = filter_var($_POST["nationalid"], FILTER_SANITIZE_STRING);
							$address = filter_var($_POST["address"], FILTER_SANITIZE_STRING);
							$birthdate = filter_var(date('Y-m-d', strtotime($_POST["birthdate"])), FILTER_SANITIZE_STRING);
							$fatherjob = filter_var($_POST["fatherjob"], FILTER_SANITIZE_STRING);

							//	Validate The Form

							$formErrors = array();

							if (empty($code)) {

								$formErrors[] = "يجب ادخال  <strong>كود الطالب</strong>";

							}

							if (empty($name)) {

								$formErrors[] = "يجب ادخال <strong>اسم الطالب</strong>";

							}

							if (empty($grade)) {

								$formErrors[] = "يجب ادخال <strong>سنة الطالب الدراسية</strong>";

							}

							if (empty($class)) {

								$formErrors[] = "يجب ادخال <strong>فصل الطالب</strong>";

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

									move_uploaded_file($imageTmp, "data\uploads\students_images\\" . $image);

								} else {

									$image = "";

								}

								// Check If This User Exists Or Not

								$isExist = checkSelect("name", "students", "name = '" . $name . "' AND id != " . $id);

								if ($isExist === 0) {

									// Get Student Image Name

									$getImage = getSelect("image", "students", "id = " . $id, 1, "ASC", false);

									$getImage = $getImage['image'];

									// Get The Student Image

									$studentImage = getSelect("*", "students", "id = " . $id, 1, "ASC", false);

									// Update The Database With This Info

									if ($image == "") {

										$stmt = $con->prepare("UPDATE students SET code = ?, name = ?, grade = ?, class = ?, second_language = ?, national_id = ?, address = ?, birth_date = ?, father_job = ? WHERE id = ?");
									
										$stmt->execute(array($code, $name, $grade, $class, $secondLanguage, $nationalid, $address, $birthdate, $fatherjob, $id));

									} else {

										$stmt = $con->prepare("UPDATE students SET code = ?, name = ?, grade = ?, class = ?, second_language = ?, national_id = ?, address = ?, birth_date = ?, father_job = ?, image = ? WHERE id = ?");
									
										$stmt->execute(array($code, $name, $grade, $class, $secondLanguage, $nationalid, $address, $birthdate, $fatherjob, $image, $id));

									}
									
									if ($stmt->rowCount() > 0) {

										if ($image != "") {

											if (! empty($getImage)) {

												$path = "data/uploads/students_images";

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
																	('students', 'update', '" . date("Y-m-d h:i:s") . "', ?, ?)");

										$stmt->execute(array($userid['id'], $id));

									}

									// Echo Success Message

									echo "<div class = 'alert alert-success'>تم تعديل بيانات الطالب بنجاح</div>";

								} else {

									echo "<div class = 'alert alert-danger'>هذا الطالب موجود بالفعل</div>";

								}
								
							}

						echo "</div>";

					// If There Is No Such ID Show Error Message

					} else {

						echo "<div class = 'alert alert-danger'>هذا الطالب غير موجود</div>";

					}

				} else {

					redirect("<div class = 'alert alert-danger'>لا يمكنك تصفح هذه الصفحة مباشرةً</div>");

				}

			} else {

				header("Location: students.php");

			}

		} else if ($do === "Delete") {	// Delete Page

			if ($powers <= 2) {

				echo "<h1 class = 'text-center'>حذف الطالب</h1>";
				echo "<div class = 'container text-right'>";

					// Check If Get Request studentid Is Numeric & Get The Integer Value Of It

					$studentid = isset($_GET["id"]) && is_numeric($_GET["id"]) ? intval($_GET["id"]) : 0;

					// Check If This Student Exists Or Not

					$isExist = checkSelect("id", "students", "id = " . $studentid);

					// If There Is Such ID Show The Form

					if ($isExist > 0) { 

						// Get Student Image Name

						$image = getSelect("image", "students", "id = " . $studentid, 1, "ASC", false);

						$image = $image['image'];

						// Delete From The Update History

						$stmt = $con->prepare("DELETE FROM 
													data_updates
												WHERE 
													updated_id = ? AND category = 'students'");

						$stmt->execute(array($studentid));

						$statements = getSelect("id", "students_phones", "student_id = " . $studentid);

						foreach ($statements as $statement) {
							
							$stmt = $con->prepare("DELETE FROM 
														data_updates
													WHERE 
														updated_id = ? AND category = 'students_phones'");

							$stmt->execute(array($statement["id"]));

						}

						$statements = getSelect("id", "students_activities", "student_id = " . $studentid);

						foreach ($statements as $statement) {
							
							$stmt = $con->prepare("DELETE FROM 
														data_updates
													WHERE 
														updated_id = ? AND category IN ('students_activities', 'students_irregularities')");

							$stmt->execute(array($statement["id"]));

						}

						// Delete Student

						$stmt = $con->prepare("DELETE FROM students WHERE id = :id");

						$stmt->bindParam(":id", $studentid);

						$stmt->execute();

						if ($stmt->rowCount() > 0) {

							if (! empty($image)) {

								$path = "data/uploads/students_images";

								if (file_exists("$path/$image")) {

									unlink("$path/$image");

								}

							}

						}

						redirect("<div class = 'alert alert-success'>تم حذف الطالب بنجاح</div>", 3, "students.php", "صفحة بيانات الطلاب");

					} else {

						// If There Is No Such ID Show Error Message

						redirect("<div class = 'alert alert-danger'>هذا الطالب غير موجود</div>");

					}

				echo "</div>";

			} else {

				header("Location: students.php");

			}

		} else if ($do === "StudentHistory") {	// Student History Page

			if ($powers <= 2) {

				if (!isset($_GET['currentPage'])) {

					$currentPage = 1;

				} else {

					$currentPage = $_GET['currentPage'];

				}

				// Check If Get Request studentid Is Numeric & Get The Integer Value Of It

				$studentid = isset($_GET["id"]) && is_numeric($_GET["id"]) ? intval($_GET["id"]) : 0;

				// Check If This Student Exists Or Not

				$isExist = checkSelect("id", "students", "id = " . $studentid);

				// If There Is Such ID Show The Form

				if ($isExist != 0) {

					// Check If This Student Exists Or Not

					$isExist = checkSelect("updated_id, category", "data_updates", "updated_id = " . $studentid . " AND category = 'students'");

					// If There Is Such ID Show The Form

					if ($isExist != 0) {

						$countStudents = checkSelect("*", "data_updates", "updated_id = " . $studentid . " AND category = 'students'");

						$currentPage = filter_var($currentPage, FILTER_SANITIZE_NUMBER_INT);

						$maxItems = 10;

						if (! filter_var($currentPage, FILTER_VALIDATE_INT) || $currentPage > ceil($countStudents / $maxItems)) {

							$currentPage = 1;

						}

						$paginationData = setPagination($countStudents, $maxItems, $currentPage);

						$histories = getSelect("*", "data_updates", "updated_id = " . $studentid . " AND category = 'students'", "date", "DESC LIMIT " . $paginationData['srow'] . " , " . $maxItems);

?>
						<div class = "container text-right">
							<h1 class = "text-center">تاريخ الطالب</h1>				
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
												<p>تم <?php echo lang($history['action']); ?> بيانات هذا الطالب بواسطة <?php echo getWord($updaterName['full_name'], 3); ?></p>
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

						echo "<div class = 'alert alert-warning container'><i class='fas fa-exclamation-circle'></i> لا يوجد تاريخ لهذا الطالب</div>";

					}

				} else {

					redirect("<div class = 'alert alert-danger'>هذا الطالب غير موجود</div>", 3, "students.php", "صفحة بيانات الطلاب");

				}

			} else {

				header("Location: students.php");

			}

		} else if ($do === "StudentsSearch") { // Students Search Page

			$search = filter_var($_POST['search'], FILTER_SANITIZE_STRING);
			$searchtype = isset($_POST["searchtype"]) && is_numeric(filter_var($_POST["searchtype"], FILTER_SANITIZE_NUMBER_INT)) ? intval($_POST["searchtype"]) : 1;
			if (isset($_POST['showall'])) {
				$showall = filter_var($_POST['showall'], FILTER_SANITIZE_NUMBER_INT);
			}

			if (!isset($_GET['currentPage'])) {

				$currentPage = 1;

			} else {

				$currentPage = $_GET['currentPage'];

			}

			$searchColumn = "";

			if ($searchtype == 0) {

				$searchColumn = "code";

			} else if ($searchtype == 1) {

				$searchColumn = "name";
				
			} else if ($searchtype == 2) {

				$searchColumn = "grade";
				
			} else if ($searchtype == 3) {

				$searchColumn = "class";

			} else if ($searchtype == 4) {

				$searchColumn = "second_language";

			} else if ($searchtype == 5) {

				$searchColumn = "national_id";

			} else if ($searchtype == 6) {

				$searchColumn = "address";

			} else if ($searchtype == 7) {

				$searchColumn = "birth_date";

				$search = str_replace("/", "-", $search);

			} else if ($searchtype == 8) {

				$searchColumn = "father_job";

			} else if ($searchtype == 9) {

				if ($powers <= 3) {

					$searchColumn = "who_added";

				} else {

					$searchColumn = "name";

				}

			} else {

				$searchColumn = "name";

			}

			if (!empty($search) || $search != "") {

				$countStudents = checkSelect("*", "students", $searchColumn . " LIKE '%" . $search . "%'");

			} else {

				$countStudents = checkSelect("*", "students");

			}

			if (empty($showall)) {

				$currentPage = filter_var($currentPage, FILTER_SANITIZE_NUMBER_INT);

				$maxItems = 10;

				if (! filter_var($currentPage, FILTER_VALIDATE_INT) || $currentPage > ceil($countStudents / $maxItems)) {

					$currentPage = 1;

				}

				$paginationData = setPagination($countStudents, $maxItems, $currentPage);

			}

			if (!empty($search) || $search != "") {

				if (empty($showall)) {

					$students = getSelect("*", "students", $searchColumn . " LIKE '%" . $search . "%'", 1, "ASC LIMIT " . $paginationData['srow'] . " , " . $maxItems);

				} else {

					$students = getSelect("*", "students", $searchColumn . " LIKE '%" . $search . "%'");

				}

			} else {

				if (empty($showall)) {

					$students = getSelect("*", "students", 1, "grade, class, name", "ASC LIMIT " . $paginationData['srow'] . " , " . $maxItems);

				} else {

					$students = getSelect("*", "students", 1, "grade, class, name");

				}

			}

			if (! empty($students)) {

?>

				<button class = "btn btn-secondary print"><i class="fas fa-print"></i> طباعة</button>
				<div class = "table-responsive printData">
					<table class="main-table text-center table table-bordered">
						<thead class="thead-dark">
							<tr>
								<td colspan = "100%" class = "header-print" style="border: none !important;">
									<p>محافظة الجيزة <br> إدارة الهرم التعليمية <br> مدرسة منشأة البكارى الثانوية (بنين)</p>
									<h1 class = "text-center">بيانات الطلاب</h1>
								</td>
							</tr>
							<tr>
								<th class = "no-print" scope="col">صحيفة الطالب</th>
								<th scope="col">كود الطالب</th>
								<th scope="col">الصورة</th>
								<th scope="col" style = "min-width: 175px;">الإسم</th>
								<th scope="col">السنة الدراسية</th>
								<th scope="col">الفصل</th>
								<th scope="col" style = "min-width: 100px;">اللغة الثانية</th>
								<th scope="col">الرقم القومى</th>
								<th scope="col" style = "min-width: 190px; max-width: 300px;">العنوان</th>
								<th scope="col" style = "min-width: 150px;">تاريخ الميلاد</th>
								<th scope="col" style = "min-width: 90px;">وظيفة الأب</th>			
								<?php if ($powers <= 3) { ?>
									<th class = "no-print" scope="col" style = "min-width: 175px;">أضيف بواسطة</th>
									<th class = "no-print" scope="col">أدوات التحكم</th> 
								<?php } ?>
							</tr>
						</thead>
						<tbody>
							<?php

								foreach ($students as $student) {
									
							?>

									<tr>
										<th scope="row" class = "no-print"><a href = "students-profiles.php?id=<?php echo $student['id']; ?>" class = "btn btn-info">اختر</a></th>
										<th scope="row"><?php echo $student["code"]; ?></th>
										<th scope="row">
											<?php
												echo "<img class = 'img-fluid' src = 'data\uploads\students_images\\"; 
													if (!empty($student["image"])) {

														echo $student["image"];

													} else {

														echo "default.png";
														
													}
												echo "'>";
											?>												
										</th>
										<th scope="row"><?php echo $student["name"]; ?></th>
										<th scope="row"><?php echo $student["grade"]; ?></th>
										<th scope="row"><?php echo $student["class"]; ?></th>
										<th scope="row"><?php echo $student["second_language"]; ?></th>
										<th scope="row"><?php echo $student["national_id"]; ?></th>
										<th scope="row"><?php echo $student["address"]; ?></th>
										<th scope="row"><?php if ($student["birth_date"] != "0000-00-00" && $student["birth_date"] != "1970-01-01") { echo str_replace('-', ' / ', arabicNumbers(date('d-m-Y', strtotime($student["birth_date"])))); } ?></th>
										<th scope="row"><?php echo $student["father_job"]; ?></th>
										<?php if ($powers <= 3) { ?>
												<th scope="row" class = "no-print"><?php echo $student["who_added"]; ?></th>
												<th scope="row" class = "no-print">
													<?php if ($powers <= 2) { ?>
														<a href="?do=Edit&id=<?php echo $student['id']; ?>" class="btn btn-sm btn-success"><i class="fas fa-edit"></i> تعديل</a> 
														<a href="?do=Delete&id=<?php echo $student['id']; ?>" class="btn btn-sm btn-danger confirm" data-toggle="modal" data-target="#delete-confirm"><i class="fas fa-trash-alt"></i> حذف</a>
													<?php } ?>
													<div class="btn-group">
													  <button type="button" class="btn btn-sm btn-info dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
													    <i class="fas fa-cogs"></i> خيارات <?php if ($powers <= 2) { echo "أخرى"; } ?>
													  </button>
													  <div class="dropdown-menu text-right">
													    <a class="dropdown-item" href="?do=Phones&id=<?php echo $student['id']; ?>&currentPage=1">هواتف الطالب</a>
													    <?php if ($powers <= 3) { ?>	
													    	<a class="dropdown-item" href="?do=AddActivity&id=<?php echo $student['id']; ?>&category=Activities">إضافة نشاط</a>
													    	<a class="dropdown-item" href="?do=AddActivity&id=<?php echo $student['id']; ?>&category=Irregularities">إضافة مخالفة</a>
													    <?php } ?>
													    <div class="dropdown-divider"></div>
													    <a class="dropdown-item" href="?do=Activities&id=<?php echo $student['id']; ?>&category=Activities">أنشطة الطالب</a>
													    <a class="dropdown-item" href="?do=Activities&id=<?php echo $student['id']; ?>&category=Irregularities">مخالفات الطالب</a>
													    <?php if ($powers <= 2) { ?>
													    	<a class="dropdown-item" href="?do=StudentHistory&id=<?php echo $student['id']; ?>&currentPage=1">تاريخ التعديل</a>
													  	<?php } ?>
													  </div>
													</div>
												</th>
										<?php } ?>
									</tr>

							<?php

								}
							
							?>
						</tbody>
					</table>
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
									<div class="modal-body text-right">هل أنت متأكد أنك تريد حذف هذا الطالب؟</div>
									<div class="modal-footer">
										<button type="submit" class="btn btn-primary delete-confirm-true">حذف</button>
										<button type="button" class="btn btn-secondary" data-dismiss="modal">إلغاء</button>
									</div>
								</div>
							</div>
						</div>
					</form>
				</div>
				<a href = "?do=Add"  class = "btn btn-primary"><i class="fas fa-plus"></i> إضافة طالب</a>
				<footer class = "my-2 searchFooter">
					<?php

						if (empty($showall)) {

							// Call The Pagination Bar
					
							$paging_info = getPagination($paginationData);

						}

					?>
				</footer>

<?php

			} else {

				echo "<div class = 'alert alert-warning container'><i class='fas fa-exclamation-circle'></i> لا يوجد طلاب بهذه المواصفات هل تريد <a href = '?do=Add'  class = 'badge badge-pill badge-primary'><i class='fas fa-plus'></i> إضافة طالب</a> جديد؟</div>";

			}

		} else if ($do === "Phones") { // Add Page

			echo "<div class = 'container text-right'>";

				// Check If Get Request studentid Is Numeric & Get The Integer Value Of It

				$studentid = isset($_GET["id"]) && is_numeric($_GET["id"]) ? intval($_GET["id"]) : 0;

				// Check If This Student Exists Or Not

				$isExist = checkSelect("id", "students", "id = " . $studentid);

				// If There Is Such ID Show The Form

				if ($isExist > 0) { 

					if (!isset($_GET['currentPage'])) {

						$currentPage = 1;

					} else {

						$currentPage = $_GET['currentPage'];

					}

					$countStudents = checkSelect("*", "students_phones", "student_id = " . $studentid);

					$currentPage = filter_var($currentPage, FILTER_SANITIZE_NUMBER_INT);

					$maxItems = 5;

					if (! filter_var($currentPage, FILTER_VALIDATE_INT) || $currentPage > ceil($countStudents / $maxItems)) {

						$currentPage = 1;

					}

					$paginationData = setPagination($countStudents, $maxItems, $currentPage);

					$phones = getSelect("*", "students_phones", "student_id = " . $studentid, 1, "ASC LIMIT " . $paginationData['srow'] . " , " . $maxItems);

					if (! empty($phones)) {

?>

						<div class = "main">
							<div class = "container text-right">
								<h1 class = "text-center">أرقام الطالب</h1>
								<form class = "SearchForm ajax-form" action = "?do=PhonesSearch" method = "POST" enctype = "multipart/form-data">
									<!--Start Student Code Field-->
									<div class = "form-group row justify-content-md-center">
										<div class = "col-md-4 pl-md-0 search">
											<input type = "hidden" name = "studentid" value = "<?php echo $studentid; ?>" />
											<input type = "search" name = "search" placeholder = "بحث"  class = "form-control rounded-right ajax-type" autocomplete = "off" />
											<button type = "submit" class = "btn bg-transparent p-0 position-absolute"><i class = "fas fa-search"></i></button>
										</div>
										<div class = "col-md-2 pr-md-0">
										    <select class="form-control rounded-left ajax-select" id="selectstatus" name = "searchtype" data-size="5">
												<option value = "0" selected>الرقم</option>
												<?php if ($powers <= 2) { ?><option value = "1">أضيف بواسطة</option><?php } ?>
										    </select>
										</div>
									</div>
									<!--End Student Code Field-->
								</form>
								<div class = "main-data ajax-val">
									<button class = "btn btn-secondary print"><i class="fas fa-print"></i> طباعة</button>
									<div class = "table-responsive printData">
										<table class="main-table text-center table table-bordered">
											<thead class="thead-dark">
												<tr>
													<td colspan = "100%" class = "header-print" style="border: none !important;">
														<p>محافظة الجيزة <br> إدارة الهرم التعليمية <br> مدرسة منشأة البكارى الثانوية (بنين)</p>
														<h1 class = "text-center">أرقام الطالب</h1>
													</td>
												</tr>
												<tr>
													<th scope="col">الرقم</th>
													<?php if ($powers <= 2) { ?>
														<th class = "no-print" scope="col">أضيف بواسطة</th>
														<th class = "no-print" scope="col">أدوات التحكم</th>
													<?php } ?>
												</tr>
											</thead>
											<tbody>
												<?php

													foreach ($phones as $phone) {
														
												?>

														<tr>
															<th scope="row"><?php echo $phone["phone"]; ?></th>
															<?php if ($powers <= 2) { ?>
																<th class = "no-print" scope="row"><?php echo $phone["who_added"]; ?></th>
																<th class = "no-print" scope="row">
																		<a href="?do=EditPhone&id=<?php echo $phone['id']; ?>" class="btn btn-sm btn-success"><i class="fas fa-edit"></i> تعديل</a> 
																		<a href="?do=DeletePhone&id=<?php echo $phone['id']; ?>&studentid=<?php echo $studentid; ?>" class="btn btn-sm btn-danger confirm" data-toggle="modal" data-target="#delete-confirm"><i class="fas fa-trash-alt"></i> حذف</a>
																	<div class="btn-group">
																		<button type="button" class="btn btn-sm btn-info dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
																	    	<i class="fas fa-cogs"></i> خيارات أخرى
																	  	</button>
																		<div class="dropdown-menu text-right">
																		    <a class="dropdown-item" href="?do=PhoneHistory&id=<?php echo $phone['id']; ?>&currentPage=1">تاريخ التعديل</a>
																		</div>
																	</div>
																</th>
															<?php } ?>
														</tr>

												<?php

													}
												
												?>
											</tbody>
										</table>
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
														<div class="modal-body text-right">هل أنت متأكد أنك تريد حذف هذا الهاتف</div>
														<div class="modal-footer">
															<button type="submit" class="btn btn-primary delete-confirm-true">حذف</button>
															<button type="button" class="btn btn-secondary" data-dismiss="modal">إلغاء</button>
														</div>
													</div>
												</div>
											</div>
										</form>
									</div>
									<?php if ($powers <= 2) { ?><a href = "?do=AddPhone&id=<?php echo $studentid; ?>"  class = "btn btn-primary"><i class="fas fa-plus"></i> إضافة رقم</a><?php } ?>
									<footer class = "my-2">
										<?php

											// Call The Pagination Bar
									
											$paging_info = getPagination($paginationData);

										?>
									</footer>
								</div>
							</div>
						</div>

<?php

					} else {

						echo "<div class = 'alert alert-warning container'><i class='fas fa-exclamation-circle'></i> لا يوجد هواتف لهذا الطالب هل تريد <a href = '?do=AddPhone&id=" . $studentid . "'  class = 'badge badge-pill badge-primary'><i class='fas fa-plus'></i> إضافة هاتف</a> جديد لهذا الطالب؟</div>";

					}

				echo "</div>";

			} else {

				header("Location: students.php");

			}

		} else if ($do === "AddPhone") {	// Add Phone Page

			if ($powers <= 2) {

				// Check If Get Request studentid Is Numeric & Get The Integer Value Of It

				$studentid = isset($_GET["id"]) && is_numeric($_GET["id"]) ? intval($_GET["id"]) : 0;

?>

				<h1 class = "text-center">إضافة هاتف جديد</h1>
				<div class = "container text-right">
					<form class = "ajax-form" action = "?do=InsertPhone" method = "POST" enctype = "multipart/form-data">
						<input type = "hidden" name = "id" value = "<?php echo $studentid; ?>" />
						<!--Start Student Phone Field-->
						<div class = "form-group row justify-content-md-center">
							<label class = "col-md-2 control-label">رقم الهاتف</label>
							<div class = "col-md-4">
								<input type = "number" name = "phone" class = "form-control" required = "required" autocomplete = "off" placeholder = "رقم هاتف الطالب" />
							</div>
						</div>
						<!--End Student Phone Field-->
						<!--Start Submit Button-->
						<div class = "form-group row justify-content-md-center text-left">
							<div class = "col-md-6">
	              				<button type = "submit" class = "btn btn-primary"><i class="fas fa-plus"></i> إضافة الطالب</button>
							</div>
						</div>
						<!--End Submit Button-->
					</form>
					<div class = "ajax-val"></div>
				</div>

<?php
	
			} else {

				header("Location: students.php");

			}

		} else if ($do === "InsertPhone") {	// Insert Phone Page

			if ($powers <= 2) {

				if ($_SERVER['REQUEST_METHOD'] === "POST") {

					echo "<div class = 'container text-right'>";

						// Get Variables From  The Form

						$id = filter_var($_POST["id"], FILTER_SANITIZE_NUMBER_INT);
						$phone = filter_var($_POST["phone"], FILTER_SANITIZE_NUMBER_INT);

						//	Validate The Form

						$formErrors = array();

						if (empty($id)) {

							$formErrors[] = "كود الطالب البرمجى لا يمكن أن يكون <strong>فارغ</strong>";

						}

						if (empty($phone)) {

							$formErrors[] = "هاتف الطالب لا يمكن أن يكون <strong>فارغ</strong>";

						}

						if (strlen($phone) != 11) {

							$formErrors[] = "هاتف الطالب يجب أن يكون <strong>14 رقم</strong>";

						}

						// Loop Into Error Array And Echo It

						foreach ($formErrors as $error) {

							echo "<div class = 'alert alert-danger'>" . $error . "</div>";

						}

						// Check If There's No Error Proceed The Update Operation

						if (empty($formErrors)) {

							// Check If This Student Exists Or Not

							$isExist = checkSelect("*", "students", "id = " . $id);

							if ($isExist != 0) {

								// Check If This Phone Exists Or Not

								$isPhoneExist = checkSelect("*", "students_phones", "phone = '" . $phone . "' AND student_id = " . $id);

								if ($isPhoneExist === 0) {

									// Get Who Added

									$who_added = getSelect("full_name", "users", "username = '" . $_SESSION["AdminUsername"] . "'", 1, "ASC", false);

									// Insert Into Database With This Info

									$stmt = $con->prepare("INSERT INTO 
																students_phones(phone, who_added, student_id)
															VALUES 
																(:phone, :who_added, :student_id)");

									$stmt->execute(array(

										"phone"				=>	$phone,
										"who_added"			=>	$who_added['full_name'],
										"student_id"		=>	$id

									));

									if ($stmt->rowCount() > 0) {

										// Get User ID

										$userid = getSelect("id", "users", "username = '" . $_SESSION["AdminUsername"] . "'", 1, "ASC", false);

										// Get Phone ID

										$phoneid = getSelect("id", "students_phones", "phone = '" . $phone . "' AND student_id = '" . $id . "'", 1, "ASC", false);

										// Add To The Update History

										$stmt = $con->prepare("INSERT INTO 
																	data_updates(category, action, date, updater_id, updated_id)
																VALUES 
																	('students_phones', 'add', '" . date("Y-m-d h:i:s") . "', ?, ?)");

										$stmt->execute(array($userid['id'], $phoneid['id']));

									}
									
									// Echo Success Message

									echo "<div class = 'alert alert-success'>تم إضافة هاتف الطالب بنجاح</div>";

								} else {

									echo "<div class = 'alert alert-warning'>تم إضافة هذا الهاتف من قبل لهذا الطالب</div>";

								}

							} else {

								echo "<div class = 'alert alert-danger'>هذا الطالب غير موجود</div>";

							}
							
						}

					echo "</div>";

				} else {

					redirect("<div class = 'alert alert-danger'>لا يمكنك تصفح هذه الصفحة مباشرةً</div>");

				}

			} else {

				header("Location: students.php");

			}

		} else if ($do === "EditPhone") {	//	Edit Page 

			if ($powers <= 2) {

				// Check If Get Request Phone ID Is Numeric & Get The Integer Value Of It

				$id = isset($_GET["id"]) && is_numeric(filter_var($_GET["id"], FILTER_SANITIZE_NUMBER_INT)) ? intval($_GET["id"]) : 0;

				// The Row Count

				$count = checkSelect("*", "students_phones", "id = " . $id);

				// If There Is Such ID Show The Form

				if ($count > 0) {

					$phone = getSelect("*", "students_phones", "id = " . $id, 1, "ASC", false);

?>

					<h1 class = "text-center">تعديل هاتف الطالب</h1>
					<div class = "container text-right">
						<form class = "ajax-form" action = "?do=UpdatePhone" method = "POST" enctype = "multipart/form-data">
							<input type = "hidden" name = "id" value = "<?php echo $phone['id']; ?>" />
							<input type = "hidden" name = "studentid" value = "<?php echo $phone['student_id']; ?>" />
							<!--Start Student Phone Field-->
							<div class = "form-group row justify-content-md-center">
								<label class = "col-md-2 control-label">رقم الهاتف</label>
								<div class = "col-md-4">
									<input type = "number" name = "phone" value = "<?php echo $phone['phone']; ?>" class = "form-control" required = "required" autocomplete = "off" placeholder = "رقم هاتف الطالب" />
								</div>
							</div>
							<!--End Student Phone Field-->
							<!--Start Submit Button-->
							<div class = "form-group row justify-content-md-center text-left">
								<div class = "col-md-6">
		              				<button type = "submit" class = "btn btn-primary"><i class="fas fa-plus"></i> تعديل الهاتف</button>
								</div>
							</div>
							<!--End Submit Button-->
						</form>
						<div class = "ajax-val"></div>
					</div>

<?php

				// If There Is No Such ID Show Error Message

				} else {

					redirect("<div class = 'alert alert-danger'>هذا الرقم غير موجود</div>", 3, "students.php", "صفحة بيانات الطلاب");

				}

			} else {

				header("Location: students.php");

			}

		} else if ($do === "UpdatePhone") {	//	Update Page

			if ($powers <= 2) {

				if ($_SERVER['REQUEST_METHOD'] === "POST") {

					// Check If Post Request Student ID Is Numeric & Get The Integer Value Of It

					$id = isset($_POST["id"]) && is_numeric(filter_var($_POST["id"], FILTER_SANITIZE_NUMBER_INT)) ? intval($_POST["id"]) : 0;

					// The Row Count

					$count = checkSelect("*", "students_phones", "id = " . $id);

					// If There Is Such ID Show The Form

					if ($count > 0) {

						echo "<div class = 'container text-right'>";

							// Get Variables From  The Form

							$id = filter_var($_POST["id"], FILTER_SANITIZE_NUMBER_INT);
							$phone = filter_var($_POST["phone"], FILTER_SANITIZE_NUMBER_INT);
							$studentid = filter_var($_POST["studentid"], FILTER_SANITIZE_NUMBER_INT);

							//	Validate The Form

							$formErrors = array();

							if (empty($id)) {

								$formErrors[] = "كود هاتف الطالب البرمجى لا يمكن أن يكون <strong>فارغ</strong>";

							}

							if (empty($phone)) {

								$formErrors[] = "هاتف الطالب لا يمكن أن يكون <strong>فارغ</strong>";

							}

							if (strlen($phone) != 11) {

								$formErrors[] = "هاتف الطالب يجب أن يكون <strong>14 رقم</strong>";

							}

							if (empty($id)) {

								$formErrors[] = "كود الطالب البرمجى لا يمكن أن يكون <strong>فارغ</strong>";

							}

							// Loop Into Error Array And Echo It

							foreach ($formErrors as $error) {

								echo "<div class = 'alert alert-danger'>" . $error . "</div>";

							}

							// Check If There's No Error Proceed The Update Operation

							if (empty($formErrors)) {

								// Check If This User Exists Or Not

								$isExist = checkSelect("*", "students_phones", "phone = '" . $phone . "' AND student_id = " . $studentid);

								if ($isExist === 0) {

									// Update The Database With This Info

									$stmt = $con->prepare("UPDATE students_phones SET phone = ? WHERE id = ?");
									
									$stmt->execute(array($phone, $id));

									if ($stmt->rowCount() > 0) {

										// Get User ID

										$userid = getSelect("id", "users", "username = '" . $_SESSION["AdminUsername"] . "'", 1, "ASC", false);

										// Add To The Update History

										$stmt = $con->prepare("INSERT INTO 
																	data_updates(category, action, date, updater_id, updated_id)
																VALUES 
																	('students_phones', 'update', '" . date("Y-m-d h:i:s") . "', ?, ?)");

										$stmt->execute(array($userid['id'], $id));

									}

									// Echo Success Message

									echo "<div class = 'alert alert-success'>تم تعديل هاتف الطالب بنجاح</div>";

								} else {

									echo "<div class = 'alert alert-warning'>تم إضافة هذا الرقم لهذا الطالب سابقاً</div>";

								}
								
							}

						echo "</div>";

					// If There Is No Such ID Show Error Message

					} else {

						echo "<div class = 'alert alert-danger'>هذا الهاتف غير موجود</div>";

					}

				} else {

					redirect("<div class = 'alert alert-danger'>لا يمكنك تصفح هذه الصفحة مباشرةً</div>");

				}

			} else {

				header("Location: students.php");

			}

		} else if ($do === "DeletePhone") {	// Delete Phone Page

			if ($powers <= 2) {

				echo "<h1 class = 'text-center'>مسح رقم الطالب</h1>";
				echo "<div class = 'container text-right'>";

					// Check If Get Request Phone ID Is Numeric & Get The Integer Value Of It

					$phoneid = isset($_GET["id"]) && is_numeric($_GET["id"]) ? intval($_GET["id"]) : 0;
					$studentid = isset($_GET["studentid"]) && is_numeric($_GET["studentid"]) ? intval($_GET["studentid"]) : 0;

					// Check If This Student Exists Or Not

					$isExist = checkSelect("id", "students_phones", "id = " . $phoneid);

					// If There Is Such ID Show The Form

					if ($isExist > 0) { 

						// Delete Student

						$stmt = $con->prepare("DELETE FROM students_phones WHERE id = :id");

						$stmt->bindParam(":id", $phoneid);

						$stmt->execute();

						if ($stmt->rowCount() > 0) {

							// Add To The Update History

							$stmt = $con->prepare("DELETE FROM 
														data_updates
													WHERE 
														updated_id = ? AND category = 'students_phones'");

							$stmt->execute(array($phoneid));

						}
						
						$isStudentExist = checkSelect("id", "students", "id = " . $studentid);
						
						if ($isStudentExist > 0) {

							redirect("<div class = 'alert alert-success'>تم حذف هذا الهاتف بنجاح</div>", 3, "students.php?do=Phones&id=" . $studentid . "&currentPage=1", "صفحة أرقام هذا الطالب");

						} else {
							
							redirect("<div class = 'alert alert-success'>تم حذف هذا الهاتف بنجاح</div>", 3, "students.php", "صفحة بيانات الطلاب");
							
						}
							
					} else {

						// If There Is No Such ID Show Error Message

						redirect("<div class = 'alert alert-danger'>هذا الهاتف غير موجود</div>", 3, "students.php", "صفحة بيانات الطلاب");

					}

				echo "</div>";

			} else {

				header("Location: students.php");

			}

		} else if ($do === "PhoneHistory") {		// Student History Page

			if ($powers <= 2) {

				if (!isset($_GET['currentPage'])) {

					$currentPage = 1;

				} else {

					$currentPage = $_GET['currentPage'];

				}

				// Check If Get Request Phone ID Is Numeric & Get The Integer Value Of It

				$phoneid = isset($_GET["id"]) && is_numeric($_GET["id"]) ? intval($_GET["id"]) : 0;

				// Check If This Student Exists Or Not

				$isExist = checkSelect("id", "students_phones", "id = " . $phoneid);

				// If There Is Such ID Show The Form

				if ($isExist != 0) {

					// Check If This Student Exists Or Not

					$isExist = checkSelect("updated_id, category", "data_updates", "updated_id = " . $phoneid . " AND category = 'students_phones'");

					// If There Is Such ID Show The Form

					if ($isExist != 0) {

						$countStudents = checkSelect("*", "data_updates", "updated_id = " . $phoneid . " AND category = 'students_phones'");

						$currentPage = filter_var($currentPage, FILTER_SANITIZE_NUMBER_INT);

						$maxItems = 10;

						if (! filter_var($currentPage, FILTER_VALIDATE_INT) || $currentPage > ceil($countStudents / $maxItems)) {

							$currentPage = 1;

						}

						$paginationData = setPagination($countStudents, $maxItems, $currentPage);

						$histories = getSelect("*", "data_updates", "updated_id = " . $phoneid . " AND category = 'students_phones'", "date", "DESC LIMIT " . $paginationData['srow'] . " , " . $maxItems);

?>
						<div class = "container text-right">
							<h1 class = "text-center">تاريخ الهاتف</h1>				
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
												<p>تم <?php echo lang($history['action']); ?> هذا الرقم بواسطة <?php echo getWord($updaterName['full_name'], 3); ?></p>
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

						echo "<div class = 'alert alert-warning container'><i class='fas fa-exclamation-circle'></i> لا يوجد تاريخ لهذا الطالب</div>";

					}

				} else {

					redirect("<div class = 'alert alert-danger'>هذا الهاتف غير موجود</div>", 3, "students.php", "صفحة بيانات الطلاب");

				}

			} else {

				header("Location: students.php");

			}

		} else if ($do === "PhonesSearch") { // Students Search Page

			$studentid = filter_var($_POST['studentid'], FILTER_SANITIZE_NUMBER_INT);
			$search = filter_var($_POST['search'], FILTER_SANITIZE_STRING);
			$searchtype = isset($_POST["searchtype"]) && is_numeric(filter_var($_POST["searchtype"], FILTER_SANITIZE_NUMBER_INT)) ? intval($_POST["searchtype"]) : 1;

			if (!isset($_GET['currentPage'])) {

				$currentPage = 1;

			} else {

				$currentPage = $_GET['currentPage'];

			}

			$searchColumn = "";

			if ($searchtype == 0) {

				$searchColumn = "phone";

			} else if ($searchtype == 1) {

				$searchColumn = "who_added";

			} else {

				$searchColumn = "phone";

			}

			if (!empty($search) || $search != "") {

				$countPhones = checkSelect("*", "students_phones", "student_id = " . $studentid . " AND  " . $searchColumn . " LIKE '%" . $search . "%'");

			} else {

				$countPhones = checkSelect("*", "students_phones", "student_id = " . $studentid);

			}

			$currentPage = filter_var($currentPage, FILTER_SANITIZE_NUMBER_INT);

			$maxItems = 10;

			if (! filter_var($currentPage, FILTER_VALIDATE_INT) || $currentPage > ceil($countPhones / $maxItems)) {

				$currentPage = 1;

			}

			$paginationData = setPagination($countPhones, $maxItems, $currentPage);

			if (!empty($search) || $search != "") {

				$phones = getSelect("*", "students_phones", "student_id = " . $studentid . " AND  " . $searchColumn . " LIKE '%" . $search . "%'", 1, "ASC LIMIT " . $paginationData['srow'] . " , " . $maxItems);

			} else {

				$phones = getSelect("*", "students_phones", "student_id = " . $studentid, 1, "ASC LIMIT " . $paginationData['srow'] . " , " . $maxItems);

			}

			if (! empty($phones)) {

?>

				<button class = "btn btn-secondary print"><i class="fas fa-print"></i> طباعة</button>
				<div class = "table-responsive printData">
					<table class="main-table text-center table table-bordered">
						<thead class="thead-dark">
							<tr>
								<td colspan = "100%" class = "header-print" style="border: none !important;">
									<p>محافظة الجيزة <br> إدارة الهرم التعليمية <br> مدرسة منشأة البكارى الثانوية (بنين)</p>
									<h1 class = "text-center">أرقام الطالب</h1>
								</td>
							</tr>
							<tr>
								<th scope="col">الرقم</th>
								<?php if ($powers <= 2) { ?>
									<th class = "no-print" scope="col">أضيف بواسطة</th>
									<th class = "no-print" scope="col">أدوات التحكم</th>
								<?php } ?>
							</tr>
						</thead>
						<tbody>
							<?php

								foreach ($phones as $phone) {
														
							?>

									<tr>
										<th scope="row"><?php echo $phone["phone"]; ?></th>
										<?php if ($powers <= 2) { ?>
											<th class = "no-print" scope="row"><?php echo $phone["who_added"]; ?></th>
											<th class = "no-print" scope="row">
												<a href="?do=EditPhone&id=<?php echo $phone['id']; ?>" class="btn btn-sm btn-success"><i class="fas fa-edit"></i> تعديل</a> 
												<a href="?do=DeletePhone&id=<?php echo $phone['id']; ?>&studentid=<?php echo $studentid; ?>" class="btn btn-sm btn-danger confirm" data-toggle="modal" data-target="#delete-confirm"><i class="fas fa-trash-alt"></i> حذف</a>
												<div class="btn-group">
													<button type="button" class="btn btn-sm btn-info dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
														<i class="fas fa-cogs"></i> خيارات أخرى
													</button>
													<div class="dropdown-menu text-right">
														<a class="dropdown-item" href="?do=PhoneHistory&id=<?php echo $phone['id']; ?>&currentPage=1">تاريخ التعديل</a>
													</div>
												</div>
											</th>
										<?php } ?>
									</tr>

							<?php

								}
												
							?>
						</tbody>
					</table>
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
									<div class="modal-body text-right">هل أنت متأكد أنك تريد حذف هذا الهاتف</div>
									<div class="modal-footer">
										<button type="submit" class="btn btn-primary delete-confirm-true">حذف</button>
										<button type="button" class="btn btn-secondary" data-dismiss="modal">إلغاء</button>
									</div>
								</div>
							</div>
						</div>
					</form>
				</div>
				<?php if ($powers <= 2) { ?><a href = "?do=AddPhone&id=<?php echo $studentid; ?>"  class = "btn btn-primary"><i class="fas fa-plus"></i> إضافة رقم</a><?php } ?>
				<footer class = "my-2">
					<?php

						// Call The Pagination Bar
									
						$paging_info = getPagination($paginationData);

					?>
				</footer>

<?php

			} else {
				
?>

				<div class = 'alert alert-warning container'><i class='fas fa-exclamation-circle'></i> لا يوجد أى أرقام بهذه المواصفات <?php if ($powers <= 2) { echo "هل تريد <a href = '?do=Add'  class = 'badge badge-pill badge-primary'><i class='fas fa-plus'></i> إضافة رقم</a> جديد؟"; } ?></div>

<?php
				
			}

		} else if ($do === "Activities") { // Activity Page

			echo "<div class = 'container text-right'>";

				// Check If Get Request studentid Is Numeric & Get The Integer Value Of It

				$studentid = isset($_GET["id"]) && is_numeric($_GET["id"]) ? intval($_GET["id"]) : 0;
				$category = isset($_GET["category"]) && ($_GET["category"] == "Activities" || $_GET["category"] == "Irregularities") ? $_GET["category"] : "Activities";
				$cat_page = $category;
				$category = $category == "Activities" ? 0 : 1;

				// Check If This Student Exists Or Not

				$isExist = checkSelect("id", "students", "id = " . $studentid);

				// If There Is Such ID Show The Form

				if ($isExist > 0) { 

					if (!isset($_GET['currentPage'])) {

						$currentPage = 1;

					} else {

						$currentPage = $_GET['currentPage'];

					}

					$stmt = $con->prepare("
											SELECT 
												students_activities.*,
												activities.name,
											    activities.category
											FROM 
												students_activities 
											INNER JOIN 
												activities 
											ON 
												students_activities.activity_id = activities.id 
											WHERE 
												category = ?
											AND
												student_id = ?
										");

					$stmt->execute(array($category, $studentid));

					$countActivities = $stmt->rowCount();

					$currentPage = filter_var($currentPage, FILTER_SANITIZE_NUMBER_INT);

					$maxItems = 10;

					if (! filter_var($currentPage, FILTER_VALIDATE_INT) || $currentPage > ceil($countActivities / $maxItems)) {

						$currentPage = 1;

					}

					$paginationData = setPagination($countActivities, $maxItems, $currentPage);

					$stmt = $con->prepare("
											SELECT 
												students_activities.*,
												activities.name,
											    activities.category
											FROM 
												students_activities 
											INNER JOIN 
												activities 
											ON 
												students_activities.activity_id = activities.id 
											WHERE 
												category = ?
											AND
												student_id = ?
											ORDER BY id ASC
											LIMIT " . $paginationData['srow'] . " , " . $maxItems . "
										");

					$stmt->execute(array($category, $studentid));

					$activities = $stmt->fetchAll();

					if (! empty($activities)) {

?>

						<div class = "main">
							<div class = "container text-right">
								<h1 class = "text-center"><?php if ($category == 0) { echo "أنشطة الطالب"; } else { echo "مخالفات الطالب"; } ?></h1>
								<form class = "SearchForm ajax-form" action = "?do=ActivitySearch&category=<?php echo $cat_page; ?>" method = "POST" enctype = "multipart/form-data">
									<!--Start Student Code Field-->
									<div class = "form-group row justify-content-md-center">
										<div class = "col-md-4 pl-md-0 search">
											<input type = "hidden" name = "studentid" value = "<?php echo $studentid; ?>" />
											<input type = "hidden" name = "category" value = "<?php echo $cat_page; ?>" />
											<input type = "search" name = "search" placeholder = "بحث"  class = "form-control ajax-type rounded-right" autocomplete = "off" />
											<button type = "submit" class = "btn bg-transparent p-0 position-absolute"><i class = "fas fa-search"></i></button>
										</div>
										<div class = "col-md-2 pr-md-0">
										    <select class="form-control rounded-left ajax-select" id="selectstatus" name = "searchtype" data-size="5">
												<option value = "0" selected>نوع <?php if ($category == 0) { echo "النشاط"; } else { echo "المخالفة"; } ?></option>
												<option value = "1">التعليق</option>
												<?php if ($powers <= 3) { ?><option value = "2">أضيف بواسطة</option><?php } ?>
										    </select>
										</div>
									</div>
									<!--End Student Code Field-->
								</form>
								<div class = "main-data ajax-val">
									<button class = "btn btn-secondary print"><i class="fas fa-print"></i> طباعة</button>
									<div class = "table-responsive printData">
										<table class="main-table text-center table table-bordered">
											<thead class="thead-dark">
												<tr>
													<td colspan = "100%" class = "header-print" style="border: none !important;">
														<p>محافظة الجيزة <br> إدارة الهرم التعليمية <br> مدرسة منشأة البكارى الثانوية (بنين)</p>
														<h1 class = "text-center"><?php if ($category == 0) { echo "أنشطة الطالب"; } else { echo "مخالفات الطالب"; } ?></h1>
													</td>
												</tr>
												<tr>
													<th scope="col">نوع <?php if ($category == 0) { echo "النشاط"; } else { echo "المخالفة"; } ?></th>
													<th scope="col">التعليق</th>
													<?php if ($powers <= 3) { ?>
														<th class = "no-print" scope="col">أضيف بواسطة</th>
														<th class = "no-print" scope="col">أدوات التحكم</th>
													<?php } ?>
												</tr>
											</thead>
											<tbody>
												<?php

													foreach ($activities as $activity) {
														
												?>

														<tr>
															<th scope="row"><?php echo $activity["name"]; ?></th>
															<th scope="row"><?php echo $activity["comment"]; ?></th>
															<?php if ($powers <= 3) { ?>
																<th class = "no-print" scope="row"><?php echo $activity["who_added"]; ?></th>
																<th class = "no-print" scope="row">
																	<a href="?do=EditActivity&id=<?php echo $activity['id']; ?>&category=<?php echo $cat_page; ?>" class="btn btn-sm btn-success"><i class="fas fa-edit"></i> تعديل</a> 
																	<a href="?do=DeleteActivity&id=<?php echo $activity['id']; ?>&studentid=<?php echo $studentid; ?>&category=<?php echo $cat_page; ?>" class="btn btn-sm btn-danger confirm" data-toggle="modal" data-target="#delete-confirm"><i class="fas fa-trash-alt"></i> حذف</a>
																	<div class="btn-group">
																		<button type="button" class="btn btn-sm btn-info dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
																	    	<i class="fas fa-cogs"></i> خيارات أخرى
																	  	</button>
																		<div class="dropdown-menu text-right">
																		    <a class="dropdown-item" href="?do=ActivityHistory&id=<?php echo $activity['id']; ?>&currentPage=1&category=<?php echo $cat_page; ?>">تاريخ التعديل</a>
																		</div>
																	</div>
																</th>
															<?php } ?>
														</tr>

												<?php

													}
												
												?>
											</tbody>
										</table>
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
														<div class="modal-body text-right">هل انت متأكد أنك تريد حذف <?php if ($category == 0) { echo "هذا النشاط"; } else { echo "هذه المخالفة"; } ?></div>
														<div class="modal-footer">
															<button type="submit" class="btn btn-primary delete-confirm-true">حذف</button>
															<button type="button" class="btn btn-secondary" data-dismiss="modal">إلغاء</button>
														</div>
													</div>
												</div>
											</div>
										</form>
									</div>
									<?php if ($powers <= 3) { ?><a href = "?do=AddActivity&id=<?php echo $studentid; ?>&category=<?php if ($category == 0) { echo "Activities"; } else { echo "Irregularities"; } ?>"  class = "btn btn-primary"><i class="fas fa-plus"></i> إضافة <?php if ($category == 0) { echo "نشاط"; } else { echo "مخالفة"; } ?></a><?php } ?>
									<footer class = "my-2">
										<?php

											// Call The Pagination Bar
									
											$paging_info = getPagination($paginationData);

										?>
									</footer>
								</div>
							</div>
						</div>

<?php

					} else {

?>
					
						<div class = 'alert alert-warning container'><i class='fas fa-exclamation-circle'></i> لا يوجد <?php if ($category == 0) { echo "أنشطة"; } else { echo "مخالفات"; } ?> لهذا الطالب هل تريد <a href = '?do=AddActivity&id=<?php echo $studentid; ?>&category=<?php if ($category == 0) { echo "Activities"; } else { echo "Irregularities"; } ?>'  class = 'badge badge-pill badge-primary'><i class='fas fa-plus'></i> إضافة <?php if ($category == 0) { echo "نشاط"; } else { echo "مخالفة"; } ?></a> <?php if ($category == 0) { echo "جديد"; } else { echo "جديدة"; } ?> لهذا الطالب؟</div>

<?php
						
					}

				} else {

					// If There Is No Such ID Show Error Message

					redirect("<div class = 'alert alert-danger'>هذا الطالب غير موجود</div>");

				}

			echo "</div>";

		} else if ($do === "AddActivity") {	// Add Activity Page

			if ($powers <= 3) {

				// Check If Get Request studentid Is Numeric & Get The Integer Value Of It

				$studentid = isset($_GET["id"]) && is_numeric($_GET["id"]) ? intval($_GET["id"]) : 0;
				$category = isset($_GET["category"]) && ($_GET["category"] == "Activities" || $_GET["category"] == "Irregularities") ? $_GET["category"] : "Activities";
				$category = $category == "Activities" ? 0 : 1;

?>

				<h1 class = "text-center">إضافة <?php if ($category == 0) { echo "نشاط جديد"; } else { echo "مخالفة جديدة"; } ?></h1>
				<div class = "container text-right">
					<form class = "ajax-form" action = "?do=InsertActivity" method = "POST" enctype = "multipart/form-data">
						<input type = "hidden" name = "id" value = "<?php echo $studentid; ?>" />
						<input type = "hidden" name = "category" value = "<?php echo $category; ?>" />
						<!--Start Activities / Irregularities Select-->
						<div class = "form-group row justify-content-md-center">
							<label class = "col-md-2 control-label">نوع <?php if ($category == 0) { echo "النشاط"; } else { echo "المخالفة"; } ?></label>
							<div class = "col-md-4">
								<select class="form-control" id="selectstatus" name = "activity" data-size="5">
									<?php
										
										$activities = getSelect("*", "activities", "category = " . $category);
										
										foreach ($activities as $activity) {
											
											echo "<option value = '" . $activity['id'] . "'>" . $activity['name'] . "</option>";
											
										}
										
									?>
								</select>
							</div>
						</div>
						<!--End Activities / Irregularities Select-->
						<!--Start Comment Field-->
						<div class = "form-group row justify-content-md-center">
							<label class = "col-md-2 control-label">التعليق</label>
							<div class = "col-md-4">
								<textarea type = "text" name = "comment" class = "form-control" autocomplete = "off" rows = "5" placeholder = "التعليق"></textarea>
							</div>
						</div>
						<!--End Comment Field-->
						<!--Start Submit Button-->
						<div class = "form-group row justify-content-md-center text-left">
							<div class = "col-md-6">
	              				<button type = "submit" class = "btn btn-primary"><i class="fas fa-plus"></i> إضافة <?php if ($category == 0) { echo "النشاط"; } else { echo "المخالفة"; } ?></button>
							</div>
						</div>
						<!--End Submit Button-->
					</form>
					<div class = "ajax-val"></div>
				</div>

<?php

			} else {

				header("Location: students.php");

			}

		} else if ($do === "InsertActivity") {	// Insert Activity Page

			if ($powers <= 3) {

				if ($_SERVER['REQUEST_METHOD'] === "POST") {
					
					// Get Variables From  The Form

					$id = filter_var($_POST["id"], FILTER_SANITIZE_NUMBER_INT);
					$activity = filter_var($_POST["activity"], FILTER_SANITIZE_NUMBER_INT);
					$comment = filter_var($_POST["comment"], FILTER_SANITIZE_STRING);
					
					$cat = filter_var($_POST["category"], FILTER_SANITIZE_STRING);
					$category = isset($cat) && ($cat == 0 || $cat == 1) ? $cat : 0;
					$cat_page = "";
					
					if ($category == 1) {
						
						$cat_page = "Irregularities";
						
					} else {
						
						$cat_page = "Activities";
						
					}

					echo "<div class = 'container text-right'>";

						//	Validate The Form

						$formErrors = array();

						if (empty($id)) {

							$formErrors[] = "كود الطالب البرمجى لا يمكن أن يكون <strong>فارغ</strong>";

						}

						if (empty($activity)) {
							
							$theMsg = "";
							
							if ($category == 0) {
								
								$theMsg = "النشاط"; 
								
							} else { 
							
								$theMsg = "المخالفة"; 
								
							}

							$formErrors[] = "كود نوع " . $theMsg . " البرمجى لا يمكن أن يكون <strong>فارغ</strong>";

						}

						// Loop Into Error Array And Echo It

						foreach ($formErrors as $error) {

							echo "<div class = 'alert alert-danger'>" . $error . "</div>";

						}

						// Check If There's No Error Proceed The Update Operation

						if (empty($formErrors)) {

							// Check If This Student Exists Or Not

							$isExist = checkSelect("*", "students", "id = " . $id);

							if ($isExist != 0) {
								
								// Check If The Activity Exists Or Not
								
								$isActivityExist = checkSelect("*", "activities", "id = " . $activity);
								
								if ($isActivityExist != 0) {
									
									// Get Who Added

									$who_added = getSelect("full_name", "users", "username = '" . $_SESSION["AdminUsername"] . "'", 1, "ASC", false);

									// Check If This Student Activity Exists Or Not

									$isStudentActivityExist = checkSelect("*", "students_activities", "comment = '" . $comment . "' AND student_id =" . $id . " AND activity_id = " . $activity);

									if ($isStudentActivityExist === 0) {

										// Insert Into Database With This Info

										$stmt = $con->prepare("INSERT INTO 
																	students_activities(comment, who_added, student_id, activity_id)
																VALUES 
																	(?, ?, ?, ?)");

										$stmt->execute(array($comment, $who_added['full_name'], $id, $activity));

										if ($stmt->rowCount() > 0) {

											// Get User ID

											$userid = getSelect("id", "users", "username = '" . $_SESSION["AdminUsername"] . "'", 1, "ASC", false);

											// Get Phone ID

											$activityid = getSelect("id", "students_activities", "comment = '" . $comment . "' AND who_added = '" . $who_added['full_name'] . "' AND student_id =" . $id . " AND activity_id = " . $activity , 1, "ASC", false);

											// Add To The Update History

											$stmt = $con->prepare("INSERT INTO 
																		data_updates(category, action, date, updater_id, updated_id)
																	VALUES 
																		(?, 'add', '" . date("Y-m-d h:i:s") . "', ?, ?)");
												
											$history_cat = "";
												
											if ($category == 0) {
												
												$history_cat = "students_activities"; 
												
											} else { 
											
												$history_cat = "students_irregularities";
												
											}

											$stmt->execute(array($history_cat, $userid['id'], $activityid['id']));

										}
										
										// Echo Success Message

										$theMsg = "<div class = 'alert alert-success'>تم إضافة ";
										
										if ($category == 0) {
											
											$theMsg .= "نشاط"; 
											
										} else { 
										
											$theMsg .= "مخالفة"; 
											
										}
										
										$theMsg .= " بنجاح </div>";
										
										$theMsg2 = "صفحة ";
										
										if ($category == 0) {
											
											$theMsg2 .= "أنشطة"; 
											
										} else { 
										
											$theMsg2 .= "مخالفات"; 
											
										}
										
										$theMsg2 .= " هذا الطالب";

										echo $theMsg;

									} else {
										
										$theMsg = "";
										
										if ($category == 0) {
											
											$theMsg = "هذا النشاط"; 
											
										} else { 
										
											$theMsg = "هذه المخالفة"; 
											
										}
										
										
										$theMsg2 = "صفحة ";
											
										if ($category == 0) {
											
											$theMsg2 .= "أنشطة"; 
											
										} else { 
										
											$theMsg2 .= "مخالفات"; 
											
										}
										
										$theMsg2 .= " هذا الطالب";
										
										echo "<div class = 'alert alert-warning'>تم إضافة " . $theMsg . " لهذا الطالب بنفس البيانات من قبل</div>";
										

									}
									
								} else {
									
									$theMsg = "<div class = 'alert alert-danger'>";
										
									if ($category == 0) {
										
										$theMsg .= "هذا النشاط غير موجود"; 
										
									} else { 
									
										$theMsg .= "هذه المخالفة غير موجودة"; 
										
									}
									
									$theMsg .= " </div>";
									
									$theMsg2 = "صفحة ";
									
									if ($category == 0) {
										
										$theMsg2 .= "أنشطة"; 
										
									} else { 
									
										$theMsg2 .= "مخالفات"; 
										
									}
									
									$theMsg2 .= " هذا الطالب";

									echo $theMsg;
									
								}

							} else {

								echo "<div class = 'alert alert-danger'>هذا الطالب غير موجود</div>";

							}
							
						}

					echo "</div>";

				} else {

					redirect("<div class = 'alert alert-danger'>لا يمكنك تصفح هذه الصفحة مباشرةً</div>");

				}

			} else {

				header("Location: students.php");

			}

		} else if ($do === "EditActivity") {	//	Edit Page 

			if ($powers <= 3) {

				// Check If Get Request Phone ID Is Numeric & Get The Integer Value Of It

				$id = isset($_GET["id"]) && is_numeric(filter_var($_GET["id"], FILTER_SANITIZE_NUMBER_INT)) ? intval($_GET["id"]) : 0;

				$category = isset($_GET["category"]) && ($_GET["category"] == "Activities" || $_GET["category"] == "Irregularities") ? $_GET["category"] : "Activities";
				$cat_page = $category;
				$category = $category == "Activities" ? 0 : 1;

				// The Row Count

				$count = checkSelect("*", "students_activities", "id = " . $id);

				// If There Is Such ID Show The Form

				if ($count > 0) {

					$studentActivity = getSelect("*", "students_activities", "id = " . $id, 1, "ASC", false);

?>

					<h1 class = "text-center"><?php if ($category === 0) { echo "تعديل نشاط الطالب"; } else { echo "تعديل مخالفة الطالب"; } ?></h1>
					<div class = "container text-right">
						<form class = "ajax-form" action = "?do=UpdateActivity" method = "POST" enctype = "multipart/form-data">
							<input type = "hidden" name = "id" value = "<?php echo $studentActivity['id']; ?>" />
							<input type = "hidden" name = "studentid" value = "<?php echo $studentActivity['student_id']; ?>" />
							<input type = "hidden" name = "category" value = "<?php echo $cat_page; ?>" />
							<!--Start Activities / Irregularities Select-->
							<div class = "form-group row justify-content-md-center">
								<label class = "col-md-2 control-label">نوع <?php if ($category == 0) { echo "النشاط"; } else { echo "المخالفة"; } ?></label>
								<div class = "col-md-4">
									<select class="form-control" id="selectstatus" name = "activity" data-size="5">
										<?php
											
											$activities = getSelect("*", "activities", "category = " . $category);
											
											foreach ($activities as $activity) {
										
										?>

												<option value = "<?php echo $activity['id']; ?>" <?php if ($studentActivity['activity_id'] == $activity['id']) { echo "selected"; } ?>><?php echo $activity['name']; ?></option>
										
										<?php

											}
											
										?>
									</select>
								</div>
							</div>
							<!--End Activities / Irregularities Select-->
							<!--Start Comment Field-->
							<div class = "form-group row justify-content-md-center">
								<label class = "col-md-2 control-label">التعليق</label>
								<div class = "col-md-4">
									<textarea type = "text" name = "comment" class = "form-control" autocomplete = "off" rows = "5" placeholder = "التعليق"><?php echo $studentActivity['comment']; ?></textarea>
								</div>
							</div>
							<!--End Comment Field-->
							<!--Start Submit Button-->
							<div class = "form-group row justify-content-md-center text-left">
								<div class = "col-md-6">
		              				<button type = "submit" class = "btn btn-primary"><i class="fas fa-plus"></i> تعديل <?php if ($category == 0) { echo "النشاط"; } else { echo "المخالفة"; } ?></button>
								</div>
							</div>
							<!--End Submit Button-->
						</form>
						<div class = "ajax-val"></div>
					</div>

<?php

				// If There Is No Such ID Show Error Message

				} else {

					redirect("<div class = 'alert alert-danger'>هذا الرقم غير موجود</div>", 3, "students.php", "صفحة بيانات الطلاب");

				}

			} else {

				header("Location: students.php");

			}

		} else if ($do === "UpdateActivity") {	//	Update Page

			if ($powers <= 3) {

				if ($_SERVER['REQUEST_METHOD'] === "POST") {

					// Check If Post Request Student ID Is Numeric & Get The Integer Value Of It

					$id = isset($_POST["id"]) && is_numeric(filter_var($_POST["id"], FILTER_SANITIZE_NUMBER_INT)) ? intval($_POST["id"]) : 0;

					// The Row Count

					$count = checkSelect("*", "students_activities", "id = " . $id);

					// If There Is Such ID Show The Form

					if ($count > 0) {

						echo "<div class = 'container text-right'>";

							// Get Variables From  The Form

							$id = filter_var($_POST["id"], FILTER_SANITIZE_NUMBER_INT);
							$studentid = filter_var($_POST["studentid"], FILTER_SANITIZE_NUMBER_INT);
							$activity = filter_var($_POST["activity"], FILTER_SANITIZE_NUMBER_INT);
							$comment = filter_var($_POST["comment"], FILTER_SANITIZE_STRING);

							$category = isset($_POST["category"]) && ($_POST["category"] == "Activities" || $_POST["category"] == "Irregularities") ? $_POST["category"] : "Activities";
							$cat_page = $category;
							$category = $category == "Activities" ? 0 : 1;

							//	Validate The Form

							$formErrors = array();

							if (empty($id)) {

								$formErrors[] = "كود الطالب البرمجى لا يمكن أن يكون <strong>فارغ</strong>";

							}

							if (empty($activity)) {
								
								$theMsg = "";
								
								if ($category == 0) {
									
									$theMsg = "النشاط"; 
									
								} else { 
								
									$theMsg = "المخالفة"; 
									
								}

								$formErrors[] = "كود نوع " . $theMsg . " البرمجى لا يمكن أن يكون <strong>فارغ</strong>";

							}

							// Loop Into Error Array And Echo It

							foreach ($formErrors as $error) {

								echo "<div class = 'alert alert-danger'>" . $error . "</div>";

							}

							// Check If There's No Error Proceed The Update Operation

							if (empty($formErrors)) {

								// Check If This User Exists Or Not

								$isExist = checkSelect("*", "students_activities", "comment = '" . $comment . "' AND student_id =" . $studentid . " AND activity_id = " . $activity);

								if ($isExist === 0) {

									// Update The Database With This Info

									$stmt = $con->prepare("UPDATE students_activities SET comment = ?, activity_id = ? WHERE id = ?");
									
									$stmt->execute(array($comment, $activity, $id));

									if ($stmt->rowCount() > 0) {

										// Get User ID

										$userid = getSelect("id", "users", "username = '" . $_SESSION["AdminUsername"] . "'", 1, "ASC", false);

										// Add To The Update History

										$stmt = $con->prepare("INSERT INTO 
																	data_updates(category, action, date, updater_id, updated_id)
																VALUES 
																	(?, 'update', '" . date("Y-m-d h:i:s") . "', ?, ?)");

										$history_cat = "";
												
										if ($category == 0) {
											
											$history_cat = "students_activities"; 
											
										} else { 
										
											$history_cat = "students_irregularities";
											
										}

										$stmt->execute(array($history_cat, $userid['id'], $id));

									}

									// Echo Success Message
									
									$theMsg = "<div class = 'alert alert-success'>تم تعديل ";
										
									if ($category == 0) {
										
										$theMsg .= "نشاط"; 
										
									} else { 
									
										$theMsg .= "مخالفة"; 
										
									}
									
									$theMsg .= " هذا الطالب بنجاح</div>";
									
									$theMsg2 = "صفحة ";
									
									if ($category == 0) {
										
										$theMsg2 .= "أنشطة"; 
										
									} else { 
									
										$theMsg2 .= "مخالفات"; 
										
									}
									
									$theMsg2 .= " هذا الطالب";

									echo $theMsg;

								} else {
									
									$theMsg = "<div class = 'alert alert-warning'>تم إضافة ";
										
									if ($category == 0) {
										
										$theMsg .= "هذا النشاط"; 
										
									} else { 
									
										$theMsg .= "هذه المخالفة"; 
										
									}
									
									$theMsg .= " لهذا الطالب سابقاً</div>";

									echo $theMsg;

								}
								
							}

						echo "</div>";

					// If There Is No Such ID Show Error Message

					} else {
						
						$theMsg = "<div class = 'alert alert-warning'>";
										
						if ($category == 0) {
							
							$theMsg .= "هذا النشاط غير موجود"; 
							
						} else { 
						
							$theMsg .= "هذه المخالفة غير موجودة"; 
							
						}
						
						$theMsg .= "</div>";

						echo $theMsg;

					}

				} else {

					redirect("<div class = 'alert alert-danger'>لا يمكنك تصفح هذه الصفحة مباشرةً</div>");

				}

			} else {

				header("Location: students.php");

			}

		} else if ($do === "DeleteActivity") {	// Delete Activity Page

			if ($powers <= 3) {
		
				// Check If Get Request Phone ID Is Numeric & Get The Integer Value Of It

				$activityid = isset($_GET["id"]) && is_numeric($_GET["id"]) ? intval($_GET["id"]) : 0;
				$studentid = isset($_GET["studentid"]) && is_numeric($_GET["studentid"]) ? intval($_GET["studentid"]) : 0;
			
				$category = isset($_GET["category"]) && ($_GET["category"] == "Activities" || $_GET["category"] == "Irregularities") ? $_GET["category"] : "Activities";
				$cat_page = $category;
				$category = $category == "Activities" ? 0 : 1;

				echo "<h1 class = 'text-center'>";
					if ($category === 0) {
						echo "حذف نشاط الطالب";
					} else {
						echo "حذف مخالفة الطالب";
					}
				echo "</h1>";
				echo "<div class = 'container text-right'>";

					// Check If This Student Exists Or Not

					$isExist = checkSelect("id", "students_activities", "id = " . $activityid);

					// If There Is Such ID Show The Form

					if ($isExist > 0) { 

						// Delete Student

						$stmt = $con->prepare("DELETE FROM students_activities WHERE id = :id");

						$stmt->bindParam(":id", $activityid);

						$stmt->execute();
						
						if ($stmt->rowCount() > 0) {

							// Get User ID

							$userid = getSelect("id", "users", "username = '" . $_SESSION["AdminUsername"] . "'", 1, "ASC", false);

							// Add To The Update History

							$stmt = $con->prepare("DELETE FROM 
														data_updates
													WHERE 
														updated_id = ? AND category = ?");
								
							$history_cat = "";
								
							if ($category == 0) {
								
								$history_cat = "students_activities"; 
								
							} else { 
							
								$history_cat = "students_irregularities";
								
							}

							$stmt->execute(array($activityid, $history_cat));

						}
						
						$isStudentExist = checkSelect("id", "students", "id = " . $studentid);
						
						if ($isStudentExist > 0) {
							
							$theMsg = "<div class = 'alert alert-success'>تم حذف ";
										
							if ($category == 0) {
								
								$theMsg .= "هذا النشاط"; 
								
							} else { 
							
								$theMsg .= "هذه المخالفة"; 
								
							}
							
							$theMsg .= " بنجاح</div>";
							
							$theMsg2 = "صفحة ";
										
							if ($category == 0) {
								
								$theMsg2 .= "أنشطة"; 
								
							} else { 
							
								$theMsg2 .= "مخالفات"; 
								
							}
							
							$theMsg2 .= " هذا الطالب";

							redirect($theMsg, 3, "students.php?do=Activities&id=" . $studentid . "&category=" . $cat_page, $theMsg2);

						} else {
							
							$theMsg = "<div class = 'alert alert-success'>تم حذف ";
										
							if ($category == 0) {
								
								$theMsg .= "هذا النشاط"; 
								
							} else { 
							
								$theMsg .= "هذه المخالفة"; 
								
							}
							
							$theMsg .= " بنجاح</div>";
							
							redirect($theMsg, 3, "students.php", "صفحة بيانات الطلاب");
							
						}
							
					} else {
						
						$theMsg = "<div class = 'alert alert-danger'>";
										
						if ($category == 0) {
							
							$theMsg .= "هذا النشاط غير موجود"; 
							
						} else { 
						
							$theMsg .= "هذه المخالفة غير موجودة"; 
							
						}
						
						$theMsg .= "</div>";

						// If There Is No Such ID Show Error Message

						redirect($theMsg, 3, "students.php?do=Activities&id=" . $studentid . "&category=" . $cat_page, "صفحة بيانات الطلاب");

					}

				echo "</div>";

			} else {

				header("Location: students.php");

			}

		} else if ($do === "ActivityHistory") {		// Activity History Page

			if ($powers <= 3) {
		
				$category = isset($_GET["category"]) && ($_GET["category"] == "Activities" || $_GET["category"] == "Irregularities") ? $_GET["category"] : "Activities";
				$cat = $category == "Activities" ? 0 : 1;
				$category = $category == "Activities" ? "students_activities" : "students_irregularities";

				if (!isset($_GET['currentPage'])) {

					$currentPage = 1;

				} else {

					$currentPage = $_GET['currentPage'];

				}

				// Check If Get Request Phone ID Is Numeric & Get The Integer Value Of It

				$activityid = isset($_GET["id"]) && is_numeric($_GET["id"]) ? intval($_GET["id"]) : 0;

				// Check If This Student Exists Or Not

				$isExist = checkSelect("id", "students_activities", "id = " . $activityid);

				// If There Is Such ID Show The Form

				if ($isExist != 0) {

					// Check If This Student Exists Or Not

					$isExist = checkSelect("updated_id, category", "data_updates", "updated_id = " . $activityid . " AND category = '" . $category . "'");

					// If There Is Such ID Show The Form

					if ($isExist != 0) {

						$countStudents = checkSelect("*", "data_updates", "updated_id = " . $activityid . " AND category = '" . $category . "'");

						$currentPage = filter_var($currentPage, FILTER_SANITIZE_NUMBER_INT);

						$maxItems = 10;

						if (! filter_var($currentPage, FILTER_VALIDATE_INT) || $currentPage > ceil($countStudents / $maxItems)) {

							$currentPage = 1;

						}

						$paginationData = setPagination($countStudents, $maxItems, $currentPage);

						$histories = getSelect("*", "data_updates", "updated_id = " . $activityid . " AND category = '" . $category . "'", "date", "DESC LIMIT " . $paginationData['srow'] . " , " . $maxItems);

?>

						<div class = "container text-right">
							<h1 class = "text-center">تاريخ <?php if ($cat === 0) { echo "النشاط"; } else { echo "المخالفة"; } ?></h1>				
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
												<p>تم <?php echo lang($history['action']); if ($cat === 0) { echo " هذا النشاط "; } else { echo " هذه المخالفة "; } echo "بواسطة " . getWord($updaterName['full_name'], 3); ?></p>
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
						
						$theMsg = "<div class = 'alert alert-warning container'><i class='fas fa-exclamation-circle'></i> لا يوجد تاريخ تعديل ";
										
						if ($category == 0) {
							
							$theMsg .= "لهذا النشاط"; 
							
						} else { 
						
							$theMsg .= "لهذه المخالفة"; 
							
						}
						
						$theMsg .= " </div>";

						echo $theMsg;

					}

				} else {
					
					$theMsg = "<div class = 'alert alert-danger'> ";
									
					if ($category == 0) {
						
						$theMsg .= " هذا النشاط غير موجود"; 
						
					} else { 
					
						$theMsg .= "هذه المخالفة غير موجودة"; 
						
					}

					redirect($theMsg, 3, "students.php", "صفحة بيانات الطلاب");

				}

			} else {

				header("Location: students.php");

			}

		} else if ($do === "ActivitySearch") { // Students Activities Search Page

			$studentid = filter_var($_POST['studentid'], FILTER_SANITIZE_NUMBER_INT);
			$search = filter_var($_POST['search'], FILTER_SANITIZE_STRING);
			$searchtype = isset($_POST["searchtype"]) && is_numeric(filter_var($_POST["searchtype"], FILTER_SANITIZE_NUMBER_INT)) ? intval($_POST["searchtype"]) : 1;
			
			$category = isset($_GET["category"]) && ($_GET["category"] == "Activities" || $_GET["category"] == "Irregularities") ? $_GET["category"] : "Activities";
			$cat_page = $category;
			$category = $category == "Activities" ? 0 : 1;
			
			if (!isset($_GET['currentPage'])) {

				$currentPage = 1;

			} else {

				$currentPage = $_GET['currentPage'];

			}

			$searchColumn = "";

			if ($searchtype == 0) {

				$searchColumn = "activities.name";

			} else if ($searchtype == 1) {
				
				$searchColumn = "students_activities.comment";
				
			} else if ($searchtype == 2) {

				$searchColumn = "students_activities.who_added";

			} else {

				$searchColumn = "activities.name";

			}

			if (!empty($search) || $search != "") {
				
				$stmt = $con->prepare("
										SELECT 
											students_activities.*,
											activities.comment,
											activities.name,
											activities.who_added,
											activities.category
										FROM 
											students_activities 
										INNER JOIN 
											activities 
										ON 
											students_activities.activity_id = activities.id 
										WHERE 
											category = ?
										AND
											student_id = ?
										AND 										
											" . $searchColumn . "
											LIKE '%" . $search . "%'
									");

				$stmt->execute(array($category, $studentid));

				$countStudentActivities = $stmt->rowCount();

			} else {

				$stmt = $con->prepare("
										SELECT 
											students_activities.*,
											activities.name,
											activities.category
										FROM 
											students_activities 
										INNER JOIN 
											activities 
										ON 
											students_activities.activity_id = activities.id 
										WHERE 
											category = ?
										AND
											student_id = ?
									");

				$stmt->execute(array($category, $studentid));

				$countStudentActivities = $stmt->rowCount();
			
			}

			$currentPage = filter_var($currentPage, FILTER_SANITIZE_NUMBER_INT);

			$maxItems = 10;

			if (! filter_var($currentPage, FILTER_VALIDATE_INT) || $currentPage > ceil($countStudentActivities / $maxItems)) {

				$currentPage = 1;

			}

			$paginationData = setPagination($countStudentActivities, $maxItems, $currentPage);

			if (!empty($search) || $search != "") {
				
				$stmt = $con->prepare("
										SELECT 
											students_activities.*,
											activities.name,
											activities.category
										FROM 
											students_activities 
										INNER JOIN 
											activities 
										ON 
											students_activities.activity_id = activities.id 
										WHERE 
											category = ?
										AND
											student_id = ?
										AND 										
											" . $searchColumn . "
											LIKE '%" . $search . "%'
										ORDER BY id ASC
										LIMIT " . $paginationData['srow'] . " , " . $maxItems . "
									");

				$stmt->execute(array($category, $studentid));

				$studentActivities = $stmt->fetchAll();

			} else {
				
				$stmt = $con->prepare("
										SELECT 
											students_activities.*,
											activities.name,
											activities.category
										FROM 
											students_activities 
										INNER JOIN 
											activities 
										ON 
											students_activities.activity_id = activities.id 
										WHERE 
											category = ?
										AND
											student_id = ?
										ORDER BY id ASC
										LIMIT " . $paginationData['srow'] . " , " . $maxItems . "
									");

				$stmt->execute(array($category, $studentid));

				$studentActivities = $stmt->fetchAll();

			}

			if (! empty($studentActivities)) {

?>
				
				<button class = "btn btn-secondary print"><i class="fas fa-print"></i> طباعة</button>
				<div class = "table-responsive printData">
					<table class="main-table text-center table table-bordered">
						<thead class="thead-dark">
							<tr>
								<td colspan = "100%" class = "header-print" style="border: none !important;">
									<p>محافظة الجيزة <br> إدارة الهرم التعليمية <br> مدرسة منشأة البكارى الثانوية (بنين)</p>
									<h1 class = "text-center"><?php if ($category == 0) { echo "أنشطة الطالب"; } else { echo "مخالفات الطالب"; } ?></h1>
								</td>
							</tr>
							<tr>
								<th scope="col">نوع <?php if ($category == 0) { echo "النشاط"; } else { echo "المخالفة"; } ?></th>
								<th scope="col">التعليق</th>
								<?php if ($powers <= 3) { ?>
									<th class = "no-print" scope="col">أضيف بواسطة</th>
									<th class = "no-print" scope="col">أدوات التحكم</th>
								<?php } ?>
							</tr>
						</thead>
						<tbody>
							<?php

								foreach ($studentActivities as $activity) {
									
							?>

									<tr>
										<th scope="row"><?php echo $activity["name"]; ?></th>
										<th scope="row"><?php echo $activity["comment"]; ?></th>
										<?php if ($powers <= 3) { ?>
											<th class = "no-print" scope="row"><?php echo $activity["who_added"]; ?></th>
											<th class = "no-print" scope="row">
												<a href="?do=EditActivity&id=<?php echo $activity['id']; ?>&category=<?php echo $cat_page; ?>" class="btn btn-sm btn-success"><i class="fas fa-edit"></i> تعديل</a> 
												<a href="?do=DeleteActivity&id=<?php echo $activity['id']; ?>&studentid=<?php echo $studentid; ?>&category=<?php echo $cat_page; ?>" class="btn btn-sm btn-danger confirm" data-toggle="modal" data-target="#delete-confirm"><i class="fas fa-trash-alt"></i> حذف</a>
												<div class="btn-group">
													<button type="button" class="btn btn-sm btn-info dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
														<i class="fas fa-cogs"></i> خيارات أخرى
													</button>
													<div class="dropdown-menu text-right">
														<a class="dropdown-item" href="?do=ActivityHistory&id=<?php echo $activity['id']; ?>&currentPage=1&category=" . <?php echo $cat_page; ?>>تاريخ التعديل</a>
													</div>
												</div>
											</th>
										<?php } ?>
									</tr>

							<?php

								}
							
							?>
						</tbody>
					</table>
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
									<div class="modal-body text-right">هل انت متأكد أنك تريد حذف <?php if ($category == 0) { echo "هذا النشاط"; } else { echo "هذه المخالفة"; } ?></div>
									<div class="modal-footer">
										<button type="submit" class="btn btn-primary delete-confirm-true">حذف</button>
										<button type="button" class="btn btn-secondary" data-dismiss="modal">إلغاء</button>
									</div>
								</div>
							</div>
						</div>
					</form>
				</div>
				<?php if ($powers <= 3) { ?><a href = "?do=AddActivity&id=<?php echo $studentid; ?>&category=<?php if ($category == 0) { echo "Activities"; } else { echo "Irregularities"; } ?>"  class = "btn btn-primary"><i class="fas fa-plus"></i> إضافة <?php if ($category == 0) { echo "نشاط"; } else { echo "مخالفة"; } ?></a><?php } ?>
				<footer class = "my-2">
					<?php

						// Call The Pagination Bar
				
						$paging_info = getPagination($paginationData);

					?>
				</footer>

<?php

			} else {
				
?>

				<div class = 'alert alert-warning container'><i class='fas fa-exclamation-circle'></i> لا يوجد <?php if ($category == 0) { echo "أنشطة"; } else { echo "مخالفات"; } ?> لهذا الطالب بهذه المواصفات <?php if ($powers <= 3) { ?> هل تريد <a href = '?do=AddActivity&id=<?php echo $studentid; ?>&category=<?php if ($category == 0) { echo "Activities"; } else { echo "Irregularities"; } ?>'  class = 'badge badge-pill badge-primary'><i class='fas fa-plus'></i> إضافة <?php if ($category == 0) { echo "نشاط"; } else { echo "مخالفة"; } ?></a> <?php if ($category == 0) { echo "جديد؟"; } else { echo "جديدة؟"; } } ?></div>

<?php
				
			}

		} else {

			header("Location: students.php?do=Manage");

		}

		if ($do != "StudentsSearch" && $do != "PhonesSearch" && $do != "ActivitySearch" && $do != "Insert" && $do != "Update" && $do != "InsertPhone" && $do != "UpdatePhone" && $do != "InsertActivity" && $do != "UpdateActivity") {

			include $templates . "footer.php";

		}

	} else {

		header("Location: index.php");

		exit();

	}

	ob_end_flush();	//	Output Buffering End

?>