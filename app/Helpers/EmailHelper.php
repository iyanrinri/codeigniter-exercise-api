<?php

namespace App\Helpers;

use CodeIgniter\Email\Email;
use Config\Email as EmailConfig;

class EmailHelper
{
    public $email;
    protected $emailQueue;

    public function __construct()
    {
        $this->email = new Email(new EmailConfig());
    }

    public function sendRegistrationEmail($userData)
    {
        service('queue')->push('email', 'WelcomeNotificationUserJob', ['user_data' => $userData]);
    }

    /**
     * Send email directly (used by queue processor)
     */
    public function sendEmail($to, $subject, $message)
    {
        $this->email->setFrom($this->email->fromEmail, 'CodeIgniter API Agent');
        $this->email->setTo($to);
        $this->email->setSubject($subject);
        $this->email->setMessage($message);
        
        return $this->email->send();
    }
}
