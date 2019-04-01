<?php
include_once 'Dao.php';

class PDOData
{

    // gettable
    public function gettable($mywhere=NULL) {
        try {
            $dao = new Dao();
            $conn = $dao->openConnection();
            $sql = "SELECT * FROM INFORMATION_SCHEMA.COLUMNS";
            if (isset($mywhere) and strlen($mywhere) > 0) { $sql .= " where {$mywhere}"; }
            $resource = $conn->prepare($sql);
            $resource->execute();
            $dao->closeConnection();
        } catch (PDOException $e) {
            echo "There is some problem in gettable connection: " . $e->getMessage();
        }
        if (! empty($resource)) {
            return $resource;
        }
    }    /* Fetch All */

    // getschemalist
    public function getschemalist($mywhere=NULL) {
        try {
            $dao = new Dao();
            $conn = $dao->openConnection();
            $sql = "select distinct table_schema FROM INFORMATION_SCHEMA.COLUMNS where table_schema not in ('mysql','performance_schema','phpmyadmin') order by table_schema";
            if (isset($mywhere) and strlen($mywhere) > 0) { $sql .= " where {$mywhere}"; }
            $resource = $conn->prepare($sql);
            $resource->execute();
            $dao->closeConnection();
        } catch (PDOException $e) {
            echo "There is some problem in gettableschema connection: " . $e->getMessage();
        }
        if (! empty($resource)) {
            return $resource;
        }
    }    /* Fetch All */

    // gettablelist
    public function gettablelist($mywhere=NULL) {
        try {
            $dao = new Dao();
            $conn = $dao->openConnection();
            $sql = "	select distinct table_name FROM INFORMATION_SCHEMA.COLUMNS where table_schema not in ('mysql','performance_schema','phpmyadmin') order by table_name";
            if (isset($mywhere) and strlen($mywhere) > 0) { $sql .= " where {$mywhere}"; }
            $resource = $conn->prepare($sql);
            $resource->execute();
            $dao->closeConnection();
        } catch (PDOException $e) {
            echo "There is some problem in gettablelist connection: " . $e->getMessage();
        }
        if (! empty($resource)) {
            return $resource;
        }
    }    /* Fetch All */

}

?>
