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