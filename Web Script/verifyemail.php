<?php
//A função eregi() foi removida no PHP 7.0+, foi modificado "eregi()" por "preg_match()" e "split()" por "explode()"
//lembre de mudar a senha em $scriptpass = "E35DCBD20CC0"; para maior segurança.

//------------ SETTINGS ------------

$helo = $_SERVER['HTTP_HOST'];
$from = "info@mail.com";
$scriptpass = "E35DCBD20CC0";

//------------ END SETTINGS ------------

$email = $_GET["email"];
$password = $_GET["password"];

if ($password != $scriptpass) {
    echo "<check>96DA8A550749</check><server>Verify Script</server><message>603 Wrong Password</message><log></log>";
    return;
}

$result = Test($email, $from, $helo);
echo "<check>96DA8A550749</check><server>" . $result[1] . "</server><message>" . $result[0] . "</message><log>" . $result[2] . "</log>";

// Function result results in an array:
// $result[0] - SMTP Server Replay
// $result[1] - SMTP Server Host
// $result[2] - SMTP Server Log

function Test($Email, $From, $Helo) {
    $result = array();
    $log = "";
    
    if (!preg_match("/^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,3})$/i", $Email)) {
        $result[0] = "500 Bad Syntax";
        return $result;
    }
    
    list($Username, $Domain) = explode("@", $Email);
    if (checkdnsrr($Domain, "MX")) {
        $log .= "MX record about {$Domain} exists:\r";
        if (getmxrr($Domain, $MXHost)) {
            // for ($i = 0, $j = 1; $i < count($MXHost); $i++, $j++) {
            //     $log .= "$MXHost[$i]\r";
            // }
        }
        
        $ConnectAddress = $MXHost[0];
        $log .= $ConnectAddress . "\r";
    
    } else {
        $ConnectAddress = $Domain;
        $log .= "MX record about {$Domain} does not exist.\r";
    }
    
    $Connect = fsockopen($ConnectAddress, 25);
    $result[1] = $ConnectAddress;
    
    // Success in socket connection
    if ($Connect) {
        $log .= "Connection succeeded to {$ConnectAddress} SMTP.\r";
        
        $reply = fgets($Connect, 1024);
        $log .= $reply . "\r";
        
        // Finish connection.
        fputs($Connect, "QUIT\r\n");
        $log .= "> QUIT\r";
        fclose($Connect);
    } // Failure in socket connection
    else {
        $result[0] = "500 Can not connect E-Mail server: ({$ConnectAddress}).";
        $result[2] = $log;
        return $result;
    }
    $result[0] = $reply;
    $result[2] = $log;
    return $result;
}

?>
