const colors = ["red", "green", "blue", "yellow", "brown"];
const rows = 4;
const cols = 4;
const state = {
  board: [],
  hand: [],
  picked_hand_card: null,
  picked_board_card: null,
};
const arr = [];
// Code until the next comment mentioned from this was made with help from Raj Tiller
function color_fitting_adjecent_squares(object, nums) {
  let i = nums[0];
  let j = nums[1];
  const ret = [];
  if (object[i][j]) {
    const desired_color = object[i][j].color;
    if (
      i + 1 < rows &&
      object[i + 1][j] &&
      object[i + 1][j].color == desired_color
    ) {
      ret.push([i + 1, j]);
    }
    if (i > 0 && object[i - 1][j] && object[i - 1][j].color == desired_color) {
      ret.push([i - 1, j]);
    }
    if (
      j + 1 < cols &&
      object[i][j + 1] &&
      object[i][j + 1].color == desired_color
    ) {
      ret.push([i, j + 1]);
    }
    if (j > 0 && object[i][j - 1] && object[i][j - 1].color == desired_color) {
      ret.push([i, j - 1]);
    }
  }
  return ret;
}

function arr_to_num(arr) {
  return arr[0] * 4 + arr[1];
}

function number_fitting_adjecent_squares(object, nums) {
  let i = nums[0];
  let j = nums[1];
  const desired_value = object[i][j].number + 1;
  let ret = [];
  if (
    i + 1 < rows &&
    object[i + 1][j] &&
    object[i + 1][j].number == desired_value
  ) {
    ret.push([i + 1, j]);
  }
  if (i > 0 && object[i - 1][j] && object[i - 1][j].number == desired_value) {
    ret.push([i - 1, j]);
  }
  if (
    j + 1 < cols &&
    object[i][j + 1] &&
    object[i][j + 1].number == desired_value
  ) {
    ret.push([i, j + 1]);
  }
  if (j > 0 && object[i][j - 1] && object[i][j - 1].number == desired_value) {
    ret.push([i, j - 1]);
  }
  return ret;
}

