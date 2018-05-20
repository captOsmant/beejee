<?php
namespace Classes\Models;

class Task{

    const STATUS_UNDONE = 0;
    const STATUS_DONE = 1;
    
    private $username, $email, $content,  $status;
    public function __construct($id, $username, $email, $content, $isDone){
        $this->id = $id;
        $this->username = $username;
        $this->email = $email;
        $this->content = $content;
        // $this->image = $image;
        $this->status = $isDone;
    }

    public function getId(){
        return $this->id;
    }

    public function getStatus(){
        return $this->status;
    }

    public function getUsername(){
        return $this->username;
    }

    public function getEmail(){
        return $this->email;
    }

    public function getContent(){
        return $this->content;
    }


    public function setStatus($val){
        $this->status = $val;
        return $this;
    }

    public function setContent($val){
        $this->content = htmlspecialchars($val);
        return $this;
    }

    public function toArray(){
        return [
            "id"=>$this->id
            ,"username" => $this->username
            ,"email" => $this->email
            ,"content" => $this->content
           
            ,"isDone" => $this->status
        ];
    }
}