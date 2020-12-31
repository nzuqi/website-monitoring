<?php
    //sendgrid
    require './vendor/autoload.php';

    $dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
    $dotenv->load();

    include('monitor.php');
    include('email.php');

    $instructions = "<p>Your request should look like this: <strong>https://your-domain.com/script-folder/?token=1234&url=https://domain-to-check.com&always-notify=false</string></p>";

    if (!isset($_GET['token'])) {
        echo "<p>Token missing in request. Check and try again.</p>" . $instructions;
        exit();
    } else {
        if ($_GET['token'] != $_ENV['REQUEST_TOKEN']) {
            echo "<p>Invalid token in request. Check and try again.</p>" . $instructions;
            exit();
        }
    }
    
    $admin_email = $admin_name = $always_notify = $url = "";

    if (isset($_GET['always-notify'])) $always_notify = $_GET['always-notify'];

    if (isset($_GET['url'])) {
        $url = strtolower($_GET['url']);
    } else {
        echo "<p>URL to check is missing in request. Check and try again.</p>" . $instructions;
        exit();
    }

    if (!filter_var($url, FILTER_VALIDATE_URL)) {
        echo "<p>URL to check is invalid. Check and try again.</p>" . $instructions;
        exit();
    }
    
    $monitor = new Monitor();
    $send_email = new Email();

    $monitor->url = $url;
    $send_email->send_to = $_ENV['ADMIN_EMAIL'];
    $send_email->send_to_name = $_ENV['ADMIN_NAME'];

    if ($monitor->isDomainAvailible()) {
        $send_email->subject = $url . " is OKAY";
        $send_email->message = "The domain " . $url . " is <strong>up & running</strong> as at " . date('h:i:s a') . ", on " . date('j, M y') . ". This is cool.";
        if ($always_notify == 'true')
            $send_email->send_email();
    } else {
        $send_email->subject = $url . " is DOWN";
        $send_email->message = "The domain " . $url . " is currently <strong>not running</strong> as at " . date('h:i:s a') . ", on " . date('j, M y') . ". Please do something about this.";
        $send_email->send_email();
    }