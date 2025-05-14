<?php

/*
 * @package OpenEMR
 *
 * @author Sherwin Gaddis <sherwingaddis@gmail.com>
 * Copyright (c) 2023.
 * @license "All rights reserved"
 */

namespace Juggernaut\Modules\Jitsi8x8Telehealth\Jitsi8x8Telehealth;

require dirname(__FILE__, 2) . '/vendor/autoload.php';

use Firebase\JWT\JWT;
    class JavaWebToken
    {
        /**
         * Change the variables below.
         */
        const API_KEY = "vpaas-magic-cookie-02bc0019d5a3438186239dc1711e0ee1/fe45eb";
        const APP_ID="vpaas-magic-cookie-02bc0019d5a3438186239dc1711e0ee1"; // Your AppID (previously tenant)
        const USER_EMAIL="sherwingaddis@gmail.com";
        const USER_NAME="Sherwin Gaddis";
        const USER_IS_MODERATOR=true;
        const USER_AVATAR_URL="";
        const USER_ID="1711e0ee1/674ba7";
        const LIVESTREAMING_IS_ENABLED=true;
        const RECORDING_IS_ENABLED=true;
        const OUTBOUND_IS_ENABLED=false;
        const TRANSCRIPTION_IS_ENABLED=false;
        const EXP_DELAY_SEC=7200;
        const NBF_DELAY_SEC=0;
        ///
        private string $keyLocation;
        private string $privateKey;
        public function __construct()
        {
            // Read your private key from file see https://jaas.8x8.vc/#/apikeys
            //$private_key = file_get_contents("./rsa-private.pk");
            $this->keyLocation = dirname(__FILE__, 6) . "/sites/jaasauth.key";
            $this->privateKey = file_get_contents($this->keyLocation);
        }

        // Use the following function to generate your JaaS JWT.
    public function create_jaas_token(
        $api_key,
        $app_id,
        $user_email,
        $user_name,
        $user_is_moderator,
        $room,
        $user_avatar_url,
        $user_id,
        $live_streaming_enabled,
        $recording_enabled,
        $outbound_enabled,
        $transcription_enabled,
        $exp_delay,
        $nbf_delay,
        $private_key): string
    {

        $payload = array(
            'iss' => 'chat',
            'aud' => 'jitsi',
            'exp' => time() + $exp_delay,
            'nbf' => time() - $nbf_delay,
            'room'=> $room,
            'sub' => $app_id,
            'context' => [
                'user' => [
                    'moderator' => $user_is_moderator ? "true" : "false",
                    'email' => $user_email,
                    'name' => $user_name,
                    'avatar' => $user_avatar_url,
                    'id' => $user_id
                ],
                'features' => [
                    'recording' => $recording_enabled ? "true" : "false",
                    'livestreaming' => $live_streaming_enabled ? "true" : "false",
                    'transcription' => $transcription_enabled ? "true" : "false",
                    'outbound-call' => $outbound_enabled ? "true" : "false"
                ]
            ]
        );
        return JWT::encode($payload, $private_key, "RS256", $api_key);
    }

        //
        public function buildToken(
            $email,
            $username,
            $ismoderator,
            $room
        ): string
        //public function buildToken(): string
        {
            /// This writes the jwt to standard output
            return $this->create_jaas_token(self::API_KEY,
                self::APP_ID,
                $email,
                $username,
                $ismoderator,
                $room,
                self::USER_AVATAR_URL,
                self::USER_ID,
                self::LIVESTREAMING_IS_ENABLED,
                self::RECORDING_IS_ENABLED,
                self::OUTBOUND_IS_ENABLED,
                self::TRANSCRIPTION_IS_ENABLED,
                self::EXP_DELAY_SEC,
                self::NBF_DELAY_SEC,
                $this->privateKey);
        }
    }
