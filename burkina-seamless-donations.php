<?php
/*
Plugin Name: Seamless Donations - Burkina Vert
Plugin URI: https://maisgeeks.com/
Description: Burkina Vert extra features for Seamless Donations.
Version: 1.2.7
Author: Daniel Akio Oizumi
Author URI: https://maisgeeks.com/
Text Domain: burkina-seamless-donations
Domain Path: /languages
License: GPL2
*/

//	Exit if .php file accessed directly
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/*
 * Activate Plugin
 */
function burkina_vert_seamless_donations_plugin_activated() {
    // Require Seamless Donation
    if ( ! is_plugin_active( 'seamless-donations/seamless-donations.php' ) and current_user_can( 'activate_plugins' ) ) {
        // Stop activation redirect and show error
        wp_die( 'Sorry, but this plugin requires the Seamless Donations plugin to be installed and active. <br><a href="' . admin_url( 'plugins.php' ) . '">&laquo; Return to Plugins</a>' );
    }

    // Create custom database table
    burkina_vert_seamless_donation_install();
}
register_activation_hook( __FILE__, 'burkina_vert_seamless_donations_plugin_activated' );

/*
 *  Create Burkina's custom Seamless Donation database table and directory when the plugin is activated
 */
global $wpdb;
global $table_name;
$table_name = $wpdb->prefix . "burkina_seamless_donations";
global $burkina_db_version;
$burkina_db_version = '1.0';
global $burkina_dir;
$burkina_dir = WP_CONTENT_DIR."/uploads/burkina-vert";

// Notification for new folder creation fail
function burkina_vert_create_folder_error() {
    $class = 'notice notice-error';
    $message = __( 'The directory burkina-vert was not created. Please verify the /wp-content/upload/ permission.', 'burkina-seamless-donations' );
    printf( '<div class="%1$s"><p>%2$s</p></div>', esc_attr( $class ), esc_html( $message ) ); 
}

// Create image folder
function burkina_vert_seamless_donation_create_image_folder(){
    global $burkina_dir;
    
    if( ! is_dir( $burkina_dir ) ) {
        $context = array( 'source' => 'burkina-vert' );
        if( ! mkdir( $burkina_dir, 0755 ) ){
            add_action( 'admin_notices', 'burkina_vert_create_folder_error' );
        }
    }
    return false;
}

// Create database table
function burkina_vert_seamless_donation_create_db_table(){
    global $wpdb;
    global $burkina_db_version;
    global $table_name;

    // create database table
    if ( $wpdb->get_var( "SHOW TABLES LIKE '" . $table_name . "'" ) !=  $table_name ){
        $charset_collate = $wpdb->get_charset_collate();

        $create_sql = "CREATE TABLE " . $table_name . " (
            bsd_id INT(11) NOT NULL auto_increment,
            session_id VARCHAR(500) NOT NULL,
            amount DECIMAL(14,2) NOT NULL,
            message VARCHAR(100) NOT NULL,
            team VARCHAR(100) NOT NULL,
            tribute_type TINYINT(1) NOT NULL,
            tribute VARCHAR(100) NOT NULL,
            fname VARCHAR(500) NOT NULL,
            lname VARCHAR(500) NOT NULL,
            email VARCHAR(500) NOT NULL,
            phone VARCHAR(20) NOT NULL,
            compleated BOOLEAN NOT NULL DEFAULT FALSE,
            anonymous BOOLEAN NOT NULL DEFAULT FALSE,
            timestamp TIMESTAMP NOT NULL,
            PRIMARY KEY (bsd_id)
        ) $charset_collate;";

        require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
        dbDelta( $create_sql );

        add_option( 'burkina_db_version', $burkina_db_version );
    }
}

// Initialize instalation
function burkina_vert_seamless_donation_install() {
    burkina_vert_seamless_donation_create_db_table();
    burkina_vert_seamless_donation_create_image_folder();
}

/*
 * Load script resources for frontend users
 */
