<?php
use App\Models\Sms;
use App\Models\User;
class GlobalHelpers
{
    static function INTECH_SMS_API_KEY()
    {
        return env("INTECH_SMS_API_KEY"); //Vous le déclarez dans le fichier .env INTECH_SMS_API_KEY=clé de votre compte intechsms.Vous pouvez créer un compte sur le  intechsms.sn;
    }

    // static public function post($url, $data = [], $header = [])
    // {
    //     $strPostField = http_build_query($data);

    //     $ch = curl_init($url);
    //     curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
    //     curl_setopt($ch, CURLOPT_POSTFIELDS, $strPostField);
    //     curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    //     curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
    //     curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
    //     curl_setopt(
    //         $ch,
    //         CURLOPT_HTTPHEADER,
    //         array_merge(
    //             $header,
    //             [
    //                 'Content-Type: application/x-www-form-urlencoded;charset=utf-8',
    //                 'Content-Length: ' . mb_strlen(
    //                     $strPostField,
    //                 )
    //             ],
    //         ),
    //     );

    //     return curl_exec($ch);
    // }
    static function httpPost($url, array $data = [], $header = [], $decodeJson = false, $method = 'POST', $isJson = false)
    {
        $strPostField = $isJson ? json_encode($data) : http_build_query($data);
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $strPostField);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 30);
        curl_setopt($ch, CURLOPT_TIMEOUT, 120);
        $headersSet = [
            $isJson ? 'Content-Type: application/json' : 'Content-Type: application/x-www-form-urlencoded;charset=utf-8',
            'Content-Length: ' . mb_strlen($strPostField)
        ];

        foreach ($header as $key => $val) {
            $headersSet[] = "$key: $val";
        }

        curl_setopt($ch, CURLOPT_HTTPHEADER, $headersSet);
        $response = @curl_exec($ch);
        curl_close($ch);

        if (!$decodeJson) {
            return $response;
        } else {
            return json_decode($response, false);
        }
    }

    public static function sendSms($phone, $message)
    {
        $url = 'https://gateway.intechsms.sn/api/send-sms';

        $response = self::httpPost($url, array(
            "app_key" => self::INTECH_SMS_API_KEY(),
            "sender" => "AbdouTest",
            "content" => $message,
            "msisdn" => [
                $phone
            ]
        ));
        $data = json_decode($response, false);

        if (empty($data)) {
            return response()->error('Le message n est pas envoyé');
        }

        if ($data && $data->code === 201) {
            $sms = new Sms();
            $sms->status = $data->code;
            $sms->phone = $phone;
            $sms->message = $message;
            $sms->response_api_message = json_encode($response);
            $sms->save();
            return true;
        } else {
            $sms = new Sms();
            $sms->status = $data->code ?? 400;
            $sms->phone = $phone;
            $sms->message = $message;
            $sms->response_api_message = json_encode($response);
            $sms->save();
            return false;
        }
    }
}
