jQuery(document).ready(function($) {   
	 var title = 'mytitle';
	 var year = '';
	 var horsepower = '';
	 var myaction = 'getYears';
	 //var myaction = 'getHorsepowers';
	 //var myaction = 'getBlades';

	 function getHorsepowers() {
		$.post(my_ajax_obj.ajax_url, {         
			_ajax_nonce: my_ajax_obj.nonce,
			action: "propfinder_scripts",
			myaction: myaction,
			// manufacturer: manufacturer,
			year: year
		}, function(data) {
		  data.horsepowers.forEach(function(power) {
			  $('#select-horsepower').append("<option>" + power + "</option>")
			})
		})
	 }

	 function getBlades() {
		$.post(my_ajax_obj.ajax_url, {         
			_ajax_nonce: my_ajax_obj.nonce,
			action: "propfinder_scripts",
			myaction: myaction,
			// manufacturer: manufacturer,
			year: year,
			horsepower: horsepower
		}, function(data) {
		  data.blades.forEach(function(blade) {
			  $('#select-blades').append("<option>" + blade + "</option>")
			})
		})
	 }

	 $('#select-manufacturer').change(function(e) {
		var value = $(this).children("option:selected").val()
		var manufacturer = value
		if( value !== '' ) {
		  $('#select-year').removeAttr('disabled')
		} else {
		  $('#select-year').attr('disabled', true)
		}
		$('#select-year').empty()
		$('#select-year').append("<option> Select Year </option>")

		function getYears() {
		   $.post(my_ajax_obj.ajax_url, {         
			   _ajax_nonce: my_ajax_obj.nonce,
			   action: "propfinder_scripts",
			   myaction: myaction,
			   manufacturer: manufacturer,
		   }, function(data) {
			   console.log(data)
			 data.forEach(function(year) {
				 $('#select-year').append("<option>" + year + "</option>")
			   })
		   })
		 }
		 
		getYears();
	})

	 $('#select-year').change(function(e) {
		var value = $(this).children("option:selected").val()
		year = value
		if( value !== '' ) {
		  $('#select-horsepower').removeAttr('disabled')
		} else {
		  $('#select-horsepower').attr('disabled', true)
		}
		$('#select-horsepower').empty()
		$('#select-horsepower').append("<option> Select Horsepower </option>")
		getHorsepowers()
	})

	$('#select-horsepower').change(function(e) {
		var value = $(this).children("option:selected").val()
		horsepower = value
		if( value !== '' ) {
		  $('#select-blades').removeAttr('disabled')
		} else {
		  $('#select-blades').attr('disabled', true)
		}
		$('#select-blades').empty()
		$('#select-blades').append("<option> Select Blades </option>")
		getBlades();
	})

 });
           