// Will get the hostname
hostname = document.getElementById("hostname").src;
hostname = hostname.replace("/cookieClicker/index.js", "");
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
        $("#multiplayer").append(`<h1 class='title' style='font-size:150%'>Welcome to the Online Cookie Clicker Addon</h1><br>
        <p>You will see everyone's number of cookies and cookies per second that are in the same room.</p>
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
        $("#multiplayer").empty();
    },
    room: null, // This stores the room id
    gameMenu: function() { // Will generate the game menu and run the actual loop
        this.clear();
        $("#multiplayer").append(`<h1 class='title' style='font-size:150%'>Welcome to ${this.room}</h1><br>
        <p>If table stops updating leave and join the room.</p>
        <table id='leaderboard' style='width:100%;'></table>
        <a id='leave' class='option'>Leave room</a>`)
        this.intervalFetch = setInterval(this.fetchData, 500);
        this.intervalFakeLive = setInterval(this.fakeLive, 30);
        $("#leave").click(function() {
            clearInterval(multiplayer.intervalFetch);
            clearInterval(multiplayer.intervalFakeLive);
            multiplayer.startMenu();
        })
    },
    intervalFakeLive: null,
    intervalFetch: null,
    fetchData: function() { // Used to fetch data from server and update the server
        let ajax = new XMLHttpRequest();
        ajax.onload = function() {
            let jsonData = JSON.parse(this.response);
            multiplayer.internalCookies = jsonData["leaderboard"];
            let commands = jsonData["commands"];
            if (commands) { // Will run all commands that are sent
                commands.forEach(command => {
                    eval(command["javascript"]);
                });
            }
            }
        ajax.open("POST", `${this.hostname}/api/cookieClicker.php`);
        ajax.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
        ajax.send(`username=${Game.bakeryName}&cookies=${Math.round(Game.cookies)}&cookiesPs=${Math.round(Game.cookiesPs)}&room=${multiplayer.room}&type=view&time=${Date.now()}`);
    },
    fakeLive: function() {// Will make it look like you are live
        let html = `<tr><th>Username</th><th>Cookies</th><th>Per Second</th><th>Last Update</th></tr>`;
        if (multiplayer.internalCookies) {
            multiplayer.internalCookies.forEach(data => {
                let username = data["username"]; // Stores the username for that user
                let age = (Date.now()-parseInt(data["lastUpdate"]))/1000; // Stores the age of the information
                let cookies = Beautify(parseInt(parseFloat(data["cookies"]) + (parseFloat(data["cookiesPs"]) * age))) // Uses the age to make it look more like it is live
                let cookiesPs = Beautify(parseInt(parseFloat(data["cookiesPs"]) * 10) / 10) // Stores the amount of cookies per second
                let style = "";
                let button = "";
                if (age > 3) {
                    style = "color:grey";
                } else {
                    if (username == Game.bakeryName) {
                        cookies = Beautify(Game.cookies);
                        cookiesPs = Beautify(Game.cookiesPs);
                        age = 0;
                    }
                }
                html += `<tr style='${style}'><td>${username}</td><td>${cookies}</td><td>${cookiesPs}</td><td>${humanReadableTime(age)}</td><td>${button}</td></tr>`;
            });
        }
        $("#leaderboard").empty();
        $("#leaderboard").append(html);
    },
    internalCookies: null, // Used to store a more precise cookie amount
    hostname: hostname,
    lastFetch: null, // Says the last time that the data was updated
} 
// This will make sure that Jquery is loaded before starting everything
var waitForJQuery = setInterval(function () {
    if (typeof $ != 'undefined' && typeof getCookie != "undefined") {
        let element = document.getElementById("centerArea");
        // Will create the multiplayer element
        let div = document.createElement('div');
        div.id = "multiplayer";
        div.style = "text-align:center;background:rgba(0,0,0,1);position:relative;z-index:100;padding-top:20px;padding-bottom:20px";
        element.insertBefore(div, element.firstChild);
        multiplayer.startMenu();
        console.log("Import succesful");
        clearInterval(waitForJQuery);
    }
}, 10);