function burkina_vert_seamless_donation_enqueue_script() {
    wp_enqueue_script( 'jquery' );
    wp_enqueue_script( 'jcrop' );

    // Enqueue jquery mask for the seamless donation form 
    wp_enqueue_script( 'burkina_vert-jquery-mask', plugins_url( '/js/jquery.mask.js', __FILE__ ), array(), '1.14.16', true );

    // Enqueue custom script for the seamless donation form 
    $plugin_data = get_plugin_data( __FILE__ );
    $plugin_version = $plugin_data['Version'];
    wp_enqueue_script( 'burkina-vert-custom-js', plugins_url( '/js/burkina-seamless-donations.js', __FILE__ ), array(), $plugin_version, true );

    // The wp_localize_script used for saving custom seamless donation message
    wp_localize_script(
        'burkina-vert-custom-js',
        'burkina_ajax_obj',
        array(
            'ajaxurl'   => admin_url('admin-ajax.php'),
            'nonce'     => wp_create_nonce('burkina-ajax-nonce')
        )
    );
}
add_action( 'wp_enqueue_scripts', 'burkina_vert_seamless_donation_enqueue_script' );

/*
 * Load style resources for frontend users
 */
function burkina_vert_seamless_donation_enqueue_style() {
    wp_enqueue_style( 'jcrop' );

    $plugin_data = get_plugin_data( __FILE__ );
    $plugin_version = $plugin_data['Version'];
    wp_register_style( 'burkina_seamless_donations_css', plugins_url('/css/style.css', __FILE__ ), array(), $plugin_version );
    wp_enqueue_style( 'burkina_seamless_donations_css' );
}
add_action( 'wp_enqueue_scripts', 'burkina_vert_seamless_donation_enqueue_style' );

/*
 * Load script resources for backend users (admin)
 */
function burkina_vert_seamless_donation_enqueue_script_admin( $hook ) {
    // Only add to the admin.php admin page.
    if ( 'toplevel_page_add-offline-donor' == $hook || 'seamless-donations_page_seamless_donations_tab_settings' == $hook ) {
        wp_enqueue_script( 'jquery' );
        wp_enqueue_script( 'jcrop' );

        // Enqueue jquery mask for the seamless donation form 
        wp_enqueue_script( 'burkina_vert-jquery-mask', plugins_url( '/js/jquery.mask.js', __FILE__ ), array(), '1.14.16', true );

        $script_url = plugins_url( '/js/burkina-seamless-donations-admin.js', __FILE__ );
        wp_register_script( 'burkina-vert-admin-scripts', $script_url, array( 'jquery' ), '1.0', true );
        wp_enqueue_script( 'burkina-vert-admin-scripts' );

        // The wp_localize_script used for saving custom seamless donation message
        wp_localize_script(
            'burkina-vert-admin-scripts',
            'burkina_ajax_obj',
            array(
                'ajaxurl'   => admin_url( 'admin-ajax.php' ),
                'nonce'     => wp_create_nonce( 'burkina-admin-ajax-nonce' )
            )
        );
    }
}
add_action( 'admin_enqueue_scripts', 'burkina_vert_seamless_donation_enqueue_script_admin' );

/*
 * Load style resources for backend users (admin)
 */
function burkina_vert_seamless_donation_enqueue_style_admin() {
    wp_enqueue_style( 'jcrop' );

    $plugin_data = get_plugin_data( __FILE__ );
    $plugin_version = $plugin_data['Version'];
    wp_register_style( 'burkina_seamless_donations_css', plugins_url( '/css/admin-style.css', __FILE__ ), array(), $plugin_version );
    wp_enqueue_style( 'burkina_seamless_donations_css' );
}
add_action( 'admin_enqueue_scripts', 'burkina_vert_seamless_donation_enqueue_style_admin' );

/*
 *  Add async attributes to enqueued scripts to improve SEO
 */
add_filter('script_loader_tag', 'burkina_vert_script_loader_tag', 10, 3);
function burkina_vert_script_loader_tag($tag, $handle, $src) {
    if ($handle === 'burkina-vert-custom-js') {
        if (false === stripos($tag, 'async')) {            
            $tag = str_replace(' src', ' async="async" src', $tag);
        }
    }
    return $tag;    
}

/*
 * Remove the "Funds" menu item from admin side menu
 * This will be replaced by burkina_add_admin_menu function (Offline Donor)
 */
function burkina_remove_admin_menus(){
    remove_menu_page( 'edit.php?post_type=donor' );
    remove_menu_page( 'edit.php?post_type=funds' );
    remove_menu_page( 'edit.php?post_type=donation' );
}
add_action( 'admin_init', 'burkina_remove_admin_menus' );

/*
 * Add Offline Donors
 */
// Offline page form to add offline donors
function burkina_admin_offline_donor_page() {
    echo '<div class="wrap">';
    echo '<h1>Add Offline Donor</h1>';

    $file = plugin_dir_path( __FILE__ ) . "admin/add-offline-donor.php";
    if ( file_exists( $file ) ) {
        require $file;
    }

    echo '</div>';
}

