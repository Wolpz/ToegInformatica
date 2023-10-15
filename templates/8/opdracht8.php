<!DOCTYPE html>
<html>
<head>
    <title>Klok</title>
</head>
<body>
<p>Script start een klok:</p>

<p id="klokje"></p>

<script>
    var myVar=setInterval(function(){myTimer()},1000);

    function myTimer()
    {
        var d=new Date();
        document.getElementById("klokje").innerHTML=d.toLocaleTimeString();
    }
</script>

</body>
</html>