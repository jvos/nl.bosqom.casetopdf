<?php
class CRM_Casetopdf_Config {
  /*
   * singleton pattern
   */
  static private $_singleton = NULL;
  
  private $hovHouseholdCustomGroupName = 'Huurovereenkomst (huishouden)';
  public $hovHouseholdCustomGroupId = 0;
  public $hovHouseholdCustomTableName = '';
  public $hovHouseholdCustomFields = [];
  
  private $perGegevensCustomGroupName = 'Aanvullende_persoonsgegevens';
  public $perGegevensCustomGroupId = 0;
  public $perGegevensCustomTableName = '';
  public $perGegevensCustomFields = [];
  
  /**
   * Constructor function
   */
  function __construct() {
    $this->setHovHouseholdCustomGroup();
    $this->setPerGegevensCustomGroup();
  }
  
  // Huurovereenkomst Household
  private function setHovHouseholdCustomGroup(){
    try {
      $customGroup = civicrm_api3('CustomGroup', 'Getsingle', array('name' => $this->hovHouseholdCustomGroupName));
    } catch (CiviCRM_API3_Exception $ex) {
      throw new Exception('Could not find a group with name '.$this->hovHouseholdCustomGroupName
        .',  error from API CustomGroup Getvalue : '.$ex->getMessage());
    }
    $this->hovHouseholdCustomGroupId = $customGroup['id'];
    $this->hovHouseholdCustomTableName = $customGroup['table_name'];
    $this->setHovHouseholdCustomFields();
  }
  
  private function setHovHouseholdCustomFields(){    
    try {
      $customFields = civicrm_api3('CustomField', 'Get', array('custom_group_id' => $this->hovHouseholdCustomGroupId));
    } catch (CiviCRM_API3_Exception $ex) {
      throw new Exception('Could not find custom fields with group id '.$this->hovHouseholdCustomGroupId
        .' in custom group '.$this->hovHouseholdCustomGroupName.', error from API CustomField Getvalue :'.$ex->getMessage());
    }
    
    foreach ($customFields['values'] as $custom_field){
      $this->hovHouseholdCustomFields[$custom_field['name']] = $custom_field;
    }
  }
  
  // Aanvullende persoonsgegevens
  private function setPerGegevensCustomGroup(){
    try {
      $customGroup = civicrm_api3('CustomGroup', 'Getsingle', array('name' => $this->perGegevensCustomGroupName));
    } catch (CiviCRM_API3_Exception $ex) {
      throw new Exception('Could not find a group with name '.$this->perGegevensCustomGroupName
        .',  error from API CustomGroup Getvalue : '.$ex->getMessage());
    }
    $this->perGegevensCustomGroupId = $customGroup['id'];
    $this->perGegevensCustomTableName = $customGroup['table_name'];
    $this->setPerGegevensCustomFields();
  }
  
  private function setPerGegevensCustomFields(){    
    try {
      $customFields = civicrm_api3('CustomField', 'Get', array('custom_group_id' => $this->perGegevensCustomGroupId));
    } catch (CiviCRM_API3_Exception $ex) {
      throw new Exception('Could not find custom fields with group id '.$this->perGegevensCustomGroupId
        .' in custom group '.$this->perGegevensCustomGroupName.', error from API CustomField Getvalue :'.$ex->getMessage());
    }
    
    foreach ($customFields['values'] as $custom_field){
      $this->perGegevensCustomFields[$custom_field['name']] = $custom_field;
    }
  }
  
