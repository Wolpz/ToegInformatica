<!DOCTYPE html>
<html>
<head>
    <title>RPS</title>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
</head>

<body>
<h1>ROCK PAPER SCISSORS</h1>
<form id="choiceform" action="rps_handler.php" method="POST" enctype="multipart/form-data">
    <button type="submit" name="selectedOption" value="rock">Rock</button>
    <button type="submit" name="selectedOption" value="paper">Paper</button>
    <button type="submit" name="selectedOption" value="scissors">Scissors</button>
</form>

<div id="player"></div>
<div id="vs">VS</div>
<div id="computer"></div>
<div id="result"></div>

<script type="text/javascript">
    const options = ['rock', 'paper', 'scissors'];

    $(document).ready(function () {
        $('#choiceform').on('submit', function (e) {
            e.preventDefault();
            $.ajax({
                url: $(this).attr('action'),
                type: "POST",
                dataType: 'json',
                encode: true,
                data: $(this).serialize(),

                success: function (json) {
                    if (json)
                        console.log(json);

                    document.getElementById('result').innerHTML = json.result;
                    document.getElementById('player').innerHTML = json.player;
                    document.getElementById('computer').innerHTML = json.computer;
                },
                error: function (jXHR, textStatus, errorThrown) {
                    alert(errorThrown);
                }
            });
        });
    });
</script>

</body>
</html>