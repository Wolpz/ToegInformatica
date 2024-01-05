<!DOCTYPE html>
<html>
<head>
    <title>CALCULATRON</title>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
</head>

<body>
<h1>CALCULATRON 9000</h1>
<form id="form" action="calculate.php" method="POST" enctype="multipart/form-data">
    <select id="mode" name="mode" onchange="fancyAssSelect()">
        <option value="sum">Add</option>
        <option value="subtraction">Subtract</option>
        <option value="multiplication">Multiply</option>
        <option value="division">Divide</option>
        <option value="square root">Square root</option>
        <option value="square">Square</option>
        <option value="factorial">Factorial</option>
        <option value="world domination">World Domination</option>
    </select>
    <input id="input_1" type="text" name="input_1" value="">
    <input id="input_2" type="text" name="input_2" value="">
    <input id="submit" type="submit" value="Calculate!">
</form>
<p id="output"></p>


<script type="text/javascript">
    function fancyAssSelect(){
        let select = document.getElementById('mode').value;

        document.getElementById('submit').value = "Calculate!";
        document.getElementById('submit').style.background = "";
        switch (select){
            case 'square root':
            case 'square':
            case 'factorial':
                document.getElementById('input_1').style.display = "";
                document.getElementById('input_2').style.display = "none";
                break;
            case 'world domination':
                document.getElementById('input_1').style.display = "none";
                document.getElementById('input_2').style.display = "none";
                document.getElementById('submit').value = "SUBMIT";
                document.getElementById('submit').style.background = "#f44336";
                break;
            default:
                document.getElementById('input_1').style.display = "";
                document.getElementById('input_2').style.display = "";
        }
    }

    $(document).ready(function () {
        $('#form').on('submit', function (e) {
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

                    document.getElementById('output').innerHTML = json.output;
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