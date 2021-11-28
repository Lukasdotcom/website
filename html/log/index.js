// Will find the category number for the id of a row in the log
function findCategory(category) {
    for(var i=0;i<typeLength;i++) {
        if (types[i]["name"] == category) {
            return i;
        }
    }
    return false
}
function search(term) {
    rows = document.getElementById('log').rows
    colors = []
    for(var i=0;i<typeLength;i++) {
        colors.push([document.getElementById(types[i]["name"]).checked, document.getElementById(`${types[i]["name"]}.color`).value]);
        document.getElementById(`${types[i]["name"]}.text`).style.color = document.getElementById(`${types[i]["name"]}.color`).value;
    }
    localStorage.log = JSON.stringify(colors);
    localStorage.logSearch = term;
    for(var i=0;i<rows.length-1;i++) {
        if (document.getElementById(`${String(i)}.message`).innerHTML.includes(term) && document.getElementById(document.getElementById(`${String(i)}.category`).innerHTML).checked) {
            document.getElementById(i).style.display = "";
        } else {
            document.getElementById(i).style.display = "none";
        }
        document.getElementById(i).style.color = colors[findCategory(document.getElementById(`${String(i)}.category`).innerHTML)][1]
      }
}
function resetColor(searchTerm, term, id) {
    document.getElementById(`${term}.color`).value = types[id]["color"];
    search(searchTerm);
}
function remove(message, time, id) {
    const ajax = new XMLHttpRequest();
    ajax.onload = function() {
        if (ajax.status == 200) {
            document.getElementById(id).style.display = "none";
            document.getElementById(`${id}.category`).innerHTML = "deleted";
        } else {
            window.location.reload();
        }
    }
    ajax.open("POST", "/api.php");
    ajax.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    ajax.send(`log=remove&message=${message}&time=${time}&key='${getCookie('user')}'`); 
}
function collapseCategories() {
    if (! localStorage.collapseCategories) {
        document.getElementById(`collapseCategories`).innerHTML = "Uncollapse Categories";
        for(var i=0;i<typeLength;i++) {
            document.getElementById(`${types[i]["name"]}.text`).style.display = "none";
        }
        localStorage.collapseCategories = true;
    } else {
        document.getElementById(`collapseCategories`).innerHTML = "Collapse Categories";
        for(var i=0;i<typeLength;i++) {
            document.getElementById(`${types[i]["name"]}.text`).style.display = "";
        }
        localStorage.collapseCategories = "";
    }
}
function update() {
    const ajax = new XMLHttpRequest();
    
    ajax.onload = function() {
        if (ajax.status == 200) {
            alert("Updating Server.")
        } else {
            alert("Error something went wrong.")
        }
        window.location.reload();      
        }
    ajax.open("POST", "/api.php");
    ajax.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    ajax.send(`server=update&key='${getCookie('user')}'`); 
}
function restart() {
    const ajax = new XMLHttpRequest();
    
    ajax.onload = function() {
        if (ajax.status == 200) {
            alert("Restarting Server.")
        } else {
            alert("Error something went wrong.")
        }
        window.location.reload();      
        }
    ajax.open("POST", "/api.php");
    ajax.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    ajax.send(`server=restart&key='${getCookie('user')}'`); 
}
$(document).ready(function() {
    collapseCategories();
    collapseCategories();
    if (localStorage.log != undefined) {
        $("#searchText").val(localStorage.logSearch)
    }
    if (localStorage.log != undefined) {
        colors = JSON.parse(localStorage.log);
        for(var i=0;i<typeLength;i++) {
            document.getElementById(`${types[i]["name"]}.text`).style.color = colors[i][1];
            document.getElementById(`${types[i]["name"]}.color`).value = colors[i][1];
            document.getElementById(`${types[i]["name"]}`).checked = colors[i][0];
        }
    }
    if (localStorage.logSearch == undefined) {
        localStorage.logSearch = "";
    }
    search(localStorage.logSearch);
});
var offline = false;
function updateStats() {
    const ajax = new XMLHttpRequest();
    
    ajax.onload = function() {
        if (ajax.status == 200) {
            $("#uptime").text(ajax.responseText);
            if (offline) {
                offline = false;
                $(".offline").hide();
            }
        } else {
            if (!offline) {
                offline = true;
                $(".offline").show();
            }
        }
        }
    ajax.open("GET", `/api/server.php?uptime=true&key='${getCookie('user')}'`);
    ajax.send(); 
    const ajax2 = new XMLHttpRequest();
    
    ajax2.onload = function() {
        if (ajax2.status == 200) {
            $("#temp").text(ajax2.responseText);
            if (offline) {
                offline = false;
                $(".offline").hide();
            }
        } else {
            if (!offline) {
                offline = true;
                $(".offline").show();
            }
        }   
        }
    ajax2.open("GET", `/api/server.php?temp=true&key='${getCookie('user')}'`);
    ajax2.send(); 
}