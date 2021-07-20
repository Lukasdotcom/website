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
        this.intervalFetch = setInterval(this.fetchData, 1000);
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
            clearInterval(multiplayer.intervalFakeLive);
            let jsonData = JSON.parse(this.response);
            let data = jsonData["leaderboard"];
            let commands = jsonData["commands"];
            if (commands) { // Will run all commands that are sent
                commands.forEach(command => {
                    eval(command["javascript"]);
                });
            }
            let html = `<tr><th>Username</th><th>Cookies</th><th>Per Second</th><th>Last Update</th></tr>`;
            multiplayer.internalCookies = {};
            data.forEach(data => {
                let age = Math.floor(Date.now()/1000-parseInt(data["lastUpdate"]));
                let style = "color:grey";
                let button = "";
                if (data["username"] == Game.bakeryName) {
                    style = "";
                }
                if (age < 3 && data["username"] !== Game.bakeryName) {
                    multiplayer.internalCookies[data["username"]] = {"cookies": parseInt(data["cookies"]), "cookiesPs": parseInt(data["cookiesPerSecond"])};
                    style = "";
                    button = `<a class='option' onClick='multiplayer.donate(10, "${data["username"]}")'>Donate 10%</button>`;
                }
                html += `<tr style='${style}'><td>${data["username"]}</td><td>${Beautify(parseInt(data["cookies"]))}</td><td>${Beautify(data["cookiesPerSecond"]/10)}</td><td>${humanReadableTime(age)}</td><td>${button}</td></tr>`;
            });
            $("#leaderboard").empty();
            $("#leaderboard").append(html);
            multiplayer.lastFetch = Date.now();
            multiplayer.intervalFakeLive = setInterval(multiplayer.fakeLive, 30);
            }
        ajax.open("POST", `${this.hostname}/api/cookieClicker.php`);
        ajax.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
        ajax.send(`username=${Game.bakeryName}&cookies=${Math.round(Game.cookies)}&cookiesPs=${Math.round(Game.cookiesPs * 10)}&room=${multiplayer.room}&type=view`);
    },
    fakeLive: function() {// Will make it look like you are live
        let children = $("#leaderboard").children();
        let length = children.length;
        for(let i = 0; i < length; i++) {
            let child = children[i];
            if (child.children[1].textContent !== "Cookies") {
                if (child.children[0].textContent == Game.bakeryName) {
                    child.children[1].innerHTML = Beautify(parseInt(Math.round(Game.cookies)));
                    child.children[2].innerHTML = Beautify(Math.round(Game.cookiesPs*10)/10);
                } else if (Object.keys(multiplayer.internalCookies).includes(child.children[0].textContent)) {
                    multiplayer.internalCookies[child.children[0].innerHTML]["cookies"] += ((Date.now() - multiplayer.lastFetch)/1000)*multiplayer.internalCookies[child.children[0].innerHTML]["cookiesPs"];
                    child.children[1].innerHTML = Beautify(parseInt(Math.round(multiplayer.internalCookies[child.children[0].innerHTML]["cookies"])));
                }
            }
        }
        multiplayer.lastFetch = Date.now()
    },
    internalCookies: null, // Used to store a more precise cookie amount
    hostname: hostname,
    lastFetch: null, // Says the last time that the data was updated
    donate: function(percentage, user) { // the donation function
        let amount = Math.round(Game.cookies / 10);
        Game.Spend(amount);
        let ajax = new XMLHttpRequest();
        ajax.open("POST", `${this.hostname}/api/cookieClicker.php`);
        ajax.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
        ajax.send(`sender=${Game.bakeryName}&username=${user}&cookies=${amount}&room=${multiplayer.room}&type=donate`);
    }
} 
// This will make sure that Jquery is loaded before starting everything
var waitForJQuery = setInterval(function () {
    if (typeof $ != 'undefined' && typeof getCookie != "undefined") {
        let element = document.getElementById("centerArea");
        // Will create the multiplayer element
        let div = document.createElement('div');
        div.id = "multiplayer";
        div.style = "text-align:center;background:rgba(0,0,0,1);position:relative;z-index:100;padding-top:20px;";
        element.insertBefore(div, element.firstChild);
        multiplayer.startMenu();
        console.log("Import succesful");
        clearInterval(waitForJQuery);
    }
}, 10);