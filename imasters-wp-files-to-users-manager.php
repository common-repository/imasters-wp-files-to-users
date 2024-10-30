<?php 
/**
 * Define name of base_name and base_page
 */
$base_name = plugin_basename('imasters-wp-files-to-users/imasters-wp-files-to-users-manager.php');
$base_page = 'admin.php?page='.$base_name;

if ( isset( $_POST['act'] ) ) :
	switch( $_POST['act'] ) :
		case 'add_file' :
				
			// Include validation class
			include 'includes/validation.php';
			
			// Import variables into the current symbol table from an array
			extract($_POST, EXTR_SKIP);
			
			// set the errors array to empty, by default
			$errors = array(); 
			
			// stores the field values
			$fields = array(); 
			
			// Define an empty success message, by default
			$success_message = "";

			// stores the validation rules
			$rules = array();
			
			// standard form fields
			$rules[] = "required,file_name," . __( 'Insert the file name.', 'iwpftu' );
			$rules[] = "required,file_upload," . __( 'Select a file.', 'iwpftu' );
			
			// hack validation :D
			$_POST['file_upload'] = $_FILES["file_upload"]["name"];
			
			// get size of file
			$file_bytes = $_FILES["file_upload"]["size"];
			
			// Check the validation rules against the values informed
			$errors = validateFields($_POST, $rules);
			
			// If no errors, procede
			if ( empty($errors) ) :
				
				// If the error was 1 (one) the uploaded file exceeds the upload_max_filesize directive in php.ini
				if ( $_FILES['file_upload']['error'] == 1 ) :
				
					// Set up a user message
					$text_message = __( 'The file exceeded the size limit.', 'iwpftu' );
					$class_name = 'error';
					break;
					
				else :
				
					$check_upload = $objIMASTERS_WP_FTU->check_upload($_FILES, $file_name);
					
					$extension = explode( '.', $_FILES['file_upload']['name'] );
					$extension = end( $extension );
					
					if ( $check_upload ) :
						// Upload the image
						$add_file = $objIMASTERS_WP_FTU->upload($_FILES, $file_name, 'add' );
						
						if ( $add_file ) :
					
							//Add the values in DB
							$added = $wpdb->query(sprintf("
								INSERT INTO $wpdb->imasters_wp_files_to_users
								(file_name,
								 file_name_file,
								 file_user_id,
								 file_bytes
								 )
								VALUES
								('%s', '%s', %d, %d)
								",
								$file_name,
								$add_file,
								$file_user_id,
								$file_bytes
							));
							if ( $added ) :
							
								// Set up a user message
								$text_message 	= __( 'File has been sent.', 'iwpftu' );
								$class_name 	= 'updated fade';
							else :
							
								// Set up a user message
								$text_message 	= __( 'Was not possible send file.', 'iwpftu' );
								$class_name 	= 'error';
							endif;	
						else :
							// Set up a user message
							$text_message 	= __( 'Already exists a file with this name.', 'iwpftu' );
							$class_name 	= 'error';
						endif;
						
					else :
						// Set up a user message
						$text_message = sprintf('%s "%s".', $extension, __( 'For security, is not allowed sent files with extension', 'iwpftu' ) );
						$class_name = 'error';
					endif;
					
				endif;

			endif;
		break;		
		
		case 'edit_file' :
			// Include validation class
			include 'includes/validation.php';
			
			// Import variables into the current symbol table from an array
			extract($_POST, EXTR_SKIP);
			
			// set the errors array to empty, by default
			$errors = array(); 
			
			// stores the field values
			$fields = array(); 
			
			// Define an empty success message, by default
			$success_message = "";

			// stores the validation rules
			$rules = array();
			
			// standard form fields
			$rules[] = "required,file_name," . __( 'Insert the file name.', 'iwpftu' );
			if ( isset( $_POST['check_file_edit'] ) ) :
				$rules[] = "required,file_upload," . __( 'Select a file.', 'iwpftu' );
			endif;
			
			// hack validation :D
			$_POST['file_upload'] = $_FILES["file_upload"]["name"];
			
			// get size of file
			$file_bytes = $_FILES["file_upload"]["size"];
			
			$file_tmp = $_FILES["file_upload"]["tmp_name"];
			
			// Check the validation rules against the values informed
			$errors = validateFields($_POST, $rules);
			
			// If no errors, procede
			if ( empty($errors) ) :
				
				// Create image and thumb image if a image was selected
				if ( isset($_POST['check_file_edit']) and !empty($file_tmp) ) :
				
					// If the error was 1 (one) the uploaded file exceeds the upload_max_filesize directive in php.ini
					if ( $_FILES['file_upload']['error'] == 1 ) :
					
						// Set up a user message
						$text_message 	= _( 'The file exceeded the size limit.', 'iwpftu');
						$class_name 	= 'error';
						break;
						
					else :
				
					// Upload the image
					$add_file = $objIMASTERS_WP_FTU->upload($_FILES, $file_name, 'edit');
					
					endif;
				
					if ( $add_file ) :
						//Add the values in DB
						$added = $wpdb->query( sprintf( "
							UPDATE $wpdb->imasters_wp_files_to_users
							SET 
							file_name 		= '%s',
							file_name_file 	= '%s',
							file_user_id = %d,
							file_bytes = %d
							WHERE
							file_id = %d
							",
							$file_name,
							$add_file,
							$file_user_id,
							$file_bytes,						
							$_POST['file_id']
						) );
						
						if ( strtolower( $add_file ) <> strtolower( $_POST['name_older'] ) ) : 
							$objIMASTERS_WP_FTU->delete_file( $_POST['name_older'] );
						endif; 
						
					else :
						// Set up a user message
						$text_message = __( 'Already exists a file with this name.', 'iwpftu' );
						$class_name = 'error';
					endif;
				
				else :
					//Add the values in DB
						$added = $wpdb->query(sprintf("
							UPDATE $wpdb->imasters_wp_files_to_users
							SET 
							file_name 		= '%s',
							file_user_id 	= %d
							WHERE
							file_id = %d
							",
							$file_name,
							$file_user_id,
							$_POST['file_id']
						));
				endif;
						if ( $added ) :
						
							// Set up a user message
							$text_message 	= __( 'File has been edited.', 'iwpftu' );
							$class_name 	= 'updated fade';
						else :
						
							// Set up a user message
							$text_message 	= __( "Any alteration didn't happen", 'iwpftu' );
							$class_name 	= 'error';
						endif;	
			endif;
		break;	
		
		case __( 'Delete selected', 'iwpftu' ) :
			if ( isset($_POST['delete']) ) :
				
				foreach( $_POST['delete'] as $file_id ) :
					
					$file_id = (int)$file_id;
					
					$objFileName = $wpdb->get_var( $wpdb->prepare ( "
						SELECT file_name_file FROM $wpdb->imasters_wp_files_to_users
						WHERE file_id = %d
						", 
						$file_id 
					) );
		
					if ( $objFileName ) 
						$objIMASTERS_WP_FTU->delete_file( $objFileName );
					
					// Delete the records
					$wpdb->query( "DELETE FROM $wpdb->imasters_wp_files_to_users WHERE file_id = $file_id" );
					
				endforeach;
				// Set up a user message
				$text_message 	= __( 'File has been deleted', 'iwpftu' );
				$class_name 	= 'updated fade';
			endif;
		break;
	endswitch;
