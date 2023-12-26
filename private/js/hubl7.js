var xmlhttp;
if (window.XMLHttpRequest)
{// code for IE7+, Firefox, Chrome, Opera, Safari
    xmlhttp=new XMLHttpRequest();

    document.addEventListener('keydown', function(e) {
        if (e.keyCode == 123 || (e.ctrlKey && e.shiftKey && e.keyCode == 73)) {
            e.preventDefault();
        }
    });

}
else
{// code for IE6, IE5
    xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
}
xmlhttp.onreadystatechange=function()
{
    if (xmlhttp.readyState==4 && xmlhttp.status==200)
    {
        document.getElementById("attacksdiv").innerHTML=xmlhttp.responseText;
        eval(document.getElementById("ajax").innerHTML);
    }
}
xmlhttp.open("GET","ajax/hub.php?type=attacks",true);
xmlhttp.send();

function start()
{
    launch.disabled = true;
    var host=$('#host').val();
    var port=$('#port').val();
    var time=$('#time').val();
    var rpip=$('#rpip').val();
    var method=$('#method').val();
    var concurrents = $('#concurrents').val();
    document.getElementById("div").style.display="none";
    document.getElementById("image").style.display="inline";
    var xmlhttp;
    if (window.XMLHttpRequest)
    {// code for IE7+, Firefox, Chrome, Opera, Safari
        xmlhttp=new XMLHttpRequest();
    }
    else
    {// code for IE6, IE5
        xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
    }
    xmlhttp.onreadystatechange=function()
    {
        if (xmlhttp.readyState==4 && xmlhttp.status==200)
        {
            launch.disabled = false;
            document.getElementById("div").innerHTML=xmlhttp.responseText;
            document.getElementById("image").style.display="none";
            document.getElementById("div").style.display="inline";
            if (xmlhttp.responseText.search("success") != -1)
            {
                attacks();
                window.setInterval(ping(host),10000);
            }
        }
    }
    xmlhttp.open("GET","ajax/hub.php?type=start" + "&host=" + encodeURIComponent(host) + "&port=" + port + "$rpip=" + rpip + "&time=" + time + "&method=" + method + "&concurrents=" + concurrents,true);
    xmlhttp.send();
}

function renew(id)
{
    rere.disabled = true;
    document.getElementById("div").style.display="none";
    document.getElementById("image").style.display="inline";
    var xmlhttp;
    if (window.XMLHttpRequest)
    {// code for IE7+, Firefox, Chrome, Opera, Safari
        xmlhttp=new XMLHttpRequest();
    }
    else
    {// code for IE6, IE5
        xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
    }
    xmlhttp.onreadystatechange=function()
    {
        if (xmlhttp.readyState==4 && xmlhttp.status==200)
        {
            rere.disabled = false;
            document.getElementById("div").innerHTML=xmlhttp.responseText;
            document.getElementById("image").style.display="none";
            document.getElementById("div").style.display="inline";
            if (xmlhttp.responseText.search("success") != -1)
            {
                attacks();
                window.setInterval(ping(host),10000);
            }
        }
    }
    xmlhttp.open("GET","ajax/hub.php?type=renew" + "&id=" + id,true);
    xmlhttp.send();
}

function stop(id)
{
    st.disabled = true;
    document.getElementById("div").style.display="none";
    document.getElementById("image").style.display="inline";
    var xmlhttp;
    if (window.XMLHttpRequest)
    {// code for IE7+, Firefox, Chrome, Opera, Safari
        xmlhttp=new XMLHttpRequest();
    }
    else
    {// code for IE6, IE5
        xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
    }
    xmlhttp.onreadystatechange=function()
    {
        if (xmlhttp.readyState==4 && xmlhttp.status==200)
        {
            st.disabled = false;
            document.getElementById("div").innerHTML=xmlhttp.responseText;
            document.getElementById("image").style.display="none";
            document.getElementById("div").style.display="inline";
            if (xmlhttp.responseText.search("success") != -1)
            {
                attacks();
                window.setInterval(ping(host),10000);
            }
        }
    }
    xmlhttp.open("GET","ajax/hub.php?type=stop" + "&id=" + id,true);
    xmlhttp.send();
}

function attacks()
{
    document.getElementById("attacksdiv").style.display="none";
    document.getElementById("attacksimage").style.display="inline";
    var xmlhttp;
    if (window.XMLHttpRequest)
    {// code for IE7+, Firefox, Chrome, Opera, Safari
        xmlhttp=new XMLHttpRequest();
    }
    else
    {// code for IE6, IE5
        xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
    }
    xmlhttp.onreadystatechange=function()
    {
        if (xmlhttp.readyState==4 && xmlhttp.status==200)
        {
            document.getElementById("attacksdiv").innerHTML=xmlhttp.responseText;
            document.getElementById("attacksimage").style.display="none";
            document.getElementById("attacksdiv").style.display="inline-block";
            document.getElementById("attacksdiv").style.width="100%";
            eval(document.getElementById("ajax").innerHTML);
        }
    }
    xmlhttp.open("GET","ajax/hub.php?type=attacks",true);
    xmlhttp.send();
}

function adminattacks()
{
    document.getElementById("attacksdiv").style.display="none";
    document.getElementById("attacksimage").style.display="inline";
    var xmlhttp;
    if (window.XMLHttpRequest)
    {// code for IE7+, Firefox, Chrome, Opera, Safari
        xmlhttp=new XMLHttpRequest();
    }
    else
    {// code for IE6, IE5
        xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
    }
    xmlhttp.onreadystatechange=function()
    {
        if (xmlhttp.readyState==4 && xmlhttp.status==200)
        {
            document.getElementById("attacksdiv").innerHTML=xmlhttp.responseText;
            document.getElementById("attacksimage").style.display="none";
            document.getElementById("attacksdiv").style.display="inline-block";
            document.getElementById("attacksdiv").style.width="100%";
            eval(document.getElementById("ajax").innerHTML);
        }
    }
    xmlhttp.open("GET","ajax/hub.php?type=adminattacks",true);
    xmlhttp.send();
}