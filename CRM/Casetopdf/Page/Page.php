<?php

require_once 'CRM/Core/Page.php';

class CRM_Casetopdf_Page_Page extends CRM_Core_Page {
  public function run() {
    // Example: Set the page-title dynamically; alternatively, declare a static title in xml/Menu/*.xml
    CRM_Utils_System::setTitle(ts('Page'));

    // Example: Assign a variable for use in a template
    $this->assign('currentTime', date('Y-m-d H:i:s'));
    
    $caseID            = CRM_Utils_Request::retrieve('caseID', 'Positive', CRM_Core_DAO::$_nullObject);
    $clientID          = CRM_Utils_Request::retrieve('cid', 'Positive', CRM_Core_DAO::$_nullObject);
    $activitySetName   = CRM_Utils_Request::retrieve('asn', 'String', CRM_Core_DAO::$_nullObject);
    $isRedact          = CRM_Utils_Request::retrieve('redact', 'Boolean', CRM_Core_DAO::$_nullObject);
    $includeActivities = CRM_Utils_Request::retrieve('all', 'Positive', CRM_Core_DAO::$_nullObject);
    
    $htmlcasereport  = new CRM_Casetopdf_Case_XMLProcessor_Report();
    $html = $htmlcasereport->htmlCaseReport($caseID, $clientID, $activitySetName, $isRedact, $includeActivities);
    
    CRM_Utils_PDF_Utils::html2pdf($html, 'casetopdf.pdf', False);
    CRM_Utils_System::civiExit();
    
    parent::run();
  }
}
