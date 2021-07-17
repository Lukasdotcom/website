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
        $("#multiplayer").append(`<h1 class='title' style='font-size:150%'>Welcome ${Game.bakeryName}</h1><br>
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
        <table id='leaderboard'></table>
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
        const ajax = new XMLHttpRequest();
        ajax.onload = function() {
            clearInterval(multiplayer.intervalFakeLive);
            let data = JSON.parse(this.response);
            $("#leaderboard").empty();
            $("#leaderboard").append(`<tr><th>Username</th><th>Cookies</th><th>Per Second</th><th>Last Update</th></tr>`)
            multiplayer.internalCookies = {};
            data.forEach(data => {
                let age = Math.floor(Date.now()/1000-parseInt(data["lastUpdate"]));
                let style = "color:grey";
                if (age < 3) {
                    multiplayer.internalCookies[data["username"]] = parseInt(data["cookies"]);
                     style = "";
                }
                $("#leaderboard").append(`<tr style='${style}'><td>${data["username"]}</td><td>${data["cookies"]}</td><td>${data["cookiesPerSecond"]/10}</td><td>${age}</td></tr>`);
            });
            multiplayer.lastFetch = Date.now();
            multiplayer.intervalFakeLive = setInterval(multiplayer.fakeLive, 30);
            }
        ajax.open("POST", `${this.hostname}/api/cookieClicker.php`);
        ajax.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
        ajax.send(`username=${Game.bakeryName}&cookies=${Math.round(Game.cookies)}&cookiesPs=${Game.cookiesPs * 10}&room=${multiplayer.room}`);
    },
    fakeLive: function() {// Will make it look like you are live
        let children = $("#leaderboard").children();
        let length = children.length;
        for(let i = 0; i < length; i++) {
            let child = children[i];
            if (child.children[1].textContent !== "Cookies") {
                if (child.children[0].textContent == Game.bakeryName) {
                    child.children[1].innerHTML = Math.round(Game.cookies);
                    child.children[2].innerHTML = Math.round(Game.cookiesPs*10)/10;
                } else if (Object.keys(multiplayer.internalCookies).includes(child.children[0].textContent)) {
                    multiplayer.internalCookies[child.children[0].innerHTML] += ((Date.now() - multiplayer.lastFetch)/1000)*parseInt(child.children[2].innerHTML);
                    child.children[1].innerHTML = Math.round(multiplayer.internalCookies[child.children[0].innerHTML]);
                }
            }
        }
        multiplayer.lastFetch = Date.now()
    },
    internalCookies: null, // Used to store a more precise cookie amount
    hostname: hostname,
    lastFetch: null // Says the last time that the data was updated
} 
// This will make sure that Jquery is loaded before starting everything
var waitForJQuery = setInterval(function () {
    if (typeof $ != 'undefined' && typeof getCookie != "undefined") {
        let element = document.getElementById("sectionRight");
        while (element.firstChild.id !== "store") {
            element.firstChild.remove();
        }
        let div = document.createElement('div');
        div.id = "multiplayer";
        div.style = "width:300px;text-align:center;background:rgba(0,0,0,0.5);position:relative;z-index:100;";
        element.insertBefore(div, element.firstChild);
        multiplayer.startMenu();
        console.log("Import succesful");
        clearInterval(waitForJQuery);
    }
}, 10);