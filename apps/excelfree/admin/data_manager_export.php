<?php
/**
 * Data manager
 * @author Kondin Dmitriy <kondin@etown.ru>
 */
class Data_Manager_Export extends Object_Manager {
    private $category_not_defined_title = 'Категория не указана';
    /**
     * Constructor
     */
    function __construct() {
        $this->SiteBill();
        $this->table_name = 'data';
        $this->action = 'data';
        $this->primary_key = 'id';
        
       
	    
        require_once(SITEBILL_DOCUMENT_ROOT.'/apps/system/lib/model/model.php');
	    $data_model = new Data_Model();
        $this->data_model = $data_model->get_kvartira_model();
        $tmp = $this->data_model[$this->table_name]['text'];
        unset($this->data_model[$this->table_name]['text']);
        unset($this->data_model[$this->table_name]['image']);
        unset($this->data_model[$this->table_name]['youtube']);
        //$this->data_model['data']['image']['type'] = 'hidden';
        
        
        $this->data_model['data']['topic_id']['name'] = 'topic_id';
        $this->data_model['data']['topic_id']['title'] = Multilanguage::_('L_TEXT_TOPIC');
        $this->data_model['data']['topic_id']['primary_key_name'] = 'id';
        $this->data_model['data']['topic_id']['primary_key_table'] = 'topic';
        $this->data_model['data']['topic_id']['value_string'] = '';
        $this->data_model['data']['topic_id']['value'] = 0;
        $this->data_model['data']['topic_id']['length'] = 40;
        //$this->data_model['data']['topic_id']['type'] = 'select_box_structure';
        $this->data_model['data']['topic_id']['type'] = 'structure_chain';
        $this->data_model['data']['topic_id']['query'] = 'select * from '.DB_PREFIX.'_topic';
        $this->data_model['data']['topic_id']['value_name'] = 'name';
        $this->data_model['data']['topic_id']['title_default'] = Multilanguage::_('L_CHOOSE_TOPIC');
        $this->data_model['data']['topic_id']['value_default'] = 0;
        $this->data_model['data']['topic_id']['required'] = 'on';
        $this->data_model['data']['topic_id']['unique'] = 'off';
        
        $this->data_model[$this->table_name]['text'] = $tmp;
        $this->data_model[$this->table_name]['id']['title'] = 'ID';
        $this->data_model[$this->table_name]['user_id']['title'] = Multilanguage::_('L_TEXT_USER');
        $this->data_model[$this->table_name]['active']['title'] = Multilanguage::_('L_PUBLISHED_SH');
        $this->data_model[$this->table_name]['hot']['title'] = Multilanguage::_('L_SPECIAL_SH');
        $this->data_model[$this->table_name]['view_count']['title'] = Multilanguage::_('L_VIEW_COUNT');
        
        
        
        
        
        
        
        
        
        
        
        $this->model = $data_model; 
    }
    
	
	function get_model ($adopt=false) {
    	if($adopt){
    		$m=$this->data_model;
    		if(isset($this->data_model[$this->table_name]['tlocation'])){
    			require_once SITEBILL_DOCUMENT_ROOT.'/apps/tlocation/admin/admin.php';
    			$m=tlocation_admin::adoptTLocationModel($this->data_model[$this->table_name]);
    			return $m;
    		}
    	}
    	return $this->data_model[$this->table_name];
    }
    
    function get_search_model () {
        $search_model = $this->data_model[$this->table_name];
        
        $search_model['active']['title'] = Multilanguage::_('PUBLISHED_ONLY','excelfree');
        $search_model['hot']['title'] = Multilanguage::_('HOT_ONLY','excelfree');
        $search_model['topic_id']['required'] = 'off';
        $search_model['user_id']['required'] = 'off';
        $search_model['topic_id']['type'] = 'select_box_structure';
        
        
        unset($search_model['new_street']);
        unset($search_model['ad_mobile_phone']);
        unset($search_model['ad_stacionary_phone']);
        unset($search_model['can_call_start']);
        unset($search_model['can_call_end']);
        
        
        
        unset($search_model['number']);
        unset($search_model['price']);
        unset($search_model['room_count']);
        unset($search_model['floor']);
        unset($search_model['floor_count']);
        unset($search_model['walls']);
        unset($search_model['planning']);
        unset($search_model['balcony']);
        unset($search_model['square_all']);
        unset($search_model['square_live']);
        unset($search_model['square_kitchen']);
        unset($search_model['bathroom']);
        unset($search_model['plate']);
        unset($search_model['is_telephone']);
        unset($search_model['furniture']);
        unset($search_model['fio']);
        unset($search_model['email']);
        unset($search_model['phone']);
        unset($search_model['text']);

        unset($search_model['spacer1']);
        unset($search_model['dom']);
        unset($search_model['flat_number']);
        unset($search_model['owner']);
        unset($search_model['source']);
        unset($search_model['adv_date']);
        unset($search_model['more1']);
        unset($search_model['more2']);
        unset($search_model['more3']);
        
        return $search_model;
    }
    
    
    
    
    
    
    
    
    
    
    
