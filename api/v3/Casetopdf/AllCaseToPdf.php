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
  $return['message'] = [];
  
  if($debug){
    $return['message'][] = ts('Debug is on !');
    echo ts('Debug is on !') . '<br/>' . PHP_EOL;
  }
  
  $config = CRM_Casetopdf_Config::singleton();
    
  $customFileUploadDir = CRM_Casetopdf_Config::getSetting('customFileUploadDir');  
  $pathname = $customFileUploadDir . 'casetopdf/';
    
  $return = CRM_Casetopdf_Config::mkdir($pathname, 0770, false);
  if($return['is_error']){
    if($debug){
      echo $return['error_message'] . '<br/>' . PHP_EOL;
    }
    return civicrm_api3_create_error($return);
    
  }
  $return['message'][] = ts('Directory created, with $pathname \'%s\'.' . $pathname);
  
  $query = "SELECT * FROM civicrm_case
    LEFT JOIN civicrm_case_contact ON civicrm_case_contact.case_id = civicrm_case.id
    LEFT JOIN civicrm_contact ON civicrm_contact.id = civicrm_case_contact.contact_id
    WHERE civicrm_case.is_deleted = '0' AND civicrm_contact.is_deleted = '0'
  ";
  
  if(!$dao = CRM_Core_DAO::executeQuery($query)){
    $return['is_error'] = true;
    $return['error_message'] = sprintf('Failed execute query (%s) !', $query);
    if($debug){
      echo $return['error_message'] . '<br/>' . PHP_EOL;
    }
    return civicrm_api3_create_error($return);
  }
  
  $count = 0;
  while ($dao->fetch()) { 
    if('0' != $limit and $limit == $count){
      if($debug){
        CRM_Utils_System::civiExit();
      }

      return civicrm_api3_create_success($return);
    }
    
    $htmlcasereport  = new CRM_Casetopdf_Case_XMLProcessor_Report();
    $html = $htmlcasereport->htmlCaseReport($dao->case_id, $dao->contact_id, $activitySetName, $isRedact, $includeActivities);
    if(isset($html['is_error']) and $html['is_error']){
      $return['message'][] = $html['error_message'];
      if($debug){
        echo $html['error_message'] . '<br/>' . PHP_EOL;
      }
    }
    
    $filename = $pathname . '(' . $dao->case_id . '_' . $dao->contact_id . ')' . '.pdf';
    if('0' != $limit and CRM_Casetopdf_Config::file_exists($filename)){
      continue;
    }
    
    $output = CRM_Utils_PDF_Utils::html2pdf($html, $filename, true);
    $_return = CRM_Casetopdf_Config::fwrite($filename, $output, 'w');
    
    if($_return['is_error']){
      $return['message'][] = $_return['error_message'];
      if($debug){
        echo $_return['error_message'] . '<br/>' . PHP_EOL;
      }

    }else {
      $return['message'][] = ts('Pdf file created, with $filename \'%1\'.', array(1 => $filename));
      echo ts('Pdf file created, with $filename \'%1\'.', array(1 => $filename)) . '<br/>' . PHP_EOL;
    }
    
    $count++;
  }
  
  if($debug){
    CRM_Utils_System::civiExit();
  }
  
  return civicrm_api3_create_success($return);
}