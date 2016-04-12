<?php
class CRM_Casetopdf_Config {
  /*
   * singleton pattern
   */
  static private $_singleton = NULL;
  
  /*private $hovHouseholdCustomGroupName = 'Huurovereenkomst (huishouden)';
  public $hovHouseholdCustomGroupId = 0;
  public $hovHouseholdCustomTableName = '';
  public $hovHouseholdCustomFields = [];
  
  private $perGegevensCustomGroupName = 'Aanvullende_persoonsgegevens';
  public $perGegevensCustomGroupId = 0;
  public $perGegevensCustomTableName = '';
  public $perGegevensCustomFields = [];
  
  private $hoofdhuurderRelationshipTypeName = 'Hoofdhuurder';
  public $hoofdhuurderRelationshipTypeId = 0;
  
  private $medehuurderRelationshipTypeName = 'Medehuurder';
  public $medehuurderRelationshipTypeId = 0;*/
  
  /**
   * Constructor function
   */
  function __construct() {
    /*$this->setHovHouseholdCustomGroup();
    $this->setPerGegevensCustomGroup();
    
    $this->setHoofdhuurderRelationshipTypeId();
    $this->setMedehuurderRelationshipTypeId();*/
  }
  
  /*// Huurovereenkomst Household
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
  
  public function getHovHousehold($household_id){
    $query = "SELECT hov.entity_id,
      hov." . $this->hovHouseholdCustomFields['HOV_nummer_First']['column_name'] . " as 'HOV_nummer_First',
      hov." . $this->hovHouseholdCustomFields['VGE_nummer_First']['column_name'] . " as 'VGE_nummer_First',
      hov." . $this->hovHouseholdCustomFields['VGE_adres_First']['column_name'] . " as 'VGE_adres_First',
      hov." . $this->hovHouseholdCustomFields['Correspondentienaam_First']['column_name'] . " as 'Correspondentienaam_First',
      hov." . $this->hovHouseholdCustomFields['Begindatum_HOV']['column_name'] . " as 'Begindatum_HOV',
      hov." . $this->hovHouseholdCustomFields['Einddatum_HOV']['column_name'] . " as 'Einddatum_HOV'
      FROM " . $this->hovHouseholdCustomTableName . " as hov
      WHERE hov.entity_id = '%1'
    ";
    $params = array( 
        1 => array($household_id, 'Integer'),
    );
    
    if(!$dao = CRM_Core_DAO::executeQuery($query, $params)){
      return false;
    }
    
    $dao->fetch();
    
    return $dao;
  }
  
  public function getPerNummerFirst($contact_id){
    $query = "SELECT per.entity_id,
      per." . $this->perGegevensCustomFields['Persoonsnummer_First']['column_name'] . " as 'Persoonsnummer_First',
      per." . $this->perGegevensCustomFields['BSN']['column_name'] . " as 'BSN',
      per." . $this->perGegevensCustomFields['Burgerlijke_staat']['column_name'] . " as 'Burgerlijke_staat',
      per." . $this->perGegevensCustomFields['Totaal_debiteur']['column_name'] . " as 'Totaal_debiteur',
      FROM " . $this->perGegevensCustomTableName . " as per
      WHERE per.entity_id = '%1'
    ";
    $params = array( 
        1 => array($contact_id, 'Integer'),
    );
    
    if(!$dao = CRM_Core_DAO::executeQuery($query, $params)){
      return false;
    }
    
    $dao->fetch();
    
    return $dao;
  }*/
  
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
    
    $string = '<Files "*">' . PHP_EOL;
    $string .= '  Order allow,deny' . PHP_EOL;
    $string .= '  Deny from all' . PHP_EOL;
    $string .= '</Files>' . PHP_EOL;
    
