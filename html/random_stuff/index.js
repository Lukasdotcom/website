$(document).ready(function() {
  // Sets up all the buttons
  $( "#movie" ).button();
  $( "#movie" ).click(() => {
    if ($("#movie-icon")[0].className === "ui-icon ui-icon-caret-1-e") {
      $("#movie-icon")[0].className = "ui-icon ui-icon-caret-1-s";
      $("#movie-def").show();
    } else {
      $("#movie-icon")[0].className = "ui-icon ui-icon-caret-1-e";
      $("#movie-def").hide();
    }
  });

  $( "#word" ).button();
  $( "#word" ).click(() => {
    if ($("#word-icon")[0].className === "ui-icon ui-icon-caret-1-e") {
      $("#word-icon")[0].className = "ui-icon ui-icon-caret-1-s";
      $("#word-def").show();
    } else {
      $("#word-icon")[0].className = "ui-icon ui-icon-caret-1-e";
      $("#word-def").hide();
    }
  });

  $( "#acronym" ).button();
  $( "#acronym" ).click(() => {
    if ($("#acronym-icon")[0].className === "ui-icon ui-icon-caret-1-e") {
      $("#acronym-icon")[0].className = "ui-icon ui-icon-caret-1-s";
      $("#acronym-def").show();
    } else {
      $("#acronym-icon")[0].className = "ui-icon ui-icon-caret-1-e";
      $("#acronym-def").hide();
    }
  });

  $( "#date" ).button();
  $( "#date" ).click(() => {
    if ($("#date-icon")[0].className === "ui-icon ui-icon-caret-1-e") {
      $("#date-icon")[0].className = "ui-icon ui-icon-caret-1-s";
      $("#date-def").show();
    } else {
      $("#date-icon")[0].className = "ui-icon ui-icon-caret-1-e";
      $("#date-def").hide();
    }
  });
});