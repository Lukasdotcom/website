function updateKey() { // Used to update the keys and session data
    const ajax = new XMLHttpRequest();
    
    ajax.onload = function() {
        if (ajax.status == 200) {
            let text = "<tr><th>Key</th><th>Expiration</th></tr>";
            Object.values(JSON.parse(this.responseText)).forEach(element => {
                let date = new Date(element["expire"]*1000);
                expireText = `${date.getHours()}:${date.getMinutes()}:${date.getSeconds()} at ${date.getMonth()+1}-${date.getDate()}-${date.getFullYear()}`;
                text += `<tr id='${element["cookie"]}' ><td>${element["cookie"]}</td><td>${expireText}</td><td><button onclick='revoke("${element["cookie"]}")'>Revoke</button></td></tr>`;
            });
            $("#keys").html(text);
        } else {
            JQerror(this.responseText);
        }
    }
    ajax.open("GET", `/api/key.php?get=true&key='${getCookie('user')}'`);
    ajax.send(); 
}

function revoke(key) { // Used to revoke a key
    const ajax = new XMLHttpRequest();
    
    ajax.onload = function() {
        if (ajax.status != 200) {
            JQerror(this.responseText);
        }
        updateKey();
    }
    ajax.open("POST", `/api/key.php`);
    ajax.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    ajax.send(`delete=${key}&key='${getCookie('user')}'`); 
}

$(document).ready(function() {
    updateKey();
});