// Will find the category number for the id of a row in the log
function findCategory(category) {
    for(var i=0;i<typeLength;i++) {
        if (types[i]["name"] == category) {
            return i;
        }
    }
    return false
}
function search(term) {
    rows = document.getElementById('log').rows
    colors = []
    for(var i=0;i<typeLength;i++) {
        colors.push([document.getElementById(types[i]["name"]).checked, document.getElementById(`${types[i]["name"]}.color`).value]);
        document.getElementById(`${types[i]["name"]}.text`).style.color = document.getElementById(`${types[i]["name"]}.color`).value;
    }
    document.cookie= `log=${JSON.stringify(colors)}`;
    document.cookie = `logSearch=${term}`;
    for(var i=0;i<rows.length-1;i++) {
        if (document.getElementById(`${String(i)}.message`).innerHTML.includes(term) && document.getElementById(document.getElementById(`${String(i)}.category`).innerHTML).checked) {
            document.getElementById(i).style.display = "";
        } else {
            document.getElementById(i).style.display = "none";
        }
        document.getElementById(i).style.color = colors[findCategory(document.getElementById(`${String(i)}.category`).innerHTML)][1]
      }
}
function resetColor(searchTerm, term, id) {
    document.getElementById(`${term}.color`).value = types[id]["color"];
    search(searchTerm);
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
function collapseCategories() {
    if (getCookie("collapseCategories")) {
        document.getElementById(`collapseCategories`).innerHTML = "Uncollapse Categories";
        for(var i=0;i<typeLength;i++) {
            document.getElementById(`${types[i]["name"]}.text`).style.display = "none";
        }
        document.cookie = "collapseCategories=true";
    } else {
        document.getElementById(`collapseCategories`).innerHTML = "Collapse Categories";
        for(var i=0;i<typeLength;i++) {
            document.getElementById(`${types[i]["name"]}.text`).style.display = "";
        }
        document.cookie = "collapseCategories=";
    }
}