function humanReadableTime(number) {
    let sec_num = parseInt(number, 10);
    let days    = Math.floor(sec_num / 86400);
    let hours   = Math.floor(sec_num / 3600);
    let minutes = Math.floor((sec_num - (hours * 3600)) / 60);
    let seconds = sec_num - (hours * 3600) - (minutes * 60);

    if (hours   < 10) {hours   = "0"+hours;}
    if (minutes < 10) {minutes = "0"+minutes;}
    if (seconds < 10) {seconds = "0"+seconds;}
    return days+':'+hours+':'+minutes+':'+seconds;
}
function getCookie(name) {
    // Split cookie string and get all individual name=value pairs in an array
    var cookieArr = document.cookie.split(";");
    
    // Loop through the array elements
    for(var i = 0; i < cookieArr.length; i++) {
        var cookiePair = cookieArr[i].split("=");
        
        /* Removing whitespace at the beginning of the cookie name
        and compare it with the given string */
        if(name == cookiePair[0].trim()) {
            // Decode the cookie value and return
            return decodeURIComponent(cookiePair[1]);
        }
    }
    
    // Return null if not found
    return null;
}

function randomInt(min, max) { // returns a random number between min (included) and max (included)
    max += 1;
    return Math.floor(Math.random() * (max - min) ) + min;
} 

function lastElement(array) { // returns the last element in an array
    return array[array.length - 1];
}


function beautify(val) // Will make a big number easier to read
{
    const shortcut = ["", "thousand", "million", "billion", "trillion", "quadrillion", "quintillion", "sextillion", "septillion", "octillion", "nonillion", "decillion"]
    const digits = (val, count = -1) => {
        if(val || count == -1){
            return digits(Math.floor(val / 10), ++count);
        }
        return count;
    };
	let length = digits(val);
	return `${Math.floor(val / (10**(length - (length % 3) - 3))) / 1000} ${shortcut[Math.floor(length / 3)]}`
}

function JQerror(text, length=10000) { // Used to display an error to the user
    let id = `error${randomInt(0, 100000000)}`;
    text = `<div id='${id}' class="ui-state-error ui-corner-all" style="padding: 0 .7em;">
                <p><span class="ui-icon ui-icon-alert" style="float: left; margin-right: .3em;"></span>
                <strong>Alert:</strong> ${text}</p>
            </div>`
    $(".main").prepend(text);
    setTimeout(function() {
        $(`#${id}`).remove()
    }, length)
}

function randomElement(array) { // Returns a random element in an array
    return array[Math.floor(Math.random() * array.length)]
}