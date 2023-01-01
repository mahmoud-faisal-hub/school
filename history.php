<?php

	ob_start();	//	Output Buffering Start

	session_start();

	if (isset($_SESSION["AdminUsername"])) {

		$do = isset($_GET['do']) ? $_GET['do'] : "History";

		if ($do != "HistorySearch") {

			$pageTitle = "سجل النشاطات";

			$pageActive = basename(__FILE__);

		} else {

			$noNavbar = "";

		}

		include "init.php";

		$userPowers = getSelect("group_id", "users", "username ='" . $_SESSION["AdminUsername"] . "'", 1, "ASC", false);

		$powers = $userPowers["group_id"];
		
		if ($powers <= 2) {

			// Start History Page

			if ($do === "History") {	// History Page 

				if (!isset($_GET['currentPage'])) {

					$currentPage = 1;

				} else {

					$currentPage = $_GET['currentPage'];

				}

				if ($powers == 1) {
		
					$countHistory = checkSelect("*", "data_updates");
				
				} else if ($powers == 2) {
					
					$countHistory = checkSelect("*", "data_updates", "category != 'users'");
					
				}

				$currentPage = filter_var($currentPage, FILTER_SANITIZE_NUMBER_INT);

				$maxItems = 10;

				if (! filter_var($currentPage, FILTER_VALIDATE_INT) || $currentPage > ceil($countHistory / $maxItems)) {

					$currentPage = 1;

				}

				$paginationData = setPagination($countHistory, $maxItems, $currentPage);
				
				if ($powers == 1) {
		
					$history = getSelect("*", "data_updates", 1, "date", "DESC LIMIT " . $paginationData['srow'] . " , " . $maxItems);
				
				} else if ($powers == 2) {
					
					$history = getSelect("*", "data_updates", "category != 'users'", "date", "DESC LIMIT " . $paginationData['srow'] . " , " . $maxItems);
					
				}

				if (! empty($history)) {

?>

					<div class = "container">
						<h1 class = "text-center">سجل النشاطات</h1>

<?php

							foreach ($history as $action) {

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
									
								} else if ($action["category"] == "users") {
																								
									$updatedName = getSelect("*", "users", "id = " . $action["updated_id"], "id", "DESC", false);
									
									$updatedName = "<a href = 'users.php?do=Edit&id=" . $updatedName["id"] . "'>" . $updatedName["full_name"] . "</a> فى قسم <a href = 'users.php'>بيانات الأعضاء</a>";
									
								}

?>

									<div class="card mb-2">
										<div class="card-header">
											<?php echo $event; ?>										
										</div>
										<div class="card-body">
											<blockquote class="blockquote mb-0">
												<p><?php echo "تم " . $event . " " . $eventCat . " " . $updatedName . " بواسطة "; if ($powers == 1) { echo "<a href = 'users.php?do=Edit&id=" . $whoAdded['id'] . "'>" . $whoAdded['full_name'] . "</a>"; } else if ($powers == 2) { echo $whoAdded["full_name"]; } ?></p>
												<footer class="blockquote-footer">بتاريخ <?php echo str_replace('-', ' / ', arabicNumbers(date('d-m-Y', strtotime($action["date"])))) . " توقيت " . arabicNumbers(date('h:i', strtotime($action["date"]))); ?></footer>
											</blockquote>
										</div>
									</div>

<?php

							}

						echo '</div>';

					echo '<footer class = "my-2">';

							// Call The Pagination Bar
					
							$paging_info = getPagination($paginationData);

					echo '</footer>';

				} else {

?>
				
					<div class = 'alert alert-warning container'><i class='fas fa-exclamation-circle'></i> لا يوجد أحداث فى سجل النشاطات</div>

<?php
					
				}

			} else if ($do === "HistorySearch") { // History Search Page

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

					$searchColumn = "name";

				} else if ($searchtype == 1) {
					
					$searchColumn = "comment";
					
				} else if ($searchtype == 2) {

					$searchColumn = "who_added";

				} else {

					$searchColumn = "name";

				}

				if (!empty($search) || $search != "") {
					
					$stmt = $con->prepare("SELECT 
												* 
											FROM 
												activities 
											WHERE 
												category = ?
											AND " . $searchColumn . " LIKE '%" . $search . "%'");

					$stmt->execute(array($category));

					$countActivities = $stmt->rowCount();

				} else {

					$stmt = $con->prepare("SELECT 
												* 
											FROM 
												activities 
											WHERE 
												category = ?");

					$stmt->execute(array($category));

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
												activities 
											WHERE 
												category = ?
											AND " . $searchColumn . " 
											LIKE '%" . $search . "%'
											ORDER BY id ASC
											LIMIT " . $paginationData['srow'] . " , " . $maxItems . "
										");

					$stmt->execute(array($category));

					$activities = $stmt->fetchAll();

				} else {
					
					$stmt = $con->prepare("
											SELECT 
												* 
											FROM 
												activities 
											WHERE 
												category = ?
											ORDER BY id ASC
											LIMIT " . $paginationData['srow'] . " , " . $maxItems . "
										");

					$stmt->execute(array($category));

					$activities = $stmt->fetchAll();

				}

				if (! empty($activities)) {

?>
					
					<div class = "activities">
						<div class="card">
							<div class="card-header">
								<i class="fas fa-edit"></i> إدارة <?php if ($category == 0) { echo "الأنشطة"; } else { echo "المخالفات"; }  ?>
							</div>
							<div class="card-body">
								<?php

									foreach ($activities as $activity) {

										echo "<div class = 'activity'>";
											if ($powers <= 3) {
												echo "<div class = 'hidden-buttons'>";
													echo "
														<a href = '?do=Edit&id=" . $activity['id'] . "&category=" . $cat_page . "' class = 'btn btn-primary btn-sm'><i class = 'fas fa-edit'></i> تعديل</a>
														<a href = '?do=Delete&id=" . $activity['id'] . "&category=" . $cat_page . "' class = 'btn btn-danger btn-sm confirm' data-toggle='modal' data-target='#delete-confirm'><i class = 'fas fa-trash-alt'></i> حذف</a>
														<a href = '?do=ActivityHistory&id=" . $activity['id'] . "&currentPage=1&category=" . $cat_page . "' class='btn btn-info btn-sm'><i class='fas fa-history'></i> تاريخ التعديل</a>";
												echo "</div>";
											}
											echo "<h3>" . $activity["name"] . "</h3>";
											echo "<p>";
													echo $activity["comment"];
											echo "</p>";
											if ($powers <= 3) {
												echo "<p>";

													if ($activity["who_added"] != "") {

														echo "أضيف بواسطة " . $activity["who_added"];

													}

												echo "</p>";
											}											
										echo "</div>";
										echo "<hr>";

									}

								?>
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
									<div class="modal-body text-right">هل انت متأكد أنك تريد حذف <?php if ($category == 0) { echo "هذا النشاط"; } else { echo "هذه المخالفة"; } ?>؟</div>
									<div class="modal-footer">
										<button type="submit" class="btn btn-primary delete-confirm-true">حذف</button>
										<button type="button" class="btn btn-secondary" data-dismiss="modal">إلغاء</button>
									</div>
								</div>
							</div>
						</div>
					</form>
					<?php if ($powers <= 3) { ?><a href = "?do=Add&category=<?php if ($category == 0) { echo "Activities"; } else { echo "Irregularities"; } ?>"  class = "btn btn-primary"><i class="fas fa-plus"></i> إضافة <?php if ($category == 0) { echo "نشاط"; } else { echo "مخالفة"; } ?></a> <?php } ?>
					<footer class = "my-2 searchFooter">
						<?php

							// Call The Pagination Bar
					
							$paging_info = getPagination($paginationData);

						?>
					</footer>

<?php

				} else {
					
?>

					<div class = 'alert alert-warning container'><i class='fas fa-exclamation-circle'></i> لا يوجد <?php if ($category == 0) { echo "أنشطة"; } else { echo "مخالفات"; } ?> بهذه المواصفات <?php if ($powers <= 3) { ?> هل تريد <a href = '?do=Add&category=<?php if ($category == 0) { echo "Activities"; } else { echo "Irregularities"; } ?>'  class = 'badge badge-pill badge-primary'><i class='fas fa-plus'></i> إضافة <?php if ($category == 0) { echo "نشاط"; } else { echo "مخالفة"; } ?></a> <?php if ($category == 0) { echo "جديد؟"; } else { echo "جديدة؟"; } } ?></div>

<?php
					
				}

			} else {

				header("Location: history.php?do=History");

			}

			if ($do != "HistorySearch") {

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