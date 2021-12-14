function update() { // Will update the game.
    var text = "<tr><th>Name</th><Players</th><th>Current Players</th><th>Max Players</th><th>Password</th></tr>";
    const ajax = new XMLHttpRequest();
    ajax.onload = function() {
        if (ajax.status == 200) {
            JSON.parse(this.response).forEach(element => {
                text += `<tr><td>${element.name}</td><td>${element.players}</td><td>${element.playersToStart}</td><td>${element.password ? `true` : "false"}</td><td><button onClick='joinGame(${element.ID})'>Join</button></td></tr>`;
            });
            $("#games").html(text);
        }
        setTimeout(update, 5000);
        }
    ajax.open("GET", `/api/golf.php?game=true&key='${getCookie('user')}'`);
    ajax.send(); 
}

function joinGame(id) {
    window.location = `game.php?game=${id}`;
}


$(document).ready(function() {
    update();
});