// Offline page list to display offline donors
function burkina_admin_donation_list_page() {
    echo '<div class="wrap">';
    echo '<h1>Donation List</h1>';

    $file = plugin_dir_path( __FILE__ ) . "admin/donation-list-page.php";
    if ( file_exists( $file ) ) {
        require $file;
    }

    echo '</div>';
}

// Add Offline Donor menu
function burkina_add_admin_menu() {
    add_menu_page(
        'Add Offline Donation',
        'Add Offline Donation',
        'manage_options',
        'add-offline-donor',
        'burkina_admin_offline_donor_page',
        'dashicons-palmtree',
        2
    );
    add_menu_page(
        'Donations',
        'Donations',
        'manage_options',
        'burkina_donation_list',
        'burkina_admin_donation_list_page',
        'dashicons-palmtree',
        3
    );
}
add_action( 'admin_menu', 'burkina_add_admin_menu' );

/* 
 * Creates an Ajax to save data on the custom table (burkina_seamless_donations)
 * Online Payment
 */
// Save user image
function burkina_vert_save_image( $last_id ) {
    global $burkina_dir;

    if ( $last_id > 0 && is_uploaded_file( $_FILES['_dgx_donate_image']['tmp_name'] ) ) {
        $extension = end( explode( ".", $_FILES["_dgx_donate_image"]["name"] ) );
        $file_path_name = $burkina_dir . "/cropped_img_$last_id." . $extension;
        move_uploaded_file( $_FILES["_dgx_donate_image"]["tmp_name"], $file_path_name );

        if ( file_exists( $file_path_name ) ) {
            $src_x = $_POST['Jcrop_x'];
            $src_y = $_POST['Jcrop_y'];
            $dst_w = $_POST['Jcrop_w'];
            $dst_h = $_POST['Jcrop_h'];

            $src_w = $_POST['Jcrop_ow'];
            $src_h = $_POST['Jcrop_oh'];

            if ( preg_match( '/jpg|jpeg/i', $extension ) ) {
                $image_tmp = imagecreatefromjpeg( $file_path_name );
            } elseif ( preg_match( '/png/i', $extension ) ) {
                $image_tmp = imagecreatefrompng( $file_path_name );
            } elseif ( preg_match( '/gif/i', $extension ) ) {
                $image_tmp = imagecreatefromgif( $file_path_name );
            } elseif ( preg_match( '/bmp/i', $extension ) ) {
                $image_tmp = imagecreatefrombmp( $file_path_name );
            } else {
                wp_die( 'Invalid format' );
            }

            $image_tmp = imagescale( $image_tmp, $src_w, $src_h, IMG_NEAREST_NEIGHBOUR );
            $new_cropped_image = imagecrop( $image_tmp, ['x' => $src_x, 'y' => $src_y, 'width' => $dst_w, 'height' => $dst_h] );

            if ($new_cropped_image !== FALSE) {
                unlink( $file_path_name );
                $file_path_name = $burkina_dir . "/cropped_img_$last_id.jpg";
                imagejpeg( $new_cropped_image, $file_path_name, 80 );
                imagedestroy( $new_cropped_image );
            } else {			
                wp_die( 'Invalid resize' );
            }
            imagedestroy( $image_tmp );
        }
    }
}
//  Save user data
function burkina_vert_save_custom_data() {
    if ( isset( $_POST ) ) {
        $nonce = $_POST['nonce'];

        if ( ! wp_verify_nonce( $nonce, 'burkina-ajax-nonce' ) ) {
            wp_die( 'Certificate value cannot be verified.' );
        }

        global $wpdb;
        global $table_name;

        if ( $wpdb->get_var( "SHOW TABLES LIKE '" . $table_name . "'" ) ==  $table_name ) {
            $session_id = $_POST['_dgx_donate_session_id'];
            $amount = $_POST['_dgx_donate_amount'];
            $msg = stripslashes( $_POST['_dgx_donate_message'] );
            $team = stripslashes( $_POST['_dgx_donate_team'] );
            $tribute_type = $_POST['_dgx_donate_tribute_type'];
            $tribute = $_POST['_dgx_donate_honoree_name'];
            $first_name = $_POST['_dgx_donate_donor_first_name'];
            $last_name = $_POST['_dgx_donate_donor_last_name'];
            $email = $_POST['_dgx_donate_donor_email'];
            $phone = $_POST['_dgx_donate_donor_phone'];
            $anonymous = $_POST['_dgx_donate_anonymous'];
            
            if ( $anonymous == 1 ) {
            	$first_name = "";
            	$last_name = "";
            }

            $data = array(
                'session_id'    => $session_id,
                'amount'        => $amount,
                'message'       => $msg,
                'team'          => $team,
                'tribute_type'  => $tribute_type,
                'tribute'       => $tribute,
                'fname'         => $first_name,
                'lname'         => $last_name,
                'email'         => $email,
                'phone'         => $phone,
                'anonymous'     => $anonymous
            );
            $format = array(
                '%s',
                '%f',
                '%s',
                '%s',
                '%d',
                '%s',
                '%s',
                '%s',
                '%s',
                '%s',
                '%d'
            );

            $wpdb->insert( $table_name, $data, $format );

            $last_id = $wpdb->insert_id;

            burkina_vert_save_image( $last_id );

            echo $last_id;
        }
    }
    wp_die();
}
add_action( 'wp_ajax_burkina_vert_save_custom_data', 'burkina_vert_save_custom_data' );
add_action( 'wp_ajax_nopriv_burkina_vert_save_custom_data', 'burkina_vert_save_custom_data' );

