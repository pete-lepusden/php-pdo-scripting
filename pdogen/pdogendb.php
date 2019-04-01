<?php

include_once 'pdogendatadao.php';

$dbclass = new PDOData();
if((!empty($_POST)) && (isset($_POST['code']))){
$mywhere = "table_name = '".$_POST['table_name']."' AND table_schema = '".$_POST['schema_name']."' AND column_key = 'PRI'";
$stmtbg = $dbclass->gettable($mywhere);
$bg = $stmtbg->fetchAll(PDO::FETCH_ASSOC);
$id_column = $bg[0]["COLUMN_NAME"];
$mywhere = "table_name = '".$_POST['table_name']."' AND table_schema = '".$_POST['schema_name']."'";
//$mywhere = "table_name = 'user_security' AND table_schema = 'burials'";
$stmtbg = $dbclass->gettable($mywhere);
$bg = $stmtbg->fetchAll(PDO::FETCH_ASSOC);
//var_dump($bg);

//Script to turn list of MySQL columns into functions
	//Set Table name & ID variables

	$table_name = $bg[0]["TABLE_NAME"];
	$schema_name = $bg[0]["TABLE_SCHEMA"];
	$uc_table_name = ucwords($table_name);
	//Create variable strings for the DB calls

	$var_list = $col_list = $param_list = $update_list = $bind_list = $set_list ="";
	$array_list = "array(";
	$first = true;
	//Create static string variables for text with reserved characters
	$ds_str = "$"; //Dollar sign for use in the result

	foreach ($bg as $k => $v) {

		if($first){
			$var_list .= "$" . $bg[$k]["COLUMN_NAME"];
			$col_list .= "`" . $bg[$k]["COLUMN_NAME"] . "`";
			$param_list .= ":" . $bg[$k]["COLUMN_NAME"];
			$update_list .= "`" . $bg[$k]["COLUMN_NAME"] . "`=:" . $bg[$k]["COLUMN_NAME"];
			$first = false;
		}else{
			$var_list .= ",$" . $bg[$k]["COLUMN_NAME"];
			$col_list .= ",`" . $bg[$k]["COLUMN_NAME"] . "`";
			$param_list .= ",:" . $bg[$k]["COLUMN_NAME"];
			$update_list .= ",`" . $bg[$k]["COLUMN_NAME"] . "`=:" . $bg[$k]["COLUMN_NAME"];
		}
		$postlower = strtolower($bg[$k]["COLUMN_NAME"]);
		$bind_list .= "\t\t" . '$stmt->bindparam(":' . $bg[$k]["COLUMN_NAME"] .'", $' . $bg[$k]["COLUMN_NAME"] . '); ' . "\n";
		$array_list .= "':".$bg[$k]["COLUMN_NAME"]."' => {$ds_str}_POST['{$postlower}_1'],";
		$set_list .= $bg[$k]["COLUMN_NAME"]." = :".$bg[$k]["COLUMN_NAME"].",";
	}
	$array_list = substr($array_list, 0, -1).")";
	$set_list = substr($set_list,0,-1);

//-------------------------------------------------------------------------------------Display Insert Function
$html = <<<EOD
<h3>Write Function for Table {$table_name}</h3>
<!--<pre>-->
public function wr{$table_name} () {<br />
	try  {<br />
		{$ds_str}dao = new Dao();<br />
		{$ds_str}conn = {$ds_str}dao->openConnection();<br />
		{$ds_str}sql = "SELECT {$id_column} FROM {$schema_name}.{$table_name} where {$id_column} = '{{$ds_str}_POST['{$id_column}_1']}'";<br />
		{$ds_str}resource = {$ds_str}conn->prepare({$ds_str}sql);<br />
		{$ds_str}resource->execute();<br />
		{$ds_str}cnt = {$ds_str}resource->rowCount();<br />

		if ({$ds_str}cnt == 0){<br />
			{$ds_str}sql = "INSERT INTO {$table_name}({$col_list}) VALUES({$param_list})";<br />

			{$ds_str}resource = {$ds_str}conn->prepare({$ds_str}sql);<br />

			{$ds_str}resource->execute({$array_list});<br />
		} else {<br />
			{$ds_str}sql = "UPDATE {$schema_name}.{$table_name} set {$set_list} WHERE {$id_column} = :{$id_column}";<br />
			{$ds_str}resource->execute({$array_list});<br />
		}<br />

		{$ds_str}dao->closeConnection();<br />
	} catch (PDOException {$ds_str}e) {
			echo "There is some problem in wr{$table_name} connection: " . {$ds_str}e->getMessage();
	}<br />
	if (!empty({$ds_str}resource)) {
			return "Record Saved";
	}<br />
}
<!--<//pre>-->
EOD;

//-------------------------------------------------------------------------------------Display Select Function
$html .= <<<EOD
<h3>Select Function for Table {$table_name}</h3>
<!--<pre>-->
public function get{$uc_table_name}({$ds_str}mywhere=NULL) {<br />
   try {<br />
			{$ds_str}dao = new Dao();<br />
			{$ds_str}conn = {$ds_str}dao->openConnection();<br />
			{$ds_str}sql = "SELECT {$col_list} FROM {$schema_name}.{$table_name}");<br />
			if (isset({$ds_str}mywhere) and strlen({$ds_str}mywhere) > 0) { {$ds_str}sql .= " where {{$ds_str}mywhere}"; }<br />
			{$ds_str}resource = {$ds_str}conn->prepare({$ds_str}sql);<br />
			{$ds_str}resource->execute();<br />
			{$ds_str}dao->closeConnection();<br />
		} catch (PDOException {$ds_str}e) { echo "There is some problem in get{$table_name} connection: " . {$ds_str}e->getMessage(); }<br />
			if (! empty({$ds_str}resource)) { return {$ds_str}resource; }<br />
   }
