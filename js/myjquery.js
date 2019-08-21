jQuery(document).ready(function($) {   
	var manufacturer = '';
	var year = '';
	var horsepower = '';
	var blades = '';

   function getYears() {
	   var myaction = 'getYears';

	   $.post(my_ajax_obj.ajax_url, {         
		   _ajax_nonce: my_ajax_obj.nonce,
		   action: "propfinder_scripts",
		   myaction: myaction,
		   manufacturer: manufacturer,
	   }, 
	   
	   function(data) {
		   console.log(data)
		 data.forEach(function(year) {
			 $('#select-year').append("<option>" + year + "</option>")
		   })
		   if( data ) {
			   $('#select-year').removeAttr('disabled')
			 } else {
			   $('#select-year').attr('disabled', true)
			 }
	   })
	 }

	function getHorsepowers() {
	   var myaction = 'getHorsepowers';

	   $.post(my_ajax_obj.ajax_url, {         
		   _ajax_nonce: my_ajax_obj.nonce,
		   action: "propfinder_scripts",
		   myaction: myaction,
		   manufacturer: manufacturer,
		   year: year
	   }, 
	   
	   function(data) {
		 data.forEach(function(power) {
			 $('#select-horsepower').append("<option>" + power + "</option>")
		   })
		   if( data ) {
			   $('#select-horsepower').removeAttr('disabled')
			 } else {
			   $('#select-horsepower').attr('disabled', true)
			 }
	   })
	}

	function getBlades() {
	   var myaction = 'getBlades';

	   $.post(my_ajax_obj.ajax_url, {         
		   _ajax_nonce: my_ajax_obj.nonce,
		   action: "propfinder_scripts",
		   myaction: myaction,
		   manufacturer: manufacturer,
		   year: year,
		   hp: horsepower
	   }, 
	   
	   function(data) {
		   var length = data.length 
		   console.log(length)
		   data.forEach(function(blade) {			  
			   if (length > 1) {
			   $('#select-blades').append("<option>" + blade + "</option>")
			   } else {
			   $('#select-blades').append("<option>" + blade + "</option>").val(data)
			   blades = data[0]
			   getResults()
			   }
	   })

		   if( data ) {
			   $('#select-blades').removeAttr('disabled')
			 } else {
			   $('#select-blades').attr('disabled', true)
			 }
	   })
	}

	function getResults() {
	   var myaction = 'getBladeGroup';

	   $.post(my_ajax_obj.ajax_url, {         
		   _ajax_nonce: my_ajax_obj.nonce,
		   action: "propfinder_scripts",
		   myaction: myaction,
		   manufacturer: manufacturer,
		   year: year,
		   hp: horsepower,
		   blades: blades
	   }, 
	   
	   function(data) {
		   var results = data;

		   $(".prop-finder-results-box").html(results)			
	   })
	}

   function changeAll(){
	   $("#select-year").empty()
	   $("#select-year").append("<option>Select Year</option>")
	   $('#select-year').attr('disabled', true)
	   $("#select-horsepower").empty()
	   $("#select-horsepower").append("<option>Select Horsepower</option>")
	   $('#select-horsepower').attr('disabled', true)
	   $("#select-blades").empty()
	   $("#select-blades").append("<option>Select Blades</option>")
	   $('#select-blades').attr('disabled', true)

	   $(".prop-finder-results-box").empty()
   }
   function changeYear() {
	   $("#select-horsepower").empty()
	   $("#select-horsepower").append("<option>Select Horsepower</option>")
	   $('#select-horsepower').attr('disabled', true)
	   $("#select-blades").empty()
	   $("#select-blades").append("<option>Select Blades</option>")
	   $('#select-blades').attr('disabled', true)


	   $(".prop-finder-results-box").empty()
   }

   function changeHP() {
	   $("#select-blades").empty()
	   $("#select-blades").append("<option>Select Blades</option>")
	   $('#select-blades').attr('disabled', true)

	   $(".prop-finder-results-box").empty()
   }

   function changeBlades() {
	   $(".prop-finder-results-box").empty()
   }

   $('#select-manufacturer').change(function(e) {
	   var value = $(this).children("option:selected").val();
	   manufacturer = value;	

	   getYears();
	   changeAll()
   })

   $('#select-year').change(function(e) {
	   var value = $(this).children("option:selected").val();
	   year = value;

	   getHorsepowers();
	   changeYear()
   })

   $('#select-horsepower').change(function(e) {
	   var value = $(this).children("option:selected").val();
	   horsepower = value;


	   getBlades();
	   changeHP();
   })

   $('#select-blades').change(function(e) {
	   var value = $(this).children("option:selected").val();
	   blades = value;

	   console.log(blades)

	   changeBlades();
	   getResults();
   })
});