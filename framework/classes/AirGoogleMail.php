<?php
require_once AIR_ROOT . "/libs/PHPMailer/PHPMailerAutoload.php";

class AirGoogleMail
{
    /**
     * @var PHPMailerOAuth
     */
    protected $mailer;

    function __construct()
    {
        $this->mailer = new PHPMailerOAuth();
        $this->mailer->isSMTP();
        $this->mailer->SMTPDebug = 0;
        $this->mailer->Debugoutput = 'html';
        $this->mailer->Host = 'smtp.gmail.com';
        $this->mailer->Port = 587;
        $this->mailer->SMTPSecure = 'tls';
        $this->mailer->SMTPAuth = true;

        $this->mailer->AuthType = 'XOAUTH2';
        $this->mailer->oauthUserEmail = cfg("googlemail", "oauthUserEmail");
        $this->mailer->oauthClientId = cfg("googlemail", "oauthClientId");
        $this->mailer->oauthClientSecret = cfg("googlemail", "oauthClientSecret");
        $this->mailer->oauthRefreshToken = cfg("googlemail", "oauthRefreshToken");
        $this->mailer->setFrom(cfg("googlemail", "fromAddress"), cfg("googlemail", "fromName"));
    }

}