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

if (empty($input)) {
    $sql="SELECT [Id Ord] AS [ID], [Tipo Ord] AS [Tipo], [N Ord] AS [Numero], [Data Ord] AS [Data], [Ragione Sociale], [Indirizzo], [TotImp] AS [IMPORTO TOTALE], [TotIva] AS [IMPORTO IVA] FROM [Ordini] WHERE [Id Ord] LIKE '$id' OR [Tipo Ord] LIKE '$tipo' OR [Data Ord] BETWEEN #$data1# AND #$data2#";
} else {
    $sql="SELECT [Id Ord] AS [ID], [Tipo Ord] AS [Tipo], [N Ord] AS [Numero], [Data Ord] AS [Data], [Ragione Sociale], [Indirizzo], [TotImp] AS [IMPORTO TOTALE], [TotIva] AS [IMPORTO IVA] FROM [Ordini] WHERE [Indirizzo] LIKE '%$input%' OR [Ragione Sociale] LIKE '%$input%' OR [Id Ord] LIKE '$id' OR [Tipo Ord] LIKE '$tipo' OR [Data Ord] BETWEEN #$data1# AND #$data2#";
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
//echo ("<div class='table-responsive'>");
echo ("<table class='datatable table tabella_reponsive ui-responsive' summary='Prova dati con MS Access'>");
echo("<caption>Tabella ".DBTBL."</caption>\n");
echo("<thead><tr>\n");
for ($i=0;$i<$numFields;$i++){
    echo("<th scope='col'>");
    echo $rs->Fields($i)->name;
    echo("</th>\n");
}
echo("</tr></thead>\n");
echo("<tbody>");

$alt = false;
while (!$rs->EOF)
{
    echo("<tr>");
    for ($i=0;$i<$numFields;$i++){
      $altClass = $alt ? " class='alt'" : "";
      if (LINKPK && $i==PKCOL){
        echo "<td".$altClass."><a href='?id=".$rs->Fields($i)->value
              ."'>".$rs->Fields($i)->value."</a></td>\n";
      }
      else{
        echo "<td".$altClass.">".$rs->Fields($i)->value."</td>\n";
      }
    }
    echo("</tr>\n");    
    $rs->MoveNext();
    $alt = !$alt;
}
echo("</tbody>");
echo("</table>\n");
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