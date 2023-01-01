<?php

	ob_start();	//	Output Buffering Start

	session_start();

	if (isset($_SESSION["AdminUsername"])) {

		$do = isset($_GET['do']) ? $_GET['do'] : "Manage";

		$category = isset($_GET["category"]) && ($_GET["category"] == "Activities" || $_GET["category"] == "Irregularities") ? $_GET["category"] : "Activities";
		$cat_page = $category;
		$category = $category == "Activities" ? 0 : 1;

		if ($do != "ActivitySearch" && $do != "Insert" && $do != "Update") {

			$pageTitle = $category === 1? "المخالفات" : "الأنشطة";

			$pageActive = basename(__FILE__) . "?" . $cat_page;

		} else {

			$noNavbar = "";

		}

		include "init.php";

		$userPowers = getSelect("group_id", "users", "username ='" . $_SESSION["AdminUsername"] . "'", 1, "ASC", false);

		$powers = $userPowers["group_id"];

		// Start Manage Page

		if ($do === "Manage") {	// Manage Members Page 

			if (!isset($_GET['currentPage'])) {

				$currentPage = 1;

			} else {

				$currentPage = $_GET['currentPage'];

			}

			$countActivities = checkSelect("*", "activities", "category = " . $category);

			$currentPage = filter_var($currentPage, FILTER_SANITIZE_NUMBER_INT);

			$maxItems = 10;

			if (! filter_var($currentPage, FILTER_VALIDATE_INT) || $currentPage > ceil($countActivities / $maxItems)) {

				$currentPage = 1;

			}

			$paginationData = setPagination($countActivities, $maxItems, $currentPage);

			$activities = getSelect("*", "activities", "category = " . $category, 1, "ASC LIMIT " . $paginationData['srow'] . " , " . $maxItems);
			
			if (! empty($activities)) {

?>
				<div class = "container text-right">
					<h1 class = "text-center"><?php if ($category === 0) { echo "الأنشطة"; } else { echo "المخالفات"; } ?></h1>
					<form class = "SearchForm ajax-form" action = "?do=ActivitySearch&category=<?php echo $cat_page; ?>" method = "POST" enctype = "multipart/form-data">
						<!--Start Student Code Field-->
						<div class = "form-group row justify-content-md-center">
							<div class = "col-md-4 pl-md-0 search">
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
						<?php if ($powers <= 3) { ?><a href = "?do=Add&category=<?php if ($category == 0) { echo "Activities"; } else { echo "Irregularities"; } ?>"  class = "btn btn-primary"><i class="fas fa-plus"></i> إضافة <?php if ($category == 0) { echo "نشاط"; } else { echo "مخالفة"; } ?></a><?php } ?>
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

?>
			
				<div class = 'alert alert-warning container'><i class='fas fa-exclamation-circle'></i> لا يوجد <?php if ($category == 0) { echo 'أنشطة'; } else { echo 'مخالفات'; } if ($powers <= 3) { ?> هل تريد <a href = "?do=Add&category=<?php if ($category == 0) { echo "Activities"; } else { echo "Irregularities"; } ?>"  class = 'badge badge-pill badge-primary'><i class='fas fa-plus'></i> إضافة <?php if ($category == 0) { echo 'نشاط'; } else { echo 'مخالفة'; } ?></a> <?php if ($category == 0) { echo 'جديد؟'; } else { echo 'جديدة؟'; } }?></div>

<?php
				
			}

		} else if ($do === "Add") { // Add Page

			if ($powers <= 3) {

				$category = isset($_GET["category"]) && ($_GET["category"] == "Activities" || $_GET["category"] == "Irregularities") ? $_GET["category"] : "Activities";
				$category = $category == "Activities" ? 0 : 1;

?>

				<h1 class = "text-center">إضافة <?php if ($category == 0) { echo "نشاط جديد"; } else { echo "مخالفة جديدة"; } ?></h1>
				<div class = "container text-right">
					<form class = "ajax-form" action = "?do=Insert" method = "POST" enctype = "multipart/form-data">
						<input type = "hidden" name = "category" value = "<?php echo $category; ?>" />
						<!--Start Name Field-->
						<div class = "form-group row justify-content-md-center">
							<label class = "col-md-2 control-label">اسم <?php if ($category == 0) { echo "النشاط"; } else { echo "المخالفة"; } ?></label>
							<div class = "col-md-4">
								<input type = "text" name = "name" class = "form-control" autocomplete = "off" placeholder = "اسم <?php if ($category == 0) { echo "النشاط"; } else { echo "المخالفة"; } ?>" required />
							</div>
						</div>
						<!--End Name Field-->
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

				header("Location: activities.php");

			}

		} else if ($do === "Insert") {	//	Insert Page

			if ($powers <= 3) {

				if ($_SERVER['REQUEST_METHOD'] === "POST") {
					
					// Get Variables From  The Form

					$name = filter_var($_POST["name"], FILTER_SANITIZE_STRING);
					$comment = filter_var($_POST["comment"], FILTER_SANITIZE_STRING);
					
					$cat = filter_var($_POST["category"], FILTER_SANITIZE_NUMBER_INT);
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

						if (empty($name)) {
							
							$theMsg = "اسم ";
							
							if ($category == 0) {
								
								$theMsg .= "النشاط"; 
								
							} else { 
							
								$theMsg .= "المخالفة"; 
								
							}
							
							$theMsg .= " لا يجب أن يكون ";

							$formErrors[] = $theMsg . " <strong>فارغ</strong>";

						}

						// Loop Into Error Array And Echo It

						foreach ($formErrors as $error) {

							echo "<div class = 'alert alert-danger'>" . $error . "</div>";

						}

						// Check If There's No Error Proceed The Update Operation

						if (empty($formErrors)) {
								
							// Get Who Added

							$who_added = getSelect("full_name", "users", "username = '" . $_SESSION["AdminUsername"] . "'", 1, "ASC", false);

							// Check If This Activity Exists Or Not

							$isActivityExist = checkSelect("*", "activities", "name = '" . $name . "' AND category = " . $category);

							if ($isActivityExist === 0) {

								// Insert Into Database With This Info

								$stmt = $con->prepare("INSERT INTO 
															activities(name, comment, who_added, category)
														VALUES 
															(?, ?, ?, ?)");

								$stmt->execute(array($name, $comment, $who_added['full_name'], $category));

								if ($stmt->rowCount() > 0) {

									// Get User ID

									$userid = getSelect("id", "users", "username = '" . $_SESSION["AdminUsername"] . "'", 1, "ASC", false);

									// Get Activity ID

									$activityid = getSelect("id", "activities", "name = '" . $name . "'", 1, "ASC", false);

									// Add To The Update History

									$stmt = $con->prepare("INSERT INTO 
																data_updates(category, action, date, updater_id, updated_id)
															VALUES 
																(?, 'add', ?, ?, ?)");
										
									$history_cat = "";
										
									if ($category == 0) {
										
										$history_cat = "activities"; 
										
									} else { 
									
										$history_cat = "irregularities";
										
									}

									$stmt->execute(array( $history_cat, date("Y-m-d h:i:s"), $userid['id'], $activityid['id'] ));

								}
								
								// Echo Success Message

								$theMsg = "<div class = 'alert alert-success'>تم إضافة ";
								
								if ($category == 0) {
									
									$theMsg .= "النشاط"; 
									
								} else { 
								
									$theMsg .= "المخالفة"; 
									
								}
								
								$theMsg .= " بنجاح </div>";
								
								$theMsg2 = "صفحة ";
								
								if ($category == 0) {
									
									$theMsg2 .= "الأنشطة"; 
									
								} else { 
								
									$theMsg2 .= "المخالفات"; 
									
								}

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
									
									$theMsg2 .= "الأنشطة"; 
									
								} else { 
								
									$theMsg2 .= "المخالفات"; 
									
								}
								
								echo "<div class = 'alert alert-warning'>تم إضافة " . $theMsg . " من قبل</div>";
								

							}
							
						}

					echo "</div>";

				} else {

					redirect("<div class = 'alert alert-danger'>لا يمكنك تصفح هذه الصفحة مباشرةً</div>");

				}

			} else {

				header("Location: activities.php");

			}

		} else if ($do === "Edit") {	//	Edit Page 

			if ($powers <= 3) {

				// Check If Get Request Phone ID Is Numeric & Get The Integer Value Of It

				$id = isset($_GET["id"]) && is_numeric(filter_var($_GET["id"], FILTER_SANITIZE_NUMBER_INT)) ? intval($_GET["id"]) : 0;

				$category = isset($_GET["category"]) && ($_GET["category"] == "Activities" || $_GET["category"] == "Irregularities") ? $_GET["category"] : "Activities";
				$cat_page = $category;
				$category = $category == "Activities" ? 0 : 1;

				// The Row Count

				$count = checkSelect("*", "activities", "id = " . $id);

				// If There Is Such ID Show The Form

				if ($count > 0) {

					$studentActivity = getSelect("*", "activities", "id = " . $id, 1, "ASC", false);

?>
					
					<h1 class = "text-center">تعديل <?php if ($category == 0) { echo "النشاط"; } else { echo "المخالفة"; } ?></h1>
					<div class = "container text-right">
						<form class = "ajax-form" action = "?do=Update" method = "POST" enctype = "multipart/form-data">
							<input type = "hidden" name = "id" value = "<?php echo $id; ?>" />
							<input type = "hidden" name = "category" value = "<?php echo $cat_page; ?>" />
							<!--Start Name Field-->
							<div class = "form-group row justify-content-md-center">
								<label class = "col-md-2 control-label">اسم <?php if ($category == 0) { echo "النشاط"; } else { echo "المخالفة"; } ?></label>
								<div class = "col-md-4">
									<input type = "text" name = "name" class = "form-control" autocomplete = "off" placeholder = "اسم <?php if ($category == 0) { echo "النشاط"; } else { echo "المخالفة"; } ?>" value = "<?php echo $studentActivity['name']; ?>" />
								</div>
							</div>
							<!--End Name Field-->
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
					
					$theMsg = "";
								
					if ($category == 0) {
						
						$theMsg = "هذا النشاط غير موجود"; 
						
					} else { 
					
						$theMsg = "هذه المخالفة غير موجودة"; 
						
					}
					
					$theMsg2 = "صفحة";
								
					if ($category == 0) {
						
						$theMsg2 .= " الأنشطة"; 
						
					} else { 
					
						$theMsg2 .= " المخالفات"; 
						
					}

					redirect("<div class = 'alert alert-danger'>" . $theMsg . "</div>", 3, "activities.php?category=" . $cat_page, $theMsg2);

				}

			} else {

				header("Location: activities.php");

			}

		} else if ($do === "Update") {	//	Update Page

			if ($powers <= 3) {

				if ($_SERVER['REQUEST_METHOD'] === "POST") {

					// Check If Post Request Student ID Is Numeric & Get The Integer Value Of It

					$id = isset($_POST["id"]) && is_numeric(filter_var($_POST["id"], FILTER_SANITIZE_NUMBER_INT)) ? intval($_POST["id"]) : 0;

					// The Row Count

					$count = checkSelect("*", "activities", "id = " . $id);

					// If There Is Such ID Show The Form

					if ($count > 0) {

						echo "<div class = 'container text-right'>";

							// Get Variables From  The Form

							$id = filter_var($_POST["id"], FILTER_SANITIZE_NUMBER_INT);
							$name = filter_var($_POST["name"], FILTER_SANITIZE_STRING);
							$comment = filter_var($_POST["comment"], FILTER_SANITIZE_STRING);

							$category = isset($_POST["category"]) && ($_POST["category"] == "Activities" || $_POST["category"] == "Irregularities") ? $_POST["category"] : "Activities";
							$cat_page = $category;
							$category = $category == "Activities" ? 0 : 1;

							//	Validate The Form

							$formErrors = array();

							if (empty($id)) {
								
								$theMsg = "";
								
								if ($category == 0) {
									
									$theMsg = "النشاط"; 
									
								} else { 
								
									$theMsg = "المخالفة"; 
									
								}

								$formErrors[] = "كود " . $theMsg . "  لا يمكن أن يكون <strong>فارغ</strong>";

							}

							if (empty($name)) {
								
								$theMsg = "";
								
								if ($category == 0) {
									
									$theMsg = "النشاط"; 
									
								} else { 
								
									$theMsg = "المخالفة"; 
									
								}

								$formErrors[] = "اسم " . $theMsg . " لا يمكن أن يكون <strong>فارغ</strong>";

							}

							// Loop Into Error Array And Echo It

							foreach ($formErrors as $error) {

								echo "<div class = 'alert alert-danger'>" . $error . "</div>";

							}

							// Check If There's No Error Proceed The Update Operation

							if (empty($formErrors)) {

								// Check If This User Exists Or Not

								$isExist = checkSelect("*", "activities", "name = '" . $name . "'");

								if ($isExist === 0) {

									// Update The Database With This Info

									$stmt = $con->prepare("UPDATE activities SET name = ?, comment = ? WHERE id = ?");
									
									$stmt->execute(array($name, $comment, $id));

									if ($stmt->rowCount() > 0) {

										// Get User ID

										$userid = getSelect("id", "users", "username = '" . $_SESSION["AdminUsername"] . "'", 1, "ASC", false);

										// Add To The Update History

										$stmt = $con->prepare("INSERT INTO 
																	data_updates(category, action, date, updater_id, updated_id)
																VALUES 
																	(?, 'update', ?, ?, ?)");

										$history_cat = "";
												
										if ($category == 0) {
											
											$history_cat = "activities"; 
											
										} else { 
										
											$history_cat = "irregularities";
											
										}

										$stmt->execute(array($history_cat, date("Y-m-d h:i:s"), $userid['id'], $id));

									}

									// Echo Success Message
									
									$theMsg = "<div class = 'alert alert-success'>تم تعديل ";
										
									if ($category == 0) {
										
										$theMsg .= "النشاط"; 
										
									} else { 
									
										$theMsg .= "المخالفة"; 
										
									}
									
									$theMsg .= " بنجاح</div>";
									
									$theMsg2 = "صفحة ";
									
									if ($category == 0) {
										
										$theMsg2 .= "الأنشطة"; 
										
									} else { 
									
										$theMsg2 .= "المخالفات"; 
										
									}
									
									echo $theMsg;

								} else {
									
									$theMsg = "<div class = 'alert alert-warning'>تم إضافة ";
										
									if ($category == 0) {
										
										$theMsg .= "هذا النشاط"; 
										
									} else { 
									
										$theMsg .= "هذه المخالفة"; 
										
									}
									
									$theMsg .= " من قبل</div>";

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
						
						$theMsg2 = "صفحة ";
										
						if ($category == 0) {
							
							$theMsg2 .= "الأنشطة"; 
							
						} else { 
						
							$theMsg2 .= "المخالفات"; 
							
						}

						echo $theMsg;

					}

				} else {

					redirect("<div class = 'alert alert-danger'>لا يمكنك تصفح هذه الصفحة مباشرةً</div>");

				}

			} else {

				header("Location: activities.php");

			}

		} else if ($do === "Delete") {	// Delete Page

			if ($powers <= 3) {

				// Check If Get Request Phone ID Is Numeric & Get The Integer Value Of It

				$activityid = isset($_GET["id"]) && is_numeric($_GET["id"]) ? intval($_GET["id"]) : 0;
			
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

					$isExist = checkSelect("id", "activities", "id = " . $activityid);

					// If There Is Such ID Show The Form

					if ($isExist > 0) { 

						// Delete Student

						$stmt = $con->prepare("DELETE FROM activities WHERE id = :id");

						$stmt->bindParam(":id", $activityid);

						$stmt->execute();
						
						if ($stmt->rowCount() > 0) {

							// Add To The Update History

							$stmt = $con->prepare("DELETE FROM 
														data_updates
													WHERE 
														updated_id = ? AND category = ?");
								
							$history_cat = "";
								
							if ($category == 0) {
								
								$history_cat = "activities"; 
								
							} else { 
							
								$history_cat = "irregularities";
								
							}

							$stmt->execute(array($activityid, $history_cat));

						}

						$theMsg = "<div class = 'alert alert-success'>تم حذف ";
									
						if ($category == 0) {
							
							$theMsg .= "هذا النشاط"; 
							
						} else { 
						
							$theMsg .= "هذه المخالفة"; 
							
						}
						
						$theMsg .= " بنجاح</div>";
						
						$theMsg2 = "صفحة ";
									
						if ($category == 0) {
							
							$theMsg2 .= "الأنشطة"; 
							
						} else { 
						
							$theMsg2 .= "المخالفات"; 
							
						}

						redirect($theMsg, 3, "activities.php?category=" . $cat_page, $theMsg2);

							
					} else {
						
						$theMsg = "<div class = 'alert alert-danger'>";
										
						if ($category == 0) {
							
							$theMsg .= "هذا النشاط غير موجود"; 
							
						} else { 
						
							$theMsg .= "هذه المخالفة غير موجودة"; 
							
						}
						
						$theMsg .= "</div>";

						// If There Is No Such ID Show Error Message

						redirect("<div class = 'alert alert-danger'>هذا الهاتف غير موجود</div>", 3, "activities.php?category=" . $cat_page, "صفحة بيانات الطلاب");

					}

				echo "</div>";

			} else {

				header("Location: activities.php");

			}

		} else if ($do === "ActivityHistory") {	// Student History Page

			if ($powers <= 3) {

				$category = isset($_GET["category"]) && ($_GET["category"] == "Activities" || $_GET["category"] == "Irregularities") ? $_GET["category"] : "Activities";
				$cat = $category == "Activities" ? 0 : 1;
				$category = $category == "Activities" ? "activities" : "irregularities";

				if (!isset($_GET['currentPage'])) {

					$currentPage = 1;

				} else {

					$currentPage = $_GET['currentPage'];

				}

				// Check If Get Request Phone ID Is Numeric & Get The Integer Value Of It

				$activityid = isset($_GET["id"]) && is_numeric($_GET["id"]) ? intval($_GET["id"]) : 0;

				// Check If This Student Exists Or Not

				$isExist = checkSelect("id", "activities", "id = " . $activityid);

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
					
					$theMsg .= "</div>";
					
					$theMsg2 = "صفحة ";
									
					if ($category == 0) {
						
						$theMsg2 .= "الأنشطة"; 
						
					} else { 
					
						$theMsg2 .= "المخالفات"; 
						
					}

					redirect($theMsg, 3, "students.php", "صفحة بيانات الطلاب");

				}

			} else {

				header("Location: activities.php");

			}

		} else if ($do === "ActivitySearch") { // Activities Search Page

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

			header("Location: activities.php?do=Manage&category=" . $cat_page);

		}

		if ($do != "ActivitySearch" && $do != "Insert" && $do != "Update") {

			include $templates . "footer.php";

		}

	} else {

		header("Location: index.php");

		exit();

	}

	ob_end_flush();	//	Output Buffering End

?>