// Offline Payment
function burkina_vert_save_custom_offline_data() {
    if ( isset( $_POST ) ) {
        $nonce = $_POST['nonce'];

        if ( ! wp_verify_nonce($nonce, 'burkina-admin-ajax-nonce' ) ) {
            wp_die( 'Certificate value cannot be verified.' );
        }

        global $wpdb;
        global $table_name;
        global $burkina_dir;

        if ( $wpdb->get_var( "SHOW TABLES LIKE '" . $table_name . "'" ) ==  $table_name ){
            $session_id = 'OFFLINE';
            $amount = $_POST['_dgx_donate_amount'];
            $msg = stripslashes( $_POST['_dgx_donate_message'] );
            $team = stripslashes( $_POST['_dgx_donate_team'] );
            $tribute_type = $_POST['_dgx_donate_tribute_type'];
            $tribute = $_POST['_dgx_donate_honoree_name'];
            $first_name = $_POST['_dgx_donate_donor_first_name'];
            $last_name = $_POST['_dgx_donate_donor_last_name'];
            $email = $_POST['_dgx_donate_donor_email'];
            $phone = $_POST['_dgx_donate_donor_phone'];
            $anonymous = $_POST['_dgx_donate_anonymous'];

            if($anonymous == 1){
                $first_name = "";
                $last_name = "";
            }

            $data = array(
                'session_id' => $session_id,
                'amount' => $amount,
                'message' => $msg,
                'team' => $team,
                'tribute_type' => $tribute_type,
                'tribute' => $tribute,
                'fname' => $first_name,
                'lname' => $last_name,
                'email' => $email,
                'phone' => $phone,
                'anonymous' => $anonymous,
                'compleated' => 1
            );

            $format = array(
                '%s',
                '%f',
                '%s',
                '%s',
                '%d',
                '%s',
                '%s',
                '%s',
                '%s',
                '%s',
                '%d',
                '%d'
            );

            $wpdb->insert( $table_name, $data, $format );
            $last_id = $wpdb->insert_id;

            if ( $wpdb->last_error !== '' ) {
                echo $wpdb->print_error();
            }

            burkina_vert_save_image( $last_id );
            
            echo $last_id;
        }
    }
    wp_die();
}
add_action( 'wp_ajax_burkina_vert_save_custom_offline_data', 'burkina_vert_save_custom_offline_data' );
add_action( 'wp_ajax_nopriv_burkina_vert_save_custom_offline_data', 'burkina_vert_save_custom_offline_data' );

/*
 * ====================
 * Donation Form
 * ====================
 */
// Reorder donation buttons
function burkina_vert_reorderGivingLevels( $array ){
    return array(5,20,50,100);
}
add_filter( 'dgx_donate_giving_levels', 'burkina_vert_reorderGivingLevels' );

/*
 * Change ammount button and donation frequency
 */
