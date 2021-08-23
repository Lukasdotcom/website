function purchase(value, type) { // Used to purchase an upgrade
    if (type == 0) {
        if (dice[value][0] > points || purchased) { // Used to buy a die
            alert("Something went wrong you can not purchase this.");
        } else {
            purchased = true;
            points -= dice[value][0];
            diceAmount += lastElement(dice[value]);
        }
    } else if (type == 1) { // Used to upgrade max dice
        if (maxDiceCost[value][0] > points || purchased) {
            alert("Something went wrong you can not purchase this.");
        } else {
            purchased = true;
            points -= maxDiceCost[value][0];
            maxDice += 1;
        }
    } else if (type == 2) { // Used to reset
        if (resetCost[value] > points || purchased) {
            alert("Something went wrong you can not purchase this.");
        } else {
            purchased = true;
            points = 0;
            maxDice = 1;
            diceAmount = 1;
            reset += 1;
        }
    }
    updateLayout();
}
function diceCounter(diceNumber) { // Will return all the dice the player has according to a variable
    let ownedDice = JSON.parse(JSON.stringify(dice));
    Object.keys(dice).forEach(function(value) {
        let amount = diceNumber % (lastElement(dice[value]) * 2);
        diceNumber -= amount;
        amount /= lastElement(dice[value]);
        ownedDice[value] = amount;
    });
    return ownedDice
}
function updateLayout() { // Will update the layout of the shop to make sure the information is up to date
    let text = "";
    let ownedDice = diceCounter(diceAmount);
    let counter = 1;
    Object.keys(dice).forEach(function(value) {
        text += `<p>${value} sided die. You have ${ownedDice[value]}. `;
        if (purchased) {
            text += `<button class='grayed'>You already bought something.</button></p>`;
        } else if (dice[value][0] <= points) {
            text += `<button onClick = 'purchase(${value}, 0)'>Buy for ${dice[value][0]}</button></p>`;
        } else {
            text += `<button class='grayed'>You need ${dice[value][0]} points to buy</button></p>`;
        }
        counter *= 2;
    });
    if (maxDiceCost[maxDice+1] !== undefined) {
        text += `<p>Upgrade max dice from ${maxDice} to ${maxDice + 1}. `;
        if (purchased) {
            text += `<button class='grayed'>You already bought something.</button></p>`;
        } else if (maxDiceCost[maxDice+1][0] <= points) {
            text += `<button onClick = 'purchase(${maxDice+1}, 1)'>Buy for ${maxDiceCost[maxDice+1][0]}</button></p>`;
        } else {
            text += `<button class='grayed'>You need ${maxDiceCost[maxDice+1][0]} points to upgrade.</button></p>`;
        }
    } else {
        text += `<p>Max dice ${maxDice} is the max.</p>`;
    }
    $("#diceShop").html(text);
    text = '';
    if (resetCost[reset+1] !== undefined) {
        text += `<p>Upgrade max dice from ${maxDice} to ${maxDice + 1}. `;
        if (purchased) {
            text += `<button class='grayed'>You already bought something.</button></p>`;
        } else if (resetCost[reset] <= points) {
            text += `<button onClick = 'purchase(${reset}, 2)'>Buy for ${resetCost[reset]}</button></p>`;
        } else {
            text += `<button class='grayed'>You need ${resetCost[reset]} points to reset.</button></p>`;
        }
    } else {
        text += `<p>You can not reset anymore.</p>`;
    }
    $("#reset").html(text);
    $('#points').text(points);
    save();
}
maxDiceCost = {
    2 : [4],
    3 : [12],
    4 : [24],
    5 : [36],
    6 : [45]
}
dice = { // A list of information for the dices
    4 : [3],
    6 : [4],
    8: [10],
    10: [16],
    12: [22],
    20: [28]
}
multiplier = { // list of multipliers for the amount of dices that are the same; doubles, triples, etc.
    1 : [1, "Single"],
    2 : [1.5, "Double"],
    3 : [2, "Triple"],
    4 : [3, "Quadruple"],
    5 : [5, "Quintuple"],
    6 : [8, "Sextuple"]
}
resetCost = {} // Stores the cost of a reset
for (let i=0; i<10; i++) {
    resetCost[i] = 12 * (4 ** i);
}
counter = 1;
Object.keys(dice).forEach(function(value) { // Adds the storage number for each die
    dice[value].push(counter);
    counter *= 2;
});
function completeReset() { // Completely restarts the game
    reset = 0; // Stores the mount of resets currently used
    diceAmount = 1; // Used to store what dice the user owns
    points = 0; // the amount of points the user has
    purchased = false; // if the user has purchased something this turn
    maxDice = 1; // Stores the max amount of dice
    $("#diceRolls").text(0); // Resets the dice rolls
    save();
    updateLayout();
}
function save() { // Saves the game
    localStorage.reset = reset;
    localStorage.diceAmount = diceAmount;
    localStorage.maxDice = maxDice;
    localStorage.diceRolls = $("#diceRolls").text();
}
$(document).ready(function() {  
    if (localStorage.reset == undefined) {
        completeReset();
    } else {
        reset = parseInt(localStorage.reset); // Stores the mount of resets currently used
        diceAmount = parseInt(localStorage.diceAmount); // Used to store what dice the user owns
        points = 0; // the amount of points the user has
        purchased = false; // if the user has purchased something this turn
        maxDice = parseInt(localStorage.maxDice); // Stores the max amount of dice
        $("#diceRolls").text(parseInt(localStorage.diceRolls));
        updateLayout();
    }
    $("#roll").click(function () {
        points = 0;
        purchased = false;
        let amount = diceCounter(diceAmount);
        let rollResult = {};
        let numbers = {};
        let diceLeft = maxDice
        Object.keys(amount).reverse().forEach(function(value) {
            if (amount[value] && diceLeft > 0) {
                let roll = randomInt(1, parseInt(value));
                rollResult[value] = roll;
                points += roll;
                if (numbers[roll]>0) {
                    numbers[roll] += 1;
                } else {
                    numbers[roll] = 1;
                }
                diceLeft -= 1;
            }
        });
        numbers = Object.values(numbers);
        numbers.sort();
        numbers = lastElement(numbers);
        points *= multiplier[numbers][0];
        points *= 2 ** reset;
        let text = ""
        if (numbers > 1) {
            text += `<h2 id='multiplier'>Multiplier x${multiplier[numbers][0]}! You got a ${multiplier[numbers][1]}!</h2>`;
        }
        Object.keys(rollResult).forEach(function(value) {
            text += `<p>${value} sided die rolled ${rollResult[value]}.</p>`
        });
        text += `<p>You have <c id='points'>${points}</c> points.</p>`
        $("#rollResult").html(text);
        $("#diceRolls").text(parseInt($("#diceRolls").text())+1);
        $('#multiplier').effect("bounce", { times: 5, distance: 40 }, "slow")
        updateLayout();
    });
});