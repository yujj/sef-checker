<?php
if (isset($_REQUEST['url'])) {
    header("Content-type: application/json; charset=utf-8");
    $GLOBALS['headers'] = array();
    $response = checkLink($_REQUEST['url']);
    $response['url'] = urldecode($response['url']);
    $response['orig_url'] = urldecode(urlencodeAsBrowser($_REQUEST['url']));
    $response['http_code'] = $GLOBALS['headers'][0];
    echo json_encode($response);
    exit();
}

function readHeader($ch, $header)
{
    if (preg_match('#^HTTP.*(\d{3}) #', $header, $h)) {
        $GLOBALS['headers'][] = $h[1];
    }
    return strlen($header);
} 
function urlencodeAsBrowser($url){
    include_once('idna_convert.class.php');
    $IDN = new idna_convert();
    $domain = parse_url($url, PHP_URL_HOST);
    $encoded_domain = $IDN->encode($domain);
    $url = str_replace($domain, $encoded_domain, $url);

    $url = str_replace(
        array("%2F", "%3F", "%3D", "%40", "%3A", "%26", "%3B", "%2A", "%27"), 
        array("/",   "?",   "=",   "@",   ":",   "&",   ";",   "*",   "'"  ), 
        urlencode(urldecode($url)));
    return $url;
}

function checkLink($url) {
    $url = urlencodeAsBrowser($url);
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');
    curl_setopt($ch, CURLOPT_HEADER, true);
    curl_setopt($ch, CURLOPT_NOBODY, true);
    curl_setopt($ch, CURLOPT_USERAGENT , "Mozilla/5.0 (Windows; U; Windows NT 5.1; ru-RU; rv:1.7.12) Gecko/20050919 Firefox/1.0.7");
    curl_setopt($ch, CURLOPT_RETURNTRANSFER , 1);
    curl_setopt($ch, CURLOPT_AUTOREFERER, 1);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
    curl_setopt($ch, CURLOPT_MAXREDIRS, 5);
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
    curl_setopt($ch, CURLOPT_TIMEOUT, 5);
    curl_setopt($ch, CURLOPT_HEADERFUNCTION, 'readHeader'); 
    curl_exec($ch); 
    $response = curl_getinfo($ch);
    curl_close($ch);
    return $response;
}

header("Content-type: text/html; charset=utf-8");