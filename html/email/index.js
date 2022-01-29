function render() {
    $("#render").html($("#body").val());
}

$(document).ready(function() {
    $("#send").button()
    setInterval(render, 500);
    $("#send").click(function() {
        const ajax = new XMLHttpRequest;
        $("#send").text("Sending");
        $("#send").button("disable");
        ajax.onload = function() {
            if (ajax.status != 200) {
                JQerror(this.responseText);
                $("#send").text("Failed To Send");
            } else {
                $("#send").text("Sent");
            }
            setTimeout(function() {$("#send").text("Send");$("#send").button("enable");}, 1000)
        }
        ajax.open("POST", `/api/mail.php`);
        ajax.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
        ajax.send(`mail=${encodeURI($("#reciever").val())}&senderName=${encodeURI($("#senderName").val())}&sender=${encodeURI($("#sender").val())}&subject=${encodeURI($("#subject").val())}&body=${encodeURI($("#body").val())}&key='${getCookie('user')}'`); 
    });
});