function burkina_vert_changeAmmountSection( $array ) {
    // Change header text
    $array['elements']['header_desc']['value'] = '$1 plante un arbre';

    // Change default selection
    $array['elements']['dgx_donate_giving_level_5']['select'] = false;
    $array['elements']['dgx_donate_giving_level_20']['select'] = true;

    // Change text label
    $array['elements']['dgx_donate_giving_level_5']['prompt'] = '5 arbres';
    $array['elements']['dgx_donate_giving_level_20']['prompt'] = '20 arbres';
    $array['elements']['dgx_donate_giving_level_50']['prompt'] = '50 arbres';
    $array['elements']['dgx_donate_giving_level_100']['prompt'] = '100 arbres';

    // Wrap around label for css styling
    $array['elements']['dgx_donate_giving_level_5']['before'] = '<label class="donate-ammount">';
    $array['elements']['dgx_donate_giving_level_5']['after'] = '</label>';
    $array['elements']['dgx_donate_giving_level_20']['before'] = '<label class="donate-ammount donate-ammount-checked">'; //apply selected color
    $array['elements']['dgx_donate_giving_level_20']['after'] = '</label>';
    $array['elements']['dgx_donate_giving_level_50']['before'] = '<label class="donate-ammount">';
    $array['elements']['dgx_donate_giving_level_50']['after'] = '</label>';
    $array['elements']['dgx_donate_giving_level_100']['before'] = '<label class="donate-ammount">';
    $array['elements']['dgx_donate_giving_level_100']['after'] = '</label>';
    $array['elements']['other_radio_button']['before'] = '<label class="donate-ammount donate-ammount-last">';
    $array['elements']['other_radio_button']['after'] = '</label>';

    // Remove text label and add placeholder
    $array['elements']['_dgx_donate_user_amount']['before'] = '';

    // Add radio box option for donation frequency
    $repeating_section = array(
        'elements' => array(
            '_dgx_donate_no_repeating' => array(
                'type'      => 'radio',
                'id'        => 'dgx-donate-no-repeating',
                'prompt'    => 'Don Unique',
                'before'    => '<label class="donate-frequency donate-frequency-checked">',
                'after'     => '</label>',
                'group'     => '_dgx_donate_repeating',
                'select'    => true,
            ),
            '_dgx_donate_repeating' => array(
                'type'      => 'radio',
                'id'        => 'dgx-donate-repeating',
                'prompt'    => 'Don Mensuel',
                'before'    => '<label class="donate-frequency donate-frequency-last">',
                'after'     => '</label>',
                'group'     => '_dgx_donate_repeating',
                'value'     => 'true',
            ),
        ),
    );
    $array['repeating_section'] = $repeating_section;

    // Add new text field for the personal message
    $message_section = array(
        'elements' => array(
            '_dgx_donate_message' => array(
                'type'        => 'text',
                'class'       => 'dgx-donate-message',
                'class-label' => 'seamless-donations-col-25',
                'class-input' => 'seamless-donations-col-25',
                'size'        => 10,
                'placeholder' => 'Mon message est...',
            ),
        ),
    );
    $array['message_section'] = $message_section;

    // Add new text field for the team name
    $message_section = array(
        'elements' => array(
            '_dgx_donate_team' => array(
                'type'        => 'text',
                'class'       => 'dgx-donate-team',
                'class-label' => 'seamless-donations-col-25',
                'class-input' => 'seamless-donations-col-25',
                'size'        => 10,
                'placeholder' => 'Équipe',
            ),
        ),
    );
    $array['team_section'] = $message_section;

    return $array;
}
add_filter( 'seamless_donations_form_donation_section', 'burkina_vert_changeAmmountSection' );

/* 
 * Change tribute section
 */
function burkina_vert_changeTributeSection( $array ) {
    // Wrap around label for css
    $array['elements']['_dgx_donate_tribute_gift']['before'] = '<label>';
    $array['elements']['_dgx_donate_tribute_gift']['after'] = '</label>';
    $array['elements']['_dgx_donate_tribute_gift']['prompt'] = 'Cochez ici pour que ce don soit fait en l’honneur ou à la mémoire de quelqu’un';

    // Add radio button option
    $tribute_element = array(
        '_dgx_donate_honor_gift' => array(
            'type'      => 'radio',
            'id'        => 'dgx-donate-honor-gift',
            'cloak'     => 'in-honor',
            'prompt'    => 'En l’honneur de …',
            'before'    => '<label>',
            'after'     => '</label>',
            'group'     => '_dgx_donate_tribute_gift_radio',
            'select'    => true,
            'value'     => '0',
        ),
    );
    array_splice( $array['elements'], 2, 0, $tribute_element );

    // Adjust existing button from checkbox to radio
    $array['elements']['_dgx_donate_memorial_gift']['type'] = 'radio';
    $array['elements']['_dgx_donate_memorial_gift']['before'] = '<label>';
    $array['elements']['_dgx_donate_memorial_gift']['after'] = '</label>';
    $array['elements']['_dgx_donate_memorial_gift']['prompt'] = 'En mémoire de …';
    $array['elements']['_dgx_donate_memorial_gift']['id'] = 'dgx-donate-memory-gift';
    $array['elements']['_dgx_donate_memorial_gift']['value'] = '1';
    $array['elements']['_dgx_donate_memorial_gift']['group'] = '_dgx_donate_tribute_gift_radio';

    $array['elements']['_dgx_donate_honoree_name']['before'] = '';
    $array['elements']['_dgx_donate_honoree_name']['placeholder'] = 'Nom de la personne honorée (champ obligatoire)';

    $array['elements']['_dgx_donate_honoree_email_name']['before'] = '';
    $array['elements']['_dgx_donate_honoree_email_name']['placeholder'] = 'Courriel de remerciements à';

    $array['elements']['_dgx_donate_honoree_email']['before'] = '';
    $array['elements']['_dgx_donate_honoree_email']['placeholder'] = 'Courriel';

    return $array;
}
add_filter( 'seamless_donations_form_tribute_section', 'burkina_vert_changeTributeSection' );

