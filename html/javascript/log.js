function search(term) {
    rows = document.getElementById('log').rows
    for(var i=0;i<rows.length-1;i++) {
        if (document.getElementById(`${String(i)}.message`).innerHTML.includes(term) && document.getElementById(document.getElementById(`${String(i)}.category`).innerHTML).checked) {
            document.getElementById(i).style.display = "";
        } else {
            document.getElementById(i).style.display = "none";
        }
      }
}
function remove(message, time, id) {
    const ajax = new XMLHttpRequest();
    ajax.onload = function() {
        if (ajax.status == 200) {
            document.getElementById(id).style.display = "none";
            document.getElementById(`${id}.category`).innerHTML = "deleted";
        } else {
            window.location.reload();
        }
    }
    console.log(id);
    ajax.open("POST", "api.php");
    ajax.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    ajax.send(`log=remove&message=${message}&time=${time}&key='${getCookie('user')}'`); 
}