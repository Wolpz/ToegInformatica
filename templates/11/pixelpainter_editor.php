<!--
TODO
Use table as raster instead of spacer
clear button
save option + button
-->

<!DOCTYPE html>
<html>
<head>
    <title>PixelPainter!</title>
    <!--  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>  -->
    <script src="../../src/pp_painter.js">
        <?php
            // Setting height and width
            $height = 16;echo 'let height='.$height;
            $width = 16;echo 'let width='.$width;
            $defaultColour = '#FFFF00';echo 'let defaultColour='.$defaultColour;
            $defaultMode = "pixel";echo 'let mode ='.$defaultMode;
        ?>
    </script>
    <link rel="stylesheet" href="../../styles/pp_styles.css">
</head>

<body>
<div id="paintingTableDiv">
    <div class="buttonBar">
        <button onclick="switchTool('pixel')" id="pixel">Pixel</button>
        <button onclick="switchTool('bucket')" id="bucket">Bucket</button>
    </div>
    <form action="/pp_display", method="POST">
        <table id="paintingTable">
            <?php
                for($y=0; $y<$height; $y++){
                    echo '<tr>';
                    for($x=0; $x<$width; $x++){
                        echo '<td id="'.($x + $y * $width).'" onclick="paint(this.id)" style="background-color:#FF0000">
                        </td>';
                    }
                    echo '</tr>';
                }
            ?>
        </table>
        <input type="text" name="title" placeholder="name your work!">
        <input type="submit" value="Finished!">
    </form>
</div>
<div id="colorPickerDiv">
    <label for="colourPicker">Colour:</label>
    <input type="color" id="colorPicker" value="#000000">
</div>

</body>
</html>