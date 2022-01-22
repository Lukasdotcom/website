function deleteContainer(id) { // Used to delete a container
    const ajax = new XMLHttpRequest();
    
    ajax.onload = function() {
        if (ajax.status != 200) {
            JQerror(this.responseText);
        }
        updateContainer();
    }
    ajax.open("POST", `/api/docker.php`);
    ajax.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    ajax.send(`deleteContainer=${id}&key='${getCookie('user')}'`); 
}

function updateContainer() { // Used to update the table
    const ajax = new XMLHttpRequest();
    
    ajax.onload = function() {
        if (ajax.status == 200) {
            let text = "<tr><th>ID</th><th>Status</th><th>Link</th><th>Port</th></tr>";
            Object.values(JSON.parse(this.responseText)).forEach(element => {
                text += `<tr><td>${element["ID"]}</td><td>${element["action"]}</td><td>${element["link"]}</td><td>${element["port"]}</td>`;
                if (element["action"] == "stopped") { // Used to add button to delete container
                    text += `<td><button onclick='deleteContainer("${element["ID"]}")'>Delete</button></td>`;
                }
                text += "</tr>"
            });
            $("#container").html(text);
        } else {
            JQerror(this.responseText);
        }
    }
    ajax.open("GET", `/api/docker.php?containers=get&all=true&key='${getCookie('user')}'`);
    ajax.send(); 
}

$(document).ready(function() {
    updateContainer();
    $("#createContainer").click(function() { // Used to create a container.
        const ajax = new XMLHttpRequest();
    
        ajax.onload = function() {
            if (ajax.status != 200) {
                JQerror(this.responseText);
            }
            updateContainer();
        }
        ajax.open("POST", `/api/docker.php`);
        ajax.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
        ajax.send(`createContainer=true&link=${encodeURI($("#link").val())}&port=${encodeURI($("#port").val())}&key='${getCookie('user')}'`);
    })
});