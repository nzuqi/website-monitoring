<?php

    date_default_timezone_set("Africa/Nairobi");
    require './vendor/autoload.php';
    $dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
    $dotenv->load();
    include('monitor.php');
    include('email.php');

    $instructions = "<p><small>Your request should look like this: <strong>https://your-domain.com/script-folder/?token=1234&url=https://domain-to-check.com&always-notify=false</string></small></p>";

    if (!isset($_GET['token'])) {
        echo "<p>Token missing in request. Check and try again.</p>" . $instructions;
        exit();
    } else {
        if ($_GET['token'] != $_ENV['REQUEST_TOKEN']) {
            echo "<p>Invalid token in request. Check and try again.</p>" . $instructions;
            exit();
        }
    }
    
    $url = "";

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
    $send_email->sendgrid_api_key = $_ENV['SENDGRID_API_KEY'];
    $send_email->send_to = $_ENV['ADMIN_EMAIL'];
    $send_email->send_to_name = $_ENV['ADMIN_NAME'];

    $domain_available = $monitor->isDomainAvailible();

    if ($domain_available) {
        $send_email->subject = $url . " is OKAY";
        $send_email->message = "The domain " . $url . " is <strong>up & running</strong> as at " . date('h:i:s a') . ", on " . date('j, M y') . ". This is cool.";
        if ($_ENV['ALWAYS_NOTIFY'] == 'TRUE')
            $send_email->send_email();
    } else {
        $send_email->subject = $url . " is DOWN";
        $send_email->message = "The domain " . $url . " is currently <strong>not running</strong> as at " . date('h:i:s a') . ", on " . date('j, M y') . ". Please do something about this.";
        $send_email->send_email();
    }

    echo "<p>Domain check successful.</p>";
    echo "<p><small>";
    echo "<strong>URL</strong>: " . $url . "<br>";
    echo "<strong>Status</strong>: <u>" . ($domain_available ? "Up & running" : "Not running") . "</u><br>";
    echo "<strong>Time</strong>: " . date('j, M Y') . " at " . date('h:i:s a');
    echo "</small></p>";