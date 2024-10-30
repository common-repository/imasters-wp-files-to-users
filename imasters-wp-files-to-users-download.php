<?php
/**
 * Get directory and name of file
 */
$file_name = $_GET['file_name'];

$file = dirname( __FILE__ ) . '/files/' . $file_name;

if ( !function_exists( 'add_action' ) ) {
    $wp_root = '../../..';
    if ( file_exists( $wp_root.'/wp-load.php' ) ) {
        require_once( $wp_root.'/wp-load.php' );
    } else {
        require_once( $wp_root.'/wp-config.php' );
    }
}

global $wp_roles;
global $wpdb;

/**
 * Get the current (logged) user
 */
$current_user = wp_get_current_user();

/**
 * Get the ID of the user logged
 */
$user_id = $current_user->ID;

//$objUserId = $wpdb->get_var("SELECT
//            file_user_id
//            FROM $wpdb->imasters_wp_files_to_users
//            WHERE file_name = pepito.png");

$varUserId = $wpdb->get_var( $wpdb->prepare( "
            SELECT file_user_id
            FROM " . $wpdb->prefix ."imasters_wp_files_to_users
            WHERE file_name_file = '%s'
            ",
            $file_name
            ));


if( $user_id == $varUserId ) {
    header('Content-type: application/save');
    header('Content-Disposition: attachment; filename="' .  $file_name . '"');
    header('Content-Transfer-Encoding: binary');
    header('Expires: 0');
    header('Pragma: no-cache');
    readfile( $file );
    exit();
}
else {
echo _e( 'File not found!', 'iwpftu' );
}
?>