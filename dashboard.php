<?php

	ob_start();	//	Output Buffering Start

	session_start();

	if (isset($_SESSION["AdminUsername"])) {

		$pageTitle = "لوحة التحكم";
		$pageActive = basename(__FILE__);

		include "init.php";

		/* Start Dashboard Page */

		$userPowers = getSelect("group_id", "users", "username ='" . $_SESSION["AdminUsername"] . "'", 1, "ASC", false);

		$powers = $userPowers["group_id"];

		$numStudents = 5;

		$latestStudents = getLatest("*", "students", 1, "id", $numStudents);

		$numActivities = 5;

		$latestActivities = getLatest("*", "activities", "category = 0", "id", $numActivities);

		$numIrregularities = 5;

		$latestIrregularities = getLatest("*", "activities", "category = 1", "id", $numIrregularities);

		$numActions = 5;

		if ($powers == 1) {
		
			$latestActions = getLatest("*", "data_updates", 1, "date", $numActions);
		
		} else if ($powers == 2) {
			
			$latestActions = getLatest("*", "data_updates", "category != 'users'", "date", $numActions);
			
		}

		if ($powers == 1) {

			$numUsers = 5;

			$latestUsers = getLatest("*", "users", 1, "id", $numUsers);

		}

?>

		<section class = "statistics">
			<div class = "container">
				<h1 class = "text-center">الصفحة الرئيسية</h1>
				<div class="row">
			        <div class="<?php if ($powers == 1) { echo "col-lg-3 col-md-6"; } else if ($powers >= 2) { echo "col-lg-4 col-md-6"; } ?>">
						<div class="small-box" style = "background-color: #00c0ef;">
							<div class="inner">
								<p>عدد الطلاب</p>
								<h3><?php echo arabicNumbers(checkSelect("id", "students")); ?></h3>
							</div>
							<div class="icon">
								<i class="fas fa-user-graduate"></i>
							</div>
							<a href="students.php" class="small-box-footer">بيانات الطلاب <i class="fas fa-arrow-circle-left"></i></a>
						</div>
			        </div>
			        <div class="<?php if ($powers == 1) { echo "col-lg-3 col-md-6"; } else if ($powers >= 2) { echo "col-lg-4 col-md-6"; } ?>">
						<div class="small-box" style = "background-color: #00a65a;">
							<div class="inner">
								<p>عدد النشيطين</p>
								<?php 

									$countStudentsActivities = checkSelect("*", "students", "
										EXISTS (SELECT 
							            	* 
							            FROM 
							            	students_activities 
							            WHERE  students_activities.student_id = students.id
							           	AND 
							            EXISTS (SELECT
							                    	*
							                    FROM
							                    	activities
							                    WHERE
							                    	activities.category = 0
							                	AND
							                		activities.id = students_activities.activity_id    
							                )
							           )
									");

								?>
								<h3><?php echo arabicNumbers($countStudentsActivities); ?></h3>
							</div>
							<div class="icon">
								<i class="fas fa-thumbs-up"></i>
							</div>
							<a href="students-activities.php?category=Activities" class="small-box-footer">أنشطة الطلاب <i class="fas fa-arrow-circle-left"></i></a>
						</div>
			        </div>
					<div class="<?php if ($powers == 1) { echo "col-lg-3 col-md-6"; } else if ($powers >= 2) { echo "col-lg-4 col-md-12"; } ?>">
						 <div class="small-box" style = "background-color: #dd4b39;">
						    <div class="inner">
						    	<p>عدد المخالفين</p>
						    	<?php 

									$countStudentsIrregularities = checkSelect("*", "students", "
										EXISTS (SELECT 
							            	* 
							            FROM 
							            	students_activities 
							            WHERE  students_activities.student_id = students.id
							           	AND 
							            EXISTS (SELECT
							                    	*
							                    FROM
							                    	activities
							                    WHERE
							                    	activities.category = 1
							                	AND
							                		activities.id = students_activities.activity_id    
							                )
							           )
									");

								?>
						    	<h3><?php echo arabicNumbers($countStudentsIrregularities); ?></h3>
						    </div>
						    <div class="icon">
						    	<i class="fas fa-thumbs-down"></i>
						    </div>
						    <a href="students-activities.php?category=Irregularities" class="small-box-footer">مخالفات الطلاب <i class="fas fa-arrow-circle-left"></i></a>
						</div>
					</div>
					<?php

						if ($powers == 1) {

					?>
							<div class="col-lg-3 col-md-6">
								 <div class="small-box" style = "background-color: #f39c12;">
								    <div class="inner">
								    	<p>عدد الأعضاء</p>
								    	<h3><?php echo arabicNumbers(checkSelect("id", "users")); ?></h3>
								    </div>
								    <div class="icon">
								    	<i class="fas fa-users"></i>
								    </div>
								    <a href="users.php" class="small-box-footer">بيانات الأعضاء <i class="fas fa-arrow-circle-left"></i></a>
								</div>
							</div>
					<?php

						}

					?>
	       		</div>
	  		</div>
	  	</section>

	  	<section class = "latest">
			<div class = "container">
				<div class = "row">
					<div class = "col-md-6">
						<div class="card">
							<div class="card-header">
								<i class="fas fa-user-graduate"></i>
								أخر <?php echo arabicNumbers($numStudents); ?> طلاب تم إضافتهم
								<span class = "toggle-info float-left">
									<i class="fas fa-minus-square"></i>
								</span>
							</div>
							<div class="card-body">
								<div class = "container p-0">
									
									<?php

										if (! empty($latestStudents)) {

											foreach ($latestStudents as $student) {

												echo "<ul class = 'list-unstyled latest-items'>";
													echo "<li>" . $student["name"];
														if ($powers <= 2) {
															echo "
																<a href = 'students.php?do=Delete&id=" . $student["id"] . "' class = 'btn btn-sm btn-danger confirm' data-toggle='modal' data-target='#delete-confirm'><i class='fas fa-trash-alt'></i> حذف</a>
																<a href = 'students.php?do=Edit&id=" . $student["id"] . "' class = 'btn btn-sm btn-success'><i class='fas fa-edit'></i> تعديل</a> 
															";
														}
													echo "</li>";
												echo "</ul>";

											}

										} else {

											echo "<div class = 'alert alert-info mt-3'>لا يوجد طلاب لعرضهم</div>";

										} 

									?>

								</div>
							</div>
						</div>	
					</div>
					<div class = "col-md-6">
						<div class="card">
							<div class="card-header">
								<i class="fas fa-thumbs-up"></i>
								أخر <?php echo arabicNumbers($numActivities); ?> أنشطة تم إضافتهم
								<span class = "toggle-info float-left">
									<i class="fas fa-minus-square"></i>
								</span>
							</div>
							<div class="card-body">
								<div class = "container p-0">
									
									<?php

										if (! empty($latestActivities)) {

											foreach ($latestActivities as $activity) {

												echo "<ul class = 'list-unstyled latest-items'>";
													echo "<li>" . $activity["name"];
														if ($powers <= 3) {
															echo "
																<a href = 'activities.php?do=Delete&id=" . $activity["id"] . "&category=Activities' class = 'btn btn-sm btn-danger confirm' data-toggle='modal' data-target='#delete-confirm'><i class='fas fa-trash-alt'></i> حذف</a>
																<a href = 'activities.php?do=Edit&id=" . $activity["id"] . "&category=Activities' class = 'btn btn-sm btn-success'><i class='fas fa-edit'></i> تعديل</a> 
															";
														}
													echo "</li>";
												echo "</ul>";

											}

										} else {

											echo "<div class = 'alert alert-info mt-3'>لا يوجد أنشطة لعرضها</div>";

										} 

									?>

								</div>
							</div>
						</div>	
					</div>
					<div class = "col-md-6">
						<div class="card">
							<div class="card-header">
								<i class="fas fa-thumbs-down"></i>
								أخر <?php echo arabicNumbers($numIrregularities); ?> مخالفات تم إضافتهم
								<span class = "toggle-info float-left">
									<i class="fas fa-minus-square"></i>
								</span>
							</div>
							<div class="card-body">
								<div class = "container p-0">
									
									<?php

										if (! empty($latestIrregularities)) {

											foreach ($latestIrregularities as $infraction) {

												echo "<ul class = 'list-unstyled latest-items'>";
													echo "<li>" . $infraction["name"];
														if ($powers <= 3) {
															echo "
																<a href = 'activities.php?do=Delete&id=" . $infraction["id"] . "&category=Irregularities' class = 'btn btn-sm btn-danger confirm' data-toggle='modal' data-target='#delete-confirm'><i class='fas fa-trash-alt'></i> حذف</a>
																<a href = 'activities.php?do=Edit&id=" . $infraction["id"] . "&category=Irregularities' class = 'btn btn-sm btn-success'><i class='fas fa-edit'></i> تعديل</a> 
															";
														}
													echo "</li>";
												echo "</ul>";

											}

										} else {

											echo "<div class = 'alert alert-info mt-3'>لا يوجد أنشطة لعرضها</div>";

										} 

									?>

								</div>
							</div>
						</div>	
					</div>
					<?php

						if ($powers == 1) {

					?>
							<div class = "col-md-6">
								<div class="card">
									<div class="card-header">
										<i class="fas fa-users"></i>
										أخر <?php echo arabicNumbers($numUsers); ?> أعضاء
										<span class = "toggle-info float-left">
											<i class="fas fa-minus-square"></i>
										</span>
									</div>
									<div class="card-body">
										<div class = "container p-0">
											
											<?php

												if (! empty($latestUsers)) {

													foreach ($latestUsers as $user) {

														echo "<ul class = 'list-unstyled latest-items'>";
															echo "<li>" . $user["full_name"];
																echo "
																	<a href = 'users.php?do=Delete&id=" . $user["id"] . "' class = 'btn btn-sm btn-danger confirm' data-toggle='modal' data-target='#delete-confirm'><i class='fas fa-trash-alt'></i> حذف</a>
																	<a href = 'users.php?do=Edit&id=" . $user["id"] . "' class = 'btn btn-sm btn-success'><i class='fas fa-edit'></i> تعديل</a> 
																";
															echo "</li>";
														echo "</ul>";

													}

												} else {

													echo "<div class = 'alert alert-info mt-3'>لا يوجد طلاب لعرضهم</div>";

												} 

											?>

										</div>
									</div>
								</div>	
							</div>
					<?php

						}

					?>
					<?php

						if ($powers <= 2) {

					?>
							<div class = "col-md-12">
								<div class="card">
									<div class="card-header">
										<i class="fas fa-history"></i>
										أخر <?php echo arabicNumbers($numActions); ?> أحداث
										<span class = "toggle-info float-left">
											<i class="fas fa-minus-square"></i>
										</span>
									</div>
									<div class="card-body" style = "background-color: #eeeeee;">
										<div class = "container p-0">
											
											<?php

												if (! empty($latestActions)) {

													foreach ($latestActions as $action) {

														if ($action["category"] == "students") {

															$eventCat = "بيانات الطالب";

														} else if ($action["category"] == "activities") {

															$eventCat = "نشاط";

														} else if ($action["category"] == "irregularities") {

															$eventCat = "مخالفة";

														} else if ($action["category"] == "students_activities") {

															$eventCat = "النشاط";

														} else if ($action["category"] == "students_irregularities") {

															$eventCat = "المخالفة";

														} else if ($action["category"] == "students_phones") {

															$eventCat = "الهاتف";

														} else if ($action["category"] == "users") {

															$eventCat = "بيانات العضو";

														} else if ($action["category"] == "users_images") {

															$eventCat = "صورة العضو";

														} else {
															
															$eventCat = "";
															
														}
														
														if ($action["action"] == "add") {
															
															$event = "إضافة";
															
														} else if ($action["action"] == "update") {

															$event = "تعديل";

														} else if ($action["action"] == "delete") {

															$event = "حذف";

														} else {
															
															$event = "";
															
														}
														
														$whoAdded = getSelect("*", "users", "id = " . $action["updater_id"], "id", "DESC", false);
														
														if ($action["category"] == "students") {
																														
															$updatedName = getSelect("*", "students", "id = " . $action["updated_id"], "id", "DESC", false);
															
															$updatedName = "<a href = 'students.php?do=Edit&id=" . $updatedName["id"] . "'>" . $updatedName["name"] . "</a> فى قسم <a href = 'students.php'>بيانات الطلاب</a>";
															
														} else if ($action["category"] == "activities" || $action["category"] == "irregularities") {
																														
															$updatedName = getSelect("*", "activities", "id = " . $action["updated_id"], "id", "DESC", false);
															
															if ($action["category"] == "activities") {
																
																$actionCategoryName = "الأنشطة";
																
																$actionCategoryType = "Activities";
																
															} else if ($action["category"] == "irregularities") {
																
																$actionCategoryName = "المخالفات";
																
																$actionCategoryType = "Irregularities";
																
															} else {
																
																$actionCategoryName = "الأنشطة";
																
																$actionCategoryType = "Activities";
																
															}
															
															$updatedName = "<a href = 'activities.php?do=Edit&id=" . $updatedName["id"] . "&category=" . $actionCategoryType . "'>" . $updatedName["name"] . "</a> فى قسم <a href = 'activities.php?category=" . $actionCategoryType . "'>" . $actionCategoryName . "</a>";
															
														} else if ($action["category"] == "students_activities" || $action["category"] == "students_irregularities") {
																														
															$studentsActivitiesData = getSelect("*", "students_activities", "id = " . $action["updated_id"], "id", "DESC", false);
															
															$activityName = getSelect("*", "activities", "id = " . $studentsActivitiesData["activity_id"], "id", "DESC", false);
															
															$studentName = getSelect("*", "students", "id = " . $studentsActivitiesData["student_id"], "id", "DESC", false);
															
															if ($action["category"] == "students_activities") {
																
																$actionCategoryName = "أنشطة الطالب";
																
																$actionCategoryType = "Activities";
																
															} else if ($action["category"] == "students_irregularities") {
																
																$actionCategoryName = "مخالفات الطالب";
																
																$actionCategoryType = "Irregularities";
																
															} else {
																
																$actionCategoryName = "أنشطة الطالب";
																
																$actionCategoryType = "Activities";
																
															}
															
															$updatedName = "<a href = 'students.php?do=EditActivity&id=" . $studentsActivitiesData["id"] . "&category=" . $actionCategoryType . "'>" . $activityName["name"] . "</a> للطالب <a href = 'students.php?do=Edit&id=" . $studentName["id"] . "'>" . $studentName["name"] . "</a> فى قسم <a href = 'students.php?do=Activities&id=" . $studentName["id"] . "&category=" . $actionCategoryType . "'>" . $actionCategoryName . "</a>";
															
														} else if ($action["category"] == "students_phones") {
																														
															$studentsPhonesData = getSelect("*", "students_phones", "id = " . $action["updated_id"], "id", "DESC", false);
															
															$studentName = getSelect("*", "students", "id = " . $studentsPhonesData["student_id"], "id", "DESC", false);
															
															$updatedName = "<a href = 'students.php?do=EditPhone&id=" . $studentsPhonesData["id"] . "'>" . $studentsPhonesData["phone"] . "</a> للطالب <a href = 'students.php?do=Edit&id=" . $studentName["id"] . "'>" . $studentName["name"] . "</a> فى قسم <a href = 'students.php?do=Phones&id=" . $studentName["id"] . "'>هواتف الطالب</a> ";
															
														} else if ($action["category"] == "users" || $action["category"] == "users_images") {
																														
															$updatedName = getSelect("*", "users", "id = " . $action["updated_id"], "id", "DESC", false);
															
															$updatedName = "<a href = 'users.php?do=Edit&id=" . $updatedName["id"] . "'>" . $updatedName["full_name"] . "</a> فى قسم <a href = 'users.php'>بيانات الأعضاء</a>";
															
														}
	
?>
	
														<div class="card m-2">
															<div class="card-header">
																<?php echo $event; ?>										
															</div>
															<div class="card-body p-4">
																<blockquote class="blockquote mb-0">
																	<p><?php echo "تم " . $event . " " . $eventCat . " " . $updatedName . " بواسطة "; if ($powers == 1) { echo "<a href = 'users.php?do=Edit&id=" . $whoAdded['id'] . "'>" . $whoAdded['full_name'] . "</a>"; } else if ($powers == 2) { echo $whoAdded["full_name"]; } ?></p>
																	<footer class="blockquote-footer">بتاريخ <?php echo str_replace('-', ' / ', arabicNumbers(date('d-m-Y', strtotime($action["date"])))) . " توقيت " . arabicNumbers(date('h:i', strtotime($action["date"]))); ?></footer>
																</blockquote>
															</div>
														</div>

<?php

													}

												} else {

													echo "<div class = 'alert alert-info mt-3'>لا يوجد طلاب لعرضهم</div>";

												} 

											?>

										</div>
									</div>
								</div>	
							</div>
					<?php

						}

					?>
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
		</section>

<?php

		include $templates . "footer.php";

	} else {

		header("Location: index.php");

		exit();

	}

	ob_end_flush();	//	Output Buffering End

?>