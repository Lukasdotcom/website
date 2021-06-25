function login(username, password, signup) {  // Used to login/Signup and will redirect to the username with a succesful login
    const ajax = new XMLHttpRequest();
    ajax.onload = function() {
        if (ajax.status == 200) {
            location.replace("/usermenu.php");
        } else {
            alert(ajax.responseText);
        }
        }
    if (signup == true){
        type = 'signup';
    } else {
        type = 'login';
    }
    console.log(1);
    ajax.open("POST", "api.php");
    ajax.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    ajax.send(`login='${type}'&password='${password}'&username='${username}'`);
}