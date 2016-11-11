<?php
require_once("config.php");

$con = new COM("ADODB.Connection");
$conStr = "DRIVER={Microsoft Access Driver (*.mdb)}; DBQ=".
            realpath(DBNAME).";";
$con->open($conStr);

$input=$_POST['input'];
$id=$_POST['id'];
$tipo=$_POST['tipo'];
$numero1=$_POST['numero1'];
$numero2=$_POST['numero2'];
$data1=date('d/m/Y', strtotime($_POST['from']));;
$data2=date('d/m/Y', strtotime($_POST['to']));;

//If the data text box are empty the system write "01/01/1970" so, I cancel that
if ($data1=="01/01/1970") { 
  
  $data1 = ""; 
} 

if ($data2=="01/01/1970") { 
  
  $data2 = ""; 
} 

if ($tipo=="Tutti i tipi") { 
  
  $tipo = ""; 
} 

$optionsSQL = "";

foreach($_POST["options"] as $index => $option) {
  if ($optionsSQL == "") $optionsSQL = " Stato IN ("; //if it's the first detected option, add the IN clause to the string
  $optionsSQL .= $option.",";
}

//trim the trailing comma and add the closing bracket of the IN clause instead
if ($optionsSQL != "") 
{
  $optionsSQL = rtrim($optionsSQL, ","); 
  $optionsSQL .= ")";
}

$sql="SELECT [Id Ord] AS [ID], [Tipo Ord] AS [Tipo], [N Ord] AS [Numero], [Data Ord] AS [Data], [Ragione Sociale], [Indirizzo], [Stato], [TotImp] AS [IMPORTO TOTALE], [TotIva] AS [IMPORTO IVA] FROM [Ordini] WHERE";
$whereSql=""; 

if (!empty($input)) { 
  $whereSql .= " [Indirizzo] LIKE '%$input%' OR [Ragione Sociale] LIKE '%$input%'"; 
} 
if (!empty($id)) { 
  if ($whereSql != "") { 
    $whereSql .= " AND";  
  }
  $whereSql .= " [Id Ord] LIKE '$id'"; 
} 

if (!empty($tipo)) { 
  if ($whereSql != "") { 
    $whereSql .= " AND";  
  }
  $whereSql .= " [Tipo Ord] LIKE '$tipo'"; 
} 

if (!empty($data1)) { 
  if ($whereSql != "") { 
    $whereSql .= " AND";  
  }
  $whereSql .= " [Data Ord] BETWEEN #$data1# AND #$data2#"; 
} 

if (!empty($numero1)) { 
  if ($whereSql != "") { 
    $whereSql .= " AND";  
  }
  $whereSql .= " [N Ord] BETWEEN '$numero1' AND '$numero2'"; 
} 

if (!empty($option)) { 
  if ($whereSql != "") { 
    $whereSql .= " AND";  
  }
  $whereSql .= $optionsSQL; 
} 

/*
if (empty($input)) {
    $sql="SELECT [Id Ord] AS [ID], [Tipo Ord] AS [Tipo], [N Ord] AS [Numero], [Data Ord] AS [Data], [Ragione Sociale], [Indirizzo], [Stato], [TotImp] AS [IMPORTO TOTALE], [TotIva] AS [IMPORTO IVA] FROM [Ordini] WHERE [Id Ord] LIKE '$id' OR [Tipo Ord] LIKE '$tipo' OR [Data Ord] BETWEEN #$data1# AND #$data2#".$optionsSQL;
} else {
    $sql="SELECT [Id Ord] AS [ID], [Tipo Ord] AS [Tipo], [N Ord] AS [Numero], [Data Ord] AS [Data], [Ragione Sociale], [Indirizzo], [Stato], [TotImp] AS [IMPORTO TOTALE], [TotIva] AS [IMPORTO IVA] FROM [Ordini] WHERE [Indirizzo] LIKE '%$input%' OR [Ragione Sociale] LIKE '%$input%' OR [Id Ord] LIKE '$id' OR [Tipo Ord] LIKE '$tipo' OR [Data Ord] BETWEEN #$data1# AND #$data2#".$optionsSQL;
}
*/

//Try the variable output
    echo '<br>';
    var_dump($sql);
    echo '<br>';
    var_dump($input);
    echo '<br>';
    var_dump($id);
    echo '<br>';
    var_dump($tipo);
    echo '<br>';
    var_dump($numero1);
    echo '<br>';
    var_dump($numero2);
    echo '<br>';
    var_dump($data1);
    echo '<br>';
    var_dump($data2);
    echo '<br>';
    var_dump($option);
    echo '<br>';

$sql .= $whereSql; 

//Try the final sql output
    echo '<br>';
    var_dump($sql);

$rs = $con->execute($sql);

if($rs === false) {
  trigger_error('Wrong SQL: ' . $sql . ' Error: ' . $con->ErrorMsg(), E_USER_ERROR);
} else {
  $rows_returned = $rs->RecordCount();
}

$numFields = $rs->Fields->count;
 
// Print HTML
echo '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
        "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" 
     content="text/html; charset=utf-8" />
<title>Gestione degli '.DBTBL.'</title>
<link rel="stylesheet" href="//cdn.datatables.net/1.10.12/css/jquery.dataTables.min.csss">
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.1.0/jquery.min.js"></script>
<script src="//cdn.datatables.net/1.10.12/js/jquery.dataTables.min.js"></script>';
echo("<script>
$(document).ready(function(){
    $('#mydatatable').DataTable();
});
</script>");
echo '</head><body>
<h1>GESTIONE '.DBTBL.'</h1>';
// Elenca records -----
//echo ("<div class='table-responsive'>");
echo '<table class="display" id="mydatatable" cellspacing="0" summary="Prova dati con MS Access">';
echo '<caption>Tabella '.DBTBL.'</caption>';
echo("<thead><tr>");
for ($i=0;$i<$numFields;$i++){
    echo("<th>");
    echo $rs->Fields($i)->name;
    echo("</th>");
}
echo("</tr></thead>");

echo("<tfoot><tr>");
for ($i=0;$i<$numFields;$i++){
    echo("<th>");
    echo $rs->Fields($i)->name;
    echo("</th>");
}
echo("</tr></tfoot>");

echo("<tbody>");
while (!$rs->EOF)
{
    echo("<tr>");
    for ($i=0;$i<$numFields;$i++){   
      if (LINKPK && $i==PKCOL){
        echo("<td>");
        echo "<a href='?id=".$rs->Fields($i)->value
              ."'>".$rs->Fields($i)->value."</a>";
        //echo $rs->Fields($i)->value;
        echo("</td>");
        }
        else{
          echo("<td>");
           echo $rs->Fields($i)->value;
          echo("</td>");
      }
    }
    echo("</tr>\n");    
    $rs->MoveNext();
}
echo("</tbody>");
echo("</table>");
echo("</div>");

echo ("<p>[ <a href='?ins=1'>Inserimento nuovo record</a> ]</p>");

// Modifica record -----
if (!empty($_GET['id'])){
  $id = intval($_GET['id']);
  $rs = $con->execute("SELECT [Id Ord] AS [ID], [Tipo Ord] AS [Tipo], [N Ord] AS [Numero], [Data Ord] AS [Data], [Ragione Sociale], [Indirizzo], [Stato], [TotImp] AS [IMPORTO TOTALE], [TotIva] AS [IMPORTO IVA] FROM".DBTBL." WHERE ".PKNAME."=".$id);
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
}
echo '</body>';
echo '</html>';
$rs->Close();
$con->Close();
?>