endif;
?>

<div class="wrap">

<?php 
// Show a message to user about the insertion proccess
if ( !empty($text_message) ) : 
?>

<div id="message" class="<?php echo $class_name; ?>">
	<p><?php echo $text_message; ?> <a href="<?php echo $base_page; ?>" title="Back to files management"><?php _e( 'File Manager', 'iwpftu' ); ?></a></p>
</div>
<?php endif; ?>

<?php
/*
 * If we have errors about the validation, shows them
 */
if ( !empty($errors) ) :
?>
<div id="message" class="error">
	<p><strong><?php _e('Note:', 'iwpftu' ); ?></strong></p>
	<ul>
<?php foreach($errors as $error) : ?>
		<li><?php echo $error; ?></li>
<?php endforeach; ?>
	</ul>
</div>
<?php endif; ?>

<?php

if (! empty($_GET['mode']))
    $mode = $_GET['mode'];
else
    $mode = '';
switch($mode) :
	case 'add_file' :
	?>
        <h2><img style="margin-right: 5px;" src="<?php echo plugins_url( 'imasters-wp-files-to-users/assets/images/imasters32.png' )?>" alt="imasters-ico"/><?php _e( 'Add new files', 'iwpftu' ); ?></h2>
        <form class="form-table" action="<?php echo htmlspecialchars($_SERVER['REQUEST_URI']); ?>" method="post" enctype="multipart/form-data" >
            <table class="form-table">
                <tbody>
                    <tr valign="top">
                        <th scope="row">
                            <label for="file_name"><?php _e( 'File name', 'iwpftu' ); ?></label>
                        </th>
                        <td>
                            <input type="text" id="file_name" name="file_name" class="regular-text" value="<?php echo isset($_POST['file_name']) ? $_POST['file_name'] : ''; ?>" />
                        </td>
                    </tr>
                    <tr valign="top">
                        <th scope="row">
                            <label for="file_user_id"><?php _e( 'User', 'iwpftu' ); ?></label>
                        </th>                        
                        <td>
                           	<?php
                                        $users_search = new WP_User_Search( '', '', 'administrator'  );
                                        $users_admin = $users_search->results;

                                        //print_r($users_admin);
                                        //exit;
							/* List Users*/
							wp_dropdown_users( array(
								"name" 		   => "file_user_id",
								"exclude" 	   => $users_admin
							) ); 
							?>
                        </td>
                    </tr>
                    <tr valign="top">
                        <th scope="row">
                            <label for="file_upload"><?php _e( 'Select file', 'iwpftu' ); ?></label>
                        </th>
                        <td>
                        	<input type="file" id="file_upload" name="file_upload"  />
                        </td>
                    </tr>
                </tbody>
            </table> 
            <p class="submit">
            <input type="hidden" id="act" name="act" value="add_file" />
                <input type="submit" id="button" name="button" value="<?php _e( 'Save', 'iwpftu' ); ?>" class="button-primary"/> <?php _e( 'or', 'iwpftu' ); ?> <a href="<?php echo $base_page; ?>"><?php _e( 'Cancel', 'iwpftu' ); ?></a>
            </p>
        </form>
   	<?php 
	break;
	
	case 'edit_file' :
		
		if( isset( $_GET['file_id']) ) :
			
			$file_id = (int)$_GET['file_id'];
			
			$objFile = $wpdb->get_row($wpdb->prepare( "
				SELECT file_id, file_name, file_name_file, file_user_id FROM $wpdb->imasters_wp_files_to_users
				WHERE 
				file_id = %d
				", 
				$file_id 
			) );
			
			if ( $objFile ) :
	?>
        <h2><img style="margin-right: 5px;" src="<?php echo plugins_url( 'imasters-wp-files-to-users/assets/images/imasters32.png' )?>" alt="imasters-ico"/><?php _e( 'Edit feature file', 'iwpftu' ); ?></h2>
        <form class="form-table" action="<?php echo htmlspecialchars($_SERVER['REQUEST_URI']); ?>" method="post" enctype="multipart/form-data" > 
            <table class="form-table">
                <tbody>
                    <tr valign="top">
                        <th scope="row">
                            <label for="file_name"><?php _e( 'File name', 'iwpftu' ); ?></label>
                        </th>
                        <td>
                            <input type="text" id="file_name" name="file_name" class="regular-text" value="<?php echo $objFile->file_name; ?>" />
                        </td>
                    </tr>
                    <tr valign="top">
                        <th scope="row">
                            <label for="file_user_id"><?php _e( 'To user', 'iwpftu' ); ?></label>
                        </th>
                        <td>
                            <?php 
							/* List Users*/
							wp_dropdown_users( array( 
								"name" 		=> "file_user_id",
								"selected" 	=> $objFile->file_user_id,
								"exclude" 	=> '1, 2'
							) ); 
							?>
                        </td>
                    </tr>
                    <tr valign="top">
                        <td colspan="2">
                            <label for="check_file_edit">
                                <input type="checkbox" id="check_file_edit" name="check_file_edit" />
                                <?php _e( 'Mark to', 'iwpftu' ); ?> <strong><?php _e( 'edit file', 'iwpftu' ); ?></strong>
                            </label>
                        </td>
                    </tr>
                    <tr valign="top" id="file">
                        <th scope="row">
                            <label for="file_upload"><?php _e( 'Select new file', 'iwpftu' ); ?></label>
                        </th>
                        <td>
                        	<input type="file" id="file_upload" name="file_upload"  />
                        </td>
                    </tr>
                </tbody>
            </table> 
            <p class="submit">
            	<input type="hidden" id="name_older" name="name_older" 	value="<?php echo $objFile->file_name_file; ?>" />
            	<input type="hidden" id="file_id" 	 name="file_id" 	value="<?php echo $objFile->file_id;?>" />
                <input type="hidden" id="act" 		 name="act" 		value="edit_file" />
                <input type="submit" id="button" 	 name="button" 		value="<?php _e( 'Save', 'iwpftu' ); ?>" class="button-primary"/> <?php _e( 'or', 'iwpftu' ); ?> <a href="<?php echo $base_page; ?>"><?php _e( 'Cancel', 'iwpftu' ); ?></a>
            </p>
        </form>
        <script type="text/javascript">
            jQuery( function($) {
                var File = $('#file');
               	File.hide();
                $('#check_file_edit').click(function() {
                    var is_checked = $('#check_file_edit:checked').length;
                    if ( is_checked ) {
                        File.fadeIn();
                    } else {
                        File.fadeOut();
                    }
                });
            });
        </script>
    <?php
    		endif;
		endif;
	break;
	
	default :
	?>       
        <h2><img style="margin-right: 5px;" src="<?php echo plugins_url( 'imasters-wp-files-to-users/assets/images/imasters32.png' )?>" alt="imasters-ico"/><?php _e( 'Manager File', 'iwpftu' ); ?></h2>
	<form action="<?php echo htmlspecialchars($_SERVER['REQUEST_URI']); ?>" method="post">
        <div class="tablenav">
            <p class="search-box">
                    <label for="filter_name_file"><?php _e( 'Search', 'iwpftu' ); ?></label>
                    <input type="text" class="search-input" id="filter_name_file" name="filter_name_file" />
                    <input type="submit" class="button-secondary action" id="act" name="act" value="Ok" />
            </p>
            <div class="alignleft">
                <input type="submit" id="delete-file" class="button-secondary delete" name="act" value="<?php _e( 'Delete selected', 'iwpftu' ); ?>"/>
                <a class="button-primary" href="<?php echo $base_page; ?>&amp;mode=add_file"><?php _e( 'Insert new file', 'iwpftu' ); ?></a>
            </div>

            <br class="clear"/>
        </div>
        <br class="clear"/>
        
        <table class="widefat fixed">
            <thead>
                <tr>
                    <th class="check-column" scope="col"><input type="checkbox" /></th>
                    <th scope="col"><?php _e( 'File name', 'iwpftu' ); ?></th>
                    <th scope="col"><?php _e( 'User', 'iwpftu' ); ?></th>
                    <th scope="col"><?php _e( 'Download', 'iwpftu' ); ?></th>
                    <th scope="col"><?php _e( 'Date', 'iwpftu' ); ?></th>
                    <th scope="col"><?php _e( 'Size', 'iwpftu' ); ?></th>
                    <th scope="col"><?php _e( 'Extension', 'iwpftu' ); ?></th>
                </tr>
            </thead>
            <tbody>
            <?php
			if ( isset( $_POST['filter_name_file'] ) and !empty( $_POST['filter_name_file'] ) ) :
				$name_of_file = $_POST['filter_name_file'];
				$objFiles = $wpdb->get_results( "
					SELECT file_id, file_name, file_name_file, file_user_id, file_bytes, file_registered_at
					FROM $wpdb->imasters_wp_files_to_users
					WHERE file_name 
					LIKE '%$name_of_file%'
				" );
			else : 
				
				$objFiles = $wpdb->get_results( "
					SELECT file_id, file_name, file_name_file, file_user_id, file_bytes, file_registered_at
					FROM $wpdb->imasters_wp_files_to_users
					ORDER BY file_id DESC
				" );
				
			endif;
			

			if( $objFiles ) :
				foreach( $objFiles as $objFile ) :
				
				/**
				 * Get user name
				 */
				$user_name = get_userdata($objFile->file_user_id);
				
				/**
				 * Define download path
				 */
				$download_path = WP_PLUGIN_URL . '/imasters-wp-files-to-users/imasters-wp-files-to-users-download.php';
				$file_name = $objFile->file_name_file;
			?>
            	<tr>
                    <th class="check-column" scope="row">
                        <input type="checkbox" value="<?php echo $objFile->file_id; ?>" name="delete[]"/>
                    </th>
                    <td><a href="<?php echo $base_page; ?>&amp;mode=edit_file&amp;file_id=<?php echo $objFile->file_id; ?>"><?php echo $objFile->file_name; ?></a></td>
                    <td><?php echo $user_name->display_name; ?></td>
                    <td><a href="<?php echo $download_path; ?>?file_name=<?php echo $file_name; ?>"><?php _e( 'Download', 'iwpftu' ); ?></a></td>
                    <td><?php echo date('d/m/Y', strtotime($objFile->file_registered_at)); ?></td>
                    <td><?php echo $objIMASTERS_WP_FTU->convert_bytes( $objFile->file_bytes ) . ' KB'; ?></td>
                    <td><?php echo $extension = end( explode( '.', $objFile->file_name_file ) ); ?></td>
                </tr>
            <?php 
				endforeach; 
			else :
			?>
                <tr>
                	<td colspan="7"><strong><?php _e( 'Any file inserted yet', 'iwpftu' ); ?></strong></td>
                </tr>
            <?php endif;?>
            </tbody>
        </table>
    </form>
   <?php 
   break;
   endswitch; ?>
</div> <!-- / wrap -->