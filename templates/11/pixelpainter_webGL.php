<!DOCTYPE html>
<html>
<head>
    <title>Paint!</title>
    <link rel="stylesheet" href="../../styles/painter_styles.css">
    <script type="text/javascript" src="../../src/pixelpainter_webGL.js"></script>
    <script>
        document.addEventListener("DOMContentLoaded", (event) => {
            canvasID = "painter";
            clrCanvas(canvasID);
            drawTriangle(canvasID);
        });
    </script>
</head>

<body>
    <canvas id="painter" width="600px" height="600px"></canvas>
</body>
</html>