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
  //$spec['magicword']['api.required'] = 1;
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
function civicrm_api3_casetopdf_casetopdf($params) {
  /*if (array_key_exists('magicword', $params) && $params['magicword'] == 'sesame') {
    $returnValues = array( // OK, return several data rows
      12 => array('id' => 12, 'name' => 'Twelve'),
      34 => array('id' => 34, 'name' => 'Thirty four'),
      56 => array('id' => 56, 'name' => 'Fifty six'),
    );
    // ALTERNATIVE: $returnValues = array(); // OK, success
    // ALTERNATIVE: $returnValues = array("Some value"); // OK, return a single value

    // Spec: civicrm_api3_create_success($values = 1, $params = array(), $entity = NULL, $action = NULL)
    return civicrm_api3_create_success($returnValues, $params, 'NewEntity', 'NewAction');
  } else {
    throw new API_Exception(/*errorMessage*/ /*'Everyone knows that the magicword is "sesame"', /*errorCode*/ /*1234);
  }*/
  
  $caseID            = CRM_Utils_Request::retrieve('caseID', 'Positive', CRM_Core_DAO::$_nullObject);
  $clientID          = CRM_Utils_Request::retrieve('cid', 'Positive', CRM_Core_DAO::$_nullObject);
  $activitySetName   = CRM_Utils_Request::retrieve('asn', 'String', CRM_Core_DAO::$_nullObject);
  $isRedact          = CRM_Utils_Request::retrieve('redact', 'Boolean', CRM_Core_DAO::$_nullObject);
  $includeActivities = CRM_Utils_Request::retrieve('all', 'Positive', CRM_Core_DAO::$_nullObject);

  $htmlcasereport  = new CRM_Casetopdf_Case_XMLProcessor_Report();
  $html = $htmlcasereport->htmlCaseReport($caseID, $clientID, $activitySetName, $isRedact, $includeActivities);

  CRM_Utils_PDF_Utils::html2pdf($html, 'casetopdf.pdf', false);
  CRM_Utils_System::civiExit();
}

