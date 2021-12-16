function update() { // Will update the game.
    var text = "<tr><th>Name</th><Players</th><th>Current Players</th><th>Max Players</th><th>Multiplier for Flipping Last Card</th><th>Start Points</th><th>Password</th></tr>";
    const ajax = new XMLHttpRequest();
    ajax.onload = function() {
        if (ajax.status == 200) {
            JSON.parse(this.response).forEach(element => {
                text += `<tr><td>${element.name}</td><td>${element.players}</td><td>${element.playersToStart}</td><td>${element.multiplierForFlip}</td><td>${element.pointsToEnd}</td><td>${element.password ? `true` : "false"}</td><td><button onClick='joinGame(${element.ID})'>Join</button></td></tr>`;
            });
            $("#games").html(text);
        } else {
            JQerror(this.responseText);
        }
        setTimeout(update, 5000);
        }
    ajax.open("GET", `/api/golf.php?game=true&key='${getCookie('user')}'`);
    ajax.send(); 
}
function create() { // Will create a game
    const ajax = new XMLHttpRequest();
    ajax.onload = function() {
        if (ajax.status == 200) {
            update();
        } else {
            JQerror(this.response);
        }
        setTimeout(update, 5000);
        }
    ajax.open("POST", `/api/golf.php`);
    ajax.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    let password = '';
    if ($("#password").val()) {
        password = `&password=${$("#password").val()}`;
    }
    ajax.send(`create=${$("#name").val()}&cardNumber=${$("#cardNumber").val()}&flipNumber=${$("#flipNumber").val()}&playersToStart=${$("#playersToStart").val()}&multiplierForFlip=${$("#multiplierForFlip").val()}&pointsToEnd=${$("#pointsToEnd").val()}&key='${getCookie('user')}'${password}`); 
}
function joinGame(id) {
    window.location = `game.php?game=${id}`;
}


$(document).ready(function() {
    update();
    $("#create").click(create);
});