function clump_score(object) {
  const clumps = [];
  const inxs_done = [];
  for (let i = 0; i < rows; i++) {
    for (let j = 0; j < cols; j++) {
      if (object[i][j]) {
        curr_clump = [];
        if (inxs_done.indexOf(arr_to_num([i, j])) == -1) {
          curr_clump.push(arr_to_num([i, j]));
          inxs_done.push(arr_to_num([i, j]));
          let clumps_to_try = [[i, j]];
          while (clumps_to_try.length != 0) {
            let pot_new_squares = [];
            for (square of clumps_to_try) {
              to_add = color_fitting_adjecent_squares(object, square);
              if (to_add.length != 0) {
                pot_new_squares = pot_new_squares.concat(to_add);
              }
            }
            clumps_to_try = [];
            for (pot_square of pot_new_squares) {
              if (
                inxs_done.indexOf(arr_to_num(pot_square)) == -1 &&
                curr_clump.indexOf(arr_to_num(pot_square)) == -1
              ) {
                curr_clump.push(arr_to_num(pot_square));
                inxs_done.push(arr_to_num(pot_square));
                clumps_to_try.push(pot_square);
              }
            }
          }
        }

        if (curr_clump.length != 0) {
          clumps.push(curr_clump);
        }
      }
    }
  }
  let ret = 0;
  for (clump of clumps) {
    ret += clump.length * clump.length;
  }
  return ret;
}
function single_run_score(object) {
  let longest_run_len = 0;
  let lowest_value = 0;
  let highest_value;
  for (let i = 0; i < rows; i++) {
    for (let j = 0; j < cols; j++) {
      if (object[i][j]) {
        let curr_squares = [];
        curr_squares.push([i, j]);
        let pot_long_run_length = 0;
        let pot_low_val = object[i][j].number;
        let pot_high_val = object[i][j].number - 1;
        while (curr_squares.length != 0) {
          let to_add = [];
          for (const nums of curr_squares) {
            if (nums.length != 0) {
              const new_squares = number_fitting_adjecent_squares(object, nums);
              if (new_squares.length > 0) {
                to_add = to_add.concat(
                  number_fitting_adjecent_squares(object, nums)
                );
              }
            }
          }
          curr_squares = [...to_add];
          pot_high_val++;
          pot_long_run_length++;
        }
        if (
          pot_long_run_length > longest_run_len ||
          (pot_long_run_length == longest_run_len && pot_low_val < lowest_value)
        ) {
          lowest_value = pot_low_val;
          highest_value = pot_high_val;
          longest_run_len = pot_long_run_length;
        }
      }
    }
  }
  if (highest_value === undefined) {
    return 0;
  }
  return (9 - lowest_value) * (highest_value - lowest_value + 1);
}
function increasing_row_across(object) {
  let ret = 0;
  for (let i = 0; i < rows; i++) {
    let curr_num = object[i][0] ? object[i][0].number : 0;
    for (let j = 1; j < cols; j++) {
      if (object[i][j] === null) {
        if (j == cols - 1) {
          ret += 10;
        }
        continue;
      }
      if (object[i][j].number <= curr_num) {
        break;
      }
      if (j == cols - 1 && curr_num < object[i][j].number) {
        ret += 10;
      }
      curr_num = object[i][j].number;
    }
  }
  return ret;
}
function tot_sum(object) {
  let ret = 0;
  for (let i = 0; i < rows; i++) {
    for (let j = 0; j < cols; j++) {
      num = object[i][j] ? object[i][j].number : 0;
      ret += num;
    }
  }
  return Math.floor(ret / 2);
}
function all_numbers(object) {
  const nums = [];
  for (let i = 0; i < rows; i++) {
    for (let j = 0; j < cols; j++) {
      if (object[i][j]) {
        let curr_num = object[i][j].number;
        if (nums.indexOf(curr_num) == -1) {
          nums.push(curr_num);
        }
      }
    }
  }
  return nums.length * nums.length;
}
function calculate_score(object) {
  //object is an array of four arrays each of which is an array of four objects
  const clump_result = clump_score(object);
  // console.log("clump_result: ", clump_result);
  const single_run_result = single_run_score(object);
  // console.log("single_run_result: ", single_run_result);
  const increasing_row_across_result = increasing_row_across(object);
  // console.log("increasing_row_across_result: ", increasing_row_across_result);
  const tot_sum_result = tot_sum(object);
  // console.log("tot_sum_result: ", tot_sum_result);
  const all_numbers_result = all_numbers(object);
  // console.log("all_numbers_result: ", all_numbers_result);
  return [
    clump_result,
    single_run_result,
    increasing_row_across_result,
    tot_sum_result,
    all_numbers_result,
  ];
}
// End of code made with help from Raj Tiller
function render(state) {
  if (state.picked_board_card !== null && state.picked_hand_card !== null) {
    $("#play").button("enable");
  } else {
    $("#play").button("disable");
  }
  let board = state.board;
  const score = calculate_score(board);
  let new_score = score;
  if (state.picked_board_card !== null) {
    const new_temp_board = state.board.map((row_data, row) => {
      return row_data.map((a, col) => {
        if (
          row === state.picked_board_card[0] &&
          col === state.picked_board_card[1]
        ) {
          return state.hand[state.picked_hand_card];
        } else {
          return a;
        }
      });
    });
    new_score = calculate_score(new_temp_board);
  }
  score.push(score.reduce((a, b) => a + b, 0));
  new_score.push(new_score.reduce((a, b) => a + b, 0));
  const score_parts = [
    "#clump_score",
    "#single_run_score",
    "#increasing_row_across_score",
    "#tot_sum_scores",
    "#all_number_scores",
    "#score",
  ];
  score_parts.forEach((part, i) => {
    let text = score[i];
    if (new_score[i] > text) {
      text += ` <span style="color: green">+${new_score[i] - text}</span>`;
    } else if (new_score[i] < text) {
      text += ` <span style="color: red">-${text - new_score[i]}</span>`;
    }
    $(part).html(text);
  });
  let html = board
    .map((row, rowNum) => {
      return (
        "<tr>" +
        row
          .map((cell, rowCol) => {
            let highlight = "";
            let temp_card_text = "";
            let temp_card_class = "";
            if (
              state.picked_board_card &&
              rowNum === state.picked_board_card[0] &&
              rowCol === state.picked_board_card[1]
            ) {
              highlight = "highlight-td";
              if (state.picked_hand_card !== null) {
                temp_card_text = state.hand[state.picked_hand_card].number;
                temp_card_class =
                  state.hand[state.picked_hand_card].color + "-td";
              }
            }
            return cell
              ? `<td class="${cell.color}-td">${cell.number}</td>`
              : `<td onClick="cellClicked(${rowNum}, ${rowCol})" class="${highlight} ${temp_card_class}">${temp_card_text}</td>`;
          })
          .join("") +
        "</tr>"
      );
    })
    .join("");
  $("#game").html(html);
  let hand = state.hand;
  html =
    "<tr>" +
    hand.map((cell, index) => {
      let highlight = "";
      if (index == state.picked_hand_card) {
        highlight = "highlight-td";
      }
      return cell
        ? `<td onClick="cellClicked(${index})" class="${cell.color}-td ${highlight}">${cell.number}</td>`
        : `<td></td>`;
    }) +
    "</tr>";
  $("#hand").html(html);
}
// If one element that hand card is picked otherwise that board card is picked
function cellClicked(i, j) {
  if (j >= 0) {
    state.picked_board_card = [i, j];
  } else {
    state.picked_hand_card = i;
  }
  render(state);
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
  }
  arr.push(add);
}
state.board = arr;
giveHand();
$("document").ready(() => {
  $(".help").tooltip();
  $("#play").button();
  $("#play").click(() => {
    state.board[state.picked_board_card[0]][state.picked_board_card[1]] =
      state.hand[state.picked_hand_card];
    state.picked_hand_card = null;
    state.picked_board_card = null;
    giveHand();
    render(state);
  });
  render(state);
});
