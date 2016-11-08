<?php
require_once("config.php");

$con = new COM("ADODB.Connection");
$conStr = "DRIVER={Microsoft Access Driver (*.mdb)}; DBQ=".
            realpath(DBNAME).";";
$con->open($conStr);


if (empty($input)) {
    $sql="SELECT * FROM [Ordini]";
} else {
    $$sql="SELECT * FROM [Ordini]";
}

$rs = $con->execute($sql);

if($rs === false) {
  trigger_error('Wrong SQL: ' . $sql . ' Error: ' . $con->ErrorMsg(), E_USER_ERROR);
} else {
  $rows_returned = $rs->RecordCount();
}

$numFields = $rs->Fields->count;

// Print HTML
echo '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
        "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">';
echo '<html xmlns="http://www.w3.org/1999/xhtml">';
echo '<head>';
echo '<meta http-equiv="Content-Type" 
     content="text/html; charset=utf-8" />';
echo '<title>Gestione degli '.DBTBL.'</title>';
echo '<link href="styles.css" rel="stylesheet" type="text/css" />';
echo '<link rel="stylesheet" href="css/bootstrap.css">';
echo '<link rel="stylesheet" href="css/footable.bootstrap.css">';
echo '<link rel="stylesheet" href="css/footable.bootstrap.min.css">';
echo '<link rel="stylesheet" href="css/footable.core.bootstrap.min.css">';
echo '</head><body>';
echo '<h1>GESTIONE '.DBTBL.'</h1>';
// Elenca records -----

if ($rs->num_rows > 0) {
     echo "<table>
  <thead>
    <tr>
      <th>ID</th>
      <th>Tipo</th>
      <th>Numero</th>
      <th>Data</th>
      <th>Ragione Sociale</th>
      <th>Indirizzo</th>
      <th>Importo Totale</th>
      <th>Importo Iva</th>
    </tr>
  </thead>";
     // output data of each row
     while($row = $rs->fetch_assoc()) {
         echo "<tr><td>" . $row["Id Ord"]. "</td><td>" . $row["Tipo Ord"]. " " . $row["N Ord"]. "</td></tr>";
     }
     echo "</table>";
} else {
     echo "0 results";
}


echo("</div>");
echo ("<p>[ <a href='?ins=1'>Inserimento nuovo record</a> ]</p>");

// Modifica record -----
if (!empty($_GET['id'])){
  $id = intval($_GET['id']);
  $rs = $con->execute("SELECT [Id Ord] AS [ID], [Tipo Ord] AS [Tipo], [N Ord] AS [Numero], [Data Ord] AS [Data], [Ragione Sociale], [Indirizzo], [TotImp] AS [IMPORTO TOTALE], [TotIva] AS [IMPORTO IVA] FROM ".DBTBL." WHERE ".PKNAME."=".$id);
  echo ("<form action='modify.php' method='post'>");
  echo ("<fieldset>");
  echo ("<legend>Modifica record</legend>");
  for ($i=0;$i<$numFields;$i++){
    if (LINKPK && $i==PKCOL){
      echo ("<label for='".$rs->Fields($i)->name."'>"
             .$rs->Fields($i)->name."</label>");
      echo ("<input type='text' readonly='readonly' name='"
             .$rs->Fields($i)->name."' value=\""
             .$rs->Fields($i)->value."\" /><br />\n");      
    }
    else {
      echo ("<label for='".$rs->Fields($i)->name."'>"
             .$rs->Fields($i)->name."</label>");
      echo ("<input type='text' name='".$rs->Fields($i)->name."' value=\""
             .$rs->Fields($i)->value."\" /><br />\n");
    }
  }
  echo ("<button type='submit' name='azione' value='modifica'>Modifica</button>");
  echo ("<button class='affiancato' type='submit' 
        name='azione' value='cancella'>Cancella</button>");
  echo ("</fieldset></form>");
}

// Inserimento record -----
elseif (!empty($_GET['ins'])){
  echo ("<form action='modify.php' method='post'>");
  echo ("<fieldset>");
  echo ("<legend>Inserimento record</legend>");
  for ($i=0;$i<$numFields;$i++){
    if ($i!=PKCOL){
      echo ("<label for='".$rs->Fields($i)->name."'>"
             .$rs->Fields($i)->name."</label>");
      echo ("<input type='text' name='".$rs->Fields($i)->name."' /><br />\n");
    }
  }
  echo ("<button type='submit' name='azione' value='inserisci'>Inserisci</button>");
  echo ("<br />");
  echo ("</fieldset></form>");
  echo '<script src="js/footable.js"></script>';
  echo '<script src="js/footable.min.js"></script>';
}
echo '</body></html>';
$rs->Close();
$con->Close();
?>