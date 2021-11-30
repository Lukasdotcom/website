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
            document.getElementById(id).remove();
        } else {
            alert("Could not delete log entry.")
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
        }
    ajax.open("POST", "/api.php");
    ajax.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    ajax.send(`server=restart&key='${getCookie('user')}'`); 
}
function updateLog() { // Used to update the log.
    const ajax = new XMLHttpRequest();
    
    ajax.onload = function() {
        if (ajax.status == 200) {
            let response = JSON.parse(ajax.response);
            if (response) {
                response.forEach(appendLogData);
                search($("#searchText").val());
                latestTime = lastElement(response)["time"]
            }
        } else {
            if (!offline) {
                offline = true;
                $(".offline").show();
            }
        }
        setTimeout(updateLog, 4000)
        search($("#searchText").val());
        }
    ajax.open("GET", `/api/server.php?log=true&key='${getCookie('user')}'&startTime=${latestTime}`);
    ajax.send(); 
}
function appendLogData(item, index, array) { // Used to add a log to the users screen
    let date = new Date(item["time"]*1000);
    let information = `<tr id='${logLength}' style='color:${types[item["type"]]["color"]}'><td id='${logLength}.category'>${types[item["type"]]["name"]}</td><td id='${logLength}.message'>${item["message"]}</td><td id='${logLength}.time'>${item["time"]}</td><td id='${logLength}.clockTime'>${date.getHours()}:${date.getMinutes()}:${date.getSeconds()} at ${date.getMonth()+1}-${date.getDate()}-${date.getFullYear()}</td>`;
    if (deleteLog) {
        information += `<td id='${logLength}.button' style='color: white'><button type='button' onClick="remove('${item["message"]}', '${item["time"]}', '${logLength}')">Delete</button><br></td>`;
    } 
    information += "</tr>";
    $(information).insertAfter("#tableHeader")
    logLength ++;
}
var logLength = 0;
var latestTime = 0; // Stores the latest log time
$(document).ready(function() {
    updateLog();
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
var offline = false; // Stores if the user is offline
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