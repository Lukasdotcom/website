const colors = ["red", "green", "blue", "yellow", "brown"];
const rows = 4;
const cols = 4;
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
  let add = 5;
  for (let i = 0; i < rows; i++) {
    let curr_num = object[i][0] ? object[i][0].number : 0;
    for (let j = 1; j < cols; j++) {
      if (object[i][j] === null) {
        if (j == cols - 1) {
          add += 5;
          ret += add;
        }
        continue;
      }
      if (object[i][j].number <= curr_num) {
        break;
      }
      if (j == cols - 1 && curr_num < object[i][j].number) {
        add += 5;
        ret += add;
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
if (typeof window === "undefined") {
  const args = process.argv.slice(2);
  console.log(JSON.stringify(calculate_score(JSON.parse(args[0]))));
}
