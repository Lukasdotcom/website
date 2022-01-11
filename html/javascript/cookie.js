$(document).ready(function() {
    if (localStorage.cookie_accept != "true") {
        let div = document.createElement('div');
        div.className = "popup";
        div.id = "cookie_popup";
        div.innerHTML = `<div class='popup-content'>
                            <h1>Cookie Policy</h1>
                            <p>This website uses cookies to function properly. Currently all cookies are strictly functional or to save your preferences locally. Your preferences will never be saved on our server. This also uses Cloudflare's CDN. If you want to see exactly what the cookies are used for <a href="https://github.com/Lukasdotcom/website" target="_blank" rel="noopener noreferrer" >click here</a> to see the source code of the website.</p>
                            <button id='accept_cookies'>Click here to Accept Cookies</button>
                            <button id='decline_cookies'>Click here to decline cookies and go back to previous page</button>
                        </div>`;
        $("body").append(div);
        $("#cookie_popup").show();
        $("#accept_cookies").click(function() {
            $("#cookie_popup").hide();
            localStorage.cookie_accept = true;
        })
        $("#decline_cookies").click(function() {
            window.history.go(-1)
        })
    }
})