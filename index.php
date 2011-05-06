<?php
session_start();
include "mysql.php";

head:
echo '<html>'."\n";
echo '<head>'."\n";
echo '   <title>Curtains\' Workshop</title>'."\n";
echo '</head>'."\n\n";

echo '<body>'."\n";
echo '<table border="0" width="100%" style="margin-top:150px;">'."\n";
echo '   <tr>'."\n";
echo '      <td width="10%"></td>'."\n";
echo '      <td width="80%" align="center">'."\n\n";
body:

if(isset($_REQUEST['orders']) || empty($_REQUEST)){
   if(isset($_REQUEST['new'])){
   	if(isset($_REQUEST['apply'])){

         mysql_("INSERT INTO Orders (EID, CustomName, Warranter, Since, Deadline) values ()");
         
         echo '<script type="text/javascript">window.location.href="?orders"</script>';
      	goto end;
   	}

      if(strlen(trim($_REQUEST['new']))>0)
         $provided = mysql_("SELECT EID, Warranter as warranter, Deadline as deadline, Width as width, Height as height, Cloth as cloth FROM Orders WHERE EID='".$_REQUEST['new']."' LIMIT 1");
      else
         $provided = array('EID'=>'', 'warranter'=>'', 'deadline'=>'', 'width'=>'', 'height'=>'', 'cloth'=>'');



      // Display Form
      echo '<h2>Нова Поръчка</h2>'."<br />\n";
      echo '<a href="#" onclick="window.history.back();" style="float:left">Обратно</a>'."<br />\n";
      echo "\n";
      echo '<form action="?orders&new&apply" method="post">'."\n";
      echo '<input type="hidden" name="EID" value="'.$provided['EID'].'" />'."\n";
      echo "\n";
      echo '<table>'."\n";
      echo '   <tr>'."\n";
      echo '      <td>Поръчител: </td>'."\n";
      echo '      <td><input type="text" name="warranter" value="'.$provided['warranter'].'" /></td>'."<br />\n";
      echo '   </tr>'."\n";
      echo '   <tr>'."\n";
      echo '      <td>Срок:</td>'."\n";
      echo '      <td><input type="text" name="deadline"  value="'.$provided['deadline'] .'" /></td>'."<br />\n";
      echo '   </tr>'."\n";
      echo '   <tr>'."\n";
      echo '      <td colspan="2"><br /></td>'."\n";
      echo '   </tr>'."\n";
      echo '   <tr>'."\n";
      echo '      <td>Ширина:</td>'."\n";
      echo '      <td><input type="text" name="width"  value="'.$provided['width'] .'"/></td>'."<br />\n";
      echo '   </tr>'."\n";
      echo '   <tr>'."\n";
      echo '      <td>Височина:</td>'."\n";
      echo '      <td><input type="text" name="height" value="'.$provided['height'].'"/></td>'."<br />\n";
      echo '   </tr>'."\n";
      echo '   <tr>'."\n";
      echo '      <td colspan="2"><br /></td>'."\n";
      echo '   </tr>'."\n";
      echo '   <tr>'."\n";
      echo '      <td colspan="2" align="center">Плат/Цвят:</td>'."\n";
      echo '   </tr>'."\n";
      echo '   <tr>'."\n";
      echo '      <td colspan="2" align="center">'."\n";
      echo '         <select name="cloth"'."\n";
      echo '           onchange="document.forms[0].subname.disabled=false; document.forms[0].subname.innerHTML = \'<option selected></option><option>\'+(this.options[this.selectedIndex].attributes[\'subnames\'].value).split(\',\').join(\'</option><option>\')+\'</option>\';">'."\n";
      echo '            <option></option>'."\n";

      $option = mysql_("Select Cloth, Subname from Materials Where Subname is not null");
      foreach($option  as $value)
      	$options[$value['Cloth']][] = $value['Subname'];
      foreach($options as $key => $value)
         echo '         <option value="'.$key.'" subnames="'.implode(",", $value).'" '.(stripos(" ".$provided['cloth'], $key)? 'selected' : '').' >'.ucfirst($key).'</option>'."\n";
         
      echo '         </select>'."\n";
      echo '         <select name="subname" disabled'."\n";
      echo '           onchange="document.forms[0].quantity.disabled=false;">'."\n";
      echo '         </select>'."\n";
      echo '      </td>'."\n";
      echo '   </tr>'."\n";
      echo '   <tr>'."\n";
      echo '      <td colspan="2" align="center">'."\n";
      echo '         Нужни: <span id="cloth:needed"></span>'."\n";
      echo '      </td>'."\n";
      echo '   </tr>'."\n";
      echo '   <tr>'."\n";
      echo '      <td colspan="2"><br /></td>'."\n";
      echo '   </tr>'."\n";
      echo '   <tr>'."\n";
      echo '      <td colspan="2" align="center"><input type="submit" name="apply" value="Confirm" /></td>'."\n";
      echo '   </tr>'."\n";
      echo '</table>'."\n";
      echo '</form>'."\n";
      
      goto end;
   }
   if(isset($_REQUEST['finish'])){
      mysql_("DELETE FROM Orders WHERE EID='".$_REQUEST['finish']."'");

      echo '<script type="text/javascript">window.location.href="?orders"</script>';
      goto end;
   }
   if(isset($_REQUEST['cancel'])){
      mysql_(
         "Update Materials, Orders ".
         "Set Materials.Quantity=(Materials.Quantity+Orders.Supplied) ".
         "Where Orders.EID='".$_REQUEST['cancel']."' ".
         ' AND Materials.Cloth=LEFT(Orders.Cloth, Locate("/", Orders.Cloth)-1) '.
         ' AND Materials.Subname=RIGHT(Orders.Cloth, LENGTH(Orders.Cloth)-Locate("/", Orders.Cloth));'
      );
      mysql_("DELETE FROM Orders WHERE EID='".$_REQUEST['cancel']."'");

      echo '<script type="text/javascript">window.location.href="?orders"</script>';
      goto end;;
   }

   echo '<h2>Поръчки</h2>'."\n\n";
   echo '<a href="?materials"  style="float:left">Материали   </a>'   ."<br />\n";
   echo '<a href="?revision"   style="float:left">Ревизия     </a>'   ."<br />\n";
   echo '<span                 style="float:left">----------- </span>'."<br />\n";
   echo '<a href="?orders&new" style="float:left">Нова Поръчка </a>'   ."<br />\n";

   $orders = mysql_("SELECT EID, CustomName, Warranter, Since, Deadline FROM Orders ORDER BY EID");

   if($orders)
      foreach($orders as $value){
      	echo '<div class="order node" id="order:'.$value['EID'].'">'."\n";
         echo '   <table width="650" rules="cols">'."\n";
         echo '      <tr>'."\n";
         echo '         <td width="69%" style="min-width:450;">'."\n";
         echo "\n";
         echo '            <table width="100%">'."\n";
         echo '               <tr>'."\n";
         echo '                  <td rowspan="2" align="center" valign="middle">#'.$value['EID'].'</td>'."\n";
         echo '                  <td colspan="2"><i>&bdquo; '.$value['CustomName'].' &rdquo;</i></td>'."\n";
         echo '                  <td colspan="3">за '.$value['Warranter'].'</td>'."\n";
         echo '               </tr>'."\n";
         echo '               <tr>'."\n";
         echo '                  <td></td>'."\n";
         echo '                  <td colspan="2">от '.$value['Since'].'</td>'."\n";
         echo '                  <td colspan="2">за '.$value['Deadline'].'</td>'."\n";
         echo '               </tr>'."\n";
         echo '               <tr>'."\n";
         echo '                  <td width="15%"><!--               | EID --></td>'."\n";
         echo '                  <td width="17%"><!-- Date Padding  | Half of cName --></td>'."\n";
         echo '                  <td width="17%"><!-- Half of Since | Half of cName --></td>'."\n";
         echo '                  <td width="17%"><!-- Half of Since    | Third of Warranter --></td>'."\n";
         echo '                  <td width="17%"><!-- Half of Deadline | Third of Warranter --></td>'."\n";
         echo '                  <td width="17%"><!-- Half of Deadline | Third of Warranter --></td>'."\n";
         echo '               </tr>'."\n";
         echo '            </table>'."\n";
         echo "\n";
         echo '         </td>'."\n";
         echo '         <td width="30%" style="min-width:200;">'."\n";
         echo "\n";
         echo '            <table width="100%">'."\n";
         echo '               <tr>'."\n";
         echo '                  <td width="50%" align="center"> <input type="button" onclick="if(confirm(\'This will remove the entry.\nAre you sure you want to continue?\')) window.location.href=\'?orders&finish='.$value['EID'].'\';" value="Finish" /> </td>'."\n";
         echo '                  <td width="50%" align="center"> <input type="button" onclick="if(confirm(\'This will remove the entry.\nAre you sure you want to continue?\')) window.location.href=\'?orders&cancel='.$value['EID'].'\';" value="Cancel" /> </td>'."\n";
         echo '               </tr>'."\n";
         echo '               <tr>'."\n";
         echo '                  <td colspan="2" align="center"> <input type="button" onclick="window.location.href=\'?orders&new='.$value['EID'].'\';" value="Edit" />  </td>'."\n";
         echo '               </tr>'."\n";
         echo '            </table>'."\n";
         echo "\n";
         echo '         </td>'."\n";
         echo '      </tr>'."\n";
         echo '   </table>'."\n";
         echo '</div>'."\n";
         echo '<br /><br />'."\n";
      }
   else
      echo "Няма чакащи заявки<br /><br />\n";

   goto end;
}

