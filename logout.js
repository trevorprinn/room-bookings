function logout(safeLocation, redirUrl){
    var outcome, u, m = "You should be logged out now.";
    // IE has a simple solution for it - API:
    try { outcome = document.execCommand("ClearAuthenticationCache") }catch(e){}
    // Other browsers need a larger solution - AJAX call with special user name - 'logout'.
    if (!outcome) {
        // Let's create an xmlhttp object
        outcome = (function(x){
            if (x) {
                // the reason we use "random" value for password is 
                // that browsers cache requests. changing
                // password effectively behaves like cache-busing.
                x.open("HEAD", safeLocation || location.href, true, "logout", (new Date()).getTime().toString())
                x.send("")
                // x.abort()
                return 1 // this is **speculative** "We are done." 
            } else {
                return
            }
        })(window.XMLHttpRequest ? new window.XMLHttpRequest() : ( window.ActiveXObject ? new ActiveXObject("Microsoft.XMLHTTP") : u ))
    }
    if (!outcome) {
        m = "Your browser is too old or too weird to support log out functionality. Close all windows and restart the browser."
    } else {
    	location = redirUrl;
 	 }
}
