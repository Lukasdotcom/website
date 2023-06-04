const state = {
  board: [],
  hand: [],
  picked_hand_card: null,
  picked_board_card: null,
};
const history = [];
const arr = [];
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
function giveHand() {
  state.hand = [0, 0, 0].map(() => {
    return {
      number: Math.floor(Math.random() * 8) + 1,
      color: colors[Math.floor(Math.random() * colors.length)],
    };
  });
}
// Generate hand and empty board
for (let i = 0; i < rows; i++) {
  const add = [];
  for (let j = 0; j < cols; j++) {
    add.push(null);
    // add.push({
    //   number: Math.floor(Math.random() * 8) + 1,
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
    state.picked_hand_card = null;
    state.picked_board_card = null;
    giveHand();
    render_game(state);
  });
  render_game(state);
});
