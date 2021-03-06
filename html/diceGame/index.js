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
            maxDice = permaMaxDice;
            diceAmount = permaDiceAmount;
            reset += 1;
            bonus = 0;
        }
    } else if (type == 3) { // Used to buy a perma die
        if (dice[value][1] > points || purchased) {
            alert("Something went wrong you can not purchase this.");
        } else {
            purchased = true;
            points -= dice[value][1];
            permaDiceAmount += lastElement(dice[value]);
            diceAmount += lastElement(dice[value]);
            let max = lastElement(lastElement(Object.values(dice)))*2;
        }
    } else if (type == 4) { // Used to upgrade perma max dice
        if (maxDiceCost[value][1] > points || purchased) {
            alert("Something went wrong you can not purchase this.");
        } else {
            purchased = true;
            points -= maxDiceCost[value][1];
            permaMaxDice += 1;
            if (permaMaxDice > maxDice) {
                maxDice = permaMaxDice;
            }
        }
    } else if (type == 5) { // Used to purchase rolls of the winner die
        if ((superRollCost * value) > points || purchased) {
            alert("Something went wrong you can not purchase this.");
        } else {
            purchased = true;
            points -= value * superRollCost;
            superRoll(value);
        }
    } else if (type == 6) { // Used to purchase a bonus
        if ((bonusCost * value) > points || purchased) {
            alert("Something went wrong you can not purchase this.");
        } else {
            purchased = true;
            points -= value * bonusCost;
            bonus += value;
        }
    }
    let max = lastElement(lastElement(Object.values(dice)))*2;
    while (diceAmount > (max)-1) {
        diceAmount -= max;
        permaDiceAmount += 4;
    }
    while (permaDiceAmount > (max)-1) {
        permaDiceAmount -= max;
        superRoll(1);
    }
    updateLayout();
}
function superRoll(rollsLeft) { // Roll a 20 sided die to be able to win the game
    $("#winGameRoll").text('Roll 20 sided die');
    $("#winGameRollsLeft").text(rollsLeft);
    rollsLeft -= 1;
    $(`#winGameText`).html(``);
    $("#winGame").show();
    $("#winGameRoll").click(function() {
        let guess = parseInt($("#guess").val());
        $("#winGameRoll").off("click");
        $("#winGameRoll").text('Stop roll');
        $(`#winGameText`).html(`The die rolled <c id='winGameRollResult'></c>.`);
        let rollInterval = setInterval(function() {
            $(`#winGameRollResult`).text(randomInt(1, 20));
        },40);
        $("#winGameRoll").click(function () {
            $("#winGameRoll").off("click");
            clearInterval(rollInterval);
            if (guess == parseInt($(`#winGameRollResult`).text())) {
                $(`#winGameText`).html(`<h3 id='winMessage'>Correct guess, You have won!</h3>${$(`#winGameText`).html()}`);
                $('#winMessage').effect("bounce", { times: 5, distance: 40 }, "slow");
                $("#winGameRoll").text('Restart game and play again');
                $("#winGameRoll").click(function () {
                    $("#winGameRoll").off("click");
                    $("#winGame").hide();
                    completeReset();
                })
            } else if (rollsLeft > 0) {
                $(`#winGameText`).html(`</p>Wrong guess. :(</p>${$(`#winGameText`).html()}`);
                $("#winGameRoll").text('Try Again');
                $("#winGameRoll").click(function () {
                    $("#winGameRoll").off("click");
                    superRoll(rollsLeft);
                })
            } else {
                $(`#winGameText`).html(`</p>Wrong guess. :(</p>${$(`#winGameText`).html()}`);
                $("#winGameRoll").text('close');
                $("#winGameRoll").click(function () {
                    $("#winGameRoll").off("click");
                    $("#winGame").hide();
                })
            }
        })
        
    })
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
    // Does the dice shop
    let text = "";
    let ownedDice = diceCounter(diceAmount);
    let counter = 1;
    Object.keys(dice).forEach(function(value) {
        text += `<p>${value} sided die. You have ${ownedDice[value]}. `;
        if (purchased) {
            text += `<button class='grayed'>You already bought something.</button></p>`;
        } else if (dice[value][0] <= points) {
            text += `<button onClick = 'purchase(${value}, 0)'>Buy for ${beautify(dice[value][0])}</button></p>`;
        } else {
            text += `<button class='grayed'>You need ${beautify(dice[value][0])} points to buy</button></p>`;
        }
        counter *= 2;
    });
    if (maxDiceCost[maxDice+1] !== undefined) {
        text += `<p>Upgrade max dice from ${maxDice} to ${maxDice + 1}. `;
        if (purchased) {
            text += `<button class='grayed'>You already bought something.</button></p>`;
        } else if (maxDiceCost[maxDice+1][0] <= points) {
            text += `<button onClick = 'purchase(${maxDice+1}, 1)'>Buy for ${beautify(maxDiceCost[maxDice+1][0])}</button></p>`;
        } else {
            text += `<button class='grayed'>You need ${beautify(maxDiceCost[maxDice+1][0])} points to upgrade.</button></p>`;
        }
    } else {
        text += `<p>Max dice ${maxDice} is the max.</p>`;
    }
    $("#diceShop").html(text);
    // Does the perma shop
    text = '';
    ownedDice = diceCounter(permaDiceAmount);
    Object.keys(dice).forEach(function(value) {
        text += `<p>${value} sided permanent die. You have ${ownedDice[value]}. `;
        if (purchased) {
            text += `<button class='grayed'>You already bought something.</button></p>`;
        } else if (dice[value][1] <= points) {
            text += `<button onClick = 'purchase(${value}, 3)'>Buy for ${beautify(dice[value][1])}</button></p>`;
        } else {
            text += `<button class='grayed'>You need ${beautify(dice[value][1])} points to buy</button></p>`;
        }
        counter *= 2;
    });
    if (maxDiceCost[permaMaxDice+1] !== undefined) {
        text += `<p>Upgrade perma max dice from ${permaMaxDice} to ${permaMaxDice + 1}. `;
        if (purchased) {
            text += `<button class='grayed'>You already bought something.</button></p>`;
        } else if (maxDiceCost[permaMaxDice+1][1] <= points) {
            text += `<button onClick = 'purchase(${permaMaxDice+1}, 4)'>Buy for ${beautify(maxDiceCost[permaMaxDice+1][1])}</button></p>`;
        } else {
            text += `<button class='grayed'>You need ${beautify(maxDiceCost[permaMaxDice+1][1])} points to upgrade.</button></p>`;
        }
    } else {
        text += `<p>Perma max dice ${permaMaxDice} is the max.</p>`;
    }
    $("#reset").html(text);
    // Updates the points
    $('#points').text(beautify(points));
    // Updates the other shop
    text = `<p>Reset level at ${reset} or x${beautify(2 ** reset)}. `;
    if (resetCost[reset+1] !== undefined) {
        if (purchased) {
            text += `<button class='grayed'>You already bought something.</button></p>`;
        } else if (resetCost[reset] <= points) {
            text += `<button onClick = 'purchase(${reset}, 2)'>Reset for ${beautify(resetCost[reset])}</button></p>`;
        } else {
            text += `<button class='grayed'>You need ${beautify(resetCost[reset])} points to reset.</button></p>`;
        }
    } else {
        text += `You can not reset anymore.</p>`;
    }
    text += `<p>Buy a bonus that gives you points every turn. You are at a +${beautify(bonus)} bonus. `;
    if (purchased) {
        text += `<button class='grayed'>You already bought something.</button></p>`;
    } else if (bonusCost <= points) {
        let purchaseAmount = Math.floor(points / bonusCost);
        text += `<button onClick = 'purchase(${purchaseAmount}, 6)'>Purchase ${beautify(purchaseAmount)} bonus points for ${beautify(purchaseAmount * bonusCost)}</button></p>`;
    } else {
        text += `<button class='grayed'>You need ${beautify(bonusCost)} points to buy a bonus.</button></p>`;
    }
    text += '<p>Buy a roll with the winning die. ';
    if (purchased) {
        text += `<button class='grayed'>You already bought something.</button></p>`;
    } else if (superRollCost <= points) {
        let purchaseAmount = Math.floor(points / superRollCost);
        text += `<button onClick = 'purchase(${purchaseAmount}, 5)'>Roll dice ${beautify(purchaseAmount)} time(s) for ${beautify(purchaseAmount * superRollCost)}</button></p>`;
    } else {
        text += `<button class='grayed'>You need ${beautify(superRollCost)} to roll the die.</button></p>`;
    }
    $('#otherShop').html(text);
    // Saves the game
    save();
}
superRollCost = 1000;
bonusCost = 10;
multiplier = { // list of multipliers for the amount of dices that are the same; doubles, triples, etc.
    1 : [1, "Single"],
    2 : [1.5, "Double"],
    3 : [2, "Triple"],
    4 : [4, "Quadruple"],
    5 : [8, "Quintuple"],
    6 : [16, "Sextuple"],
    7 : [32, "Septuple"],
    8 : [64, "Octuple"],
    9 : [128, "Nonuple"],
    10 : [256, "Decuple"],
    11 : [512, "Undecuple"],
    12 : [1024, "Duodecuple"],
    13 : [2048, "Tredecuple"],
    14 : [4096, "Quattuordecuple"],
    15 : [8192, "Quindecuple"],
    16 : [16384, "Sexdecuple"],
    17 : [32768, "Septendecuple"],
    18 : [65536, "Octodecuple"],
    19 : [131072, "Novemdecuple"],
    20 : [262144, "Vigintuple"]
}
maxDiceCost = { } // The cost of the maximum amount of dice.
dice = { } // A list of information for the dices
current_max_dice_cost = 0;
current_dice_cost = 0;
for (let i=1; i<=Object.keys(multiplier).length; i++) {
    current_max_dice_cost += i * 2;
    if (i>1) {
        maxDiceCost[i] = [current_max_dice_cost, current_max_dice_cost*5];
    }
    current_dice_cost += i;
    dice[i * 2] = [current_dice_cost, current_dice_cost*5];
}
resetCost = {} // Stores the cost of a reset
for (let i=0; i<=50; i++) {
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
    permaDiceAmount = 1; // Used to store what dice the user gets at reset
    points = 0; // the amount of points the user has
    purchased = false; // if the user has purchased something this turn
    maxDice = 1; // Stores the max amount of dice
    permaMaxDice = 1; // Stores the max amount of dice at reset
    bonus = 0; // The bonus points given every turn
    $("#diceRolls").text(0); // Resets the dice rolls
    $("#rollResult").html('');
    updateLayout();
}
function save() { // Saves the game
    localStorage.reset = reset;
    localStorage.diceAmount = diceAmount;
    localStorage.maxDice = maxDice;
    localStorage.permaMaxDice = permaMaxDice;
    localStorage.diceRolls = $("#diceRolls").text();
    localStorage.permaDiceAmount = permaDiceAmount;
    localStorage.bonus = bonus;
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
        permaMaxDice = parseInt(localStorage.permaMaxDice); // Stores the max amount of dice at reset
        permaDiceAmount = parseInt(localStorage.permaDiceAmount); // Used to store what dice the user gets at reset
        bonus = parseInt(localStorage.bonus); // The bonus points given every turn
        $("#diceRolls").text(parseInt(localStorage.diceRolls)); // Stores the amount of dice rolls
        updateLayout();
    }
    $("#roll").click(function () {  // Used to roll the die
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
        numbers.sort().reverse();
        total_multiplier = 1;
        multiplier_text = "";
        numbers.forEach(function(value) {
            points *= multiplier[value][0];
            if (multiplier[value][0] > 1) {
                if (multiplier_text) {
                    multiplier_text += `, ${multiplier[value][1]}`;
                } else {
                    multiplier_text += `${multiplier[value][1]}`;
                }
            }
            total_multiplier *= multiplier[value][0];
        })
        numbers = numbers[0];
        multiplier_text = `Multiplier x${beautify(total_multiplier)}! You got a ${multiplier_text}!`;
        points *= 2 ** reset;
        points = Math.floor(points);
        points += bonus;
        let text = "";
        $("#multiplier").text('')
        Object.keys(rollResult).forEach(function(value) {
            text += `<p>${value} sided die rolled <c id='${value}sidedResult'></c>.</p>`;
        });
        text += `<p>x${beautify(2 ** reset)} reset multiplier.</p>`;
        text += `<p>+${beautify(bonus)} points bonus.</p>`;
        text += `<button id='stopRoll'>Stop roll</button>`;
        $("#rollResult").html(text);
        text += `<p>You have <c id='points'></c> points.</p>`
        let rollInterval = setInterval(function() {
            Object.keys(rollResult).forEach(function(value) {
                $(`#${value}sidedResult`).text(randomInt(1, parseInt(value)));
            });
        },40)
        $("#stopRoll").click(function() { // Used to stop the roll and add the points for the roll
            $("#stopRoll").off("click")
            clearInterval(rollInterval);
            $("#points").text(beautify(points));
            if (numbers > 1) {
                $("#multiplier").text(multiplier_text);
            }
            Object.keys(rollResult).forEach(function(value) {
                $(`#${value}sidedResult`).text(rollResult[value]);
            });
            $("#diceRolls").text(parseInt($("#diceRolls").text())+1);
            $("#roll").show();
            $("#stopRoll").hide();
            $('#multiplier').effect("bounce", { times: 5, distance: 40 }, "slow")
            updateLayout();
        })
        $("#roll").hide();
    });
});
