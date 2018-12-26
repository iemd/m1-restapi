<?php
$callbackUrl = 'http://'.$_SERVER['HTTP_HOST'].$_SERVER['PHP_SELF']; // This scripts url
$magentoUrl = 'http://localhost/jeptags/'; // The url of your magento installation
$temporaryCredentialsRequestUrl = $magentoUrl . 'oauth/initiate?oauth_callback=' . urlencode($callbackUrl);
$adminAuthorizationUrl = $magentoUrl . 'admin/oauth_authorize';
$accessTokenRequestUrl = $magentoUrl . 'oauth/token';
$apiUrl = $magentoUrl . 'api/rest';
$consumerKey = 'd545e3629c42e6781f083695010ff736'; // Get from System > Web Services > REST - OAUTH Consumers > Edit Consumer > Key
$consumerSecret = '1f4adedc685fe6260f48c5d77e6d7786'; // Get from System > Web Services > REST - OAUTH Consumers > Edit Consumer > Secret
session_start();
if (!isset($_GET['oauth_token']) && isset($_SESSION['state']) && $_SESSION['state'] == 1) {
    $_SESSION['state'] = 0;
}
try {
    $authType = ($_SESSION['state'] == 2) ? OAUTH_AUTH_TYPE_AUTHORIZATION : OAUTH_AUTH_TYPE_URI;
    $oauthClient = new OAuth($consumerKey, $consumerSecret, OAUTH_SIG_METHOD_HMACSHA1, $authType);
    $oauthClient->enableDebug();
    if (!isset($_GET['oauth_token']) && !$_SESSION['state']) {
        $requestToken = $oauthClient->getRequestToken($temporaryCredentialsRequestUrl);
        $_SESSION['secret'] = $requestToken['oauth_token_secret'];
        $_SESSION['state'] = 1;
        header('Location: ' . $adminAuthorizationUrl . '?oauth_token=' . $requestToken['oauth_token']);
        exit;
    } else if ($_SESSION['state'] == 1) {
        $oauthClient->setToken($_GET['oauth_token'], $_SESSION['secret']);
        $accessToken = $oauthClient->getAccessToken($accessTokenRequestUrl);
        $_SESSION['state'] = 2;
        $_SESSION['token'] = $accessToken['oauth_token'];
        $_SESSION['secret'] = $accessToken['oauth_token_secret'];
        header('Location: ' . $callbackUrl);
        exit;
    } else {
        $oauthClient->setToken($_SESSION['token'], $_SESSION['secret']);
        $resourceUrl = "$apiUrl/products";
        $oauthClient->disableRedirects();
        $headers = array('Content-Type' => 'application/json', 'Content_Type' => 'application/json', 'Accept' => '*/*');
        $oauthClient->fetch($resourceUrl, array(), 'GET', $headers);
        $response = json_decode($oauthClient->getLastResponse());
        print_r($response);
    }
} catch (OAuthException $e) {
    print_r($e->getMessage());
    echo "<br>";
    print_r($e->lastResponse);
}