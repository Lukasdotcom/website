function start(id) { // Used to start a container
    const ajax = new XMLHttpRequest();
    
    ajax.onload = function() {
        if (ajax.status != 200) {
            JQerror(this.responseText);
        }
        update();
    }
    ajax.open("POST", `/api/docker.php`);
    ajax.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    ajax.send(`start=${id}&image=${$(`#${id}image`).val()}&key='${getCookie('user')}'`); 
}

function update() { // Used to update the table
    const ajax = new XMLHttpRequest();
    
    ajax.onload = function() {
        if (ajax.status == 200) {
            let text = "<tr><th>ID</th><th>Status</th><th>Image</th><th>Password</th><th>Link</th></tr>";
            Object.values(JSON.parse(this.responseText)).forEach(element => {
                let password = "";
                let image = "";
                if (element["action"] == "started") {
                    password = element["password"];
                } 
                if (element["action"] == "started") { // Used to find the Human Readable name of the image
                    image = "unknown";
                    Object.values(images).forEach(element2 => {
                        if (element2["realName"] == element["image"]) {
                            image = element2["shortName"];
                        }
                    });
                } else if (element["action"] == "stopped") {
                    image = `<select  id='${element["ID"]}image'>`;
                    Object.values(images).forEach(element2 => {
                        let selected = "";
                        if ($(`#${element["ID"]}image`).val() == element2["realName"]) {
                            selected = 'selected="selected"';
                        }
                        image += `<option value='${element2["realName"]}' ${selected}>${element2["shortName"]}</option>`;
                    });
                    image += "</select>";
                }
                text += `<tr><td>${element["ID"]}</td><td>${element["action"]}</td><td>${image}</td><td>${password}</td><td>${element["link"]}</td>`;
                if (element["action"] == "stopped") { // Used to add button to start container
                    text += `<td><button onclick='start("${element["ID"]}")'>Start</button></td>`;
                }
                text += "</tr>"
            });
            $("#docker").html(text);
        } else {
            JQerror(this.responseText);
        }
    }
    ajax.open("GET", `/api/docker.php?containers=get&key='${getCookie('user')}'`);
    ajax.send(); 
}

$(document).ready(function() {
    update();
    setInterval(update, 5000);
});