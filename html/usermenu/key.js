function updateKey() { // Used to update the keys and session data
    const ajax = new XMLHttpRequest();
    
    ajax.onload = function() {
        if (ajax.status == 200) {
            let text = "<tr><th>Key</th><th>Expiration</th></tr>";
            Object.values(JSON.parse(this.responseText)).forEach(element => {
                let date = new Date(element["expire"]*1000);
                expireText = `${date.getHours()}:${date.getMinutes()}:${date.getSeconds()} at ${date.getMonth()+1}-${date.getDate()}-${date.getFullYear()}`;
                text += `<tr><td>${element["cookie"]}</td><td>${expireText}</td></tr>`;
            });
            $("#keys").html(text);
        } else {
            JQerror("Could not update the sessions.");
        }
    }
    ajax.open("GET", `/api/key.php?get=true&key='${getCookie('user')}'`);
    ajax.send(); 
}

$(document).ready(function() {
    updateKey();
});