<?php
/*
Plugin Name: iMaters WP Files to Users
Plugin URI: http://code.imasters.com.br/wordpress/plugins/imasters-wp-files-to-users/
Description: iMasters WP Files to users is used to manage and send files to specific users.
Version: 0.2
Author: Apiki
Author URI: http://www.apiki.com
*/
/* Copyright 2009  Apiki (email : leandro@apiki.com)

   This program is free software; you can redistribute it and/or modify
   it under the terms of the GNU General Public License as published by
   the Free Software Foundation; either version 2 of the License, or
   (at your option) any later version.

   This program is distributed in the hope that it will be useful,
   but WITHOUT ANY WARRANTY; without even the implied warranty of
   MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
   GNU General Public License for more details.

   You should have received a copy of the GNU General Public License
   along with this program; if not, write to the Free Software
   Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/
/**
 * Create class iMasters WP Files to Users
 */
class IMASTERS_WP_FILES_TO_USERS{
	
	/**
	 * Call the function always initilize plugin
	 * @global $wpdb native wp var for database support
	 */
	function IMASTERS_WP_FILES_TO_USERS()
	{
            global $wpdb;

            /**
             * Name of databese plugin tables
             */
            $wpdb->imasters_wp_files_to_users	= $wpdb->prefix . 'imasters_wp_files_to_users';

            //Call the function to build the plugin menu
            add_action( 'admin_menu', array( &$this, 'menu' ) );

            //Call the function to build the plugin database
            add_action( 'activate_imasters-wp-files-to-users/imasters-wp-files-to-users.php', array( &$this, 'install' ) );

            //Call the function to insert the JavaScript environment
            add_action( 'wp_print_scripts', array( &$this, 'header' ) );

            //Call Function to textdomain for translation language
            add_action( 'init', array( &$this, 'textdomain' ) );

	}
	
	/**
	 * This metod build the menu used by this plugin
	 */
	function menu()
	{
            /**
             * Create the menu parent page
             */
            if ( function_exists('add_menu_page') ) :
                    add_menu_page( __( 'iMasters WP Files to Users', 'iwpftu' ), __( 'iMasters WP Files to Users', 'iwpftu' ), 'administrator_files_to_user', 'imasters-wp-files-to-users/imasters-wp-files-to-users-manager.php', '' , plugins_url( 'imasters-wp-files-to-users/assets/images/imasters.png' ) );
            endif;

            /**
             * Create the menu submenu pages
             */
            if ( function_exists('add_submenu_page') ) :
                    add_submenu_page( 'imasters-wp-files-to-users/imasters-wp-files-to-users-manager.php' , __( 'Manager Files', 'iwpftu' ), 	__( 'Manager Files', 'iwpftu' ),'admin_files_to_user', 'imasters-wp-files-to-users/imasters-wp-files-to-users-manager.php');
                 if(!current_user_can( 'install_plugins') )
                    add_submenu_page( 'imasters-wp-files-to-users/imasters-wp-files-to-users-manager.php' , __( 'Files', 'iwpftu' ),	__( 'Files', 'iwpftu' ),'subscriber_files_to_user', 'imasters-wp-files-to-users/imasters-wp-files-to-users-manager-user.php');
                    //add_submenu_page( 'imasters-wp-files-to-users/imasters-wp-files-to-users-manager.php' , __( 'Files', 'iwpftu' ),	__( 'Files', 'iwpftu' ),'contributor_files_to_user', 'imasters-wp-files-to-users/imasters-wp-files-to-users-manager-user.php');

                    add_submenu_page( 'imasters-wp-files-to-users/imasters-wp-files-to-users-manager.php' , __( 'Uninstall', 'iwpftu' ), 	__( 'Uninstall', 'iwpftu' ),'admin_files_to_user', 'imasters-wp-files-to-users/imasters-wp-files-to-users-uninstall.php');
            endif;	
	}
	
