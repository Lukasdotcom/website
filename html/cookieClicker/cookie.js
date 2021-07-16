// Will add jquery and another script
var script = document.createElement('script');
script.src = `${hostname}/javascript/functions.js`;
document.head.appendChild(script);
var script = document.createElement('script');
script.src = `${hostname}/javascript/jquery.js`;
document.head.appendChild(script);
// Used to store all values to make storage not overlap with cookie clicker values
var multiplayer = {
    startMenu: function() { // Will generate the startup menu
        this.clear()
        $("#smallSupport").append(`<h1 class='title' style='font-size:150%'>Welcome ${Game.bakeryName}</h1><br>
        <label for="room">Room ID:</label>
        <input type="text" id="room" name="room"/>
        <a id='joinButton' class='option'>Join room</a>`);
        // Will run the code for when the user clicks join
        $("#joinButton").click(function() {
            multiplayer.room = $("#room").val();
            multiplayer.gameMenu();
        })
    },
    clear: function() { // Will clear the menu area for this
        $("#smallSupport").empty();
    },
    room: null, // This stores the room id
    gameMenu: function() { // Will generate the game menu and run the actual loop
        this.clear();
        $("#smallSupport").append(`<h1 class='title' style='font-size:150%'>Welcome to ${this.room}</h1><br>
        <p>If table stops updating leave and join the room.</p>
        <table id='leaderboard'></table>
        <a id='leave' class='option'>Leave room</a>`)
        this.interval = setInterval(this.fetchData, 1000);
        $("#leave").click(function() {
            clearInterval(multiplayer.interval);
            multiplayer.startMenu();
        })
    },
    interval: null,
    fetchData: function() { // Used to fetch data from server and update the server
        const ajax = new XMLHttpRequest();
        ajax.onload = function() {
            let data = JSON.parse(this.response);
            $("#leaderboard").empty();
            $("#leaderboard").append(`<tr><th>Username</th><th>Cookies</th><th>Cookies Per Second</th></tr>`)
            data.forEach(data => {
                $("#leaderboard").append(`<tr><td>${data["username"]}</td><td>${data["cookies"]}</td><td>${data["cookiesPerSecond"]/10}</td></tr>`);
            });
            }
        ajax.open("POST", `${this.hostname}/api/cookieClicker.php`);
        ajax.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
        ajax.send(`username=${Game.bakeryName}&cookies=${Math.round(Game.cookies)}&cookiesPs=${Game.cookiesPs * 10}&room=${multiplayer.room}`);
    },
    hostname: hostname
} 
// This will make sure that Jquery is loaded before starting everything
var waitForJQuery = setInterval(function () {
    if (typeof $ != 'undefined' && typeof getCookie != "undefined") {
        multiplayer.startMenu()
        clearInterval(waitForJQuery);
    }
}, 10);