    if(!file_exists($pathname . '.htaccess')){
      if(!CRM_Casetopdf_Config::fwrite($pathname . '.htaccess', $string, 'w')){
        $return['is_error'] = true;
        $return['error_message'] = sprintf('Cannot create .htaccess with patname \'%s\' !', $pathname . '.htaccess');
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
  
  public static function file_exists($path){
    return file_exists($path);
  }
  
  /*private function setHoofdhuurderRelationshipTypeId(){
    try {
      $params = array(
        'version' => 3,
        'sequential' => 1,
        'name_a_b' => $this->hoofdhuurderRelationshipTypeName,
      );
      $result = civicrm_api('RelationshipType', 'getsingle', $params);
    } catch (CiviCRM_API3_Exception $ex) {
      throw new Exception('Could not find a relationshiptype with name '.$this->hoofdhuurderRelationshipTypeName
        .',  error from API RelationshipType getsingle : '.$ex->getMessage());
    }
    $this->hoofdhuurderRelationshipTypeId = $result['id'];
  }
  
  private function setMedehuurderRelationshipTypeId(){
    try {
      $params = array(
        'version' => 3,
        'sequential' => 1,
        'name_a_b' => $this->medehuurderRelationshipTypeName,
      );
      $result = civicrm_api('RelationshipType', 'getsingle', $params);
    } catch (CiviCRM_API3_Exception $ex) {
      throw new Exception('Could not find a relationshiptype with name '.$this->medehuurderRelationshipTypeName
        .',  error from API RelationshipType getsingle : '.$ex->getMessage());
    }
    $this->medehuurderRelationshipTypeId = $result['id'];
  }
  
  public function getHoofdhuurder($contact_id){
    try {
      $params = array(
        'version' => 3,
        'sequential' => 1,
        'contact_id' => $contact_id,
      );
      $result = civicrm_api('Contact', 'getsingle', $params);
      
    } catch (CiviCRM_API3_Exception $ex) {
      throw new Exception('Could not find Contact '. $name
        .', error from API Contact Getsingle :'.$ex->getMessage());
    }
    
    if(isset($result['is_error']) and $result['is_error']){
      return false;
    }
    
    switch($result['contact_type']){
      case 'Organization':
        break;
      case 'Household':
        try {
          $params = array(
            'version' => 3,
            'sequential' => 1,
            'contact_id_b' => $result['contact_id'],
            'relationship_type_id' => $this->hoofdhuurderRelationshipTypeId,
            'options' => array(
                'sort' => 'is_active DESC, end_date DESC, start_date DESC'
            )
          );
          $result = civicrm_api('Relationship', 'get', $params);
          
        } catch (CiviCRM_API3_Exception $ex) {
          throw new Exception('Could not find Relationship '. $name
            .', error from API Relationship get :'.$ex->getMessage());
        }
        
        return $result['values'][0]['contact_id_a'];
        
        break;
      case 'Individual':
        return $result['contact_id'];
        break;       
    }
    
    return false;
  }
  
  public function getHousehold($contact_id){
    try {
      $params = array(
        'version' => 3,
        'sequential' => 1,
        'contact_id' => $contact_id,
      );
      $result = civicrm_api('Contact', 'getsingle', $params);
      
    } catch (CiviCRM_API3_Exception $ex) {
      throw new Exception('Could not find Contact '. $name
        .', error from API Contact Getsingle :'.$ex->getMessage());
    }
    
    if(isset($result['is_error']) and $result['is_error']){
      return false;
    }
    
    switch($result['contact_type']){
      case 'Organization':
        break;
      case 'Household':
        return $result['contact_id'];
        break;
      case 'Individual':
        try {
          $params = array(
            'version' => 3,
            'sequential' => 1,
            'contact_id_a' => $result['contact_id'],
            'relationship_type_id' => $this->hoofdhuurderRelationshipTypeId,
            'options' => array(
                'sort' => 'is_active DESC, end_date DESC, start_date DESC'
            )
          );
          $result = civicrm_api('Relationship', 'get', $params);
          
        } catch (CiviCRM_API3_Exception $ex) {
          throw new Exception('Could not find Relationship '. $name
            .', error from API Relationship get :'.$ex->getMessage());
        }
        
        return $result['values'][0]['contact_id_b'];
        break;       
    }
    
    return false;
  }*/
  
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