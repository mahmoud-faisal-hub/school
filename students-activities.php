<?php

	ob_start();	//	Output Buffering Start

	session_start();

	if (isset($_SESSION["AdminUsername"])) {

		$do = isset($_GET['do']) ? $_GET['do'] : "Manage";

		$category = isset($_GET["category"]) && ($_GET["category"] == "Activities" || $_GET["category"] == "Irregularities") ? $_GET["category"] : "Activities";
		$cat_page = $category;
		$category = $category == "Activities" ? 0 : 1;

		if ($do != "ActivitySearch") {

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
		                    	activities.category = " . $category . "
		                	AND
		                		activities.id = students_activities.activity_id    
		                )
		           )
				");

			$currentPage = filter_var($currentPage, FILTER_SANITIZE_NUMBER_INT);

			$maxItems = 10;

			if (! filter_var($currentPage, FILTER_VALIDATE_INT) || $currentPage > ceil($countStudentsActivities / $maxItems)) {

				$currentPage = 1;

			}

			$paginationData = setPagination($countStudentsActivities, $maxItems, $currentPage);

			$activities = getSelect("*", "students", "
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
		                    	activities.category = " . $category . "
		                	AND
		                		activities.id = students_activities.activity_id    
		                )
		           )
				", 1, "ASC LIMIT " . $paginationData['srow'] . " , " . $maxItems);
			
			if (! empty($activities)) {

?>

				<div class = "main">
					<div class = "container text-right">
						<h1 class = "text-center"><?php if ($category === 0) { echo "أنشطة الطلاب"; } else { echo "مخالفات الطلاب"; } ?></h1>
						<form class = "SearchForm ajax-form" action = "?do=ActivitySearch&category=<?php echo $cat_page; ?>" method = "POST" enctype = "multipart/form-data">
							<!--Start Student Code Field-->
							<div class = "form-group row justify-content-md-center">
								<div class = "col-md-4 pl-md-0 search">
									<input type = "search" name = "search" placeholder = "بحث"  class = "form-control ajax-type rounded-right" autocomplete = "off" />
									<button type = "submit" class = "btn bg-transparent p-0 position-absolute"><i class = "fas fa-search"></i></button>
								</div>
								<div class = "col-md-2 pr-md-0">
								    <select class="form-control rounded-left ajax-select" id="selectstatus" name = "searchtype" data-size="5">
										<option value = "0" selected>الاسم</option>
										<option value = "1">نوع <?php if ($category == 0) { echo "النشاط"; } else { echo "المخالفة"; } ?></option>
										<option value = "2">التعليق</option>
								    </select>
								</div>
							</div>
							<div class = "form-group row justify-content-md-center">
								<div class="col-md-6 custom-control custom-checkbox mr-sm-2 text-md-center">
							    	<input type="checkbox" name = "showall" value = "1" class="custom-control-input showAll" id="customControlAutosizing" data-target = "students-activities.php?do=ActivitySearch&category=<?php if ($category == 0) { echo "Activities"; } else { echo "Irregularities"; } ?>">
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
												<h1 class = "text-center"><?php if ($category === 0) { echo "أنشطة الطلاب"; } else { echo "مخالفات الطلاب"; } ?></h1>
											</td>
										</tr>
										<tr>
											<th scope="col">الصورة</th>
											<th scope="col">الإسم</th>
											<th scope="col">نوع <?php if ($category == 0) { echo "الأنشطة"; } else { echo "المخالفات"; } ?></th>
											<th scope="col">التعليقات</th>
											<th class = "no-print" scope="col">أدوات التحكم</th> 
										</tr>
									</thead>
									<tbody>
										<?php

											foreach ($activities as $student) {
												
										?>

												<tr>
													<th scope="row">
														<?php
															echo "<img class = 'img-fluid' src = 'data/uploads/students_images/"; 
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
																					category = ?
																				AND
																					student_id = ?
																			");

														$stmt->execute(array($category, $student["id"]));

														$studentActivities = $stmt->fetchAll();

													?>
													<th scope="row">
														<?php

																$studentName = "";

																foreach ($studentActivities as $studentActivity) {
																
																	$studentName .= $studentActivity["name"] . ", ";

																}

																echo rtrim($studentName,', ');

														?>	
													</th>
													<th scope="row">
														<?php

																$studentComment = "";

																foreach ($studentActivities as $studentActivity) {
																
																	$studentComment .= $studentActivity["comment"] . ", ";

																}

																echo rtrim($studentComment,', ');

														?>	
													</th>
													<?php if ($powers <= 4) { ?>
															<th class = "no-print" scope="row">
																<a href="students.php?do=Activities&id=<?php echo $student['id']; ?>&category=<?php echo $cat_page; ?>" class="btn btn-sm btn-primary"><i class="fas fa-edit"></i> تعديل</a> 
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
			
				<div class = 'alert alert-warning container'><i class='fas fa-exclamation-circle'></i> لا يوجد <?php if ($category == 0) { echo 'أنشطة للطلاب'; } else { echo 'مخالفات للطلاب'; } ?></div>

<?php
				
			}

		} else if ($do === "ActivitySearch") { // Activities Search Page

			$search = filter_var($_POST['search'], FILTER_SANITIZE_STRING);
			$searchtype = isset($_POST["searchtype"]) && is_numeric(filter_var($_POST["searchtype"], FILTER_SANITIZE_NUMBER_INT)) ? intval($_POST["searchtype"]) : 1;
			if (isset($_POST['showall'])) {
				$showall = filter_var($_POST['showall'], FILTER_SANITIZE_NUMBER_INT);
			}

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
				
				$searchColumnWhere = "studentName";
				$searchColumn = "name";
				
			} else if ($searchtype == 1) {
				
				$searchColumnWhere = "activityName";
				$searchColumn = "name";

			} else if ($searchtype == 2) {

				$searchColumnWhere = "activityComment";
				$searchColumn = "comment";

			} else {

				$searchColumnWhere = "studentName";
				$searchColumn = "name";

			}

			if (!empty($search) || $search != "") {

				if ($searchColumnWhere == "studentName") {
				
					$countStudentsActivities = checkSelect("*", "students", "
						" . $searchColumn . " LIKE '%" . $search . "%'
						AND
						EXISTS (SELECT 
			            	* 
			            FROM 
			            	students_activities 
			            WHERE  
			            	students_activities.student_id = students.id
			           	AND 
			            EXISTS (SELECT
			                    	*
			                    FROM
			                    	activities
			                    WHERE
			                    	activities.category = " . $category . "
			                	AND
			                		activities.id = students_activities.activity_id    
			                )
			           )
					");

				} else if ($searchColumnWhere == "activityComment") {

					$countStudentsActivities = checkSelect("*", "students", "
						EXISTS (SELECT 
			            	* 
			            FROM 
			            	students_activities 
			            WHERE  
			            	students_activities.student_id = students.id
			            AND
			            	students_activities." . $searchColumn . " LIKE '%" . $search . "%'
			           	AND 
			            EXISTS (SELECT
			                    	*
			                    FROM
			                    	activities
			                    WHERE
			                    	activities.category = " . $category . "
			                	AND
			                		activities.id = students_activities.activity_id    
			                )
			           )
					");

				} else if ($searchColumnWhere == "activityName") {

					$countStudentsActivities = checkSelect("*", "students", "
						EXISTS (SELECT 
			            	* 
			            FROM 
			            	students_activities 
			            WHERE  
			            	students_activities.student_id = students.id
			           	AND 
			            EXISTS (SELECT
			                    	*
			                    FROM
			                    	activities
			                    WHERE
			                    	activities.category = " . $category . "
			                	AND
			                		activities.id = students_activities.activity_id   
			                	AND
			            			activities." . $searchColumn . " LIKE '%" . $search . "%' 
			                )
			           )
					");

				}


			} else {

				$countStudentsActivities = checkSelect("*", "students", "
						EXISTS (SELECT 
			            	* 
			            FROM 
			            	students_activities 
			            WHERE  
			            	students_activities.student_id = students.id
			           	AND 
			            EXISTS (SELECT
			                    	*
			                    FROM
			                    	activities
			                    WHERE
			                    	activities.category = " . $category . "
			                	AND
			                		activities.id = students_activities.activity_id    
			                )
			           )
					");
			
			}

			if (empty($showall)) {

				$currentPage = filter_var($currentPage, FILTER_SANITIZE_NUMBER_INT);

				$maxItems = 10;

				if (! filter_var($currentPage, FILTER_VALIDATE_INT) || $currentPage > ceil($countStudentsActivities / $maxItems)) {

					$currentPage = 1;

				}

				$paginationData = setPagination($countStudentsActivities, $maxItems, $currentPage);

			}

			if (!empty($search) || $search != "") {

				if ($searchColumnWhere == "studentName") {

					if (empty($showall)) {
				
						$activities = getSelect("*", "students", "
							" . $searchColumn . " LIKE '%" . $search . "%'
							AND
							EXISTS (SELECT 
				            	* 
				            FROM 
				            	students_activities 
				            WHERE  
				            	students_activities.student_id = students.id
				           	AND 
				            EXISTS (SELECT
				                    	*
				                    FROM
				                    	activities
				                    WHERE
				                    	activities.category = " . $category . "
				                	AND
				                		activities.id = students_activities.activity_id    
				                )
				           )
						", "name", "ASC LIMIT " . $paginationData['srow'] . " , " . $maxItems);

					} else {

						$activities = getSelect("*", "students", "
							" . $searchColumn . " LIKE '%" . $search . "%'
							AND
							EXISTS (SELECT 
				            	* 
				            FROM 
				            	students_activities 
				            WHERE  
				            	students_activities.student_id = students.id
				           	AND 
				            EXISTS (SELECT
				                    	*
				                    FROM
				                    	activities
				                    WHERE
				                    	activities.category = " . $category . "
				                	AND
				                		activities.id = students_activities.activity_id    
				                )
				           )
						", "name", "ASC");

					}

				} else if ($searchColumnWhere == "activityComment") {

					if (empty($showall)) {

						$activities = getSelect("*", "students", "
							EXISTS (SELECT 
				            	* 
				            FROM 
				            	students_activities 
				            WHERE  
				            	students_activities.student_id = students.id
				            AND
				            	students_activities." . $searchColumn . " LIKE '%" . $search . "%'
				           	AND 
				            EXISTS (SELECT
				                    	*
				                    FROM
				                    	activities
				                    WHERE
				                    	activities.category = " . $category . "
				                	AND
				                		activities.id = students_activities.activity_id    
				                )
				           )
						", "name", "ASC LIMIT " . $paginationData['srow'] . " , " . $maxItems);

					} else {

						$activities = getSelect("*", "students", "
							EXISTS (SELECT 
				            	* 
				            FROM 
				            	students_activities 
				            WHERE  
				            	students_activities.student_id = students.id
				            AND
				            	students_activities." . $searchColumn . " LIKE '%" . $search . "%'
				           	AND 
				            EXISTS (SELECT
				                    	*
				                    FROM
				                    	activities
				                    WHERE
				                    	activities.category = " . $category . "
				                	AND
				                		activities.id = students_activities.activity_id    
				                )
				           )
						", "name", "ASC");

					} 

				} else if ($searchColumnWhere == "activityName") {

					if (empty($showall)) {

						$activities = getSelect("*", "students", "
							EXISTS (SELECT 
				            	* 
				            FROM 
				            	students_activities 
				            WHERE  
				            	students_activities.student_id = students.id
				           	AND 
				            EXISTS (SELECT
				                    	*
				                    FROM
				                    	activities
				                    WHERE
				                    	activities.category = " . $category . "
				                	AND
				                		activities.id = students_activities.activity_id  
				                	AND
				            			activities." . $searchColumn . " LIKE '%" . $search . "%'  
				                )
				           )
						", "name", "ASC LIMIT " . $paginationData['srow'] . " , " . $maxItems);

					} else {

						$activities = getSelect("*", "students", "
							EXISTS (SELECT 
				            	* 
				            FROM 
				            	students_activities 
				            WHERE  
				            	students_activities.student_id = students.id
				           	AND 
				            EXISTS (SELECT
				                    	*
				                    FROM
				                    	activities
				                    WHERE
				                    	activities.category = " . $category . "
				                	AND
				                		activities.id = students_activities.activity_id   
				                	AND
				            			activities." . $searchColumn . " LIKE '%" . $search . "%'   
				                )
				           )
						", "name", "ASC");

					}

				}

			} else {

				if (empty($showall)) {
				
					$activities = getSelect("*", "students", "
							EXISTS (SELECT 
				            	* 
				            FROM 
				            	students_activities 
				            WHERE  
				            	students_activities.student_id = students.id
				           	AND 
				            EXISTS (SELECT
				                    	*
				                    FROM
				                    	activities
				                    WHERE
				                    	activities.category = " . $category . "
				                	AND
				                		activities.id = students_activities.activity_id    
				                )
				           )
						", "name", "ASC LIMIT " . $paginationData['srow'] . " , " . $maxItems);

				} else {

					$activities = getSelect("*", "students", "
							EXISTS (SELECT 
				            	* 
				            FROM 
				            	students_activities 
				            WHERE  
				            	students_activities.student_id = students.id
				           	AND 
				            EXISTS (SELECT
				                    	*
				                    FROM
				                    	activities
				                    WHERE
				                    	activities.category = " . $category . "
				                	AND
				                		activities.id = students_activities.activity_id    
				                )
				           )
						", "name", "ASC");

				}

			}

			if (! empty($activities)) {

?>
				
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
									<h1 class = "text-center"><?php if ($category === 0) { echo "أنشطة الطلاب"; } else { echo "مخالفات الطلاب"; } ?></h1>
								</td>
							</tr>
							<tr>
								<th scope="col">الصورة</th>
								<th scope="col">الإسم</th>
								<th scope="col">نوع <?php if ($category == 0) { echo "الأنشطة"; } else { echo "المخالفات"; } ?></th>
								<th scope="col">التعليقات</th>
								<th class = "no-print" scope="col">أدوات التحكم</th> 
							</tr>
						</thead>
						<tbody>
							<?php

								foreach ($activities as $student) {
									
							?>

									<tr>
										<th scope="row">
											<?php
												echo "<img class = 'img-fluid' src = 'data/uploads/students_images/"; 
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
																		category = ?
																	AND
																		student_id = ?
																");

											$stmt->execute(array($category, $student["id"]));

											$studentActivities = $stmt->fetchAll();

										?>
										<th scope="row">
											<?php

													$studentName = "";

													foreach ($studentActivities as $studentActivity) {
													
														$studentName .= $studentActivity["name"] . ", ";

													}

													echo rtrim($studentName,', ');

											?>	
										</th>
										<th scope="row">
											<?php

													$studentComment = "";

													foreach ($studentActivities as $studentActivity) {
													
														$studentComment .= $studentActivity["comment"] . ", ";

													}

													echo rtrim($studentComment,', ');

											?>	
										</th>
										<?php if ($powers <= 4) { ?>
												<th class = "no-print" scope="row">
													<a href="students.php?do=Activities&id=<?php echo $student['id']; ?>&category=<?php echo $cat_page; ?>" class="btn btn-sm btn-primary"><i class="fas fa-edit"></i> تعديل</a> 
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
				<footer class = "my-2">
					<?php

						if (empty($showall)) {

							// Call The Pagination Bar
					
							$paging_info = getPagination($paginationData);

						}

					?>
				</footer>

<?php

			} else {
				
?>

				<div class = 'alert alert-warning container'><i class='fas fa-exclamation-circle'></i> لا يوجد <?php if ($category == 0) { echo "أنشطة"; } else { echo "مخالفات"; } ?> بهذه المواصفات</div>

<?php
				
			}

		} else {

			header("Location: activities.php?do=Manage&category=" . $cat_page);

		}

		if ($do != "ActivitySearch") {

			include $templates . "footer.php";

		}

	} else {

		header("Location: index.php");

		exit();

	}

	ob_end_flush();	//	Output Buffering End

?>