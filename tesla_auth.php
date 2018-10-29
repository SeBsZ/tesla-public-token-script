<?php
/* Script retrieved from https://github.com/SeBsZ/tesla-public-token-script by SeBsZ
   When proper Tesla login details are provided this script will return a JSON-encoded output containing the access and refresh tokens
   When there is an error or the login details are invalid, this script will return false.
*/

/*
MIT License

Copyright (c) 2018 Sebastiaan Bakker

Permission is hereby granted, free of charge, to any person obtaining a copy
of this software and associated documentation files (the "Software"), to deal
in the Software without restriction, including without limitation the rights
to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
copies of the Software, and to permit persons to whom the Software is
furnished to do so, subject to the following conditions:

The above copyright notice and this permission notice shall be included in all
copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
SOFTWARE.
*/

/* You need to modify these */
$TESLA_EMAIL = "your_email";
$TESLA_PASSWORD = "your_password";

/* You generally don't need to modify these */
$tokenUrl = 'https://owner-api.teslamotors.com/oauth/token';
$clientId = '81527cff06843c8634fdc09e8ac0abefb46ac849f38fe1e431c2ef2106796384';
$clientSecret = 'c7257eb71a564034f9419ee651c7d0e5f7aa6bfbd18bafb5c5c033b093bb2fa3';

$result = false;

$token = getAccessToken($TESLA_EMAIL, $TESLA_PASSWORD, $clientId, $clientSecret, $tokenUrl);
if ($token) {
    //Valid token
    if ( ! empty($token['access_token'])) {
        $result = new stdClass();
        $result->access_token = $token['access_token'];
        $result->refresh_token = $token['refresh_token'];
    }
}

echo json_encode($result);

function getAccessToken($username, $password, $clientId, $clientSecret, $tokenUrl)
{
    try {
        $ch = curl_init();

        $params = array(
            'grant_type'    => 'password',
            'client_id'     => $clientId,
            'client_secret' => $clientSecret,
            'email'         => $username,
            'password'      => $password,
        );

        curl_setopt($ch, CURLOPT_URL, $tokenUrl);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_USERAGENT, 'curl/7.47.0');
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($params));
        curl_setopt($ch, CURLOPT_HTTPHEADER,
            [
                'Content-Type: application/json',
                'Accept: application/json',
            ]);

        $apiResult = curl_exec($ch);

        if ($apiResult === false) {
            return false;
        }

        if ($apiResult) {
            $apiResultJson = json_decode($apiResult, true);
            curl_close($ch);

            return $apiResultJson;
        }
    } catch (Exception $e) {
        return false;
    }

    return false;
}
