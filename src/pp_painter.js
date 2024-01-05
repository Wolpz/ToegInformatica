// TODO
// - Add undo/redo (see how much history you need)
// - Get a better/faster floodfill algorithm, recursive takes up a lot of browser memory making it slow
// - Update with opengl or something?

let defaultColour = "#FFFFFF"; // White
let currPaint = defaultColour;
let mode = "pixel";
let height = 16;
let width = 16;
const artBuf = Array(width*height).fill(defaultColour);
// TODO revamp everything so that it requires implicit setting of array to update

// Create event listeners
addEventListener('DOMContentLoaded', function(){
    colourPicker = document.querySelector("#colorPicker");
    colourPicker.value = defaultColour;
    colourPicker.addEventListener("change", updateCurrColour);
    painter_initialise();
});
// Initialise painter, resetting everything
function painter_initialise(){
    currPaint = defaultColour;
    mode = "pixel";
    painting_clear();
}
// Clear painting and image buffer
function painting_clear(){
    artBuf.fill(defaultColour);
    painting_update();
}
// Update full painting TODO TEST
function painting_update(){
    for(let id = 0; id < (width*height); id++){
        updatePixel(id);
    }
}
// Updates entry in buffer
function updateBuffer(id, colour){
    console.log("replaced field"+ id +' with ' + colour);
    artBuf[id] = colour;
}
// Updates colour of table field from buffer
function updatePixel(id) {
    document.getElementById(id).style.backgroundColor = artBuf[id];
}
// Updates currently selected colour value
function updateCurrColour() {
    currPaint = colourPicker.value;
    console.log(currPaint);
}
// Fills in pixel with colour id based on currently selected mode
function paint(id) {
    switch(mode){
        case "pixel":
            updateBuffer(id, currPaint);
            updatePixel(id);
            break;
        case "bucket":
            paintBucket(id, currPaint);
            break;
    }
}
// Updates mode variable with new tool and logs to console for debugging
function switchTool(tool){
    mode = tool;
    console.log(mode);
}
// Recursively fills adjacent squares based on colour of selected pixel tile
function paintBucket(id, newColour) {
    let oldColour = artBuf[id];
    console.log(oldColour);
    if(oldColour !== newColour){
        recursiveFill(id, oldColour, newColour);
    }
}
// Queue-based filling algorithm in + pattern, works with table with no risk of stack overflow TODO
function queueFill(id, OldColour){

}
// Recursive filling algorithm in + pattern TODO fix
function recursiveFill(id, oldColour, newColour){
    if(artBuf[id] === oldColour){
        updateBuffer(id, newColour);
        updatePixel(id);
        let id_int = parseInt(id);
        let w = id_int%width;
        let h = Math.floor(id_int/ width );
        console.log("replaced x: " + w + " y: " + h);
        if(h+1 < height )
            recursiveFill(id_int + height, oldColour);
        if(h-1 >= 0 )
            recursiveFill(id_int - height, oldColour);
        if(w-1 >= 0 )
            recursiveFill(id_int - 1, oldColour);
        if(w+1 < width )
            recursiveFill(id_int + 1, oldColour);
    }
    return;
}