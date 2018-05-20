<?php
namespace Classes\Models;

class TaskList{

    private $items;
    private $offset, $step, $total;

    public function __construct($offset, $step, $total){
        $this->offset = $offset;
        $this->step = $step;
        $this->total = $total;
        $this->items = [];
    }  

    public function add(Task $task){
        $this->items[] = $task;
    }

    public function toArray(){
        $items = array_map(function(Task $task){
            return $task->toArray();
        },$this->items);

        return [
            "offset" => $this->offset
            ,"step" => $this->step
            ,"count" => $this->total
            ,"tasks" => $items
        ];
    }

}