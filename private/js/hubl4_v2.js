let stop;

(function() {
    function getById(id) {
        return document.getElementById(id);
    }

    function getVal(id) {
        return getById(id).value;
    }

    // Get attacks
    let xhr = new XMLHttpRequest();
    xhr.onreadystatechange = function() {
        if (this.readyState === this.DONE && this.status === 200) {
            getById("attacksdiv").innerHTML = this.responseText;

            eval(getById("ajax").innerHTML); // Safe ?
        }
    };
    xhr.open("GET", "ajax/hub.php?type=attacks", true);
    xhr.send();

    // Popup
    getById("popup").onclick = function() {
        let host = getVal("host");
        let time = getVal("time");

        let url = "ping.php?host=" + host + "&time=" + time;
        let popup = window.open(url, "Ping Popup", "width=400,height=400");

        setTimeout(function() {
            popup.close();
        }, time * 1_000);
    };

    // Attacks
    const attacks = function() {
        getById("attacksdiv").style.display = "none";
        getById("attacksimage").style.display = "inline";

        let xhr = new XMLHttpRequest();
        xhr.onreadystatechange = function() {
            if (this.readyState === this.DONE && this.status === 200) {
                getById("attacksdiv").innerHTML = this.responseText;
                getById("attacksimage").style.display = "none";
                getById("attacksdiv").style.display = "inline-block";
                getById("attacksdiv").style.width = "100%";

                eval(getById("ajax").innerHTML);
            }
        };
    };

    // Start
    getById("launch").onclick = function() {
        getById("launch").disabled = true;

        let host = getVal("host");
        let port = getVal("port");
        let time = getVal("time");
        let method = getVal("method");
        let concurrents = getVal("concurrents");

        getById("div").style.display = "none";
        getById("image").style.display = "inline";

        let xhr = new XMLHttpRequest();
        xhr.onreadystatechange = function() {
            if (this.readyState === this.DONE && this.status === 200) {
                getById("launch").disabled = true;

                getById("div").innerHTML = xhr.responseText;
                getById("image").style.display = "none";
                getById("div").style.display = "inline";

                if (this.responseText.search("success") !== -1) {
                    attacks();
                }
            }
        };
    }

    // Stop
    stop = function ()
})();