/* 
 * Change donor information section
 */
function burkina_vert_changeDonorInformationSection( $array ) {
    $array['elements']['_dgx_donate_donor_first_name']['before'] = '';
    $array['elements']['_dgx_donate_donor_first_name']['placeholder'] = 'Prénom';

    $array['elements']['_dgx_donate_donor_last_name']['before'] = '';
    $array['elements']['_dgx_donate_donor_last_name']['placeholder'] = 'Nom';

    $array['elements']['_dgx_donate_donor_email']['before'] = '';
    $array['elements']['_dgx_donate_donor_email']['placeholder'] = 'Courriel';

    $array['elements']['_dgx_donate_donor_phone']['before'] = '';
    $array['elements']['_dgx_donate_donor_phone']['placeholder'] = 'Téléphone';

    $array['elements']['_dgx_donate_anonymous']['before'] = '<label>';
    $array['elements']['_dgx_donate_anonymous']['after'] = '</label>';
    $array['elements']['_dgx_donate_anonymous']['prompt'] = 'Ne publiez pas mon nom. Je souhaite rester anonyme';

    $array['elements']['_dgx_donate_add_to_mailing_list']['before'] = '<label>';
    $array['elements']['_dgx_donate_add_to_mailing_list']['after'] = '</label>';

    $keepArray1 = array_slice( $array['elements'], 0, 4 );
    $keepArray2 = array_slice( $array['elements'], -1 );
    $moveArray = array_slice( $array['elements'], 4, 2 );

    $array['elements'] = array_merge( $keepArray1, $keepArray2, $moveArray );

    return $array;
}
add_filter( 'seamless_donations_form_donor_section', 'burkina_vert_changeDonorInformationSection' );

/*
 * Save payment confirmation
 */
function burkina_confirm_payment( $subject, $donation_id ) {
    global $wpdb;
    global $table_name;

    $session_id = get_post_meta( $donation_id, '_dgx_donate_session_id', true );

    // Check if custom information exists
    $results =  $wpdb->get_results( 
        $wpdb->prepare( "SELECT * FROM $table_name WHERE session_id = %s LIMIT 1", $session_id ) 
    );

    if ( $results ) {
        // Custom information exists
        $is_compleated = false;
        foreach ( $results as $result ) {
            if ( $result->compleated == 1 ) {
                $is_compleated = true;
            }
        }
        if ( $is_compleated == false ) {
            // Update as compleated
            $data = array('compleated' => 1);
            $where = array('session_id' => $session_id);
            $format = array('%d','%s');
            $wpdb->update( $table_name, $data, $where, $format );
        } else {
            // This was already compleated so we need to insert a new full custom information
            $amount = 0;
            $message = '';
            $team = '';
            $tribute = '';
            $first_name = '';
            $last_name = '';
            $email = '';
            $anonymous = '';

            foreach ($results as $result){ 
                $amount = $result->amount;
                $message = $result->message;
                $team = $result->team;
                $tribute = $result->tribute;
                $first_name = $result->fname;
                $last_name = $result->lname;
                $email = $result->email;
                $anonymous = $result->anonymous;
            }

            $data = array(
                'session_id' => $session_id,
                'amount' => $amount,
                'message' => $message,
                'team' => $team,
                'tribute' => $tribute,
                'fname' => $first_name,
                'lname' => $last_name,
                'email' => $email,
                'anonymous' => $anonymous,
                'compleated' => 1
            );
            $format = array(
                '%s',
                '%f',
                '%s',
                '%s',
                '%s',
                '%s',
                '%s',
                '%s',
                '%d',
                '%d'
            );
            $wpdb->insert( $table_name, $data, $format );
        }
    } else {
        // Insert partial custom information
        $amount = get_post_meta( $donation_id, '_dgx_donate_amount', true );
        $first_name = get_post_meta( $donation_id, '_dgx_donate_donor_first_name', true );
        $last_name = get_post_meta( $donation_id, '_dgx_donate_donor_last_name', true );
        $email = get_post_meta( $donation_id, '_dgx_donate_donor_email', true );

        $data = array(
            'session_id' => $session_id,
            'amount' => $amount,
            'fname' => $first_name,
            'lname' => $last_name,
            'email' => $email,
            'compleated' => 1
        );
        $format = array(
            '%s',
            '%f',
            '%s',
            '%s',
            '%s',
            '%d'
        );
        $wpdb->insert( $table_name, $data, $format );
    }
    return $subject;
}
add_filter( 'seamless_donations_email_subject', 'burkina_confirm_payment', 10, 2 );

