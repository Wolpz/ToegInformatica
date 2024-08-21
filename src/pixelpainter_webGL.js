function displayError(e){
    console.log(e);
}

function getGl2Canvas(elementID){
    const canvas = document.getElementById(elementID);
    if(!canvas){
        displayError("No canvas found.");
        return;
    }

    const gl = canvas.getContext('webgl2');
    if(gl == null){
        // Redo this later to show user or use webgl 1
        displayError("Error: WebGL 2 not supported in your browser.");
        return;
    }
    return gl;
}

function clrCanvas(elementID){
    gl = getGl2Canvas(elementID);

    gl.clearColor(0.9, 0.9, 0.9, 1);
    gl.clear(gl.COLOR_BUFFER_BIT | gl.DEPTH_BUFFER_BIT);
}

function drawTriangle(elementID){
    gl = getGl2Canvas(elementID);

    const triangleVertices = [
        // Top middle
        0.0, 0.5,
        // Bottom left
        -0.5, -0.5,
        // Bottom right
        0.5, -0.5
    ];
    const triangleGeoCpuBuffer = new Float32Array(triangleVertices);
    const triangleGeoBuffer = gl.createBuffer();
    gl.bindBuffer(gl.ARRAY_BUFFER, triangleGeoBuffer);
    gl.bufferData(gl.ARRAY_BUFFER, triangleGeoCpuBuffer, gl.STATIC_DRAW)
    gl.bindBuffer(gl.ARRAY_BUFFER, null);

    const vertexShaderSourceCode = `#version 300 es
        precision mediump float;
        
        in vec2 vertexPosition;
        
        void main() {
          gl_Position = vec4(vertexPosition, 0.0, 1.0);
        }`;

    const vertexShader = gl.createShader(gl.VERTEX_SHADER);
    gl.shaderSource(vertexShader, vertexShaderSourceCode);
    gl.compileShader(vertexShader);
    if (!gl.getShaderParameter(vertexShader, gl.COMPILE_STATUS)) {
        const errorMessage = gl.getShaderInfoLog(vertexShader);
        displayError(`Failed to compile vertex shader: ${errorMessage}`);
        return;
    }

    const fragmentShaderSourceCode = `#version 300 es
        precision mediump float;
        
        out vec4 outputColor;
        
        void main() {
          outputColor = vec4(0.294, 0.0, 0.51, 1.0);
        }`;

    const fragmentShader = gl.createShader(gl.FRAGMENT_SHADER);
    gl.shaderSource(fragmentShader, fragmentShaderSourceCode);
    gl.compileShader(fragmentShader);
    if (!gl.getShaderParameter(fragmentShader, gl.COMPILE_STATUS)) {
        const errorMessage = gl.getShaderInfoLog(fragmentShader);
        displayError(`Failed to compile fragment shader: ${errorMessage}`);
        return;
    }

    const helloTriangleProgram = gl.createProgram();
    gl.attachShader(helloTriangleProgram, vertexShader);
    gl.attachShader(helloTriangleProgram, fragmentShader);
    gl.linkProgram(helloTriangleProgram);
    if (!gl.getProgramParameter(helloTriangleProgram, gl.LINK_STATUS)) {
        const errorMessage = gl.getProgramInfoLog(helloTriangleProgram);
        displayError(`Failed to link GPU program: ${errorMessage}`);
        return;
    }


}