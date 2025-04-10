<?php

include '../includes/misc/autoload.phtml';

session_start();

if ($_SESSION['role'] == "Reseller") {
    die("Resellers can't access this.");
}

if (!isset($_SESSION['app'])) {
    dashboard\primary\error("Application not selected");
    die("Application not selected.");
}

switch ($_POST['type'] ?? $_GET['type']) {
    case 'account':
        $jsonarray = json_encode(
            array(
                "files" => array(),
                "keys" => array(),
                "subscriptions" => array(),
                "tokens" => array(),
                "users" => array(),
                "uservars" => array(),
                "vars" => array(),
                "webhooks" => array()
            )
        );

        $jsondata = json_decode($jsonarray);

        $filesquery = misc\mysql\query("SELECT * FROM `files` WHERE `app` = ?", [$_SESSION['app']]);
        while ($row = mysqli_fetch_array($filesquery->result)){
            $filesjson = array(
                "name" => $row["name"],
                "id" => $row["id"],
                "url" => $row["url"]
            );
            array_push($jsondata->files, $filesjson);
        }

        $keyquery = misc\mysql\query("SELECT * FROM `keys` WHERE `app` = ?", [$_SESSION['app']]);
        while ($row = mysqli_fetch_array($keyquery->result)){
            $keyjson = array(
                "key" => $row["key"],
                "note" => $row["note"],
                "level" => $row["level"],
                "genby" => $row["genby"],
                "usedby" => $row["usedby"]
            );
            array_push($jsondata->keys, $keyjson);
        }

        $subscriptionsquery = misc\mysql\query("SELECT * FROM `subscriptions` WHERE `app` = ?", [$_SESSION['app']]);
        while ($row = mysqli_fetch_array($subscriptionsquery->result)){
            $subscriptionjson = array(
                "name" => $row["name"],
                "level" => $row["level"]
            );
            array_push($jsondata->subscriptions, $subscriptionjson);
        }

        $tokensquery = misc\mysql\query("SELECT * FROM `tokens` WHERE `app` = ?", [$_SESSION['app']]);
        while ($row = mysqli_fetch_array($tokensquery->result)){
            $tokenjson = array(
                "token" => $row["token"],
                "assigned" => $row["assigned"],
                "hash" => $row["hash"],
                "type" => $row["type"],
                "status" => $row["status"]
            );
            array_push($jsondata->tokens, $tokenjson);
        }

        $usersquery = misc\mysql\query("SELECT * FROM `users` WHERE `app` = ?", [$_SESSION['app']]);
        while ($row = mysqli_fetch_array($usersquery->result)){
            $usersjson = array(
                "username" => $row["username"],
                "email" => $row["email"],
                "password" => $row["password"],
                "hwid" => $row["hwid"],
                "owner" => $row["owner"],
                "ip" => $row["ip"]
            );
            array_push($jsondata->users, $usersjson);
        }

        $uservarsquery = misc\mysql\query("SELECT * FROM `uservars` WHERE `app` = ?", [$_SESSION['app']]);
        while ($row = mysqli_fetch_array($uservarsquery->result)){
            $uservarsjson = array(
                "name" => $row["name"],
                "data" => $row["data"],
                "user" => $row["user"]
            );
            array_push($jsondata->uservars, $uservarsjson);
        }

        $varsquery = misc\mysql\query("SELECT * FROM `vars` WHERE `app` = ?", [$_SESSION['app']]);
        while ($row = mysqli_fetch_array($varsquery->result)){
            $varsjson = array(
                "varid" => $row["varid"],
                "msg" => $row["msg"]
            );
            array_push($jsondata->vars, $varsjson);
        }

        $webhooksquery = misc\mysql\query("SELECT * FROM `webhooks` WHERE `app` = ?", [$_SESSION['app']]);
        while ($row = mysqli_fetch_array($webhooksquery->result)){
            $webhooksjson = array(
                "webid" => $row["webid"],
                "baselink" => $row["baselink"],
                "useragent" => $row["useragent"]
            );
            array_push($jsondata->webhooks, $webhooksjson);
        }

        $newjson = json_encode($jsondata);
        
        header('Content-Description: File Transfer');
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename="KeyAuthAccExport.json"');
        header('Expires: 0');
        header('Cache-Control: must-revalidate');
        header('Pragma: public');
        header('Content-Length: ' . strlen($newjson));
        
        
        die($newjson); 
    case 'users':
        $jsonarray = json_encode(
            array(
                "users" => array(),
                "subscription" => array(),
                "tokens" => array()
            )
        );
        
        $jsondata = json_decode($jsonarray);
        
        $userquery = misc\mysql\query("SELECT * FROM `users` WHERE `app` = ?", [$_SESSION['app']]);
        
        while ($row = mysqli_fetch_array($userquery->result)) {
        
            $userjson = array(
                "username" => $row["username"],
                "email" => $row["email"],
                "password" => $row["password"],
                "hwid" => $row["hwid"],
                "owner" => $row["owner"],
                "createdate" => $row["createdate"],
                "lastlogin" => $row["lastlogin"],
                "banned" => $row["banned"],
                "ip" => $row["ip"],
                "cooldown" => $row["cooldown"]
            );
        
            array_push($jsondata->users, $userjson);
        }
        
        $subscriptionquery = misc\mysql\query("SELECT * FROM `subs` WHERE `app` = ? ", [$_SESSION['app']]);
        
        while ($row = mysqli_fetch_array($subscriptionquery->result)) {
        
            $subjson = array(
                "user" => $row["user"],
                "subscription" => $row["subscription"],
                "expiry" => $row["expiry"],
                "key" => $row["key"],
                "paused" => $row["paused"]
            );
        
            array_push($jsondata->subscription, $subjson);
        }

        $tokensquery = misc\mysql\query("SELECT * FROM `tokens` WHERE `type` = ? AND `app` = ?", ["user", $_SESSION['app']]);

        while ($row = mysqli_fetch_array($tokensquery->result)){

            $tokenjson = array(
                "token" => $row["token"],
                "assigned" => $row["assigned"],
                "banned" => $row["banned"],
                "reason" => $row["reason"],
                "hash" => $row["hash"],
                "type" => $row["type"],
                "status" => $row["status"]
            );

            array_push($jsondata->tokens, $tokenjson);
        }
        
        $newjson = json_encode($jsondata);
        
        header('Content-Description: File Transfer');
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename="KeyAuthUsers.json"');
        header('Expires: 0');
        header('Cache-Control: must-revalidate');
        header('Pragma: public');
        header('Content-Length: ' . strlen($newjson));
        
        
        die($newjson);   
    case 'licenses':
            $jsonarray = json_encode(
                array(
                    "keys" => array(),
                    "tokens" => array()
                )
            );
            
            $jsondata = json_decode($jsonarray);
            
            $query = misc\mysql\query("SELECT * FROM `keys` WHERE `app` = ?",[$_SESSION['app']]);
        
            // Create an array to hold the keys
            $keysArray = array();
            
            while ($row = mysqli_fetch_array($query->result)) {
                // Add each key to the array
                $keysArray[] = array(
                    "key" => $row['key'],
                    "note" => $row['note'],
                    "expiry" => $row['expires'], // Export keys is in SECONDS, because that works better than fractions of a day.
                    // so, tell people to convert seconds to hours or days..
                    "status" => $row['status'],
                    "level" => $row['level'],
                    "genby" => $row['genby'],
                    "gendate" => $row['gendate'],
                    "usedon"  => $row['usedon'],
                    "banned" => $row['banned']
                );
            }
        
            $tokensquery = misc\mysql\query("SELECT * FROM `tokens` WHERE `type` = ? AND `app` = ?", ["license", $_SESSION['app']]);
        
            while ($row = mysqli_fetch_array($tokensquery->result)){
        
                $tokenjson = array(
                    "token" => $row["token"],
                    "assigned" => $row["assigned"],
                    "banned" => $row["banned"],
                    "reason" => $row["reason"],
                    "hash" => $row["hash"],
                    "type" => $row["type"],
                    "status" => $row["status"]
                );
        
                array_push($jsondata->tokens, $tokenjson);
            }
            
            // Assign keys array to jsondata
            $jsondata->keys = $keysArray;
            
            // Convert the array to a JSON string
            $jsonData = json_encode($jsondata);
            
            header('Content-Description: File Transfer');
            header('Content-Type: application/json'); // Set the content type to JSON
            header('Content-Disposition: attachment; filename="KeyAuthKeys.json"');
            header('Expires: 0');
            header('Cache-Control: must-revalidate');
            header('Pragma: public');
            header('Content-Length: ' . strlen($jsonData));
            
            echo $jsonData;
            
            die($stringData);
        
    case 'logs':
        $jsonarray = json_encode(
            array(
                "logs" => array()
            )
        );
        
        $jsondata = json_decode($jsonarray);
        
        $userlogquery = misc\mysql\query("SELECT * FROM `logs` WHERE `logapp` = ?", [$_SESSION['app']]);
        
        while ($row = mysqli_fetch_array($userlogquery->result)) {
        
            $userlogjson = array(
                "logdate" => $row["logdate"],
                "logdata" => $row["logdata"],
                "credential" => $row["credential"],
                "pcuser" => $row["pcuser"]
            );
        
            array_push($jsondata->logs, $userlogjson);
        }
        
        
        $newjson = json_encode($jsondata);
        
        header('Content-Description: File Transfer');
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename="KeyAuthUserLogs.json"');
        header('Expires: 0');
        header('Cache-Control: must-revalidate');
        header('Pragma: public');
        header('Content-Length: ' . strlen($newjson));
        
        die($newjson);     
    case 'auditLog':
        $jsonarray = json_encode(
            array(
                "auditLog" => array()
            )
        );
        
        $jsondata = json_decode($jsonarray);
        
        $userquery = misc\mysql\query("SELECT * FROM `auditLog` WHERE `app` = ?", [$_SESSION['app']]);
        
        while ($row = mysqli_fetch_array($userquery->result)) {
        
            $userjson = array(
                "id" => $row["id"],
                "user" => $row["user"],
                "event" => $row["event"],
                "time" => $row["time"],
            );
        
            array_push($jsondata->auditLog, $userjson);
        }
        
        
        $newjson = json_encode($jsondata);
        
        header('Content-Description: File Transfer');
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename="KeyAuthAuditLogs.json"');
        header('Expires: 0');
        header('Cache-Control: must-revalidate');
        header('Pragma: public');
        header('Content-Length: ' . strlen($newjson));
        
        die($newjson);     
    default:
        echo 'Invalid Type or Type does not Exist';
}