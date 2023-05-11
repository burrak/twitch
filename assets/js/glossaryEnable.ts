function callAjax(url: string) {
    var xhr = new XMLHttpRequest();
    xhr.open("POST", url);
    xhr.send();
}

window.callAjax = callAjax;