    function init_request_from_xls ( $assoc_array, $data ) {
    	
        $model_array = $this->get_model(true);
        
        $tlocation_data=array(
        		'country_id'=>$data['country_id'],
        		'district_id'=>$data['district_id'],
        		'region_id'=>$data['region_id'],
        		'city_id'=>$data['city_id'],
        		'street_id'=>$data['street_id']
        );
        
        $tld=$this->createTLocationData($tlocation_data);
        foreach($tld as $kk=>$vv){
        	$this->setRequestValue($kk, $vv);
        }
        
        foreach ( $assoc_array as $key => $value ) {
        	if(in_array($key, array('country_id', 'district_id', 'region_id', 'city_id', 'street_id'))){
        		continue;
        		//break;
        	}
            if ( $model_array[$key]['type'] == 'select_by_query' ) {
                $id = $this->get_value_id_by_name($model_array[$key]['primary_key_table'],$model_array[$key]['value_name'],$model_array[$key]['primary_key_name'],$data[$value]);
                if (empty($id) ) {
                    $id = $this->add_value($model_array[$key]['primary_key_table'],$model_array[$key]['value_name'],$model_array[$key]['primary_key_name'],$data[$value]);
                }
                $this->setRequestValue($key, $id);
            } elseif($model_array[$key]['type'] == 'structure_chain') {
            	$chain=$data[$value];
            	if(empty($chain)){
            		$chain=$this->category_not_defined_title;
            	}
            	$chain=mb_strtolower($chain, SITE_ENCODING);
            	$x=$this->getCatalogChains();
    			$catalogChain=$x['txt'];
    			$catalogChainRev=array_flip($catalogChain);
    			if(isset($catalogChainRev[$chain])){
            		$this->setRequestValue($key, $catalogChainRev[$chain]);
    			}else{
    				$this->setRequestValue($key, $this->createTopicPoints($chain));
    			}
            	
            } elseif( $model_array[$key]['type'] == 'select_box' ){
            	if(!empty($model_array[$key]['select_data'])){
            		foreach($model_array[$key]['select_data'] as $k=>$v){
            			if($v==$data[$value]){
            				$this->setRequestValue($key, $k);
            				break;
            			}
            		}
            	}
            } elseif ( $model_array[$key]['type'] == 'geodata' ) {
            	$geodata_name=$model_array[$key]['name'];
            	$geodata=array();
            	$geodata=explode(',',$data[$value]);
            	if(count($geodata)>1){
            		if(preg_match('/^(-?)([0-9]?)([0-9])((\.?)(\d*)?)$/',trim($geodata[0]))){
            			$lat=trim($geodata[0]);
            		}else{
            			$lat='';
            		}
            		if(preg_match('/^(-?)([0-9]?)([0-9]?)([0-9])((\.?)(\d*)?)$/',trim($geodata[1]))){
            			$lng=trim($geodata[1]);
            		}else{
            			$lng='';
            		}
            		if($lat!='' && $lng!=''){
            			$this->setRequestValue($key, array('lat'=>$lat,'lng'=>$lng));
            		}else{
            			$this->setRequestValue($key, array('lat'=>'','lng'=>''));
            		}
            	}else{
            		$this->setRequestValue($key, array('lat'=>'','lng'=>''));
            	}
            	
            	//continue;
            }elseif($model_array[$key]['type'] == 'tlocation') {
            	
            	/*if ( $model_array[$key]['type'] == 'tlocation' ) {
            		$model_array[$key]['value']['country_id'] = $this->getRequestValue('country_id');
            		$model_array[$key]['value']['region_id'] = $this->getRequestValue('region_id');
            		$model_array[$key]['value']['city_id'] = $this->getRequestValue('city_id');
            		$model_array[$key]['value']['district_id'] = $this->getRequestValue('district_id');
            		$model_array[$key]['value']['street_id'] = $this->getRequestValue('street_id');
            		continue;
            	}*/
            	
            	
            }elseif($model_array[$key]['type'] == 'checkbox'){
            	if($data[$value]==1){
            		$this->setRequestValue($key, 1);
            	}else{
            		unset($_POST[$key]);
            		unset($_GET[$key]);
            	}
            } else {
            	
                $this->setRequestValue($key, $data[$value]);
            }
        }
    }
    
    function get_value_id_by_name($table,$field,$primary_key,$value){
        if ( $table == 'topic' and empty($value) ) {
            $value = $this->category_not_defined_title;
        }
    	$query="SELECT ".$primary_key." FROM ".DB_PREFIX."_".$table." WHERE ".$field."='".$value."'";
    	$this->db->exec($query);
    	if($this->db->success){
    		$this->db->fetch_assoc();
    		if($this->db->row[$primary_key]!=0){
    			return $this->db->row[$primary_key];
    		}else{
    			return FALSE;
    		}
    	}else{
    		return FALSE;
    	}
    }
    
    
    function add_value ($table,$field,$primary_key,$value) {
        if ( $table == 'topic' and empty($value) ) {
            $value = $this->category_not_defined_title;
        }
        if ( empty($value) ) {
            return false;
        }
        $query = "insert into ".DB_PREFIX."_".$table." set ".$field."='".$value."'";
        $this->db->exec($query);
        if ( $this->db->success ) {
            return $this->db->last_insert_id();
        }
        return false;
    }
    
    function edit() {
        //$form_data[$this->table_name] = $this->data_model[$this->table_name];
    	
        $form_data[$this->table_name] = $this->get_model(true);
        
        $form_data[$this->table_name] = $this->model->init_model_data_from_request($form_data[$this->table_name]);
        
        $this->model->forse_auto_add_values($form_data[$this->table_name]);
        $this->edit_data($form_data[$this->table_name]);
        if ( $this->getError() ) {
        	$rs .= '<div style="color: red">Ошибка: '.$this->GetErrorMessage().'</div><br>';
        } else {
        	$rs .= 'Запись обновлена успешно. ID = '.$form_data[$this->table_name][$this->primary_key]['value'].'<br>';
        }
        return $rs;
    }
    
