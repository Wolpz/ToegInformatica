<?php
class test{
    var $tabel;
    function setvar($f){
        $this->tabel=$f;
    }
    function getvar(){
        return $this->tabel;
    }
    function addend($f){
        $this->tabel=$this->tabel.$f;
    }
    function addfront($f){
        $this->tabel=$f.$this->tabel;
    }
    function display(){
        echo $this->tabel."<BR>";
    }
    function clear(){// Leeg de variabele
        $this->tabel = '';
    }
    function replace($find, $repl){// Vervang string/character door een andere string/character
        $this->tabel = preg_replace('/'.$find.'/', $repl, $this->tabel);
    }
    function length(){// Lengte van de string
        return strlen($this->tabel);
    }
    function reverse(){// Draai de string om
        $hold1 = str_split($this->tabel);
        for($i = 0; $i < round($this->length()/2); $i++) {
            $hold2 = $hold1[$i];
            $hold1[$i] = $hold1[$this->length() - 1 - $i];
            $hold1[$this->length() - 1 - $i] = $hold2;
        }
        $this->tabel = implode($hold1);
    }
    function countChar($find){// Tel het aantal karakters van het meegeven karakter
        return preg_match_all('/'.$find.'/', $this->tabel);
    }
    function strPos($find){// Zoek positie van meegegeven string
        return strpos($this->tabel, $find);
    }
    function upperCase(){// Maak hoofdletters van alle karakters
        $this->tabel = strtoupper($this->tabel);
    }
    function lowerCase(){// Maak kleine letters van alle karakters
        $this->tabel = strtolower($this->tabel);
    }
    function conCat($str){// Voeg een string toe aan de bestaande string
        $this->addend($str);
    }
    function midStr($num){// Vind de karakters in het midden van de string. Geef als parameter hoeveel karakters je wilt hebben mee.
        return substr($this->tabel, max(round($this->length()/2-$num/2), 0), $num);
    }
}

$in = new test;

if (isset($_POST['input']))
    $in->setvar($_POST["input"]);
else
    $in->setvar("");;
if (isset($_POST['input2']))
    $in2 = $_POST["input2"];
else
    $in2 = '';
if (isset($_POST['input3']))
    $in3 = $_POST["input3"];
else
    $in3 = '';

$out = '';

?>

<html>
<head>
    <title>Classes</title>
</head>
<body>
Voer een string in:<br>
<form name="string" method="post">
    <label for="input">String:</label>
    <input type="text" name="input" value="<?php echo $in->getvar() ?>">
    <label for="input2">Find/add:</label>
    <input type="text" name="input2" value="<?php echo $in2 ?>">
    <label for="input2">Replace:</label>
    <input type="text" name="input3" value="<?php echo $in3 ?>">
    <br>
    <input type="submit" value="Add to end" name="addend">
    <input type="submit" value="Add to front" name="addfront">
    <input type="submit" value="clear" name="clear">
    <input type="submit" value="replace" name="replace">
    <input type="submit" value="Count length" name="length">
    <input type="submit" value="Reverse" name="reverse">
    <br>
    <input type="submit" value="Find character" name="countChar">
    <input type="submit" value="Find string position" name="strPos">
    <input type="submit" value="uppercase" name="uppercase">
    <input type="submit" value="lowercase" name="lowercase">
    <input type="submit" value="Concatenate" name="conCat">
    <input type="submit" value="Middle characters" name="midStr">
</form>
<?php
if(isset($_POST['addend'])){
    $in->addend($in2);
    $out = $in->getvar();
}
else if(isset($_POST['addfront'])){
    $in->addfront($in2);
    $out = $in->getvar();
}
else if(isset($_POST['clear'])){
    $in->clear();
    $out = $in->getvar();
}
else if(isset($_POST['replace'])){
    $in->replace($in2, $in3);
    $out = $in->getvar();
}
else if(isset($_POST['length'])){
    $out = $in->length();
}
else if(isset($_POST['reverse'])){
    $in->reverse();
    $out = $in->getvar();
}
else if(isset($_POST['countChar'])){
    $out = $in->countChar($in2);
}
else if(isset($_POST['strPos'])){
    $out = $in->strPos($in2);
}
else if(isset($_POST['uppercase'])){
    $in->upperCase();
    $out = $in->getvar();
}
else if(isset($_POST['lowercase'])){
    $in->lowerCase();
    $out = $in->getvar();
}
else if(isset($_POST['conCat'])){
    $in->conCat($in2);
    $out = $in->getvar();
}
else if(isset($_POST['midStr'])){
    if(is_numeric($in2))
        $out = $in->midStr($in2);
}
else
    $out = $in->getvar();
?>
Output: <?php echo $out; ?><br>
</body>
</html>

