<?php
 mysql_connect('localhost','','');
 mysql_select_db('');
 define('API_KEY', 'ab34ba4bae88100c859c552738a91072');
 define('API_URL', 'http://apilayer.net/api/live?access_key='.API_KEY.'&currencies=USD,GBP,EUR,KES,ZAR&format=1');
?>