    /**
     * Get search form
     * @param array $form_data
     * @param string $do
     * @param int $language_id
     * @param string $button_title
     * @return string
     */
    function get_search_form ( $form_data=array(), $do = 'new', $language_id = 0, $button_title = '' ) {
    
    	if($button_title==''){
    		$button_title = Multilanguage::_('L_TEXT_SAVE');
    	}
    	require_once(SITEBILL_DOCUMENT_ROOT.'/apps/system/lib/model/model.php');
    	$data_model = new Data_Model();
    	 
    	require_once(SITEBILL_DOCUMENT_ROOT.'/apps/system/lib/system/form/form_generator.php');
    	$form_generator = new Form_Generator();
    
    	$rs .= $this->get_ajax_functions();
    
    	$rs .= '<form method="post" action="index.php" id="export_form" enctype="multipart/form-data">';
    	$rs .= '<table>';
    	if ( $this->getError() ) {
    		$rs .= $form_generator->get_error_message_row($this->GetErrorMessage());
    	}
    	$rs .= $form_generator->compile_form($form_data);
    
    	$rs .= '<input type="hidden" name="do" value="export">';
    	$rs .= '<input type="hidden" name="action" value="excelfree">';

    	$rs .= '<tr>';
    	$rs .= '<td>Колонки</td>';
    	$rs .= '<td>'.$this->get_export_columns_list().'</td>';
    	$rs .= '</tr>';

    	$rs .= '<tr>';
    	$rs .= '<td></td>';
    	$rs .= '<td><input type="submit" name="submit" id="formsubmit" onClick="return SitebillCore.formsubmit(this);" value="'.Multilanguage::_('LOAD_EXCEL_FILE','excelfree').'"></td>';
    	$rs .= '</tr>';
    	
    	$rs .= '</table>';
    	$rs .= '</form>';
    
    	return $rs;
    
    }
    
    function get_export_columns_list() {
        $model = $this->get_model();
        $parameters=array();
    	foreach ( $model as $key => $value ) {
    		$parameters[$key]=array('key'=>$key,'title'=>$value['title']);
        	//$rs .= '<li><input type="checkbox" name="template_fields['.$key.']" checked/>'.$value['title'].'</li>';
        }
        $rs = '<table>';
        $rs .= '<tbody class="applied">';
        foreach ( $parameters as $key => $value ) {
            $rs .= '<tr><td><input type="checkbox" name="template_fields['.$value['key'].']" checked/>'.$value['title'].'</td></tr>';
        }
        $rs .= '</tbody>';
        $rs .= '</table>';
        return $rs;
    }
    
    
    /**
     * Add data
     * @param array $form_data form data
     * @param int $language_id
     * @return boolean
     */
    function add_data ( $form_data, $language_id = 0 ) {
    	require_once(SITEBILL_DOCUMENT_ROOT.'/apps/system/lib/model/model.php');
    	$data_model = new Data_Model();
    	$query = $this->get_insert_query(DB_PREFIX.'_'.$this->table_name, $form_data, $language_id);
    	//echo $query.'<br>';
    	$this->nullError();
    	$this->db->exec($query);
    	if ( !$this->db->success ) {
    		$this->riseError($this->db->error);
    		return false;
    	}
    	$new_record_id = $this->db->last_insert_id();
    	return $new_record_id;
    }
    
    /**
     * Get insert query
     * @param string $table_name table name
     *
     * @param array $model_array
     * @param int $language_id
     * @return boolean
     */
    function get_insert_query ( $table_name, $model_array, $language_id = 0 ) {
    	$set = array();
    	$values = array();
    	unset($model_array['image']);
    
    	foreach ( $model_array as $key => $item_array ) {
    		if ( $item_array['type'] == 'separator' ) {
    			continue;
    		}
    
    		if ( $item_array['type'] == 'spacer_text' ) {
    			continue;
    		}
    
    		if ( $item_array['type'] == 'photo' ) {
    			continue;
    		}
    		if ( $item_array['dbtype'] == 'notable' ) {
    			continue;
    		}
    		
    		if ( $item_array['dbtype'] == 'notable' ) {
    			continue;
    		}
    		
    		if ( $item_array['type'] == 'geodata' ) {
    			$set[] = '`'.$key.'_lat`';
    			if($item_array['value']['lat']==''){
    				$values[] = "NULL";
    			}else{
    				$values[] = "'".$this->escape($item_array['value']['lat'])."'";
    			}
    		
    			$set[] = '`'.$key.'_lng`';
    		
    			if($item_array['value']['lng']==''){
    				$values[] = "NULL";
    			}else{
    				$values[] = "'".$this->escape($item_array['value']['lng'])."'";
    			}
    			continue;
    		}
    		
    		if ( $item_array['name'] == 'date_added' and empty($item_array['value']) ) {
    		    $item_array['value'] = date('Y-m-d H:i:s');
    		}
    		
    		
    		
    
    		$set[] = '`'.$key.'`';
    		$item_array['value']=preg_replace('/<script.*\/script>/','',$item_array['value']);
    		$values[] = "'".$this->model->escape($item_array['value'])."'";
    	}
    	//echo "primary_key = $primary_key<br>";
    	//echo '$this->getRequestValue($primary_key) = '.$this->getRequestValue($primary_key).'<br>';
    	if ( $language_id > 0 ) {
    		$set[] = '`language_id`';
    		$values[] = "'".$language_id."'";
    		$set[] = '`link_id`';
    		$values[] = "'".$this->getRequestValue($primary_key)."'";
    	}
    	$query = "insert into $table_name (".implode(' , ', $set).") values (".implode(' , ', $values).")";
    	//echo $query;
    	return $query;
    }
    
    
    /**
     * Edit data
     * @param array $form_data form data
     * @param int $language_id language id
     * @return boolean
     */
    function edit_data ( $form_data, $language_id = 0, $primary_key_value = false ) {
    	require_once(SITEBILL_DOCUMENT_ROOT.'/apps/system/lib/model/model.php');
    	$data_model = new Data_Model();
    	if ( $primary_key_value ) {
    		$query = $data_model->get_edit_query(DB_PREFIX.'_'.$this->table_name, $this->primary_key, $primary_key_value, $form_data, $language_id);
    	} else {
    		$query = $data_model->get_edit_query(DB_PREFIX.'_'.$this->table_name, $this->primary_key, $this->getRequestValue($this->primary_key), $form_data, $language_id);
    	}
    	//echo '<br><br>'.$query.'<br><br>';
    	$this->db->success = true;
    	$this->nullError();
    	$this->db->exec($query);
    	if ( !$this->db->success ) {
    	    $this->riseError($this->db->error);
    		return false;
    	}
    }
    
