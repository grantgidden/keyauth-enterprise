<?php
if ($_SESSION['role'] == "Reseller") {
    header("location: ./?page=reseller-licenses");
    die();
}
if ($role == "Manager" && !($permissions & 1024)) {
    misc\auditLog\send("Attempted (and failed) to view app settings.");
    dashboard\primary\error("You weren't granted permission to view this page!");
    die();
}
if (!isset($_SESSION['app'])) {
    dashboard\primary\error("Application not selected");
    die("Application not selected.");
}

if (isset($_POST['validate_custom_domain_api'])) {
    $custom_domain_id = $_POST['validate_custom_domain_api'];
    $url = "https://api.cloudflare.com/client/v4/zones/{$cfZoneIdCache}/custom_hostnames/{$custom_domain_id}";

    $curl = curl_init($url);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

    $headers = array(
    "Authorization: Bearer {$cfApiKeyCache}",
    "Content-Type: application/json",
    );
    curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);

    $resp = curl_exec($curl);
    $statusCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);

    $json = json_decode($resp);

    if($json->result->status == "active") {
        dashboard\primary\success("Custom domain setup successfully!");
        misc\mysql\query("UPDATE `apps` SET `apiCustomDomainId` = ? WHERE `secret` = ?", [$custom_domain_id, $_SESSION['app']]);
    }
    else {
        if($statusCode > 399) {
            dashboard\primary\error($json->errors[0]->message);
        }
        else {
            if($json->verification_errors) {
                dashboard\primary\error("You need to set CNAME to api-worker.keyauth.win then try again!");
            }
            else {
                dashboard\primary\error("Failed to setup custom domain! Please try again");
            }
        }
        ?>
        <div id="validate-custom-domain-api-modal" tabindex="-1"
                        class="fixed grid place-items-center h-screen bg-black bg-opacity-60 z-50 p-4 overflow-x-hidden overflow-y-auto md:inset-0 h-[calc(100%-1rem)] max-h-full">
                                <div class="relative w-full max-w-md max-h-full">
                                        <!-- Modal content -->
                                        <div class="relative bg-[#0f0f17] rounded-lg border-[#1d4ed8] shadow">
                                                <div class="px-6 py-6 lg:px-8">
                                                        <h3 class="mb-4 text-xl font-medium text-white-900">Try again after waiting 1 minute</h3>
                                                        <form class="space-y-6" method="POST">
                                                                <button name="validate_custom_domain_api"
                                                                        value="<?= $custom_domain_id ?>"
                                                                        id="validate_custom_domain_api"
                                                                        class="w-full text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center">Check
                                                                        Domain</button>
                                                        </form>
                                                </div>
                                        </div>
                                </div>
                        </div>
        <?php
    }
}

if (isset($_POST['addhash'])) {
    $resp = misc\app\addHash($_POST['hash']);
    match ($resp) {
        'shorthash' => dashboard\primary\error("Hash is too short!"),
        'failure' => dashboard\primary\error("Failed to add hash!"),
        'success' => dashboard\primary\success("Added hash successfully!"),
        default => dashboard\primary\error("Unhandled Error! Contact us if you need help")
    };
}

if (isset($_POST['resethash'])) {
    $resp = misc\app\resetHash();
    match ($resp) {
        'failure' => dashboard\primary\error("Failed to reset hash!"),
        'success' => dashboard\primary\success("Successfully reset hash!"),
        default => dashboard\primary\error("Unhandled Error! Contact us if you need help")
    };
}

if ($_SESSION['app']) {
    $query = misc\mysql\query("SELECT * FROM `apps` WHERE `secret` = ?", [$_SESSION['app']]);
    if ($query->num_rows > 0) {
        while ($row = mysqli_fetch_array($query->result)) {
            $enabled = $row['enabled'];
            $owner = $row['owner'];
            $hwidcheck = $row['hwidcheck'];
            $forceHwid = $row['forceHwid'];
            $minHwid = $row['minHwid'];
            $vpnblock = $row['vpnblock'];
            $panelstatus = $row['panelstatus'];
            $customDomain = $row['customDomain'];
            $hashcheck = $row['hashcheck'];
            $tokenvalidation = $row["tokensystem"];
            $cooldown = $row['cooldown'];
            $session = $row['session'];
            $cooldownUnit = $row['cooldownUnit'];
            $sessionUnit = $row['sessionUnit'];
            $verr = $row['ver'];
            $dll = $row['download'];
            $webdll = $row['webdownload'];
            $wh = $row['webhook'];
            $ipLogging = $row['ipLogging'];
            $rs = $row['resellerstore'];
            $sellappwhsecret = $row['sellappsecret'];
            $sellappdayproduct = $row['sellappdayproduct'];
            $sellappweekproduct = $row['sellappweekproduct'];
            $sellappmonthproduct = $row['sellappmonthproduct'];
            $sellapplifetimeproduct = $row['sellapplifetimeproduct'];
            $sellixwhsecret = $row['sellixsecret'];
            $sellixdayproduct = $row['sellixdayproduct'];
            $sellixweekproduct = $row['sellixweekproduct'];
            $sellixmonthproduct = $row['sellixmonthproduct'];
            $sellixlifetimeproduct = $row['sellixlifetimeproduct'];
            $shoppywhsecret = $row['shoppysecret'];
            $shoppydayproduct = $row['shoppydayproduct'];
            $shoppyweekproduct = $row['shoppyweekproduct'];
            $shoppymonthproduct = $row['shoppymonthproduct'];
            $shoppylifetimeproduct = $row['shoppylifetimeproduct'];
            $appdisabled = $row['appdisabled'];
            $tokeninvalid = $row["tokeninvalid"];
            $usernametaken = $row['usernametaken'];
            $keynotfound = $row['keynotfound'];
            $keyused = $row['keyused'];
            $nosublevel = $row['nosublevel'];
            $usernamenotfound = $row['usernamenotfound'];
            $passmismatch = $row['passmismatch'];
            $hwidmismatch = $row['hwidmismatch'];
            $noactivesubs = $row['noactivesubs'];
            $hwidblacked = $row['hwidblacked'];
            $pausedsub = $row['pausedsub'];
            $vpnblocked = $row['vpnblocked'];
            $keybanned = $row['keybanned'];
            $userbanned = $row['userbanned'];
            $sessionunauthed  = $row['sessionunauthed'];
            $hashcheckfail  = $row['hashcheckfail'];
            $minUsernameLength  = $row['minUsernameLength'];
            $blockLeakedPasswords  = $row['blockLeakedPasswords'];
            $customerPanelIcon  = $row['customerPanelIcon'];
            $loggedInMsg  = $row['loggedInMsg'];
            $pausedApp  = $row['pausedApp'];
            $unTooShort  = $row['unTooShort'];
            $pwLeaked  = $row['pwLeaked'];
            $chatHitDelay  = $row['chatHitDelay'];
            $enabledFunctions  = $row['enabledFunctions'];
            $apiCustomDomainId  = $row['apiCustomDomainId'];
        }
    }
}

if (isset($_POST['updatesettings'])) {
    $status = misc\etc\sanitize($_POST['statusinput']);
    $hwid = misc\etc\sanitize($_POST['hwidinput']);
    $tokenvalidation = misc\etc\sanitize($_POST["tokeninput"]);
    $forceHwid = misc\etc\sanitize($_POST['forceHwid']);
    $minHwid = misc\etc\sanitize($_POST['minHwid']);
    $vpn = misc\etc\sanitize($_POST['vpninput']);
    $hashstatus = misc\etc\sanitize($_POST['hashinput']);
    $ver = misc\etc\sanitize($_POST['version']);
    $dl = misc\etc\sanitize($_POST['download']);
    $webdl = misc\etc\sanitize($_POST['webdownload']);
    $webhook = misc\etc\sanitize($_POST['webhook']);
    $ipLogging = misc\etc\sanitize($_POST['ipLogging']);
    $resellerstorelink = misc\etc\sanitize($_POST['resellerstore']);
    $panelstatus = misc\etc\sanitize($_POST['panelstatus']);
    $customDomain = misc\etc\sanitize($_POST['customDomain']);
    $cooldownexpiry = misc\etc\sanitize($_POST['cooldownexpiry']);
    $cooldownduration = misc\etc\sanitize($_POST['cooldownduration']) * $cooldownexpiry;
    $sessionexpiry = misc\etc\sanitize($_POST['sessionexpiry']);
    $sessionduration = misc\etc\sanitize($_POST['sessionduration']) * $sessionexpiry;
    $minUsernameLength = misc\etc\sanitize($_POST['minUsernameLength']);
    $blockLeakedPasswords = misc\etc\sanitize($_POST['blockLeakedPasswords']);
    $appdisabledpost = misc\etc\sanitize($_POST['appdisabled']);
    $tokeninvalid = misc\etc\sanitize($_POST['tokeninvalid']);
    $usernametakenpost = misc\etc\sanitize($_POST['usernametaken']);
    $keynotfoundpost = misc\etc\sanitize($_POST['keynotfound']);
    $keyusedpost = misc\etc\sanitize($_POST['keyused']);
    $nosublevelpost = misc\etc\sanitize($_POST['nosublevel']);
    $usernamenotfoundpost = misc\etc\sanitize($_POST['usernamenotfound']);
    $passmismatchpost = misc\etc\sanitize($_POST['passmismatch']);
    $hwidmismatchpost = misc\etc\sanitize($_POST['hwidmismatch']);
    $noactivesubspost = misc\etc\sanitize($_POST['noactivesubs']);
    $hwidblackedpost = misc\etc\sanitize($_POST['hwidblacked']);
    $pausedsubpost = misc\etc\sanitize($_POST['pausedsub']);
    $vpnblockedpost = misc\etc\sanitize($_POST['vpnblocked']);
    $keybannedpost = misc\etc\sanitize($_POST['keybanned']);
    $userbannedpost = misc\etc\sanitize($_POST['userbanned']);
    $sessionunauthedpost = misc\etc\sanitize($_POST['sessionunauthed']);
    $hashcheckfailpost = misc\etc\sanitize($_POST['hashcheckfail']);
    $shoppywebhooksecret = misc\etc\sanitize($_POST['shoppywebhooksecret']);
    $shoppyday = misc\etc\sanitize($_POST['shoppydayproduct']);
    $shoppyweek = misc\etc\sanitize($_POST['shoppyweekproduct']);
    $shoppymonth = misc\etc\sanitize($_POST['shoppymonthproduct']);
    $shoppylife = misc\etc\sanitize($_POST['shoppylifetimeproduct']);
    $sellixwebhooksecret = misc\etc\sanitize($_POST['sellixwebhooksecret']);
    $sellixday = misc\etc\sanitize($_POST['sellixdayproduct']);
    $sellixweek = misc\etc\sanitize($_POST['sellixweekproduct']);
    $sellixmonth = misc\etc\sanitize($_POST['sellixmonthproduct']);
    $sellixlife = misc\etc\sanitize($_POST['sellixlifetimeproduct']);
    $sellappwebhooksecret = misc\etc\sanitize($_POST['sellappwebhooksecret']);
    $sellappday = misc\etc\sanitize($_POST['sellappdayproduct']);
    $sellappweek = misc\etc\sanitize($_POST['sellappweekproduct']);
    $sellappmonth = misc\etc\sanitize($_POST['sellappmonthproduct']);
    $sellapplife = misc\etc\sanitize($_POST['sellapplifetimeproduct']);
    $customerPanelIcon = misc\etc\sanitize($_POST['customerPanelIcon']);
    $loggedInMsg = misc\etc\sanitize($_POST['loggedInMsg']);
    $pausedApp = misc\etc\sanitize($_POST['pausedApp']);
    $unTooShort = misc\etc\sanitize($_POST['unTooShort']);
    $pwLeaked = misc\etc\sanitize($_POST['pwLeaked']);
    $chatHitDelay = misc\etc\sanitize($_POST['chatHitDelay']);
    $enabledFunctions = misc\etc\sanitize($_POST['functionValue']) ?? $enabledFunctions;

    if (!is_null($customDomain) && ($_SESSION['role'] == "seller" || $_SESSION['role'] == "Manager")) {
        if (strpos($customDomain, "http") === 0) {
            dashboard\primary\error("Do not include protocol. Your custom domain should be entered as panel.example.com not https://panel.example.com or http://panel.example.com");
            echo "<meta http-equiv='Refresh' Content='2;'>";
            return;
        }
        $query = misc\mysql\query("SELECT `name`, `owner` FROM `apps` WHERE `customDomain` = ? AND `secret` != ?", [$customDomain, $_SESSION['app']]);
        if ($query->num_rows > 0) {
            $row = mysqli_fetch_array($query->result);
            $name = $row["name"];
            $owner = $row["owner"];
            dashboard\primary\error("The domain {$customDomain} is already being used on app named \"{$name}\" owned by {$owner}. Use a different domain or subdomain please.");
            echo "<meta http-equiv='Refresh' Content='2;'>";
            return;
        }
    } else if (!empty($customDomain)) {
        dashboard\primary\error("You must have seller plan to utilize customer panel");
        echo "<meta http-equiv='Refresh' Content='2;'>";
        return;
    }

    if($_SESSION['role'] == "tester" && !is_null($webhook)) {
        dashboard\primary\error("You must upgrade to developer or seller to use Discord webhook!");
        echo "<meta http-equiv='Refresh' Content='2;'>";
        return;
    }

    if((!is_null($webhook) && !str_contains($webhook, "discord")) || (!is_null($webhook) && str_contains($webhook, "localhost")) || (!is_null($webhook) && str_contains($webhook, "127.0.0.1"))) {
        dashboard\primary\error("Webhook URL is supposed to be a Discord webhook!");
        echo "<meta http-equiv='Refresh' Content='2;'>";
        return;
    }

    if($_SESSION['role'] == "tester" && $vpn) {
        dashboard\primary\error("You must upgrade to developer or seller to use VPN block!");
        echo "<meta http-equiv='Refresh' Content='2;'>";
        return;
    }

    if (!empty($shoppywebhooksecret) && !empty($sellixwebhooksecret)) {
        dashboard\primary\error("You cannot utilize Sellix and Shoppy simultaneously due to conflicting JavaScript code");
        echo "<meta http-equiv='Refresh' Content='2;'>";
        return;
    }

    if ($sessionduration > 604800) {
        dashboard\primary\error("Session duration can be at most 7 days/1 week");
        echo "<meta http-equiv='Refresh' Content='2;'>";
        return;
    }

    $query = misc\mysql\query(
        "UPDATE `apps` SET 
                `cooldown` = ?,
                `customDomain` = NULLIF(?, ''),
                `session` = ?,
                `tokensystem` = ?,
                `cooldownUnit` = ?,
                `sessionUnit` = ?,
                `appdisabled` = ?,
                `tokeninvalid` = ?,
                `hashcheckfail` = ?,
                `sessionunauthed` = ?,
                `userbanned` = ?,
                `vpnblocked` = ?,
                `keybanned` = ?,
                `usernametaken` = ?,
                `keynotfound` = ?,
                `keyused` = ?,
                `nosublevel` = ?,
                `usernamenotfound` = ?,
                `passmismatch` = ?,
                `hwidmismatch` = ?,
                `noactivesubs` = ?,
                `hwidblacked` = ?,
                `pausedsub` = ?,
                `enabled` = ?,
                `minUsernameLength` = ?,
                `blockLeakedPasswords` = ?,
                `hashcheck` = ?,
                `hwidcheck` = ?,
                `forceHwid` = ?,
                `minHwid` = ?,
                `vpnblock` = ?,
                `ver` = ?,
                `download` = NULLIF(?, ''),
                `webdownload` = NULLIF(?, ''),
                `webhook` = NULLIF(?, ''),
                `ipLogging` = ?,
                `resellerstore` = NULLIF(?, ''),
                `sellixsecret` = NULLIF(?, ''),
                `sellixdayproduct` = NULLIF(?, ''),
                `sellixweekproduct` = NULLIF(?, ''),
                `sellixmonthproduct` = NULLIF(?, ''),
                `sellixlifetimeproduct` = NULLIF(?, ''),
                `sellappsecret` = NULLIF(?, ''),
                `sellappdayproduct` = NULLIF(?, ''),
                `sellappweekproduct` = NULLIF(?, ''),
                `sellappmonthproduct` = NULLIF(?, ''),
                `sellapplifetimeproduct` = NULLIF(?, ''),
                `shoppysecret` = NULLIF(?, ''),
                `shoppydayproduct` = NULLIF(?, ''),
                `shoppyweekproduct` = NULLIF(?, ''),
                `shoppymonthproduct` = NULLIF(?, ''),
                `shoppylifetimeproduct` = NULLIF(?, ''),
                `customerPanelIcon` = ?,
                `panelstatus` = ?,
                `loggedInMsg` = ?,
                `pausedApp` = ?,
                `unTooShort` = ?,
                `pwLeaked` = ?,
                `chatHitDelay` = ?,
                `enabledFunctions` = ?
        WHERE `secret` = ?",
        [
            $cooldownduration,
            $customDomain,
            $sessionduration,
            $tokenvalidation,
            $cooldownexpiry,
            $sessionexpiry,
            $appdisabledpost,
            $tokeninvalid,
            $hashcheckfailpost,
            $sessionunauthedpost,
            $userbannedpost,
            $vpnblockedpost,
            $keybannedpost,
            $usernametakenpost,
            $keynotfoundpost,
            $keyusedpost,
            $nosublevelpost,
            $usernamenotfoundpost,
            $passmismatchpost,
            $hwidmismatchpost,
            $noactivesubspost,
            $hwidblackedpost,
            $pausedsubpost,
            $status,
            $minUsernameLength,
            $blockLeakedPasswords,
            $hashstatus,
            $hwid,
            $forceHwid,
            $minHwid,
            $vpn,
            $ver,
            $dl,
            $webdl,
            $webhook,
            $ipLogging,
            $resellerstorelink,
            $sellixwebhooksecret,
            $sellixday,
            $sellixweek,
            $sellixmonth,
            $sellixlife,
            $sellappwebhooksecret,
            $sellappday,
            $sellappweek,
            $sellappmonth,
            $sellapplife,
            $shoppywebhooksecret,
            $shoppyday,
            $shoppyweek,
            $shoppymonth,
            $shoppylife,
            $customerPanelIcon,
            $panelstatus,
            $loggedInMsg,
            $pausedApp,
            $unTooShort,
            $pwLeaked,
            $chatHitDelay,
            $enabledFunctions,
            $_SESSION['app']
        ]
    );

    if ($query->affected_rows > 0) {
        $appName = strtolower($_SESSION["name"]);
        $ownerid = strtolower($_SESSION['ownerid']);
        
        $url = "https://api.cloudflare.com/client/v4/zones/{$cfZoneIdCache}/purge_cache";
        
        $curl = curl_init($url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode(array("files" => ["https://keyauth.win/api/KeyAuthApp-{$appName}-{$ownerid}"])));
        
        $headers = array(
        "Authorization: Bearer {$cfApiKeyCache}",
        "Content-Type: application/json",
        );
        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
                        
        curl_exec($curl);
        
        misc\cache\purge('KeyAuthApp:' . $_SESSION["name"] . ':' . $_SESSION['ownerid']);
        dashboard\primary\success("Successfully set settings!");
    } else {
        dashboard\primary\error("Failed to set settings!");
    }
}

