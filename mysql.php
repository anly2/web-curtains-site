<?php
// MySQL variables
$mysql_host = 'localhost';
$mysql_user = 'root';
$mysql_pass = 'ju44rff';
$mysql_db   = 'curtains';

function mysql_($query, $cRows=false){
   global $mysql_host, $mysql_user, $mysql_pass, $mysql_db;

   if(!mysql_connect($mysql_host, $mysql_user, $mysql_pass))
      return false;
   if(!mysql_select_db($mysql_db))
      return false;

   
   $q = mysql_query($query);
        
   if(is_bool($q))
      return $q;

   if($cRows)
      return mysql_num_rows($q);

   if(mysql_num_rows($q)>0)
      if(mysql_num_rows($q)==1)
         if(mysql_num_fields($q)==1)
            return mysql_result($q, 0, 0);
         else
            return mysql_fetch_array($q);
      else{
         while($r = mysql_fetch_array($q))
            $a[] = $r;
         return $a;
      }
   else
      return false;
}

?>