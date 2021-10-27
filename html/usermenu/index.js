function deleteUser() { // Used to delete a user
    let ajax = new XMLHttpRequest();
    ajax.onload = function() {
        setTimeout(() => { $("#saveStatus").text(""); }, 5000);
        if (ajax.status == 200) {
            $("#saveStatus").append(`${this.responseText}`);
            if (user == username) {
                location.replace("/login.php");
            } else {
                $(`option[value='${user}']`).remove();
                $("#user").val(username);
                search();
            }
        } else {
            $("#saveStatus").append(`Error while deleting user: ${this.responseText}.`);
        }
    }
    ajax.open("POST", `/api/user.php`);
    ajax.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    ajax.send(`type=delete&key=${getCookie("user")}&username=${user}`);
}
function password() { // Used to change the password
    let ajax = new XMLHttpRequest();
    ajax.onload = function() {
        setTimeout(() => { $("#saveStatus").text(""); }, 5000);
        if (ajax.status == 200) {
            $("#saveStatus").append(`${this.responseText}`);
        } else {
            $("#saveStatus").append(`Error while changing password: ${this.responseText}. `);
        }
    }
    ajax.open("POST", `/api/user.php`);
    ajax.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    ajax.send(`type=password&key=${getCookie("user")}&username=${user}&password=${$("#password").val()}`);
}
function search() { // Gets the data for the search
    user = $("#user").val();
    if (editUser || username == user) {
        $("#save").show();
        var progress = 0 // Used to see how many requests have gone through
        // Requests the privileges of the user currently logged in
        let ajax = new XMLHttpRequest();
        ajax.onload = function() {
            if (ajax.status == 200) {
                window.userPriv = JSON.parse(ajax.responseText);
                if (progress == 1) {
                    if (user == username) {
                        window.requestUser = userPriv;
                    }
                    search2(userPriv, requestUser);
                } else {
                    progress = 1;
                }
            } else {
                $("#saveStatus").append(`Error while loading: ${this.responseText}. `);
            }
        }
        ajax.open("GET", `/api/user.php?type=view&key=${getCookie("user")}`);
        ajax.send();
        // Gets the privileges of the user selected
        if (user == username) {
            progress = 1;
        } else {
            console.log(1)
            let ajax2 = new XMLHttpRequest();
            ajax2.onload = function() {
                if (ajax.status == 200) {
                    window.requestUser = JSON.parse(this.responseText);
                    if (progress == 0) {
                        progress = 1
                    } else {
                        search2(userPriv, requestUser)
                    }
                } else {
                    $("#saveStatus").append(`Error while loading: ${this.responseText}. `);
                }
            }
            ajax2.open("GET", `/api/user.php?type=view&key=${getCookie("user")}&user=${user}`);
            ajax2.send();
            $("#header").text(`Privileges for ${user}`);
        }
    } else {
        $("#privilege").empty();
        $("#header").text("");
        $("#save").hide();
    }
}
function search2(userPriv, requestUser) { // When the search is done it will update the user interface
    howMany = userPriv.length
    $("#privilege").empty();
    for (let i=0; i<howMany;i++) {
        element = userPriv[i];
        if (requestUser.includes(element)) {
            checked = "checked";
        } else {
            checked = "";
        }
        $("#privilege").append(`<input type='checkbox' name='${element}' id='${element}' ${checked} value='True'>${element}<br>`);
    }
    if ($("#changeCredintials").length || user == username) { // Checks if the user can change the Credintials
        $("#passwordChange").show()
    } else {
        $("#passwordChange").hide()
    }
    if ($("#changeCredintials").length || user == username) { // Checks if the user can delete that user
        $("#delete").show()
    } else {
        $("#delete").hide()
    }
    setTimeout(() => { $("#saveStatus").text(""); }, 5000);
}
function save() {
    let checkboxes = $("#privilege").find("input[type=checkbox]");
    let text = `type=edit&key=${getCookie('user')}&user=${$("#user").val()}`;
    let checkboxLength = checkboxes.length;
    for (let i=0; i<checkboxLength;i++) {
        let check = checkboxes[i];
        text += `&${check.name}=${check.checked}`;
    }
    let ajax = new XMLHttpRequest();
    ajax.onload = function() {
        if (ajax.status == 200) {
            $("#saveStatus").append(`${this.responseText}. `);
        } else {
            $("#saveStatus").append(`Error while loading: ${this.responseText}. `);
        }
        search()
        }
    ajax.open("POST", "/api/user.php");
    ajax.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    ajax.send(text);
}
$(document).ready(function() {
    search();
    $("#user").change(function() {search()});
    $("#save").click(function() {save()});
    $("#changePassword").click(function() {password()});
    $("#delete").click(function() {deleteUser()});
    $("#download").click(function() {
        let ajax = new XMLHttpRequest();
        ajax.onload = function() {
            if (ajax.status == 200) {
                $("#saveStatus").append(`Downloaded Preferences. `);
                newLocalStorage = JSON.parse(this.response);
                Object.keys(newLocalStorage).forEach(function(value) {
                    localStorage[value] = newLocalStorage[value];
                });
            } else {
                $("#saveStatus").append(`Error while loading: ${this.response}. `);
            }
        }
        ajax.open("POST", "/api/localStorage.php");
        ajax.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
        ajax.send(`load='true'&key=${getCookie("user")}`);
    });
    $("#upload").click(function() {
        let ajax = new XMLHttpRequest();
        ajax.onload = function() {
            if (ajax.status == 200) {
                $("#saveStatus").append(`${this.response}. `);
            } else {
                $("#saveStatus").append(`Error while loading: ${this.response}. `);
            }
        }
        ajax.open("POST", "/api/localStorage.php");
        ajax.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
        ajax.send(`save=${JSON.stringify(localStorage)}&key=${getCookie("user")}`);
    });
});