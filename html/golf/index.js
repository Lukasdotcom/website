function update(repeat=false) { // Will update the game.
    var text = "<tr><th>Name</th><th>Players</th><th>Max Players</th><th>Skip Time</th><th>Multiplier for Flipping Last Card</th><th>Points to Out</th><th>Password</th><th>Cards</th><th>Cards to flip</th><th>Decks</th></tr>";
    const ajax = new XMLHttpRequest();
    ajax.onload = function() {
        if (ajax.status == 200) {
            JSON.parse(this.response).forEach(element => {
                let joinText = "Join";
                if (element.players == element.playersToStart) {
                    joinText = "Continue";
                }
                text += `<tr><td>${element.name} <button onClick='joinGame(${element.ID})'>${joinText}</button></td><td>${element.players}</td><td>${element.playersToStart}</td><td>${element.skipTime}</td><td>${element.multiplierForFlip}</td><td>${element.pointsToEnd}</td><td>${element.password ? `true` : "false"}</td><td>${element.cardNumber}</td><td>${element.flipNumber}</td><td>${element.decks}</td></tr>`;
            });
            $("#games").html(text);
        } else {
            JQerror(this.responseText);
        }
        if (repeat) {
            setTimeout(function() {
                update(repeat=true);
            }, 5000);

        }
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
    ajax.send(`create=${$("#name").val()}&cardNumber=${$("#cardNumber").val()}&flipNumber=${$("#flipNumber").val()}&playersToStart=${$("#playersToStart").val()}&multiplierForFlip=${$("#multiplierForFlip").val()}&pointsToEnd=${$("#pointsToEnd").val()}&decks=${$("#decks").val()}&skipTime=${$("#skipTime").val()}&key='${getCookie('user')}'${password}`); 
}
function joinGame(id) {
    window.location = `game.php?game=${id}`;
}


$(document).ready(function() {
    update(repeat=true);
    $("#create").click(create);
});