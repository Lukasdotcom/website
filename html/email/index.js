function render() {
    // Explanation of how this securly renders the untrusted html input.
    // https://making.close.com/posts/rendering-untrusted-html-email-safely

    $("#render").attr("srcdoc", `
    <!DOCTYPE html>
    <html>
        <head>
            <meta http-equiv="Content-Security-Policy" content="script-src 'none'">
            <base target="_blank">
            <style>
                body {
                    background-color: black;
                    color: white;
                    font-family: 'Calibri';
                }
            </style>
        </head>
        <body>${$("#body").val()}</body>
    </html>`);
}

$(document).ready(function() {
    $("#send").button()
    render();
    $("#renderButton").click(render);
    $("#send").click(function() { // Used to send an email.
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