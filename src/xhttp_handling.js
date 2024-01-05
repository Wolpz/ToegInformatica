function xhttp_GET(url, onComplete) {
    var xhttp = new XMLHttpRequest();
    xhttp.onreadystatechange = function() {
        if (this.readyState == 4 && this.status == 200) {
            onComplete(this);
        }
    };
    xhttp.open("GET", url, true);
    xhttp.send();
}