<?php
require_once 'config.php';
$movies = $conn->query("SELECT * FROM movies")or die($conn->error);
$movieArray = array();
while($movie = $movies->fetch_assoc()){
    array_push($movieArray, $movie);
}


$players = $conn->query("SELECT * FROM players ORDER BY score DESC,duration LIMIT 10")or die($conn->error);


?>


<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link rel="stylesheet" href="bootstrap.min.css">
        <script src="bootstrap.bundle.min.js"></script>
        <style>
            #ui1 {
                height: 100vh;
            }
        </style>
        <title>TP2</title>
    </head>
    <body>
        <div class="container">
            <div class="row justify-content-center align-items-center" id="ui1">
                <div class="col-md-8">
                    <h4 class="text-center">Movie Quiz</h4>
                    <div class="input-group">
                        <button class="btn-btn-success" id="leaderboard">Leaderboard</button>
                        <input type="text" name="playername" id="playername" class="form-control">
                        <button class="btn btn-primary" id="playnow">Play Now!</button>
                    </div>
                    <div class="table-responsive d-none">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th scope="col">Date</th>
                                    <th scope="col">Player Name</th>
                                    <th scope="col">Score</th>
                                    <th scope="col">Duration</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php while ($player =$players->fetch_assoc()) {?>
                                <tr class="">
                                    <td scope="row"><?= $player['date'] ?></td>
                                    <td><?= $player['playername'] ?></td>
                                    <td><?= $player['score'] ?></td>
                                    <td><?= $player['duration'] ?> seconds</td>
                                </tr>
                                <?php } ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

        <div id="ui2" class="d-none">
        <div class="row justify-content-center mt-2">
            <div class="col-md-6">
                <div class="input-group">
                    <button class="btn btn-secondary ">Score</button>
                    <input type="text" class="form-control text-center score" name="score" id="score">
                    <button class="btn btn-success validate">Validate</button>
                </div>
            </div>
            </div>
            <div class="row justify-content-center mt-3">
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-body m-auto">
                            <img alt="img" class="img-fluid" id="image">
                        </div>
                    </div>
                </div>
            </div>
                <div class="col-md-8 m-auto mt-2">
                    <div class="row">
                        <div class="col-md-6">
                            <button class="form-control bg-success text-light choices" id="choiceA">Choice A</button>
                        </div>
                        <div class="col-md-6">
                        <button class="form-control bg-success text-light choices" id="choiceB">Choice B</button>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                        <button class="form-control bg-success text-light choices" id="choiceC">Choice C</button>
                        </div>
                        <div class="col-md-6">
                        <button class="form-control bg-success text-light choices" id="choiceD">Choice D</button>
                        </div>
                </div>
            </div>

        </div>
        <script src="jquery-3.6.1.min.js"></script>
        <script>
            $(document).ready(function () {
                let startTime = null;
                let playername = null;

                $('#leaderboard').click(function () {
                    $('.table-responsive').toggleClass('d-none');
                })
                $('#playnow').click(function () {
                    playername = $('#playername').val();
                    if (playername.length == 0) {
                        alert('Must input a player name');
                        return;
                    }
                    $('#ui2').removeClass('d-none');
                    $('#ui1').addClass('d-none');
                    startTime = Date.now();
                    console.log(startTime);
            })
            let movieArr = <?php echo json_encode($movieArray); ?>;
            movieArr.sort((a, b) => .5 - Math.random() );
            console.log(movieArr);
            let pointer = 0
            let score = 0;

            class Question{
                constructor(poster,answer,choices) {
                    this.poster = poster;
                    this.answer = answer;
                    this.choices = choices.sort((a, b) => .5 - Math.random());
                }
            }

            let questionBank = [];
            for (let i = 0; i < 10; i++) {
                let moviequestion = movieArr.pop()
	
	            let question = new Question(moviequestion.imdbID, moviequestion.Title,
	            [moviequestion.Title, movieArr[0].Title, movieArr[1].Title, movieArr[2].Title]);

                questionBank.push(question);
            }
            console.log(questionBank);

            showUi(pointer);

            $('.choices').click(function () {
                let value = this.value
                if (questionBank[pointer].answer == value) {
                    score++;
                    $('.score').val(score);
                    $('.validate').html('You are Correct');
                }else{
                    $('.validate').html('You are Wrong');
                }
                pointer++;
                if (pointer == questionBank.length) {
                    let timeEnd = Date.now();
                    let duration = Math.floor((timeEnd - startTime) / 1000);
                    var request = $.ajax({
                        url: "script.php",
                        method: "POST",
                        data: {
                            playername: playername,
                            score: score,
                            duration: duration
                        },
                        dataType: "html"
                    });

                    request.done(function(msg) {
                        alert(`Game Ended at ${duration}s, Your score is ${score}, Congratulations ${playername}`);
                        location.reload();
                    });

                    request.fail(function(jqXHR, textStatuS) {
                        alert("Request failed: " + TextStatus); 
                    });


                }
                showUi(pointer);
            })

            function showUi (pointer) {
                $('#image').attr('src', 'images/'+ questionBank[pointer].poster + '.jpg');
                $('#choiceA').val(questionBank[pointer].choices[0]).html(questionBank[pointer].choices[0]);
                $('#choiceB').val(questionBank[pointer].choices[1]).html(questionBank[pointer].choices[1]);
                $('#choiceC').val(questionBank[pointer].choices[2]).html(questionBank[pointer].choices[2]);
                $('#choiceD').val(questionBank[pointer].choices[3]).html(questionBank[pointer].choices[3]);
            }
        })
        </script>
    </body>
</html>