// Will render the UI based on a state and returns the score
function render(state) {
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
    new_score.push(new_score.reduce((a, b) => a + b, 0));
  }
  score.push(score.reduce((a, b) => a + b, 0));
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
  return score;
}
