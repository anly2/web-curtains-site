<html>
<head><title>Curtains' Workshop Test</title></head>
<body>

   <form action="?revision&apply" method="post">
      <select name="cloth"
        onchange="document.forms[0].subname.disabled=false; document.forms[0].subname.innerHTML = '<option></option><option>'+(this.options[this.selectedIndex].attributes['subnames'].value).split(',').join('</option><option>')+'</option>';">
         <option value="rococo" subnames="1203,1205,1206">Rococo</option>
         <option value="roco"   subnames="1303,1305,1306">Roco</option>
         <option value="roc"    subnames="130,130,130">Roc</option>
      </select>

      <select name="subname" disabled
        onchange="document.forms[0].quantity.disabled=false;">
      </select>
      <br />

      Количество:
      <input type="text" name="quantity" disabled
        onchange="if(!isNaN(this.value) && parseInt(this.value)>0 && parseInt(this.value)<=5) document.forms[0].confirm.disabled=false;"/>
      <br />

      <br />
      <input type="submit" name="confirm" value="Confirm" disabled />
   </form>

</body>
</html>