    function insert () {
        //$form_data[$this->table_name] = $this->data_model[$this->table_name];
        $form_data[$this->table_name] = $this->get_model(true);
        $form_data[$this->table_name] = $this->model->init_model_data_from_request($form_data[$this->table_name]);
        $new_record_id=$this->add_data($form_data[$this->table_name]);
        if ( $this->getError() ) {
            $rs .= '<div style="color: red">'.Multilanguage::_('L_ERROR').': '.$this->GetErrorMessage().'</div><br>';
        } else {
            $rs .= Multilanguage::_('L_MESSAGE_ADD_SUCCESS').'<br>';
        }
        return $rs;
    }
    
    function nullError () {
    	$this->error_message = false;
    	$this->error_state = false;
    }
    
    function is_record_exist ( $data, $assoc_array ) {
        $primary_key_value = $data[$assoc_array[$this->primary_key]];
    	$query = "select {$this->primary_key} from ".DB_PREFIX."_{$this->table_name} where {$this->primary_key}='$primary_key_value'";
    	$this->db->exec($query);
    	$this->db->fetch_assoc();
    	if ( !empty($this->db->row[$this->primary_key]) ) {
    		return $this->db->row[$this->primary_key];
    	}
    	return false;
    }
    
    /**
     * Get sitebill adv ext
     * @param array $params
     * @param boolean $random
     * @return array
     */
    function get_search_query ( $params ) {
    	//print_r($params);
    	$this->grid_total = 0;
    	$where_array = false;
    
    	if ( $params['order'] == 'city' ) {
    		$where_array[] = 're_city.city_id=re_data.city_id';
    		$add_from_table .= ' , re_city ';
    		$add_select_value .= ' , re_city.name as city ';
    	}
    
    	if ( $params['order'] == 'district' ) {
    		$where_array[] = 're_district.id=re_data.district_id';
    		$add_from_table .= ' , re_district ';
    		$add_select_value .= ' , re_district.name as district ';
    	}
    
    	if ( $params['order'] == 'metro' ) {
    		$where_array[] = 're_metro.metro_id=re_data.metro_id';
    		$add_from_table .= ' , re_metro ';
    		$add_select_value .= ' , re_metro.name as metro ';
    	}
    
    	if ( $params['order'] == 'street' ) {
    		$where_array[] = 're_street.street_id=re_data.street_id';
    		$add_from_table .= ' , re_street ';
    		$add_select_value .= ' , re_street.name as street ';
    	}
    
    	if(isset($params['favorites']) AND !empty($params['favorites'])){
    		$where_array[] = 're_data.id IN ('.implode(',',$params['favorites']).')';
    	}
    
    
    
    	$where_array[] = 're_topic.id=re_data.topic_id';
    
    	//echo '$params[\'topic_id\'] = '.$params['topic_id'].'<br>';
    
    	if ( $params['topic_id'] != '' ) {
    		require_once(SITEBILL_DOCUMENT_ROOT.'/apps/system/lib/admin/structure/structure_manager.php');
    		$Structure_Manager = new Structure_Manager();
    		$category_structure = $Structure_Manager->loadCategoryStructure();
    		global $smarty;
    		//echo $category_structure['catalog'][$params['topic_id']]['description'];
    		$smarty->assign('topic_description', $category_structure['catalog'][$params['topic_id']]['description']);
    
    		$childs = $Structure_Manager->get_all_childs($params['topic_id'], $category_structure);
    		if ( count($childs) > 0 ) {
    			array_push($childs, $params['topic_id']);
    			$where_array[] = DB_PREFIX.'_data.topic_id in ('.implode(' , ',$childs).') ';
    		} else {
    			$where_array[] = DB_PREFIX.'_data.topic_id='.$params['topic_id'];
    		}
    		//print_r($params);
    	}
    
    	if ( isset($params['country_id']) and $params['country_id'] != 0  ) {
    		$where_array[] = DB_PREFIX.'_data.country_id = '.$params['country_id'];
    	}
    
    	if ( isset($params['id']) and $params['id'] != 0  ) {
    		$where_array[] = DB_PREFIX.'_data.id = '.$params['id'];
    	}
    
    
    	if ( isset($params['user_id']) and $params['user_id'] > 0  ) {
    		$where_array[] = DB_PREFIX.'_data.user_id = '.$params['user_id'];
    	}
    
    	if ( isset($params['onlyspecial']) and $params['onlyspecial'] > 0  ) {
    		$where_array[] = DB_PREFIX.'_data.hot = 1';
    	}
    
    
    	if ( isset($params['price']) and $params['price'] != 0  ) {
    		$where_array[] = DB_PREFIX.'_data.price  <= '.$params['price'];
    	}
    
    	if ( isset($params['price_min']) and $params['price_min'] != 0  ) {
    		$where_array[] = 're_data.price  >= '.$params['price_min'];
    	}
    
    	if ( isset($params['house_number']) and $params['house_number'] != 0  ) {
    		$where_array[] = DB_PREFIX.'_data.number  = \''.$params['house_number'].'\'';
    	}
    
    
    	if ( isset($params['region_id']) and $params['region_id'] != 0 ) {
    		$where_array[] = DB_PREFIX.'_data.region_id = '.$params['region_id'];
    	}
    	if ( isset($params['spec']) ) {
    		$where_array[] = ' '.DB_PREFIX.'_data.hot = 1 ';
    	}
    	if ( isset($params['hot']) ) {
    		$where_array[] = ' '.DB_PREFIX.'_data.hot = 1 ';
    	}
    	if ( isset($params['active']) ) {
    		$where_array[] = ' '.DB_PREFIX.'_data.active = 1 ';
    	}
    	 
    	if ( isset($params['city_id']) and $params['city_id'] != 0  ) {
    		$where_array[] = DB_PREFIX.'_data.city_id = '.$params['city_id'];
    	}
    	if ( isset($params['district_id']) and $params['district_id'] != 0  ) {
    		$where_array[] = DB_PREFIX.'_data.district_id = '.$params['district_id'];
    	}
    	if ( isset($params['district_id']) and $params['district_id'] != 0  ) {
    		$where_array[] = DB_PREFIX.'_data.district_id = '.$params['district_id'];
    	}
    	if ( isset($params['metro_id']) and $params['metro_id'] != 0  ) {
    		$where_array[] = DB_PREFIX.'_data.metro_id = '.$params['metro_id'];
    	}
    	if ( isset($params['street_id']) and $params['street_id'] != 0  ) {
    		$where_array[] = DB_PREFIX.'_data.street_id = '.$params['street_id'];
    	}
    		
    	if(isset($params['srch_phone']) and $params['srch_phone'] !== NULL){
    		$phone = preg_replace('/[^\d]/', '', $params['srch_phone']);
    		$sub_where=array();
    		if($this->getConfigValue('allow_additional_mobile_number')){
    			$sub_where[] = '('.DB_PREFIX.'_data.ad_mobile_phone LIKE \'%'.$phone.'%\')';
    		}
    		if($this->getConfigValue('allow_additional_stationary_number')){
    			$sub_where[] = '('.DB_PREFIX.'_data.ad_stacionary_phone LIKE \'%'.$phone.'%\')';
    		}
    		$sub_where[] = '('.DB_PREFIX.'_data.phone LIKE \'%'.$phone.'%\')';
    		$where_array[]='('.implode(' OR ',$sub_where).')';
    	}
    
    	if(isset($params['srch_word']) and $params['srch_word'] !== NULL){
    		$sub_where=array();
    		$word=htmlspecialchars($params['srch_word']);
    		$sub_where[] = '('.DB_PREFIX.'_data.text LIKE \'%'.$word.'%\')';
    		$sub_where[] = '('.DB_PREFIX.'_data.more1 LIKE \'%'.$word.'%\')';
    		$sub_where[] = '('.DB_PREFIX.'_data.more2 LIKE \'%'.$word.'%\')';
    		$sub_where[] = '('.DB_PREFIX.'_data.more3 LIKE \'%'.$word.'%\')';
    		$where_array[]='('.implode(' OR ',$sub_where).')';
    	}
    
    	if($params['srch_date_from']!=0 && $params['srch_date_to']!=0){
    		$where_array[]="((".DB_PREFIX."_data.date_added>='".$params['srch_date_from']."') AND ('.DB_PREFIX.'_data.date_added<='".$params['srch_date_to']."'))";
    	}elseif($params['srch_date_from']!=0){
    		$where_array[]="(".DB_PREFIX."_data.date_added>='".$params['srch_date_from']."')";
    	}elseif($params['srch_date_to']!=0){
    		$where_array[]="(".DB_PREFIX."_data.date_added<='".$params['srch_date_to']."')";
    	}
    
    
    	/*
    	 if ($_SERVER['REQUEST_URI'] == '/')
    		$order = "re_data.id desc";
    	else
    		$order = "re_data.date_added desc";
    	*/
    	if ( $params['admin'] != 1 ) {
    		$where_array[] = DB_PREFIX.'_data.active=1';
    	} elseif ( $params['active'] == 1 ) {
    		$where_array[] = DB_PREFIX.'_data.active=1';
    	} elseif ( $params['active'] == 'notactive' ) {
    		$where_array[] = DB_PREFIX.'_data.active=0';
    	}
    
    	if ( $this->getConfigValue('apps.company.timelimit') ) {
    		$current_time = time();
    
    		$where_array[] = DB_PREFIX.'_data.user_id=u.user_id';
    		$where_array[] = 'u.company_id=c.company_id';
    		$where_array[] = "c.start_date <= $current_time";
    		$where_array[] = "c.end_date >= $current_time";
    		$add_from_table .= ' , '.DB_PREFIX.'_user u, '.DB_PREFIX.'_company c ';
    	}
    
    	if ( $params['only_img'] ) {
    		 
    		$where_array[] = DB_PREFIX.'_data.id=i.id';
    		$add_from_table .= ' , '.DB_PREFIX.'_data_image i ';
    	}
    
    
    	if ( $where_array ) {
    		$where_statement = " where ".implode(' and ', $where_array);
    	}
    
    	if ( isset($params['order']) ) {
    
    		if ( !isset($params['asc']) ) {
    			$asc = 'asc';
    		}
    		elseif ($params['asc'] == 'asc')  $asc = 'asc';
    		elseif ($params['asc'] == 'desc') $asc = 'desc';
    		//
    		if     ( $params['order'] == 'type' ) $order = 'type_sh ';
    		elseif ( $params['order'] == 'street' ) $order = 're_street.name ';
    		elseif ( $params['order'] == 'district' ) $order = 're_district.name ';
    		elseif ( $params['order'] == 'metro' ) $order = 're_metro.name ';
    		elseif ( $params['order'] == 'city' ) $order = 're_city.name ';
    		elseif ( $params['order'] == 'date_added' ) $order = 're_data.date_added ';
    		elseif ( $params['order'] == 'price' ){
    			if($this->getConfigValue('currency_enable')){
    				$order = 'price_ue ';
    			}else{
    				$order = 'price ';
    			}
    			 
    		}
    
    		$order .= $asc;
    	} else {
    		//$order = "re_data.id desc";
    		$order = "re_data.date_added DESC, re_data.id DESC";
    	}
    
    	foreach ( $params as $key => $value ) {
    		if ( $value != '') {
    			if($key!='topic_id'){
    				//echo "key = $key, value = $value<br>";
    				$pairs[] = "$key=$value";
    			}
    		}
    	}
    
    
		$query = "select re_data.*, re_topic.name as type_sh $add_select_value from re_data, re_topic $add_from_table $where_statement order by $order";
    
    	return $query;
    }
    
    
    /**
     * Grid
     * @param array $params
     * @return array
     */
    function grid_array ( $params, $fields=array() ) {
    	require_once(SITEBILL_DOCUMENT_ROOT.'/apps/system/lib/system/view/grid.php');
    	$common_grid = new Common_Grid($this);
    
    	if(empty($fields)){
	    	$common_grid->add_grid_item($this->primary_key);
	    	foreach ( $this->data_model[$this->table_name] as $key => $items ) {
	    		if($key=='tlocation'){
	    			$common_grid->add_grid_item('country_id');
	    			$common_grid->add_grid_item('region_id');
	    			$common_grid->add_grid_item('city_id');
	    			$common_grid->add_grid_item('district_id');
	    			$common_grid->add_grid_item('street_id');
	    		}else{
	    			$common_grid->add_grid_item($key);
	    		}
	    	}
    	}else{
    		foreach($fields as $field){
    			if($field=='tlocation'){
    				$common_grid->add_grid_item('country_id');
    				$common_grid->add_grid_item('region_id');
    				$common_grid->add_grid_item('city_id');
    				$common_grid->add_grid_item('district_id');
    				$common_grid->add_grid_item('street_id');
    			}else{
    				$common_grid->add_grid_item($field);
    			}
    		}
    	}
    
    	$common_grid->add_grid_control('edit');
    	$common_grid->add_grid_control('delete');
    
    	$common_grid->setPagerParams(array('action'=>$this->action,'page'=>1,'per_page'=>99999));
    	$query = $this->get_search_query($params);
    
    	$common_grid->set_grid_query($query);
    	return $common_grid->construct_grid_array();
    }
    
