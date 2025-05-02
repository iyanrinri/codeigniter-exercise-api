<?php

namespace App\Jobs;

use Exception;
use CodeIgniter\Queue\BaseJob;
use CodeIgniter\Queue\Interfaces\JobInterface;
use App\Helpers\EmailHelper;

class WelcomeNotificationUserJob extends BaseJob implements JobInterface
{

    protected int $retryAfter = 60;

    protected $emailHelper;

    public function __construct(array $data)
    {
        parent::__construct($data); 
        $this->emailHelper = new EmailHelper();
    }

    /**
     * @throws Exception
     */
    public function process()
    {
        $userData = $this->data['user_data'] ?? [];
        $result = $this->emailHelper->sendEmail(
            $userData['email'],
            'Welcome to Our Platform',
            view('emails/registration_success', [
                'name' => $userData['name'],
                'email' => $userData['email']
            ])
        );
        if (!$result) {
            throw new Exception("Failed to send email {$userData['email']}");
        }

        return $result;
    }
}