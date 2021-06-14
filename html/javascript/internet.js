function remove(id) {
    document.getElementById("saveStatus").innerHTML = "Saving";
    const ajax = new XMLHttpRequest();
    
    ajax.onload = function() {
        document.getElementById("saveStatus").innerHTML = "Saved";
        document.getElementById(id + ".row").remove()
        setTimeout(() => { document.getElementById("saveStatus").innerHTML = ""; }, 2000);
        }
    if (Math.sign(id) == -1) {
        idNegative = 'True';
    } else {
        idNegative = 'False';
    }
    ajax.open("GET", "api.php?idNegative=" + idNegative + "&internet=delete&id=" + id);
    ajax.send();
}
function save(id) {
    document.getElementById("saveStatus").innerHTML = "Saving";
    const ajax = new XMLHttpRequest();
    
    ajax.onload = function() {
        document.getElementById("saveStatus").innerHTML = "Saved";
        setTimeout(() => { document.getElementById("saveStatus").innerHTML = ""; }, 2000);        
        }
    if (Math.sign(id) == -1) {
        idNegative = 'True';
    } else {
        idNegative = 'False';
    }
    ajax.open("GET", "api.php?idNegative=" + idNegative + "&internet=edit&id=" + id + "&startHour=" + document.getElementById(id + '.startHour').value + "&startMinute=" + document.getElementById(id + '.startMinute').value + "&endHour=" + document.getElementById(id + '.endHour').value + "&endMinute=" + document.getElementById(id + '.endMinute').value + "&expire=" + document.getElementById(id + '.expire').value);
    ajax.send(); 
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

function button() {
    document.getElementById("saveStatus").innerHTML = "Saving";
    const ajax = new XMLHttpRequest();
    
    ajax.onload = function() {
        document.getElementById("saveStatus").innerHTML = "Saved";
        setTimeout(() => { window.location.reload(); }, 500);             
        }
      
    ajax.open("GET", "api.php?internet=button");
    ajax.send();
}