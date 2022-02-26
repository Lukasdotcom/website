// Will find the category number for the id of a row in the log
function findCategory(category) {
    for(var i=0;i<typeLength;i++) {
        if (types[i]["name"] == category) {
            return i;
        }
    }
    return false
}
function search(term) { // Used to search the log
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
function resetColor(searchTerm, term, id) { // Used to reset the log color of a certain type
    document.getElementById(`${term}.color`).value = types[id]["color"];
    search(searchTerm);
}
function remove(message, time, id) { // Used to remove a log entry from the log.
    const ajax = new XMLHttpRequest();
    ajax.onload = function() {
        if (ajax.status == 200) {
            document.getElementById(id).remove();
        } else {
            alert("Could not delete log entry.")
        }
    }
    ajax.open("POST", "/api/log.php");
    ajax.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    ajax.send(`remove=true&message=${message}&time=${time}&key='${getCookie('user')}'`); 
}
function collapseCategories() { // Collapses or uncollapses the categories to make the webpage cleaner or more detailed.
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
function updateUpdateInfo() { // Used to get the response to the update info.
    const ajax = new XMLHttpRequest();

    ajax.onload = function() {
        if (ajax.status == 200) {
            $('#updateText').text(ajax.responseText)
        }
        }
    ajax.open("GET", `/api/server.php?update=true&key='${getCookie('user')}'`);
    ajax.send(); 
}
function update() { // Sends a request to the server for the server to update
    const ajax = new XMLHttpRequest();
    
    ajax.onload = function() {
        if (ajax.status == 200) {
            alert("Updating Server.");
            updateUpdateInfo();
        } else {
            alert("Error something went wrong.");
        }     
        }
    ajax.open("POST", "/api/server.php");
    ajax.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    ajax.send(`update=true&key='${getCookie('user')}'`); 
}
function restart() { // Sends a request to the server to tell it to restart
    const ajax = new XMLHttpRequest();
    
    ajax.onload = function() {
        if (ajax.status == 200) {
            alert("Restarting Server.")
        } else {
            alert("Error something went wrong.")
        }    
        }
    ajax.open("POST", "/api/server.php");
    ajax.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    ajax.send(`restart=true&key='${getCookie('user')}'`); 
}
function updateLog(previous=false, all=false) { // Used to update the log with new entries that came. either before or after the log(previous). All indicates if it should load everything before a certain point.
    const ajax = new XMLHttpRequest();
    
    ajax.onload = function() {
        if (ajax.status == 200) {
            let response = JSON.parse(ajax.response);
            if (previous) {
                response.reverse(); // Makes sure to reverse the array so the elements are in the right order.
                earliestTime = earliestTime - 3600 * 24 * $("#days").val();
            } 
            if (response.length) {
                if (! previous) {
                    latestTime = lastElement(response)["time"]
                }
                response.forEach(function(item) {
                    appendLogData(item, previous)
                });
            }
            $("#loadMore").button("enable");
        } else {
            if (!offline) {
                offline = true;
                $(".offline").show();
            }
        }
        if (! previous) {
            setTimeout(updateLog, 4000)
        }
        search($("#searchText").val());
        }
    if (previous) {
        $("#loadMore").button("disable");
        ajax.open("GET", `/api/log.php?log=true&key='${getCookie('user')}'&startTime=${(all) ? 0 : earliestTime - 3600 * 24 * $("#days").val() }&endTime=${earliestTime}`);
    } else {
        ajax.open("GET", `/api/log.php?log=true&key='${getCookie('user')}'&startTime=${latestTime}`);
    }
    ajax.send(); 
}
function appendLogData(item, bottom=false) { // Used to add a log entry to the users screen at the top or bottom.
    let date = new Date(item["time"]*1000);
    let information = `<tr id='${logLength}' style='color:${types[item["type"]]["color"]}'><td id='${logLength}.category'>${types[item["type"]]["name"]}</td><td id='${logLength}.message'>${item["message"]}</td><td id='${logLength}.time'>${item["time"]}</td><td id='${logLength}.clockTime'>${date.getHours()}:${date.getMinutes()}:${date.getSeconds()} at ${date.getMonth()+1}-${date.getDate()}-${date.getFullYear()}</td>`;
    if (deleteLog) {
        information += `<td id='${logLength}.button' style='color: white'><button type='button' onClick="remove('${item["message"]}', '${item["time"]}', '${logLength}')">Delete</button><br></td>`;
    } 
    information += "</tr>";
    if (bottom) {
        $("#log").append(information);
    } else {
        $(information).insertAfter("#tableHeader");
    }
    logLength ++;
}
var logLength = 0;
var latestTime = Date.now() / 1000 - 3600 * 24 * 7; // Stores the latest log time which starts at 1 week before the visit
var earliestTime = latestTime; // Stores the earliest time which the log has
$(document).ready(function() {
    updateLog(); // Updates the log to preload it.
    // Makes sure that the categories are collapsed in the right way
    collapseCategories();
    collapseCategories();

    setInterval(updateUpdateInfo, 10000); // Makes sure to update the information about updates every 10 seconds
    if (localStorage.log != undefined) { // Sets the correct color and visibilty for each log category.
    
        colors = JSON.parse(localStorage.log);
        for(var i=0;i<typeLength;i++) {
            document.getElementById(`${types[i]["name"]}.text`).style.color = colors[i][1];
            document.getElementById(`${types[i]["name"]}.color`).value = colors[i][1];
            document.getElementById(`${types[i]["name"]}`).checked = colors[i][0];
        }
    }
    if (localStorage.logSearch != undefined) { // Makes sure to prefill the previous search
        $("#searchText").val(localStorage.logSearch)
    }
    search(localStorage.logSearch);
    
    // Shows server status like temprature and uptime
    if (serverStatus) {
        updateUptime();
        updateTemp();
    }
    $("#loadMore").button()
    $("#loadAll").button()
    $("#loadMore").click(function() {
        updateLog(true);
    });
    $("#loadAll").click(function() { // Used to load all data
        updateLog(true, all=true)
        $("#load").remove();
    });
});
var offline = false; // Stores if the user is offline
function updateUptime() { // Updates the uptime and server load indicator
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
        setTimeout(updateUptime, 1000);
        }
    ajax.open("GET", `/api/server.php?uptime=true&key='${getCookie('user')}'`);
    ajax.send(); 
}
function updateTemp() { // Updates the temprature indicator
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
        setTimeout(updateTemp, 1000);
        }
    ajax2.open("GET", `/api/server.php?temp=true&key='${getCookie('user')}'`);
    ajax2.send(); 
}