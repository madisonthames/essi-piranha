<?php
/**
 * Plugin Name: ESSI Piranha Prop Finder
 * Plugin URI: https://www.excelss.com
 * Description: Custom plugin for Piranha to filter and display propeller options based on model
 * Version: 1.0
 * Author: Lynn Thames
 * Author URI: https://www.excelss.com
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

add_action('admin_menu', 'piranha_propfinder_setup_menu');
 
function piranha_propfinder_setup_menu(){
		add_menu_page( 'PropFinder', 'PropFinder', 'manage_options', 'piranha-propfinder', 'piranha_propfinder_import' );
}
 


/**
 * Enqueue script for propfinder page.
 */
add_action( 'wp_enqueue_scripts', 'piranha_propfinder_scripts' );
function piranha_propfinder_scripts( $hook ) {
	//don't add script to all pages.
	if( !is_page( 'propfinder') ) return;
    wp_enqueue_script( 'propfinder-ajax-script', plugins_url( '/js/myjquery.js', __FILE__ ), array( 'jquery' ));
	//assign nonce for verification
    $propfinder_nonce = wp_create_nonce( 'piranha_propfinder' );
	//add url and nonce for script to access
    wp_localize_script( 'propfinder-ajax-script', 'my_ajax_obj', array(
       'ajax_url' => admin_url( 'admin-ajax.php' ),
       'nonce'    => $propfinder_nonce,
    ) );
}

/**
 * Handle ajax request here
*/
function my_propfinder_ajax_handler() {
    check_ajax_referer( 'piranha_propfinder' );
	$action = '';
	$content = '';
	$manufacturer = '';
	$year = '';
	$hp = '';
	$blades = '';
	if(isset($_POST['myaction'])) $action = $_POST['myaction'];
	if(isset($_POST['manufacturer'])) $manufacturer = $_POST['manufacturer'];
	if(isset($_POST['year'])) $year = $_POST['year'];
	if(isset($_POST['hp'])) $hp = $_POST['hp'];
	if(isset($_POST['blades'])) $blades = $_POST['blades'];

	if($action=='getYears'){
		$content = getYearOptions($manufacturer);
	}elseif($action=='getHorsepowers'){
		$content = getHorsepowerOptions($manufacturer, $year);
	}elseif($action=='getBlades'){
		$content = getBladeOptions($manufacturer, $year, $hp);
	}elseif($action=='getBladeGroup'){
		$content = getBladeGroup($manufacturer, $year, $hp, $blades);
		
	}else{
		$content = 'invalid_action';
	}
    header('Content-Type: application/json');
    echo json_encode($content);
    wp_die(); 
}
add_action('wp_ajax_propfinder_scripts', 'my_propfinder_ajax_handler');
add_action('wp_ajax_nopriv_propfinder_scripts', 'my_propfinder_ajax_handler');

//function to get years from manufacturer
function getYearOptions($manufacturer = ''){
		global $wpdb;
		$propTable = $wpdb->prefix . 'piranha_propfinder';
        //pre-populate first drop down box
		$qry = "SELECT distinct year FROM `" . $propTable . "` 
				where manufacturer = '" . $manufacturer . "' order by year";
		$results = $wpdb->get_results($qry);
		//$content = "<option value=''>--Select Year--</option>";
		$content = Array();
		foreach($results as $row){
			//$content .= "<option value='" . $row->year . "'>" . $row->year . "</option>"; 
			$content[] = $row->year;
		}
        return $content;	
}

//function to get horsepower from manufacturer and year
function getHorsepowerOptions($manufacturer = '', $year = ''){
		global $wpdb;
		$propTable = $wpdb->prefix . 'piranha_propfinder';
        //pre-populate first drop down box
		$qry = "SELECT distinct hp FROM `" . $propTable . "` 
				where manufacturer = '" . $manufacturer . "' and year = '" . $year . "' order by hp";
		$results = $wpdb->get_results($qry);
		$content = "<option value=''>--Select Horsepower--</option>";
		foreach($results as $row){
			$content .= "<option value='" . $row->hp . "'>" . $row->hp . "</option>"; 
		}
        return $content;		
}

