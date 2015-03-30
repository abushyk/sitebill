<?php
/**
 * Metro manager
 * @author Kondin Dmitriy <kondin@etown.ru>
 */
class Metro_Manager extends Object_Manager {
    /**
     * Constructor
     */
    function Metro_Manager() {
        $this->SiteBill();
        $this->table_name = 'metro';
        $this->action = 'metro';
        $this->app_title = Multilanguage::_('METRO_APP_NAME','system');
        $this->primary_key = 'metro_id';
	    
        require_once(SITEBILL_DOCUMENT_ROOT.'/apps/system/lib/model/model.php');
	    $data_model = new Data_Model();
        $this->data_model = $this->get_metro_model();
    }
    
    function get_metro_model () {
    	$form_data = array();
    	$table_name='metro';
    	if(file_exists(SITEBILL_DOCUMENT_ROOT.'/apps/table/admin/admin.php') && file_exists(SITEBILL_DOCUMENT_ROOT.'/apps/columns/admin/admin.php') && file_exists(SITEBILL_DOCUMENT_ROOT.'/apps/table/admin/helper.php') ){
    		require_once SITEBILL_DOCUMENT_ROOT.'/apps/table/admin/helper.php';
    		$ATH=new Admin_Table_Helper();
    		$form_data=$ATH->load_model($table_name);
    		if(empty($form_data)){
    			$form_data = array();
    			$form_data = $this->_get_metro_model();
    			
    			require_once SITEBILL_DOCUMENT_ROOT.'/apps/system/lib/admin/object_manager.php';
    			require_once SITEBILL_DOCUMENT_ROOT.'/apps/table/admin/admin.php';
    			$TA=new table_admin();
    			$TA->create_table_and_columns($form_data, $table_name);
    			$form_data = array();
    			$form_data=$ATH->load_model($table_name);
    			
    		}
    		$form_data=$ATH->add_ajax($form_data);
    	}else{
    		$form_data = $this->_get_metro_model($ajax);
    	}
    	return $form_data;
    }
    
    /**
     * Get metro model
     * @param
     * @return
     */
    function _get_metro_model () {
    	$form_metro = array();
    
    	$form_metro['metro']['metro_id']['name'] = 'metro_id';
    	$form_metro['metro']['metro_id']['title'] = Multilanguage::_('L_TEXT_IDENTIFIER');
    	$form_metro['metro']['metro_id']['value'] = 0;
    	$form_metro['metro']['metro_id']['length'] = 40;
    	$form_metro['metro']['metro_id']['type'] = 'primary_key';
    	$form_metro['metro']['metro_id']['required'] = 'off';
    	$form_metro['metro']['metro_id']['unique'] = 'off';
    
    	$form_metro['metro']['city_id']['name'] = 'city_id';
    	$form_metro['metro']['city_id']['primary_key_name'] = 'city_id';
    	$form_metro['metro']['city_id']['title'] = Multilanguage::_('L_CITY');
    	$form_metro['metro']['city_id']['value'] = 0;
    	$form_metro['metro']['city_id']['length'] = 40;
    	$form_metro['metro']['city_id']['type'] = 'select_by_query';
    	$form_metro['metro']['city_id']['query'] = 'select * from '.DB_PREFIX.'_city order by name';
    	$form_metro['metro']['city_id']['value_name'] = 'name';
    	$form_metro['metro']['city_id']['title_default'] = Multilanguage::_('L_CHOOSE_CITY');
    	$form_metro['metro']['city_id']['value_default'] = 0;
    	$form_metro['metro']['city_id']['required'] = 'off';
    	$form_metro['metro']['city_id']['unique'] = 'off';
    
    	$form_metro['metro']['name']['name'] = 'name';
    	$form_metro['metro']['name']['title'] = Multilanguage::_('L_TEXT_TITLE');
    	$form_metro['metro']['name']['value'] = '';
    	$form_metro['metro']['name']['length'] = 40;
    	$form_metro['metro']['name']['type'] = 'safe_string';
    	$form_metro['metro']['name']['required'] = 'on';
    	$form_metro['metro']['name']['unique'] = 'off';
    
    	return $form_metro;
    }
}
?>