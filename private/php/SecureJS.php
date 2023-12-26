<?php

function generateRandomString($length = 25) : string {
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $charactersLength = strlen($characters);
    $randomString = '';
    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[rand(0, $charactersLength - 1)];
    }
    return $randomString;
}

class SecureJS {
    private string $script;
    private string $name;

    /**
     * @throws Exception
     */
    function __construct($script) {
        $this->script = $script;
        $this->name = "_js" . generateRandomString(8);
    }

    function include() {
        // Set part count
        $parts = str_split(base64_encode($this->script), 4096 - (strlen($this->name) + 8));

        for ($i = 0; $i < count($parts); $i++) {
            setcookie("p" . $i . $this->name, $parts[$i], time() + 1, "/");
        }

        setcookie("pc" . $this->name, count($parts), time() + 1, "/");

        echo "<!-- SecureJS -->";
        echo "<script type='text/javascript'>";

        // getCookie(name)
        echo "const gc".$this->name."=(n)=>{";
        echo "let c=document.cookie.split(';');";
        echo "for(let i=0;i<c.length;i++){";
        echo "if(c[i].trim().startsWith(n+'".$this->name."=')){";
        echo "document.cookie=name+'".$this->name."=;path=/;expires='+new Date(0).toUTCString();";
        echo "return c[i].trim().substring(n.length+".strlen($this->name."=").");";
        echo "}";
        echo "}";
        echo "};";

        // JS Generation
        echo "let js".$this->name."='';";
        echo "let pc".$this->name."=parseInt(gc".$this->name."('pc'));";
        echo "for(let i=0;i<pc".$this->name.";i++){";
        echo "js".$this->name."+=gc".$this->name."('p'+i);";
        echo "}";

        // JS Execution
        echo "eval(atob(decodeURIComponent(js".$this->name.")));";

        echo "</script>";
    }
}
