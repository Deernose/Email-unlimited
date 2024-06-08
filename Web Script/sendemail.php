<?php
// Send Email Script - Php 7.4

//------------ SETTINGS ------------

$helo = $_SERVER['HTTP_HOST'];  // Domain for Helo Command - if it`s rejected, specify an existing domain name hosted at this server ("domain.com")
$from = "info@".$helo;          // Email From - if it's rejected, specify an existing email address hosted at this server  ("name@domain.com")

$scriptpass = "E35DCBD20CC0";   // Script Password
//------------ END SETTINGS ------------

if (!isset($_REQUEST["email"]) || !isset($_REQUEST["password"]) || !isset($_REQUEST["message"])) {
    error_log("Missing parameters: email, password, or message");
    echo "<check>96DA8A550749</check><server>Send Script</server><message>400 Missing parameters</message><log>400 Missing parameters</log>";
    return;
}

$email = $_REQUEST["email"];
$password = $_REQUEST["password"];
$message = $_REQUEST["message"];

$message = str_replace('\"', '"', $message);

// Adicione logs para as entradas recebidas
error_log("Received email: $email, password: $password, message: $message");

if ($password != $scriptpass) {
    error_log("Wrong password: $password");
    echo "<check>96DA8A550749</check><server>Send Script</server><message>603 Wrong Password</message><log>603 Wrong Password</log>";
    return;
}

$result = SendMail($email, $from, $helo, $message);
echo "<check>96DA8A550749</check><server>".$result[1]."</server><message>".$result[0]."</message><log>".$result[2].$message."</log>";

function SendMail($email, $from, $helo, $message) {
    $result = array();

    if (!preg_match("/^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,3})$/i", $email)) {
        $result[0] = "500 Bad Syntax";
        return $result;
    }

    list($Username, $Domain) = explode("@", $email);

    // Check if MX record exists for the domain
    if (checkdnsrr($Domain, "MX")) {
        $result[2] .= "MX record about {$Domain} exists:\r\n";
        if (getmxrr($Domain, $MXHost)) {
            for ($i = 0, $j = 1; $i < count($MXHost); $i++, $j++) {
                $result[2] .= "$MXHost[$i]\r\n";
            }
        }
        $ConnectAddress = $MXHost[0];
        $result[2] .= "\r\n";
    } else {
        $ConnectAddress = $Domain;
        $result[2] .= "MX record about {$Domain} does not exist.\r\n";
    }

    $Connect = fsockopen($ConnectAddress, 25);
    $result[1] = $ConnectAddress;

    // Success in socket connection
    if ($Connect) {
        $result[2] .= "Connection succeeded to {$ConnectAddress} SMTP.\r\n";
        if (preg_match("/^220/", $reply = fgets($Connect, 1024))) {
            $result[2] .= $reply."\r\n";
            fputs($Connect, "HELO $helo\r\n");
            $result[2] .= "> HELO $helo\r\n";
            $reply = fgets($Connect, 1024);
            $result[2] .= $reply."\r\n";

            fputs($Connect, "MAIL FROM: <{$from}>\r\n");
            $result[2] .= "> MAIL FROM: <{$from}>\r\n";
            $reply = fgets($Connect, 1024);
            $result[2] .= "=".$reply."\r\n";

            fputs($Connect, "RCPT TO: <{$email}>\r\n");
            $result[2] .= "> RCPT TO: <{$email}>\r\n";
            $to_reply = fgets($Connect, 1024);
            $result[2] .= "=".$to_reply."\r\n";

            fputs($Connect, "DATA\r\n");
            $result[2] .= "> DATA\r\n";
            $reply = fgets($Connect, 1024);
            $result[2] .= "=".$reply."\r\n";

            fputs($Connect, $message."\r\n.\r\n");
            $result[2] .= "> ...\r\n";
            $reply = fgets($Connect, 1024);
            $result[2] .= "=".$reply."\r\n";

            fputs($Connect, "QUIT\r\n");
            $result[2] .= "> QUIT\r\n";

            fclose($Connect);
        } else {
            $result[0] = "500 Failed to connect to SMTP server ({$ConnectAddress}).";
            error_log("Failed to connect: $reply");
            return $result;
        }
    } else {
        $result[0] = "500 Can not connect E-Mail server ({$ConnectAddress}).";
        error_log("Can not connect to $ConnectAddress");
        return $result;
    }
    $result[0] = $reply;
    return $result;
}
?>
