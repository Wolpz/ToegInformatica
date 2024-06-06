<!DOCTYPE html>
<html>
<head>
    <title>GRAPHINATOR</title>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
    <script src="https://cdn.canvasjs.com/canvasjs.min.js"></script>
    <script type="text/javascript">
        function drawGraph(containerElementId, chartType, data){
            var chart = new CanvasJS.Chart(containerElementId, {
                title:{
                    text: "Charted for your pleasure"
                },
                data: [
                    {
                        type: chartType,
                        dataPoints: data
                    }
                ]
            });
            chart.render();
        }
    </script>
    
    <script>
        $(document).ready(function () {
            $('#id_graphSelection').on('submit', function (e) {
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

                        switch(document.getElementById("field_graphType").value){
                            case "lijn":
                                chartType = "line";
                                break;
                            case "taart":
                                chartType = "pie";
                                break;
                            case "staaf":
                                chartType = "column";
                                break;
                            default:
                                chartType = "line";
                        }

                        let graphData  = [];
                        for(let i = 1; i <= json.fileData.length; i++){
                            graphData.push({x: i, y: +json.fileData[i-1]});
                        }
                        console.log(graphData);

                        drawGraph("p_graphDisplay", chartType, graphData);
                        //document.getElementById("p_graphDisplay").innerHTML = chartType + graphData;
                    },
                    error: function (jXHR, textStatus, errorThrown) {
                        alert(errorThrown);
                    }
                });
            });
        });
    </script>

</head>

<body>
    <form id=id_graphSelection name=f_graphSelection method="post" action="ajax_getData.php" enctype="multipart/form-data">
            <select id="field_graphType" name="graphType">
                <option value="lijn">Lijngrafiek</option>
                <option value="taart">Taartgrafiek</option>
                <option value="staaf">Staafgrafiek</option>
            </select>
            <select id="field_fileSelect" name="fileSelect">
                <option value="1">File_1</option>
                <option value="2">File_2</option>
                <option value="3">File_3</option>
            </select>
            <input type="submit" value="Show!" name="sub">
    </form>

    <p id="p_graphDisplay"></p>

</body>
</html>