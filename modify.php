<?php
require_once("config.php");
//Gestione delle richieste POST
$cn = new COM("ADODB.Connection");
$cnStr = "DRIVER={Microsoft Access Driver (*.mdb)}; DBQ=".
            realpath("./DinamicoWeb.mdb").";";
$cn->open($cnStr);

if ($_POST['azione']=="cancella"){
  $query = "DELETE from ".DBTBL." WHERE ".PKNAME." = ".$_POST[PKNAME];
}
elseif ($_POST['azione']=="inserisci"){
  $query = "INSERT INTO ".DBTBL." ";
  $fieldlist = "(";
  $valuelist = "(";
  foreach($_POST as $key => $val) {
    if ($key<>PKNAME && $key<>'azione'){
      if (!empty($val)){
        $fieldlist .= $key.", ";
        $val = str_replace("'","''",stripslashes($val));
        $valuelist .= "'".$val."', ";
      }
    } 
  }
  $fieldlist = substr($fieldlist, 0, -2);
  $valuelist = substr($valuelist, 0, -2);
  $fieldlist .= ") VALUES "; 
  $valuelist .= ")"; 
  $query .= $fieldlist.$valuelist;
}
else{
  $query = "UPDATE ".DBTBL." SET ";
  foreach($_POST as $key => $val) {
    if ($key<>PKNAME && $key<>'azione'){
      if (!empty($val)){
        $val = str_replace("'","''",stripslashes($val));
        $query .= $key."='".$val."', ";
      }
      else{
        $query .= $key."=NULL, ";
      }
    } 
  }
  $query = substr($query, 0, -2);
  $query .= " WHERE ".PKNAME." = ".$_POST[PKNAME];
}
$rs = $cn->execute($query);
header ("Location: ./test.php");
?>
