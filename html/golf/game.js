var searchParams = new URLSearchParams(window.location.search);
var game = searchParams.get('game');
function join() { // Used to join a game
    const ajax = new XMLHttpRequest();
    ajax.onload = function() {
        if (this.status == 200) {
            window.location.reload()
        } else {
            JQerror(this.responseText);
        }
    }
    ajax.open("POST", `/api/golf.php`);
    ajax.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    let passwordText = "&";
    if ($("#password").length) {
        passwordText = `&password=${$("#password").val()}`;
    }
    ajax.send(`join=${game}${passwordText}&key=${getCookie("user")}`);
}

function submitMove() { // Used to submit a move
    const ajax = new XMLHttpRequest();
    ajax.onload = function() {
        if (this.status == 200) {
            update(start=true);
        } else {
            JQerror(this.responseText);
        }
    }
    ajax.open("POST", `/api/golf.php`);
    ajax.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    ajax.send(`swap=${highlightCard}&swap2=${highlightDeck}&game=${game}&key=${getCookie("user")}`);
}

function update(start=false, repeat=false, newFocus="") { // Used to request the latest information
    if (! paused) { // Checks if there is currently a pause on the update loop
        const ajax = new XMLHttpRequest();
        if (start) { // Checks if it should clear the cache or not.
            ajax.onload = function() {
                if (this.status != 200) {
                    JQerror(this.responseText, 5000);
                }
                update(start=false, repeat=repeat, newFocus=newFocus)
            }
            ajax.open("GET", `/api/golf.php?forceUpdate=${game}&key=${getCookie("user")}`);
        } else {
            ajax.open("GET", `/api/golf.php?update=${game}&key=${getCookie("user")}`);
            ajax.onload = function() {
                if (this.status == 304) {
                    1
                } else if (this.status == 200) {
                    data = JSON.parse(this.response);
                    updateUI(focus=newFocus);
                } else {
                    JQerror(this.responseText, 5000);
                }
                if (repeat) {
                    setTimeout(function() {
                        update(start=false, repeat=true)
                    }, 500);
                }
            }
        }
        ajax.send();
    } else if (repeat) {
        setTimeout(function() {
            update(start=false, repeat=true)
        }, 500);
    }
}

function updateUI(focus="") { // Used to update the UI's info
    if (Object.keys(data).length) {
        if (data.action == "roundOver") { // Used to have the game pause until the user presses continue
            paused = true;
            $("#continue").show();
            $("#continue").click(function() {
                $("#continue").off("click");
                $("#continue").hide();
                let focusedPlayer = data.players[playerNumber].user;
                paused = false;
                update(start=true, repeat=false, newFocus=focusedPlayer);
            });
        }
        if (focus) { // Checks if a certain player should be focused on.
            Object.keys(data.players).forEach(element => {
                if (data.players[element].user == focus) {
                    playerNumber = element;
                    changedFocus=true;
                }
            });
        }
        // Resets the highlights to make sure that not both are highlighted
        $("#deck").removeClass("highlight");
        $("#discard").removeClass("highlight");
        // Makes sure that the selected player is valid
        let maxPlayer = Object.keys(data.players).length-1;
        if (maxPlayer<playerNumber) {
            playerNumber = 0;
        } else if (playerNumber < 0) {
            playerNumber = maxPlayer;
        }
        $("#wait").hide();
        $("#game").show();
        let cards = {} // Makes a dictionary that stores every card
        for (let i = 1; i<=data.rules.cardNumber; i++) {
            cards[i] = "back";
        }
        if (data.players[playerNumber]["cards"]) {
            data.players[playerNumber].cards.forEach(element => {
                cards[element.cardPlacement] = element.card;
            });
        }
        // Enables your turn indicator when neccessary
        if (data.action == "switch") {
            $("#yourTurn").show();
        } else {
            $("#yourTurn").hide();
        }
        Object.keys(cards).forEach(element => { // Updates all neccessary cards
            let url = `/img/deck/${cards[element]}.jpg`;
            if ($(`#card${element}`).attr("src") != url) {
                $(`#card${element}`).attr("src", url)
            }
            if (element == highlightCard && player == data.players[playerNumber].user) {
                $(`#card${element}`).addClass("highlight");
            } else {
                $(`#card${element}`).removeClass("highlight");
            }
        });
        // Highlights the correct deck/discard
        if (player == data.players[playerNumber].user) {
            if (highlightDeck == "deck") {
                $("#deck").addClass("highlight");
            } else if (highlightDeck == "discard") {
                $("#discard").addClass("highlight");
            }
        }
        // Makes sure the info about the player is right
        $("#points").text(data.players[playerNumber].points);
        $("#name").text(data.players[playerNumber].user);
        if (player == data.players[playerNumber].user) {
            $("#discard").attr("onclick", onclick="highlight('discard')");
            $("#deck").attr("onclick", onclick="highlight('deck')");
        } else {
            $("#discard").attr("onclick", onclick="");
            $("#deck").attr("onclick", onclick="");
        }
        // Shows who is eliminated
        if (data.players[playerNumber].lastMode == "eliminated") {
            $("#eliminated").show();
        } else {
            $("#eliminated").hide();
        }
        // Shows the button when neccessary
        if (highlightDeck && highlightCard && data.action == "switch") {
            $("#submitMove").show()
        } else {
            $("#submitMove").hide()
        }
        // Shows the discard Pile
        let url = `/img/deck/${lastElement(data.discard)}.jpg`;
        if ($(`#discard`).attr("src") != url) {
            $(`#discard`).attr("src", url)
        }
        // Updates the players points in this turn
        $("#newPoints").text(data.players[playerNumber].currentGamePoints);
    } else {
        $("#wait").show();
        $("#game").hide();
    }
    return;
}

function highlight(element) {
    if (element == "deck" || element == "discard") {
        highlightDeck = element;
    } else {
        highlightCard = element;
    }
    updateUI()
}
var highlightCard = null; // The card currently selected.
var highlightDeck = null; // The deck currently selected.
var playerNumber = 0; // The current Player that is looked at in the UI
var data = {}; // Stores the entirety of the data
var paused = false; // Checks if the game should not check for newer information.
$(document).ready(function() {
    if (joined) {
        update(start=true, repeat=true, newFocus=player);
        $("#left-arrow").button({
            icon: "ui-icon-caret-1-w"
        })
        $("#left-arrow").click(function() {
            playerNumber --;
            updateUI(changedFocus=true)
        })
        $("#right-arrow").button({
            icon: "ui-icon-caret-1-e"
        })
        $("#right-arrow").click(function() {
            playerNumber ++;
            updateUI(changedFocus=true)
        })
    }
});