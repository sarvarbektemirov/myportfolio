<?php
namespace PHPMailer\PHPMailer;

class SMTP {
    protected $smtp_conn;
    public $DebugOutput = false;

    protected function log($msg) {
        if ($this->DebugOutput) {
            echo htmlspecialchars($msg) . "<br>";
        }
    }

    public function connect($host, $port = 587, $timeout = 30) {
        $this->log("Ulanish: $host:$port...");
        $this->smtp_conn = @fsockopen($host, $port, $errno, $errstr, $timeout);
        
        if (!$this->smtp_conn) {
            $this->log("XATO: Ulanib bo'lmadi - $errstr ($errno)");
            return false;
        }

        $this->log($this->getResponse());
        
        // Gmail uchun STARTTLS kerak
        $this->send("EHLO localhost");
        $this->log($this->getResponse());
        
        $this->send("STARTTLS");
        $res = $this->getResponse();
        $this->log($res);

        if (strpos($res, '220') !== false) {
            if (!stream_socket_enable_crypto($this->smtp_conn, true, STREAM_CRYPTO_METHOD_TLS_CLIENT)) {
                $this->log("XATO: TLS ulashda xatolik (OpenSSL yoqilmagan bo'lishi mumkin)");
                return false;
            }
            $this->log("TLS ulandi.");
            
            // TLSdan keyin qayta Salomlashish kerak
            $this->send("EHLO localhost");
            $this->log($this->getResponse());
        }

        return true;
    }

    public function authenticate($user, $pass) {
        $this->log("Autentifikatsiya boshlandi...");
        $this->send("AUTH LOGIN");
        $this->log($this->getResponse());
        
        $this->send(base64_encode($user));
        $this->log($this->getResponse());
        
        $this->send(base64_encode($pass));
        $reply = $this->getResponse();
        $this->log($reply);

        if (strpos($reply, '235') === false) {
            $this->log("XATO: Login yoki Parol noto'g'ri!");
            return false;
        }
        return true;
    }

    public function sendMail($from, $to, $subject, $body) {
        $this->log("Xabar yuborilmoqda...");
        $this->send("MAIL FROM: <$from>");
        $this->log($this->getResponse());
        
        $this->send("RCPT TO: <$to>");
        $this->log($this->getResponse());
        
        $this->send("DATA");
        $this->log($this->getResponse());
        
        $headers = "From: " . MAIL_FROM_NAME . " <$from>\r\n";
        $headers .= "To: <$to>\r\n";
        $headers .= "Subject: $subject\r\n";
        $headers .= "MIME-Version: 1.0\r\n";
        $headers .= "Content-Type: text/html; charset=UTF-8\r\n\r\n";
        
        $this->send($headers . $body . "\r\n.");
        $reply = $this->getResponse();
        $this->log($reply);
        
        return strpos($reply, '250') !== false;
    }

    protected function send($cmd) {
        fputs($this->smtp_conn, $cmd . "\r\n");
    }

    protected function getResponse() {
        $res = "";
        while ($str = fgets($this->smtp_conn, 515)) {
            $res .= $str;
            if (substr($str, 3, 1) == " ") break;
        }
        return $res;
    }

    public function quit() {
        $this->send("QUIT");
        if($this->smtp_conn) fclose($this->smtp_conn);
    }
}
?>
