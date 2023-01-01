$(function () {

	'use strict';

	// Trigger The Selectboxit

	$("select").selectBoxIt({

		// Uses the jQuery 'slideDown' effect when opening the drop down
	    showEffect: "slideDown",

	    // Sets the jQuery 'slideDown' effect speed to 200 milleseconds
	    showEffectSpeed: 200,

	    // Uses the jQuery 'slideUp' effect when closing the drop down
	    hideEffect: "slideUp",

	    // Sets the jQuery 'slideUp' effect speed to 200 milleseconds
	    hideEffectSpeed: 200,

	    autoWidth: false

	});

	// Trigger The Tagsit

    $('#tags').tagit({
        removeConfirmation: true,
        caseSensitive: false,
        allowSpaces: true,
        placeholderText: "Tag"
    });

    // Trigger The Date Picker

    $(".date").datepicker({
    	format: 'dd-mm-yyyy'
    });

	// Hide Placeholder On Form Focus

	$("[placeholder]").focus(function () {

		$(this).attr('data-text', $(this).attr("placeholder"));
		$(this).attr("placeholder", "");

	}).blur(function () {

		$(this).attr("placeholder", $(this).attr("data-text"));

	});

	// Add Asterisk On Required Field

	$(":required").each(function () {

		$(this).after('<span class = "asterisk"><i class="fas fa-asterisk"></i></span>');

	});

	// Convert Password Field To Text Field

	$(".show-pass").on("click", function () {

		if ($(this).siblings(".password").attr("type") === "password") {

			$(this).removeClass("fa-eye").addClass("fa-eye-slash").css("color", "rgba(0,123,255, 0.8)");

			$(this).siblings(".password").attr("type", "text");

		} else {

			$(this).removeClass("fa-eye-slash").addClass("fa-eye").css("color", "#000");

			$(this).siblings(".password").attr("type", "password");

		}
		
	});

	// Show Pass Icon Design

	$(".show-pass").css("height", "100%").css("lineHeight", $(".show-pass").height() + "px");

	// Pagination Bar

	if ($(window).width() <= 576) {

		$(".pagination").addClass("pagination-sm");

	}

	$(window).on("resize", function () {

		if ($(window).width() <= 576) {

			$(".pagination").addClass("pagination-sm");

		} else {

		$(".pagination").removeClass("pagination-sm");

		}

	});

	// Footer

	if ($(window).height() == $(document).height()) {

		$("body").css({
			"padding-bottom": $(".copyright").outerHeight(),
			"position": "relative"
		});

		$(".copyright").css({
			"position": "absolute",
			"bottom": "0"
		});


	}

	$(window).on("resize", function () {

		if ($(window).height() == $(document).height()) {

			$("body").css({
				"padding-bottom": $(".copyright").outerHeight(),
				"position": "relative"
			});

			$(".copyright").css({
				"position": "absolute",
				"bottom": "0"
			});


		}

	});

	// Confirmation Message On Delete Button

	var deletePath;

	$(".main-data").on("click", ".confirm", function () {

		deletePath = $(this).attr("href");

		$(".confirm-modal").attr("action", deletePath);

	});

	// Show Image

	var editAction;
	var deleteAction;
	var image;

	$(".main-data").on("click", ".show_image", function () {

		editAction = $(this).attr("data_edit");
		deleteAction = $(this).attr("data_delete");
		image = $(this).attr("src");

		$(".show_image_modal .modal-body .edit").attr("href", editAction);
		$(".show_image_modal .modal-body .delete").attr("href", deleteAction);
		$(".show_image_modal .modal-body img").attr("src", image);

	});

	// Custom File

	$(".custom-file-input").on("change", function () {

		$(this).siblings(".custom-file-label").text($(this)[0].files[0].name);

	});

	// Login Form

    $('.index-login .input').each(function(){

        $(this).on('blur', function(){

            if ($(this).val().trim() != "") {

                $(this).addClass('has-val');

            } else {

                $(this).removeClass('has-val');

            }

        });    
    });

    var input = $('.index-login .validate-input .input');

    $('.index-login .validate-form').on('submit',function(){

        var check = true;

        for (var i=0; i<input.length; i++) {

            if(validate(input[i]) == false){

                showValidate(input[i]);

                check=false;

            }

        }

        return check;

    });


    $('.index-login .validate-form .input').each(function(){

        $(this).focus(function(){

           hideValidate(this);

        });

    });

    function validate (input) {

        if ($(input).attr('type') == 'email' || $(input).attr('name') == 'email') {
            
            if ($(input).val().trim().match(/^([a-zA-Z0-9_\-\.]+)@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.)|(([a-zA-Z0-9\-]+\.)+))([a-zA-Z]{1,5}|[0-9]{1,3})(\]?)$/) == null) {
                
                return false;

            }

        } else {

            if ($(input).val().trim() == ''){

                return false;

            }

        }

    }

    function showValidate(input) {

        var thisAlert = $(input).parent();

        $(thisAlert).addClass('alert-validate');

    }

    function hideValidate(input) {

        var thisAlert = $(input).parent();

        $(thisAlert).removeClass('alert-validate');

    }

    $("body").css("paddingTop", $(".navbar").outerH);

    var mainWidth;

    var categories = $(".categories");

    var emails = $(".emails");

    var mainPadding = categories.css("paddingRight");

    $("#toggle-category").on("click", function () {

    	if ($(window).width() >= 768) {

			if (categories.outerWidth() <= 56) {

				categories.find("li span").show();

	    		categories.css({

	    			"width": mainWidth,
	    			"overflow": "scroll",
	    			"position": "relative"

	    		});

	    		$(".emails").width($(window).width() - categories.outerWidth());

			} else {

				mainWidth = categories.outerWidth();

		    	categories.find("li span").hide();

		    	categories.css({

		    		"overflow": "hidden",
		    		"width": "56px"

		    	});

		    	emails.width($(window).width() - categories.outerWidth());

			}

		}

    });

    $(window).on("resize", function () {

    	if (categories.css("display") != "none") {

    		emails.width($(window).width() - categories.outerWidth());

    	} else {

    		emails.width("100%");

    	}

    	

    });

    categories.hover(function(){
		
		if ($(this).outerWidth()  == 56) {

			$(this).css({

				"position": "absolute",
				"overflow": "scroll"

			});

			emails.width("100%");

			$(this).animate({

	    		width: mainWidth

	    	}, 400, function () {

	    		$(this).find("li span").fadeIn();

	    	});

		}
		
	},function(){

    	if ($(this).css("position") == "absolute") {

	    	$(this).find("li span").fadeOut();

	    	$(this).animate({

	    		width: '56px'

	    	}, function () {

	    		$(this).css({

		    		"position": "relative",
		    		"overflow": "hidden"

		    	});

	    		emails.width($(window).width() - categories.outerWidth());

	    	});

	    }
		
	});

	// Dashboard

	$(".toggle-info").on("click", function () {

		$(this).toggleClass("selected").parent().next(".card-body").slideToggle();

		if ($(this).hasClass("selected")) {

			$(this).html("<i class='fas fa-plus-square'></i>");

		} else {

			$(this).html("<i class='fas fa-minus-square'></i>");

		}

	});

	// Users

	var userSearch = $(".searchUser");

	var userSearchedBy = $(".userSearchBy");

	var usersSearchType = userSearchedBy.val();

	var searchIcon = userSearch.next("button[type = submit]").html();

	userSearchedBy.on("change", function () {

		usersSearchType = $(this).val();

		var search = userSearch.get(0);
		var searchClass = userSearch.attr("class");
		var searchName = userSearch.attr("name");

		if (usersSearchType == 4) {

			userSearch.after('<select class="' + searchClass + ' ajax-type-select" id="selectstatus" name = "' + searchName + '" data-size="5"><option value = "1" selected>المدير</option><option value = "2">مساعد المدير</option><option value = "3">أنشطة / مخالفات</option><option value = "4">مراجع</option></select>');
			$("select").selectBoxIt({

				// Uses the jQuery 'slideDown' effect when opening the drop down
			    showEffect: "slideDown",

			    // Sets the jQuery 'slideDown' effect speed to 200 milleseconds
			    showEffectSpeed: 200,

			    // Uses the jQuery 'slideUp' effect when closing the drop down
			    hideEffect: "slideUp",

			    // Sets the jQuery 'slideUp' effect speed to 200 milleseconds
			    hideEffectSpeed: 200,

			    autoWidth: false

			});
			$("button[type = submit]").html("");
			$(".searchUser[type = search]").remove();
			$(".searchUser").removeClass("ajax-type");

		} else {

			$("select.searchUser").after(search);
			$("button[type = submit]").html(searchIcon);
			$("select.searchUser").remove();
			$("span.searchUser").remove();

		}

	});

	// Print

	function printData() {

	   	var pageData = $(".main").html();
		var printData = $(".printData").html();

		$(".main").html(printData);

		window.print();

		$(".main").html(pageData);

		location.reload();

	}

	$('.main').on('click', ".print", function(){

		$(this).parents("#print-options").modal('hide');

		$(".modal-backdrop").hide();

		printData();	

	})

});