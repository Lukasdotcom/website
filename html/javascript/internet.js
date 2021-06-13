function remove(test) {
    console.log(test);
}
function save(test) {
    document.getElementById("saveStatus").innerHTML = "Saving";
    const ajax = new XMLHttpRequest();
    
    ajax.onload = function() {
        document.getElementById("saveStatus").innerHTML = "Saved";
        setTimeout(() => { document.getElementById("saveStatus").innerHTML = ""; }, 2000);        
        }
      
    ajax.open("GET", "api.php?internet=edit&id=" + test + "&startHour=" + document.getElementById(test + '.startHour').value + "&startMinute=" + document.getElementById(test + '.startMinute').value + "&endHour=" + document.getElementById(test + '.endHour').value + "&endMinute=" + document.getElementById(test + '.endMinute').value + "&expire=" + document.getElementById(test + '.expire').value);
    console.log("api.php?internet=edit&id=" + test + "&startHour=" + document.getElementById(test + '.startHour').value + "&startMinute=" + document.getElementById(test + '.startMinute').value + "&endHour=" + document.getElementById(test + '.endHour').value + "&endMinute=" + document.getElementById(test + '.endMinute').value + "&expire=" + document.getElementById(test + '.expire').value);
    ajax.send(); 
}