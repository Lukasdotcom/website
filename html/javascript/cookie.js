$(document).ready(function() {
    if (localStorage.cookies_accept != "true") {
        let div = document.createElement('div');
        div.className = "notification";
        div.id = "cookie_popup";
        div.innerHTML = `<h3>Cookie Policy</h3>
                        <p>This website uses cookies to function properly. Most cookies are strictly functional. This also uses Cloudflare's CDN. If you want to see exactly what the cookies are used for <a href="https://github.com/Lukasdotcom/website" target="_blank" rel="noopener noreferrer" >click here</a> to see the source code of the website.
                        This website also uses matomo you may disable matomo analytics by clicking this <a href="https://analytics.lschaefer.xyz/index.php?module=CoreAdminHome&action=optOut&language=en&backgroundColor=000000&fontColor=ffffff&fontSize=17px&fontFamily=" target="_blank" rel="noopener noreferrer" >link</a> and unchecking the check box or just declining nonfunctional cookies(second button).</p>
                        <button id='accept_cookies' style="font-size:19px">Click here to accept all cookies</button>
                        <div class="red"><button id='decline_cookies'>Click here to decline all non required cookies(only functional)</button>
                        <div class="red"><button id='decline_all_cookies'>Click here to decline all cookies and go back</button></div>`;
        $("body").append(div);
        $("#cookie_popup").show();
        $("#accept_cookies").button();
        $("#accept_cookies").click(function() {
            $("#cookie_popup").hide();
            localStorage.cookies_accept = true;
            _paq.push(['forgetUserOptOut']);
        })
        $("#decline_cookies").click(function() {
            $("#cookie_popup").hide();
            localStorage.cookies_accept = true;
            _paq.push(['optUserOut']);
        })
        $("#decline_all_cookies").click(function() {
            window.history.go(-1);
        })
    }
})
