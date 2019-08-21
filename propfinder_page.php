<?php
function propfinder_shortcodes_init()
{
    function propfinder_shortcode($atts = [], $content = null)
    {
		//include( 'propfinder_page.php' );
		global $wpdb;
		$propTable = $wpdb->prefix . 'piranha_propfinder';
        //pre-populate first drop down box
		$qry = "SELECT distinct manufacturer FROM `" . $propTable . "` order by manufacturer";
		$results = $wpdb->get_results($qry);
		$content = '<div class="prop-finder-container">';
		$content .= '<select id="select-manufacturer" class="prop-finder-dropdown" value="manufacturer">';
		$content .= "<option value=''>Select Manufacturer</option>";
		foreach($results as $row){
			$content .= "<option value='" . $row->manufacturer . "'>" . $row->manufacturer . "</option>"; 
		}
		$content .= '</select>';
        $content .= '<select disabled id="select-year" class="prop-finder-year-dropdown">';
		$content .= '<option>Select Year</option>';
        $content .= '</select>';
		
        $content .= '<select disabled id="select-horsepower" class="prop-finder-horsepower-dropdown">';
		$content .= '<option>Select Horsepower</option>';
		$content .= '</select>';
		
		$content .= '<select disabled id="select-blades" class="prop-finder-blades-dropdown">';
		$content .= '<option>Select Blades</option>';
		$content .= '</select>';
		
        $content .= '</div>';
		$content .= '<div class="prop-finder-results-box">';
		$content .= '</div>';
        // always return
        return $content;
    }
    add_shortcode('propfinder', 'propfinder_shortcode');
}
add_action('init', 'propfinder_shortcodes_init');
?>