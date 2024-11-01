<?php
    /**
    * Author: Agile Infoways
    * Author URI: http://www.agileinfoways.com
    */
?>
<?php
    //Global Query
    function wcam_menu_getquery($query){
        global $wpdb;  
        $wpdb->query( $wpdb->prepare( $query,""));
    }
    //Get Queries
    function wcam_menu_select_data($tablename,$where,$select = '*'){
        global $wpdb;  
        $q = "SELECT $select FROM $tablename WHERE $where";    
        $result = $wpdb->get_results($q);
        return $result;
    }
    //Update Queries
    function wcam_menu_update_data($tablename,$values,$where){
        global $wpdb;  
        $q = $wpdb->update($tablename, $values, $where);
        wcam_menu_getquery($q);
        return ;
    }
    //Insert Queries
    function wcam_menu_insert_data($tablename,$fields,$format){
        global $wpdb;
        $q = $wpdb->insert($tablename,$fields,$format);
        wcam_menu_getquery($q);
        return $lastid = $wpdb->insert_id;
    }
    //Delete Queries
    function wcam_menu_delete_data($tablename,$field, $value){
        global $wpdb;
        if(is_array($value)){
            for ($i=0; $i<=count($value); $i++){
                $q= $wpdb->delete($tablename, array('id'=>sanitize_text_field($value[$i])), array('%d'));
                wcam_menu_getquery($q);
            }
        }else{
            $tags = $value;
            $q= $wpdb->delete($tablename, array('id'=>sanitize_text_field($tags)), array('%d'));
            wcam_menu_getquery($q);
        }
        
    }
?>