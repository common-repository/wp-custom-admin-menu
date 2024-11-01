<?php
    /**
    * Plugin Name:  Wp Custom Admin Menu
    * Plugin URI: http://www.agileinfoways.com
    * Description: This Plugin is used to create Wordpress Admin Menu and Submenu with Default Content.
    * Version: 1.0.0
    * Author: Agile Infoways
    * Author URI: https://profiles.wordpress.org/agileinfowayspvtltd/
    * License: GPL2
    */
?>
<?php
    global $wpdb;
    global $table_admin_menu; //table name variable
    $table_admin_menu    = $wpdb->prefix . "admin_menu"; //table name
    include('common.php');
    include('function.php');
    //This are Hooks which are called when plugin is loaded
    add_action('admin_menu', 'wcam_custom_admin_menu');   
    add_action('admin_enqueue_scripts', 'wcam_menu_adminscripts');
    register_activation_hook( __FILE__, 'wcam_admin_menu_install' );
    register_deactivation_hook( __FILE__, 'wcam_admin_menu_uninstall' );
?>
