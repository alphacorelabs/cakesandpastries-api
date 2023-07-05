<?php

/*
 * This file is part of the Laravel Smartsms package.
 *
 * (c) Bolaji Ajani <fabulousbj@hotmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace BJTheCod3r\SmartSms;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Config;


/**
 * Class SmartSms
 * @package BJTheCod3r\SmartSms
 * @author  Bolaji Ajani <fabulousbj@hotmail.com>
 */
class SmartSms
{
    /**
     * @var array $codes
     */
    const CODES = [
        "1000" => "Successful",
        "1001" => "Invalid Token",
        "1002" => "Error Sending SMS",
        "1003" => "Insufficient Balance",
        "1004" => "No valid phone number found",
        "1005" => "Application Error",
        "1006" => "Error Retrieving Balance",
        "1007" => "Message Schedule Error",
        "1008" => "Unregistered Bank Sender ID",
        "1009" => "Phone numbers on MTN DND"
    ];

    /**
     * @var string $sender
     */
    protected $sender;

    /**
     * @var string $token
     */
    protected $token;

    /**
     * @var string simserver_token
     */
    protected $simserver_token;

    /**
     * @var int $type
     */
    protected $type;

    /**
     * @var string $ref_id
     */
    protected $ref_id;

    /**
     * @var int $route_value
     */
    protected $route_value;

    /**
     * @var string $message
     */
    protected $message;

    /**
     * @var string $to
     */
    protected $to;

    /**
     * @var string $schedule
     */
    protected $schedule;

    /**
     * @var string $dlr_timeout
     */
    protected $dlr_timeout;

    public function __construct()
    {
        $this->sender = Config::get('smartsms.sender');
        $this->token = Config::get('smartsms.token');
        $this->type = Config::get('smartsms.type');
        $this->route_value = Config::get('smartsms.route');
        $this->simserver_token = Config::get('smartsms.simserver_token');   
    }

    /**
     * Send sms through smart sms API
     * @param string $to
     * @param string $message
     * @param string $sender
     */
    public function sendSms(string $to, string $message, string $sender=null)
    {
        if ($sender != null)
            $this->sender = $sender;
        $this->to = $to;
        $this->message = $message;

        return $this;
    }

    /**
     * Add routing
     * @param int $routing
     */
    public function route(int $route)
    {
        $this->route_value = $route;
        return $this;
    }

    /**
     * Add type
     * @param int $type
     */
    public function type(int $type)
    {
        $this->type = $type;
        return $this;
    }

    /**
     * Add ref
     * @param string $ref
     */
    public function ref(string $ref_id)
    {
        $this->ref_id = $ref_id;
        return $this;
    }

    /**
     * Add schedule - YYYY-MM-DD HH:MM eg 2018-01-01 08:10 (24 hour format)
     * @param string $schedule
     */
    public function schedule(string $schedule)
    {
        $this->schedule = $schedule;
        return $this;
    }

    /**
     * Add dlr timeout
     * @param string $dlr_timeout
     */
    public function dlr(string $dlr_timeout)
    {
        $this->dlr_timeout = $dlr_timeout;
        return $this;
    }

    /**
     * Send sms
     * @return array
     */
    public function send(): array
    {
        //let us send to the smartsms API
        $response = Http::get(Config::get('smartsms.sms_uri'), [
            'sender' => $this->sender,
            'to' => $this->to,
            'message' => $this->message,
            'type' => $this->type,
            'routing' => $this->route_value,
            'token' => $this->token,
            'simserver_token' => $this->simserver_token,
            'schedule' => $this->schedule,
            'ref_id' => $this->ref_id,
        ]);
        
        $response = $response->json();

        return [
            "success" => $response["code"] == "1000" ? true : false,
            "response" => $response
        ];
    }

    /**
     * Check balance returns 
     * @return Illuminate\Http\Client\Response of float
     */
    public function checkBalance()
    {
        $response = Http::get(Config::get('smartsms.base_url'), [
            'checkbalance' => 1,
            'token' => $this->token
        ]);

        return $response;
    }

}