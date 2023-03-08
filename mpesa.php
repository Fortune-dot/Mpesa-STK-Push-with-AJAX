<?php
header('content-type: application/json');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $data = file_get_contents("php://input");
    $data = json_decode($data, true);


    Mpesa::stkSend($data);

 
    echo Mpesa::$response;
    //echo Mpesa::$payload;

    // echo json_encode(Mpesa::$payload);

} else {
   echo json_encode(['code' => 0, 'method' => $_SERVER['REQUEST_METHOD']]);
}

return;


class Mpesa
{
    public static $credentials, $token, $payload, $response, $url;


    public static function qrSend(){
        $args = func_get_args();
        self::$url = "https://sandbox.safaricom.co.ke/mpesa/qrcode/v1/generate";

        self::$payload = array(
            'Amount' => 1, 
            'RefNo' => 'deeznuts',
            'TrxCode' => 'PB',
            'CPI' => 174379,
            'MerchantName' => 'Daraja Sandbox',
        );   

        self::curlPost();
    }

    public static function load() : void
    {
        self::$credentials = array(
            'consumer_key' => 'nk16Y74eSbTaGQgc9WF8j6FigApqOMWr', // add your own
            'consumer_secret' => '40fD1vRXCq90XFaU', // add your own
        );
    }

    public static function stkSend(){
        $args = func_get_args();

        $Amount = $args[0]['amount'];
        $AccountReference = 'deeznuts';
        $CallBackURL = 'https://02a5-154-159-237-114.in.ngrok.io/callback_url.php';
        $PhoneNumber = $PartyA = $args[0]['phone-num'];

        $passkey = "bfb279f9aa9bdbcf158e97dd71a467cd2e0c893059b10f78e6b72ada1ed2c919";
        $Timestamp = date('YmdHis');
        $TransactionType = 'CustomerPayBillOnline';
        $TransactionDesc = 'Test Payment';

        $PartyB = $BusinessShortCode = 174379;
        $Password = base64_encode($BusinessShortCode . $passkey . $Timestamp);

        self::$payload = compact(
            'Password',
            'BusinessShortCode',
            'PhoneNumber',
            'PartyB',
            'PartyA',
            'Timestamp',
            'AccountReference',
            'Amount',
            'TransactionDesc',
            'CallBackURL',
            'TransactionType'
        );

        self::$url = "https://sandbox.safaricom.co.ke/mpesa/stkpush/v1/processrequest";

        self::curlPost();
    }


    public static function curlPost()
    {
        self::AccessToken();
        $ch = curl_init();
        curl_setopt_array(
            $ch,
            array(
                CURLOPT_URL => self::$url,
                CURLINFO_HEADER_OUT => true,
                CURLOPT_HTTPHEADER =>  array('Content-Type: application/json', 'Authorization:Bearer ' . self::$token),
                CURLOPT_POST =>  1,
                CURLOPT_POSTFIELDS =>  json_encode(self::$payload),
                CURLOPT_RETURNTRANSFER =>  true,
                CURLOPT_SSL_VERIFYPEER =>  false,
                CURLOPT_SSL_VERIFYHOST =>  false
            )
        );
        self::$response = curl_exec($ch);
        curl_close($ch);
        return;
    }

    public static function AccessToken()
    {
        self::load();

        $curl = curl_init('https://sandbox.safaricom.co.ke/oauth/v1/generate?grant_type=client_credentials');
        curl_setopt_array(
            $curl,
            array(
                CURLOPT_HTTPHEADER => ['Content-Type:application/json; charset=utf8'],
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_HEADER => false,
                CURLOPT_USERPWD => self::$credentials['consumer_key'] . ':' . self::$credentials['consumer_secret']
            )
        );
        $result = json_decode(curl_exec($curl));
        curl_close($curl);
        self::$token = $result->access_token;
    }
}
