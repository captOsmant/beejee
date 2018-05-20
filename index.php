<?php
    session_start();
    require_once $_SERVER['DOCUMENT_ROOT']."/Classes/autoload.php";
    use Classes\Models\TaskFactory;
    use Classes\Admin\Admin;

    $isAdmin = Admin::isCurrentlyAdmin();

    

?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>BeeJee test task</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
    <script src="https://code.jquery.com/jquery-3.2.1.slim.min.js" integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>
  
    <link rel="stylesheet" type="text/css" media="screen" href="/css/main.css" />

    
</head>
<body>
    <div id='tasklist' class='container'>
        <div class='tools'>
            <select id='order'>
                <option value='<?=TaskFactory::ORDER_ID?>'>By date</option>
                <option value='<?=TaskFactory::ORDER_USERNAME?>'>By username</option>
                <option value='<?=TaskFactory::ORDER_EMAIL?>'>By email</option>
                <option value='<?=TaskFactory::ORDER_STATUS?>'>By status</option>
            </select>
            <select id='order-dir'>
                <option value='<?=TaskFactory::ORDER_DESC?>'>Z-A</option>
                <option value='<?=TaskFactory::ORDER_ASC?>'>A-Z</option>
            
            </select>

            <div id='admin' style='float: right'>
            <?if (!$isAdmin){
               echo "
                 <button id='loginBtn' 
                    class='btn btn-primary' 
                    data-toggle='modal'
                    data-target='#logInModal'>
                    Log In
                </button>
                ";
            } else {
                echo "<span>Hello, Admin!</span> <a href='/api/logout/'>Logout</a>";
            }
            ?>
            
            </div>
        </div>
        <div id='container' class='row'></div>
        <div id='pagination' class='btn-group'></div>
    </div>

    <div id='create-task'>
        <form class='container' id='create-form'>

            <div class="input-group mb-3">
                <div class="input-group-prepend">
                    <span class="input-group-text" >Username</span>
                </div>
                
                <input type='text' class="form-control" id='username' name="username" required/>
            </div>

            <div class="input-group mb-3">
                <div class="input-group-prepend">
                    <span class="input-group-text" >Email</span>
                </div>
                
                <input type='email' class="form-control" id='email' name="email" required/>
            </div>

            <div class="input-group">
                <div class="input-group-prepend">
                    <span class="input-group-text">Task contents</span>
                </div>
                <textarea class="form-control" aria-label="Task contents" required id="content"></textarea>
            </div>

            <label class='container'>
                <span>Image</span>
                <input type='file' accept="image/*" required id="image" class='form-control-file'/>
            </label>
            <div class='btn-group'>
                <input type='submit' class='btn btn-primary' value='Create'>
                <input type='button' id='open-preview' value='Preview' class=' btn btn-info'>
            </div>
            
            
        </form>

        <div id='preview'>
            <input type='button' id='close-preview' value='Close' class='btn btn-close'>
            <div class='item' data-id='${task.id}'>
                <figure class='card' style="width: 320px">
                    <img class='card-img-top'  id='p-image'/>
                    <figcaption class='card-body'>
                        <h3 id='p-username'></h3>
                        <h6 id='p-email'></h4>
                        <div id='p-content'></div>
                        <p class='status badge badge-warning' role='badge' data-status='0'>
                            Pending
                        </p>
                    </figcaption>
                </figure>
            </div>
        </div>
    </div>

    <div id='logInModal' class='modal fade' role='dialog'>
        <div class='modal-dialog' role='document'>
            <div class='modal-content'>
                <div class='modal-header'>
                    <h5 class="modal-title">Log In</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form action='.' id='login-form' method='POST'>
                        <div class='input-group'>
                            <div class="input-group-prepend">
                                <span class="input-group-text">Login</span>
                            </div>
                            <input type='text' name='login' id='login' class='form-control'/>
                        </div>
                        <div class='input-group'>
                            <div class="input-group-prepend">
                                <span class="input-group-text">Password</span>
                            </div>
                            <input type='password' name='password' id='password' class='form-control'/>
                        </div>
                        <input type='submit' value='Log In' class='btn btn-primary'/>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class='modal' id='editTask'>
        <div class='modal-dialog'>
            
            <div class='modal-content'>
                <div class='modal-header'>
                    <h3 class='modal-title'> Edit task</h3>
                </div>

                <div class='modal-body'>
                    <form id='editTask'>
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text">Task contents</span>
                            </div>
                            <textarea 
                                class="form-control" 
                                aria-label="Task contents" 
                                required 
                                id="content-edit"></textarea>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" value="" id="isDone">
                            <label class="form-check-label" for="isDone">
                                Is finished
                            </label>
                        </div>
                        <input type='submit' value='Save' class='btn btn-primary'/>
                    </form>
                </div>
                
            </div>
        </div>
    </div>

    <script src="js/main.js"></script>
    <script>Tasks.isAdmin = <?= $isAdmin ? "true" : "false" ?></script>
</body>
</html>