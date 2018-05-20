<?php
use Classes\Models\TaskFactory;
use Classes\Utils\GDUtil;
use Classes\Admin\Admin;

class Handler{
 
    # @http GET /tasks/
    public function getTasks(){
        $taskFactory = new TaskFactory();
        return $taskFactory
            ->getTasks($_GET['offset'], $_GET['step'], $_GET['column'], $_GET['dir'])
            ->toArray();
    }

    # @http POST /tasks/
    public function addTask(){

        $taskFactory = new TaskFactory();
        $image = $_POST['image'];        
        
        $image = GDUtil::convertToBlob($image);
        $image = GDUtil::minimize($image);    

        $taskFactory->create($_POST['username'], $_POST['email'], $_POST['content'], $image);        
    }

    # @http GET /image/
    public function getImage(){
        $id = $_GET['task'];
        $image = (new TaskFactory)->getImage($id);
    
        $data = GDUtil::convertToBlob($image);
        header("Content-Type: ".$data['type']);
        echo $data['data'];
    }

    # @http POST /login/
    public function login(){
        $res = Admin::login($_POST['login'], $_POST['password']);
        if($res){
            return ["status"=>"ok"];
        }
        return ["status"=>"error","message"=>"Incorrect credentials!"];
    }

    # @http GET /logout/
    public function logout(){
        Admin::logout();
        header("Location: /");
    }

    # @http POST /task/
    public function modifyTask(){
        $taskFactory = new TaskFactory();
        $id = $_POST['id']*1;
        $content = $_POST['content'];
        $status = $_POST['status'];

        $task = $taskFactory->update(
            $taskFactory
                ->getById($id)
                ->setStatus($status)
                ->setContent($content)
        );
        return [];
    }
}