<?php

	ob_start();	//	Output Buffering Start

	session_start();

	if (isset($_SESSION["AdminUsername"])) {

		$do = isset($_GET['do']) ? $_GET['do'] : "Manage";

		if ($do != "Update") {

			$pageTitle = "بيانات الطالب";

			$pageActive = basename(__FILE__);

		} else {

			$noNavbar = "";

		}

		include "init.php";

		$userPowers = getSelect("group_id", "users", "username ='" . $_SESSION["AdminUsername"] . "'", 1, "ASC", false);

		$powers = $userPowers["group_id"];

		// Start Manage Page

		if ($do === "Manage") {	// Manage Members Page 

			// Check If Get Request Phone ID Is Numeric & Get The Integer Value Of It

			$studentid = isset($_GET["id"]) && is_numeric(filter_var($_GET["id"], FILTER_SANITIZE_NUMBER_INT)) ? intval($_GET["id"]) : 0;

			$student = getSelect("*", "students", "id = " . $studentid, 1, "ASC", false);
			
			if (! empty($student)) {

?>
				<div class = "container text-right students-profiles">
					<h1 class = "text-center"><?php echo "صحيفة الطالب " . getWord($student["name"]); ?></h1>
					<?php

						if ($student['image'] != "") {

							if (file_exists("data/uploads/students_images/" . $student['image'])) {

								echo '<img src="data/uploads/students_images/' . $student['image'] . '" class="img-fluid img-thumbnail rounded-circle mx-auto d-block mb-4" style = "width: 150; height: 170;">';

							}
							
						}

					?>
					<div class = "main-data">
						<form class = "ajax-form" action = "?do=Update" method = "POST" enctype = "multipart/form-data">
							<div class="card">
								<div class="card-header bg-primary text-white text-center">
									<i class="fas fa-user"></i> البيانات الشخصية
								</div>
								<div class="card-body">
									<div class = "container text-right">
										<input type = "hidden" name = "studentid" value = "<?php echo $studentid; ?>" />
										<div class = "form-group row">
											<!--Start Student Code Field-->
											<label class = "col-md-2 control-label">الكود</label>
											<div class = "col-md-4">
												<input type = "number" name = "studentcode" class = "form-control" value = "<?php echo $student['code']; ?>" required = "required" autocomplete = "off" placeholder = "كود الطالب" <?php if ($powers > 2) { echo "disabled"; } ?>/>
											</div>
											<!--End Student Code Field-->
											<!--Start Name Field-->
											<label class = "col-md-2 control-label">اسم الطالب</label>
											<div class = "col-md-4">
												<input type = "text" name = "studentname" class = "form-control" value = "<?php echo $student['name']; ?>" required = "required" autocomplete = "off" placeholder = "اسم الطالب" <?php if ($powers > 2) { echo "disabled"; } ?> />
											</div>
											<!--End Name Field-->
										</div>
										<div class = "form-group row">
											<!--Start Grade Field-->
											<label class = "col-md-2 control-label">السنة الدراسية</label>
											<div class = "col-md-4">
												<input type = "text" name = "studentgrade" class = "form-control" value = "<?php echo $student['grade']; ?>" required = "required" autocomplete = "off" placeholder = "السنة الدراسية" <?php if ($powers > 2) { echo "disabled"; } ?> />
											</div>
											<!--End Grade Field-->
											<!--Start Class Field-->
											<label class = "col-md-2 control-label">الفصل</label>
											<div class = "col-md-4">
												<input type = "text" name = "studentclass" class = "form-control" value = "<?php echo $student['class']; ?>" required = "required" autocomplete = "off" placeholder = "الفصل" <?php if ($powers > 2) { echo "disabled"; } ?> />
											</div>
											<!--End Class Field-->
										</div>
										<div class = "form-group row">
											<!--Start Second Language Field-->
											<label class = "col-md-2 control-label">اللغة الثانية</label>
											<div class = "col-md-4">
											 	<input type = "text" name = "studentsecondLanguage" class = "form-control" value = "<?php echo $student['second_language']; ?>" autocomplete = "off" placeholder = "اللغة الثانية" <?php if ($powers > 2) { echo "disabled"; } ?> />
											</div>
											<!--End Second Language Field-->
											<!--Start National ID Field-->
											<label class = "col-md-2 control-label">الرقم القومى</label>
											<div class = "col-md-4">
												<input type = "text" name = "studentnationalid" class = "form-control" value = "<?php echo $student['national_id']; ?>" autocomplete = "off" placeholder = "الرقم القومى" <?php if ($powers > 2) { echo "disabled"; } ?> />
											</div>
											<!--End National ID Field-->
										</div>
										<div class = "form-group row">
											<!--Start address Field-->
											<label class = "col-md-2 control-label">العنوان</label>
											<div class = "col-md-4">
												<input type = "text" name = "studentaddress" class = "form-control" value = "<?php echo $student['address']; ?>" autocomplete = "off" placeholder = "العنوان" <?php if ($powers > 2) { echo "disabled"; } ?> />
											</div>
											<!--End address Field-->
											<!--Start Birthday Field-->
											<label class = "col-md-2 control-label">تاريخ الميلاد</label>
											<div class="start_date input-group col-md-4">
											    <div class="input-group-append">
											      <span class="far fa-calendar-alt input-group-text" aria-hidden="true "></span>
											    </div>
											    <input type="text" name = "studentbirthdate" class="form-control date" value = "<?php echo date('d-m-Y', strtotime($student['birth_date'])); ?>" autocomplete = "off" placeholder = "تاريخ الميلاد" <?php if ($powers > 2) { echo "disabled"; } ?> >
											</div>
											<!--End Birthday Field-->
										</div>
										<div class = "form-group row">
											<!--Start Father Job Field-->
											<label class = "col-md-2 control-label">وظيفة الأب</label>
											<div class = "col-md-4">
												<input type = "text" name = "studentfatherjob" class = "form-control" value = "<?php echo $student['father_job']; ?>" autocomplete = "off" placeholder = "وظيفة الأب" <?php if ($powers > 2) { echo "disabled"; } ?> />
											</div>
											<!--End Father Job Field-->
										</div>
									</div>
								</div>
							</div>
							<div class="card">
								<div class="card-header bg-primary text-white text-center">
									<i class="fas fa-phone"></i> الهواتف 
								</div>
								<div class="card-body">
									<div class = "container text-right">
										<?php 

											$phones = getSelect("*", "students_phones", "student_id = " . $studentid, "id", "ASC LIMIT 3");

											if (! empty($phones)) {

												foreach ($phones as $phone) {

										?>
													<input type = "hidden" name = "phoneid[<?php echo $phone['id']; ?>]" value = "<?php echo $phone['id']; ?>" />
													<div class = "form-group row">
														<!--Start Type Field-->
														<label class = "col-md-1 control-label">هاتف</label>
														<div class = "col-md-2">
															<select class="form-control" id="selectstatus" name = "phonetype[<?php echo $phone['id']; ?>]" data-size="5" <?php if ($powers > 2) { echo "disabled"; } ?>>
																<option value = "1" <?php if ($phone['type'] == 1) { echo "selected"; } ?>>الطالب</option>
																<option value = "2" <?php if ($phone['type'] == 2) { echo "selected"; } ?>>الأب</option>
																<option value = "3" <?php if ($phone['type'] == 3) { echo "selected"; } ?>>الأم</option>
																<option value = "4" <?php if ($phone['type'] == 4) { echo "selected"; } ?>>ولى الأمر</option>
															</select>
														</div>
														<!--End Type Field-->
														<!--Start Name Field-->
														<label class = "col-md-2 control-label">رقم الهاتف</label>
														<div class = "col-md-7">
															<input type = "text" name = "phonenumber[<?php echo $phone['id']; ?>]" class = "form-control" value = "<?php echo $phone['phone']; ?>" required = "required" autocomplete = "off" placeholder = "اسم الطالب" <?php if ($powers > 2) { echo "disabled"; } ?> />
														</div>
														<!--End Name Field-->
													</div>
										<?php

												}

											} else {

												echo "<div class = 'alert alert-info'>لا يوجد هواتف للطالب</div>";

											}

										?>
									</div>
								</div>
								<div class = "card-footer text-muted text-center">المزيد <i class="fas fa-chevron-circle-down"></i></div>
							</div>
							<div class="card">
								<div class="card-header bg-primary text-white text-center">
									<i class="fas fa-thumbs-up"></i> الأنشطة  
								</div>
								<div class="card-body">
									<div class = "container text-right">
										<?php 

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
																		category = 0
																	AND
																		student_id = ?
																	ORDER BY id ASC
																	LIMIT 3
																");

											$stmt->execute(array($studentid));

											$studentActivities = $stmt->fetchAll();

											if (! empty($studentActivities)) {

												foreach ($studentActivities as $studentActivity) {

										?>
													<input type = "hidden" name = "studentActivityid[<?php echo $studentActivity['id']; ?>]" value = "<?php echo $studentActivity['id']; ?>" />
													<div class = "form-group row">
														<!--Start Activities / Irregularities Select-->
														<label class = "col-md-1 control-label">نوع النشاط</label>
														<div class = "col-md-3">
															<select class="form-control" id="selectstatus" name = "studentActivityactivity[<?php echo $studentActivity['id']; ?>]" data-size="5">
																<?php
																	
																	$activities = getSelect("*", "activities", "category = 0");
																	
																	foreach ($activities as $activity) {
																
																?>

																		<option value = "<?php echo $activity['id']; ?>" <?php if ($studentActivity['activity_id'] == $activity['id']) { echo "selected"; } ?>><?php echo $activity['name']; ?></option>
																
																<?php

																	}
																	
																?>
															</select>
														</div>
														<!--End Activities / Irregularities Select-->
														<!--Start Comment Field-->
														<label class = "col-md-1 control-label">التعليق</label>
														<div class = "col-md-3">
															<textarea type = "text" name = "studentActivitycomment[<?php echo $studentActivity['id']; ?>]" class = "form-control" autocomplete = "off" rows = "5" placeholder = "التعليق"><?php echo $studentActivity['comment']; ?></textarea>
														</div>
														<!--End Comment Field-->
														<!--Start Procedure Field-->
														<label class = "col-md-1 control-label">الإجراء</label>
														<div class = "col-md-3">
															<textarea type = "text" name = "studentActivityprocedure[<?php echo $studentActivity['id']; ?>]" class = "form-control" autocomplete = "off" rows = "5" placeholder = "الإجراء"><?php echo $studentActivity['procedure']; ?></textarea>
														</div>
														<!--End Procedure Field-->
													</div>
										<?php 

												}

											} else {

												echo "<div class = 'alert alert-info'>لا يوجد أنشطة للطالب</div>";

											}

										?>
									</div>
								</div>
								<div class = "card-footer text-muted text-center">المزيد <i class="fas fa-chevron-circle-down"></i></div>
							</div>
							<div class="card">
								<div class="card-header bg-primary text-white text-center">
									<i class="fas fa-thumbs-down"></i> المخالفات  
								</div>
								<div class="card-body">
									<div class = "container text-right">
										<?php 

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
																		category = 1
																	AND
																		student_id = ?
																	ORDER BY id ASC
																	LIMIT 3
																");

											$stmt->execute(array($studentid));

											$studentActivities = $stmt->fetchAll();

											if (! empty($studentActivities)) {

												foreach ($studentActivities as $studentActivity) {

										?>
													<input type = "hidden" name = "studentIrregularitiesid[<?php echo $studentActivity['id']; ?>]" value = "<?php echo $studentActivity['id']; ?>" />
													<div class = "form-group row">
														<!--Start Activities / Irregularities Select-->
														<label class = "col-md-1 control-label">نوع المخالفة</label>
														<div class = "col-md-3">
															<select class="form-control" id="selectstatus" name = "studentIrregularitiesactivity[<?php echo $studentActivity['id']; ?>]" data-size="5">
																<?php
																	
																	$activities = getSelect("*", "activities", "category = 1");
																	
																	foreach ($activities as $activity) {
																
																?>

																		<option value = "<?php echo $activity['id']; ?>" <?php if ($studentActivity['activity_id'] == $activity['id']) { echo "selected"; } ?>><?php echo $activity['name']; ?></option>
																
																<?php

																	}
																	
																?>
															</select>
														</div>
														<!--End Activities / Irregularities Select-->
														<!--Start Comment Field-->
														<label class = "col-md-1 control-label">التعليق</label>
														<div class = "col-md-3">
															<textarea type = "text" name = "studentIrregularitiescomment[<?php echo $studentActivity['id']; ?>]" class = "form-control" autocomplete = "off" rows = "5" placeholder = "التعليق"><?php echo $studentActivity['comment']; ?></textarea>
														</div>
														<!--End Comment Field-->
														<!--Start Procedure Field-->
														<label class = "col-md-1 control-label">الإجراء</label>
														<div class = "col-md-3">
															<textarea type = "text" name = "studentIrregularitiesprocedure[<?php echo $studentActivity['id']; ?>]" class = "form-control" autocomplete = "off" rows = "5" placeholder = "الإجراء"><?php echo $studentActivity['procedure']; ?></textarea>
														</div>
														<!--End Procedure Field-->
													</div>
										<?php 

												}

											} else {

												echo "<div class = 'alert alert-info'>لا يوجد مخالفات للطالب</div>";

											}

										?>
									</div>
								</div>
								<div class = "card-footer text-muted text-center">المزيد <i class="fas fa-chevron-circle-down"></i></div>
							</div>
							<?php if ($powers <= 3) { ?><button type = "submit" class = "btn btn-primary mb-2"><i class="fas fa-edit"></i> حفظ</button><?php } ?>
							<div class = "ajax-val"></div>
						</form>

						<!--<form action = "" method = "POST" class = "confirm-modal">	-->
							<!-- Modal -->
							<!--<div class="modal fade" id="delete-confirm" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
								<div class="modal-dialog modal-dialog-centered" role="document">
									<div class="modal-content">
										<div class="modal-header pl-0">
											<h5 class="modal-title" id="exampleModalLongTitle">حذف</h5>
											<button type="button" class="close ml-0" data-dismiss="modal" aria-label="Close">
												<span aria-hidden="true">&times;</span>
											</button>
										</div>
										<div class="modal-body text-right">هل انت متأكد أنك تريد حذف <?php // if ($category == 0) { echo "هذا النشاط"; } else { echo "هذه المخالفة"; } ?>؟</div>
										<div class="modal-footer">
											<button type="submit" class="btn btn-primary delete-confirm-true">حذف</button>
											<button type="button" class="btn btn-secondary" data-dismiss="modal">إلغاء</button>
										</div>
									</div>
								</div>
							</div>
						</form>-->
						<footer class = "my-2">
							
						</footer>
					</div>
				</div>

<?php

			} else {

?>
			
				<div class = 'alert alert-warning container'><i class='fas fa-exclamation-circle'></i> لا يوجد <?php if ($category == 0) { echo 'أنشطة'; } else { echo 'مخالفات'; } if ($powers <= 3) { ?> هل تريد <a href = "?do=Add&category=<?php if ($category == 0) { echo "Activities"; } else { echo "Irregularities"; } ?>"  class = 'badge badge-pill badge-primary'><i class='fas fa-plus'></i> إضافة <?php if ($category == 0) { echo 'نشاط'; } else { echo 'مخالفة'; } ?></a> <?php if ($category == 0) { echo 'جديد؟'; } else { echo 'جديدة؟'; } }?></div>

<?php
				
			}

		} else if ($do === "Update") {	//	Update Page

			if ($powers <= 3) {

				if ($_SERVER['REQUEST_METHOD'] === "POST") {

					// Get Variables From  The Form

					$studentid = isset($_POST["studentid"]) && is_numeric(filter_var($_POST["studentid"], FILTER_SANITIZE_NUMBER_INT)) ? intval(filter_var($_POST["studentid"], FILTER_SANITIZE_NUMBER_INT)) : 0;

					$phoneids = isset($_POST["phoneid"]) ? filter_var_array($_POST["phoneid"], FILTER_SANITIZE_NUMBER_INT) : "";

					$studentActivityids = isset($_POST["studentActivityid"]) ? filter_var_array($_POST["studentActivityid"], FILTER_SANITIZE_NUMBER_INT) : "";

					$studentIrregularitiesids = isset($_POST["studentIrregularitiesid"]) ? filter_var_array($_POST["studentIrregularitiesid"], FILTER_SANITIZE_NUMBER_INT) : "";

					$count = checkSelect("*", "students", "id = " . $studentid);

					if ($count > 0) {

						if ($powers <= 2) {

							// Edit Student Data

							$studentcode = filter_var($_POST["studentcode"], FILTER_SANITIZE_NUMBER_INT);
							$studentname = filter_var($_POST["studentname"], FILTER_SANITIZE_STRING);
							$studentgrade = filter_var($_POST["studentgrade"], FILTER_SANITIZE_STRING);
							$studentclass = filter_var($_POST["studentclass"], FILTER_SANITIZE_STRING);
							$studentsecondLanguage = filter_var($_POST["studentsecondLanguage"], FILTER_SANITIZE_STRING);
							$studentnationalid = filter_var($_POST["studentnationalid"], FILTER_SANITIZE_STRING);
							$studentaddress = filter_var($_POST["studentaddress"], FILTER_SANITIZE_STRING);
							$studentbirthdate = filter_var(date('Y-m-d', strtotime($_POST["studentbirthdate"])), FILTER_SANITIZE_STRING);
							$studentfatherjob = filter_var($_POST["studentfatherjob"], FILTER_SANITIZE_STRING);

							//	Validate The Form

							$formErrors = array();

							if (empty($studentcode)) {

								$formErrors[] = "يجب ادخال  <strong>كود الطالب</strong>";

							}

							if (empty($studentname)) {

								$formErrors[] = "يجب ادخال <strong>اسم الطالب</strong>";

							}

							if (empty($studentgrade)) {

								$formErrors[] = "يجب ادخال <strong>سنة الطالب الدراسية</strong>";

							}

							if (empty($studentclass)) {

								$formErrors[] = "يجب ادخال <strong>فصل الطالب</strong>";

							}

							// Loop Into Error Array And Echo It

							foreach ($formErrors as $error) {

								echo "<div class = 'alert alert-danger'>" . $error . "</div>";

							}

							if (empty($formErrors)) {

								$isExist = checkSelect("name", "students", "name = '" . $studentname . "' AND id != " . $studentid);

								if ($isExist === 0) {

									$stmt = $con->prepare("UPDATE students SET code = ?, name = ?, grade = ?, class = ?, second_language = ?, national_id = ?, address = ?, birth_date = ?, father_job = ? WHERE id = ?");
										
									$stmt->execute(array($studentcode, $studentname, $studentgrade, $studentclass, $studentsecondLanguage, $studentnationalid, $studentaddress, $studentbirthdate, $studentfatherjob, $studentid));

								} else {

									echo "<div class = 'alert alert-danger'>هذا الطالب موجود بالفعل</div>";

								}

								if ($stmt->rowCount() > 0) {

									// Get Userid

									$userid = getSelect("id", "users", "username = '" . $_SESSION["AdminUsername"] . "'", 1, "ASC", false);

									// Add To The Update History

									$stmt = $con->prepare("INSERT INTO 
																data_updates(category, action, date, updater_id, updated_id)
															VALUES 
																('students', 'update', '" . date("Y-m-d h:i:s") . "', ?, ?)");

									$stmt->execute(array($userid['id'], $studentid));

								}

							}

							// Edit Student Phones

							if (isset($phoneids) && !empty($phoneids)) {

								foreach ($phoneids as $phoneid) {

									$phoneidnum = filter_var($_POST["phoneid"][$phoneid], FILTER_SANITIZE_NUMBER_INT);
									$phonetype = filter_var($_POST["phonetype"][$phoneid], FILTER_SANITIZE_NUMBER_INT);
									$phonenumber = filter_var($_POST["phonenumber"][$phoneid], FILTER_SANITIZE_NUMBER_INT);

									$count = checkSelect("*", "students_phones", "id = " . $phoneidnum);

									if ($count > 0) {

										$isExist = checkSelect("*", "students_phones", "phone = '" . $phonenumber . "' AND student_id = " . $studentid . " AND id != " . $phoneidnum);

										if ($isExist === 0) {

											// Update The Database With This Info

											$stmt = $con->prepare("UPDATE students_phones SET phone = ?, type = ? WHERE id = ?");
											
											$stmt->execute(array($phonenumber, $phonetype, $phoneidnum));

											if ($stmt->rowCount() > 0) {

												// Get User ID

												$userid = getSelect("id", "users", "username = '" . $_SESSION["AdminUsername"] . "'", 1, "ASC", false);

												// Add To The Update History

												$stmt = $con->prepare("INSERT INTO 
																			data_updates(category, action, date, updater_id, updated_id)
																		VALUES 
																			('students_phones', 'update', '" . date("Y-m-d h:i:s") . "', ?, ?)");

												$stmt->execute(array($userid['id'], $phoneidnum));

											}

										} else {

											echo "<div class = 'alert alert-warning'>تم إضافة هذا الرقم لهذا الطالب سابقاً</div>";

										}

									} else {

										echo "<div class = 'alert alert-danger'>هذا الهاتف غير موجود</div>";

									}

								}

							}

						} 

						if ($powers <= 3) {

							// Edit Student Activities

							if (isset($studentActivityids) && !empty($studentActivityids)) {

								foreach ($studentActivityids as $studentActivityid) {

									$studentActivityidnum = filter_var($_POST["studentActivityid"][$studentActivityid], FILTER_SANITIZE_NUMBER_INT);
									$studentActivityactivity = filter_var($_POST["studentActivityactivity"][$studentActivityid], FILTER_SANITIZE_NUMBER_INT);
									$studentActivitycomment = filter_var($_POST["studentActivitycomment"][$studentActivityid], FILTER_SANITIZE_STRING);
									$studentActivityprocedure = filter_var($_POST["studentActivityprocedure"][$studentActivityid], FILTER_SANITIZE_STRING);
									
									// The Row Count

									$count = checkSelect("*", "students_activities", "id = " . $studentActivityidnum);

									if ($count > 0) {

										//	Validate The Form

										$formErrors = array();

										if (empty($studentActivityidnum)) {

											$formErrors[] = "كود النشاط البرمجى لا يمكن أن يكون <strong>فارغ</strong>";

										}

										if (empty($studentActivityactivity)) {

											$formErrors[] = "كود نوع النشاط البرمجى لا يمكن أن يكون <strong>فارغ</strong>";

										}

										// Loop Into Error Array And Echo It

										foreach ($formErrors as $error) {

											echo "<div class = 'alert alert-danger'>" . $error . "</div>";

										}

										// Check If There's No Error Proceed The Update Operation

										if (empty($formErrors)) {

											// Check If This Activity Exists Or Not

											$isExist = checkSelect("*", "students_activities", "comment = '" . $studentActivitycomment . "' AND student_id =" . $studentid . " AND activity_id = " . $studentActivityactivity . " AND id != " . $studentActivityidnum);

											if ($isExist === 0) {

												// Update The Database With This Info

												$stmt = $con->prepare("UPDATE students_activities SET comment = ?, activity_id = ?, `procedure` = ? WHERE id = ?");
												
												$stmt->execute(array($studentActivitycomment, $studentActivityactivity, $studentActivityprocedure, $studentActivityidnum));

												if ($stmt->rowCount() > 0) {

													// Get User ID

													$userid = getSelect("id", "users", "username = '" . $_SESSION["AdminUsername"] . "'", 1, "ASC", false);

													// Add To The Update History

													$stmt = $con->prepare("INSERT INTO 
																				data_updates(category, action, date, updater_id, updated_id)
																			VALUES 
																				('students_activities', 'update', '" . date("Y-m-d h:i:s") . "', ?, ?)");

													$stmt->execute(array($userid['id'], $studentActivityidnum));

												}

											} else {

												echo "<div class = 'alert alert-warning'>تم إضافة هذا النشاط لهذا الطالب سابقاً</div>";

											}

										}

									}
									
								}

							}

							// Edit Student Irregularities

							if (isset($studentIrregularitiesids) && !empty($studentIrregularitiesids)) {

								foreach ($studentIrregularitiesids as $studentIrregularitiesid) {

									$studentIrregularitiesidnum = filter_var($_POST["studentIrregularitiesid"][$studentIrregularitiesid], FILTER_SANITIZE_NUMBER_INT);
									$studentIrregularitiesactivity = filter_var($_POST["studentIrregularitiesactivity"][$studentIrregularitiesid], FILTER_SANITIZE_NUMBER_INT);
									$studentIrregularitiescomment = filter_var($_POST["studentIrregularitiescomment"][$studentIrregularitiesid], FILTER_SANITIZE_STRING);
									$studentIrregularitiesprocedure = filter_var($_POST["studentIrregularitiesprocedure"][$studentIrregularitiesid], FILTER_SANITIZE_STRING);

									// The Row Count

									$count = checkSelect("*", "students_activities", "id = " . $studentIrregularitiesidnum);

									if ($count > 0) {

										//	Validate The Form

										$formErrors = array();

										if (empty($studentIrregularitiesidnum)) {

											$formErrors[] = "كود المخالفة البرمجى لا يمكن أن يكون <strong>فارغ</strong>";

										}

										if (empty($studentIrregularitiesactivity)) {

											$formErrors[] = "كود نوع المخالفة البرمجى لا يمكن أن يكون <strong>فارغ</strong>";

										}

										// Loop Into Error Array And Echo It

										foreach ($formErrors as $error) {

											echo "<div class = 'alert alert-danger'>" . $error . "</div>";

										}

										// Check If There's No Error Proceed The Update Operation

										if (empty($formErrors)) {

											// Check If This Irregularity Exists Or Not

											$isExist = checkSelect("*", "students_activities", "comment = '" . $studentIrregularitiescomment . "' AND student_id =" . $studentid . " AND activity_id = " . $studentIrregularitiesactivity . " AND id != " . $studentIrregularitiesidnum);

											if ($isExist === 0) {

												// Update The Database With This Info

												$stmt = $con->prepare("UPDATE students_activities SET comment = ?, activity_id = ?, `procedure` = ? WHERE id = ?");
												
												$stmt->execute(array($studentIrregularitiescomment, $studentIrregularitiesactivity, $studentIrregularitiesprocedure, $studentIrregularitiesidnum));

												if ($stmt->rowCount() > 0) {

													// Get User ID

													$userid = getSelect("id", "users", "username = '" . $_SESSION["AdminUsername"] . "'", 1, "ASC", false);

													// Add To The Update History

													$stmt = $con->prepare("INSERT INTO 
																				data_updates(category, action, date, updater_id, updated_id)
																			VALUES 
																				('students_irregularities', 'update', '" . date("Y-m-d h:i:s") . "', ?, ?)");

													$stmt->execute(array($userid['id'], $studentIrregularitiesidnum));

												}

											} else {

												echo "<div class = 'alert alert-warning'>تم إضافة هذه المخالفة لهذا الطالب سابقا</div>";

											}

										}

									}

								}

							}

						}

						echo "<div class = 'alert alert-success'>تم الحفظ بنجاح</div>";


					} else {

						echo "<div class = 'alert alert-danger'>هذا الطالب غير موجود</div>";

					}

				} else {

					redirect("<div class = 'alert alert-danger'>لا يمكنك تصفح هذه الصفحة مباشرةً</div>");

				}

			} else {

				header("Location: index.php");

			}

		} else {

			header("Location: activities.php?do=Manage&category=" . $cat_page);

		}

		if ($do != "Update") {

			include $templates . "footer.php";

		}

	} else {

		header("Location: index.php");

		exit();

	}

	ob_end_flush();	//	Output Buffering End

?>