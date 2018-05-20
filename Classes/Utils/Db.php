<?php
namespace Classes\Utils;

class Db{



    private const DB_HOST = "localhost";
    private const DB_USERNAME = "root";
    private const DB_PASSWORD = "";
    private const DB_NAME = "beejee";

    private function __construct(){
        $this->link = mysqli_connect(self::DB_HOST, self::DB_USERNAME, self::DB_PASSWORD,self::DB_NAME);
        
    }

    private static $link;

    # Singletone pattern
    public static function getInstance(){
        if(self::$link === null){
            self::$link = new DB();
        }
        return self::$link;
    }

    public function query($query, $params = null){
        $res = $this->link->query($query);
        
        if($this->link->error){
            echo $query;
            throw new \Exception($this->link->error);
        }
        return $res;
    }

    public function escape(string $str){
        return $this->link->real_escape_string($str);
    }

    public function getArray($query, $params = null){
        $res = $this->query($query);

        $r = [];
        while($l = $res->fetch_assoc()){
            $r[] = $l;
        }
        return $r;

    }

    public function __destruct(){
        $this->link->close();
    }
}