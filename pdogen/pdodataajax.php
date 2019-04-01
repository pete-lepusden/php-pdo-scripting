<?php
include_once 'Dao.php';

class pdodataajax
{
  public function tablelist($schema)
  {
      try {
          $dao = new Dao();
          $conn = $dao->openConnection();
          $sql = "select distinct table_name FROM INFORMATION_SCHEMA.COLUMNS where table_schema = '".$schema."' order by table_name";
          $resource = $conn->prepare($sql);
          $resource->execute();

          // Generate skills data array
          $myselect = '<option value="">--Select Table Name--</option>';
          while($row = $resource->fetch(PDO::FETCH_ASSOC)){
						 $myselect .= '<option value="'.$row['table_name'].'">'.$row['table_name'].'</option>';
        //    $data["table_name"] = $row["table_name"];
          }
          $dao->closeConnection();
          return $myselect;
      } catch (PDOException $e) {
          echo "There is some problem in connection: " . $e->getMessage();
      }
  }
}

$pdodataajax = new pdodataajax();

// Get search term
$themethod = $_GET['method'];
$thename = $_GET['schema'];
switch ($themethod) {
  case 'tablelist':
    $nf = $pdodataajax->tablelist($thename);
    echo json_encode($nf);
    break;
  default:
    echo json_encode('method not found');
}
?>
