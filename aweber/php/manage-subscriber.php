
<?php
$content=trim(file_get_contents("php://input"));
$decoded=json_decode($content,true);

$email = $decoded['email'];
$name = $decoded['name'];


require 'vendor/autoload.php';
date_default_timezone_set("Asia/Vladivostok");

require 'vendor/autoload.php';
use League\Oauth2\Client\Provider\GenericProvider;
const OAUTH_URL = 'https://auth.aweber.com/oauth2/';
const TOKEN_URL = 'https://auth.aweber.com/oauth2/token';

$credentials = parse_ini_file('credentials.ini', true);
if(sizeof($credentials) == 0 ||
   !array_key_exists('clientId', $credentials) ||
   !array_key_exists('clientSecret', $credentials) ||
   !array_key_exists('accessToken', $credentials) ||
   !array_key_exists('refreshToken', $credentials)) {
    echo "No credentials.ini exists, or file is improperly formatted.\n";
    echo "Please create new credentials.";
    exit();
}
$client = new GuzzleHttp\Client();
$clientId = $credentials['clientId'];
$clientSecret = $credentials['clientSecret'];
$response = $client->post(
    TOKEN_URL, [
        'auth' => [
            $clientId, $clientSecret
        ],
        'json' => [
            'grant_type' => 'refresh_token',
            'refresh_token' => $credentials['refreshToken']
        ]
    ]
);
$body = $response->getBody();
$newCreds = json_decode($body, true);
$accessToken = $newCreds['access_token'];
$refreshToken = $newCreds['refresh_token'];



use GuzzleHttp\Client;
const BASE_URL = 'https://api.aweber.com/1.0/';

// Create a Guzzle client
$client = new GuzzleHttp\Client();
$returnValue=array("status"=>"noproduct");
// Load credentials
// $credentials = parse_ini_file('credentials.ini');
// $accessToken = $credentials['accessToken'];

/**
 * Get all of the entries for a collection
 *
 * @param Client $client HTTP Client used to make a GET request
 * @param string $accessToken Access token to pass in as an authorization header
 * @param string $url Full url to make the request
 * @return array Every entry in the collection
 */
function getCollection($client, $accessToken, $url) {
    $collection = array();
    while (isset($url)) {
        $request = $client->get($url,
            ['headers' => ['Authorization' => 'Bearer ' . $accessToken]]
        );
        $body = $request->getBody();
        $page = json_decode($body, true);
        $collection = array_merge($page['entries'], $collection);
        $url = isset($page['next_collection_link']) ? $page['next_collection_link'] : null;
    }
    return $collection;
}

// get all the accounts entries
$accounts = getCollection($client, $accessToken, BASE_URL . 'accounts');

$accountUrl = $accounts[0]['self_link'];

// get all the list entries for the first account
$listsUrl = $accounts[0]['lists_collection_link'];
$lists = getCollection($client, $accessToken, $listsUrl);
//var_dump($lists);exit;
// find out if a subscriber exists on the first list

$params = array(
    'ws.op' => 'find',
    'email' => $email
);
// var_dump($accessToken);
// exit;
$subsUrl = $lists[0]['subscribers_collection_link'];
$findUrl = $subsUrl . '?' . http_build_query($params);

$foundSubscribers = getCollection($client, $accessToken, $findUrl);
// echo 'Found subscribers: ';
// print_r($foundSubscribers);

if (isset($foundSubscribers[0]['self_link'])) {
    // update the subscriber if they are on the first list
    // $data = array(
    //     'custom_fields' => array('awesomeness' => 'really awesome'),
    //     'tags' => array('add' => array('prospect'))
    // );
    // $subscriberUrl = $foundSubscribers[0]['self_link'];
    // $subscriberResponse = $client->patch($subscriberUrl, [
    //         'json' => $data, 
    //         'headers' => ['Authorization' => 'Bearer ' . $accessToken]
    //     ])->getBody();
    // $subscriber = json_decode($subscriberResponse, true);
    $returnValue["status"]="exist";
} else {
    // add the subscriber if they are not already on the first list
    $data = array(
        'email' => $email,
        'name' => $name,
        'custom_fields' => array('awesomeness' => 'somewhat'),
        'tags' => array('prospect')
    );
    $body = $client->post($subsUrl, [
            'json' => $data, 
            'headers' => ['Authorization' => 'Bearer ' . $accessToken]
        ]);

    // get the subscriber entry using the Location header from the post request
    $subscriberUrl = $body->getHeader('Location')[0];
    $subscriberResponse = $client->get($subscriberUrl,
        ['headers' => ['Authorization' => 'Bearer ' . $accessToken]])->getBody();
    $subscriber = json_decode($subscriberResponse, true);
    $returnValue["status"]="ok";
}
echo json_encode($returnValue);
// // print_r($subscriber);

// // get the activity for the subscriber
// $params = array('ws.op' => 'getActivity');
// $activityUrl = $subscriberUrl . '?' . http_build_query($params);
// $activity = $client->get($activityUrl,
//     ['headers' => ['Authorization' => 'Bearer ' . $accessToken]])->getBody();
// echo 'Subscriber Activity: ';
// print_r(json_decode($activity, true));

// // delete the subscriber; this can only be performed on confirmed subscribers
// // or a 405 Method Not Allowed will be returned
// if ($subscriber['status'] == 'subscribed') {
//     $client->delete($subscriberUrl, 
//         ['headers' => ['Authorization' => 'Bearer ' . $accessToken]])->getBody();
//     echo 'Deleted subscriber with email: ' . $email;
// }
?>
