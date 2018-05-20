<?php

namespace Classes\Admin;

class Admin{

    private const LOGIN = "admin";
    private const PASSWORD = "123";
    private const SSKEY = "ssid"; 

    public static function login($login, $password){
        if($login == self::LOGIN){
            if($password == self::PASSWORD){
                self::auth();
                return true;
            }
        }
        return false;
    }

    public static function auth(){
        $_SESSION[self::SSKEY] = true;
    }

    public static function isCurrentlyAdmin(){
        return $_SESSION[self::SSKEY];
    }

    public function logout(){

        unset($_SESSION[self::SSKEY]);

    }
}