 	function load_by_id ( $record_id ) {
	    require_once(SITEBILL_DOCUMENT_ROOT.'/apps/system/lib/model/model.php');
	    if ( !is_object($this->data_model_object) ) {
	        $this->data_model_object = new Data_Model();
	    }
	    $form_data = $this->data_model;
	    $form_data[$this->table_name]=$this->get_model(true);
	    if ( $record_id > 0 ) {
	    	$form_data[$this->table_name] = $this->data_model_object->init_model_data_from_db ( $this->table_name, $this->primary_key, $record_id, $form_data[$this->table_name], TRUE );
	    }
     	
    	$x=$this->getCatalogChains();
    	$catalogChain=$x['txt'];
    	//print_r($categoryChain);
	    foreach($form_data[$this->table_name] as &$fd){
	    	if($fd['type']=='structure_chain'){
	    		$fd['value_string']=$catalogChain[$fd['value']];
	    		$fd['value']=$fd['value_string'];
	    	}
	    }
	    
        return $form_data[$this->table_name];
    }
    
    //helper functions for working with topic chains
    function getCatalogChains(){
		$ret=array();
		$query='SELECT id, parent_id, LOWER(name) AS name FROM '.DB_PREFIX.'_topic';
		$this->db->exec($query);
		while($this->db->fetch_assoc()){
			$categories[$this->db->row['id']]=$this->db->row['name'];
			$items[$this->db->row['id']]=$this->db->row['parent_id'];
			$points[]=$this->db->row['id'];
		}
		foreach($points as $p){
			$chain=$categories[$p];
			$chain_num=$p;
			$this->findParent($p,$items,$chain,$chain_num,$categories);
			$ret[$p]=$chain;
			$ret_num[$p]=$chain_num;
		}
		
		return $rs=array('txt'=>$ret,'num'=>$ret_num);
	}
	
