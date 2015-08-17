<?php
 require_once('includes/db_connection.php');
 function pageRender($module='', $action=''){
  if(!file_exists("modules/".$module."_processor.php")){
   print "modules/".$module.".php";
    return page404($module);
  }
  require_once("modules/".$module."_processor.php");
  $module_object = new $module;
  if(!method_exists($module_object, $action)){
	return page404($module);
  }
  
  
  return $module_object->$action();
 }
 
 function page404($class){
   return "<DIV class='error404'>Error 404<br/><br/>The system is unable to find the requested action</DIV>";
 }

?>