/* 
 * ================================
 * Shortcodes for frontpage ranking
 * ================================
 */
/*
 * Latest Donors
 */
function get_latest_donors() {
    global $wpdb;
    global $table_name;

    $html = '';

    $number_to_show = 30;

    $results =  $wpdb->get_results( 
        $wpdb->prepare( "SELECT bsd_id, amount, message, team, tribute_type, tribute, fname, lname, anonymous FROM $table_name WHERE compleated = 1 ORDER BY bsd_id DESC LIMIT %d", $number_to_show )
    );

    if ( $results ) {
        $html .= '<ul class="burkina-ranking">';
        $count = 1;

        foreach ( $results as $result ) {
            $filename = '/wp-content/uploads/burkina-vert/cropped_img_' . $result->bsd_id . '.jpg';

            if ( ! is_file('.'.$filename ) ) {                
                $filename = "/wp-content/uploads/burkina-vert/img.jpg";
            }

            $html .= '<li>';
            $html .= '<div class="ranking-col1">';
            $html .= '<div class="ranking-counter"><span>' . $count . '</span></div>';
            $html .= '<div class="ranking-photo"><span class="burkina-hex"><img loading="lazy" class="burkina-ranking-img" alt="Burkina Vert\'s Ranking" width="80" height="80" src="' . $filename . '"></span></div>';
            $html .= '</div><div class="ranking-col2">';
            $html .= '<div class="ranking-team">' . $result->team . '</div>';

            if ( $result->anonymous == 1 ) {
                $html .= '<div class="ranking-name">Anonyme</div>';
            } else {
                $html .= '<div class="ranking-name">' . $result->fname . ' ' . $result->lname . '</div>';
            }

            $html .= '</div><div class="ranking-col3">';

            if ( $result->tribute != '' ) {
                if( $result->tribute_type == '0' ) {
                    $tributeLabel = 'En l’honneur de: ';
                } else {
                    $tributeLabel = 'En mémoire de: ';
                }
                $html .= '<div class="ranking-tribute">' . $tributeLabel . $result->tribute . '</div>';
            }

            $html .= '<div class="ranking-message">' . $result->message . '</div>';
            $html .= '</div><div class="ranking-col4">';
            $html .= '<div class="ranking-total">' . number_format( intval( $result->amount ), 0, "", "." ) . ' arbres</div>';
            $html .= '</div></li>';
            $count++;
        }

        $html .= "</ul>";
    }
    return $html;
}
function burkina_vert_latest_donors_shortcode() {
    return get_latest_donors();
}
add_shortcode( 'top_latest', 'burkina_vert_latest_donors_shortcode' );

/*
 * Leadership Board by Top Donors
 */