if (isset($_POST['deleteHash'])){
    $hashToDelete = $_POST['deleteHash'];

    $query = misc\mysql\query("UPDATE `apps` SET `hash` = REPLACE(`hash`,? , '') WHERE `secret` = ?", [$hashToDelete, $_SESSION['app']]);
    if ($query->affected_rows > 0) {
        dashboard\primary\success("Successfully deleted hash!");
    } else {
        dashboard\primary\error("Failed to delete hash!");
    }
}
?>

<div class="p-4 bg-[#09090d] block sm:flex items-center justify-between lg:mt-1.5">
        <div class="mb-1 w-full bg-[#0f0f17] rounded-xl">
                <div class="mb-4 p-4">
                        <?php require '../app/layout/breadcrumb.php'; ?>
                        <h1 class="text-xl font-semibold text-white-900 sm:text-2xl">Application Settings</h1>
                        <p class="text-xs text-gray-500">Control your application here. <a
                                        href="https://keyauthdocs.apidog.io/sellerapi/misc/retrieve-application-details"
                                        target="_blank" class="text-blue-600 hover:underline">Learn More</a>. Tip: You can press Ctrl + S to save settings!</p>
                        <br>

                        <div class="mb-4 border-b border-gray-200  ">
                                <ul class="flex flex-wrap -mb-px text-sm font-medium text-center" id="myTab"
                                        data-tabs-toggle="#myTabContent" role="tablist">
                                        <li class="mr-2" role="presentation">
                                                <button class="inline-flex p-4 border-b-2 rounded-t-lg"
                                                        id="appsettings-tab" data-tabs-target="#appsettings"
                                                        type="button" role="tab" aria-controls="appsettings"
                                                        aria-selected="false" data-popover-target="appSettings-popover">
                                                        <svg class="w-5 h-5 mr-1" fill="currentColor"
                                                                viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                                                <path
                                                                        d="M11.55 21H3v-8.55h8.55V21ZM21 21h-8.55v-8.55H21V21Zm-9.45-9.45H3V3h8.55v8.55Zm9.45 0h-8.55V3H21v8.55Z">
                                                                </path>
                                                        </svg>

                                                        Application Functions
                                                </button>
                                                <?php dashboard\primary\popover("appSettings-popover", "App Settings", "Control the main functions of your app."); ?>
                                        </li>
                                        <li class="mr-2" role="presentation">
                                                <button class="inline-flex p-4 border-b-2 border-transparent rounded-t-lg hover:text-gray-600 hover:border-gray-300  "
                                                        id="messages-tab" data-tabs-target="#messages" type="button"
                                                        role="tab" aria-controls="messages" aria-selected="false"
                                                        data-popover-target="alertMessages-popover">
                                                        <svg class="w-5 h-5 mr-1" fill="currentColor"
                                                                viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                                                <path
                                                                        d="M3 4v12c0 1.103.897 2 2 2h3.5l3.5 4 3.5-4H19c1.103 0 2-.897 2-2V4c0-1.103-.897-2-2-2H5c-1.103 0-2 .897-2 2Zm8 1h2v6h-2V5Zm0 8h2v2h-2v-2Z">
                                                                </path>
                                                        </svg>

                                                        Alert Messages
                                                </button>
                                                <?php dashboard\primary\popover("alertMessages-popover", "Alert Messages", "Customize the error messages that users will receive."); ?>
                                        </li>
                                        <li class="mr-2" role="presentation">
                                                <button class="inline-flex p-4 border-b-2 border-transparent rounded-t-lg hover:text-gray-600 hover:border-gray-300 "
                                                        id="storesettings-tab" data-tabs-target="#storesettings"
                                                        type="button" role="tab" aria-controls="storesettings"
                                                        aria-selected="false"
                                                        data-popover-target="storeSettings-popover">
                                                        <svg class="w-5 h-5 mr-1" fill="currentColor"
                                                                viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                                                <path
                                                                        d="M4 20h2V10a1 1 0 0 1 1-1h12V7a1 1 0 0 0-1-1h-3.051c-.252-2.244-2.139-4-4.449-4S6.303 3.756 6.051 6H3a1 1 0 0 0-1 1v11a2 2 0 0 0 2 2Zm6.5-16c1.207 0 2.218.86 2.45 2h-4.9c.232-1.14 1.243-2 2.45-2Z">
                                                                </path>
                                                                <path
                                                                        d="M21 11H9a1 1 0 0 0-1 1v8a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2v-8a1 1 0 0 0-1-1Zm-6 7c-2.757 0-5-2.243-5-5h2c0 1.654 1.346 3 3 3s3-1.346 3-3h2c0 2.757-2.243 5-5 5Z">
                                                                </path>
                                                        </svg>

                                                        Reseller
                                                </button>
                                                <?php dashboard\primary\popover("storeSettings-popover", "Reseller Settings", "Setup your reseller system here"); ?>
                                        </li>
                                        <li class="mr-2" role="presentation">
                                                <button class="inline-flex p-4 border-b-2 border-transparent rounded-t-lg hover:text-gray-600 hover:border-gray-300 "
                                                        id="hashsettings-tab" data-tabs-target="#hashsettings"
                                                        type="button" role="tab" aria-controls="hashsettings"
                                                        aria-selected="false"
                                                        data-popover-target="hashSettings-popover">
                                                        <svg class="w-5 h-5 mr-1" aria-hidden="true" fill="none"
                                                                stroke-width="1.5" stroke="currentColor"
                                                                viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                                                <path d="M7.864 4.243A7.5 7.5 0 0 1 19.5 10.5c0 2.92-.556 5.709-1.568 8.268M5.742 6.364A7.465 7.465 0 0 0 4.5 10.5a7.464 7.464 0 0 1-1.15 3.993m1.989 3.559A11.209 11.209 0 0 0 8.25 10.5a3.75 3.75 0 1 1 7.5 0c0 .527-.021 1.049-.064 1.565M12 10.5a14.94 14.94 0 0 1-3.6 9.75m6.633-4.596a18.666 18.666 0 0 1-2.485 5.33"
                                                                        stroke-linecap="round" stroke-linejoin="round">
                                                                </path>
                                                        </svg>

                                                        Hash Settings
                                                </button>
                                                <?php dashboard\primary\popover("hashSettings-popover", "Hash Settings", "Review your application hashes."); ?>
                                        </li>
                                        <li class="mr-2" role="presentation">
                                                <button class="inline-flex p-4 border-b-2 border-transparent rounded-t-lg hover:text-gray-600 hover:border-gray-300 "
                                                        id="customerPanelSettings-tab"
                                                        data-tabs-target="#customerPanelSettings" type="button"
                                                        role="tab" aria-controls="customerPanelSettings"
                                                        aria-selected="false"
                                                        data-popover-target="customerPanelSettings-popover">
                                                        <svg class="w-5 h-5 mr-1" aria-hidden="true" fill="none"
                                                                stroke-width="1.5" stroke="currentColor"
                                                                viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                                                <path d="M18 18.72a9.094 9.094 0 0 0 3.741-.479 3 3 0 0 0-4.682-2.72m.94 3.198.001.031c0 .225-.012.447-.037.666A11.944 11.944 0 0 1 12 21c-2.17 0-4.207-.576-5.963-1.584A6.062 6.062 0 0 1 6 18.719m12 0a5.971 5.971 0 0 0-.941-3.197m0 0A5.995 5.995 0 0 0 12 12.75a5.995 5.995 0 0 0-5.058 2.772m0 0a3 3 0 0 0-4.681 2.72 8.986 8.986 0 0 0 3.74.477m.94-3.197a5.971 5.971 0 0 0-.94 3.197M15 6.75a3 3 0 1 1-6 0 3 3 0 0 1 6 0Zm6 3a2.25 2.25 0 1 1-4.5 0 2.25 2.25 0 0 1 4.5 0Zm-13.5 0a2.25 2.25 0 1 1-4.5 0 2.25 2.25 0 0 1 4.5 0Z"
                                                                        stroke-linecap="round" stroke-linejoin="round">
                                                                </path>
                                                        </svg>

                                                        Customer Panel Settings
                                                </button>
                                                <?php dashboard\primary\popover("customerPanelSettings-popover", "Customer Panel Settings", "Control your customer panel here"); ?>
                                        </li>
                                        <li class="mr-2" role="presentation">
                                                <button class="inline-flex p-4 border-b-2 border-transparent rounded-t-lg hover:text-gray-600 hover:border-gray-300 "
                                                        id="appFunctionToggle-tab" data-tabs-target="#appFunctionToggle"
                                                        type="button" role="tab" aria-controls="appFunctionToggle"
                                                        aria-selected="false"
                                                        data-popover-target="appFunctionToggle-popover">
                                                        <svg class="w-5 h-5 mr-1" aria-hidden="true" fill="none"
                                                                stroke-width="1.5" stroke="currentColor"
                                                                viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                                                <path d="M17.25 6.75 22.5 12l-5.25 5.25m-10.5 0L1.5 12l5.25-5.25m7.5-3-4.5 16.5"
                                                                        stroke-linecap="round" stroke-linejoin="round">
                                                                </path>
                                                        </svg>

                                                        Function Management
                                                </button>
                                                <?php dashboard\primary\popover("appFunctionToggle-popover", " Application Functions Toggle", "Enable/Disable your application functions."); ?>
                                        </li>
                                </ul>
                        </div>

                        <div class="p-4">
                        </div>
                        <form method="post">
                                <div id="myTabContent">
                                        <div class="hidden p-4 rounded-lg grid gap-7" id="appsettings" role="tabpanel"
                                                aria-labelledby="appsettings-tab">
                                                <button type="button"
                                                        class="inline-flex text-white bg-blue-700 hover:opacity-60 focus:ring-0 font-medium rounded-lg text-sm px-5 py-2.5 mb-2 transition duration-200"
                                                        data-modal-toggle="add-new-custom-domain"
                                                        data-modal-target="add-new-custom-domain">
                                                        <i class="lni lni-circle-plus mr-2 mt-1"></i>Add Custom domain
                                                </button>

                                                <div id="lol" class="grid grid-cols-1 lg:grid-cols-4 2xl:grid-cols-8">
                                                        <div class="relative mb-4 " style="margin-right: 10px;">
                                                                <select id="statusinput" name="statusinput"
                                                                        class="bg-[#0f0f17] border border-gray-700 text-white-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5"
                                                                        data-popover-target="status-popover">
                                                                        <option value="0"
                                                                                <?= $enabled == 0 ? ' selected="selected"' : ''; ?>>
                                                                                Disabled
                                                                        </option>
                                                                        <option value="1"
                                                                                <?= $enabled == 1 ? ' selected="selected"' : ''; ?>>
                                                                                Enabled
                                                                        </option>
                                                                </select>
                                                                <label for="statusinput"
                                                                        class="absolute text-sm text-gray-400 duration-300 transform -translate-y-4 scale-75 top-2 z-10 origin-[0] bg-[#0f0f17] px-2 peer-focus:px-2 peer-focus:text-blue-600 peer-placeholder-shown:scale-100 peer-placeholder-shown:-translate-y-1/2 peer-placeholder-shown:top-1/2 peer-focus:top-2 peer-focus:scale-75 peer-focus:-translate-y-4 left-1">App
                                                                        Status</label>
                                                                <?php dashboard\primary\popover("status-popover", "App Status", "Allow users to open your app or not."); ?>
                                                        </div>

                                                        <div class="relative mb-4 " style="margin-right: 10px;">
                                                                <select id="hwidinput" name="hwidinput"
                                                                        class="bg-[#0f0f17] border border-gray-700 text-white-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5"
                                                                        data-popover-target="hwidlock-popover">
                                                                        <option value="0"
                                                                                <?= $hwidcheck == 0 ? ' selected="selected"' : ''; ?>>
                                                                                Disabled
                                                                        </option>
                                                                        <option value="1"
                                                                                <?= $hwidcheck == 1 ? ' selected="selected"' : ''; ?>>
                                                                                Enabled
                                                                        </option>
                                                                </select>
                                                                <label for="hwidinput"
                                                                        class="absolute text-sm text-gray-400 duration-300 transform -translate-y-4 scale-75 top-2 z-10 origin-[0] bg-[#0f0f17] px-2 peer-focus:px-2 peer-focus:text-blue-600 peer-placeholder-shown:scale-100 peer-placeholder-shown:-translate-y-1/2 peer-placeholder-shown:top-1/2 peer-focus:top-2 peer-focus:scale-75 peer-focus:-translate-y-4 left-1">HWID
                                                                        Lock</label>
                                                                <?php dashboard\primary\popover("hwidlock-popover", "HWID Lock", "Lock users to a value from your user's computer which only changes
                                    if they reinstall windows. Use this to prevent people from sharing your product."); ?>
                                                        </div>

                                                        <div class="relative mb-4 " style="margin-right: 10px;">
                                                                <select id="forceHwid" name="forceHwid"
                                                                        class="bg-[#0f0f17] border border-gray-700 text-white-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5"
                                                                        data-popover-target="forceHwid-popover">
                                                                        <option value="0"
                                                                                <?= $forceHwid == 0 ? ' selected="selected"' : ''; ?>>
                                                                                Disabled
                                                                        </option>
                                                                        <option value="1"
                                                                                <?= $forceHwid == 1 ? ' selected="selected"' : ''; ?>>
                                                                                Enabled
                                                                        </option>
                                                                </select>
                                                                <label for="forceHwid"
                                                                        class="absolute text-sm text-gray-400 duration-300 transform -translate-y-4 scale-75 top-2 z-10 origin-[0] bg-[#0f0f17] px-2 peer-focus:px-2 peer-focus:text-blue-600 peer-placeholder-shown:scale-100 peer-placeholder-shown:-translate-y-1/2 peer-placeholder-shown:top-1/2 peer-focus:top-2 peer-focus:scale-75 peer-focus:-translate-y-4 left-1">Force
                                                                        HWID</label>
                                                                <?php dashboard\primary\popover("forceHwid-popover", "Force HWID", "Prevent users from logging in with a blank HWID (disable this for PHP)"); ?>
                                                        </div>
                                                        <div class="relative mb-4 " style="margin-right: 10px;">
                                                                <select id="vpninput" name="vpninput"
                                                                        class="bg-[#0f0f17] border border-gray-700 text-white-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5"
                                                                        data-popover-target="vpnblock-popover">
                                                                        <option value="0"
                                                                                <?= $vpnblock == 0 ? ' selected="selected"' : ''; ?>>
                                                                                Disabled
                                                                        </option>
                                                                        <option value="1"
                                                                                <?= $vpnblock == 1 ? ' selected="selected"' : ''; ?>>
                                                                                Enabled
                                                                        </option>
                                                                </select>
                                                                <label for="vpninput"
                                                                        class="absolute text-sm text-gray-400 duration-300 transform -translate-y-4 scale-75 top-2 z-10 origin-[0] bg-[#0f0f17] px-2 peer-focus:px-2 peer-focus:text-blue-600 peer-placeholder-shown:scale-100 peer-placeholder-shown:-translate-y-1/2 peer-placeholder-shown:top-1/2 peer-focus:top-2 peer-focus:scale-75 peer-focus:-translate-y-4 left-1">VPN
                                                                        Block</label>
                                                                <?php dashboard\primary\popover("vpnblock-popover", "VPN", "Block IP addresses associated with VPNs"); ?>
                                                        </div>
                                                        <div class="relative mb-4 " style="margin-right: 10px;">
                                                                <select id="hashinput" name="hashinput"
                                                                        class="bg-[#0f0f17] border border-gray-700 text-white-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5"
                                                                        data-popover-target="hashCheck-popover">
                                                                        <option value="0"
                                                                                <?= $hashcheck == 0 ? ' selected="selected"' : ''; ?>>
                                                                                Disabled
                                                                        </option>
                                                                        <option value="1"
                                                                                <?= $hashcheck == 1 ? ' selected="selected"' : ''; ?>>
                                                                                Enabled
                                                                        </option>
                                                                </select>
                                                                <label for="hashinput"
                                                                        class="absolute text-sm text-gray-400 duration-300 transform -translate-y-4 scale-75 top-2 z-10 origin-[0] bg-[#0f0f17] px-2 peer-focus:px-2 peer-focus:text-blue-600 peer-placeholder-shown:scale-100 peer-placeholder-shown:-translate-y-1/2 peer-placeholder-shown:top-1/2 peer-focus:top-2 peer-focus:scale-75 peer-focus:-translate-y-4 left-1">Hash
                                                                        Check</label>
                                                                <?php dashboard\primary\popover("hashCheck-popover", "Hash Check", "Checks whether the application has been modified since the last time you
                                    pressed the reset hash button. Used to prevent people from altering/bypassing your app."); ?>
                                                        </div>

                                                        <div class="relative mb-4 " style="margin-right: 10px;">
                                                                <select id="blockLeakedPasswords"
                                                                        name="blockLeakedPasswords"
                                                                        class="bg-[#0f0f17] border border-gray-700 text-white-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5"
                                                                        data-popover-target="blockLeakedPW-popover">
                                                                        <option value="0"
                                                                                <?= $blockLeakedPasswords == 0 ? ' selected="selected"' : ''; ?>>
                                                                                Disabled
                                                                        </option>
                                                                        <option value="1"
                                                                                <?= $blockLeakedPasswords == 1 ? ' selected="selected"' : ''; ?>>
                                                                                Enabled
                                                                        </option>
                                                                </select>
                                                                <label for="blockLeakedPasswords"
                                                                        class="absolute text-sm text-gray-400 duration-300 transform -translate-y-4 scale-75 top-2 z-10 origin-[0] bg-[#0f0f17] px-2 peer-focus:px-2 peer-focus:text-blue-600 peer-placeholder-shown:scale-100 peer-placeholder-shown:-translate-y-1/2 peer-placeholder-shown:top-1/2 peer-focus:top-2 peer-focus:scale-75 peer-focus:-translate-y-4 left-1">Block
                                                                        Leaked PW</label>
                                                                <?php dashboard\primary\popover("blockLeakedPW-popover", "Block Leaked Passwords", "Prevent users from using leaked passwords when registering an account."); ?>
                                                        </div>
                                                        <div class="relative mb-4 " style="margin-right: 10px;">
                                                                <select id="tokeninput" name="tokeninput"
                                                                        class="bg-[#0f0f17] border border-gray-700 text-white-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5"
                                                                        data-popover-target="tokeninput-popover">
                                                                        <option value="0"
                                                                                <?= $tokenvalidation == 0 ? ' selected="selected"' : ''; ?>>
                                                                                Disabled
                                                                        </option>
                                                                        <option value="1"
                                                                                <?= $tokenvalidation == 1 ? ' selected="selected"' : ''; ?>>
                                                                                Enabled
                                                                        </option>
                                                                </select>
                                                                <label for="tokeninput"
                                                                        class="absolute text-sm text-gray-400 duration-300 transform -translate-y-4 scale-75 top-2 z-10 origin-[0] bg-[#0f0f17] px-2 peer-focus:px-2 peer-focus:text-blue-600 peer-placeholder-shown:scale-100 peer-placeholder-shown:-translate-y-1/2 peer-placeholder-shown:top-1/2 peer-focus:top-2 peer-focus:scale-75 peer-focus:-translate-y-4 left-1">Token
                                                                        Validation</label>
                                                                <?php dashboard\primary\popover("tokeninput-popover", "Token Validation", "Checks to see if a user has a valid token to use your application."); ?>
                                                        </div>
                                                </div>

                                                <div class="relative">
                                                        <input type="number" inputtype="numeric" name="minHwid"
                                                                id="minHwid"
                                                                max="999"
                                                                class="block px-2.5 pb-2.5 pt-4 w-full text-sm text-white-900 bg-transparent rounded-lg border-1 border-gray-700 appearance-none focus:outline-none focus:ring-0 focus:border-blue-600 peer"
                                                                value="<?= $minHwid; ?>"
                                                                data-popover-target="minHwid-popover">
                                                        <label for="minHwid"
                                                                class="absolute text-sm text-gray-500 duration-300 transform -translate-y-4 scale-75 top-2 z-10 origin-[0] bg-[#0f0f17] px-2 peer-focus:px-2 peer-focus:text-blue-600 peer-focus:text-blue-500 peer-placeholder-shown:scale-100 peer-placeholder-shown:-translate-y-1/2 peer-placeholder-shown:top-1/2 peer-focus:top-2 peer-focus:scale-75 peer-focus:-translate-y-4 left-1">Minimum
                                                                HWID Length</label>
                                                        <?php dashboard\primary\popover("minHwid-popover", "Minimum HWID Length", "Prevents users from logging in with a shorter HWID than the assigned value."); ?>
                                                </div>
                                                <div class="relative">
                                                        <input type="text" inputtype="numeric" maxlength="5"
                                                                name="version" id="version"
                                                                class="block px-2.5 pb-2.5 pt-4 w-full text-sm text-white-900 bg-transparent rounded-lg border-1 border-gray-700 appearance-none focus:outline-none focus:ring-0 focus:border-blue-600 peer"
                                                                value="<?= $verr; ?>" required
                                                                data-popover-target="version-popover">
                                                        <label for="version"
                                                                class="absolute text-sm text-gray-500 duration-300 transform -translate-y-4 scale-75 top-2 z-10 origin-[0] bg-[#0f0f17] px-2 peer-focus:px-2 peer-focus:text-blue-600 peer-focus:text-blue-500 peer-placeholder-shown:scale-100 peer-placeholder-shown:-translate-y-1/2 peer-placeholder-shown:top-1/2 peer-focus:top-2 peer-focus:scale-75 peer-focus:-translate-y-4 left-1">Application
                                                                Version</label>
                                                        <?php dashboard\primary\popover("version-popover", "Version", "The version of your application. Make sure to change it in your application
                                as well."); ?>
                                                </div>
                                                <div class="relative">
                                                        <input type="url" maxlength="120" name="download" id="download"
                                                                class="block px-2.5 pb-2.5 pt-4 w-full text-sm text-white-900 bg-transparent rounded-lg border-1 border-gray-700 appearance-none focus:outline-none focus:ring-0 focus:border-blue-600 peer"
                                                                value="<?= $dll; ?>" placeholder=" "
                                                                data-popover-target="download-popover">
                                                        <label for="download"
                                                                class="absolute text-sm text-gray-500 duration-300 transform -translate-y-4 scale-75 top-2 z-10 origin-[0] bg-[#0f0f17] px-2 peer-focus:px-2 peer-focus:text-blue-600 peer-focus:text-blue-500 peer-placeholder-shown:scale-100 peer-placeholder-shown:-translate-y-1/2 peer-placeholder-shown:top-1/2 peer-focus:top-2 peer-focus:scale-75 peer-focus:-translate-y-4 left-1">Auto-Update
                                                                Download Link</label>
                                                        <?php dashboard\primary\popover("download-popover", "Download Link", "This is the link that will open if the version is different than the one in your
                                application. (AKA auto-update)."); ?>
                                                </div>
                                                <div class="relative">
                                                        <input type="url" name="webdownload" id="webdownload"
                                                                class="block px-2.5 pb-2.5 pt-4 w-full text-sm text-white-900 bg-transparent rounded-lg border-1 border-gray-700 appearance-none focus:outline-none focus:ring-0 focus:border-blue-600 peer"
                                                                value="<?= $webdll; ?>" placeholder=" "
                                                                data-popover-target="webDownload-popover">
                                                        <label for="webdownload"
                                                                class="absolute text-sm text-gray-500 duration-300 transform -translate-y-4 scale-75 top-2 z-10 origin-[0] bg-[#0f0f17] px-2 peer-focus:px-2 peer-focus:text-blue-600 peer-focus:text-blue-500 peer-placeholder-shown:scale-100 peer-placeholder-shown:-translate-y-1/2 peer-placeholder-shown:top-1/2 peer-focus:top-2 peer-focus:scale-75 peer-focus:-translate-y-4 left-1">Webloader
                                                                Download Link</label>
                                                        <?php dashboard\primary\popover("webDownload-popover", "Web Downloader", "URL link for the web loader. (this will enable the web loader if it is not empty)"); ?>
                                                </div>
                                                
                                                <!-- Alert Box -->
                                                <div id="alert" class="flex items-center p-4 mb-4 text-yellow-800 rounded-lg bg-[#09090d]"
                                                    role="alert">
                                                    <svg class="flex-shrink-0 w-4 h-4" aria-hidden="true" xmlns="http://www.w3.org/2000/svg"
                                                        fill="currentColor" viewBox="0 0 20 20">
                                                        <path
                                                            d="M10 .5a9.5 9.5 0 1 0 9.5 9.5A9.51 9.51 0 0 0 10 .5ZM9.5 4a1.5 1.5 0 1 1 0 3 1.5 1.5 0 0 1 0-3ZM12 15H8a1 1 0 0 1 0-2h1v-3H8a1 1 0 0 1 0-2h2a1 1 0 0 1 1 1v4h1a1 1 0 0 1 0 2Z" />
                                                    </svg>
                                                    <span class="sr-only">Info</span>
                                                    <div class="ml-3 text-sm font-medium text-yellow-500">
                                                        Check out <a
                                                            href="https://vaultcord.com/?utm_source=keyauth" target="_blank"
                                                            class="font-semibold underline hover:no-underline">VaultCord</a> - Free Discord backup bot. Backup all members & your entire Discord server! Avoid Discord term waves and server nukes <a
                                                            href="https://vaultcord.com/?utm_source=keyauth" target="_blank"
                                                            class="font-semibold underline hover:no-underline">(click here)</a>
                                                    </div>
                                                </div>
                                                <!-- End Alert Box -->

                                                <div class="relative">
                                                        <input type="text" name="webhook" id="webhook"
                                                                class="blur-sm hover:blur-none transition duration-500 block px-2.5 pb-2.5 pt-4 w-full text-sm text-white-900 bg-transparent rounded-lg border-1 border-gray-700 appearance-none focus:outline-none focus:ring-0 focus:border-blue-600 peer"
                                                                value="<?= $wh; ?>" placeholder=" "
                                                                data-popover-target="webhook-popover">
                                                        <label for="webhook"
                                                                class="absolute text-sm text-gray-500 duration-300 transform -translate-y-4 scale-75 top-2 z-10 origin-[0] bg-[#0f0f17] px-2 peer-focus:px-2 peer-focus:text-blue-600 peer-focus:text-blue-500 peer-placeholder-shown:scale-100 peer-placeholder-shown:-translate-y-1/2 peer-placeholder-shown:top-1/2 peer-focus:top-2 peer-focus:scale-75 peer-focus:-translate-y-4 left-1">Discord
                                                                Webhook Link</label>
                                                        <?php dashboard\primary\popover("webhook-popover", "Webhook", "Receive secure Discord webhooks for logs/activity."); ?>
                                                </div>
                                                <div class="relative">
                                                                <select id="ipLogging" name="ipLogging"
                                                                        class="bg-[#0f0f17] border border-gray-700 text-white-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5"
                                                                        data-popover-target="ipLogging-popover">
                                                                        <option value="0"
                                                                                <?= $ipLogging == 0 ? ' selected="selected"' : ''; ?>>
                                                                                Disabled
                                                                        </option>
                                                                        <option value="1"
                                                                                <?= $ipLogging == 1 ? ' selected="selected"' : ''; ?>>
                                                                                Enabled
                                                                        </option>
                                                                </select>
                                                                <label for="ipLogging"
                                                                        class="absolute text-sm text-gray-400 duration-300 transform -translate-y-4 scale-75 top-2 z-10 origin-[0] bg-[#0f0f17] px-2 peer-focus:px-2 peer-focus:text-blue-600 peer-placeholder-shown:scale-100 peer-placeholder-shown:-translate-y-1/2 peer-placeholder-shown:top-1/2 peer-focus:top-2 peer-focus:scale-75 peer-focus:-translate-y-4 left-1">Show IPs on Discord</label>
                                                                <?php dashboard\primary\popover("ipLogging-popover", "Show IPs", "Disable if you don't want IPs visible in Discord webhook logs"); ?>
                                                        </div>
                                                <div class="relative">
                                                        <select id="cooldownexpiry" name="cooldownexpiry"
                                                                class="bg-[#0f0f17] border border-gray-700 text-white-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5"
                                                                data-popover-target="cooldownExpiry-popover">
                                                                <option value="1"
                                                                        <?= $cooldownUnit == 1 ? ' selected="selected"' : ''; ?>>
                                                                        Seconds
                                                                </option>
                                                                <option value="60"
                                                                        <?= $cooldownUnit == 60 ? ' selected="selected"' : ''; ?>>
                                                                        Minutes
                                                                </option>
                                                                <option value="3600"
                                                                        <?= $cooldownUnit == 3600 ? ' selected="selected"' : ''; ?>>
                                                                        Hours
                                                                </option>
                                                                <option value="86400"
                                                                        <?= $cooldownUnit == 86400 ? ' selected="selected"' : ''; ?>>
                                                                        Days
                                                                </option>
                                                                <option value="604800"
                                                                        <?= $cooldownUnit == 604800 ? ' selected="selected"' : ''; ?>>
                                                                        Weeks</option>
                                                                <option value="2629743"
                                                                        <?= $cooldownUnit == 2629743 ? ' selected="selected"' : ''; ?>>
                                                                        Months</option>
                                                                <option value="31556926"
                                                                        <?= $cooldownUnit == 31556926 ? ' selected="selected"' : ''; ?>>
                                                                        Years</option>
                                                                <option value="315569260"
                                                                        <?= $cooldownUnit == 315569260 ? ' selected="selected"' : ''; ?>>
                                                                        Lifetime</option>
                                                        </select>
                                                        <label for="cooldownexpiry"
                                                                class="absolute text-sm text-gray-500 duration-300 transform -translate-y-4 scale-75 top-2 z-10 origin-[0] bg-[#0f0f17] px-2 peer-focus:px-2 peer-focus:text-blue-600 peer-focus:text-blue-500 peer-placeholder-shown:scale-100 peer-placeholder-shown:-translate-y-1/2 peer-placeholder-shown:top-1/2 peer-focus:top-2 peer-focus:scale-75 peer-focus:-translate-y-4 left-1">HWID
                                                                Reset Cooldown Unit</label>
                                                        <?php dashboard\primary\popover("cooldownExpiry-popover", "HWID Reset Cooldown (unit)", "The unit before a user can HWID reset again."); ?>
                                                </div>
                                                <div class="relative">
                                                        <input type="number" inputmode="numeric" name="cooldownduration"
                                                                max="999"
                                                                id="cooldownduration"
                                                                class="block px-2.5 pb-2.5 pt-4 w-full text-sm text-white-900 bg-transparent rounded-lg border-1 border-gray-700 appearance-none focus:outline-none focus:ring-0 focus:border-blue-600 peer"
                                                                value="<?= $cooldown / $cooldownUnit; ?>"
                                                                data-popover-target="cooldownDuration-popover">
                                                        <label for="cooldownduration"
                                                                class="absolute text-sm text-gray-500 duration-300 transform -translate-y-4 scale-75 top-2 z-10 origin-[0] bg-[#0f0f17] px-2 peer-focus:px-2 peer-focus:text-blue-600 peer-focus:text-blue-500 peer-placeholder-shown:scale-100 peer-placeholder-shown:-translate-y-1/2 peer-placeholder-shown:top-1/2 peer-focus:top-2 peer-focus:scale-75 peer-focus:-translate-y-4 left-1">HWID
                                                                Reset Cooldown Duration</label>
                                                        <?php dashboard\primary\popover("cooldownDuration-popover", "HWID Reset Cooldown (duration)", "The duration before a user can HWID reset again. (Unit * Duration = cooldown)"); ?>
                                                </div>
                                                <div class="relative">
                                                        <select id="sessionexpiry" name="sessionexpiry"
                                                                class="bg-[#0f0f17] border border-gray-700 text-white-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5"
                                                                data-popover-target="sessionExpiry-popover">
                                                                <option value="1"
                                                                        <?= $sessionUnit == 1 ? ' selected="selected"' : ''; ?>>
                                                                        Seconds
                                                                </option>
                                                                <option value="60"
                                                                        <?= $sessionUnit == 60 ? ' selected="selected"' : ''; ?>>
                                                                        Minutes
                                                                </option>
                                                                <option value="3600"
                                                                        <?= $sessionUnit == 3600 ? ' selected="selected"' : ''; ?>>
                                                                        Hours
                                                                </option>
                                                                <option value="86400"
                                                                        <?= $sessionUnit == 86400 ? ' selected="selected"' : ''; ?>>
                                                                        Days
                                                                </option>
                                                                <option value="604800"
                                                                        <?= $sessionUnit == 604800 ? ' selected="selected"' : ''; ?>>
                                                                        Weeks
                                                                </option>
                                                        </select>
                                                        <label for="sessionexpiry"
                                                                class="absolute text-sm text-gray-500 duration-300 transform -translate-y-4 scale-75 top-2 z-10 origin-[0] bg-[#0f0f17] px-2 peer-focus:px-2 peer-focus:text-blue-600 peer-focus:text-blue-500 peer-placeholder-shown:scale-100 peer-placeholder-shown:-translate-y-1/2 peer-placeholder-shown:top-1/2 peer-focus:top-2 peer-focus:scale-75 peer-focus:-translate-y-4 left-1">Session
                                                                Expiry Unit</label>
                                                        <?php dashboard\primary\popover("sessionExpiry-popover", "Session Expiry (unit)", "The unit before the users session expires (logs out)"); ?>
                                                </div>
                                                <div class="relative">
                                                        <input type="text" inputmode="numeric" name="sessionduration"
                                                                id="sessionduration"
                                                                class="block px-2.5 pb-2.5 pt-4 w-full text-sm text-white-900 bg-transparent rounded-lg border-1 border-gray-700 appearance-none  focus:outline-none focus:ring-0 focus:border-blue-600 peer"
                                                                value="<?= $session / $sessionUnit; ?>" required
                                                                data-popover-target="sessionDuration-popover">
                                                        <label for="sessionduration"
                                                                class="absolute text-sm text-gray-500 duration-300 transform -translate-y-4 scale-75 top-2 z-10 origin-[0] bg-[#0f0f17] px-2 peer-focus:px-2 peer-focus:text-blue-600 peer-focus:text-blue-500 peer-placeholder-shown:scale-100 peer-placeholder-shown:-translate-y-1/2 peer-placeholder-shown:top-1/2 peer-focus:top-2 peer-focus:scale-75 peer-focus:-translate-y-4 left-1">Session
                                                                Expiry Duration</label>
                                                        <?php dashboard\primary\popover("sessionDuration-popover", "Session Expiry (duration)", "The duration before the users session expires (logs out. Unit * Duration = expiry)"); ?>
                                                </div>
                                                <div class="relative">
                                                        <input type="text" inputmode="numeric" name="minUsernameLength"
                                                                id="minUsernameLength"
                                                                class="block px-2.5 pb-2.5 pt-4 w-full text-sm text-white-900 bg-transparent rounded-lg border-1 border-gray-700 appearance-none focus:outline-none focus:ring-0 focus:border-blue-600 peer"
                                                                value="<?= $minUsernameLength; ?>" required
                                                                data-popover-target="minUsernameLength-popover">
                                                        <label for="minUsernameLength"
                                                                class="absolute text-sm text-gray-500 duration-300 transform -translate-y-4 scale-75 top-2 z-10 origin-[0] bg-[#0f0f17] px-2 peer-focus:px-2 peer-focus:text-blue-600 peer-focus:text-blue-500 peer-placeholder-shown:scale-100 peer-placeholder-shown:-translate-y-1/2 peer-placeholder-shown:top-1/2 peer-focus:top-2 peer-focus:scale-75 peer-focus:-translate-y-4 left-1">Minimum
                                                                username length</label>
                                                        <?php dashboard\primary\popover("minUsernameLength-popover", "Minimum Username Length", "Prevents users from creating an account with a username less than the 
                                given value."); ?>
                                                </div>
                                        </div>
                                        <div class="hidden p-4 rounded-lg grid gap-4 -mt-8" id="messages"
                                                role="tabpanel" aria-labelledby="messages-tab">
                                                <!-- Alert Box -->
                                                <div id="alert-4"
                                                        class="flex items-center p-4 mb-4 text-red-600 rounded-lg bg-[#09090d]"
                                                        role="role">
                                                        <svg class="flex-shrink-0 w-4 h-4" aria-hidden="true"
                                                                xmlns="http://www.w3.org/2000/svg" fill="currentColor"
                                                                viewBox="0 0 20 20">
                                                                <path
                                                                        d="M10 .5a9.5 9.5 0 1 0 9.5 9.5A9.51 9.51 0 0 0 10 .5ZM9.5 4a1.5 1.5 0 1 1 0 3 1.5 1.5 0 0 1 0-3ZM12 15H8a1 1 0 0 1 0-2h1v-3H8a1 1 0 0 1 0-2h2a1 1 0 0 1 1 1v4h1a1 1 0 0 1 0 2Z" />
                                                        </svg>
                                                        <div class="ml-3 text-sm font-medium">
                                                                It's <b>highly</b> recommended to change these to
                                                                something custom!
                                                        </div>
                                                </div>
                                                <!-- End Alert Box -->
                                                <div class="relative">
                                                        <input type="text" maxlength="100" name="appdisabled"
                                                                id="appdisabled"
                                                                class="block px-2.5 pb-2.5 pt-4 w-full text-sm text-white bg-transparent rounded-lg border-1 border-gray-700 appearance-none focus:ring-0 peer"
                                                                autocomplete="on" value="<?= $appdisabled; ?>" required>
                                                        <label for="appdisabled"
                                                                class="absolute text-sm text-gray-400 duration-300 transform -translate-y-4 scale-75 top-2 origin-[0] bg-[#0f0f17] px-2 peer-focus:px-2 peer-focus:text-blue-600 peer-placeholder-shown:scale-100 peer-placeholder-shown:-translate-y-1/2 peer-placeholder-shown:top-1/2 peer-focus:top-2 peer-focus:scale-75 peer-focus:-translate-y-4 left-1">Application
                                                                Disabled Message</label>
                                                </div>
                                                <div class="relative">
                                                        <input type="text" maxlength="100" name="tokeninvalid"
                                                                id="tokeninvalid"
                                                                class="block px-2.5 pb-2.5 pt-4 w-full text-sm text-white bg-transparent rounded-lg border-1 border-gray-700 appearance-none focus:ring-0 peer"
                                                                autocomplete="on" value="<?= $tokeninvalid; ?>"
                                                                required>
                                                        <label for="tokeninvalid"
                                                                class="absolute text-sm text-gray-400 duration-300 transform -translate-y-4 scale-75 top-2 origin-[0] bg-[#0f0f17] px-2 peer-focus:px-2 peer-focus:text-blue-600 peer-placeholder-shown:scale-100 peer-placeholder-shown:-translate-y-1/2 peer-placeholder-shown:top-1/2 peer-focus:top-2 peer-focus:scale-75 peer-focus:-translate-y-4 left-1">Token
                                                                Validation Message</label>
                                                </div>
                                                <div class="relative">
                                                        <input type="text" maxlength="100" name="hashcheckfail"
                                                                id="hashcheckfail"
                                                                class="block px-2.5 pb-2.5 pt-4 w-full text-sm text-white bg-transparent rounded-lg border-1 border-gray-700 appearance-none focus:ring-0 peer"
                                                                autocomplete="on" value="<?= $hashcheckfail; ?>"
                                                                required>
                                                        <label for="hashcheckfail"
                                                                class="absolute text-sm text-gray-400 duration-300 transform -translate-y-4 scale-75 top-2 origin-[0] bg-[#0f0f17] px-2 peer-focus:px-2 peer-focus:text-blue-600 peer-placeholder-shown:scale-100 peer-placeholder-shown:-translate-y-1/2 peer-placeholder-shown:top-1/2 peer-focus:top-2 peer-focus:scale-75 peer-focus:-translate-y-4 left-1">Mismatch
                                                                Program Hash Message
                                                        </label>
                                                </div>
                                                <div class="relative">
                                                        <input type="text" maxlength="100" name="vpnblocked"
                                                                id="vpnblocked"
                                                                class="block px-2.5 pb-2.5 pt-4 w-full text-sm text-white bg-transparent rounded-lg border-1 border-gray-700 appearance-none focus:ring-0 peer"
                                                                autocomplete="on" value="<?= $vpnblocked; ?>" required>
                                                        <label for="vpnblocked"
                                                                class="absolute text-sm text-gray-400 duration-300 transform -translate-y-4 scale-75 top-2 origin-[0] bg-[#0f0f17] px-2 peer-focus:px-2 peer-focus:text-blue-600 peer-placeholder-shown:scale-100 peer-placeholder-shown:-translate-y-1/2 peer-placeholder-shown:top-1/2 peer-focus:top-2 peer-focus:scale-75 peer-focus:-translate-y-4 left-1">VPNs
                                                                are blocked on this application
                                                        </label>
                                                </div>
                                                <div class="relative">
                                                        <input type="text" maxlength="100" name="usernametaken"
                                                                id="usernametaken"
                                                                class="block px-2.5 pb-2.5 pt-4 w-full text-sm text-white bg-transparent rounded-lg border-1 border-gray-700 appearance-none focus:ring-0 peer"
                                                                autocomplete="on" value="<?= $usernametaken; ?>"
                                                                required>
                                                        <label for="usernametaken"
                                                                class="absolute text-sm text-gray-400 duration-300 transform -translate-y-4 scale-75 top-2 origin-[0] bg-[#0f0f17] px-2 peer-focus:px-2 peer-focus:text-blue-600 peer-placeholder-shown:scale-100 peer-placeholder-shown:-translate-y-1/2 peer-placeholder-shown:top-1/2 peer-focus:top-2 peer-focus:scale-75 peer-focus:-translate-y-4 left-1">Username
                                                                already taken, choose a different one
                                                        </label>
                                                </div>
                                                <div class="relative">
                                                        <input type="text" maxlength="100" name="keynotfound"
                                                                id="keynotfound"
                                                                class="block px-2.5 pb-2.5 pt-4 w-full text-sm text-white bg-transparent rounded-lg border-1 border-gray-700 appearance-none focus:ring-0 peer"
                                                                autocomplete="on" value="<?= $keynotfound; ?>" required>
                                                        <label for="keynotfound"
                                                                class="absolute text-sm text-gray-400 duration-300 transform -translate-y-4 scale-75 top-2 origin-[0] bg-[#0f0f17] px-2 peer-focus:px-2 peer-focus:text-blue-600 peer-placeholder-shown:scale-100 peer-placeholder-shown:-translate-y-1/2 peer-placeholder-shown:top-1/2 peer-focus:top-2 peer-focus:scale-75 peer-focus:-translate-y-4 left-1">Invalid
                                                                license key
                                                        </label>
                                                </div>
                                                <div class="relative">
                                                        <input type="text" maxlength="100" name="keyused" id="keyused"
                                                                class="block px-2.5 pb-2.5 pt-4 w-full text-sm text-white bg-transparent rounded-lg border-1 border-gray-700 appearance-none focus:ring-0 peer"
                                                                autocomplete="on" value="<?= $keyused; ?>" required>
                                                        <label for="keyused"
                                                                class="absolute text-sm text-gray-400 duration-300 transform -translate-y-4 scale-75 top-2 origin-[0] bg-[#0f0f17] px-2 peer-focus:px-2 peer-focus:text-blue-600 peer-placeholder-shown:scale-100 peer-placeholder-shown:-translate-y-1/2 peer-placeholder-shown:top-1/2 peer-focus:top-2 peer-focus:scale-75 peer-focus:-translate-y-4 left-1">License
                                                                key has already been used
                                                        </label>
                                                </div>
                                                <div class="relative">
                                                        <input type="text" maxlength="100" name="keybanned"
                                                                id="keybanned"
                                                                class="block px-2.5 pb-2.5 pt-4 w-full text-sm text-white bg-transparent rounded-lg border-1 border-gray-700 appearance-none focus:ring-0 peer"
                                                                autocomplete="on" value="<?= $keybanned; ?>" required>
                                                        <label for="keybanned"
                                                                class="absolute text-sm text-gray-400 duration-300 transform -translate-y-4 scale-75 top-2 origin-[0] bg-[#0f0f17] px-2 peer-focus:px-2 peer-focus:text-blue-600 peer-placeholder-shown:scale-100 peer-placeholder-shown:-translate-y-1/2 peer-placeholder-shown:top-1/2 peer-focus:top-2 peer-focus:scale-75 peer-focus:-translate-y-4 left-1">Your
                                                                license is banned
                                                        </label>
                                                </div>
                                                <div class="relative">
                                                        <input type="text" maxlength="100" name="nosublevel"
                                                                id="nosublevel"
                                                                class="block px-2.5 pb-2.5 pt-4 w-full text-sm text-white bg-transparent rounded-lg border-1 border-gray-700 appearance-none focus:ring-0 peer"
                                                                autocomplete="on" value="<?= $nosublevel; ?>" required>
                                                        <label for="nosublevel"
                                                                class="absolute text-sm text-gray-400 duration-300 transform -translate-y-4 scale-75 top-2 origin-[0] bg-[#0f0f17] px-2 peer-focus:px-2 peer-focus:text-blue-600 peer-placeholder-shown:scale-100 peer-placeholder-shown:-translate-y-1/2 peer-placeholder-shown:top-1/2 peer-focus:top-2 peer-focus:scale-75 peer-focus:-translate-y-4 left-1">There
                                                                is no subscription created for your key level. Contact
                                                                application developer.
                                                        </label>
                                                </div>
                                                <div class="relative">
                                                        <input type="text" maxlength="100" name="userbanned"
                                                                id="userbanned"
                                                                class="block px-2.5 pb-2.5 pt-4 w-full text-sm text-white bg-transparent rounded-lg border-1 border-gray-700 appearance-none focus:ring-0 peer"
                                                                autocomplete="on" value="<?= $userbanned; ?>" required>
                                                        <label for="userbanned"
                                                                class="absolute text-sm text-gray-400 duration-300 transform -translate-y-4 scale-75 top-2 origin-[0] bg-[#0f0f17] px-2 peer-focus:px-2 peer-focus:text-blue-600 peer-placeholder-shown:scale-100 peer-placeholder-shown:-translate-y-1/2 peer-placeholder-shown:top-1/2 peer-focus:top-2 peer-focus:scale-75 peer-focus:-translate-y-4 left-1">The
                                                                user is banned
                                                        </label>
                                                </div>
                                                <div class="relative">
                                                        <input type="text" maxlength="100" name="usernamenotfound"
                                                                id="usernamenotfound"
                                                                class="block px-2.5 pb-2.5 pt-4 w-full text-sm text-white bg-transparent rounded-lg border-1 border-gray-700 appearance-none focus:ring-0 peer"
                                                                autocomplete="on" value="<?= $usernamenotfound; ?>"
                                                                required>
                                                        <label for="usernamenotfound"
                                                                class="absolute text-sm text-gray-400 duration-300 transform -translate-y-4 scale-75 top-2 origin-[0] bg-[#0f0f17] px-2 peer-focus:px-2 peer-focus:text-blue-600 peer-placeholder-shown:scale-100 peer-placeholder-shown:-translate-y-1/2 peer-placeholder-shown:top-1/2 peer-focus:top-2 peer-focus:scale-75 peer-focus:-translate-y-4 left-1">Username
                                                                doesn't exist
                                                        </label>
                                                </div>
                                                <div class="relative">
                                                        <input type="text" maxlength="100" name="passmismatch"
                                                                id="passmismatch"
                                                                class="block px-2.5 pb-2.5 pt-4 w-full text-sm text-white bg-transparent rounded-lg border-1 border-gray-700 appearance-none focus:ring-0 peer"
                                                                autocomplete="on" value="<?= $passmismatch; ?>"
                                                                required>
                                                        <label for="passmismatch"
                                                                class="absolute text-sm text-gray-400 duration-300 transform -translate-y-4 scale-75 top-2 origin-[0] bg-[#0f0f17] px-2 peer-focus:px-2 peer-focus:text-blue-600 peer-placeholder-shown:scale-100 peer-placeholder-shown:-translate-y-1/2 peer-placeholder-shown:top-1/2 peer-focus:top-2 peer-focus:scale-75 peer-focus:-translate-y-4 left-1">Password
                                                                does not match.
                                                        </label>
                                                </div>
                                                <div class="relative">
                                                        <input type="text" maxlength="100" name="hwidmismatch"
                                                                id="hwidmismatch"
                                                                class="block px-2.5 pb-2.5 pt-4 w-full text-sm text-white bg-transparent rounded-lg border-1 border-gray-700 appearance-none focus:ring-0 peer"
                                                                autocomplete="on" value="<?= $hwidmismatch; ?>"
                                                                required>
                                                        <label for="hwidmismatch"
                                                                class="absolute text-sm text-gray-400 duration-300 transform -translate-y-4 scale-75 top-2 origin-[0] bg-[#0f0f17] px-2 peer-focus:px-2 peer-focus:text-blue-600 peer-placeholder-shown:scale-100 peer-placeholder-shown:-translate-y-1/2 peer-placeholder-shown:top-1/2 peer-focus:top-2 peer-focus:scale-75 peer-focus:-translate-y-4 left-1">HWID
                                                                doesn't match. Ask for a HWID reset
                                                        </label>
                                                </div>
                                                <div class="relative">
                                                        <input type="text" maxlength="100" name="noactivesubs"
                                                                id="noactivesubs"
                                                                class="block px-2.5 pb-2.5 pt-4 w-full text-sm text-white bg-transparent rounded-lg border-1 border-gray-700 appearance-none focus:ring-0 peer"
                                                                autocomplete="on" value="<?= $noactivesubs; ?>"
                                                                required>
                                                        <label for="noactivesubs"
                                                                class="absolute text-sm text-gray-400 duration-300 transform -translate-y-4 scale-75 top-2 origin-[0] bg-[#0f0f17] px-2 peer-focus:px-2 peer-focus:text-blue-600 peer-placeholder-shown:scale-100 peer-placeholder-shown:-translate-y-1/2 peer-placeholder-shown:top-1/2 peer-focus:top-2 peer-focus:scale-75 peer-focus:-translate-y-4 left-1">No
                                                                active subscription(s) found
                                                        </label>
                                                </div>
                                                <div class="relative">
                                                        <input type="text" maxlength="100" name="hwidblacked"
                                                                id="hwidblacked"
                                                                class="block px-2.5 pb-2.5 pt-4 w-full text-sm text-white bg-transparent rounded-lg border-1 border-gray-700 appearance-none focus:ring-0 peer"
                                                                autocomplete="on" value="<?= $hwidblacked; ?>" required>
                                                        <label for="hwidblacked"
                                                                class="absolute text-sm text-gray-400 duration-300 transform -translate-y-4 scale-75 top-2 origin-[0] bg-[#0f0f17] px-2 peer-focus:px-2 peer-focus:text-blue-600 peer-placeholder-shown:scale-100 peer-placeholder-shown:-translate-y-1/2 peer-placeholder-shown:top-1/2 peer-focus:top-2 peer-focus:scale-75 peer-focus:-translate-y-4 left-1">You've
                                                                been blacklisted from our application
                                                        </label>
                                                </div>
                                                <div class="relative">
                                                        <input type="text" maxlength="100" name="pausedsub"
                                                                id="pausedsub"
                                                                class="block px-2.5 pb-2.5 pt-4 w-full text-sm text-white bg-transparent rounded-lg border-1 border-gray-700 appearance-none focus:ring-0 peer"
                                                                autocomplete="on" value="<?= $pausedsub; ?>" required>
                                                        <label for="pausedsub"
                                                                class="absolute text-sm text-gray-400 duration-300 transform -translate-y-4 scale-75 top-2 origin-[0] bg-[#0f0f17] px-2 peer-focus:px-2 peer-focus:text-blue-600 peer-placeholder-shown:scale-100 peer-placeholder-shown:-translate-y-1/2 peer-placeholder-shown:top-1/2 peer-focus:top-2 peer-focus:scale-75 peer-focus:-translate-y-4 left-1">Your
                                                                subscription is paused and can't be used right now
                                                        </label>
                                                </div>
                                                <div class="relative">
                                                        <input type="text" maxlength="100" name="sessionunauthed"
                                                                id="sessionunauthed"
                                                                class="block px-2.5 pb-2.5 pt-4 w-full text-sm text-white bg-transparent rounded-lg border-1 border-gray-700 appearance-none focus:ring-0 peer"
                                                                autocomplete="on" value="<?= $sessionunauthed; ?>"
                                                                required>
                                                        <label for="sessionunauthed"
                                                                class="absolute text-sm text-gray-400 duration-300 transform -translate-y-4 scale-75 top-2 origin-[0] bg-[#0f0f17] px-2 peer-focus:px-2 peer-focus:text-blue-600 peer-placeholder-shown:scale-100 peer-placeholder-shown:-translate-y-1/2 peer-placeholder-shown:top-1/2 peer-focus:top-2 peer-focus:scale-75 peer-focus:-translate-y-4 left-1">Session
                                                                is not validated
                                                        </label>
                                                </div>
                                                <div class="relative">
                                                        <input type="text" maxlength="100" name="loggedInMsg"
                                                                id="loggedInMsg"
                                                                class="block px-2.5 pb-2.5 pt-4 w-full text-sm text-white bg-transparent rounded-lg border-1 border-gray-700 appearance-none focus:ring-0 peer"
                                                                autocomplete="on" value="<?= $loggedInMsg; ?>" required>
                                                        <label for="loggedInMsg"
                                                                class="absolute text-sm text-gray-400 duration-300 transform -translate-y-4 scale-75 top-2 origin-[0] bg-[#0f0f17] px-2 peer-focus:px-2 peer-focus:text-blue-600 peer-placeholder-shown:scale-100 peer-placeholder-shown:-translate-y-1/2 peer-placeholder-shown:top-1/2 peer-focus:top-2 peer-focus:scale-75 peer-focus:-translate-y-4 left-1">Logged
                                                                in!
                                                        </label>
                                                </div>
                                                <div class="relative">
                                                        <input type="text" maxlength="100" name="pausedApp"
                                                                id="pausedApp"
                                                                class="block px-2.5 pb-2.5 pt-4 w-full text-sm text-white bg-transparent rounded-lg border-1 border-gray-700 appearance-none focus:ring-0 peer"
                                                                autocomplete="on" value="<?= $pausedApp; ?>" required>
                                                        <label for="pausedApp"
                                                                class="absolute text-sm text-gray-400 duration-300 transform -translate-y-4 scale-75 top-2 origin-[0] bg-[#0f0f17] px-2 peer-focus:px-2 peer-focus:text-blue-600 peer-placeholder-shown:scale-100 peer-placeholder-shown:-translate-y-1/2 peer-placeholder-shown:top-1/2 peer-focus:top-2 peer-focus:scale-75 peer-focus:-translate-y-4 left-1">Application
                                                                is currently paused, please wait for the developer to
                                                                say otherwise.
                                                        </label>
                                                </div>
                                                <div class="relative">
                                                        <input type="text" maxlength="100" name="unTooShort"
                                                                id="unTooShort"
                                                                class="block px-2.5 pb-2.5 pt-4 w-full text-sm text-white bg-transparent rounded-lg border-1 border-gray-700 appearance-none focus:ring-0 peer"
                                                                autocomplete="on" value="<?= $unTooShort; ?>" required>
                                                        <label for="unTooShort"
                                                                class="absolute text-sm text-gray-400 duration-300 transform -translate-y-4 scale-75 top-2 origin-[0] bg-[#0f0f17] px-2 peer-focus:px-2 peer-focus:text-blue-600 peer-placeholder-shown:scale-100 peer-placeholder-shown:-translate-y-1/2 peer-placeholder-shown:top-1/2 peer-focus:top-2 peer-focus:scale-75 peer-focus:-translate-y-4 left-1">Username
                                                                too short, try longer one.
                                                        </label>
                                                </div>
                                                <div class="relative">
                                                        <input type="text" maxlength="100" name="pwLeaked" id="pwLeaked"
                                                                class="block px-2.5 pb-2.5 pt-4 w-full text-sm text-white bg-transparent rounded-lg border-1 border-gray-700 appearance-none focus:ring-0 peer"
                                                                autocomplete="on" value="<?= $pwLeaked; ?>" required>
                                                        <label for="pwLeaked"
                                                                class="absolute text-sm text-gray-400 duration-300 transform -translate-y-4 scale-75 top-2 origin-[0] bg-[#0f0f17] px-2 peer-focus:px-2 peer-focus:text-blue-600 peer-placeholder-shown:scale-100 peer-placeholder-shown:-translate-y-1/2 peer-placeholder-shown:top-1/2 peer-focus:top-2 peer-focus:scale-75 peer-focus:-translate-y-4 left-1">This
                                                                password has been leaked in a data breach (not from us),
                                                                please use a different one.
                                                        </label>
                                                </div>
                                                <div class="relative ">
                                                        <input type="text" maxlength="100" name="chatHitDelay"
                                                                id="chatHitDelay"
                                                                class="block px-2.5 pb-2.5 pt-4 w-full text-sm text-white bg-transparent rounded-lg border-1 border-gray-700 appearance-none focus:ring-0 peer"
                                                                autocomplete="on" value="<?= $chatHitDelay; ?>"
                                                                required>
                                                        <label for="chatHitDelay"
                                                                class="absolute text-sm text-gray-400 duration-300 transform -translate-y-4 scale-75 top-2 origin-[0] bg-[#0f0f17] px-2 peer-focus:px-2 peer-focus:text-blue-600 peer-placeholder-shown:scale-100 peer-placeholder-shown:-translate-y-1/2 peer-placeholder-shown:top-1/2 peer-focus:top-2 peer-focus:scale-75 peer-focus:-translate-y-4 left-1">Chat
                                                                slower, you've hit the delay limit
                                                        </label>
                                                </div>
                                        </div>
                                        <div class="hidden p-4 rounded-lg  grid gap-4 -mt-8" id="storesettings"
                                                role="tabpanel" aria-labelledby="storesettings-tab">
                                                <a
                                                    class="inline-flex text-white bg-green-700 hover:opacity-60 focus:ring-0 font-medium rounded-lg text-sm px-5 py-2.5 transition duration-200"
                                                    href="https://dash.sellsn.io/link/keyauth"
                                                    target="_blank">
                                                        <i class="lni lni-link mr-2 mt-1"></i>
                                                    Connect SellSN
                                                </a>
                                                <!-- Alert Box -->
                                                <div id="alert-4"
                                                        class="flex items-center p-4 mb-4 text-blue-600 rounded-lg bg-[#09090d]"
                                                        role="role">
                                                        <svg class="flex-shrink-0 w-4 h-4" aria-hidden="true"
                                                                xmlns="http://www.w3.org/2000/svg" fill="currentColor"
                                                                viewBox="0 0 20 20">
                                                                <path
                                                                        d="M10 .5a9.5 9.5 0 1 0 9.5 9.5A9.51 9.51 0 0 0 10 .5ZM9.5 4a1.5 1.5 0 1 1 0 3 1.5 1.5 0 0 1 0-3ZM12 15H8a1 1 0 0 1 0-2h1v-3H8a1 1 0 0 1 0-2h2a1 1 0 0 1 1 1v4h1a1 1 0 0 1 0 2Z" />
                                                        </svg>
                                                        <div class="ml-3 text-sm font-medium">
                                                                View our <b><u><a href="https://www.youtube.com/watch?v=wAtTwBxPdmU"
                                                                                        target="_blank">YouTube video
                                                                                        here</a></u></b> to use reseller
                                                                system.
                                                        </div>
                                                </div>
                                                <!-- End Alert Box -->
                                                <div class="relative">
                                                        <input type="text" id="resellerstore" name="resellerstore"
                                                                class="transition duration-500 block px-2.5 pb-2.5 pt-4 w-full text-sm text-white-900 bg-transparent rounded-lg border-1 border-gray-700 appearance-none focus:outline-none focus:ring-0 focus:border-blue-600 peer"
                                                                value="<?= $rs; ?>" placeholder=" "
                                                                data-popover-target="resellerstore-popover">
                                                        <label for="resellerstore"
                                                                class="absolute text-sm text-gray-500 duration-300 transform -translate-y-4 scale-75 top-2 z-10 origin-[0] bg-[#0f0f17] px-2 peer-focus:px-2 peer-focus:text-blue-600 peer-focus:text-blue-500 peer-placeholder-shown:scale-100 peer-placeholder-shown:-translate-y-1/2 peer-placeholder-shown:top-1/2 peer-focus:top-2 peer-focus:scale-75 peer-focus:-translate-y-4 left-1">Reseller
                                                                Store Link</label>

                                                        <div data-popover id="resellerstore-popover" role="tooltip"
                                                                class="absolute z-10 invisible inline-block w-64 text-sm text-gray-500 transition-opacity duration-300 bg-[#09090d] rounded-lg shadow-sm opacity-0">
                                                                <div class="px-3 py-2 bg-[#09090d]/70 rounded-t-lg">
                                                                        <h3 class="font-semibold text-white">Reseller
                                                                                Store Link</h3>
                                                                </div>
                                                                <div class="px-3 py-2">
                                                                        <p>If you're not using built-in reseller system,
                                                                                set a link will show to your resellers
                                                                                for them to buy keys.</p>
                                                                </div>
                                                                <div data-popper-arrow></div>
                                                        </div>
                                                </div>
                                                <div class="relative">
                                                        <input type="url" id="resellerstoreWebhookLink"
                                                                name="resellerstoreWebhookLink"
                                                                class="transition duration-500 block px-2.5 pb-2.5 pt-4 w-full text-sm text-white-900 bg-transparent rounded-lg border-1 border-gray-700 appearance-none    focus:outline-none focus:ring-0 focus:border-blue-600 peer blur-sm hover:blur-none"
                                                                value="<?= 'https://' . (($_SERVER['HTTP_HOST'] == 'keyauth.cc') ? 'keyauth.win' : $_SERVER['HTTP_HOST']) . '/api/reseller/?app=' . $_SESSION['app']; ?>"
                                                                placeholder=" " /
                                                                data-popover-target="resellerstoreWebhookLink-popover"
                                                                disabled>
                                                        <label for="resellerstoreWebhookLink"
                                                                class="absolute text-sm text-gray-500 duration-300 transform -translate-y-4 scale-75 top-2 z-10 origin-[0] bg-[#0f0f17] px-2 peer-focus:px-2 peer-focus:text-blue-600 peer-focus:text-blue-500 peer-placeholder-shown:scale-100 peer-placeholder-shown:-translate-y-1/2 peer-placeholder-shown:top-1/2 peer-focus:top-2 peer-focus:scale-75 peer-focus:-translate-y-4 left-1">Reseller
                                                                Webhook Link</label>

                                                        <div data-popover id="resellerstoreWebhookLink-popover"
                                                                role="tooltip"
                                                                class="absolute z-10 invisible inline-block w-64 text-sm text-gray-500 transition-opacity duration-300 bg-[#09090d] rounded-lg shadow-sm opacity-0">
                                                                <div class="px-3 py-2 bg-[#09090d]/70 rounded-t-lg">
                                                                        <h3 class="font-semibold text-white">Reseller
                                                                                Webhook Link</h3>
                                                                </div>
                                                                <div class="px-3 py-2">
                                                                        <p>This is the same if you're using Sellix or
                                                                                Shoppy, create webhook with this link
                                                                                for the event order:paid</p>
                                                                </div>
                                                                <div data-popper-arrow></div>
                                                        </div>
                                                </div>
                                                <h1 class="text-xl font-semibold text-white-900 sm:text-2xl">Sellix</h1>
                                                <div class="relative">
                                                        <input type="text" id="sellixwebhooksecret"
                                                                name="sellixwebhooksecret"
                                                                class="transition duration-500 block px-2.5 pb-2.5 pt-4 w-full text-sm text-white-900 bg-transparent rounded-lg border-1 border-gray-700 appearance-none    focus:outline-none focus:ring-0 focus:border-blue-600 peer blur-sm hover:blur-none"
                                                                value="<?= $sellixwhsecret; ?>" maxlength="32"
                                                                placeholder=" "
                                                                data-popover-target="sellixwebhooksecret-popover">
                                                        <label for="sellixwebhooksecret"
                                                                class="absolute text-sm text-gray-500 duration-300 transform -translate-y-4 scale-75 top-2 z-10 origin-[0] bg-[#0f0f17] px-2 peer-focus:px-2 peer-focus:text-blue-600 peer-focus:text-blue-500 peer-placeholder-shown:scale-100 peer-placeholder-shown:-translate-y-1/2 peer-placeholder-shown:top-1/2 peer-focus:top-2 peer-focus:scale-75 peer-focus:-translate-y-4 left-1">Sellix
                                                                Webhook Secret</label>

                                                        <div data-popover id="sellixwebhooksecret-popover"
                                                                role="tooltip"
                                                                class="absolute z-10 invisible inline-block w-64 text-sm text-gray-500 transition-opacity duration-300 bg-[#09090d] rounded-lg shadow-sm opacity-0">
                                                                <div class="px-3 py-2 bg-[#09090d]/70 rounded-t-lg">
                                                                        <h3 class="font-semibold text-white">Sellix
                                                                                Webhook Secret</h3>
                                                                </div>
                                                                <div class="px-3 py-2">
                                                                        <p>Sellix webhook secret for reseller system.
                                                                        </p>
                                                                </div>
                                                                <div data-popper-arrow></div>
                                                        </div>
                                                </div>
                                                <div class="relative">
                                                        <input type="text" id="sellixdayproduct" name="sellixdayproduct"
                                                                class="block px-2.5 pb-2.5 pt-4 w-full text-sm text-white-900 bg-transparent rounded-lg border-1 border-gray-700 appearance-none    focus:outline-none focus:ring-0 focus:border-blue-600 peer"
                                                                value="<?= $sellixdayproduct; ?>" maxlength="13"
                                                                placeholder=" " />
                                                        <label for="sellixdayproduct"
                                                                class="absolute text-sm text-gray-500 duration-300 transform -translate-y-4 scale-75 top-2 z-10 origin-[0] bg-[#0f0f17] px-2 peer-focus:px-2 peer-focus:text-blue-600 peer-focus:text-blue-500 peer-placeholder-shown:scale-100 peer-placeholder-shown:-translate-y-1/2 peer-placeholder-shown:top-1/2 peer-focus:top-2 peer-focus:scale-75 peer-focus:-translate-y-4 left-1">Sellix
                                                                Day Product ID</label>
                                                </div>
                                                <div class="relative">
                                                        <input type="text" id="sellixweekproduct"
                                                                name="sellixweekproduct"
                                                                class="block px-2.5 pb-2.5 pt-4 w-full text-sm text-white-900 bg-transparent rounded-lg border-1 border-gray-700 appearance-none    focus:outline-none focus:ring-0 focus:border-blue-600 peer"
                                                                value="<?= $sellixweekproduct; ?>" maxlength="13"
                                                                placeholder=" " />
                                                        <label for="sellixweekproduct"
                                                                class="absolute text-sm text-gray-500 duration-300 transform -translate-y-4 scale-75 top-2 z-10 origin-[0] bg-[#0f0f17] px-2 peer-focus:px-2 peer-focus:text-blue-600 peer-focus:text-blue-500 peer-placeholder-shown:scale-100 peer-placeholder-shown:-translate-y-1/2 peer-placeholder-shown:top-1/2 peer-focus:top-2 peer-focus:scale-75 peer-focus:-translate-y-4 left-1">Sellix
                                                                Week Product ID</label>
                                                </div>
                                                <div class="relative">
                                                        <input type="text" id="sellixmonthproduct"
                                                                name="sellixmonthproduct"
                                                                class="block px-2.5 pb-2.5 pt-4 w-full text-sm text-white-900 bg-transparent rounded-lg border-1 border-gray-700 appearance-none    focus:outline-none focus:ring-0 focus:border-blue-600 peer"
                                                                value="<?= $sellixmonthproduct; ?>" maxlength="13"
                                                                placeholder=" " />
                                                        <label for="sellixmonthproduct"
                                                                class="absolute text-sm text-gray-500 duration-300 transform -translate-y-4 scale-75 top-2 z-10 origin-[0] bg-[#0f0f17] px-2 peer-focus:px-2 peer-focus:text-blue-600 peer-focus:text-blue-500 peer-placeholder-shown:scale-100 peer-placeholder-shown:-translate-y-1/2 peer-placeholder-shown:top-1/2 peer-focus:top-2 peer-focus:scale-75 peer-focus:-translate-y-4 left-1">Sellix
                                                                Month Product ID</label>
                                                </div>
                                                <div class="relative">
                                                        <input type="text" id="sellixlifetimeproduct"
                                                                name="sellixlifetimeproduct"
                                                                class="block px-2.5 pb-2.5 pt-4 w-full text-sm text-white-900 bg-transparent rounded-lg border-1 border-gray-700 appearance-none    focus:outline-none focus:ring-0 focus:border-blue-600 peer"
                                                                value="<?= $sellixlifetimeproduct; ?>" maxlength="13"
                                                                placeholder=" " />
                                                        <label for="sellixlifetimeproduct"
                                                                class="absolute text-sm text-gray-500 duration-300 transform -translate-y-4 scale-75 top-2 z-10 origin-[0] bg-[#0f0f17] px-2 peer-focus:px-2 peer-focus:text-blue-600 peer-focus:text-blue-500 peer-placeholder-shown:scale-100 peer-placeholder-shown:-translate-y-1/2 peer-placeholder-shown:top-1/2 peer-focus:top-2 peer-focus:scale-75 peer-focus:-translate-y-4 left-1">Sellix
                                                                Lifetime Product ID</label>
                                                </div>
                                                <h1 class="text-xl font-semibold text-white-900 sm:text-2xl">Sellapp
                                                </h1>
                                                <div class="relative">
                                                        <input type="text" id="sellappwebhooksecret"
                                                                name="sellappwebhooksecret"
                                                                class="transition duration-500 block px-2.5 pb-2.5 pt-4 w-full text-sm text-white-900 bg-transparent rounded-lg border-1 border-gray-700 appearance-none    focus:outline-none focus:ring-0 focus:border-blue-600 peer blur-sm hover:blur-none"
                                                                value="<?= $sellappwhsecret; ?>" maxlength="64"
                                                                placeholder=" "
                                                                data-popover-target="sellappwebhooksecret-popover">
                                                        <label for="sellappwebhooksecret"
                                                                class="absolute text-sm text-gray-500 duration-300 transform -translate-y-4 scale-75 top-2 z-10 origin-[0] bg-[#0f0f17] px-2 peer-focus:px-2 peer-focus:text-blue-600 peer-focus:text-blue-500 peer-placeholder-shown:scale-100 peer-placeholder-shown:-translate-y-1/2 peer-placeholder-shown:top-1/2 peer-focus:top-2 peer-focus:scale-75 peer-focus:-translate-y-4 left-1">Sellapp
                                                                Webhook Secret</label>

                                                        <div data-popover id="sellappwebhooksecret-popover"
                                                                role="tooltip"
                                                                class="absolute z-10 invisible inline-block w-64 text-sm text-gray-500 transition-opacity duration-300 bg-[#09090d] rounded-lg shadow-sm opacity-0">
                                                                <div class="px-3 py-2 bg-[#09090d]/70 rounded-t-lg">
                                                                        <h3 class="font-semibold text-white">Sell App
                                                                                Webhook Secret</h3>
                                                                </div>
                                                                <div class="px-3 py-2">
                                                                        <p>SellApp webhook secret for reseller system.
                                                                        </p>
                                                                </div>
                                                                <div data-popper-arrow></div>
                                                        </div>
                                                </div>
                                                <div class="relative">
                                                        <input type="text" id="sellappdayproduct"
                                                                name="sellappdayproduct"
                                                                class="block px-2.5 pb-2.5 pt-4 w-full text-sm text-white-900 bg-transparent rounded-lg border-1 border-gray-700 appearance-none    focus:outline-none focus:ring-0 focus:border-blue-600 peer"
                                                                value="<?= $sellappdayproduct; ?>" placeholder=" " />
                                                        <label for="sellappdayproduct"
                                                                class="absolute text-sm text-gray-500 duration-300 transform -translate-y-4 scale-75 top-2 z-10 origin-[0] bg-[#0f0f17] px-2 peer-focus:px-2 peer-focus:text-blue-600 peer-focus:text-blue-500 peer-placeholder-shown:scale-100 peer-placeholder-shown:-translate-y-1/2 peer-placeholder-shown:top-1/2 peer-focus:top-2 peer-focus:scale-75 peer-focus:-translate-y-4 left-1">Sellapp
                                                                Day Product ID</label>
                                                </div>
                                                <div class="relative">
                                                        <input type="text" id="sellappweekproduct"
                                                                name="sellappweekproduct"
                                                                class="block px-2.5 pb-2.5 pt-4 w-full text-sm text-white-900 bg-transparent rounded-lg border-1 border-gray-700 appearance-none    focus:outline-none focus:ring-0 focus:border-blue-600 peer"
                                                                value="<?= $sellappweekproduct; ?>" placeholder=" " />
                                                        <label for="sellappweekproduct"
                                                                class="absolute text-sm text-gray-500 duration-300 transform -translate-y-4 scale-75 top-2 z-10 origin-[0] bg-[#0f0f17] px-2 peer-focus:px-2 peer-focus:text-blue-600 peer-focus:text-blue-500 peer-placeholder-shown:scale-100 peer-placeholder-shown:-translate-y-1/2 peer-placeholder-shown:top-1/2 peer-focus:top-2 peer-focus:scale-75 peer-focus:-translate-y-4 left-1">Sellapp
                                                                Week Product ID</label>
                                                </div>
                                                <div class="relative">
                                                        <input type="text" id="sellappmonthproduct"
                                                                name="sellappmonthproduct"
                                                                class="block px-2.5 pb-2.5 pt-4 w-full text-sm text-white-900 bg-transparent rounded-lg border-1 border-gray-700 appearance-none    focus:outline-none focus:ring-0 focus:border-blue-600 peer"
                                                                value="<?= $sellappmonthproduct; ?>" placeholder=" " />
                                                        <label for="sellappmonthproduct"
                                                                class="absolute text-sm text-gray-500 duration-300 transform -translate-y-4 scale-75 top-2 z-10 origin-[0] bg-[#0f0f17] px-2 peer-focus:px-2 peer-focus:text-blue-600 peer-focus:text-blue-500 peer-placeholder-shown:scale-100 peer-placeholder-shown:-translate-y-1/2 peer-placeholder-shown:top-1/2 peer-focus:top-2 peer-focus:scale-75 peer-focus:-translate-y-4 left-1">Sellapp
                                                                Month Product ID</label>
                                                </div>
                                                <div class="relative">
                                                        <input type="text" id="sellapplifetimeproduct"
                                                                name="sellapplifetimeproduct"
                                                                class="block px-2.5 pb-2.5 pt-4 w-full text-sm text-white-900 bg-transparent rounded-lg border-1 border-gray-700 appearance-none    focus:outline-none focus:ring-0 focus:border-blue-600 peer"
                                                                value="<?= $sellapplifetimeproduct; ?>"
                                                                placeholder=" " />
                                                        <label for="sellapplifetimeproduct"
                                                                class="absolute text-sm text-gray-500 duration-300 transform -translate-y-4 scale-75 top-2 z-10 origin-[0] bg-[#0f0f17] px-2 peer-focus:px-2 peer-focus:text-blue-600 peer-focus:text-blue-500 peer-placeholder-shown:scale-100 peer-placeholder-shown:-translate-y-1/2 peer-placeholder-shown:top-1/2 peer-focus:top-2 peer-focus:scale-75 peer-focus:-translate-y-4 left-1">Sellapp
                                                                Lifetime Product ID</label>
                                                </div>
                                        </div>
                                        <div class="hidden p-4 rounded-lg grid gap-7" id="hashsettings" role="tabpanel"
                                                aria-labelledby="hashsettings-tab">

                                                <div class="-mt-12">
                                                        <!-- App Settings Functions -->
                                                        <button type="button"
                                                                class="inline-flex text-white bg-blue-700 hover:opacity-60 focus:ring-0 font-medium rounded-lg text-sm px-5 py-2.5 mb-2 transition duration-200"
                                                                data-modal-toggle="add-new-hash-modal"
                                                                data-modal-target="add-new-hash-modal">
                                                                <i class="lni lni-circle-plus mr-2 mt-1"></i>Add
                                                                Additional Hash
                                                        </button>
                                                        <!-- End App Settings Functions -->

                                                        <br>

                                                        <!-- Reset App Settings Functions -->
                                                        <button type="button"
                                                                class="inline-flex text-white bg-red-700 hover:opacity-60 focus:ring-0 font-medium rounded-lg text-sm px-5 py-2.5 mb-2 transition duration-200 mb-5"
                                                                data-modal-toggle="reset-program-hash-modal"
                                                                data-modal-target="reset-program-hash-modal">
                                                                <i class="lni lni-reload mr-2 mt-1"></i>Reset All Hashes
                                                        </button>
                                                        <!-- End Reset App Settings Functions -->


                                                        <?php
                            $query = misc\mysql\query("SELECT `hash` FROM `apps` WHERE `secret` = ?", [$_SESSION['app']]);
                                if ($query->num_rows > 0){
                                    while ($row = mysqli_fetch_array($query->result)) {
                                        $hash = $row['hash'];
                                        $length = strlen($hash);
                                        $buttons = ceil($length / 32);
                                        
                                        for ($i = 0; $i < $buttons; $i++) {
                                            $txt = substr($hash, $i * 32, 32);
                                            ?>
                                                        <button type="submit" name="deleteHash" value="<?= $txt; ?>"
                                                                class="w-full text-white bg-red-700 hover:bg-red-700 focus:ring-4 focus:outline-none focus:ring-red-700 font-medium rounded-lg text-sm px-5 py-2.5 text-center mb-5">Delete
                                                                Hash: <?= $txt; ?></button>
                                                        <?php
                                        }
                                    }
                                }
                                ?>
                                                </div>
                                        </div>
                                        <div class="hidden p-4 rounded-lg  grid gap-4 -mt-8" id="customerPanelSettings"
                                                role="tabpanel" aria-labelledby="customerPanelSettings-tab">

                                                <div id="lol" class="grid grid-cols-1 lg:grid-cols-4 2xl:grid-cols-8">
                                                        <div class="relative mb-4 " style="margin-right: 10px;">
                                                                <select id="panelstatus" name="panelstatus"
                                                                        class="bg-[#0f0f17] border border-gray-700 text-white-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5"
                                                                        data-popover-target="customerPanel-popover">
                                                                        <option value="0"
                                                                                <?= $panelstatus == 0 ? ' selected="selected"' : ''; ?>>
                                                                                Disabled
                                                                        </option>
                                                                        <option value="1"
                                                                                <?= $panelstatus == 1 ? ' selected="selected"' : ''; ?>>
                                                                                Enabled
                                                                        </option>
                                                                </select>
                                                                <label for="panelstatus"
                                                                        class="absolute text-sm text-gray-400 duration-300 transform -translate-y-4 scale-75 top-2 z-10 origin-[0] bg-[#0f0f17] px-2 peer-focus:px-2 peer-focus:text-blue-600 peer-placeholder-shown:scale-100 peer-placeholder-shown:-translate-y-1/2 peer-placeholder-shown:top-1/2 peer-focus:top-2 peer-focus:scale-75 peer-focus:-translate-y-4 left-1">Customer
                                                                        Panel</label>
                                                                <?php dashboard\primary\popover("customerPanel-popover", "Customer Panel", "Allows your users to access a dedicated page just for them to manage
                                    their account and download updates/webloader."); ?>
                                                        </div>
                                                </div>
                                                <div class="relative">
                                                        <input type="text" id="customerPanelLink"
                                                                class="block px-2.5 pb-2.5 pt-4 w-full text-sm text-white-900 bg-transparent rounded-lg border-1 border-gray-700 appearance-none    focus:outline-none focus:ring-0 focus:border-blue-600 peer"
                                                                placeholder=" "
                                                                value="<?= 'https://' . ($_SERVER['HTTP_HOST'] ?? $_SERVER['SERVER_NAME']) . '/panel/' . urlencode($owner) . '/' . urlencode($_SESSION['name']); ?>"
                                                                readonly
                                                                data-popover-target="customerPanelLink-popover">
                                                        <label for="customerPanelLink"
                                                                class="absolute text-sm text-gray-500 duration-300 transform -translate-y-4 scale-75 top-2 z-10 origin-[0] bg-[#0f0f17] px-2 peer-focus:px-2 peer-focus:text-blue-600 peer-focus:text-blue-500 peer-placeholder-shown:scale-100 peer-placeholder-shown:-translate-y-1/2 peer-placeholder-shown:top-1/2 peer-focus:top-2 peer-focus:scale-75 peer-focus:-translate-y-4 left-1">Customer
                                                                Panel Link</label>
                                                        <?php dashboard\primary\popover("customerPanelLink-popover", "Customer Panel Link", "This is the link you will provide to your users if you would like them to have
                                the ability to HWID, and alter their accounts."); ?>
                                                </div>
                                                <div class="relative">
                                                        <input type="text" id="customDomain" name="customDomain"
                                                                class="block px-2.5 pb-2.5 pt-4 w-full text-sm text-white-900 bg-transparent rounded-lg border-1 border-gray-700 appearance-none focus:outline-none focus:ring-0 focus:border-blue-600 peer"
                                                                value="<?= $customDomain; ?>" placeholder=" "
                                                                data-popover-target="customDomain-popover">
                                                        <label for="customDomain"
                                                                class="absolute text-sm text-gray-500 duration-300 transform -translate-y-4 scale-75 top-2 z-10 origin-[0] bg-[#0f0f17] px-2 peer-focus:px-2 peer-focus:text-blue-600 peer-focus:text-blue-500 peer-placeholder-shown:scale-100 peer-placeholder-shown:-translate-y-1/2 peer-placeholder-shown:top-1/2 peer-focus:top-2 peer-focus:scale-75 peer-focus:-translate-y-4 left-1">Customer
                                                                Panel Link Custom Domain</label>
                                                        <?php dashboard\primary\popover("customDomain-popover", "Customer Panel Custom Domain", "Custom domain for the customer panel. Please search on YouTube for the guide."); ?>
                                                </div>
                                                <div class="relative">
                                                        <input type="url" id="customerPanelIcon"
                                                                name="customerPanelIcon"
                                                                class="block px-2.5 pb-2.5 pt-4 w-full text-sm text-white-900 bg-transparent rounded-lg border-1 border-gray-700 appearance-none focus:outline-none focus:ring-0 focus:border-blue-600 peer"
                                                                value="<?= $customerPanelIcon; ?>" placeholder=" "
                                                                data-popover-target="customerPanelIcon-popover">
                                                        <label for="customerPanelIcon"
                                                                class="absolute text-sm text-gray-500 duration-300 transform -translate-y-4 scale-75 top-2 z-10 origin-[0] bg-[#0f0f17] px-2 peer-focus:px-2 peer-focus:text-blue-600 peer-focus:text-blue-500 peer-placeholder-shown:scale-100 peer-placeholder-shown:-translate-y-1/2 peer-placeholder-shown:top-1/2 peer-focus:top-2 peer-focus:scale-75 peer-focus:-translate-y-4 left-1">Customer
                                                                Panel Icon</label>
                                                        <?php dashboard\primary\popover("customerPanelIcon-popover", "Customer Panel Icon", "Image shown on SEO and next to the title in the browser tab."); ?>
                                                </div>
                                        </div>
                                        <div class="hidden p-4 rounded-lg -mt-8" id="appFunctionToggle" role="tabpanel"
                                                aria-labelledby="appFunctionToggle-tab">
                                                <div class="mb-4">
                                                        <div class="flex items-center">
                                                                <input id="allToggle" name="allToggle" type="checkbox"
                                                                        class="w-4 h-4 text-blue-600 bg-[#0f0f17] border-gray-300 rounded focus:ring-blue-500 focus:ring-2">
                                                                <label for="allToggle"
                                                                        class="ml-2 text-sm font-medium text-white-900">Toggle
                                                                        All Functions</label>
                                                        </div>
                                                </div>

                                                <div class="grid grid-cols-4 gap-2 mt-20">
                                                        <!-- Login/Register Stuff -->
                                                        <div>
                                                                <span
                                                                        class="bg-blue-600 text-white text-sm font-medium px-2.5 py-0.5 rounded">Login/Register
                                                                        Functions</span>
                                                                <div class="flex items-center mb-4 mt-5">
                                                                        <input id="loginToggle" name="loginToggle"
                                                                                type="checkbox"
                                                                                <?php if ($enabledFunctions & 1) { ?>
                                                                                checked <?php } ?>
                                                                                class="w-4 h-4 text-blue-600 bg-[#0f0f17] border-gray-300 rounded focus:ring-blue-500 focus:ring-2">
                                                                        <label for="loginToggle"
                                                                                class="ml-2 text-sm font-medium text-white-900">Login</label>
                                                                </div>

                                                                <div class="flex items-center mb-4">
                                                                        <input id="registerToggle" name="registerToggle"
                                                                                type="checkbox"
                                                                                <?php if ($enabledFunctions & 2) { ?>
                                                                                checked <?php } ?>
                                                                                class="w-4 h-4 text-blue-600 bg-[#0f0f17] border-gray-300 rounded focus:ring-blue-500 focus:ring-2">
                                                                        <label for="registerToggle"
                                                                                class="ml-2 text-sm font-medium text-white-900">Register</label>
                                                                </div>

                                                                <div class="flex items-center mb-4">
                                                                        <input id="licenseToggle" name="licenseToggle"
                                                                                type="checkbox"
                                                                                <?php if ($enabledFunctions & 4) { ?>
                                                                                checked <?php } ?>
                                                                                class="w-4 h-4 text-blue-600 bg-[#0f0f17] border-gray-300 rounded focus:ring-blue-500 focus:ring-2">
                                                                        <label for="licenseToggle"
                                                                                class="ml-2 text-sm font-medium text-white-900">License</label>
                                                                </div>

                                                                <div class="flex items-center mb-4">
                                                                        <input id="upgradeToggle" name="upgradeToggle"
                                                                                type="checkbox"
                                                                                <?php if ($enabledFunctions & 262144) { ?>
                                                                                checked <?php } ?>
                                                                                class="w-4 h-4 text-blue-600 bg-[#0f0f17] border-gray-300 rounded focus:ring-blue-500 focus:ring-2">
                                                                        <label for="upgradeToggle"
                                                                                class="ml-2 text-sm font-medium text-white-900">Upgrade</label>
                                                                </div>
                                                        </div>
                                                        <!-- End Login/Register Stuff -->

                                                        <!-- Chatroom Stuff -->
                                                        <div>
                                                                <span
                                                                        class="bg-blue-600 text-white text-sm font-medium px-2.5 py-0.5 rounded">Chatroom
                                                                        Functions</span>
                                                                <div class="flex items-center mb-4 mt-5">
                                                                        <input id="chatSendToggle" name="chatSendToggle"
                                                                                type="checkbox"
                                                                                <?php if ($enabledFunctions & 16) { ?>
                                                                                checked <?php } ?>
                                                                                class="w-4 h-4 text-blue-600 bg-[#0f0f17] border-gray-300 rounded focus:ring-blue-500 focus:ring-2">
                                                                        <label for="chatSendToggle"
                                                                                class="ml-2 text-sm font-medium text-white-900">Send
                                                                                Messages</label>
                                                                </div>

                                                                <div class="flex items-center mb-4">
                                                                        <input id="chatGetToggle" name="chatGetToggle"
                                                                                type="checkbox"
                                                                                <?php if ($enabledFunctions & 8) { ?>
                                                                                checked <?php } ?>
                                                                                class="w-4 h-4 text-blue-600 bg-[#0f0f17] border-gray-300 rounded focus:ring-blue-500 focus:ring-2">
                                                                        <label for="chatGetToggle"
                                                                                class="ml-2 text-sm font-medium text-white-900">Retrieve
                                                                                Messages</label>
                                                                </div>
                                                        </div>
                                                        <!-- End Chatroom Stuff -->

                                                        <!-- User Variable Stuff -->
                                                        <div>
                                                                <span
                                                                        class="bg-blue-600 text-white text-sm font-medium px-2.5 py-0.5 rounded">Variable
                                                                        Functions</span>
                                                                <div class="flex items-center mb-4 mt-5">
                                                                        <input id="getVarToggle" name="getVarToggle"
                                                                                type="checkbox"
                                                                                <?php if ($enabledFunctions & 32) { ?>
                                                                                checked <?php } ?>
                                                                                class="w-4 h-4 text-blue-600 bg-[#0f0f17] border-gray-300 rounded focus:ring-blue-500 focus:ring-2">
                                                                        <label for="getVarToggle"
                                                                                class="ml-2 text-sm font-medium text-white-900">Retrieve
                                                                                User Variable</label>
                                                                </div>

                                                                <div class="flex items-center mb-4">
                                                                        <input id="setVarToggle" name="setVarToggle"
                                                                                type="checkbox"
                                                                                <?php if ($enabledFunctions & 64) { ?>
                                                                                checked <?php } ?>
                                                                                class="w-4 h-4 text-blue-600 bg-[#0f0f17] border-gray-300 rounded focus:ring-blue-500 focus:ring-2">
                                                                        <label for="setVarToggle"
                                                                                class="ml-2 text-sm font-medium text-white-900">Set
                                                                                User Variable</label>
                                                                </div>

                                                                <div class="flex items-center mb-4">
                                                                        <input id="varToggle" name="varToggle"
                                                                                type="checkbox"
                                                                                <?php if ($enabledFunctions & 131072) { ?>
                                                                                checked <?php } ?>
                                                                                class="w-4 h-4 text-blue-600 bg-[#0f0f17] border-gray-300 rounded focus:ring-blue-500 focus:ring-2">
                                                                        <label for="varToggle"
                                                                                class="ml-2 text-sm font-medium text-white-900">Retrieve
                                                                                Global Variable</label>
                                                                </div>
                                                        </div>
                                                        <!-- End User Variable Stuff -->

                                                        <!-- Misc Stuff -->
                                                        <div>
                                                                <span
                                                                        class="bg-blue-600 text-white text-sm font-medium px-2.5 py-0.5 rounded">Misc
                                                                        Functions</span>
                                                                <div class="flex items-center mb-4 mt-5">
                                                                        <input id="banToggle" name="banToggle"
                                                                                type="checkbox"
                                                                                <?php if ($enabledFunctions & 128) { ?>
                                                                                checked <?php } ?>
                                                                                class="w-4 h-4 text-blue-600 bg-[#0f0f17] border-gray-300 rounded focus:ring-blue-500 focus:ring-2">
                                                                        <label for="banToggle"
                                                                                class="ml-2 text-sm font-medium text-white-900">Ban</label>
                                                                </div>

                                                                <div class="flex items-center mb-4">
                                                                        <input id="checkBlackToggle"
                                                                                name="checkBlackToggle" type="checkbox"
                                                                                <?php if ($enabledFunctions & 256) { ?>
                                                                                checked <?php } ?>
                                                                                class="w-4 h-4 text-blue-600 bg-[#0f0f17] border-gray-300 rounded focus:ring-blue-500 focus:ring-2">
                                                                        <label for="checkBlackToggle"
                                                                                class="ml-2 text-sm font-medium text-white-900">Check
                                                                                Blackist</label>
                                                                </div>

                                                                <div class="flex items-center mb-4">
                                                                        <input id="sessionToggle" name="sessionToggle"
                                                                                type="checkbox"
                                                                                <?php if ($enabledFunctions & 512) { ?>
                                                                                checked <?php } ?>
                                                                                class="w-4 h-4 text-blue-600 bg-[#0f0f17] border-gray-300 rounded focus:ring-blue-500 focus:ring-2">
                                                                        <label for="sessionToggle"
                                                                                class="ml-2 text-sm font-medium text-white-900">Session
                                                                                Check</label>
                                                                </div>

                                                                <div class="flex items-center mb-4">
                                                                        <input id="changeUsernameToggle"
                                                                                name="changeUsernameToggle"
                                                                                type="checkbox"
                                                                                <?php if ($enabledFunctions & 1024) { ?>
                                                                                checked <?php } ?>
                                                                                class="w-4 h-4 text-blue-600 bg-[#0f0f17] border-gray-300 rounded focus:ring-blue-500 focus:ring-2">
                                                                        <label for="changeUsernameToggle"
                                                                                class="ml-2 text-sm font-medium text-white-900">Change
                                                                                Username</label>
                                                                </div>

                                                                <div class="flex items-center mb-4">
                                                                        <input id="fileToggle" name="fileToggle"
                                                                                type="checkbox"
                                                                                <?php if ($enabledFunctions & 2048) { ?>
                                                                                checked <?php } ?>
                                                                                class="w-4 h-4 text-blue-600 bg-[#0f0f17] border-gray-300 rounded focus:ring-blue-500 focus:ring-2">
                                                                        <label for="fileToggle"
                                                                                class="ml-2 text-sm font-medium text-white-900">File
                                                                                (Download)</label>
                                                                </div>

                                                                <div class="flex items-center mb-4">
                                                                        <input id="fetchOnlineToggle"
                                                                                name="fetchOnlineToggle" type="checkbox"
                                                                                <?php if ($enabledFunctions & 4096) { ?>
                                                                                checked <?php } ?>
                                                                                class="w-4 h-4 text-blue-600 bg-[#0f0f17] border-gray-300 rounded focus:ring-blue-500 focus:ring-2">
                                                                        <label for="fetchOnlineToggle"
                                                                                class="ml-2 text-sm font-medium text-white-900">Fetch
                                                                                Online</label>
                                                                </div>

                                                                <div class="flex items-center mb-4">
                                                                        <input id="forgotPasswordToggle"
                                                                                name="forgotPasswordToggle"
                                                                                type="checkbox"
                                                                                <?php if ($enabledFunctions & 8192) { ?>
                                                                                checked <?php } ?>
                                                                                class="w-4 h-4 text-blue-600 bg-[#0f0f17] border-gray-300 rounded focus:ring-blue-500 focus:ring-2">
                                                                        <label for="forgotPasswordToggle"
                                                                                class="ml-2 text-sm font-medium text-white-900">Forgot
                                                                                Password</label>
                                                                </div>

                                                                <div class="flex items-center mb-4">
                                                                        <input id="fetchStatsToggle"
                                                                                name="fetchStatsToggle" type="checkbox"
                                                                                <?php if ($enabledFunctions & 16384) { ?>
                                                                                checked <?php } ?>
                                                                                class="w-4 h-4 text-blue-600 bg-[#0f0f17] border-gray-300 rounded focus:ring-blue-500 focus:ring-2">
                                                                        <label for="fetchStatsToggle"
                                                                                class="ml-2 text-sm font-medium text-white-900">Fetch
                                                                                Stats</label>
                                                                </div>

                                                                <div class="flex items-center mb-4">
                                                                        <input id="logToggle" name="logToggle"
                                                                                type="checkbox"
                                                                                <?php if ($enabledFunctions & 32768) { ?>
                                                                                checked <?php } ?>
                                                                                class="w-4 h-4 text-blue-600 bg-[#0f0f17] border-gray-300 rounded focus:ring-blue-500 focus:ring-2">
                                                                        <label for="logToggle"
                                                                                class="ml-2 text-sm font-medium text-white-900">Log</label>
                                                                </div>

                                                                <div class="flex items-center mb-4">
                                                                        <input id="webhookToggle" name="webhookToggle"
                                                                                type="checkbox"
                                                                                <?php if ($enabledFunctions & 65536) { ?>
                                                                                checked <?php } ?>
                                                                                class="w-4 h-4 text-blue-600 bg-[#0f0f17] border-gray-300 rounded focus:ring-blue-500 focus:ring-2">
                                                                        <label for="webhookToggle"
                                                                                class="ml-2 text-sm font-medium text-white-900">Webhook</label>
                                                                </div>
                                                        </div>

                                                        <!-- End Misc Stuff -->
                                                        <span name="permissionsLabel" id="permissionsLabel"
                                                                class="bg-orange-600 text-white text-sm font-medium px-2.5 py-0.5 rounded">Permission
                                                                Code For Seller API: <?= $enabledFunctions; ?></span>
                                                </div>
                                        </div>

                                </div>
                                <div class="flex justify-end">
                                        <input id="functionValue" name="functionValue" class="hidden" type="text"
                                                value="">
                                        <button class="inline-flex text-white bg-blue-700 hover:opacity-60 focus:ring-0 font-medium rounded-lg text-sm px-5 py-2.5 mb-2 transition duration-200"
                                                name="updatesettings" id="updatesettings">
                                                <i class="lni lni-save mr-2 mt-1"></i>Update App Settings
                                        </button>
                                </div>
                        </form>

                        <?php
                        if($apiCustomDomainId) {
                                $url = "https://api.cloudflare.com/client/v4/zones/{$cfZoneIdCache}/custom_hostnames/{$apiCustomDomainId}";

                                $curl = curl_init($url);
                                curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

                                $headers = array(
                                "Authorization: Bearer {$cfApiKeyCache}",
                                "Content-Type: application/json",
                                );
                                curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
                        
                                $resp = curl_exec($curl);
                                $json = json_decode($resp);

                                $customDomainName = $json->result->hostname;
                        }
                        ?>
                        <!-- Add New Custom domain -->
                        <div id="add-new-custom-domain" tabindex="-1" aria-hidden="true"
                                class="fixed top-0 left-0 right-0 z-50 hidden w-full p-4 overflow-x-hidden overflow-y-auto md:inset-0 h-[calc(100%-1rem)] max-h-full">
                                <div class="relative w-full max-w-md max-h-full">
                                        <!-- Modal content -->
                                        <div class="relative bg-[#0f0f17] rounded-lg border border-blue-700 shadow">
                                                <div class="px-6 py-6 lg:px-8">
                                                        <h3 class="mb-4 text-xl font-medium text-white-900">Add API
                                                                custom domain</h3>
                                                        <hr class="h-px mb-4 mt-4 bg-gray-700 border-0">
                                                        <form class="space-y-6" method="POST">
                                                                <div>
                                                                        <div class="relative mb-4">
                                                                                <input type="text" id="domain"
                                                                                        name="domain"
                                                                                        class="block px-2.5 pb-2.5 pt-4 w-full text-sm text-white bg-transparent rounded-lg border-1 border-gray-700 appearance-none focus:ring-0  peer "
                                                                                        placeholder="api.example.com"
                                                                                        value="<?= $customDomainName ?>"
                                                                                        autocomplete="on"
                                                                                        required>
                                                                                <label for="domain"
                                                                                        class="absolute text-sm text-gray-400 duration-300 transform -translate-y-4 scale-75 top-2 z-10 origin-[0] bg-[#0f0f17] px-2 peer-focus:px-2 peer-focus:text-blue-600 peer-placeholder-shown:scale-100 peer-placeholder-shown:-translate-y-1/2 peer-placeholder-shown:top-1/2 peer-focus:top-2 peer-focus:scale-75 peer-focus:-translate-y-4 left-1">
                                                                                        Your custom domain</label>
                                                                        </div>
                                                                </div>
                                                                <button type="submit" name="addDomainAPI"
                                                                        class="w-full text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center">Add
                                                                        Domain</button>
                                                        </form>
                                                </div>
                                        </div>
                                </div>
                        </div>
                        <!-- End Add New Custom domain -->
                        <?php
                        if (isset($_POST['addDomainAPI'])) {
                        if ($_SESSION['role'] != "seller" || $_SESSION['username'] == "demoseller") {
                        dashboard\primary\error("You must upgrade to seller plan to use custom domain!");
                        echo "
                        <meta http-equiv='Refresh' Content='2;'>";
                        return;
                        }

                        $domainInput = $_POST['domain'];

                        $url = "https://api.cloudflare.com/client/v4/zones/{$cfZoneIdCache}/custom_hostnames";

                        $curl = curl_init($url);
                        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
                        curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode(array("hostname" => $domainInput)));

                        $headers = array(
                        "Authorization: Bearer {$cfApiKeyCache}",
                        "Content-Type: application/json",
                        );
                        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);

                        $resp = curl_exec($curl);
                        $statusCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);

                        $json = json_decode($resp);

                        if($statusCode > 399) {
                            dashboard\primary\error($json->errors[0]->message);
                            echo "
                            <meta http-equiv='Refresh' Content='2;'>";
                            return;
                        }

                        $ownership_verification = $json->result->ownership_verification;
                        $custom_domain_id = $json->result->id;
                        // must keep the $ownership_verification and $custom_domain_id variables
                        // ABOVE the dns.google request, to obtain the correct values

                        $url = "https://dns.google/resolve?type=txt&name=_domainconnect.$domainInput";

                        $curl = curl_init($url);
                        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

                        $resp = curl_exec($curl);

                        $json = json_decode($resp);

                        if($json->Answer && $json->Answer[0]->data == "api.cloudflare.com/client/v4/dns/domainconnect") {
                                $domainConnect = true;
                        }
                        ?>
                        <!-- Custom domain API Modal -->
                        <div id="validate-custom-domain-api-modal" tabindex="-1"
                        class="fixed grid place-items-center h-screen bg-black bg-opacity-60 z-50 p-4 overflow-x-hidden overflow-y-auto md:inset-0 h-[calc(100%-1rem)] max-h-full">
                                <div class="relative w-full max-w-md max-h-full">
                                        <!-- Modal content -->
                                        <div class="relative bg-[#0f0f17] rounded-lg border-[#1d4ed8] shadow">
                                                <div class="px-6 py-6 lg:px-8">
                                                        <h3 class="mb-4 text-xl font-medium text-white-900">Custom
                                                                domain</h3>
                                                        <form class="space-y-6" method="POST">
                                                                <?php
                                                                if($domainConnect) {

                                                                $rootDomain = implode('.', array_slice(explode('.', $domainInput), -2));
                                                                
                                                                // Extract the private key from the certificate
                                                                $privateKey = openssl_pkey_get_private($domainConnectKey);

                                                                $verifyTxt = $ownership_verification->value;

                                                                $data = "domain=$rootDomain&cnamehost=$domainInput&verifytxt=$verifyTxt";

                                                                // Sign the data using the private key and SHA-256 algorithm
                                                                $signature = '';
                                                                openssl_sign($data, $signature, $privateKey, OPENSSL_ALGO_SHA256);

                                                                $signature = urlencode(base64_encode($signature));
                                                                openssl_free_key($privateKey);
                                                                ?>
                                                                <div>
                                                                        <a type="button"
                                                                            class="inline-flex text-white bg-blue-700 hover:opacity-60 focus:ring-0 font-medium rounded-lg text-sm px-3 py-2.5 mb-2 transition duration-200 cursor-pointer"
                                                                            target="popup" rel="noreferrer"
                                                                            onclick="window.open('https://dash.cloudflare.com/domainconnect/v2/domainTemplates/providers/keyauth.cc/services/api-custom-domains/apply?domain=<?= $rootDomain; ?>&cnamehost=<?= $domainInput; ?>&verifytxt=<?= $ownership_verification->value; ?>&key=_dc&sig=<?= $signature ?>','popup','width=600,height=875'); return false;">
                                                                            <i class="lni lni-cogs mr-2 mt-1"></i> Setup Automatically
                                                                        </a>
                                                                </div>
                                                                <?php
                                                                }
                                                                else {
                                                                ?>
                                                                <div>
                                                                        <label class="mb-5">Please add a TXT DNS record, <a class="text-blue-600   hover:underline" href="https://developers.cloudflare.com/dns/manage-dns-records/how-to/create-dns-records/#create-dns-records" target="_blank">read tutorial here</a></label>
                                                                        <br>
                                                                        <br>
                                                                        <p class="text-xs text-white-500"><b>DNS Type:</b></p>
                                                                        <code class="text-blue-700">TXT</code>
                                                                        <br>
                                                                        <p class="text-xs text-white-500"><b>DNS Name:</b></p>
                                                                        <code class="text-blue-700"><?= $ownership_verification->name ?></code>
                                                                        <br>
                                                                        <p class="text-xs text-white-500"><b>DNS Content:</b></p>
                                                                        <code class="text-blue-700"><?= $ownership_verification->value ?></code>
                                                                        <br>
                                                                        <br>
                                                                        <label class="mb-5">Also add a CNAME record, <a class="text-blue-600   hover:underline" href="https://developers.cloudflare.com/dns/manage-dns-records/how-to/create-dns-records/#create-dns-records" target="_blank">read tutorial here</a></label>
                                                                        <br>
                                                                        <br>
                                                                        <p class="text-xs text-white-500"><b>DNS Type:</b></p>
                                                                        <code class="text-blue-700">CNAME</code>
                                                                        <br>
                                                                        <p class="text-xs text-white-500"><b>DNS Name:</b></p>
                                                                        <code class="text-blue-700"><?= $domainInput ?></code>
                                                                        <br>
                                                                        <p class="text-xs text-white-500"><b>DNS Target:</b></p>
                                                                        <code class="text-blue-700">api-worker.keyauth.win</code>
                                                                </div>
                                                                <?php
                                                                }
                                                                ?>
                                                                <button name="validate_custom_domain_api"
                                                                        value="<?= $custom_domain_id ?>"
                                                                        id="validate_custom_domain_api"
                                                                        class="w-full text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center">Check
                                                                        Domain</button>
                                                        </form>
                                                </div>
                                        </div>
                                </div>
                        </div>
                        <?php
                        } ?>

                        <!-- Add New Hash Modal -->
                        <div id="add-new-hash-modal" tabindex="-1" aria-hidden="true"
                                class="fixed top-0 left-0 right-0 z-50 hidden w-full p-4 overflow-x-hidden overflow-y-auto md:inset-0 h-[calc(100%-1rem)] max-h-full">
                                <div class="relative w-full max-w-md max-h-full">
                                        <!-- Modal content -->
                                        <div class="relative bg-[#0f0f17] rounded-lg border border-blue-700 shadow">
                                                <div class="px-6 py-6 lg:px-8">
                                                        <h3 class="mb-4 text-xl font-medium text-white-900">Add
                                                                Application Hash</h3>
                                                        <hr class="h-px mb-4 mt-4 bg-gray-700 border-0">
                                                        <form class="space-y-6" method="POST">
                                                                <div>
                                                                        <div class="relative mb-4">
                                                                                <input type="text" id="hash" name="hash"
                                                                                        class="block px-2.5 pb-2.5 pt-4 w-full text-sm text-white bg-transparent rounded-lg border-1 border-gray-700 appearance-none focus:ring-0  peer "
                                                                                        placeholder=" "
                                                                                        autocomplete="on" minlength="32"
                                                                                        maxlength="32" required>
                                                                                <label for="hash"
                                                                                        class="absolute text-sm text-gray-400 duration-300 transform -translate-y-4 scale-75 top-2 z-10 origin-[0] bg-[#0f0f17] px-2 peer-focus:px-2 peer-focus:text-blue-600 peer-placeholder-shown:scale-100 peer-placeholder-shown:-translate-y-1/2 peer-placeholder-shown:top-1/2 peer-focus:top-2 peer-focus:scale-75 peer-focus:-translate-y-4 left-1">MD5
                                                                                        Program Hash To Add</label>
                                                                        </div>
                                                                </div>
                                                                <button type="submit" name="addhash"
                                                                        class="w-full text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center">Add
                                                                        Hash</button>
                                                        </form>
                                                </div>
                                        </div>
                                </div>
                        </div>
                        <!-- End Add New Hash Modal -->

                        <!-- Reset Program Hash Modal -->
                        <div id="reset-program-hash-modal" tabindex="-1"
                                class="fixed top-0 left-0 right-0 z-50 hidden p-4 overflow-x-hidden overflow-y-auto md:inset-0 h-[calc(100%-1rem)] max-h-full">
                                <div class="relative w-full max-w-md max-h-full">
                                        <div class="relative bg-[#0f0f17] border border-red-700 rounded-lg shadow">
                                                <div class="p-6 text-center">
                                                        <div class="flex items-center p-4 mb-4 text-sm text-white border border-yellow-500 rounded-lg bg-[#0f0f17]"
                                                                role="alert">
                                                                <span class="sr-only">Info</span>
                                                                <div>
                                                                        <span class="font-medium">Notice!</span> You're
                                                                        about to reset your programs hash.
                                                                        This should only be done if you
                                                                        plan on releasing a new version/update.
                                                                        <b>This can
                                                                                NOT be undone.</b>
                                                                </div>
                                                        </div>
                                                        <h3 class="mb-5 text-lg font-normal text-gray-200">Are you sure
                                                                you want
                                                                to
                                                                reset your programs hash?</h3>
                                                        <form method="POST">
                                                                <button data-modal-hide="reset-program-hash-modal"
                                                                        name="resethash"
                                                                        class="text-white bg-red-600 hover:bg-red-800 focus:ring-4 focus:outline-none focus:ring-red-300 font-medium rounded-lg text-sm inline-flex items-center px-5 py-2.5 text-center mr-2">
                                                                        Yes, I'm sure
                                                                </button>
                                                                <button data-modal-hide="reset-program-hash-modal"
                                                                        type="button"
                                                                        class="inline-flex text-white bg-gray-700 hover:opacity-60 focus:ring-0 font-medium rounded-lg text-sm px-5 py-2.5 mb-2 transition duration-200">No,
                                                                        cancel</button>
                                                        </form>
                                                </div>
                                        </div>
                                </div>
                        </div>
                        <!-- End Reset Program HashModal -->

                        <script>
                        $(document).keydown(function(event) {
                                if (event.ctrlKey && event.key === 's') {
                                        $("#updatesettings").click();

                                        event.preventDefault();
                                }
                        });

                        $("#updatesettings").on("click", function() {

                        });


                        var perms = {
                                loginToggle: 1,
                                registerToggle: 2,
                                licenseToggle: 4,
                                chatGetToggle: 8,
                                chatSendToggle: 16,
                                getVarToggle: 32,
                                setVarToggle: 64,
                                banToggle: 128,
                                checkBlackToggle: 256,
                                sessionToggle: 512,
                                changeUsernameToggle: 1024,
                                fileToggle: 2048,
                                fetchOnlineToggle: 4096,
                                forgotPasswordToggle: 8192,
                                fetchStatsToggle: 16384,
                                logToggle: 32768,
                                webhookToggle: 65536,
                                varToggle: 131072,
                                upgradeToggle: 262144
                        }

                        document.addEventListener('DOMContentLoaded', function() {

                                function recalculate() {
                                        var totalPermissions = 0;

                                        for (var s in perms) {
                                                var checkbox = document.getElementById(s);
                                                if (checkbox && checkbox.checked) {
                                                        totalPermissions += perms[s];
                                                }
                                                document.getElementById("functionValue").value =
                                                        totalPermissions;
                                        }

                                        var permissionsLabel = document.getElementById(
                                                'permissionsLabel');
                                        if (permissionsLabel) {
                                                permissionsLabel.textContent =
                                                        "Permissions Code For Seller API: " +
                                                        totalPermissions;
                                        }
                                        document.getElementById("permissions").value = totalPermissions;
                                        console.log(totalPermissions);
                                }

                                var allToggle = document.getElementById('allToggle');
                                var checkboxes = document.querySelectorAll(
                                        'input[type=checkbox]:not(#allToggle)');

                                allToggle.addEventListener('change', function() {
                                        checkboxes.forEach(function(checkbox) {
                                                checkbox.checked =
                                                        allToggle
                                                        .checked;
                                        });
                                        recalculate();
                                });

                                checkboxes.forEach(function(checkbox) {
                                        checkbox.addEventListener('change', function() {
                                                if (!this.checked) {
                                                        allToggle
                                                                .checked =
                                                                false;
                                                } else {
                                                        if (Array.from(
                                                                        checkboxes)
                                                                .every(cb =>
                                                                        cb
                                                                        .checked
                                                                        )
                                                                ) {
                                                                allToggle
                                                                        .checked =
                                                                        true;
                                                        }
                                                }
                                                recalculate();
                                        });
                                });
                        });
                        </script>