$(document).ready(function() {
    // Checks if the user has already played the game with a different complexity
    if (localStorage.firstOperationComplexity != undefined) {
        $("#firstOperationComplexity").val(localStorage.firstOperationComplexity)
    }
    firstOperation()
});
const negationLikelyhood = 0.2
const upperCaseLetters = ['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z'];
const logicOperations = ["v", "*", "→", "↔"]
const parenthesis = ["()", "[]", "{}"]
function generateTruthSentence(complexity) { // Used to generate a logic sentence
    let truthSentence = ""
    // The depth in parenthesis
    let parenthesisNumber = 0
    // Waits until the logic sentence is completed
    while (complexity > 0) {
        if (truthSentence == "") { // If the logic sentence has not been started it either generates a single leader or a simple sentence with 2 letters
            if (complexity == 1) {
                if (Math.random() < negationLikelyhood) {
                    truthSentence =  "~"
                }
                truthSentence += randomElement(upperCaseLetters)
                complexity -= 1
            } else {
                if (Math.random() < negationLikelyhood) {
                    truthSentence +=  "~"
                }
                truthSentence += "("
                if (Math.random() < negationLikelyhood) {
                    truthSentence +=  "~"
                }
                truthSentence += randomElement(upperCaseLetters) + randomElement(logicOperations)
                if (Math.random() < negationLikelyhood) {
                    truthSentence +=  "~"
                }
                truthSentence += randomElement(upperCaseLetters) + ")"
                complexity -= 2
            }
        } else {
            // If a truth sentence exists it is expanded with a randomly sized element
            let size = Math.ceil(Math.random() * complexity)
            if (parenthesisNumber < parenthesis.length-1) {
                parenthesisNumber ++
            }
            truthSentence += randomElement(logicOperations) + generateTruthSentence(size) + parenthesis[parenthesisNumber][1]
            truthSentence = parenthesis[parenthesisNumber][0] + truthSentence
            complexity -= size
        }
    }
    return truthSentence
}
var firstOperationSentence = ""
function firstOperation() {
    // Gets the complexity from the input
    let complexity = parseInt($("#firstOperationComplexity").val())
    if (complexity < 2) {
        complexity = 2
    } else if (complexity > 20) {
        complexity = 20
    }
    localStorage.firstOperationComplexity = complexity
    // Generates a sentence
    sentence = generateTruthSentence(complexity)
    let depth = 0
    trueSentence = ""
    // Finds where the answer for the sentence is and puts buttons on all the operators
    for (let index = 0; index < sentence.length; index++) {
        parenthesis.forEach(element => {
            if (sentence[index] == element[0]) {
                depth ++
            } else if (sentence[index] == element[1]) {
                depth --
            }
        })
        if (logicOperations.includes(sentence[index])) {
            trueSentence += `<a style='color:green' onClick='if(${depth == 1}) {alert("You are correct"); firstOperation()} else {alert("This is the wrong answer")}'>${sentence[index]}</a>`
        } else {
            trueSentence += sentence[index]
        }
    }
    $("#firstOperation").html(trueSentence)
}
