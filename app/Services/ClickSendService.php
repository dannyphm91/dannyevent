<?php

namespace App\Services;

use ClickSend\Api\SMSApi;
use ClickSend\Configuration;
use ClickSend\Model\SmsMessage;
use ClickSend\Model\SmsMessageCollection;
use GuzzleHttp\Client;

class ClickSendService
{
    protected $apiInstance;

    public function __construct()
    {
        $config = Configuration::getDefaultConfiguration()
            ->setUsername('duy.danny@gmail.com')
            ->setPassword('4CE02541-5DDD-5036-DB63-1CBC313071C7');

        $this->apiInstance = new SMSApi(new Client(), $config);
    }

    public function sendSMS($to, $message, $source = 'laravel')
    {
        try {
            $msg = new SmsMessage();
            $msg->setBody($message);
            $msg->setTo($to);
            $msg->setSource($source);

            $sms_messages = new SmsMessageCollection();
            $sms_messages->setMessages([$msg]);

            $result = $this->apiInstance->smsSendPost($sms_messages);
            return $result;
        } catch (\Exception $e) {
            \Log::error('ClickSend SMS Error: ' . $e->getMessage());
            throw $e;
        }
    }
} 