function get_top_donors() {
    global $wpdb;
    global $table_name;

    $html = '';

    $number_to_show = 30;

    $results =  $wpdb->get_results( 
        $wpdb->prepare( "SELECT bsd_id, SUM( amount ) as total,
            SUBSTRING_INDEX( GROUP_CONCAT( CAST( message AS CHAR ) ORDER BY bsd_id DESC ), ',', 1 ) AS message,
            team, tribute_type, SUBSTRING_INDEX( GROUP_CONCAT( CAST( tribute AS CHAR ) ORDER BY bsd_id DESC ), ',', 1 ) AS tribute,
            fname, lname, anonymous, GROUP_CONCAT( DISTINCT anonymous ) FROM $table_name WHERE compleated = 1 GROUP BY email, team ORDER BY total DESC LIMIT %d", $number_to_show )
    );
    if ( $results ) {
        $html .= '<ul class="burkina-ranking">';
        $count = 1;

        foreach ( $results as $result ) {
            $filename = '/wp-content/uploads/burkina-vert/cropped_img_' . $result->bsd_id . '.jpg';
            if ( ! is_file( '.'.$filename ) ) {                
                $filename = "/wp-content/uploads/burkina-vert/img.jpg";
            }

            $html .= '<li>';
            $html .= '<div class="ranking-col1">';
            $html .= '<div class="ranking-counter"><span>' . $count . '</span></div>';
            $html .= '<div class="ranking-photo"><span class="burkina-hex"><img loading="lazy" class="burkina-ranking-img" alt="Burkina Vert\'s Ranking" width="80" height="80" src="' . $filename . '"></span></div>';
            $html .= '</div><div class="ranking-col2">';
            $html .= '<div class="ranking-team">' . $result->team . '</div>';
            if ( $result->anonymous == 1 ) {
                $html .= '<div class="ranking-name">Anonyme</div>';
            } else {
                $html .= '<div class="ranking-name">' . $result->fname . ' ' . $result->lname . '</div>';
            }
            $html .= '</div><div class="ranking-col3">';
            if ( $result->tribute != '' ){
                if ( $result->tribute_type == '0' ) {
                    $tributeLabel = 'En l’honneur de: ';
                } else {
                    $tributeLabel = 'En mémoire de: ';
                }
                $html .= '<div class="ranking-tribute">' . $tributeLabel . $result->tribute . '</div>';
            }
            $html .= '<div class="ranking-message">' . $result->message . '</div>';
            $html .= '</div><div class="ranking-col4">';
            $html .= '<div class="ranking-total">' . number_format( intval( $result->total ), 0, "", "." ) . ' arbres</div>';
            $html .= '</div></li>';

            $count++;
        }

        $html .= "</ul>";
    }
    return $html;
}
function burkina_vert_top_donors_shortcode() {
    return get_top_donors();
}
add_shortcode('top_donors', 'burkina_vert_top_donors_shortcode'); 

/*
 * Leadership Board by Top Team
 */
function get_top_teams() {
    global $wpdb;
    global $table_name;

    $html = '';

    $number_to_show = 30;

    $results =  $wpdb->get_results( 
        $wpdb->prepare("SELECT bsd_id, SUM( amount ) as total, SUBSTRING_INDEX( GROUP_CONCAT(CAST(message AS CHAR) ORDER BY bsd_id DESC), ',', 1 ) AS message,
        team, SUBSTRING_INDEX( GROUP_CONCAT( CAST( tribute AS CHAR) ORDER BY bsd_id DESC ), ',', 1 ) AS tribute, fname, lname, anonymous,
        GROUP_CONCAT( DISTINCT anonymous ) FROM $table_name WHERE compleated = 1 AND team <> '' GROUP BY team ORDER BY total DESC LIMIT %d", $number_to_show )
    );

    if ( $results ) {
        $html .= '<ul class="burkina-ranking">';
        $count = 1;

        foreach ( $results as $result ) {
            $filename = "/wp-content/uploads/burkina-vert/img.jpg";
            $html .= '<li>';
            $html .= '<div class="ranking-col1">';
            $html .= '<div class="ranking-counter"><span>' . $count . '</span></div>';
            $html .= '<div class="ranking-photo"><span class="burkina-hex"><img loading="lazy" class="burkina-ranking-img" alt="Burkina Vert\'s Ranking" width="80" height="80" src="' . $filename . '"></span></div>';
            $html .= '</div><div class="ranking-col2">';
            $html .= '<div class="ranking-team"></div>';
            $html .= '<div class="ranking-name">' . $result->team . '</div>';
            $html .= '</div><div class="ranking-col3">';
            $html .= '</div><div class="ranking-col4">';
            $html .= '<div class="ranking-total">' . number_format( intval( $result->total ), 0, "", "." ) . ' arbres</div>';
            $html .= '</div></li>';
            $count++;
        }

        $html .= "</ul>";
    }
    return $html;
}
function burkina_vert_top_teams_shortcode(){
    return get_top_teams();
}
add_shortcode( 'top_teams', 'burkina_vert_top_teams_shortcode' ); 