<?php
# 
 session_start();
 $_SESSION['user_id'] = 123;
 require_once('includes/functions.php');
 $stand_alone = isset($_REQUEST['no_header']) ? 1 : 0;
 if(!$stand_alone){
	require_once('includes/page_header.php');
 }
 
 $module = isset($_REQUEST['module']) ? $_REQUEST['module'] : 'currency';
 $action = isset($_REQUEST['action']) ? $_REQUEST['action'] : 'listing';

 $content = PageRender($module, $action);
 print $content;
 #debug($_REQUEST);
 if(!$stand_alone){
	require_once('includes/page_footer.php');
}
?>