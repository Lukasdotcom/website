$(document).ready(function () {
  // Checks if the user has already played the game with a different complexity
  if (localStorage.firstOperationComplexity != undefined) {
    $("#firstOperationComplexity").val(localStorage.firstOperationComplexity);
  }
  firstOperation();
});
const negationLikelyhood = 0.2;
const upperCaseLetters = [
  "A",
  "B",
  "C",
  "D",
  "E",
  "F",
  "G",
  "H",
  "I",
  "J",
  "K",
  "L",
  "M",
  "N",
  "O",
  "P",
  "Q",
  "R",
  "S",
  "T",
  "U",
  "V",
  "W",
  "X",
  "Y",
  "Z",
];
const logicOperations = ["v", "*", "→", "↔"];
// Used to return the parenthesis of a certain size
function parenthesis(size) {
  if (size < 3) {
    return [
      ["(", ")"],
      ["[", "]"],
      ["{", "}"],
    ][size];
  } else {
    return [
      `<b style='font-size:${(size - 2) * 2 + 18}px'>(</b>`,
      `<b style='font-size:${(size - 2) * 2 + 18}px'>)</b>`,
    ];
  }
}
function generateTruthSentence(complexity) {
  // Used to generate a logic sentence
  // The depth in parenthesis
  if (complexity == 1) {
    // If the size should be one it generates one random letter
    return [
      Math.random() < negationLikelyhood ? "~" : "",
      randomElement(upperCaseLetters),
    ];
  } else {
    // Otherwise it will split this in 2 and recursivly generate a sentence
    let size = Math.ceil(Math.random() * (complexity - 1));
    let sentence1 = generateTruthSentence(size);
    let sentence2 = generateTruthSentence(complexity - size);
    let parenth = -1;
    if (sentence1.length == 5) {
      parenth = sentence1[1];
    }
    if (sentence2.length == 5) {
      if (parenth < sentence2[1]) {
        parenth = sentence2[1];
      }
    }
    parenth++;
    return [
      Math.random() < negationLikelyhood ? "~" : "",
      parenth,
      sentence1,
      randomElement(logicOperations),
      sentence2,
    ];
  }
}
var firstOperationSentence = "";
var firstOperationAttempts = 0;
function firstOperation() {
  let complexity = parseInt($("#firstOperationComplexity").val());
  if (complexity < 2) {
    complexity = 2;
  } else if (complexity > 1000) {
    complexity = 1000;
  }
  localStorage.firstOperationComplexity = complexity;
  // Checks if the game has already been played and if this was a completed game sends the event.
  if (firstOperationAttempts != 0) {
    _paq.push(
      [
        "trackEvent",
        "Truth Tree",
        `Complexity : ${localStorage.firstOperationComplexity}`,
      ],
      `Attempts : ${firstOperationAttempts}`
    );
    firstOperationAttempts = 0;
  }
  // Will generate the text for the truth sentence
  function generateSentenceText(sentence, first) {
    if (sentence.length == 2) {
      return `${sentence[0]}${sentence[1]}`;
    } else {
      return `${sentence[0]}${
        parenthesis(sentence[1])[0]
      }${generateSentenceText(
        sentence[2],
        false
      )}<a style='color:green' onClick='firstOperationAttempts ++;if(${first}) {alert("You are correct"); firstOperation()} else {alert("This is the wrong answer")}'> ${
        sentence[3]
      } </a>${generateSentenceText(sentence[4], false)}${
        parenthesis(sentence[1])[1]
      }`;
    }
  }
  // Generates a sentence
  $("#firstOperation").html(
    generateSentenceText(generateTruthSentence(complexity), true)
  );
}
