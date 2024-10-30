<?php
/**
 * Define name of base_name and base_page
 */
 $base_name = plugin_basename('imasters-wp-files-to-users/imasters-wp-files-to-users-manager-user.php');
 $base_page = 'admin.php?page='.$base_name;
?>
<div class="wrap">

<?php 
// Show a message to user about the insertion proccess
if ( !empty($text_message) ) : 
?>

<div id="message" class="<?php echo $class_name; ?>">
	<p><?php echo $text_message; ?> <a href="<?php echo $base_page; ?>" title="<?php _e( 'Back to files', 'iwpftu' ); ?>"><?php _e( 'Back to files', 'iwpftu' ); ?></a></p>
</div>
<?php endif; ?>
   
	<form action="<?php echo htmlspecialchars($_SERVER['REQUEST_URI']); ?>" method="post">
    
    	<h2><img style="margin-right: 5px;" src="<?php echo plugins_url( 'imasters-wp-files-to-users/assets/images/imasters32.png' )?>" alt="imasters-ico"/><?php _e( 'Files view', 'iwpftu' ); ?></h2>
        
        <table class="widefat fixed">
            <thead>
                <tr>
                    <th scope="col"><?php _e( 'File name', 'iwpftu' ); ?></th>
                    <th scope="col"><?php _e( 'Download', 'iwpftu' ); ?></th>
                    <th scope="col"><?php _e( 'Date', 'iwpftu' ); ?></th>
                    <th scope="col"><?php _e( 'Size', 'iwpftu' ); ?></th>
                </tr>
            </thead>
            <tbody>
            <?php
			
			global $wp_roles;
			
			/**
			 * Get the current (logged) user
			 */	
			$current_user 	= 	wp_get_current_user();
			
			/**
			 * Get the ID of the user logged
			 */
			$user_id 		= 	$current_user->ID;
			
			
			$objFiles = $wpdb->get_results("SELECT 
				file_id, file_name, file_name_file, file_user_id, file_bytes, file_registered_at
				FROM $wpdb->imasters_wp_files_to_users
				WHERE file_user_id = $user_id
			");
	
			if( $objFiles ) :
                            foreach( $objFiles as $objFile ) :
				  
					$download_path = WP_PLUGIN_URL . '/imasters-wp-files-to-users/imasters-wp-files-to-users-download.php';
					$file_name = $objFile->file_name_file;
			?>
            	<tr>
                    <td><a href="<?php echo $download_path; ?>?file_name=<?php echo $file_name; ?>"><?php echo $objFile->file_name; ?></a></td>
                    <td><a href="<?php echo $download_path; ?>?file_name=<?php echo $file_name; ?>"><?php _e( 'Download', 'iwpftu' ); ?></a></td>
                    <td><?php echo date('d/m/Y', strtotime($objFile->file_registered_at)); ?></td>
                    <td><?php echo $objIMASTERS_WP_FTU->convert_bytes( $objFile->file_bytes ) . ' KB'; ?></td>
                </tr>
                <?php
                            endforeach;
                        else :
                ?>
                <tr>
                	<td colspan="4"><strong><?php _e( 'Any file inserted to you, sorry.', 'iwpftu' ); ?></strong></td>
                </tr>
            <?php endif;?>
            </tbody>
        </table>
    </form>
</div> <!-- / wrap -->