<!--<//pre>-->
EOD;

//-------------------------------------------------------------------------------------Display Delete Function
$html .= <<<EOD
<h3>Delete Function for Table {$table_name}</h3>
<h4>Warning - All data will be deleted if the mywhere filter is not passed</h4>
<!--<pre>-->
public function get{$uc_table_name}({$ds_str}mywhere=NULL) {<br />
   try {<br />
			{$ds_str}dao = new Dao();<br />
			{$ds_str}conn = {$ds_str}dao->openConnection();<br />
			{$ds_str}sql = "DELETE FROM {$schema_name}.{$table_name}");<br />
			if (isset({$ds_str}mywhere) and strlen({$ds_str}mywhere) > 0) { {$ds_str}sql .= " where {{$ds_str}mywhere}"; }<br />
			{$ds_str}resource = {$ds_str}conn->prepare({$ds_str}sql);<br />
			{$ds_str}resource->execute();<br />
			{$ds_str}dao->closeConnection();<br />
		} catch (PDOException {$ds_str}e) { echo "There is some problem in del{$table_name} connection: " . {$ds_str}e->getMessage(); }<br />
			if (! empty({$ds_str}resource)) { return {$ds_str}resource; }<br />
   }
<!--<//pre>-->
EOD;
echo $html;
} else {

	$stmtbg = $dbclass->getschemalist();
	$slist = $stmtbg->fetchAll(PDO::FETCH_ASSOC);

	?>

	<!DOCTYPE html>
	<html lang="en">
	<head>
	  <!-- Basic Page Needs
	  –––––––––––––––––––––––––––––––––––––––––––––––––– -->
	  <meta charset="utf-8">
	  <title>PHP MYSQL PDO Scripter</title>
	  <meta name="description" content="">
		<meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
	  <meta name="author" content="Michelle Woodruff">
		<meta name="description" content="https://mwoodruff.net/articles/automation/time-saving-php-pdo-code-generator/">
		<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
	  </head>
	  <body>
			<div class="container-fluid">
			   <h2 align="center">PHP PDO Script Generator</h2>
				 <h4 align="center">A Schema (database) and table must exist in the database</h4>
				 <h4 align="center">Current sql formatting is for MySQL/MariaDB</h4>
		   <br />
		   <div class="panel panel-default">
		    <div class="panel-heading">Select Schema and Table</div>
		    <div class="panel-body">
					<form action="" method="POST">
						<div class="form-row">
						<div class="form-group col-md-6">
			       <label for="schema_name">Schema Name</label>

						 <select class="form-control " name="schema_name" id="schema_name" title="schema_name" onchange="loadtablename(this)">
						 <option value="">--Select Schema Name--</option> -->
						 <?php
						 foreach ($slist as $k => $v) {
							 echo '<option value="'.$slist[$k][table_schema].'">'.$slist[$k]["table_schema"].'</option>';
						 }
						 ?>
						 <select>

					</div>
					<div class="form-group col-md-6">
					 <label for="table_name">Table Name</label>
					 <select class="form-control" name="table_name" id="table_name" title="table_name">
						 <option value="">--Select Schema First--</option>
					 <select>
					</div>
				</div>
						<input class="btn btn-primary" name="code" type="submit" value="Create PHP Functions"/>
					</form>
				</div>
			</div>
		</div>
		<script src="https://code.jquery.com/jquery-3.3.1.min.js"
		  integrity="sha256-FgpCb/KJQlLNfOu91ta32o/NMZxltwRo8QtmkMRdAu8="
		  crossorigin="anonymous"></script>
		<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js" integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous"></script>
		<script type="text/javascript">

			 function loadtablename(elm)
			 {
				 var schema = elm.value;
				 $.ajax({
					 url: "pdodataajax.php?method=tablelist",
					 dataType: "json",
						 type: "get",
						 data: {schema: schema},
						 success: function ( response ) {
							 $("#table_name").html(response);
							 $("#table_name").focus();
						 },
						 error: function( response) { alert(response); }
				 } );

			 }

		</script>

		</body>

	  <?php

}
?>