	/**
	 * This metod install plugin, build table of database
	 */
	function install()
	{
		global $wpdb;

                $role = get_role( 'administrator' );
                if ( !$role->has_cap( 'admin_files_to_user' ) ) :
                    $role->add_cap( 'admin_files_to_user' );
                endif;

                $role_subscribe = get_role( 'subscriber' );
                if ( !$role_subscribe->has_cap( 'subscriber_files_to_user' ) ) :
                    $role_subscribe->add_cap( 'subsc_files_to_user' );
                endif;

//                $role_contributor = get_role( 'contributor' );
//                if ( !$role_contributor->has_cap( 'contributor_files_to_user' ) ) :
//                    $role_contributor->add_cap( 'contributor_files_to_user' );
//                endif;


                //Contributor
		
		//This file contains the dbDelta function, and it�s not loaded by default.
		require_once ABSPATH . 'wp-admin/upgrade-functions.php';
		
		// Check if the table imasters_wp_files_to_users was already created
		if ( $wpdb->get_var("SHOW TABLES LIKE '$wpdb->imasters_wp_files_to_users'") != $wpdb->imasters_wp_files_to_users ) :
			
			// Build the SQL for the plugin tables		
			$sql = "CREATE TABLE " . $wpdb->imasters_wp_files_to_users . " (
				file_id 			INT( 11 ) UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,	
				file_name 			VARCHAR( 255 ) NOT NULL,
				file_name_file 		VARCHAR( 255 ) NOT NULL,
				file_user_id 		INT( 11 ) UNSIGNED NOT NULL,
				file_bytes 			INT( 11 ) UNSIGNED NOT NULL,
				file_registered_at 	TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
				)  ENGINE = MYISAM COMMENT = 'Table used by plugin iMasters WP Files To Users';
			";
			
			//This function examines the current table structure, compares it to the desired table structure, and either adds or modifies the table as necessary
			dbDelta($sql);
		
