<?php
/*
 * This file is part of Laravel SmartSms package.
 *
 * (c) Bolaji Ajani <fabulousbj@hotmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

return [

    /**
     * Sender ID from SmartSms
     *
     */
    'sender' => getenv('SMARTSMS_SENDER_ID'),

    /**
     * Token from SmartSms
     *
     */
    'token' => getenv('SMARTSMS_TOKEN'),

    /**
     * Sim Server Token
     */
    'simserver_token' => getenv('SMARTSMS_SIMSERVER_TOKEN'),

    /**
     * Type of sms
     *
     */
    'type' => getenv('SMARTSMS_TYPE', 0),

    /**
     * Routing through Basic Route, Corporate Route, Both routes, Hosted Sim
     *
     */
    'route' => getenv('SMARTSMS_ROUTE', 3),

    /**
     * SMS URI
     */
    'sms_uri' => "https://smartsmssolutions.com/api/json.php",

    /**
     * BASE URL
     */
    'base_url' => "https://smartsmssolutions.com/api"

];