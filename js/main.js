Tasks = {

    start: async function(){
        Tasks.List.init();        
        Tasks.Form.init()
        Tasks.Login.init();
        Tasks.Edit.init();
        
    }

    ,Edit:{

        init(){
            $("#editTask").submit(function(e){
                e.preventDefault();
                Tasks.Edit.save();
            })
        }
        ,currentTask: null
        ,open(id){
            let task = Tasks.Data.getById(id);
            this.currentTask = task;
            $("#content-edit").val(task.content);
            $("#isDone")[0].checked = task.isDone*1 == 1;
        }
        ,save: async function(){
            let [content, isDone] = [$("#content-edit").val(), $("#isDone")[0].checked ? 1 : 0];
            await Tasks.Data.editTask(this.currentTask.id, content, isDone);
            Tasks.List.openPage(Tasks.List.currentPage);
            $("#editTask").modal("hide")
        }
    }

    ,Login: {
        init(){
            $("#login-form").submit(function(e){
                e.preventDefault();
                Tasks.Login.onLogin();
            })
        }

        ,onLogin: async function(){
            let [login, password] = [$("#login").val(), $("#password").val()];
            let result = await Tasks.Data.login(login, password);
            if(result.status == "ok"){
                return location.reload();
            }
            alert(result.message);
        }
    }

    ,List:{
        TASKS_PER_PAGE: 3
        ,currentOffset: 0
        ,length : 0
        ,currentPage: 0

        ,init: function(){
            this.initOrder();
            this.reload();
        }

        ,initOrder(){
            $("#tasklist select").on("change",function(){
                Tasks.List.reload();
            })
        }

        ,reload(){
            this.openPage(0);
        }

        ,openPage: async function(num){

            let data = await Tasks.Data.getTasks(
                num*this.TASKS_PER_PAGE
                , (num+1)*this.TASKS_PER_PAGE
                ,this.getOrderColumn()
                ,this.getOrderDir());
            this.currentPage = num;
            
            this.onTasksReceived(data);

        }

        ,getOrderColumn(){
            return $("#order").val()
        }
        ,getOrderDir(){
             return $("#order-dir").val()
        }
        ,onTasksReceived(data){
            
            this.length = data.count * 1    ;
            this.currentOffset = data.offset * 1;
            this.buildPagination();
        
            this.display(data.tasks);

        
        }
        ,buildPagination(){
            $("#pagination").html(
                (new Array(Math.ceil(Tasks.List.length / Tasks.List.TASKS_PER_PAGE)))
                .fill(0)
                .map((e, i)=>i)                
                .reduce((p, c)=>{
                    return p + Tasks.List.getButtonHtml(c);
                }, ``) 
            );
            $("#pagination button").click(function(e){
                Tasks.List.openPage(this.value)
            })
            $("#pagination button").removeClass('active')
            $("#pagination button[value='"+this.currentPage+"']").addClass("active")
        }

        ,display(tasks){
            $("#tasklist #container").html(tasks.reduce((str, task)=> str + this.getTaskHtml(task), ""));
            $("#tasklist .edit").click(function(e){
                
                Tasks.Edit.open(this.getAttribute("data-id")*1);
            })
        }        

        ,getButtonHtml(index){
            return `<button class='btn' value=${index}>${index+1}</button>`;
        }
        ,getTaskHtml(task){
            return `
                <div class='item col-sm-4 ' data-id='${task.id}'>
                    <figure class='card'>
                        <img class='card-img-top' src='/api/image/?task=${task.id}'/>
                        <figcaption class='card-body'>
                            <h3>${task.username}</h3>
                            <h6>${task.email}</h4>
                            <div>
                                ${task.content}
                                <p class='status badge ${task.isDone*1 == 1 ? "badge-success" : "badge-warning"}' role='badge' data-status='${task.isDone}'>
                                    ${task.isDone*1 == 1 ? "Done" : "Pending"}
                                </p>
                            </div>
                            
                            ${Tasks.isAdmin ? this.getEditBtnHtml(task.id) : ``}
                        </figcaption>
                    </figure>
                </div>
            `;
        }

        ,getEditBtnHtml(id){
            return `<button class='btn btn-warn edit' data-id='${id}' data-toggle='modal' data-target='#editTask'> Edit </button>`;
        }
    }

    ,Form:{
        init(){
            $("#create-form").submit(this.onCreateTask.bind(this))
            $("#open-preview").click(this.openPreview.bind(this));
            $("#close-preview").click(this.closePreview.bind(this));
            
        }

        ,serialize(){
            let [username, email, content, image] = [
                $("#username").val()
                ,$("#email").val()
                ,$("#content").val()
                ,$("#image")[0].files[0]
            ]
            let task = {username, email, content, image};
            return task;
        }

        ,onCreateTask(e){
            e.preventDefault();
            let task = this.serialize();            
            if(!task.username.trim()){
                return alert("Username cannot be empty!");
            }
            if(!task.email.trim()){
                return alert("Email cannot be empty!");
            }
            if(!task.content.trim()){
                return alert("Content cannot be empty!");
            }
            if(!task.image){
                return alert("Image cannot be empty!");
            }

            Tasks.createTask(task);

        }

        ,openPreview(e){
            $("#preview").show();
            let task = this.serialize();
            $("#p-username").text(task.username);
            $("#p-email").text(task.email);
            $("#p-content").text(task.content);
            $("#p-image")[0].src = "";
            if(task.image instanceof File){
                let reader = new FileReader();
                reader.onload = ()=>{
                    $("#p-image")[0].src = reader.result;
                }
                reader.readAsDataURL(task.image);
            }
        }

        ,closePreview(e){
            $("#preview").hide();
        }
    }

    ,createTask: async function(task){
        let res = await Tasks.Data.createTask(task);
        console.log(res);
        //location.reload();
    }

    ,Data: {
        getJSON(path, data){
            return new Promise((resolve, reject)=>{
                let xhr = new XMLHttpRequest;
                path += "?" + Object.keys(data)
                    .map(key => `${encodeURIComponent(key)}=${encodeURIComponent(data[key])}`)
                    .join("&");

                xhr.open("GET",path,true);
                xhr.onload = ()=>resolve(JSON.parse(xhr.responseText));
                xhr.send();
            })
        }

        ,doPost(path, data){
            return new Promise((resolve, reject)=>{
                let fd = new FormData;
                Object.keys(data).forEach(key => fd.append(key, data[key]));
                let xhr = new XMLHttpRequest;               
                xhr.open("POST",path,true);
                xhr.onload = ()=>resolve(JSON.parse(xhr.responseText));
                xhr.send(fd);
            })
        }

        ,getTasks(offset, step, column, dir){
            return this.getJSON("/api/tasks/",{offset, step, column, dir})
                .then(data => {
                    Tasks.Data.tasklist = data.tasks;
                    return data;
                })
        }
        ,tasklist: []
        ,getById(id){
            return this.tasklist.filter(x => x.id == id)[0];
        }


        ,createTask(task){
            return new Promise(function(resolve, reject){
                let reader = new FileReader();
                reader.onload = function(){
                    task.image = reader.result;
                    resolve(Tasks.Data.doPost("/api/tasks/",task));
                }
                reader.readAsDataURL(task.image);
            })
            
        }

        ,login(login, password){
            return this.doPost("/api/login/",{login, password});
        }

        ,editTask(id, content, status){
            return this.doPost("/api/task/",{id, content, status})
        }

    }

}
$(Tasks.start());