	function findParent($child_id,&$items,&$chain,&$chain_num,$categories){
		if((int)$items[$child_id]!==0){
			//echo $child_id.' has parent '.$items[$child_id].'<br>';;
			$chain=$categories[$items[$child_id]].'/'.$chain;
			$chain_num=$items[$child_id].'/'.$chain_num;
			$this->findParent($items[$child_id],$items,$chain,$chain_num,$categories);
		}
	}
	
	private function createTopicPoints($chain){
    	
    	$x=$this->getCatalogChains();
    	$categoryChainTXT=$x['txt'];
    	$categoryChainNUM=$x['num'];
    	$chain_parts=array();
    	$chain_parts=explode('/',$chain);
    	//print_r($chain_parts);
    	if(!empty($chain_parts) AND $chain_parts[0]!=''){
			foreach($categoryChainTXT as $k=>$sc){
	    		$categoryChainArray[$k]=explode('/',$sc);
	    	}
	    	foreach($categoryChainArray as $ck=>$cca){
	    		$results[$this->compareChains($cca, $chain_parts)]=$ck;
	    	}
	    	$max_intersect=count($results)-1;
	    	if($max_intersect>0){
		    	$id=$results[$max_intersect];
		    	$branch_items=explode('/',$categoryChainNUM[$id]);
	    		return $this->addTopics(array_slice($chain_parts,$max_intersect), $branch_items[$max_intersect-1]);
	    	}else{
	    		return $this->addTopics($chain_parts, 0);
	    	}
    	}else{
    		return 0;
    	}
    }
    
	private function addTopics($items,$to){
    	$parent=$to;
    	foreach($items as $it){
    		$query='INSERT INTO '.DB_PREFIX.'_topic (name, parent_id) VALUES (\''.$this->mb_ucasefirst($it).'\','.$to.')';
    		$to=$this->db->exec($query);
    	}
    	return $to;
    }
    
