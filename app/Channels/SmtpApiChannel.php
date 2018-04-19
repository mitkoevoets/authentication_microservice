<?php

namespace App\Channels;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use Illuminate\Notifications\Notification;
class SmtpApiChannel
{
    /**
     * Send the given notification.
     *
     * @param  mixed  $notifiable
     * @param  \Illuminate\Notifications\Notification  $notification
     * @return void
     */
    public function send($notifiable, Notification $notification)
    {
        $client = new Client();

        $body = $notification->toSmtpApi($notifiable);

        $url = env('SMTP_API_URL', '') . '/email/send';

        try {
            $client->request('POST', $url, [
                'body'  =>  $body,
            ]);
        } catch (ClientException $exception) {
            // for debugging
            // $errors = json_decode($exception->getResponse()->getBody(), true);
        }
    }
}