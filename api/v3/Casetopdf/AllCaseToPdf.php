<?php
set_time_limit(0);

/**
 * Job.AllCaseToPdf API specification (optional)
 * This is used for documentation and validation.
 *
 * @param array $spec description of fields supported by this API call
 * @return void
 * @see http://wiki.civicrm.org/confluence/display/CRMDOC/API+Architecture+Standards
 */
function _civicrm_api3_casetopdf_allcasetopdf_spec(&$spec) {
}

/**
 * Job.AllCaseToPdf API
 *
 * @param array $params
 * @return array API result descriptor
 * @see civicrm_api3_create_success
 * @see civicrm_api3_create_error
 * @throws API_Exception
 */
function civicrm_api3_casetopdf_allcasetopdf($params) {
  $debug = CRM_Utils_Array::value('debug', $params, false);
  $limit = CRM_Utils_Array::value('limit', $params, '0');
    
  $return['is_error'] = false;
  
  if($debug){
    $return['message'][] = ts('Debug is on !');
    echo ts('Debug is on !') . '<br/>' . PHP_EOL;
  }
  
  $config = CRM_Casetopdf_Config::singleton();
  /*echo('$config->hovHouseholdCustomFields: <pre>');
  print_r($config->hovHouseholdCustomFields);
  echo('</pre>');
  
  echo('$config->perGegevensCustomFields: <pre>');
  print_r($config->perGegevensCustomFields);
  echo('</pre>');*/
  
  $Persoonsnummer_First = "Persoonsnummer_First";
  $HOV_nummer_First = "HOV_nummer_First";
  $VGE_adres_First = "VGE_adres_First";
  
  $customFileUploadDir = CRM_Casetopdf_Config::getSetting('customFileUploadDir');  
  $pathname = $customFileUploadDir . 'casetopdf/';
    
  $return = CRM_Casetopdf_Config::mkdir($pathname, 0770, false);
  if($return['is_error']){
    if($debug){
      echo $return['error_message'] . '<br/>' . PHP_EOL;
    }
    return $return;
    
  }
  $return['message'][] = ts('Directory created, with $pathname \'%s\'.' . $pathname);
  
  $query = "SELECT * FROM civicrm_case
    LEFT JOIN civicrm_case_contact ON civicrm_case_contact.case_id = civicrm_case.id
  ";
  
  if(0 != $limit){
    $query .= " LIMIT " . $limit;
  }
    
  if(!$dao = CRM_Core_DAO::executeQuery($query)){
    $return['is_error'] = true;
    $return['error_message'] = sprintf('Failed execute query (%s) !', $query);
    if($debug){
      echo $return['error_message'] . '<br/>' . PHP_EOL;
    }
    return $return;
  }
  
  
  while ($dao->fetch()) { 
    $htmlcasereport  = new CRM_Casetopdf_Case_XMLProcessor_Report();
    $html = $htmlcasereport->htmlCaseReport($dao->case_id, $dao->contact_id, $activitySetName, $isRedact, $includeActivities);
    if(isset($html['is_error']) and $html['is_error']){
      $return['is_error'] = true;
      $return['error_message'] = $html['error_message'];
      if($debug){
        echo $return['error_message'] . '<br/>' . PHP_EOL;
      }
      return $return;
    }

    $filename = $pathname . $dao->case_id . '-' . $dao->contact_id . '.pdf';
    $output = CRM_Utils_PDF_Utils::html2pdf($html, $filename, true);
    $return = CRM_Casetopdf_Config::fwrite($filename, $output, 'w');
    var_dump($return);
    if($return['is_error']){
      if($debug){
        echo $return['error_message'] . '<br/>' . PHP_EOL;
      }
      return $return;

    }
    $return['message'][] = ts('File created, with $pathname \'%s\'.' . $pathname);
  }
  
  if($debug){
    CRM_Utils_System::civiExit();
  }
  
  return civicrm_api3_create_success($return);
}