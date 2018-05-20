<?php
session_start();
require_once $_SERVER['DOCUMENT_ROOT']."/Classes/autoload.php";
require "Handler.php";
header("Content-Type: application/json");
    
$str = explode('?',$_SERVER['REQUEST_URI']);
parse_str($str[1], $res);
$_GET = array_merge($_GET,$res);

$method = ($_SERVER['REQUEST_METHOD']);

$uri = "/".$_GET['api_uri']."/";

$class = file_get_contents("Handler.php");
$lines = [];
preg_match_all("/@http ([\/a-zA-Z\-\_\s0-9]+)\n[a-z\s]* function ([a-zA-Z0-9\_\-]+)/",$class,$lines);

$handlers = [];
foreach($lines[1] as $i=>$m){
    $handlers[trim($m)] = "Handler$".trim($lines[2][$i]);
}

if($method=="PUT"){
    parse_str(file_get_contents("php://input"),$_PUT);
}


$http_str = $method." ".$uri;
foreach($handlers as $i=>$h){
    if($i==$http_str){
        $classname = explode('$',$h);
        $class = $classname[0];
        $method = $classname[1];
        $obj = new $class();
        $data = $obj->$method();
        echo json_encode($data);
    }
}


