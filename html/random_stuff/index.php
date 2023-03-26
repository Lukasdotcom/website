<!DOCTYPE html>

<html dir="ltr" lang="en">

<head>
    <title>
        Random Stuff
    </title>
    <?php
    $DESCRIPTION = "A place that generates a random move with its plot, a random word with its definition, and a random acronym with its meaning.";
    require_once '../include/all.php';
    ?>
    <script type='text/javascript' src='index.js'></script>
</head>

<body>
    <?php
    include '../include/menu.php';
    echo "<div class='main'>";
    // Generates the correct options for the stream context
    $options = array(
      'http'=>array(
        'method'=>"GET",
        'header'=>"Accept-language: en\r\n" .
                  "Cookie: foo=bar\r\n" .  // check function.stream-context-create on php.net
                  "User-Agent: Mozilla/5.0 (iPad; U; CPU OS 3_2 like Mac OS X; en-us) AppleWebKit/531.21.10 (KHTML, like Gecko) Version/4.0.4 Mobile/7B334b Safari/531.21.102011-10-16 20:23:10\r\n" // i.e. An iPad 
      )
    );
    $context = stream_context_create($options);
    // Gets the config
    $config = file_get_contents("../config.json");
    $config = json_decode($config, true);
    // GETS THE RANDOM MOVIE
    $random_movies = dbRequest2("SELECT * FROM random_stuff WHERE type = 'movie' ORDER BY RAND() LIMIT 1");
    $skip = false;
    if (count($random_movies) > 0) {
      $random_movie = $random_movies[0]["word"];
      $random_movie_description = $random_movies[0]["definition"];
      // Only sometimes gets a new movie
      $count = round(log(dbRequest2("SELECT COUNT(*) FROM random_stuff WHERE type = 'movie'")[0]["COUNT(*)"]+1, 10))-1;
      if ($count > 0 && rand(0, $count) > 0) {
        $skip = true;
      }
    } else {
      $random_movie = "Movie not found";
      $random_movie_description = "Plot not found";
    }
    if (!$skip) {
      // Picks a random ID from 2 to 55555
      $random_id = rand(2, 55555);
      // Gets the random movie
      $random_movie_url = "https://api.themoviedb.org/3/movie/" . $random_id . "?api_key=" . $config["TMDBApiKey"];
      // Parses the JSON response
      try {
        $random_movie_json = file_get_contents($random_movie_url, false, $context);
        $random_movie_array = json_decode($random_movie_json, true);
        if (!$random_movie_array) {
          throw new Exception("Could not get movie");
        }
      } catch (Exception $e) {
        $random_movie_array = array();
      }
      // Gets the data for a movie
      if (array_key_exists("title", $random_movie_array) && array_key_exists("overview", $random_movie_array) && $random_movie_array["overview"] != "") {
          $random_movie = $random_movie_array["title"];
          $random_movie_description = $random_movie_array["overview"];
          // Adds data to cache if it doesn't exist
          if (count(dbRequest2("SELECT * FROM random_stuff WHERE type='movie' AND word=? AND definition=?", "*", [$random_movie, $random_movie_description])) == 0) {
            dbCommand("INSERT INTO random_stuff (type, word, definition) VALUES ('movie', ?, ?)", [$random_movie, $random_movie_description]);
          }
      }
    }
    // GETS THE RANDOM WORD AND DEFINITION
    $random_words = dbRequest2("SELECT * FROM random_stuff WHERE type = 'word' ORDER BY RAND() LIMIT 1");
    $skip = false;
    if (count($random_words) > 0) {
      $random_word = $random_words[0]["word"];
      $random_word_definition = $random_words[0]["definition"];
      // Only sometimes gets a new word
      $count = round(log(dbRequest2("SELECT COUNT(*) FROM random_stuff WHERE type = 'word'")[0]["COUNT(*)"]+1, 10))-1;
      if ($count > 0 && rand(0, $count) > 0) {
        $skip = true;
      }
    } else {
      $random_word = "Word not found";
      $random_word_definition = "Definition not found";
    }
    if (!$skip) {
      $random_word_url = "https://ydr-api.yourdictionary.com/words/random?limit=1";
      try {
        $random_word_json = file_get_contents($random_word_url, false, $context);
        $random_word_array = json_decode($random_word_json, true);
        if (!$random_word_array) {
          throw new Exception("Could not get word");
        }
      } catch (Exception $e) {
        $random_word_array = array();
      }
      if (array_key_exists("data", $random_word_array)) {
        $random_word_array = $random_word_array["data"];
        if (array_key_exists(0, $random_word_array)) {
          $random_word_array = $random_word_array[0];
          if (array_key_exists("slug", $random_word_array) && array_key_exists("def", $random_word_array)) {
            $random_word = $random_word_array["slug"];
            $random_word_definition = $random_word_array["def"];
            $random_word_definition = str_replace("<a>", "", $random_word_definition);
            $random_word_definition = str_replace("</a>", "", $random_word_definition);
            // Adds data to cache if it doesn't exist
            if (count(dbRequest2("SELECT * FROM random_stuff WHERE type='word' AND word=? AND definition=?", "*", [$random_word,  $random_word_definition])) == 0) {
              dbCommand("INSERT INTO random_stuff (type, word, definition) VALUES ('word', ?, ?)", [$random_word, $random_word_definition]);
            }
          }
        }
      }
    }
    // GETS THE RANDOM ACRONYM
    $random_acronyms = dbRequest2("SELECT * FROM random_stuff WHERE type = 'acronym' ORDER BY RAND() LIMIT 1");
    $skip = false;
    if (count($random_acronyms) > 0) {
      $random_acronym = $random_acronyms[0]["word"];
      $random_acronym_meaning = $random_acronyms[0]["definition"];
      // Only sometimes gets a new acronym
      $count = round(log(dbRequest2("SELECT COUNT(*) FROM random_stuff WHERE type = 'acronym'")[0]["COUNT(*)"]+1, 10))-1;
      if ($count > 0 && rand(0, $count) > 0) {
        $skip = true;
      }
    } else {
      $random_acronym = "Acronym not found";
      $random_acronym_meaning = "Meaning not found";
    }
    if (!$skip) {
      $random_acronym_url = "https://www.acronymfinder.com/random.aspx";
      $random_acronym_html = file_get_contents($random_acronym_url, false, $context);
      preg_match("/<h1 class=\"acronym__search acronym__search--big\">What does <strong>(.*)<\/strong> stand for\?<\/h1>/", $random_acronym_html, $random_acronym);
      $failure = !(count($random_acronym) > 0);
      if (count($random_acronym) > 0) {
        $random_acronym = $random_acronym[1];
      }
      preg_match("/<h2 class=\"acronym__title\"><strong>$random_acronym<\/strong> stands for (.*)<\/h2>/", $random_acronym_html, $random_acronym_meaning);
      $failure = $failure && !(count($random_acronym_meaning) > 0);
      if (count($random_acronym_meaning) > 0) {
        $random_acronym_meaning = $random_acronym_meaning[1];
      }
      if (!$failure) {
        // Adds data to cache if it doesn't exist
        if (count(dbRequest2("SELECT * FROM random_stuff WHERE type='acronym' AND word=? AND definition=?", "*", [$random_acronym, $random_acronym_meaning])) == 0) {
          dbCommand("INSERT INTO random_stuff (type, word, definition) VALUES ('acronym', ?, ?)", [$random_acronym, $random_acronym_meaning]);
        }
      }
    }
    // GETS THE RANDOM DATE
    $year = rand(1800, 2022);
    $month = rand(1, 12);
    $month = date("F", mktime(0, 0, 0, $month, 10));
    $random_date_url = "https://www.onthisday.com/events/date/" . $year . "/" . $month;
    $random_date_html = file_get_contents($random_date_url, false, $context);
    preg_match("/<article class=\"section no-padding-top\">(?s:.*)<\/article>/", $random_date_html, $random_date_array);
    preg_match_all("/<li class=\"event\">(.*)<\/li>/", $random_date_array[0], $event_list);
    // Matches all the simple events
    for ($i = 0; $i < count($event_list[1]); $i++) {
      preg_match("/((<a href=\"(.*)\">(.*)<\/a>)|(<b>(.*)<\/b>)) (.*)/", $event_list[1][$i], $event_data);
      $random_date = $event_data[4];
      if ($random_date == "") {
        $random_date = $event_data[6];
      }
      $random_date = $random_date . " " . $year;
      $random_date_description = $event_data[7];
      // Adds data to cache if it doesn't exist
      if (count(dbRequest2("SELECT * FROM random_stuff WHERE type='date' AND word=? AND definition=?", "*", [$random_date, $random_date_description])) == 0) {
        dbCommand("INSERT INTO random_stuff (type, word, definition) VALUES ('date', ?, ?)", [$random_date, $random_date_description]);
      }
    }
    // Picks a random event
    $random_dates = dbRequest2("SELECT * FROM random_stuff WHERE type = 'date' ORDER BY RAND() LIMIT 1");
    if (count($random_dates) > 0) {
      $random_date = $random_dates[0]["word"];
      $random_date_description = $random_dates[0]["definition"];
    } else {
      $random_date = "Date not found";
      $random_date_description = "Description not found";
    }
    ?>
      <h1>Random Stuff</h1>
      <h2 style="color:white;">Random Movie</h2>
      <p id="movie">
        <span id="movie-icon" class="ui-icon ui-icon-caret-1-e"></span>
        <?php echo htmlspecialchars($random_movie); ?>
      </p>
      <p id="movie-def" style="display:none;"><?php echo htmlspecialchars($random_movie_description); ?></p>

      <h2 style="color:white;">Random Word</h2>
      <p id="word">
        <span id="word-icon" class="ui-icon ui-icon-caret-1-e"></span>
        <?php echo htmlspecialchars($random_word); ?>
      </p>
      <p id="word-def" style="display:none;"><?php echo htmlspecialchars($random_word_definition); ?></p>

      <h2 style="color:white;">Random Acronym</h2>
      <p id="acronym">
        <span id="acronym-icon" class="ui-icon ui-icon-caret-1-e"></span>
        <?php echo htmlspecialchars($random_acronym); ?>
      </p>
      <p id="acronym-def" style="display:none;"><?php echo htmlspecialchars($random_acronym_meaning); ?></p>

      <h2 style="color:white;">Random Date</h2>
      <p id="date">
        <span id="date-icon" class="ui-icon ui-icon-caret-1-e"></span>
        <?php echo htmlspecialchars($random_date); ?>
      </p>
      <p id="date-def" style="display:none;"><?php echo $random_date_description; ?></p>
    </div>
</body>

</html>