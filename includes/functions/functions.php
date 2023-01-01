<?php

	/*
	**	Title Function 	v1.0
	**	Title Function That Echo The Page Title In Case The Page
	**	Has The Variable $pageTitle And Echo Default Title For Other Pages
	*/

	function getTitle() {

		global $pageTitle;

		if (isset($pageTitle)) {

			echo $pageTitle;

		} else {

			echo "Home";

		}

	}

	/*
	**	Get Word Function 	[ This Function Accept Parameters ]	v1.0
	**	Function To Get Number Of Words From String
	**	$string = The String To Get Slice From
	**	$str_max = The Max Words The Would Return
	*/

	function getWord($string, $str_max = 2) {

		$sliceString = explode(" ", $string);

        $sliceString = array_slice($sliceString, 0, $str_max);

        $sliceString = implode(" ", $sliceString);

        return $sliceString;

	}

	/*
	**	Arabic Numbers Function 	[ This Function Accept Parameters ]	v1.0
	**	Function To Changer English Number To Arabic Numbers
	**	$str = The String
	*/

	function arabicNumbers($str) {

		$western_arabic = array('0','1','2','3','4','5','6','7','8','9');

		$eastern_arabic = array('٠','١','٢','٣','٤','٥','٦','٧','٨','٩');

		return $str = str_replace($western_arabic, $eastern_arabic, $str);

	}

	/*
	**	Redirect Function 	[ This Function Accept Parameters ]		v2.0
	**	$theMsg = Echo The Error Message
	**	$seconds = Seconds Before Redirecting
	**	$url = The Link To Redirect
	**	$link = Echo The Redirect Page Name
	*/

	function redirect($theMsg = "هناك شئ خاطئ", $seconds = 3, $url = NULL, $link = NULL) {

		if ($url === NULL) {

			$url = "index.php";

			$link = "الصفحة الرئيسية";

		} else if ($url === "back") {

			if (isset($_SERVER["HTTP_REFERER"]) && $_SERVER["HTTP_REFERER"] !== "") {

				$url = $_SERVER["HTTP_REFERER"];

				$link = "الصفحة السابقة";

			} else {

				$url = "index.php";

				$link = "الصفحة الرئيسية";

			}

		}

		echo "<div class = 'container'>";

		echo $theMsg;

		echo "<div class = 'alert alert-info'>سوف تحول إلى $link بعد <span class = 'seconds'></span> ثوانى</div>";

		echo "</div>";

		echo "<script>";

			echo "

				var seconds = $seconds;

				setInterval(function () {
					
					$('.seconds').text(seconds);

					if (seconds >= 0) {

						seconds--;

					}

				}, 1000);

			</script>";

		header("refresh:$seconds;url=$url");

	}

	/*
	**	Check Select Function v1.0
	**	Function To Check Items In Database [ This Function Accept Parameters ]
	**	$select = The Item To Select
	**	$from = The Table To Select From
	**	$value = The Value Of Select
	*/

	function checkSelect($select, $from, $where = 1) {

		global $con;

		$statement = $con->prepare("SELECT $select FROM $from WHERE $where");

		$statement->execute();

		$count = $statement->rowCount();

		return $count;

	}

	/*
	** Get Latest Records Function v1.0
	** Function To Get Latest Items From Databases [ This Function Accept Parameters ]
	** $select = Field To Select
	** $table = The Table To Choose From
	** $order = The Column To Order DESC
	** $limit = Number Of Records To Get
	*/

	function getLatest($select, $table, $where = 1, $order, $limit = 5) {

		global $con;

		$stmt3 = $con->prepare("SELECT $select FROM $table WHERE $where ORDER BY $order DESC LIMIT $limit");

		$stmt3->execute();

		$rows = $stmt3->fetchAll();

		return $rows;

	}

	/*
	** Get All From Function V1.0
	** Function To Get All Records From Databases
	** $field = Field To Select
	** $table = Table To Select From
	** $where = Where Condition
	** $orderfield = The Field To Make An Order
	** $ordering = The Value Of The $orderfield 	[ASC - DESC]
	** $many = To Know One Column Or More 	[True - false]
	*/

	function getSelect($fields, $table, $where = 1, $orderfield = 1, $ordering = "ASC", $many = true) {

		global $con;

		$getAll = $con->prepare("SELECT $fields FROM $table WHERE $where ORDER BY $orderfield $ordering");

		$getAll->execute();

		if ($many == true) {

			$all = $getAll->fetchAll();

		} else {

			$all = $getAll->fetch();

		}

		return $all;

	}

	function setPagination($rows, $pp, $curr_page) {

	    $pages = ceil($rows / $pp); // calc pages

	    $data = array(); // start out array
	    $data['srow']      = ($curr_page * $pp) - $pp; // what row to start at
	    $data['pages']     = $pages;                   // add the pages
	    $data['curr_page'] = $curr_page;               // Whats the current page

	    return $data; //return the paging data

	}

	/*
	** getPagination Info Function v1.0
	** Function To Make Pagination Bar
	** $rows = Counted Rows From Query
	** $pp = The Max Items In The Page
	** $curr_page = The Current Page Number
	** $max = The Max Number Of Shown Pagination Links [Default = 5]
	*/

	function getPagination($paginationData, $max = 3) {

		$curr_page = $paginationData['curr_page'];
		$pages = $paginationData['pages'];
		$query = $_GET;
		
	    echo "<nav aria-label='Page navigation example'>";
	    	echo "<ul class='pagination justify-content-center p-0'>";

			    // If the current page is more than 1, show the Previous links

		    	echo "<li class='page-item ";
		    	if ($curr_page == 1) {
		        	echo "disabled";
		        }
		    	echo "'>";
		    		// replace parameter(s)
					$query['currentPage'] = ($curr_page - 1);
					// rebuild url
					$query_result = http_build_query($query);
			        echo "<a class = 'page-link' href='";
			        	echo $_SERVER['PHP_SELF'] . "?" . $query_result;
			        echo "' title='Page ";
			        	echo ($curr_page - 1);
			        echo "'>السابق</a>";
			    echo "</li>";

		        //setup starting point

		        //$max is equal to number of links shown

		        if ($curr_page < $max) {

		            $sp = 1;

		        } else if ($curr_page >= ($pages - floor($max / 2)) ) {

		            $sp = $pages - $max + 1;

		        } else if ($curr_page >= $max) {

		            $sp = $curr_page  - floor($max/2);

		        }

			    // If the current page >= $max then show link to 1st page
			    if ($curr_page >= $max) {

			        echo "<li class='page-item'>";
			        	// replace parameter(s)
						$query['currentPage'] = 1;
						// rebuild url
						$query_result = http_build_query($query);
			        	echo "<a class='page-link' href='";
			        		echo $_SERVER["PHP_SELF"] . "?" . $query_result;
			        	echo "' title='Page 1'>1</a>";
			        echo "</li>";
			        echo "<li class='page-item disabled'><i class='fas fa-ellipsis-h page-link text-primary'></i></li>";

			    }

			    // Loop though max number of pages shown and show links either side equal to $max / 2
			    for ($i = $sp; $i <= ($sp + $max -1);$i++) {

		            if($i > $pages)
		                
		            continue;

			        if ($curr_page == $i) {

			            echo "<li class='page-item active'><span class='page-link'>$i<span class='sr-only'>(current)</span></span></li>";

			        } else {

			            echo "<li class='page-item'>";
			            	// replace parameter(s)
							$query['currentPage'] = ($i);
							// rebuild url
							$query_result = http_build_query($query);
			            	echo "<a class='page-link' href='";
			            		echo $_SERVER['PHP_SELF'] . "?" . $query_result;
			            	echo "' title='Page $i'>$i</a>";
			            echo "</li>";

					}

			   	}

			    if ($curr_page < ($pages - floor($max / 2))) {

			        echo "<li class='page-item disabled'><i class='fas fa-ellipsis-h page-link text-primary'></i></li>";
			        echo "<li class='page-item'>";
			        	// replace parameter(s)
						$query['currentPage'] = $pages;
						// rebuild url
						$query_result = http_build_query($query);
			        	echo "<a class='page-link' href='";
			        		echo $_SERVER['PHP_SELF'] . "?" . $query_result;
			        	echo "' title='Page ";
			        		echo $pages;
			        	echo "'>";
			        	echo $pages;
			        echo "</a>";
			        echo "</li>";

			    }

			    // Show last two pages if we're not near them

		        echo "<li class='page-item ";
		        if ($curr_page == $pages) {
		        	echo "disabled";
		        }
		        echo "'>";
		        // replace parameter(s)
				$query['currentPage'] = ($curr_page + 1);
				// rebuild url
				$query_result = http_build_query($query);
		        echo "<a class='page-link' href='";
		        	echo $_SERVER["PHP_SELF"] . "?" . $query_result;
		        echo "' title='Page ";
		        echo ($curr_page + 1);
		        echo "'>التالى</a></li>";

			echo "</ul>";
		echo "</nav>";

	}