<?php
namespace Classes\Models;

use Classes\Utils\Db;
use Classes\Models\Task;

class TaskFactory{

    private $db;
    private const TABLE_NAME = "beejee_tasks";
    const ORDER_ID = 'id';
    const ORDER_USERNAME = 'uname';
    const ORDER_EMAIL = 'email';
    const ORDER_STATUS = 'status';

    const ORDER_ASC = 0x32;
    const ORDER_DESC = 0x12;

    public function __construct(){
        $this->db = Db::getInstance();
    }

    public function getTasks(int $offset, int $step, $orderBy = self::ORDER_ID, $orderDir = self::ORDER_DESC){
        switch($orderBy){
            case self::ORDER_USERNAME:
                $order = "username";
                break;
            case self::ORDER_EMAIL:
                $order = "email";
                break;
            case self::ORDER_STATUS:
                $order = "status";
                break;
            case self::ORDER_ID:
            default:
                $order = "id";
        }

        $orderDirection = $orderDir == self::ORDER_ASC ? "ASC" : "DESC";
        $raw = $this->db->getArray("SELECT * FROM ".self::TABLE_NAME."
            ORDER BY $order $orderDirection 
            LIMIT $offset, $step ");

        $c = $this->db->getArray("SELECT COUNT(id) AS count FROM ".self::TABLE_NAME);
        $count = $c[0]['count']*1;
        
        $list = new TaskList($offset, $step, $count);
        foreach($raw as $line){
            $list->add(new Task(
                $line['id']
                , $line['username']
                , $line['email']
                , $line['content']
                // , $line['image']
                , $line['status']
            ));
        }
        return $list;        
    }

    public function getById(int $id){
        $raw = $this->db->getArray("SELECT * FROM ".self::TABLE_NAME." WHERE id=$id");
        if(!count($raw)){
            return null;
        }
        $line = $raw[0];
        return new Task(
            $line['id']
            , $line['username']
            , $line['email']
            , $line['content']
            , $line['status']
        );
    }

    
    public function getImage(int $id){
        $db = Db::getInstance();
        $raw = $db->getArray("SELECT image FROM ".TaskFactory::TABLE_NAME." WHERE id=$id");
        $line = $raw[0];
        return $line['image'];
    }


    public function update(Task $task){
        $this->db->query("UPDATE ".self::TABLE_NAME." SET 
            content='{$task->getContent()}'
            , status='{$task->getStatus()}'
        WHERE id={$task->getId()}");
    }

    public function create($username, $email, $content, $image){
        $image=  $this->db->escape($image);
        $this->db->query("INSERT INTO ".self::TABLE_NAME." 
            (username, email, content, `image`, `status`) 
            VALUES ('$username','$email','$content','$image',".Task::STATUS_UNDONE.")");
        
    }

}