if(isset($_REQUEST['materials'])){
   echo '<h2>Материали</h2>'."\n\n";
   echo '<a href="?orders"     style="float:left">Поръчки  </a>'."<br />\n";
   echo '<a href="?revision"   style="float:left">Ревизия </a>'."<br />\n";

   echo "\n".'<script type="text/javascript">'."\n";
   echo 'function displaySubname(cloth){'."\n";
   echo '    var select_ = document.getElementById("cloth:"+cloth+".subname");'."\n";
   echo '   var subname  = select_.options[select_.selectedIndex]; '."\n";
   echo '   var pic      = document.getElementById("cloth:"+cloth+".pic");'."\n";
   echo '   var quantity = document.getElementById("cloth:"+cloth+".sqd");'."\n";
   echo "\n";
   echo '   pic.setAttribute("background", subname.attributes["pic"].value);'."\n";
   echo '   quantity.innerHTML = subname.attributes["quantity"].value;'."\n";
   echo '}'."\n";
   echo '</script>'."\n\n";


   $cloths = mysql_("SELECT Cloth, Weight, Transparency, Width FROM Materials WHERE Subname is Null");
   $cloth  = (!is_array($cloths[0]))? array($cloths) : $cloths;

   foreach($cloth as $value){
      echo '<div class="cloth node" id="cloth:'.$value['Cloth'].'">'."\n";
      echo '   <table width="600" rules="cols">'."\n";
      echo '      <tr>'."\n";
      echo '         <td width="50%" style="min-width:300;">'."\n";

      echo '            <h3>Име на плата: <i>'.$value['Cloth'].'</i></h3>'."\n";
      echo '            <table>'."\n";
      echo '               <tr>'."\n";
      echo '                  <td>Ширина на лентата:</td>'."\n";
      echo '                  <td><b>'.$value['Width'].'</b>мм</td>'."\n";
      echo '               </tr>'."\n";
      echo '               <tr>'."\n";
      echo '                  <td>Тегло:</td>'."\n";
      echo '                  <td><b>'.$value['Weight'].'</b>гр./кв.м</td>'."\n";
      echo '               </tr>'."\n";
      echo '               <tr>'."\n";
      echo '                  <td>Прозрачност:</td>'."\n";
      echo '                  <td><b>'.$value['Transparency'].'</b></td>'."\n";
      echo '               </tr>'."\n";
      echo '            </table>'."\n";

      echo '         </td>'."\n";
      echo '         <td>'."\n";

      $subnames = mysql_("SELECT Subname, Picture, Quantity FROM Materials Where Cloth='".$value['Cloth']."' AND Subname is not Null");
      $subname  = (!is_array($subnames[0]))? array($subnames) : $subnames;

      echo '            <table>'."\n";
      echo '               <tr>'."\n";
      echo '                  <td id="cloth:'.$value['Cloth'].'.pic" background="'.$subname[0]['Picture'].'" width="150" height="150"></td>'."\n";
      echo '                  <td>'."\n";
      echo '                     <select size="3" id="cloth:'.$value['Cloth'].'.subname"'."\n";
      echo '                       onchange="displaySubname(\''.$value['Cloth'].'\');">'."\n";
      foreach($subname as $val)
      	echo '                        <option value="'.$val['Subname'].'" pic="'.$val['Picture'].'" quantity="'.$val['Quantity'].'">'.$value['Cloth']." ".$val['Subname'].'</option>'."\n";
      echo '                     </select>'."\n";
      echo '                  </td>'."\n";
      echo '               </tr>'."\n";
      echo '               <tr>'."\n";
      echo '                  <td align="center" colspan="2">'."\n";
      echo '                     Налични: <b id="cloth:'.$value['Cloth'].'.sqd">'.$subname[0]['Quantity'].'</b> ролки'."\n";
      echo '                  </td>'."\n";
      echo '               </tr>'."\n";
      echo '            </table>'."\n";

      echo '         </td>'."\n";
      echo '      </tr>'."\n";
      echo '   </table>'."\n";
      echo '</div>'."\n";
   }

   goto end;
}