//function to get blade options from manufacturer and year and horsepower
function getBladeOptions($manufacturer = '', $year = '', $horsepower = ''){
		global $wpdb;
		$propTable = $wpdb->prefix . 'piranha_propfinder';
        //pre-populate first drop down box
		$qry = "SELECT distinct blades FROM `" . $propTable . "` 
				where manufacturer = '" . $manufacturer . "' and year = '" . $year . "' and hp = '" . $horsepower . "'
				order by blades";
		$results = $wpdb->get_results($qry);
		$content = "<option value=''>--Select Blades--</option>";
		foreach($results as $row){
			$content .= "<option value='" . $row->blades . "'>" . $row->blades . "</option>"; 
		}
        return $content;		
}

include( 'propfinder_page.php' );


function piranha_propfinder_import(){
    // check user capabilities
    if (!current_user_can('manage_options')) {
        return;
    }
	piranha_handle_import_post();
    ?>
    <div class="wrap">
        <h1><?= esc_html(get_admin_page_title()); ?></h1>
        <h2>Upload a File</h2>
		<form  method="post" enctype="multipart/form-data">
                <input type='file' id='my_file_upload' name='my_file_upload'></input>
                <?php submit_button('Upload') ?>
        </form>		
<?php
}

function piranha_handle_import_post(){
        // First check if the file appears on the _FILES array
        if(isset($_FILES['my_file_upload'])){
			$uploaded = wp_upload_bits($_FILES['my_file_upload']['name'], null, file_get_contents($_FILES['my_file_upload']['tmp_name']));
            // Error checking using WP functions
            if($uploaded['error']){
                echo "Error uploading file: " . $uploaded['error'];
            }else{
				$myfile = $uploaded['file'];
				piranha_do_import($myfile);
            }
        }
}

function piranha_do_import($file){
	global $wpdb;
	$propTable = $wpdb->prefix . 'piranha_propfinder';

	if ( is_readable( $file ) && $_file = fopen( $file, "r" ) ) {
		// Get headers
		$header = fgetcsv( $_file );
		while ( $row = fgetcsv( $_file ) ) {
			foreach ( $header as $i => $key ) {
				$key = preg_replace('/[[:^print:]]/', '', $key);
				$data = preg_replace('/[[:^print:]]/', '', $row[$i]);
				$myrecord[$key] = $data;
			}
			$success = $wpdb->insert($propTable, $myrecord);
			if(false === $success) {
				$errors = 'yes';
			}
		}
		fclose( $_file );
		if($errors==''){
			echo '<div class="notice notice-success is-dismissible"><p>Your file has been successfully imported.</p></div>';
		}else{
			echo '<div class="notice notice-error is-dismissible"><p>There were errors in your import!</p></div>';
		}
	} else {
		echo '<div class="notice notice-error is-dismissible"><p>The file could not be opened</p></div>';
	}
}
 
function piranha_install () {
	global $wpdb;
	global $piranha_db_version;
	
	$piranha_db_version = '1.1';
	$table_name = $wpdb->prefix . "piranha_propfinder"; 
	$charset_collate = $wpdb->get_charset_collate();

	$sql = "CREATE TABLE $table_name (
	  id mediumint(9) NOT NULL AUTO_INCREMENT,
	  manufacturer varchar(100),
	  year varchar(10),
	  hp varchar(10),
	  blades varchar(10),
	  hub varchar(10),
	  bladegroup varchar(10),
	  PRIMARY KEY  (id)
	) $charset_collate;";
	error_log($sql);
	require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
	dbDelta( $sql );   
	add_option( 'piranha_db_version', $piranha_db_version );
}
register_activation_hook( __FILE__, 'piranha_install' );