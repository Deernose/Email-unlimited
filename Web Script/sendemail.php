<?php

//------------ CONFIGURAÇÕES ------------

$helo = $_SERVER['HTTP_HOST'];
$from = "info@mail.com";
$scriptpass = "E35DCBD20CC0";

//------------ FIM CONFIGURAÇÕES ------------

if (!isset($_GET["email"]) || !isset($_GET["password"])) {
    echo "<check>96DA8A550749</check><server>Verify Script</server><message>400 Bad Request</message><log>Missing email or password parameter</log>";
    return;
}

$email = $_GET["email"];
$password = $_GET["password"];

if ($password != $scriptpass) {
    echo "<check>96DA8A550749</check><server>Verify Script</server><message>603 Wrong Password</message><log>Password provided does not match the script password</log>";
    return;
}

$result = Test($email, $from, $helo);
echo "<check>96DA8A550749</check><server>" . htmlspecialchars($result[1]) . "</server><message>" . htmlspecialchars($result[0]) . "</message><log>" . htmlspecialchars($result[2]) . "</log>";

// A função Test retorna um array:
// $result[0] - Resposta do Servidor SMTP
// $result[1] - Host do Servidor SMTP
// $result[2] - Log do Servidor SMTP

function Test($Email, $From, $Helo) {
    $result = array();
    $log = "";

    if (!filter_var($Email, FILTER_VALIDATE_EMAIL)) {
        $result[0] = "500 Bad Syntax";
        $result[1] = "";
        $result[2] = "Email provided does not match valid email format.";
        return $result;
    }

    list($Username, $Domain) = explode("@", $Email);
    if (checkdnsrr($Domain, "MX")) {
        $log .= "MX record for {$Domain} exists.\r\n";
        if (getmxrr($Domain, $MXHost)) {
            $log .= "MX hosts: " . implode(", ", $MXHost) . "\r\n";
        }
        $ConnectAddress = $MXHost[0];
    } else {
        $ConnectAddress = $Domain;
        $log .= "MX record for {$Domain} does not exist.\r\n";
    }

    $Connect = @fsockopen($ConnectAddress, 25, $errno, $errstr, 30);
    $result[1] = $ConnectAddress;

    if ($Connect) {
        $log .= "Connection succeeded to {$ConnectAddress} SMTP.\r\n";
        $reply = fgets($Connect, 1024);
        $log .= "SMTP Server reply: " . trim($reply) . "\r\n";

        fputs($Connect, "QUIT\r\n");
        $log .= "> QUIT\r\n";
        fclose($Connect);
    } else {
        $result[0] = "500 Cannot connect to E-Mail server: ({$ConnectAddress}). Error: {$errstr} ({$errno})";
        $result[2] = $log;
        return $result;
    }

    $result[0] = trim($reply);
    $result[2] = $log;
    return $result;
}

?>
