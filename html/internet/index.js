function remove(id) {
    document.getElementById("saveStatus").innerHTML = "Saving";
    const ajax = new XMLHttpRequest();
    
    ajax.onload = function() {
        if (ajax.status == 200) {
            document.getElementById("saveStatus").innerHTML = "Saved";
            setTimeout(() => { document.getElementById("saveStatus").innerHTML = ""; }, 2000);
        } else if  (ajax.status == 429) {
            window.location.reload();
        } else {
            document.getElementById("saveStatus").innerHTML = ajax.responseText;
        }
        }
    ajax.open("POST", "/api/internet.php");
    ajax.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    ajax.send(`delete='${id}'&key='${getCookie('user')}'`);
    document.getElementById(id + ".row").remove()
}
function save(id) {
    document.getElementById("saveStatus").innerHTML = "Saving";
    const ajax = new XMLHttpRequest();
    
    ajax.onload = function() {
        if (ajax.status == 200) {
            document.getElementById("saveStatus").innerHTML = "Saved";
            setTimeout(() => { document.getElementById("saveStatus").innerHTML = ""; }, 2000);
        } else if  (ajax.status == 429) {
            window.location.reload();
        } else {
            document.getElementById("saveStatus").innerHTML = ajax.responseText;
        }      
        }
    ajax.open("POST", "/api/internet.php");
    ajax.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    ajax.send(`edit='${id}'&startHour='${document.getElementById(id + '.startHour').value}'&startMinute='${document.getElementById(id + '.startMinute').value}'&endHour='${document.getElementById(id + '.endHour').value}'&endMinute='${document.getElementById(id + '.endMinute').value}'&expire='${Date.parse(document.getElementById(id + '.expire').value)/1000}'&key='${getCookie('user')}'`); 
}
function addRow() {
    topPriority ++;
    var table = document.getElementById ("internetTable");
    var row = table.insertRow();
    row.id = topPriority + ".row";
    var priority = row.insertCell();
    var startTime = row.insertCell();
    var endTime = row.insertCell();
    var expiration = row.insertCell();
    var buttons = row.insertCell();
    priority.innerHTML = topPriority;
    startTime.innerHTML = `<input type='number' id='${topPriority}.startHour' value='0'>:<input type='number' id='${topPriority}.startMinute' value='0'>`;
    endTime.innerHTML = `<input type='number' id='${topPriority}.endHour' value='0'>:<input type='number' id='${topPriority}.endMinute' value='0'>`;
    expiration.innerHTML = `<input step="1" type='datetime-local' id='${topPriority}.expire'>`;
    buttons.innerHTML = `<button type='button' onClick='save("${topPriority}")'>✓</button><div class='red'><button type='button' onClick='remove("${topPriority}")'>✗</button>`;
}
