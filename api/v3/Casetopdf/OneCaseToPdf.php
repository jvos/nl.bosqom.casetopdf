<?php

/**
 * Job.CaseToPdf API specification (optional)
 * This is used for documentation and validation.
 *
 * @param array $spec description of fields supported by this API call
 * @return void
 * @see http://wiki.civicrm.org/confluence/display/CRMDOC/API+Architecture+Standards
 */
function _civicrm_api3_casetopdf_casetopdf_spec(&$spec) {
}

/**
 * Job.CaseToPdf API
 *
 * @param array $params
 * @return array API result descriptor
 * @see civicrm_api3_create_success
 * @see civicrm_api3_create_error
 * @throws API_Exception
 */
function civicrm_api3_casetopdf_onecasetopdf($params) {  
  $caseID            = CRM_Utils_Request::retrieve('caseID', 'Positive', CRM_Core_DAO::$_nullObject);
  $clientID          = CRM_Utils_Request::retrieve('cid', 'Positive', CRM_Core_DAO::$_nullObject);
  $activitySetName   = CRM_Utils_Request::retrieve('asn', 'String', CRM_Core_DAO::$_nullObject);
  $isRedact          = CRM_Utils_Request::retrieve('redact', 'Boolean', CRM_Core_DAO::$_nullObject);
  $includeActivities = CRM_Utils_Request::retrieve('all', 'Positive', CRM_Core_DAO::$_nullObject);

  $htmlcasereport  = new CRM_Casetopdf_Case_XMLProcessor_Report();
  $html = $htmlcasereport->htmlCaseReport($caseID, $clientID, $activitySetName, $isRedact, $includeActivities);
  if(isset($html['is_error']) and $html['is_error']){
    $return['message'][] = $html['error_message'];
    if($debug){
      echo $html['error_message'] . '<br/>' . PHP_EOL;
    }
  }

  $filename = $pathname . '(' . $caseID . '_' . $clientID . ')' . '.pdf';
  
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
  
  if($debug){
    CRM_Utils_System::civiExit();
  }
  
  return civicrm_api3_create_success($return);
}

