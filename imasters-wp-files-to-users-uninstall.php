<!-- Uninstall iMasters WP Files to Users -->
<?php
    if( !current_user_can('install_plugins')):
        die('Access Denied');
    endif;
$base_name = plugin_basename('imasters-wp-files-to-users/imasters-wp-files-to-users.php');
$base_page = 'admin.php?page='.$base_name;
if (!empty($_GET['mode']))
    $mode = trim($_GET['mode']);
else
    $mode = '';
$iwpftu_tables = array($wpdb->imasters_wp_files_to_users);

//Form Process
if( isset( $_POST['do'], $_POST['uninstall_iwpftu_yes'] ) ) :
    echo '<div class="wrap">';
    ?>
    <h2><img style="margin-right: 5px;" src="<?php echo plugins_url( 'imasters-wp-files-to-users/assets/images/imasters32.png' )?>" alt="imasters-ico"/><?php _e('Uninstall iMasters WP Files to Users', 'iwpftu') ?></h2>
    <?php
    switch($_POST['do']) {
        //  Uninstall iMasters WP Files to Users
        case __('Uninstall iMasters WP Files to Users', 'iwpftu') :
        if(trim($_POST['uninstall_iwpftu_yes']) == 'yes') :
        echo '<h3>'.__( 'Tables', 'iwpftu').'</h3>';
        echo '<ol>';
        foreach($iwpftu_tables as $table) :
            $wpdb->query("DROP TABLE {$table}");
            printf(__('<li>Table \'%s\' has been deleted.</li>', 'iwpftu'), "<strong><em>{$table}</em></strong>");
        endforeach;
        echo '</ol>';
        echo '<br/>';
        $mode = 'end-UNINSTALL';
        endif;

        //Delete Folder with files
        $file = WP_PLUGIN_DIR . '/imasters-wp-files-to-users/files/';

//        if(is_dir($file)) :
//            deleteDirectory($file);
//        else :
//            mkdir($file);
//        endif;
//
        if( is_dir($file) ? $objIMASTERS_WP_FTU->deleteDirectory($file) : mkdir($file) );
        if(!is_dir($file)): mkdir($file); endif;

        break;
    }
endif;
    switch($mode) {
    //  Deactivating Uninstall iMasters WP Files to Users
    case 'end-UNINSTALL':
        $deactivate_url = 'plugins.php?action=deactivate&amp;plugin=imasters-wp-files-to-users/imasters-wp-files-to-users.php';
        if(function_exists('wp_nonce_url')) {
            $deactivate_url = wp_nonce_url($deactivate_url, 'deactivate-plugin_imasters-wp-files-to-users/imasters-wp-files-to-users.php');
        }
    echo sprintf(__('<a href="%s" class="button-primary">Deactivate iMasters WP Files to Users</a> Disable that plugin to conclude the uninstalling.', 'iwpftu'), $deactivate_url);
    echo '</div>';
    break;
    default:
    ?>
    <!-- Uninstall iMasters WP Files to Users -->
    <form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>?page=<?php echo plugin_basename(__FILE__); ?>">
        <div class="wrap">
            <h2><img style="margin-right: 5px;" src="<?php echo plugins_url( 'imasters-wp-files-to-users/assets/images/imasters32.png' )?>" alt="imasters-ico"/><?php _e('Uninstall iMasters WP Files to Users', 'iwpftu'); ?></h2>
            <p><?php _e('Uninstaling this plugin the options and table used by iMasters WP Files to Users will be removed.', 'iwpftu'); ?></p>
            <div class="error">
            <p><?php _e('Warning:', 'iwpftu'); ?>
            <?php _e('This process is irreversible. We suggest that you do a database backup first.', 'iwpftu'); ?></p>
            </div>
            <table>
                <tr>
                    <td>
                    <?php _e('The following WordPress Tables will be deleted:', 'iwpftu'); ?>
                    </td>
                </tr>
            </table>
            <table class="widefat">
                <thead>
                    <tr>
                        <th><strong><?php _e('WordPress Tables', 'iwpftu'); ?></th>
                    </tr>
                </thead>
                <tr>
                    <td valign="top" class="alternate">
                        <ol>
                            <?php
                            foreach( $iwpftu_tables as $table_name )
                                printf( "<li>%s</li>\n", $table_name );
                            ?>
                        </ol>
                    </td>
                </tr>
            </table>
            <p>
                <input type="checkbox" name="uninstall_iwpftu_yes" id="uninstall_iwpftu_yes" value="yes" />
                <label for="uninstall_iwpftu_yes"><?php _e('Yes. Uninstall iMasters WP Files to Users now', 'iwpftu'); ?></label>
            </p>
            <p>
                <input type="submit" name="do" value="<?php _e('Uninstall iMasters WP Files to Users', 'iwpftu'); ?>" class="button-primary" />
            </p>
        </div>
    </form>
<?php
}
?>