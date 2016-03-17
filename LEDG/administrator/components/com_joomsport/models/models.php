<?php
defined( '_JEXEC' ) or die( 'Restricted access' );
jimport('joomla.application.component.model');

class JSPRO_Models{
	
	public $db = null;
	public $uri = null;
	public $mainframe = null;
	
	protected $js_table = null;
	
	function __construct()
	{
		$this->db		=& JFactory::getDBO();
		$this->uri		=& JFactory::getURI();
		$this->mainframe = JFactory::getApplication();
	}
	function getJS_Config($val){
		$query = "SELECT cfg_value FROM #__bl_config WHERE cfg_name='".$val."'";
		$this->db->setQuery($query);
		return $this->db->loadResult();
	}
	
	function js_publish($table,$cid){
		if(count($cid)){
			$cids = implode(',',$cid);
			$query = "UPDATE `".$table."` SET published = '1' WHERE id IN (".$cids.")";
			$this->db->setQuery($query);
			$this->db->query();
		}
		
	}
	
	function js_unpublish($table,$cid){
		if(count($cid)){
			$cids = implode(',',$cid);
			$query = "UPDATE `".$table."` SET published = '0' WHERE id IN (".$cids.")";
			$this->db->setQuery($query);
			$this->db->query();
		}
	}
	
	function js_delete($table,$cid){
		if(count($cid)){
			$cids = implode(',',$cid);
			$query = "DELETE FROM `".$table."` WHERE id IN (".$cids.")";
			$this->db->setQuery($query);
			$this->db->query();
		}
	}
	
	function get_db_Table(){
		$this->js_table = '';
	}
	
	public function set($property, $value = null)
	{
		$previous = isset($this->$property) ? $this->$property : null;
		$this->$property = $value;
		return $previous;
	}
	public function get($property, $default=null)
	{
		if (isset($this->$property)) {
			return $this->$property;
		}
		return $default;
	}
	
	function uploadFile( $filename, $userfile_name, $dir = '') 
	{
		$msg = '';
		if(!$dir){
			$baseDir =  JPATH_ROOT . '/media/bearleague/' ;
		}else{
			$baseDir = $dir;
		}
		jimport('joomla.filesystem.path');
		if (file_exists( $baseDir )) {
			if (is_writable( $baseDir )) {
				if (move_uploaded_file( $filename, $baseDir . $userfile_name )) {
				
					if (JPath::setPermissions( $baseDir . $userfile_name )) {
						return true;
					} else {
						$msg = 'Failed to change the permissions of the uploaded file.';
					}
				} else {
					$msg = 'Failed to move uploaded file to <code>/media</code> directory.';
				}
			} else {
				$msg = 'Upload failed as <code>/media</code> directory is not writable.';
			}
		} else {
			$msg = 'Upload failed as <code>/media</code> directory does not exist.';
		}
		if($msg != ''){
			JError::raiseError(500, $msg );
		}
		return false;
	}
	function getAdditfields($type, $id, $sid=0){
		$query = "SELECT ef.*,ev.fvalue as fvalue,ev.fvalue_text FROM #__bl_extra_filds as ef LEFT JOIN #__bl_extra_values as ev ON ef.id=ev.f_id AND ev.uid=".($id?$id:-1)." WHERE ef.published=1 AND ef.type='".$type."' ORDER BY ef.ordering";
		if($type == 1){
			$query = "SELECT DISTINCT(ef.id),ef.*,ev.fvalue as fvalue,ev.fvalue_text FROM #__bl_extra_filds as ef LEFT JOIN #__bl_extra_values as ev ON ef.id=ev.f_id AND ev.uid=".($id?$id:-1)." AND ev.season_id={$sid} WHERE ef.published=1 AND ef.type='".$type."' ORDER BY ef.ordering";
			$this->db->setQuery($query);
			$ext_fields_teams = $this->db->loadObjectList();
			if(!count($ext_fields_teams)){
				$query = "SELECT DISTINCT(ef.id),ef.*,ev.fvalue as fvalue,ev.fvalue_text FROM #__bl_extra_filds as ef LEFT JOIN #__bl_extra_values as ev ON ef.id=ev.f_id AND ev.uid=".($id?$id:-1)." WHERE ef.published=1 AND ef.type='".$type."' AND ev.season_id=0 ORDER BY ef.ordering";
			}
		}
		$this->db->setQuery($query);
		$ext_fields = $this->db->loadObjectList();
		$mj=0;
		if(isset($ext_fields)){
			foreach ($ext_fields as $extr){
				if($extr->field_type == '3'){
					$tmp_arr = array();
					$query = "SELECT * FROM #__bl_extra_select WHERE fid=".$extr->id;
					$this->db->setQuery($query);
					$selvals = $this->db->loadObjectList();
					if(count($selvals)){
						$tmp_arr[] = JHTML::_('select.option',  0, JText::_('BLBE_SELECTVALUE'), 'id', 'sel_value' ); 
						$selvals = array_merge($tmp_arr,$selvals);
						$ext_fields[$mj]->selvals = JHTML::_('select.genericlist',   $selvals, 'extraf['.$extr->id.']', 'class="inputbox" size="1"', 'id', 'sel_value', $extr->fvalue );
					}
				}
				if($extr->field_type == '1'){
					$ext_fields[$mj]->selvals	= JHTML::_('select.booleanlist',  'extraf['.$extr->id.']', 'class="inputbox"', $extr->fvalue );
				}
				$mj++;
			}
		}
		return $ext_fields;
	}
	
	function getSeasAttr($s_id){
		$query = "SELECT s.s_id as id, CONCAT(t.name,' ',s.s_name) as name,t.t_type,t.t_single,s.s_enbl_extra FROM #__bl_tournament as t, #__bl_seasons as s WHERE s.s_id = ".($s_id)." AND s.t_id = t.id ORDER BY t.name, s.s_name";
		$this->db->setQuery($query);
		$tourn = $this->db->loadObject();
		if($tourn){
			return $tourn;
		}else{
			return null;
		}
	}
	function get_kn_cfg(){
		//variables for knockout
		$cfg = new stdClass();
		$cfg->wdth = 150;
		$cfg->height = 50;
		$cfg->step = 70; 
		$cfg->top_next = 50;
		return $cfg;
	}
	
	function addonexist($addon_name){
		$query = "SELECT name FROM #__bl_addons WHERE published='1' AND name='".$addon_name."'";
		$this->db->setQuery($query);
		return $this->db->loadResult();
	}
	function isBet(){
		$query = "SELECT name FROM #__bl_addons WHERE published='1' AND name='betting'";
		$this->db->setQuery($query);
		$is_betting = $this->db->loadResult();
		return $is_betting;
	}
	
}

?>