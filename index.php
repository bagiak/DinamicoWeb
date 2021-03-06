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

$sql="SELECT [Id Ord] AS [ID], [Tipo Ord] AS [Tipo], [N Ord] AS [Numero], [Data Ord] AS [Data], [Ragione Sociale] AS [Cliente], [Indirizzo], [Stato], [TotImp] AS [TOTALE], [TotIva] AS [IVA] FROM [Ordini] WHERE";
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
/*
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

*/

$sql .= $whereSql; 

//Try the final sql output
//    echo '<br>';
//    var_dump($sql);

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
<link rel="stylesheet" href="css/styles.css">
<link href="css/bootstrap.css" rel="stylesheet">
<link href="external/responsive/css/responsive.bootstrap.css" rel="stylesheet">
<link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.0.0-alpha.2/css/bootstrap.css">
<link rel="stylesheet" href="https://cdn.datatables.net/1.10.12/css/dataTables.bootstrap4.min.css">
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.1.0/jquery.min.js"></script>
<script src="external/bootstrap/js/bootstrap.js"></script>
<script src="external/bootstrap/js/npm.js"></script>
<script src="//code.jquery.com/jquery-1.12.3.js"></script>
<script src="https://cdn.datatables.net/1.10.12/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.10.12/js/dataTables.bootstrap4.min.js"></script>
<script src="external/responsive/js/dataTables.responsive.js"></script>
<script src="external/responsive/js/responsive.bootstrap.js"></script>
<script src="external/responsive/js/responsive.jqueryui.js"></script>
<style type="text/css">
      body {
        padding-top: 0px;
        margin-top: 0px;
        padding-bottom: 60px;
        font-size: 14px;
      }

      /* Custom container */
      .container {
        margin-top: 0;
        width 70%;
      }
      .container > hr {
        margin: 0;
      }

      /* Main marketing message and sign up button */
      .jumbotron {
        margin: 60px 0;
        text-align: center;
        background-color: #ffffff;
        border-style: solid;
        border-color: #3d3d3d #3d3d3d;
        border-width: 2px;

      }
      .jumbotron h1 {
        font-size: 100px;
        line-height: 1;
      }
      .jumbotron .lead {
        font-size: 24px;
        line-height: 1.25;
      }
      .jumbotron .btn {
        font-size: 21px;
        padding: 14px 24px;
      }

      /* Supporting marketing content */
      .marketing {
        margin: 60px 0;
      }
      .marketing p + h4 {
        margin-top: 28px;
      }
    </style>
';
echo("<script>
$(document).ready(function(){
    $('#mydatatable').DataTable();
});
</script>");
echo '</head><body>
<div class="container">
';
// Elenca records -----
echo '<div class="jumbotron">
        <h2>Ricerca ordini</h2>';
echo '<table class="table table-striped table-bordered" id="mydatatable" cellspacing="0" width="100%">';
echo '<caption>Tabella degli '.DBTBL.'</caption>';
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
echo '</div>
      <hr>
      <div class="footer">
        <p>&copy; Dinamico Web 2016</p>
      </div>
    </div>
';
echo("</div>");
echo("</container>");
echo '</body>';
echo '</html>';
$rs->Close();
$con->Close();
?>