  public function getHovHousehold($contact_id){
    /*$query = "SELECT contact.id, hov.entity_id,
      hov." . $customfields['HOV_nummer_First']['column_name'] . ",
      hov." . $customfields['VGE_nummer_First']['column_name'] . ",
      hov." . $customfields['VGE_adres_First']['column_name'] . ",
      hov." . $customfields['Correspondentienaam_First']['column_name'] . ",
      hov." . $customfields['Begindatum_HOV']['column_name'] . ",
      hov." . $customfields['Einddatum_HOV']['column_name'] . "
      FROM " . $this->hovHouseholdCustomTableName . " 
      LEFT JOIN " . $customgroup['table_name'] . " as hov ON contact.id = hov.entity_id
      WHERE contact.contact_type = %1 AND hov." . $customfields['Einddatum_HOV']['column_name']. " < %2
      ORDER BY contact.id ASC
    ";
    $params = array( 
        1 => array('Household', 'String'),
        2 => array(date('Y-m-d H:m:i'), 'String'),
    );
    $dao = CRM_Core_DAO::executeQuery($query, $params);*/
  }
  
  public function getPerNummerFirst($contact_id){
    /*$query = "SELECT contact.id, hov.entity_id,
      hov." . $customfields['HOV_nummer_First']['column_name'] . ",
      hov." . $customfields['VGE_nummer_First']['column_name'] . ",
      hov." . $customfields['VGE_adres_First']['column_name'] . ",
      hov." . $customfields['Correspondentienaam_First']['column_name'] . ",
      hov." . $customfields['Begindatum_HOV']['column_name'] . ",
      hov." . $customfields['Einddatum_HOV']['column_name'] . "
      FROM civicrm_contact AS contact
      LEFT JOIN " . $customgroup['table_name'] . " as hov ON contact.id = hov.entity_id
      WHERE contact.contact_type = %1 AND hov." . $customfields['Einddatum_HOV']['column_name']. " < %2
      ORDER BY contact.id ASC
    ";
    $params = array( 
        1 => array('Household', 'String'),
        2 => array(date('Y-m-d H:m:i'), 'String'),
    );
    $dao = CRM_Core_DAO::executeQuery($query, $params);*/
  }
  
  public static function getSetting($name){
    try {
      $params = array(
        'version' => 3,
        'sequential' => 1,
      );
      $result = civicrm_api('Setting', 'get', $params);
      
      if(!isset($result['values'][0][$name])){
        return false;
      }
      
      return $result['values'][0][$name];
            
    } catch (CiviCRM_API3_Exception $ex) {
      throw new Exception('Could not find setting '. $name
        .', error from API Setting Get :'.$ex->getMessage());
    }
    
    return false;
  }
  
  public static function mkdir($pathname, $mode = 0770){
    $return['is_error'] = false;
    
    if(!file_exists($pathname)){
      if(!mkdir($pathname, $mode, true)){
        $return['is_error'] = true;
        $return['error_message'] = sprintf('Cannot create directotory with patname \'%s\' !', $pathname);
      }
    }
    
    return $return;
  }
  
  public static function fwrite($filename, $string, $mode =  'w'){
    $return['is_error'] = false;
    
    // Let's make sure the file exists and is writable first.
    //if (is_writable($filename)) {

        // In our example we're opening $filename in append mode.
        // The file pointer is at the bottom of the file hence
        // that's where $somecontent will go when we fwrite() it.
        if (!$handle = fopen($filename, $mode)) {
            $return['is_error'] = true;
            $return['error_message'] = sprintf('Cannot open file with filename \'%s\' !', $filename);
            return $return;
        }

        // Write $somecontent to our opened file.
        if (fwrite($handle, $string) === FALSE) {
            $return['is_error'] = true;
            $return['error_message'] = sprintf('Cannot write to file with filename \'%s\' !', $filename);
            return $return;
        }
        
        fclose($handle);
        return $return;

    /*} else {
        $return['is_error'] = true;
        $return['error_message'] = sprintf('The file is not writable with filename \'%s\' !', $filename);
        return $return;
    }*/
    
    $return['is_error'] = true;
    $return['error_message'] = sprintf('Something got wrong with filename \'%s\' !', $filename);
    return $return;
  }
  
  /**
   * Function to return singleton object
   * 
   * @return object $_singleton
   * @access public
   * @static
   */
  public static function &singleton() {
    if (self::$_singleton === NULL) {
      self::$_singleton = new CRM_Casetopdf_Config();
    }
    return self::$_singleton;
  }
}