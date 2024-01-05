<!DOCTYPE html>
<html>
<head>
    <title>CALCULATRON</title>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
</head>

<body>

<form name=Data method="post">
		<select id="mySelect" name="calculate">
			<option value="lijn">Lijngrafiek</option>
			<option value="taart">Taartgrafiek</option>
			<option value="staaf">Staafgrafiek</option>
		</select>
		<select id="mSelect" name="ff">
			<option value="1">File_1</option>
			<option value="2">File_2</option>
			<option value="3">File_3</option>
		</select>
		<input type="submit" value="Show!" name="sub">
</form>

</body>
</html>