		endif;
	}
	
	/**
	 * This method insert header with JavaScript
	 */
	function header()
	{
             if (! empty($_GET['page']))
                if ( strpos( $_GET['page'], 'imasters-wp-files-to-users' ) !== false ) :
                    $ftu_scripts_ver = filemtime( dirname( __FILE__ ) . '/assets/javascript/imasters-wp-files-to-users-scripts.js' );
                    wp_enqueue_script( 'ftu.scripts', WP_PLUGIN_URL . '/imasters-wp-files-to-users/assets/javascript/imasters-wp-files-to-users-scripts.js', array( 'jquery' ), $ftu_scripts_ver );
                    echo "\n<!-- START - Generated by iMasters WP Files To Users -->";
                    echo '<script type="text/javascript">' . "\n";
                    echo '/* <![CDATA[ */';
                    printf( 'var confirm_delete_message = "%s";', __( 'You are about to delete the files registered by iMasters WP File to Users? \n Choose [Cancel] To Cancel, [OK] to delete.', 'iwpftu' ) );
                    echo '/* ]]> */';
                    echo '</script>';
                    echo "\n<!-- END - Generated by iMasters WP Files To Users -->\n";
                endif;
	}
	
	/**
	 * Function has upoload of file
	 * @param $_FILES array
	 * @param file_name string
	 */
	function upload( $_FILES, $file_name, $action )
	{
		// Define path of file
		$file_path = WP_PLUGIN_DIR . "/imasters-wp-files-to-users/files/";
		
		// Get path information
		$arrPathParts = pathinfo($file_path . $_FILES['file_upload']['name']);
		
		// Get Extension of file
		$file_ext = $arrPathParts['extension'];
		
		// Remove accents and spaces white
		$file_name = $this->clean_string( $file_name );
		
		// Return TREU if file with name filename was sent per POST HTTP
		if ( is_uploaded_file($_FILES['file_upload']['tmp_name']) ) :
		
			// This functin check for has certain what the file digned per filename is a file of upload valid.
			if ( move_uploaded_file($_FILES['file_upload']['tmp_name'], $file_path . basename($_FILES['file_upload']['name'])) ) :
			
				if ( $action == 'add' ) :
				
					// Check if don't exists file 
					if ( !$this->check_file_exists( $file_path, $file_name, $file_ext ) ) :
					
						// Rename upload was
						$rename = rename($file_path . $_FILES['file_upload']['name'], $file_path . $file_name . '.' . $file_ext);
						
					endif;
				endif ;
				
				if ( $action == 'edit' ) :
				
					/** Check if exists file */
					if ( $this->check_file_exists( $file_path, $file_name, $file_ext ) ) :
						
						/** Correlate name */
						$name_of_file = $file_name . '.' . $file_ext;
						
						/** Execute function for del file */
						$del_file = $this->delete_file( $name_of_file );
						
						if ( $del_file ) :
							
							/** Rename upload was */
							$rename = rename($file_path . $_FILES['file_upload']['name'], $file_path . $file_name . '.' . $file_ext);
							
						endif;
						
					else :
						
						/** Rename upload was */
						$rename = rename($file_path . $_FILES['file_upload']['name'], $file_path . $file_name . '.' . $file_ext);
						
					endif;
				endif;
			
				if ( $rename ) :
				
					return $file_name . '.' . $file_ext;
				else :
				
					return false;
				endif;
			endif;
		endif;	
	}
	
	/**
	 * 	Function check if upload is valid
	 *
	 *	@param	array	$_FILES
	 *	@param	string	$file_name
	 *	@return	bool
	 */
	 function check_upload($_FILES, $file_name)
	 {
		// Array with extensions blockeds
		$arrExtensionsBlocked = array( 'exe', 'cmd', 'bat', 'jar', 'scr', 'ubs', 'ws', 'htaccess' );
		
		// Define path of file
		$file_path = WP_PLUGIN_DIR . "/imasters-wp-files-to-users/files/";
	
		// Get path information
		$arrPathParts = pathinfo($file_path . $_FILES['file_upload']['name']);
		
		// Get Extension of file
		$file_ext = $arrPathParts['extension'];
		
		// Check here
		$type_file_extension = in_array($file_ext, $arrExtensionsBlocked);
	
		if ( $type_file_extension ) :
			return false;
		else :
			return true;
		endif;
	 }
	 
	 /**
	 *	Function convert bytes to kbytes
	 *
	 *	@param	int		$bytes
	 *	@return	float
	 */
	function convert_bytes( $bytes )
	{
		$kbytes = (($bytes / 1024 * 100000) / 100000);
		
		return number_format($kbytes,0);
	}
			
			
	/**
	 * 	Delete files in directory and database
	 *
	 * 	@global	$wpdb 	Object database
	 * 	@param 	string 	$strFileName	Name of the files in directory
	 * 	@return boolean
	 */
	function delete_file( $strFileName )
	{
		/** Directory where the files is there */
		$fileDir = WP_PLUGIN_DIR . '/imasters-wp-files-to-users/files/';
		
		/** Check if the files and thumbs exist and delete it in directory	*/
		if ( file_exists($fileDir . $strFileName) ) :
			
			unlink($fileDir . $strFileName);
			return true;
		else :
			return false;
		endif;
	}		
	
	/** 
	 * Function check file exists
	 * $param file, path, ext string
	 */
	function check_file_exists( $file_path, $file_name, $file_ext )
	{
		/** Path of file */
		$file = $file_path . $file_name . '.' . $file_ext;
	
		if( file_exists( $file ) )
			return true;
		else
			return false;
	}
	
	/**
	 * This function remove  accents and minimize chars
 	 *
 	 * @param string str 
	 */
	function clean_string( $str )
	{
		/* Minimize char */
	
		// decode file name to utf-8
		$str = utf8_decode( $str );
		// lower string
		$str = strtolower( $str );
		// encode again
		$str = utf8_encode( $str );
		
		/* Remove accents */
                $str = str_replace( array( 'á', 'à', 'â', 'ã', 'ä' ),	'a', $str );
                $str = str_replace( array( 'é', 'è', 'ê', 'ë' ),		'e', $str );
                $str = str_replace( array( 'í', 'ì', 'î', 'ï' ),		'i', $str );
                $str = str_replace( array( 'ó', 'ò', 'ô', 'õ', 'ö' ),	'o', $str );
                $str = str_replace( array( 'ú', 'ù', 'û', 'ü' ), 		'u', $str );
                $str = str_replace('ç','c', $str);
		
		// Remove spaces
		$str = trim($str);
		
		// Replace
		$str = str_replace( ' ', '-', $str );
			
		return $str;
	}

        /**
         *Create the textdomain for translation language
         */
        function textdomain()
        {
            load_plugin_textdomain('iwpftu',False,'wp-content/plugins/imasters-wp-files-to-users/assets/languages');
        }

        //Delete folder function
        function deleteDirectory($dir)
        {
            if (!file_exists($dir)) return true;
            if (!is_dir($dir) || is_link($dir)) return unlink($dir);
            foreach (scandir($dir) as $item) {
                if ($item == '.' || $item == '..') continue;
                if (!$this->deleteDirectory($dir . "/" . $item)) {
                    chmod($dir . "/" . $item, 0777);
                if (!$this->deleteDirectory($dir . "/" . $item)) return false;
            };
        }
        return rmdir($dir);
    }
}
/**
 * Instance the object IMASTERS_WP_FILES_TO_USERS
 */
    $objIMASTERS_WP_FTU = new IMASTERS_WP_FILES_TO_USERS();
?>