function search() {
    var progress = 0
    const ajax = new XMLHttpRequest();
    ajax.onload = function() {
        if (ajax.status == 200) {
            window.userPriv = JSON.parse(ajax.responseText);
            if (progress == 0) {
                progress = 1
            } else {
                search2(userPriv, requestUser)
            }
        } else {
            alert('An error ocured reloading.');
            window.location.reload();
        }
    }
    ajax.open("GET", `/api/user.php?type=view&key=${getCookie("user")}`);
    ajax.send();
    const ajax2 = new XMLHttpRequest();
    ajax2.onload = function() {
        if (ajax.status == 200) {
            window.requestUser = JSON.parse(this.responseText);
            if (progress == 0) {
                progress = 1
            } else {
                search2(userPriv, requestUser)
            }
        } else {
            alert('An error ocured reloading.');
            window.location.reload();
        }
    }
    user = $("#user").val();
    ajax2.open("GET", `/api/user.php?type=view&key=${getCookie("user")}&user=${user}`);
    ajax2.send();
    $("#header").text(`Privileges for ${user}`);
}
function search2(userPriv, requestUser) {
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
}
function save() {
    let checkboxes = $("#privilege").find("input[type=checkbox]");
    let text = `type=edit&key=${getCookie('user')}&user=${$("#user").val()}`;
    let checkboxLength = checkboxes.length;
    for (let i=0; i<checkboxLength;i++) {
        let check = checkboxes[i];
        text += `&${check.name}=${check.checked}`;
    }
    const ajax = new XMLHttpRequest();
    ajax.onload = function() {
        if (ajax.status == 200) {
            search()
        } else {
            alert('An error ocured reloading.');
            window.location.reload();
        }
        }
    ajax.open("POST", "/api/user.php");
    ajax.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    ajax.send(text);
}
$(document).ready(function() {
    search();
    $("#user").change(function() {search()});
    $("#save").click(function() {save()});
});