<?php
namespace PHPMailer\PHPMailer;

class PHPMailer {
    public $Host;
    public $SMTPAuth = true;
    public $Username;
    public $Password;
    public $SMTPSecure;
    public $Port;
    public $CharSet = 'UTF-8';
    public $From;
    public $FromName;
    public $Subject;
    public $Body;
    public $isSMTP = true;
    public $ErrorInfo = "";

    protected $to = [];

    public function isSMTP() { $this->isSMTP = true; }
    public function isHTML($ishtml = true) { /* Handled in mailer */ }
    
    public function addAddress($address) {
        $this->to[] = $address;
        return true;
    }

    public function setFrom($address, $name = '') {
        $this->From = $address;
        $this->FromName = $name;
        return true;
    }

    public function send() {
        $smtp = new SMTP();
        if (!$smtp->connect($this->Host, $this->Port)) {
            $this->ErrorInfo = "SMTP ulanishda xato";
            return false;
        }

        if (!$smtp->authenticate($this->Username, $this->Password)) {
            $this->ErrorInfo = "SMTP autentifikatsiya xatosi (Email yoki App Password noto'g'ri)";
            return false;
        }

        foreach ($this->to as $address) {
            if (!$smtp->sendMail($this->From, $address, $this->Subject, $this->Body)) {
                $this->ErrorInfo = "Xabar yuborishda xato";
                return false;
            }
        }

        $smtp->quit();
        return true;
    }
}
?>
