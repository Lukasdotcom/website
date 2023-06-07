const state = {
  board: [],
  hand: [],
  picked_hand_card: null,
  picked_board_card: null,
};
const history = [];
const arr = [];
function numsToCard(arr) {
  let [a, b, c] = arr;
  if (a <= b) {
    b++;
  }
  if (a <= c) {
    c++;
  }
  if (b <= c) {
    c++;
  }
  return [
    { color: colors[Math.floor(a / 6)], number: (a % 6) + 1 },
    { color: colors[Math.floor(b / 6)], number: (b % 6) + 1 },
    { color: colors[Math.floor(c / 6)], number: (c % 6) + 1 },
  ];
}
let curr_seed = Math.floor(Date.now() / 1000 / 3600 / 24);
function prand(str) {
  let h1 = 1779033703,
    h2 = 3144134277,
    h3 = 1013904242,
    h4 = 2773480762;
  for (let i = 0, k; i < str.length; i++) {
    k = str.charCodeAt(i);
    h1 = h2 ^ Math.imul(h1 ^ k, 597399067);
    h2 = h3 ^ Math.imul(h2 ^ k, 2869860233);
    h3 = h4 ^ Math.imul(h3 ^ k, 951274213);
    h4 = h1 ^ Math.imul(h4 ^ k, 2716044179);
  }
  h1 = Math.imul(h3 ^ (h1 >>> 18), 597399067);
  h2 = Math.imul(h4 ^ (h2 >>> 22), 2869860233);
  h3 = Math.imul(h1 ^ (h3 >>> 17), 951274213);
  h4 = Math.imul(h2 ^ (h4 >>> 19), 2716044179);
  return [
    (h1 ^ h2 ^ h3 ^ h4) >>> 0,
    (h2 ^ h1) >>> 0,
    (h3 ^ h1) >>> 0,
    (h4 ^ h1) >>> 0,
  ];
}
function numToString(num) {
  let ret = "";
  while (num >= 0) {
    if (num % 100 <= 61) {
      ret = String.fromCharCode((num % 100) + 65) + ret;
    }
    num = Math.floor(num / 100) - 1;
  }
  return ret;
}
const upper_bound = prand(numToString(curr_seed))[1];
function adjust_seed(inxOfSquare, inxOfCardPlayed) {
  sqArray = [2, 3, 5, 7, 11, 13, 17, 19, 23, 29, 31, 37, 41, 43, 47, 53];
  cArray = [59, 61, 67];
  curr_seed *= sqArray[inxOfSquare] * cArray[inxOfCardPlayed];
  curr_seed %= upper_bound;
  curr_board = prand(numToString(curr_seed))[0];
  let c1 = curr_board % 30;
  let c2 = Math.floor(curr_board / 30) % 29;
  let c3 = Math.floor(curr_board / 870) % 28;
  return [c1, c2, c3];
}
function render_game(state) {
  if (state.picked_board_card !== null && state.picked_hand_card !== null) {
    $("#play").button("enable");
  } else {
    $("#play").button("disable");
  }
  const new_score = render(state);
  $("#winGamePoints").text(new_score[new_score.length - 1]);
  const game_finished =
    state.board.filter((e) => e.filter((e) => !e).length !== 0).length === 0;
  if (game_finished) {
    _paq.push(["trackEvent", "klumpy", "game_finished", new_score.join(",")]);
    fetch("/api/klumpy.php", {
      method: "POST",
      headers: {
        "Content-Type": "application/x-www-form-urlencoded",
      },
      body: `history=${JSON.stringify(history)}&board=${JSON.stringify(
        state.board
      )}&points=${new_score[new_score.length - 1]}&key=${getCookie(
        "user"
      )}&type=main`,
    }).then(async (e) => {
      if (e.status === 200) {
        const json_data = await e.json();
        $("#winGamePlace").text(json_data.position);
        $("#winGamePoints").text(json_data.points);
        if (json_data.error) {
          JQerror(json_data.message);
          $("#error").text(json_data.message);
        } else {
          $("#share").attr(
            "href",
            `/klumpy/leaderboard.php#${json_data.gameID}`
          );
          $("#share").show();
        }
      } else {
        console.error(
          "Failed to save game with unknown error: " + (await e.text())
        );
        JQerror("An unknown error occured");
      }
    });
    $("#winGame").show();
  }
}
// If one element that hand card is picked otherwise that board card is picked
function cellClicked(i, j) {
  if (j >= 0) {
    state.picked_board_card = [i, j];
  } else {
    state.picked_hand_card = i;
  }
  render_game(state);
}
function giveHand(indexOfSquare = -1, indexOfCardPlayed = -1) {
  if (indexOfSquare != -1 && indexOfCardPlayed != -1) {
    state.hand = numsToCard(adjust_seed(indexOfSquare, indexOfCardPlayed));
  } else {
    let curr_board = prand(numToString(curr_seed))[0];
    let c1 = curr_board % 30;
    let c2 = Math.floor(curr_board / 30) % 29;
    let c3 = Math.floor(curr_board / 870) % 28;
    state.hand = numsToCard([c1, c2, c3]);
  }
}
// Generate hand and empty board
for (let i = 0; i < rows; i++) {
  const add = [];
  for (let j = 0; j < cols; j++) {
    add.push(null);
    // add.push({
    //   number: Math.floor(Math.random() * 6) + 1,
    //   color: colors[Math.floor(Math.random() * colors.length)],
    // });
  }
  arr.push(add);
}
arr[0][0] = null;
state.board = arr;
giveHand();
$("document").ready(() => {
  $(".help").tooltip();
  $("#play").button();
  $("#play").click(() => {
    history.push({
      hand: state.hand,
      board: state.picked_board_card,
    });
    state.board[state.picked_board_card[0]][state.picked_board_card[1]] =
      state.hand[state.picked_hand_card];
    giveHand(
      state.picked_board_card[0] * 4 + state.picked_board_card[1],
      state.picked_hand_card
    );
    state.picked_hand_card = null;
    state.picked_board_card = null;
    render_game(state);
  });
  render_game(state);
});