if(isset($_REQUEST['revision'])){
   if(isset($_REQUEST['apply'])){
      mysql_("Update Materials Set Quantity=Quantity-".$_REQUEST['quantity']." Where Cloth='".$_REQUEST['cloth']."' AND Subname='".$_REQUEST['subname']."'");
      echo '<script type="text/javascript">window.location.href="?materials";</script>';
   	goto end;
   }

   echo '<h2>Ревизия</h2>'."\n\n";
   echo '<a href="?orders"     style="float:left">Поръчки    </a>'."<br />\n";
   echo '<a href="?materials"  style="float:left">Материали </a>'."<br />\n";

   echo '   <form action="?revision&apply" method="post">'."\n";
   echo '      <select name="cloth"'."\n";
   echo '        onchange="document.forms[0].subname.disabled=false; document.forms[0].subname.innerHTML = \'<option selected></option><option>\'+(this.options[this.selectedIndex].attributes[\'subnames\'].value).split(\',\').join(\'</option><option>\')+\'</option>\';">'."\n";
   echo '         <option selected></option>'."\n";

   $option = mysql_("Select Cloth, Subname from Materials Where Subname is not null");
   foreach($option  as $value)
   	$options[$value['Cloth']][] = $value['Subname'];
   foreach($options as $key => $value)
      echo '         <option value="'.$key.'" subnames="'.implode(",", $value).'">'.ucfirst($key).'</option>'."\n";
      
   echo '      </select>'."\n";
   echo "\n";
   echo '      <select name="subname" disabled'."\n";
   echo '        onchange="document.forms[0].quantity.disabled=false;">'."\n";
   echo '      </select>'."\n";
   echo '      <br /><br />'."\n";
   echo "\n";
   echo '      Количество:'."\n";
   echo '      <input type="text" name="quantity" disabled'."\n";
   echo '        onchange="document.forms[0].apply.disabled = (!isNaN(this.value) && parseInt(this.value)>0)? false : true;"/>'."\n";
   echo '      <br /><br />'."\n";
   echo "\n";
   echo '      <br />'."\n";
   echo '      <input type="submit" name="apply" value="Confirm" disabled />'."\n";
   echo '   </form>'."\n";

   goto end;
}

end:
echo "\n\n";
echo '      </td>'."\n";
echo '      <td width="10%"></td>'."\n";
echo '   </tr>'."\n";
echo '</table>'."\n";
echo '</body>'."\n\n";

echo '</html>'."\n";
?>
 