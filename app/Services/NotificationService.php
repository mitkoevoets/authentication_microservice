<?php
namespace App\Services;

use GuzzleHttp\Client;

/**
 * Class NotificationService
 * @package App\Services
 */
class NotificationService extends BaseService
{

    /**
     * MembershipService constructor.
     * @param Client $guzzleClient
     */
    public function __construct(Client $guzzleClient)
    {

        parent::__construct($guzzleClient);
    }

    /**
     * @param $user
     * @param $token
     * @return array
     */
    public function sendActivation($user, $token)
    {
        $contentData = (object) [
            'token' => $token,

        ];

        return $this->send('activation', $user, $contentData);
    }

    /**
     * @param $user
     * @param $token
     * @return array
     */
    public function sendResetPassword($user, $token)
    {
        $contentData = (object) [
            'token' => $token,
        ];

        return $this->send('reset-password', $user, $contentData);
    }

    /**
     * @param $notificationKey
     * @param $user
     * @param $contentData
     * @return array
     */
    public function send($notificationKey, $user, $contentData)
    {
        $recipient = (object) [
            'email' => $user->email,

        ];

        $body = [
            'recipient' => json_encode($recipient),
            'data' => json_encode($contentData)
        ];

        $url = $this->baseUrl . $this->sendNotificationRoute . '/' . $notificationKey;

        return $this->post($url, $body);
    }
}
