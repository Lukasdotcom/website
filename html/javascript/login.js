function login(username, password, signup) {  // Used to login/Signup and will redirect to the username with a succesful login
    const ajax = new XMLHttpRequest();
    ajax.onload = function() {
        if (ajax.status == 200) {
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
            document.getElementById("status").innerHTML = ajax.responseText;
        }
        }
    if (signup == true){
        type = 'signup';
    } else {
        type = 'login';
    }
    ajax.open("POST", "api.php");
    ajax.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    ajax.send(`login='${type}'&password='${password}'&username='${username}'`);
}