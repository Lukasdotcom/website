const historicalState = {
  history: [],
  board: [],
  hash: "",
};
const leaderboard = {
  data: [],
  unique: [],
};
// Will show the historical game UI when needed and hide it when needed. This also downloads the historical game data.
async function render_historical() {
  await new Promise((res) => setTimeout(res, 2));
  const hash = window.location.hash.replace("#", "");
  if (hash === "") {
    $("#historicalGame").hide();
    $("title").text("Klumpy Leaderboard");
    return;
  }
  $("#historicalGame").show();
  const game_data = await fetch(`/api/klumpy.php?gameID=${hash}`).then((e) => {
    if (e.status === 200) {
      return e.json();
    } else {
      return null;
    }
  });
  if (game_data === null) {
    $("#historicalGame").hide();
    return;
  }
  historicalState.board = game_data.board;
  historicalState.history = JSON.parse(game_data.history);
  historicalState.hash = hash;
  $("#username").text(game_data.username);
  $("title").text("Game from " + game_data.username);
  $("#slider").slider({
    max: historicalState.history.length,
    change: update_view,
    value: historicalState.history.length,
  });
  update_view();
}
// This is used to upadte the historical game UI
function update_view() {
  if (window.location.hash.replace("#", "") !== historicalState.hash) {
    render_historical();
    return;
  }
  const value = $("#slider").slider("value");
  const state = {
    board: JSON.parse(historicalState.board),
    hand: historicalState.history[value]
      ? historicalState.history[value].hand
      : [],
    picked_hand_card: null,
    picked_board_card: null,
  };
  for (i = historicalState.history.length - 1; i >= value; i--) {
    state.board[historicalState.history[i].board[0]][
      historicalState.history[i].board[1]
    ] = null;
  }
  render(state);
}
function close_historicalGame() {
  window.location.hash = "";
  render_historical();
}
async function loadMore() {
  const showAll = $("#show_all").is(":checked");
  const data = await fetch(
    `/api/klumpy.php?search=${leaderboard.data.length + 10}`
  ).then((e) => e.json());
  leaderboard.data = data;
  for (const e of data) {
    if (
      leaderboard.unique.filter((b) => b.username === e.username).length === 0
    ) {
      leaderboard.unique.push(e);
    }
  }
  let html =
    `<table class="leaderboard" id="leaderboard"><th>Position</th><th>Name</th><th>Score</th><th>View</th>` +
    (showAll ? leaderboard.data : leaderboard.unique)
      .map((e, idx) => {
        return `<tr><td>${idx + 1}</td><td>${e.username}</td><td>${
          e.score
        }</td><td><a onclick="render_historical()" href="#${
          e.gameID
        }"><button>View</button></a></td></tr>`;
      })
      .join("") +
    `</table>`;
  $("#leaderboard").html(html);
}
$("documnet").ready(function () {
  $(".help").tooltip();
  render_historical();
  loadMore();
});
