$(function () {

	// Functions

	function GetURLParameter(sParam) {

	    var sPageURL = window.location.search.substring(1);

	    var sURLVariables = sPageURL.split('&');

	    for (var i = 0; i < sURLVariables.length; i++) {

	        var sParameterName = sURLVariables[i].split('=');

	        if (sParameterName[0] == sParam) {

	            return sParameterName[1];

	        }
	    }

	}

	function ajax(form = ".ajax-form", val = ".ajax-val") {

		$.ajax({
			url: $(form).attr("action"),
			type: "post",
			data: new FormData($(form)[0]),
			processData: false,
        	contentType: false,
			beforeSend: function () {
				$(val).html(progress);
			},
			success: function (r) {
				$(".progress").hide();
				$(val).html(r);
			}
		});

		return false;

	}

	// Main Rules

	$("body").on("click", ".searchFooter a", function () {

		$url = $(this).attr("href");

		$.ajax({
			url: $url,
			type: "post",
			data: $(".SearchForm").serialize(),
			success: function (r) {
				$(".main-data").html(r);
			}
		});
		
		return false;

	});

	// Start Student Profile Page

	var progress = "<div class='progress my-3'>";
	progress += "<div class='progress-bar progress-bar-striped progress-bar-animated' role='progressbar' style='width: 100%' aria-valuenow='100' aria-valuemin='0' aria-valuemax='100'></div>";
	progress += "</div>";

	$("body").on("change", ".ajax-select", function () {

		$(".ajax-type").val("");

		ajax();

	});

	$("body").on("change", ".ajax-type-select", function () {

		ajax();

	});

	$("body").on("keyup", ".ajax-type", function () {

		ajax();

	});

	$("body").on("submit", ".ajax-form", function () {

		ajax();

		return false;

	});

	$('body').on("change", ".showAll", function() {

		ajax();

	});

});