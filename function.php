<?php
    /**
    * Author: Agile Infoways
    * Author URI: http://www.agileinfoways.com
    */
?>
<?php
    global $wpdb;
    global $table_admin_menu;
    $menuList = $wpdb->get_results($wpdb->prepare("SELECT * FROM $table_admin_menu WHERE status = %d and menu_slug = %s  order by id ASC",0,$_GET['page']));
    foreach($menuList as $key=>$value)
    {
    ?>
    <script>
        var counter = 0;
        setInterval(function(){
            if(counter==0){
                var results_html = "<?php echo '<div class=wrap><h2>'.$value->page_title.'</h2><br>'.$value->menu_content.'</div>'; ?>";
                document.getElementById('wpbody-content').innerHTML = results_html;
                counter = 1;
            }
        },1000);
    </script>
    <?php
    }
    //  This Function is used to Create Admin Menu
    function wcam_custom_admin_menu(){

        $my_plugins_page = add_menu_page('Wp Admin Menu', 'Wp Admin Menu', '1', 'admin-menu-listing', 'wcam_menu_listing');
        add_submenu_page('admin-menu-listing', 'Wp Admin Menu', 'Wp Admin Menu', '1', 'admin-menu-listing','wcam_menu_listing');
        global $wpdb;
        global $table_admin_menu;
        $menuList = $wpdb->get_results("SELECT * FROM $table_admin_menu WHERE status = '0' order by id ASC");
        $submenu  = $wpdb->get_results("SELECT * FROM $table_admin_menu WHERE status = '0' AND parent_slug !='' order by id ASC");
        $parentId = array();

        foreach ($menuList as $key => $menuvalue) {
            $menuvalue->parent_slug;
            //Show Parent Menu
            if($menuvalue->parent_slug == ""){
                $parentId[]      = $menuvalue->menu_slug;
                $my_plugins_page = add_menu_page(esc_attr($menuvalue->page_title),esc_attr($menuvalue->menu_title),esc_attr($menuvalue->capability),esc_attr($menuvalue->menu_slug),'wcam_my_callback',esc_attr($menuvalue->icon_url),esc_attr($menuvalue->position));
            }
        }
        $a=1;
        foreach ($submenu as $key => $value){
            // Dispaly Sub Menu of Parent
            global $submenu, $menu;
            $menu_array = array();
            foreach( $menu as $menuSlug ):
                $menu_array[] = $menuSlug[2];
                endforeach; 
            if(in_array($value->parent_slug,$menu_array))
                add_submenu_page(esc_attr($value->parent_slug), esc_attr($value->page_title), esc_attr($value->menu_title), esc_attr($value->capability), esc_attr($value->menu_slug),'wcam_my_callback');
        }

        add_submenu_page('admin-menu-listing', 'Help','Help','2','menu-help','wcam_menuHelp');
    }

    function wcam_my_callback(){
        return '';
    }
    //Include JSS and CSS Files for Front end and Back End
    function wcam_menu_adminscripts(){
        wp_enqueue_script('admin_enqueue_scripts', plugins_url('js/adminMenu_js.js', __FILE__ ), array('jquery'), null, true);
        wp_register_style('admin_css',    plugins_url('css/adminMenu_css.css', __FILE__ ));
        wp_enqueue_style('admin_css');
        wp_register_style('custom_admin_menu_backend_css',    plugins_url('css/backend.css', __FILE__ ));    
        wp_enqueue_style('custom_admin_menu_backend_css');
    }  
    //This Plugin is Used to Create Table when Plugin is Installed
    function wcam_admin_menu_install(){

        global $wpdb;
        global $table_admin_menu;

        $sql_exist = "DROP TABLE IF EXISTS $table_admin_menu";
        require_once(ABSPATH .'wp-admin/includes/upgrade.php');
        dbDelta($sql_exist);

        $sql = "CREATE TABLE  $table_admin_menu (
        id mediumint(9) NOT NULL AUTO_INCREMENT,
        parent_slug text,
        page_title VARCHAR(150),    
        menu_title VARCHAR(150),
        capability VARCHAR(10),
        menu_slug VARCHAR(150),
        status  BOOLEAN,
        icon_url text,
        position VARCHAR(10),
        admin_menu_created_date  date,
        menu_content text,
        UNIQUE KEY id (id)
        );";

        require_once(ABSPATH .'wp-admin/includes/upgrade.php');
        dbDelta($sql);

        $sql_exist = "DROP TABLE IF EXISTS $table_admin_menu";
        require_once(ABSPATH .'wp-admin/includes/upgrade.php');
        dbDelta($sql_exist);
    }

    // This Function is used to Un Install all the Tables when Plugin is Un Installed
    function wcam_admin_menu_uninstall(){
        global $wpdb;
        global $table_admin_menu;

        $sql = "DROP TABLE $table_admin_menu";     
        require_once(ABSPATH .'wp-admin/includes/upgrade.php');
        $wpdb->query($sql); 

    }
    // This Function is used to Show Menu Listing
    function wcam_menu_listing(){
        global $wpdb;
        global $table_admin_menu;
        // Insert record in database
        if($_POST['original_publish']=='Add'){ 
            $fields  = array(
                "id"           => "",
                "parent_slug"  => trim(sanitize_text_field($_POST['sub_menu'])), 
                "page_title"   => trim(sanitize_text_field($_POST['menu_page_title'])), 
                "menu_title"   => trim(sanitize_text_field($_POST['menu_title'])), 
                "capability"   => sanitize_text_field($_POST['capability']), 
                "menu_slug"    => sanitize_text_field($_POST['menu_slug']), 
                "icon_url"     => esc_url($_POST['icon_url']),
                "position"     => sanitize_text_field($_POST['position']),
                "admin_menu_created_date" => date("Y-m-d", strtotime("now")),
                "status"       => sanitize_text_field($_POST['status']),
                "menu_content" => mysql_real_escape_string(preg_replace( "/\r|\n/", "&#8203;",(sanitize_text_field($_POST['mycustomeditor']))))."'"
                );
            $format = array( '%d', '%s', '%s', '%s', '%d', '%s', '%s', '%s', '%s', '%d', '%s' 
                );
            wcam_menu_insert_data($table_admin_menu,$fields,$format);
            $message = '<span style="color:green;"><b>Inserted Successfully (Please reload to check changes).</b></span>';
            $_REQUEST['action'] = '';
        }
        //Update record in database
        if($_POST['original_publish']=='Update'){
            $fields  = array(
                "parent_slug"  => trim(sanitize_text_field($_POST['sub_menu'])),
                "page_title"   => trim(sanitize_text_field($_POST['menu_page_title'])),
                "menu_title"   => trim(sanitize_text_field($_POST['menu_title'])),
                "capability"   => sanitize_text_field($_POST['capability']),
                "menu_slug"    => sanitize_text_field($_POST['menu_slug']),
                "icon_url"     => esc_url($_POST['icon_url']),
                "position"     => sanitize_text_field($_POST['position']),
                "status"       => sanitize_text_field($_POST['status']),
                "menu_content" => mysql_real_escape_string(preg_replace( "/\r|\n/", "&#8203;",(sanitize_text_field($_POST['mycustomeditor']))))."'"
                );
            $where   = array(
                'id' => sanitize_text_field($_POST['id'])
                );
            wcam_menu_update_data($table_admin_menu,$fields,$where);  
            $message = '<span style="color:green;"><b>Updated Successfully (Please reload to check changes).</b></span>';
            $_REQUEST['action'] = '';
        }
        //Delete record from database
        if($_REQUEST['action']=='delete' || $_REQUEST['action2']=='delete'){
            if($_REQUEST['id']!=''){
                wcam_menu_delete_data($table_admin_menu,'id',sanitize_text_field($_REQUEST['id']));
                $message = '<span style="color:red;"><b>Deleted Successfully (Please reload to check changes).</b></span>';
                $_REQUEST['action'] = '';
            }if(count($_REQUEST['item'])>0){
                wcam_menu_delete_data($table_admin_menu,'id',$_REQUEST['item']);
                $message = '<span style="color:red;"><b>Deleted Successfully (Please reload to check changes).</b></span>';
                $_REQUEST['action'] = '';
            }
        }
        if($_REQUEST['action']=='add' || $_REQUEST['action']=='edit'){
            include('admin/edit_menu.php');
        }else{
            include('admin/menu_listing_details.php');
        }
    }
    // This Function is usec to Check Help File
    function wcam_menuHelp(){
        include "help.php";
    }
?>