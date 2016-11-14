<?php

class Notification extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        header('Content-Type: application/json');
    }


    //------------------NOTIFICATIONS-----------------
    public function sendRequestCurl()
    {
        $curl = curl_init();
         curl_setopt_array($curl, array(
             CURLOPT_URL => "https://fcm.googleapis.com/fcm/send",
             CURLOPT_RETURNTRANSFER => true,
             CURLOPT_ENCODING => "",
             CURLOPT_MAXREDIRS => 10,
             CURLOPT_SSL_VERIFYPEER => false,
             CURLOPT_TIMEOUT => 30,
             CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
             CURLOPT_CUSTOMREQUEST => "POST",
             CURLOPT_POSTFIELDS => "{\r\n    \"to\" : \"fgOaDDGtmG8:APA91bE0mqkV6bZq3GR5iFVoW8UoTyLvUZykLFoz7pDUF-rP0zc2p_EjqPMIDA071aAKRlvOWa6S35WwZWvEEpuFN6nWKGfDWZjwWjX_HE2mJ1SxFPQ5oZqEHO9g207g3mj9KPvqsGkY\",\r\n    \"notification\" : {\r\n      \"body\" : \"great match!\",\r\n      \"title\" : \"Portugal vs. Denmark\",\r\n      \"icon\" : \"myicon\"\r\n    }\r\n  }",
             CURLOPT_HTTPHEADER => array(
                 "authorization: key=AIzaSyDSg1riiArny0cda5UwPqdjbhPsjTE1z9g",
                 "cache-control: no-cache",
                 "content-type: application/json",
             ),
         ));

         $response = curl_exec($curl);
         $err = curl_error($curl);

         curl_close($curl);
         if ($err) {
             echo "cURL Error #:" . $err;
        } else {
             echo $response;
        }
    }

    public function sendRequest()
    {
        $url = 'https://fcm.googleapis.com/fcm/send';
        $headers = array('Content-Type' => 'application/json', 'authorization' => 'key=AIzaSyDSg1riiArny0cda5UwPqdjbhPsjTE1z9g0');

        $fields = array (
            'notification' => array (
                "title"=> "Tester HELLO",
		        "body" => "Hello",
		        "icon" => "myicon"
            ),
            'to' => 'fgOaDDGtmG8:APA91bE0mqkV6bZq3GR5iFVoW8UoTyLvUZykLFoz7pDUF-rP0zc2p_EjqPMIDA071aAKRlvOWa6S35WwZWvEEpuFN6nWKGfDWZjwWjX_HE2mJ1SxFPQ5oZqEHO9g207g3mj9KPvqsGkY'

        );

        $response = (Requests::post($url, $headers, json_encode($fields)));
        $result = json_decode($response->body);
        echo $response;

    }

}