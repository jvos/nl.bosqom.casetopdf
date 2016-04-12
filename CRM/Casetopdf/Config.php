<?php
class CRM_Casetopdf_Config {
  /*
   * singleton pattern
   */
  static private $_singleton = NULL;
    
  /**
   * Constructor function
   */
  function __construct() {
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
    try {
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
      
    } catch (CiviCRM_API3_Exception $ex) {
      throw new Exception('Could not mkdir '.$pathname
        .' in mkdir, error from mkdir :'.$ex->getMessage());
    }
  }
  
  public static function fwrite($filename, $string, $mode =  'w'){
    try {
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
      
    } catch (CiviCRM_API3_Exception $ex) {
      throw new Exception('Could not fwrite '.$filename
        .' in mkdir, error from fwrite :'.$ex->getMessage());
    }
  }
  
  public static function file_exists($path){
    return file_exists($path);
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