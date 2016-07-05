function getURLParameter(name) {
    return decodeURI(
        (RegExp('[?|&]' + name + '=' + '(.+?)(&|$)').exec(location.search)||[,null])[1]
    );
}

function insertParam(key, value) {

    kpv = addURLParameter(document.location.search.substr(1), [[key, value]]);

    //this will reload the page, it's likely better to store this until finished
    document.location.search = kpv; 
}

function addURLParameter(url, arr) {
    var kvp = url.split('&');
    var i= kvp.length; 
    var x;

    for (var pa=0; pa<arr.length;pa++){
        i= kvp.length; 
        key = escape(arr[pa][0]); 
        value = escape(arr[pa][1]);

        while(i--) {
            x = kvp[i].split('=');

            if (x[0] == key) {
                    x[1] = value;
                    kvp[i] = x.join('=');
                    break;
            }
        }

        if(i<0) {kvp[kvp.length] = [key,value].join('=');}
    }

    return kvp.join('&');
} 
