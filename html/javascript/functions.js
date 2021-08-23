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