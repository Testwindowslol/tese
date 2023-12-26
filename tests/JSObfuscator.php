<?php

class JSObfuscator {
    private $js;
    private $symbols;

    function __construct($js) {
        $this->js = $js;
        $this->symbols = [
            "_a" => "document",
            "_b" => "function(a) {return _jso._a.getElementById(a);}",
            "_c" => [],
            "_d" => "window"
        ];
    }

    function addCustomSymbol($key, $value) {
        $this->symbols["_c"][$key] = $value;
    }

    function replaceSymbol($originalSymbol, $newSymbol) {
        $this->js = str_replace($originalSymbol, $newSymbol, $this->js);
    }

    function obfuscate() {
        $this->replaceSymbols();
        $this->addPreloadCode();

        $this->js = base64_encode($this->js);

        $this->js = str_replace("A", "$", $this->js);
        $this->js = str_replace("ZX", "*", $this->js);
        $this->js = str_replace("YW", "/", $this->js);
        $this->js = str_replace("gIC", "€", $this->js);
        $this->js = str_replace("IC", "-", $this->js);
        $this->js = str_replace("gXy", "(", $this->js);
        $this->js = str_replace("Xy", ")", $this->js);

        $this->js = str_split($this->js, rand(0, 16));
        $this->js = implode("+", $this->js);

        $this->js = "let __ = \"" . $this->js . "\";";

        $a = "__ = __.replaceAll(\"+\", \"\")";
        $a = $a . ".replaceAll(\")\", \"Xy\")";
        $a = $a . ".replaceAll(\"(\", \"gXy\")";
        $a = $a . ".replaceAll(\"-\", \"IC\")";
        $a = $a . ".replaceAll(\"€\", \"gIC\")";
        $a = $a . ".replaceAll(\"*\", \"ZX\")";
        $a = $a . ".replaceAll(\"/\", \"YW\")";
        $a = $a . ".replaceAll(\"$\", \"A\")";
        $a = $a . ".replaceAll(\",\", \"\");";

        $a = "eval(atob(\"" . base64_encode($a) . "\"));eval(atob(__));";
        $this->js = $this->js . $a;
        $this->js = base64_encode($this->js);
        $this->js = "eval(atob(\"" . $this->js . "\"));";

        return $this->js;
    }

    private function arrayToJS($array) : string {
        $output = "{";

        foreach ($array as $key => $value) {
            if (gettype($value) == "string") {
                $output = $output . $key . ":" . $value . ",";
            } else if (gettype($value) == "array") {
                $output = $output . $key . ":" . $this->arrayToJS($value) . ",";
            }
        }

        return $output . "}";
    }

    private function addPreloadCode() {
        $code = "_ = " . $this->arrayToJS($this->symbols);
        $code = "let _; eval(atob(\"" . base64_encode($code) . "\"));";
        $this->js = $code . "\n\n" . $this->js;
    }

    private function replaceSymbols() {
        $this->replaceSymbol("document", "_._a");
        $this->replaceSymbol("_._a.getElementById", "_._b");
        $this->replaceSymbol("window", "_._d");
    }
}