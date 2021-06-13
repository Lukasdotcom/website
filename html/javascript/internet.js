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

function button() {
    document.getElementById("saveStatus").innerHTML = "Saving";
    const ajax = new XMLHttpRequest();
    
    ajax.onload = function() {
        document.getElementById("saveStatus").innerHTML = "Saved";
        setTimeout(() => { document.getElementById("saveStatus").innerHTML = ""; }, 2000);        
        }
      
    ajax.open("GET", "api.php?internet=button");
    ajax.send();
}