	private function mb_ucasefirst($str){ 
	    $str[0] = mb_strtoupper($str[0],'windows-1251'); 
	    return (string)$str; 
	}
	
	private function compareChains($chain1, $chain2){
    	$assc=0;
    	foreach($chain1 as $k=>$c1){
    		if(isset($chain2[$k])){
    			if($chain1[$k]==$chain2[$k]){
    				$assc++;
    			}else{
    				return $assc;
    			}
    		}
    	}
    	return $assc;
    }
    
    function createTLocationData($data){
    	$strategy=$this->getConfigValue('apps.excel.geodata_strategy');
    	$country_id=0;
    	$district_id=0;
    	$region_id=0;
    	$street_id=0;
    	$city_id=0;
    	 
    	if($strategy=='tlocation'){
    
    		$DBC=DBC::getInstance();
    		if(isset($data['country_id']) && $data['country_id']!=''){
    			$query='SELECT country_id FROM '.DB_PREFIX.'_country WHERE name=? LIMIT 1';
    			$stmt=$DBC->query($query, array($data['country_id']));
    			if(!$stmt){
    				$query='INSERT INTO '.DB_PREFIX.'_country (name) VALUES (?)';
    				$stmt=$DBC->query($query, array($data['country_id']));
    				if(!$stmt){
    					$country_id=0;
    				}else{
    					$country_id=$DBC->lastInsertId();
    				}
    
    			}else{
    				$ar=$DBC->fetch($stmt);
    				$country_id=$ar['country_id'];
    			}
    		}
    
    		if(isset($data['region_id']) && $data['region_id']!=''){
    			if($country_id!=0){
    				$query='SELECT region_id FROM '.DB_PREFIX.'_region WHERE name=? AND country_id=? LIMIT 1';
    				$stmt=$DBC->query($query, array($data['region_id'], $country_id));
    			}else{
    				$query='SELECT region_id FROM '.DB_PREFIX.'_region WHERE name=? LIMIT 1';
    				$stmt=$DBC->query($query, array($data['region_id']));
    			}
    
    
    			if(!$stmt){
    				if($country_id!=0){
    					$query='INSERT INTO '.DB_PREFIX.'_region (name, country_id) VALUES (?, ?)';
    					$stmt=$DBC->query($query, array($data['region_id'], $country_id));
    				}else{
    					$query='INSERT INTO '.DB_PREFIX.'_region (name) VALUES (?)';
    					$stmt=$DBC->query($query, array($data['region_id']));
    				}
    
    				if(!$stmt){
    					$region_id=0;
    				}else{
    					$region_id=$DBC->lastInsertId();
    				}
    			}else{
    				$ar=$DBC->fetch($stmt);
    				$region_id=$ar['region_id'];
    			}
    		}
    
    		if(isset($data['city_id']) && $data['city_id']!=''){
    			if($region_id!=0){
    				$query='SELECT city_id FROM '.DB_PREFIX.'_city WHERE name=? AND region_id=? LIMIT 1';
    				$stmt=$DBC->query($query, array($data['city_id'], $region_id));
    			}else{
    				$query='SELECT city_id FROM '.DB_PREFIX.'_city WHERE name=? LIMIT 1';
    				$stmt=$DBC->query($query, array($data['city_id']));
    			}
    
    
    			if(!$stmt){
    				if($region_id!=0){
    					$query='INSERT INTO '.DB_PREFIX.'_city (name, region_id) VALUES (?, ?)';
    					$stmt=$DBC->query($query, array($data['city_id'], $region_id));
    				}else{
    					$query='INSERT INTO '.DB_PREFIX.'_city (name) VALUES (?)';
    					$stmt=$DBC->query($query, array($data['city_id']));
    				}
    
    				if(!$stmt){
    					$city_id=0;
    				}else{
    					$city_id=$DBC->lastInsertId();
    				}
    			}else{
    				$ar=$DBC->fetch($stmt);
    				$city_id=$ar['city_id'];
    			}
    		}
    
    		if(isset($data['district_id']) && $data['district_id']!=''){
    			if($city_id!=0){
    				$query='SELECT id FROM '.DB_PREFIX.'_district WHERE name=? AND city_id=? LIMIT 1';
    				$stmt=$DBC->query($query, array($data['district_id'], $city_id));
    			}else{
    				$query='SELECT id FROM '.DB_PREFIX.'_district WHERE name=? LIMIT 1';
    				$stmt=$DBC->query($query, array($data['district_id']));
    			}
    
    
    			if(!$stmt){
    				if($city_id!=0){
    					$query='INSERT INTO '.DB_PREFIX.'_district (name, city_id) VALUES (?, ?)';
    					$stmt=$DBC->query($query, array($data['district_id'], $city_id));
    				}else{
    					$query='INSERT INTO '.DB_PREFIX.'_district (name) VALUES (?)';
    					$stmt=$DBC->query($query, array($data['district_id']));
    				}
    
    				if(!$stmt){
    					$district_id=0;
    				}else{
    					$district_id=$DBC->lastInsertId();
    				}
    			}else{
    				$ar=$DBC->fetch($stmt);
    				$district_id=$ar['id'];
    			}
    		}
    
    		if(isset($data['street_id']) && $data['street_id']!=''){
    			if($this->getConfigValue('link_street_to_city')==1){
    				if($city_id!=0){
    					$query='SELECT street_id FROM '.DB_PREFIX.'_street WHERE name=? AND city_id=? LIMIT 1';
    					$stmt=$DBC->query($query, array($data['street_id'], $city_id));
    				}else{
    					$query='SELECT street_id FROM '.DB_PREFIX.'_street WHERE name=? LIMIT 1';
    					$stmt=$DBC->query($query, array($data['street_id']));
    				}
    
    
    				if(!$stmt){
    					if($city_id!=0){
    						$query='INSERT INTO '.DB_PREFIX.'_street (name, city_id) VALUES (?, ?)';
    						$stmt=$DBC->query($query, array($data['street_id'], $city_id));
    					}else{
    						$query='INSERT INTO '.DB_PREFIX.'_street (name) VALUES (?)';
    						$stmt=$DBC->query($query, array($data['street_id']));
    					}
    
    					if(!$stmt){
    						$street_id=0;
    					}else{
    						$street_id=$DBC->lastInsertId();
    					}
    				}else{
    					$ar=$DBC->fetch($stmt);
    					$street_id=$ar['street_id'];
    				}
    			}else{
    				if($district_id!=0){
    					$query='SELECT street_id FROM '.DB_PREFIX.'_street WHERE name=? AND district_id=? LIMIT 1';
    					$stmt=$DBC->query($query, array($data['street_id'], $district_id));
    				}else{
    					$query='SELECT street_id FROM '.DB_PREFIX.'_street WHERE name=? LIMIT 1';
    
    					$stmt=$DBC->query($query, array($data['street_id']));
    				}
    
    
    				if(!$stmt){
    					if($city_id!=0){
    						$query='INSERT INTO '.DB_PREFIX.'_street (name, district_id) VALUES (?, ?)';
    						$stmt=$DBC->query($query, array($data['street_id'], $district_id));
    					}else{
    						$query='INSERT INTO '.DB_PREFIX.'_street (name) VALUES (?)';
    						$stmt=$DBC->query($query, array($data['street_id']));
    					}
    
    					if(!$stmt){
    						$street_id=0;
    					}else{
    						$street_id=$DBC->lastInsertId();
    					}
    				}else{
    					$ar=$DBC->fetch($stmt);
    					$street_id=$ar['street_id'];
    				}
    			}
    
    		}
    	}else{
    		$DBC=DBC::getInstance();
    		if(isset($data['country_id']) && $data['country_id']!=''){
    			$query='SELECT country_id FROM '.DB_PREFIX.'_country WHERE name=? LIMIT 1';
    			$stmt=$DBC->query($query, array($data['country_id']));
    			if(!$stmt){
    				$query='INSERT INTO '.DB_PREFIX.'_country (name) VALUES (?)';
    				$stmt=$DBC->query($query, array($data['country_id']));
    				if(!$stmt){
    					$country_id=0;
    				}else{
    					$country_id=$DBC->lastInsertId();
    				}
    
    			}else{
    				$ar=$DBC->fetch($stmt);
    				$country_id=$ar['country_id'];
    			}
    		}
    
    		if(isset($data['region_id']) && $data['region_id']!=''){
    			 
    			$query='SELECT region_id FROM '.DB_PREFIX.'_region WHERE name=? LIMIT 1';
    			$stmt=$DBC->query($query, array($data['region_id']));
    
    			if(!$stmt){
    				$query='INSERT INTO '.DB_PREFIX.'_region (name) VALUES (?)';
    				$stmt=$DBC->query($query, array($data['region_id']));
    				if(!$stmt){
    					$region_id=0;
    				}else{
    					$region_id=$DBC->lastInsertId();
    				}
    			}else{
    				$ar=$DBC->fetch($stmt);
    				$region_id=$ar['region_id'];
    			}
    		}
    
    		if(isset($data['city_id']) && $data['city_id']!=''){
    			 
    			$query='SELECT city_id FROM '.DB_PREFIX.'_city WHERE name=? LIMIT 1';
    			$stmt=$DBC->query($query, array($data['city_id']));
    
    			if(!$stmt){
    				$query='INSERT INTO '.DB_PREFIX.'_city (name) VALUES (?)';
    				$stmt=$DBC->query($query, array($data['city_id']));
    
    				if(!$stmt){
    					$city_id=0;
    				}else{
    					$city_id=$DBC->lastInsertId();
    				}
    			}else{
    				$ar=$DBC->fetch($stmt);
    				$city_id=$ar['city_id'];
    			}
    		}
    
    		if(isset($data['district_id']) && $data['district_id']!=''){
    			 
    			$query='SELECT id FROM '.DB_PREFIX.'_district WHERE name=? LIMIT 1';
    			$stmt=$DBC->query($query, array($data['district_id']));
    
    
    			if(!$stmt){
    				$query='INSERT INTO '.DB_PREFIX.'_district (name) VALUES (?)';
    				$stmt=$DBC->query($query, array($data['district_id']));
    
    				if(!$stmt){
    					$district_id=0;
    				}else{
    					$district_id=$DBC->lastInsertId();
    				}
    			}else{
    				$ar=$DBC->fetch($stmt);
    				$district_id=$ar['id'];
    			}
    		}
    
    		if(isset($data['street_id']) && $data['street_id']!=''){
    
    			$query='SELECT street_id FROM '.DB_PREFIX.'_street WHERE name=? LIMIT 1';
    			$stmt=$DBC->query($query, array($data['street_id']));
    
    
    			if(!$stmt){
    				$query='INSERT INTO '.DB_PREFIX.'_street (name) VALUES (?)';
    				$stmt=$DBC->query($query, array($data['street_id']));
    
    				if(!$stmt){
    					$street_id=0;
    				}else{
    					$street_id=$DBC->lastInsertId();
    				}
    			}else{
    				$ar=$DBC->fetch($stmt);
    				$street_id=$ar['street_id'];
    			}
    		}
    	}
    	 
    	 
    
    	return array(
    			'country_id'=>$country_id,
    			'district_id'=>$district_id,
    			'region_id'=>$region_id,
    			'city_id'=>$city_id,
    			'street_id'=>$street_id
    	);
    
    }
    
}
?>