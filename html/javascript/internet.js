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
    ajax.open("POST", "api.php");
    ajax.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    ajax.send(`internet=delete&id='${id}'&key='${getCookie('user')}'`);
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
    ajax.open("POST", "api.php");
    ajax.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    ajax.send(`internet=edit&id='${id}'&startHour='${document.getElementById(id + '.startHour').value}'&startMinute='${document.getElementById(id + '.startMinute').value}'&endHour='${document.getElementById(id + '.endHour').value}'&endMinute='${document.getElementById(id + '.endMinute').value}'&expire='${document.getElementById(id + '.expire').value}'&key='${getCookie('user')}'`); 
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
    expiration.innerHTML = `<input style='width: 120px' type='number' id='${topPriority}.expire' value='0'>`;
    buttons.innerHTML = `<button type='button' onClick='save("${topPriority}")'>✓</button><div class='red'><button type='button' onClick='remove("${topPriority}")'>✗</button>`;
}
