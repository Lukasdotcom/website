let turnstile_token = "";
// Used to rended the turnstile
turnstileCb = function () {
    if (sitekey !== "") {
        turnstile.render('#challenge', {
            sitekey,
            theme: 'dark',
            callback: function(token) {
                turnstile_token = token;
            },
        });
    }
};
$(document).ready(function() {
    $("#login").button();
    $("#login").click(function() { // Used to login/Signup and will redirect to the user menu with a succesful login
        const ajax = new XMLHttpRequest();
        $("#login").button("disable")
        ajax.onload = function() {
            if (ajax.status == 200) {
                // This is for an event in matomo
                _paq.push(['trackEvent', 'User', 'Login', username]);
                // Will redirect to a certain place when neccessary
                let searchParams = new URLSearchParams(window.location.search);
                let redirect = searchParams.get('redirect');
                if (redirect) {
                    redirect = redirect.replace(/[^a-zA-Z0-9_.]/g, "");
                    redirect = redirect.replace(/[_]/g, "/");
                    location.replace(`/${redirect}`);
                } else {
                    location.replace("/usermenu/index.php");
                }
            } else {
                turnstile.reset();
                JQerror(ajax.responseText);
            }
            $("#login").button("enable")
            }
        var type
        if ($("#signup").prop("checked")){
            type = 'signup';
        } else {
            type = 'login';
        }
        ajax.open("POST", "/api/login.php");
        ajax.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
        var username = $("#username").val()
        ajax.send(`type='${type}'&password='${$("#password").val()}'&username='${username}'&token=${turnstile_token}`);
    });
});