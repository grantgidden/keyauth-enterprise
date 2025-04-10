<?php
if ($_SESSION['role'] == "Reseller") {
    header("location: ./?page=reseller-licenses");
    die();
}
if ($role == "Manager" && !($permissions & 2)) {
    misc\auditLog\send("Attempted (and failed) to view users.");
    dashboard\primary\error("You weren't granted permission to view this page.");
    die();
}
if (!isset($_SESSION['app'])) {
    dashboard\primary\error("Application not selected");
    die("Application not selected.");
}

if (isset($_POST['saveuser'])) {
    $un = misc\etc\sanitize(urldecode($_POST['saveuser']));
    $username = misc\etc\sanitize($_POST['username']);
    $hwid = misc\etc\sanitize($_POST['hwid']);
    $pass = misc\etc\sanitize($_POST['pass']);
    $email = misc\etc\sanitize($_POST['email']);
    //$client2fa = misc\etc\sanitize($_POST['client2fa']);
    if (isset($hwid) && trim($hwid) != '') {
        $query = misc\mysql\query("SELECT `hwid` FROM `users` WHERE `username` = ? AND `app` = ?", [$un, $_SESSION['app']]);
        $row = mysqli_fetch_array($query->result);
        $oldHwid = $row["hwid"];
        $oldHwid = $oldHwid .= $hwid;
        $query = misc\mysql\query("UPDATE `users` SET `hwid` = ? WHERE `username` = ? AND `app` = ?", [$oldHwid, $un, $_SESSION['app']]);
        if ($query->affected_rows) {
            dashboard\primary\success("Successfully updated user!");
            misc\cache\purge('KeyAuthUser:' . $_SESSION['app'] . ':' . $un);
            misc\cache\purge('KeyAuthUserData:' . $_SESSION['app'] . ':' . $un);
        } else {
            dashboard\primary\error("Failed to update user!");
        }
    }
    if (isset($username) && trim($username) != '') {
        $resp = misc\user\changeUsername($un, $username, $_SESSION['app']);
        match($resp){
            'already_used' => dashboard\primary\error("Username already used!"),
            'failure' => dashboard\primary\error("Failed to change username!"),
            'success' => dashboard\primary\success("Successfully changed username!"),
            default => dashboard\primary\error("Unhandled Error! Contact us if you need help")
        };
    }
    if (isset($pass) && trim($pass) != '') {
        $resp = misc\user\changePassword($un, $pass, $_SESSION['app']);
        match($resp){
            'failure' => dashboard\primary\error("Failed to change password!"),
            'success' => dashboard\primary\success("Successfully changed password!"),
            default => dashboard\primary\error("Unhandled Error! Contact us if you need help")
        };
    }
    if (isset($email) && trim($email) != '') {
        $resp = misc\user\changeEmail($un, $email, $_SESSION['app']);
        match($resp){
            'failure' => dashboard\primary\error("Failed to change email!"),
            'success' => dashboard\primary\success("Successfully changed email!"),
            default => dashboard\primary\error("Unhandled Error! Contact us if you need help")
        };
    }
    /*if (isset($client2fa)){
        $resp = misc\user\disable2fa($un, $_SESSION['app']);
        match($resp){
            'failure' => dashboard\primary\error("Failed to disabled 2fa"),
            'success' => dashboard\primary\success("Successfully disabled 2fa"),
            default => dashboard\primary\error("Unhandled Error! Contact us if you need help")
        };
    }*/
}

if (isset($_POST['deletevar'])) {
    $resp = misc\user\deleteVar(urldecode($_POST['deletevar']), $_POST['var']);
    match($resp){
        'failure' => dashboard\primary\error("Failed to delete variable"),
        'success' => dashboard\primary\success("Successfully deleted variable"),
        default => dashboard\primary\error("Unhandled Error! Contact us if you need help")
    };
}
if (isset($_POST['deletesub'])) {
    $sub = misc\etc\sanitize($_POST['sub']);

    $resp = misc\user\deleteSub(urldecode($_POST['deletesub']), $sub);
    match($resp){
        'failure' => dashboard\primary\error("Failed to delete subscription"),
        'success' => dashboard\primary\success("Successfully deleted subscription"),
        default => dashboard\primary\error("Unhandled Error! Contact us if you need help")
    };
}
if (isset($_POST['dlusers'])) {
    echo "<meta http-equiv='Refresh' Content='0; url=download-types.php?type=users'>";
}
if (isset($_POST['importusersFile'])) {
    if ($_FILES['file_input']['error'] === UPLOAD_ERR_OK){
        $fileContent = file_get_contents($_FILES['file_input']['tmp_name']);
        $json = json_decode($fileContent);

        if ($json === null){
            dashboard\primary\error("Invalid JSON file. Double check the format you're selecting.");
            echo "<meta http-equiv='Refresh' Content='1;'>";
            return;
        }

        if (!isset($json->users)){
            dashboard\primary\error("Missing 'users' section in import file.");
            echo "<meta http-equiv='Refresh' Content='1;'>";
            return;
        }

        if (!isset($json->subscription)){
            dashboard\primary\error("Missing 'subscription' section in import file.");
            echo "<meta http-equiv='Refresh' Content='1;'>";
            return;
        }

        if (!isset($json->tokens)){
            dashboard\primary\error("Missing 'tokens' section in import file.");
            echo "<meta http-equiv='Refresh' Content='1;'>";
            return;
        }

        if (isset($json->users) && isset($json->subscription) && isset($json->tokens)){
            foreach ($json->users as $user){
                $username = $user->username;
                $email = $user->email;
                $password = $user->password;
                $hwid = $user->hwid;
                $createdate = $user->createdate;
                $lastlogin = $user->lastlogin;
                $banned = $user->banned;
                $ip = $user->ip;
                $cooldown = $user->cooldown;
    
                $users = misc\mysql\query("SELECT * FROM `users` WHERE `app` = ?", [$_SESSION["app"]]);
    
                $userrows = mysqli_fetch_all($users->result, MYSQLI_ASSOC);
    
                foreach ($userrows as $user) {
                    if ($user["username"] === $username) {
                        dashboard\primary\error("User $username Already Exists");
                        return;
                    } 
                }
                
                misc\mysql\query("INSERT INTO `users` (`username`, `email`, `password`, `hwid`, `createdate`, `lastlogin`, `banned`, `ip`, `cooldown`, `app`) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)", [$username, $email, $password, $hwid, $createdate, $lastlogin, $banned, $ip, $cooldown, $_SESSION['app']]);
            }

            foreach ($json->subscription as $sub){
                $username = $sub->user;
                $subscription = $sub->subscription;
                $expiry = $sub->expiry;
                $paused = $sub->paused;
    
                misc\mysql\query("INSERT INTO `subs` (`user`, `subscription`, `expiry`, `paused`, `app`) VALUES (?, ?, ?, ?, ?)", [$username, $subscription, $expiry, $paused, $_SESSION['app']]);
            }

            foreach ($json->tokens as $tokenData){
                $token = $tokenData->token;
                $assigned = $tokenData->assigned;
                $banned = $tokenData->banned;
                $reason = $tokenData->reason;
                $hash = $tokenData->hash;
                $type = $tokenData->type;
                $status = $tokenData->status;

                misc\mysql\query("INSERT INTO `tokens` (`token`, `assigned`, `banned`, `reason`, `hash`, `type`, `status`, `app`) VALUES (?, ?, ?, ?, ?, ?, ?, ?)", [$token, $assigned, $banned, $reason, $hash, $type, $status, $_SESSION['app']]);
            }

            dashboard\primary\success("Successfully imported user!");
        } else {
            dashboard\primary\error("Invalid JSON file. Double check the format you're selecting.");
        }
    } else {
        dashboard\primary\error("Unhandled Error! Contact support if you need help!");
    }
}
if (isset($_POST['extenduser'])) {
    $activeOnly = ($_POST['activeOnly'] == "on") ? 1 : 0;
    $expiry = time() + $_POST['time'] * $_POST['expiry'];
    $resp = misc\user\extend(urldecode($_POST['user']), $_POST['sub'], $expiry, $activeOnly);
    match($resp){
        'missing' => dashboard\primary\error("User(s) not found!"),
        'sub_missing' => dashboard\primary\error("Subscription not found!"),
        'date_past' => dashboard\primary\error("Subscription expiry must be set in the future!"),
        'failure' => dashboard\primary\error("Failed to extend user(s)"),
        'success' => dashboard\primary\success("Successfully exteneded user(s)!"),
        default => dashboard\primary\error("Unhandled Error! Contact us if you need help")
    };
}
if (isset($_POST['subtractuser'])) {
    $expiry = $_POST['time'] * $_POST['expiry'];
    $resp = misc\user\subtract(urldecode($_POST['user']), $_POST['sub'], $expiry, $secret);
    match($resp){
        'invalid_seconds' => dashboard\primary\error("Seconds specified must be greater than zero"),
        'failure' => dashboard\primary\error("Failed to subtract time from subscription"),
        'success' => dashboard\primary\success("Successfully subtracted time from subscription"),
        default => dashboard\primary\error("Unhandled Error! Contact us if you need help")
    };
}
if (isset($_POST['adduser'])) {
    $resp = misc\user\add(urldecode($_POST['username']), $_POST['sub'], strtotime($_POST['expiry']), NULL, $_POST['password']);
    match($resp){
        'already_exist' => dashboard\primary\error("Username already exists!"),
        'sub_missing' => dashboard\primary\error("Subscription not found!"),
        'date_past' => dashboard\primary\error("Subscription expiry must be set in the future!"),
        'username_not_allowed' => dashboard\primary\error("This username is not allowed!"),
        'sub_paused' => dashboard\primary\error("Unable to create user while subscription is paused!"),
        'failure' => dashboard\primary\error("Failed to create user!"),
        'success' => dashboard\primary\success("Successfully created user!"),
        default => dashboard\primary\error("Unhandled Error! Contact us if you need help")
    };
}
if (isset($_POST['delexpusers'])) {
    $resp = misc\user\deleteExpiredUsers();
    match($resp){
        'missing' => dashboard\primary\error("You do not have users!"),
        'failure' => dashboard\primary\error("No users are expired!"),
        'success' => dashboard\primary\success("Successfully deleted expired users!"),
        default => dashboard\primary\error("Unhandled Error! Contact us if you need help")
    };
}
if (isset($_POST['delusers'])) {
    $resp = misc\user\deleteAll();
    match($resp){
        'failure' => dashboard\primary\error("Failed to delete all users!"),
        'success' => dashboard\primary\success("Successfully deleted all users!"),
        default => dashboard\primary\error("Unhandled Error! Contact us if you need help")
    };
}
if (isset($_POST['resetall'])) {
    $resp = misc\user\resetAll();
    match($resp){
        'failure' => dashboard\primary\error("Failed to reset all users!"),
        'success' => dashboard\primary\success("Successfully reset all users!"),
        default => dashboard\primary\error("Unhandled Error! Contact us if you need help")
    };
}
if (isset($_POST['unbanall'])) {
    $resp = misc\user\unbanAll();
    match($resp){
        'failure' => dashboard\primary\error("Failed to unban all users!"),
        'success' => dashboard\primary\success("Successfully unbanned all users!"),
        default => dashboard\primary\error("Unhandled Error! Contact us if you need help")
    };
}
if (isset($_POST['deleteuser'])) {
    $resp = misc\user\deleteSingular(urldecode($_POST['deleteuser']));
    match ($resp){
        'failure' => dashboard\primary\error("Failed to delete user!"),
        'success' => dashboard\primary\success("Successfully deleted user!"),
        default => dashboard\primary\error("Unhandled Error! Contact us if you need help")
    };
}
if (isset($_POST['resetuser'])) {
    $resp = misc\user\resetSingular(urldecode($_POST['resetuser']));
    match ($resp){
        'failure' => dashboard\primary\error("Failed to reset user!"),
        'success' => dashboard\primary\success("Successfully reset user!"),
        default => dashboard\primary\error("Unhandled Error! Contact us if you need help")
    };
}
if (isset($_POST['setvar'])) {
    if(strlen($_POST['data']) > 500) {
        dashboard\primary\error("Variable data must be 500 characters or less!");
    }
    else {
        $readOnly = misc\etc\sanitize($_POST['readOnly']) == NULL ? 0 : 1;
        $resp = misc\user\setVariable(urldecode($_POST['user']), $_POST['var'], $_POST['data'], null, $readOnly);
        match($resp){
            'missing' => dashboard\primary\error("No users found!"),
            'failure' => dashboard\primary\error("Failed to set variable!"),
            'success' => dashboard\primary\success("Successfully set variable!"),
            default => dashboard\primary\error("Unhandled Error! Contact us if you need help")
        };
    }
}
if (isset($_POST['banuser'])) {
    $resp = misc\user\ban(urldecode($_POST['un']), $_POST['reason']);
    match($resp){
        'missing' => dashboard\primary\error("User not found!"),
        'failure' => dashboard\primary\error("Failed to ban user!"),
        'success' => dashboard\primary\success("Successfully banned user!"),
        default => dashboard\primary\error("Unhandled Error! Contact us if you need help")
    };
}
if (isset($_POST['unbanuser'])) {
    $resp = misc\user\unban(urldecode($_POST['unbanuser']));
    match($resp){
        'missing' => dashboard\primary\error("User not found!"),
        'failure' => dashboard\primary\error("Failed to unban user!"),
        'success' => dashboard\primary\success("Successfully unbanned user!"),
        default => dashboard\primary\error("Unhandled Error! Contact us if you need help")
    };
}
if (isset($_POST['pauseuser'])) {
    $resp = misc\user\pauseUser(urldecode($_POST['pauseuser']));
    match($resp){
        'failure' => dashboard\primary\error("Failed to pause user!"),
        'success' => dashboard\primary\success("Successfully paused user!"),
        default => dashboard\primary\error("Unhandled Error! Contact support if you need help")
    };
}
if (isset($_POST['unpauseuser'])) {
    $resp = misc\user\unpauseUser(urldecode($_POST['unpauseuser']));
    match($resp){
        'failure' => dashboard\primary\error("Failed to unpause user!"),
        'success' => dashboard\primary\success("Successfully unpaused user!"),
        default => dashboard\primary\error("Unhandled Error! Contact support if you need help")
    };
}

if (isset($_POST["selected"]) && isset($_POST["action"])) {
    $selected = json_decode($_POST["selected"], true);
    if ($_POST["action"] == "delete") {
        foreach ($selected as $value) {
            $resp = misc\user\deleteSingular($value);
            match($resp){
                'failure' => dashboard\primary\error("Failed to delete user!"),
                'success' => dashboard\primary\success("Successfully deleted user!"),
                default => dashboard\primary\error("Unhandled Error! Contact us if you need help")
            };
        }
    } elseif ($_POST["action"] == "unban") {
        foreach ($selected as $value) {
            $resp = misc\user\unban($value);
            match($resp){
                'missing' => dashboard\primary\error("User not found!"),
                'failure' => dashboard\primary\error("Failed to unban user!"),
                'success' => dashboard\primary\success("Successfully unbanned user!"),
                default => dashboard\primary\error("Unhandled Error! Contact us if you need help")
            };
        }
    } elseif ($_POST["action"] == "pause") {
        foreach ($selected as $value) {
            $resp = misc\user\pauseUser($value);
            match($resp){
                'failure' => dashboard\primary\error("Failed to pause user!"),
                'success' => dashboard\primary\success("Successfully paused user!"),
                default => dashboard\primary\error("Unhandled Error! Contact us if you need help")
            };
        }
    } elseif ($_POST["action"] == "unpause") {
        foreach ($selected as $value) {
            $resp = misc\user\unpauseUser($value);
            match($resp){
                'failure' => dashboard\primary\error("Failed to unpause user!"),
                'success' => dashboard\primary\success("Successfully unpaused user!"),
                default => dashboard\primary\error("Unhandled Error! Contact us if you need help")
            };
        }
    } elseif ($_POST["action"] == "resethwid") {
        foreach ($selected as $value) {
            $resp = misc\user\resetSingular($value);
            match($resp){
                'failure' => dashboard\primary\error("Failed to reset user!"),
                'success' => dashboard\primary\success("Successfully reset user!"),
                default => dashboard\primary\error("Unhandled Error! Contact us if you need help")
            };
        }
    } else {
        dashboard\primary\error("Invalid action");
    }
}

if (isset($_POST['edituser'])) {
    $un = misc\etc\sanitize(urldecode($_POST['edituser']));
    $query = misc\mysql\query("SELECT * FROM `users` WHERE `username` = ? AND `app` = ?", [$un, $_SESSION['app']]);
    if ($query->num_rows == 0) {
        dashboard\primary\error("User not Found!");
        echo "<meta http-equiv='Refresh' Content='2'>";
        return;
    }
    $row = mysqli_fetch_array($query->result);
?>

                    <!-- Edit User Modal -->
                    <div id="edit-user-modal" tabindex="-1" aria-hidden="true"
                        class="fixed grid place-items-center h-screen bg-black bg-opacity-60 z-50 p-4 overflow-x-hidden overflow-y-auto md:inset-0 h-[calc(100%-1rem)] max-h-full">
                        <div class="relative w-full max-w-md max-h-full">
                            <!-- Modal content -->
                            <div class="relative bg-[#0f0f17] rounded-lg border border-[#1d4ed8] shadow">
                                <div class="px-6 py-6 lg:px-8">
                                    <h3 class="mb-4 text-xl font-medium text-white-900">Edit User</h3>
                                    <form class="space-y-6" method="POST">
                                        <div>

                                            <div class="relative mb-4">
                                                <input type="text" id="username" name="username"
                                                    class="block px-2.5 pb-2.5 pt-4 w-full text-sm text-white bg-transparent rounded-lg border-1 border-gray-700 appearance-none focus:ring-0  peer"
                                                    placeholder=" " autocomplete="on">
                                                <label for="username"
                                                    class="absolute text-sm text-gray-400 duration-300 transform -translate-y-4 scale-75 top-2 z-10 origin-[0] bg-[#0f0f17] px-2 peer-focus:px-2 peer-focus:text-blue-600 peer-placeholder-shown:scale-100 peer-placeholder-shown:-translate-y-1/2 peer-placeholder-shown:top-1/2 peer-focus:top-2 peer-focus:scale-75 peer-focus:-translate-y-4 left-1">Username:</label>
                                            </div>

                                            <div class="relative mb-4">
                                                <input type="password" id="pass" name="pass"
                                                    class="block px-2.5 pb-2.5 pt-4 w-full text-sm text-white bg-transparent rounded-lg border-1 border-gray-700 appearance-none focus:ring-0  peer"
                                                    placeholder=" " autocomplete="on">
                                                <label for="pass"
                                                    class="absolute text-sm text-gray-400 duration-300 transform -translate-y-4 scale-75 top-2 z-10 origin-[0] bg-[#0f0f17] px-2 peer-focus:px-2 peer-focus:text-blue-600 peer-placeholder-shown:scale-100 peer-placeholder-shown:-translate-y-1/2 peer-placeholder-shown:top-1/2 peer-focus:top-2 peer-focus:scale-75 peer-focus:-translate-y-4 left-1">Password:</label>
                                            </div>

                                            <div class="relative mb-4">
                                                <input type="email" id="email" name="email"
                                                    class="block px-2.5 pb-2.5 pt-4 w-full text-sm text-white bg-transparent rounded-lg border-1 border-gray-700 appearance-none focus:ring-0  peer"
                                                    placeholder=" ">
                                                <label for="email"
                                                    class="absolute text-sm text-gray-400 duration-300 transform -translate-y-4 scale-75 top-2 z-10 origin-[0] bg-[#0f0f17] px-2 peer-focus:px-2 peer-focus:text-blue-600 peer-placeholder-shown:scale-100 peer-placeholder-shown:-translate-y-1/2 peer-placeholder-shown:top-1/2 peer-focus:top-2 peer-focus:scale-75 peer-focus:-translate-y-4 left-1">Email:</label>
                                            </div>

                                            <div class="relative mb-4 pt-3">
                                                <select id="sub" name="sub"
                                                    class="bg-[#0f0f17] border border-gray-700 text-white-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5">
                                                    <?php
                                                $query = misc\mysql\query("SELECT * FROM `subs` WHERE `user` = ? AND `app` = ? AND `expiry` > ?", [$un, $_SESSION['app'], time()], "ssi");
                                                $rows = array();
                                                while ($r = mysqli_fetch_assoc($query->result)) {
                                                    $rows[] = $r;
                                                }
                                                foreach ($rows as $subrow) {
                                                    $sub = $subrow['subscription'];
                                                    $value = "[" . $subrow['subscription'] . "] - Expires: <script>document.write(convertTimestamp(" . $subrow["expiry"] . "));</script>";
                                                ?>
                                                    <option value="<?= $sub; ?>">
                                                    <?= $value; ?></option>
                                                    <?php
                                                }
                                                ?>
                                                </select>
                                                <label for="sub"
                                                    class="absolute text-sm text-gray-400 duration-300 transform -translate-y-4 scale-75 top-2 z-10 origin-[0] bg-[#0f0f17] px-2 peer-focus:px-2 peer-focus:text-blue-600 peer-placeholder-shown:scale-100 peer-placeholder-shown:-translate-y-1/2 peer-placeholder-shown:top-1/2 peer-focus:top-2 peer-focus:scale-75 peer-focus:-translate-y-4 left-1">User
                                                    Subscription</label>
                                            </div>

                                            <div class="relative mb-4 pt-3">
                                                <select id="var" name="var"
                                                    class="bg-[#0f0f17] border border-gray-700 text-white-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5">
                                                    <?php
                                                        $query = misc\mysql\query("SELECT `name`,`data` FROM `uservars` WHERE `user` = ? AND `app` = ?", [$un, $_SESSION['app']]);
                                                        $rows = array();
                                                        while ($r = mysqli_fetch_assoc($query->result)) {
                                                            $rows[] = $r;
                                                        }
                                                        foreach ($rows as $varrow) {
                                                            $value = $varrow['name'] . " : " . $varrow["data"];
                                                        ?>

                                                    <option value="<?= $varrow['name']; ?>">
                                                        <?= $value; ?></option>

                                                    <?php
                                                    }
                                                    ?>
                                                </select>
                                                <label for="var"
                                                    class="absolute text-sm text-gray-400 duration-300 transform -translate-y-4 scale-75 top-2 z-10 origin-[0] bg-[#0f0f17] px-2 peer-focus:px-2 peer-focus:text-blue-600 peer-placeholder-shown:scale-100 peer-placeholder-shown:-translate-y-1/2 peer-placeholder-shown:top-1/2 peer-focus:top-2 peer-focus:scale-75 peer-focus:-translate-y-4 left-1">User
                                                    Variable</label>
                                            </div>

                                            <div class="relative mb-4 pt-3">
                                                <select id="client2fa" name="client2fa"
                                                    class="bg-[#0f0f17] border border-gray-700 text-white-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5">
                                                    <?php
                                                        $query = misc\mysql\query("SELECT `2fa` FROM `users` WHERE `username` = ? AND `app` = ?", [$un, $_SESSION['app']]);
                                                        if ($query->num_rows > 0){
                                                            $client2fa = $row["2fa"];
                                                        }
                                                    ?>

                                                    <option value="0"
                                                        <?= $client2fa == 0 ? ' selected="selected"' : ''; ?>>
                                                        Disabled</option>
                                                    <option value="1"
                                                        <?= $client2fa == 1 ? ' selected="selected"' : ''; ?>>
                                                        Enabled</option>

                                                </select>
                                                <label for="client2fa"
                                                    class="absolute text-sm text-gray-400 duration-300 transform -translate-y-4 scale-75 top-2 z-10 origin-[0] bg-[#0f0f17] px-2 peer-focus:px-2 peer-focus:text-blue-600 peer-placeholder-shown:scale-100 peer-placeholder-shown:-translate-y-1/2 peer-placeholder-shown:top-1/2 peer-focus:top-2 peer-focus:scale-75 peer-focus:-translate-y-4 left-1">2FA</label>
                                            </div>

                                            <div class="relative mb-4">
                                                <input type="text" id="hwid" name="hwid"
                                                    class="block px-2.5 pb-2.5 pt-4 w-full text-sm text-white bg-transparent rounded-lg border-1 border-gray-700 appearance-none focus:ring-0  peer"
                                                    placeholder=" ">
                                                <label for="hwid"
                                                    class="absolute text-sm text-gray-400 duration-300 transform -translate-y-4 scale-75 top-2 z-10 origin-[0] bg-[#0f0f17] px-2 peer-focus:px-2 peer-focus:text-blue-600 peer-placeholder-shown:scale-100 peer-placeholder-shown:-translate-y-1/2 peer-placeholder-shown:top-1/2 peer-focus:top-2 peer-focus:scale-75 peer-focus:-translate-y-4 left-1">Additional
                                                    HWID:</label>
                                            </div>

                                            <p class="text-xs text-white-500">HWID: <?= $row['hwid'] ?? "N/A"; ?>
                                            </p>
                                            <p class="text-xs text-white-500">IP: <?= $row['ip']?? "N/A";?></p>

                                        </div>
                                        <div
                                            style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 10px;">

                                            <button name="deletesub"
                                                class="w-full text-white bg-orange-700 hover:bg-orange-800 focus:ring-4 focus:outline-none focus:ring-orange-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center"
                                                value="<?= urlencode($un); ?>">Delete Subscription</button>

                                            <button name="saveuser"
                                                class="w-full text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center"
                                                value="<?= urlencode($un); ?>">Save Changes</button>


                                            <button name="deletevar"
                                                class="w-full text-white bg-yellow-700 hover:bg-yellow-800 focus:ring-4 focus:outline-none focus:ring-yellow-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center"
                                                value="<?= urlencode($un); ?>">Delete User Variable</button>

                                            <button
                                                class="w-full text-white bg-red-700 hover:bg-red-800 focus:ring-4 focus:outline-none focus:ring-red-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center"
                                                onClick="window.location.href=window.location.href">Cancel</button>
                                        </div>

                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- End Edit User Modal -->
                    <?php
}
?>

<div class="p-4 bg-[#09090d] block sm:flex items-center justify-between lg:mt-1.5">
    <div class="mb-1 w-full bg-[#0f0f17] rounded-xl">
        <div class="mb-4 p-4">
            <?php require '../app/layout/breadcrumb.php'; ?>
            <h1 class="text-xl font-semibold text-white-900 sm:text-2xl ">Users</h1>
            <p class="text-xs text-gray-500">After someone registers for your app with a license, they will appear here.
                <a href="https://keyauthdocs.apidog.io/api/getting-started/register" target="_blank"
                    class="text-blue-600  hover:underline">Learn More</a>.
            </p>
            <br>
            <div class="p-4 flex flex-col">
                <div class="overflow-x-auto">
                    <form method="POST">
                        <!-- User Functions -->
                        <button type="button"
                            class="inline-flex text-white bg-blue-700 hover:opacity-60 focus:ring-0 font-medium rounded-lg text-sm px-5 py-2.5 mb-2 transition duration-200"
                            data-modal-toggle="create-user-modal" data-modal-target="create-user-modal">
                            <i class="lni lni-circle-plus mr-2 mt-1"></i>Create User
                        </button>
                        <a href="?page=user-vars"
                            class="inline-flex text-white bg-blue-700 hover:opacity-60 focus:ring-0 font-medium rounded-lg text-sm px-5 py-2.5 mb-2 transition duration-200">
                            <i class="lni lni-circle-plus mr-2 mt-1"></i>User Variables
                        </a>
                        <button type="button"
                            class="inline-flex text-white bg-blue-700 hover:opacity-60 focus:ring-0 font-medium rounded-lg text-sm px-5 py-2.5 mb-2 transition duration-200"
                            data-modal-toggle="extend-user-modal" data-modal-target="extend-user-modal">
                            <i class="lni lni-timer mr-2 mt-1"></i>Extend User(s)
                        </button>
                        <button type="button"
                            class="inline-flex text-white bg-blue-700 hover:opacity-60 focus:ring-0 font-medium rounded-lg text-sm px-5 py-2.5 mb-2 transition duration-200"
                            data-modal-toggle="subtract-user-modal" data-modal-target="subtract-user-modal">
                            <i class="lni lni-timer mr-2 mt-1"></i>Subtract User(s)
                        </button>
                        <?php 
                        if ($role != "tester"){?>
                        <button type="button"
                            class="inline-flex text-white bg-blue-700 hover:opacity-60 focus:ring-0 font-medium rounded-lg text-sm px-5 py-2.5 mb-2 transition duration-200"
                            data-modal-toggle="import-users-modal" data-modal-target="import-users-modal">
                            <i class="lni lni-upload mr-2 mt-1"></i>Import Users
                        </button>
                        <?php } ?>
                        <button
                            class="inline-flex text-white bg-blue-700 hover:opacity-60 focus:ring-0 font-medium rounded-lg text-sm px-5 py-2.5 mb-2 transition duration-200"
                            name="dlusers">
                            <i class="lni lni-download mr-2 mt-1"></i>Export Users
                        </button>
                        <!-- End User Functions -->
                    </form>


                    <!-- Delete User Functions -->
                    <button
                        class="inline-flex text-white bg-red-700 hover:opacity-60 focus:ring-0 font-medium rounded-lg text-sm px-5 py-2.5 mb-2 transition duration-200"
                        data-modal-toggle="del-all-users-modal" data-modal-target="del-all-users-modal">
                        <i class="lni lni-trash-can mr-2 mt-1"></i>Delete All Users
                    </button>
                    <button
                        class="inline-flex text-white bg-red-700 hover:opacity-60 focus:ring-0 font-medium rounded-lg text-sm px-5 py-2.5 mb-2 transition duration-200"
                        data-modal-toggle="del-expired-users-modal" data-modal-target="del-expired-users-modal">
                        <i class="lni lni-trash-can mr-2 mt-1"></i>Delete Expired Users
                    </button>
                    <button
                        class="inline-flex text-white bg-red-700 hover:opacity-60 focus:ring-0 font-medium rounded-lg text-sm px-5 py-2.5 mb-2 transition duration-200"
                        data-modal-toggle="reset-all-users-hwid-modal" data-modal-target="reset-all-users-hwid-modal">
                        <i class="lni lni-reload mr-2 mt-1"></i>Reset All Users HWID
                    </button>
                    <button
                        class="inline-flex text-white bg-red-700 hover:opacity-60 focus:ring-0 font-medium rounded-lg text-sm px-5 py-2.5 mb-2 transition duration-200"
                        data-modal-toggle="unban-all-users-modal" data-modal-target="unban-all-users-modal">
                        <i class="lni lni-trash-can mr-2 mt-1"></i>Unban All Users
                    </button>

                    <button id="dropdownselection" data-dropdown-toggle="usersSelecteddropdown" 
                        class="inline-flex text-white bg-red-700 hover:opacity-60 focus:ring-0 font-medium rounded-lg text-sm px-5 py-2.5 mb-2 transition duration-200 hidden"
                        type="button">Selection Options
                    </button>

                    <div id="usersSelecteddropdown" class="z-10 hidden bg-[#09090d] divide-y divide-gray-100 rounded-lg shadow w-44">
                        <ul class="py-2 text-sm text-white">
                            <li>
                                <form method="post">
                                    <input type="hidden" name="action" value="delete">
                                    <input type="hidden" id="selected" name="selected">
                                    <a href="#" onclick="this.parentNode.submit();" class="block px-4 py-2 focus:bold ml-2 hover:text-red-700">Delete selected</a>
                                </form>
                            </li>
                            <li>
                                <form method="post">
                                    <input type="hidden" name="action" value="unban">
                                    <input type="hidden" id="selected" name="selected">
                                    <a href="#" onclick="this.parentNode.submit();" class="block px-4 py-2 focus:bold ml-2 hover:text-red-700">Unban selected</a>
                                </form>
                            </li>
                            <li>
                                <form method="post">
                                    <input type="hidden" name="action" value="pause">
                                    <input type="hidden" id="selected" name="selected">
                                    <a href="#" onclick="this.parentNode.submit();" class="block px-4 py-2 focus:bold ml-2 hover:text-red-700">Pause selected</a>
                                </form>
                            </li>
                            <li>
                                <form method="post">
                                    <input type="hidden" name="action" value="unpause">
                                    <input type="hidden" id="selected" name="selected">
                                    <a href="#" onclick="this.parentNode.submit();" class="block px-4 py-2 focus:bold ml-2 hover:text-red-700">Unpause selected</a>
                                </form>
                            </li>
                            <li>
                                <form method="post">
                                    <input type="hidden" name="action" value="resethwid">
                                    <input type="hidden" id="selected" name="selected">
                                    <a href="#" onclick="this.parentNode.submit();" class="block px-4 py-2 focus:bold ml-2 hover:text-red-700">Reset hwids</a>
                                </form>
                            </li>
                        </ul>
                    </div>
                    <!-- End Delete User Functions -->

                    <!-- Create User Modal -->
                    <div id="create-user-modal" tabindex="-1" aria-hidden="true"
                        class="fixed top-0 left-0 right-0 z-50 hidden w-full p-4 overflow-x-hidden overflow-y-auto md:inset-0 h-[calc(100%-1rem)] max-h-full">
                        <div class="relative w-full max-w-md max-h-full">
                            <!-- Modal content -->
                            <div class="relative bg-[#0f0f17] rounded-lg border border-[#1d4ed8] shadow">
                                <div class="px-6 py-6 lg:px-8">
                                    <h3 class="mb-4 text-xl font-medium text-white-900">Create User</h3>
                                    <hr class="h-px mb-4 mt-4 bg-gray-700 border-0">
                                    <form class="space-y-6" method="POST">
                                        <div>

                                            <div class="relative mb-4">
                                                <input type="text" id="username" name="username"
                                                    class="block px-2.5 pb-2.5 pt-4 w-full text-sm text-white bg-transparent rounded-lg border-1 border-gray-700 appearance-none focus:ring-0  peer"
                                                    placeholder=" " autocomplete="on">
                                                <label for="username"
                                                    class="absolute text-sm text-gray-400 duration-300 transform -translate-y-4 scale-75 top-2 z-10 origin-[0] bg-[#0f0f17] px-2 peer-focus:px-2 peer-focus:text-blue-600 peer-placeholder-shown:scale-100 peer-placeholder-shown:-translate-y-1/2 peer-placeholder-shown:top-1/2 peer-focus:top-2 peer-focus:scale-75 peer-focus:-translate-y-4 left-1">Username</label>
                                            </div>

                                            <div class="relative mb-4">
                                                <input type="password" id="password" name="password"
                                                    class="block px-2.5 pb-2.5 pt-4 w-full text-sm text-white bg-transparent rounded-lg border-1 border-gray-700 appearance-none focus:ring-0  peer"
                                                    placeholder=" " autocomplete="on">
                                                <label for="password"
                                                    class="absolute text-sm text-gray-400 duration-300 transform -translate-y-4 scale-75 top-2 z-10 origin-[0] bg-[#0f0f17] px-2 peer-focus:px-2 peer-focus:text-blue-600 peer-placeholder-shown:scale-100 peer-placeholder-shown:-translate-y-1/2 peer-placeholder-shown:top-1/2 peer-focus:top-2 peer-focus:scale-75 peer-focus:-translate-y-4 left-1">Password</label>
                                            </div>

                                            <p class="text-xs text-gray-500">Generate and store secure passwords with 
                                                <a href="https://bitwarden.com/" target="_blank"
                                                    class="text-blue-600 hover:underline">Bitwarden</a> or <a href="https://1password.com/" target="_blank" class="text-blue-600 hover:underline">1Password</a>
                                            </p>

                                            <div class="relative mb-4 pt-3">
                                                <select id="sub" name="sub"
                                                    class="bg-[#0f0f17] border border-gray-700 text-white-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5">
                                                    <?php
                                                    $query = misc\mysql\query("SELECT * FROM `subscriptions` WHERE `app` = ? ORDER BY `level` ASC", [$_SESSION['app']]);
                                                    if ($query->num_rows > 0) {
                                                        while ($row = mysqli_fetch_array($query->result)) {
                                                            echo "  <option value=\"" . $row["name"] . "\">" . $row["name"] . "</option>";
                                                        }
                                                    }
                                                ?>
                                                </select>
                                            </div>

                                            <div class="relative max-w-sm">
                                                <div
                                                    class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                                                    <svg class="w-4 h-4 text-gray-500 " aria-hidden="true"
                                                        xmlns="http://www.w3.org/2000/svg" fill="currentColor"
                                                        viewBox="0 0 20 20">
                                                        <path
                                                            d="M20 4a2 2 0 0 0-2-2h-2V1a1 1 0 0 0-2 0v1h-3V1a1 1 0 0 0-2 0v1H6V1a1 1 0 0 0-2 0v1H2a2 2 0 0 0-2 2v2h20V4ZM0 18a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2V8H0v10Zm5-8h10a1 1 0 0 1 0 2H5a1 1 0 0 1 0-2Z" />
                                                    </svg>
                                                </div>
                                                <?php
                                                echo '
                                                <input datepicker datepicker-autohide type="datetime-local"
                                                    name="expiry"
                                                    class="bg-[#0f0f17] border border-gray-700 text-white-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full pl-10 p-2.5 "
                                                    placeholder="Subscription Expiry"
                                                    value="' . date("Y-m-d\TH:i", time() + 3600) .'" required>';?>
                                            </div>
                                        </div>
                                        <button name="adduser"
                                            class="w-full text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center">Add
                                            User</button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- End Create User Modal -->

                    <!-- Set User Var Modal -->
                    <div id="set-user-var-modal" tabindex="-1" aria-hidden="true"
                        class="fixed top-0 left-0 right-0 z-50 hidden w-full p-4 overflow-x-hidden overflow-y-auto md:inset-0 h-[calc(100%-1rem)] max-h-full">
                        <div class="relative w-full max-w-md max-h-full">
                            <!-- Modal content -->
                            <div class="relative bg-[#0f0f17] rounded-lg border border-[#1d4ed8] shadow">
                                <div class="px-6 py-6 lg:px-8">
                                    <h3 class="mb-4 text-xl font-medium text-white-900">Set User Variable</h3>
                                    <hr class="h-px mb-4 mt-4 bg-gray-700 border-0">
                                    <form class="space-y-6" method="POST">
                                        <div>
                                            <div class="relative mb-4">
                                                <input type="text" id="user" name="user"
                                                    class="block px-2.5 pb-2.5 pt-4 w-full text-sm text-white bg-transparent rounded-lg border-1 border-gray-700 appearance-none focus:ring-0  peer"
                                                    placeholder="Type username here" placeholder=" " autocomplete="on">
                                                <label for="user"
                                                    class="absolute text-sm text-gray-400 duration-300 transform -translate-y-4 scale-75 top-2 z-10 origin-[0] bg-[#0f0f17] px-2 peer-focus:px-2 peer-focus:text-blue-600 peer-placeholder-shown:scale-100 peer-placeholder-shown:-translate-y-1/2 peer-placeholder-shown:top-1/2 peer-focus:top-2 peer-focus:scale-75 peer-focus:-translate-y-4 left-1">User</label>
                                            </div>

                                            <div class="relative mb-4">
                                                <input type="text" id="var" name="var"
                                                    class="block px-2.5 pb-2.5 pt-4 w-full text-sm text-white bg-transparent rounded-lg border-1 border-gray-700 appearance-none focus:ring-0  peer"
                                                    placeholder="" autocomplete="on" list="vars" required>
                                                <datalist id="vars">
                                                    <?php
                                                        $query = misc\mysql\query("SELECT DISTINCT `name` FROM `uservars` WHERE `app` = ?", [$_SESSION['app']]);
                                                        if ($query->num_rows > 0) {
                                                            while ($row = mysqli_fetch_array($query->result)) {
                                                                echo "  <option value=\"" . $row["name"] . "\">" . $row["name"] . "</option>";
                                                            }
                                                        }
                                                    ?>
                                                </datalist>
                                                <label for="var"
                                                    class="absolute text-sm text-gray-400 duration-300 transform -translate-y-4 scale-75 top-2 z-10 origin-[0] bg-[#0f0f17] px-2 peer-focus:px-2 peer-focus:text-blue-600 peer-placeholder-shown:scale-100 peer-placeholder-shown:-translate-y-1/2 peer-placeholder-shown:top-1/2 peer-focus:top-2 peer-focus:scale-75 peer-focus:-translate-y-4 left-1">Variable</label>
                                            </div>

                                            <div class="relative mb-4">
                                                <label for="data" name="data"
                                                    class="absolute text-sm text-gray-400 duration-300 transform -translate-y-4 scale-75 top-2 z-10 origin-[0] bg-[#0f0f17] px-2 peer-focus:px-2 peer-focus:text-blue-600 peer-placeholder-shown:scale-100 peer-placeholder-shown:-translate-y-1/2 peer-placeholder-shown:top-1/2 peer-focus:top-2 peer-focus:scale-75 peer-focus:-translate-y-4 left-1">User
                                                    Variable Data</label>
                                                <textarea id="data" name="data" rows="4"
                                                    class="block p-2.5 w-full text-sm text-white-900 bg-[#0f0f17] rounded-lg border border-gray-700 focus:ring-blue-500 focus:border-blue-500"
                                                    placeholder="" maxlength="500" required></textarea>
                                            </div>
                                        </div>

                                        <div class="flex items-center mb-4">
                                            <input id="readOnly" name="readOnly" type="checkbox"
                                                class="w-4 h-4 text-blue-600 bg-[#0f0f17] border-gray-300 rounded focus:ring-blue-500 focus:ring-2"
                                                checked>
                                            <label for="readOnly"
                                                class="ml-2 text-sm font-medium text-white-900">Readonly</label>
                                        </div>
                                        <button name="setvar"
                                            class="w-full text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center">Set
                                            User Var</button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- End Set User Var Modal -->

                    <!-- Extend User Modal -->
                    <div id="extend-user-modal" tabindex="-1" aria-hidden="true"
                        class="fixed top-0 left-0 right-0 z-50 hidden w-full p-4 overflow-x-hidden overflow-y-auto md:inset-0 h-[calc(100%-1rem)] max-h-full">
                        <div class="relative w-full max-w-md max-h-full">
                            <!-- Modal content -->
                            <div class="relative bg-[#0f0f17] rounded-lg border border-[#1d4ed8] shadow">
                                <div class="px-6 py-6 lg:px-8">
                                    <h3 class="mb-4 text-xl font-medium text-white-900">Extend User(s)</h3>
                                    <hr class="h-px mb-4 mt-4 bg-gray-700 border-0">
                                    <form class="space-y-6" method="POST">
                                        <div>
                                            <div class="relative mb-4">
                                                <input type="text" id="user" name="user"
                                                    class="block px-2.5 pb-2.5 pt-4 w-full text-sm text-white bg-transparent rounded-lg border-1 border-gray-700 appearance-none focus:ring-0  peer"
                                                    placeholder="all" autocomplete="on">
                                                <label for="user"
                                                    class="absolute text-sm text-gray-400 duration-300 transform -translate-y-4 scale-75 top-2 z-10 origin-[0] bg-[#0f0f17] px-2 peer-focus:px-2 peer-focus:text-blue-600 peer-placeholder-shown:scale-100 peer-placeholder-shown:-translate-y-1/2 peer-placeholder-shown:top-1/2 peer-focus:top-2 peer-focus:scale-75 peer-focus:-translate-y-4 left-1">User</label>
                                            </div>

                                            <div class="relative mb-4">
                                                <select id="sub" name="sub"
                                                    class="bg-[#0f0f17] border border-gray-700 text-white-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5">
                                                    <?php
                                                        $query = misc\mysql\query("SELECT * FROM `subscriptions` WHERE `app` = ? ORDER BY `level` ASC", [$_SESSION['app']]);
                                                        if ($query->num_rows > 0) {
                                                            while ($row = mysqli_fetch_array($query->result)) {
                                                                echo "  <option value=\"" . $row["name"] . "\">" . $row["name"] . "</option>";
                                                            }
                                                        }
                                                    ?>
                                                </select>
                                                <label for="sub"
                                                    class="absolute text-sm text-gray-400 duration-300 transform -translate-y-4 scale-75 top-2 z-10 origin-[0] bg-[#0f0f17] px-2 peer-focus:px-2 peer-focus:text-blue-600 peer-placeholder-shown:scale-100 peer-placeholder-shown:-translate-y-1/2 peer-placeholder-shown:top-1/2 peer-focus:top-2 peer-focus:scale-75 peer-focus:-translate-y-4 left-1">Subscription</label>
                                            </div>

                                            <div class="relative mb-4">
                                                <select id="expiry" name="expiry"
                                                    class="bg-[#0f0f17] border border-gray-700 text-white-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5">
                                                    <option value="1">Seconds</option>
                                                    <option value="60">Minutes</option>
                                                    <option value="3600">Hours</option>
                                                    <option value="86400">Days</option>
                                                    <option value="604800">Weeks</option>
                                                    <option value="2629743">Months</option>
                                                    <option value="31556926">Years</option>
                                                    <option value="315569260">Lifetime</option>
                                                </select>
                                                <label for="expiry"
                                                    class="absolute text-sm text-gray-400 duration-300 transform -translate-y-4 scale-75 top-2 z-10 origin-[0] bg-[#0f0f17] px-2 peer-focus:px-2 peer-focus:text-blue-600 peer-placeholder-shown:scale-100 peer-placeholder-shown:-translate-y-1/2 peer-placeholder-shown:top-1/2 peer-focus:top-2 peer-focus:scale-75 peer-focus:-translate-y-4 left-1">License
                                                    Expiry Unit</label>
                                            </div>

                                            <div class="relative mb-4">
                                                <input type="text" inputmode="numeric" id="time" name="time"
                                                    class="block px-2.5 pb-2.5 pt-4 w-full text-sm text-white bg-transparent rounded-lg border-1 border-gray-700 appearance-none focus:ring-0  peer"
                                                    placeholder=" " autocomplete="on">
                                                <label for="time"
                                                    class="absolute text-sm text-gray-400 duration-300 transform -translate-y-4 scale-75 top-2 z-10 origin-[0] bg-[#0f0f17] px-2 peer-focus:px-2 peer-focus:text-blue-600 peer-placeholder-shown:scale-100 peer-placeholder-shown:-translate-y-1/2 peer-placeholder-shown:top-1/2 peer-focus:top-2 peer-focus:scale-75 peer-focus:-translate-y-4 left-1">Time
                                                    To Add</label>
                                            </div>
                                        </div>

                                        <div class="flex items-center mb-4">
                                            <input id="activeOnly" name="activeOnly" type="checkbox"
                                                class="w-4 h-4 text-blue-600 bg-[#0f0f17] border-gray-300 rounded focus:ring-blue-500 focus:ring-2"
                                                checked>
                                            <label for="activeOnly"
                                                class="ml-2 text-sm font-medium text-white-900">Active users
                                                only</label>
                                        </div>
                                        <button name="extenduser"
                                            class="w-full text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center">Extend
                                            User(s)</button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- End Extend User Modal -->

                    <!-- Extend Subtract User Modal -->
                    <div id="subtract-user-modal" tabindex="-1" aria-hidden="true"
                        class="fixed top-0 left-0 right-0 z-50 hidden w-full p-4 overflow-x-hidden overflow-y-auto md:inset-0 h-[calc(100%-1rem)] max-h-full">
                        <div class="relative w-full max-w-md max-h-full">
                            <!-- Modal content -->
                            <div class="relative bg-[#0f0f17] rounded-lg border border-[#1d4ed8] shadow">
                                <div class="px-6 py-6 lg:px-8">
                                    <h3 class="mb-4 text-xl font-medium text-white-900">Subtract Time From User(s)</h3>
                                    <hr class="h-px mb-4 mt-4 bg-gray-700 border-0">
                                    <form class="space-y-6" method="POST">
                                        <div>
                                            <div class="relative mb-4">
                                                <input type="text" id="user" name="user"
                                                    class="block px-2.5 pb-2.5 pt-4 w-full text-sm text-white bg-transparent rounded-lg border-1 border-gray-700 appearance-none focus:ring-0  peer"
                                                    placeholder=" " autocomplete="on">
                                                <label for="user"
                                                    class="absolute text-sm text-gray-400 duration-300 transform -translate-y-4 scale-75 top-2 z-10 origin-[0] bg-[#0f0f17] px-2 peer-focus:px-2 peer-focus:text-blue-600 peer-placeholder-shown:scale-100 peer-placeholder-shown:-translate-y-1/2 peer-placeholder-shown:top-1/2 peer-focus:top-2 peer-focus:scale-75 peer-focus:-translate-y-4 left-1">User</label>
                                            </div>

                                            <div class="relative mb-4">
                                                <select id="sub" name="sub"
                                                    class="bg-[#0f0f17] border border-gray-700 text-white-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5">
                                                    <?php
                                                         $query = misc\mysql\query("SELECT * FROM `subscriptions` WHERE `app` = ? ORDER BY `level` ASC", [$_SESSION['app']]);
                                                         if ($query->num_rows > 0) {
                                                             while ($row = mysqli_fetch_array($query->result)) {
                                                                 echo "  <option value=\"" . $row["name"] . "\">" . $row["name"] . "</option>";
                                                             }
                                                         }
                                                    ?>
                                                </select>
                                                <label for="sub"
                                                    class="absolute text-sm text-gray-400 duration-300 transform -translate-y-4 scale-75 top-2 z-10 origin-[0] bg-[#0f0f17] px-2 peer-focus:px-2 peer-focus:text-blue-600 peer-placeholder-shown:scale-100 peer-placeholder-shown:-translate-y-1/2 peer-placeholder-shown:top-1/2 peer-focus:top-2 peer-focus:scale-75 peer-focus:-translate-y-4 left-1">Subscription</label>
                                            </div>

                                            <div class="relative mb-4">
                                                <select id="expiry" name="expiry"
                                                    class="bg-[#0f0f17] border border-gray-700 text-white-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5">
                                                    <option value="1">Seconds</option>
                                                    <option value="60">Minutes</option>
                                                    <option value="3600">Hours</option>
                                                    <option value="86400">Days</option>
                                                    <option value="604800">Weeks</option>
                                                    <option value="2629743">Months</option>
                                                    <option value="31556926">Years</option>
                                                    <option value="315569260">Lifetime</option>
                                                </select>
                                                <label for="expiry"
                                                    class="absolute text-sm text-gray-400 duration-300 transform -translate-y-4 scale-75 top-2 z-10 origin-[0] bg-[#0f0f17] px-2 peer-focus:px-2 peer-focus:text-blue-600 peer-placeholder-shown:scale-100 peer-placeholder-shown:-translate-y-1/2 peer-placeholder-shown:top-1/2 peer-focus:top-2 peer-focus:scale-75 peer-focus:-translate-y-4 left-1">License
                                                    Expiry Unit</label>
                                            </div>

                                            <div class="relative mb-4">
                                                <input type="text" inputmode="numeric" id="time" name="time"
                                                    class="block px-2.5 pb-2.5 pt-4 w-full text-sm text-white bg-transparent rounded-lg border-1 border-gray-700 appearance-none focus:ring-0  peer"
                                                    placeholder=" " autocomplete="on">
                                                <label for="time"
                                                    class="absolute text-sm text-gray-400 duration-300 transform -translate-y-4 scale-75 top-2 z-10 origin-[0] bg-[#0f0f17] px-2 peer-focus:px-2 peer-focus:text-blue-600 peer-placeholder-shown:scale-100 peer-placeholder-shown:-translate-y-1/2 peer-placeholder-shown:top-1/2 peer-focus:top-2 peer-focus:scale-75 peer-focus:-translate-y-4 left-1">Time
                                                    To Subtract</label>
                                            </div>
                                        </div>
                                        <button name="subtractuser"
                                            class="w-full text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center">Subtract
                                            Time From User(s)</button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- End Subtact User Modal -->

                    <!-- Import Users Modal -->
                    <div id="import-users-modal" tabindex="-1" aria-hidden="true"
                        class="fixed top-0 left-0 right-0 z-50 hidden w-full p-4 overflow-x-hidden overflow-y-auto md:inset-0 h-[calc(100%-1rem)] max-h-full">
                        <div class="relative w-full max-w-md max-h-full">
                            <!-- Modal content -->
                            <div class="relative bg-[#0f0f17] rounded-lg border border-[#1d4ed8] shadow">
                                <div class="px-6 py-6 lg:px-8">
                                    <h3 class="mb-4 text-xl font-medium text-white-900">Import Users .json</h3>
                                    <hr class="h-px mb-4 mt-4 bg-gray-700 border-0">
                                    <form class="space-y-6" method="POST" enctype="multipart/form-data">
                                        <div class="relative">
                                            <input
                                                class="block w-full text-sm text-gray-400 border border-gray-700 rounded-lg cursor-pointer focus:outline-none"
                                                id="file_input" name="file_input" type="file">
                                        </div>

                                        <button name="importusersFile"
                                            class="w-full text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center">Import
                                            Users</button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- End Import Users Modal -->


                    <!-- Delete All Users Modal -->
                    <div id="del-all-users-modal" tabindex="-1"
                        class="fixed top-0 left-0 right-0 z-50 hidden p-4 overflow-x-hidden overflow-y-auto md:inset-0 h-[calc(100%-1rem)] max-h-full">
                        <div class="relative w-full max-w-md max-h-full">
                            <div class="relative bg-[#0f0f17] border border-red-700 rounded-lg shadow">
                                <div class="p-6 text-center">
                                    <div class="flex items-center p-4 mb-4 text-sm text-white border border-yellow-500 rounded-lg bg-[#0f0f17]"
                                        role="alert">
                                        <span class="sr-only">Info</span>
                                        <div>
                                            <span class="font-medium">Notice!</span> You're about to delete all your
                                            users. This can not be undone.
                                        </div>
                                    </div>
                                    <h3 class="mb-5 text-lg font-normal text-gray-200">Are you sure you want to delete
                                        all of your users?</h3>
                                    <form method="POST">
                                        <button data-modal-hide="del-all-users-modal" name="delusers"
                                            class="text-white bg-red-600 hover:bg-red-800 focus:ring-4 focus:outline-none focus:ring-red-300 font-medium rounded-lg text-sm inline-flex items-center px-5 py-2.5 text-center mr-2">
                                            Yes, I'm sure
                                        </button>
                                        <button data-modal-hide="del-all-users-modal" type="button"
                                            class="inline-flex text-white bg-gray-700 hover:opacity-60 focus:ring-0 font-medium rounded-lg text-sm px-5 py-2.5 mb-2 transition duration-200">No,
                                            cancel</button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- End Delete All Users Modal -->

                    <!-- Delete Expired Users Modal -->
                    <div id="del-expired-users-modal" tabindex="-1"
                        class="fixed top-0 left-0 right-0 z-50 hidden p-4 overflow-x-hidden overflow-y-auto md:inset-0 h-[calc(100%-1rem)] max-h-full">
                        <div class="relative w-full max-w-md max-h-full">
                            <div class="relative bg-[#0f0f17] border border-red-700 rounded-lg shadow">
                                <div class="p-6 text-center">
                                    <div class="flex items-center p-4 mb-4 text-sm text-white border border-yellow-500 rounded-lg bg-[#0f0f17]"
                                        role="alert">
                                        <span class="sr-only">Info</span>
                                        <div>
                                            <span class="font-medium">Notice!</span> You're about to delete all your
                                            expired users. This can not be undone.
                                        </div>
                                    </div>
                                    <h3 class="mb-5 text-lg font-normal text-gray-200">Are you sure you want to delete
                                        all of your expired users?</h3>
                                    <form method="POST">
                                        <button data-modal-hide="del-expired-users-modal" name="delexpusers"
                                            class="text-white bg-red-600 hover:bg-red-800 focus:ring-4 focus:outline-none focus:ring-red-300 font-medium rounded-lg text-sm inline-flex items-center px-5 py-2.5 text-center mr-2">
                                            Yes, I'm sure
                                        </button>
                                        <button data-modal-hide="del-expired-users-modal" type="button"
                                            class="inline-flex text-white bg-gray-700 hover:opacity-60 focus:ring-0 font-medium rounded-lg text-sm px-5 py-2.5 mb-2 transition duration-200">No,
                                            cancel</button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- End Expired All Users Modal -->

                    <!-- Reset All Users HWID Modal -->
                    <div id="reset-all-users-hwid-modal" tabindex="-1"
                        class="fixed top-0 left-0 right-0 z-50 hidden p-4 overflow-x-hidden overflow-y-auto md:inset-0 h-[calc(100%-1rem)] max-h-full">
                        <div class="relative w-full max-w-md max-h-full">
                            <div class="relative bg-[#0f0f17] border border-red-700 rounded-lg shadow">
                                <div class="p-6 text-center">
                                    <div class="flex items-center p-4 mb-4 text-sm text-white border border-yellow-500 rounded-lg bg-[#0f0f17]"
                                        role="alert">
                                        <span class="sr-only">Info</span>
                                        <div>
                                            <span class="font-medium">Notice!</span> You're about to reset all users
                                            HWIDs.
                                        </div>
                                    </div>
                                    <h3 class="mb-5 text-lg font-normal text-gray-200">Are you sure you want to reset
                                        the HWID of all users?</h3>
                                    <form method="POST">
                                        <button data-modal-hide="reset-all-users-hwid-modal" name="resetall"
                                            class="text-white bg-red-600 hover:bg-red-800 focus:ring-4 focus:outline-none focus:ring-red-300 font-medium rounded-lg text-sm inline-flex items-center px-5 py-2.5 text-center mr-2">
                                            Yes, I'm sure
                                        </button>
                                        <button data-modal-hide="reset-all-users-hwid-modal" type="button"
                                            class="inline-flex text-white bg-gray-700 hover:opacity-60 focus:ring-0 font-medium rounded-lg text-sm px-5 py-2.5 mb-2 transition duration-200">No,
                                            cancel</button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- End Reset All Users HWID Modal -->

                    <!-- Unban All Users Modal -->
                    <div id="unban-all-users-modal" tabindex="-1"
                        class="fixed top-0 left-0 right-0 z-50 hidden p-4 overflow-x-hidden overflow-y-auto md:inset-0 h-[calc(100%-1rem)] max-h-full">
                        <div class="relative w-full max-w-md max-h-full">
                            <div class="relative bg-[#0f0f17] border border-red-700 rounded-lg shadow">
                                <div class="p-6 text-center">
                                    <div class="flex items-center p-4 mb-4 text-sm text-white border border-yellow-500 rounded-lg bg-[#0f0f17]"
                                        role="alert">
                                        <span class="sr-only">Info</span>
                                        <div>
                                            <span class="font-medium">Notice!</span> You're about to unban all banned
                                            users.
                                        </div>
                                    </div>
                                    <h3 class="mb-5 text-lg font-normal text-gray-200">Are you sure you want to unban
                                        all banned users?</h3>
                                    <form method="POST">
                                        <button data-modal-hide="unban-all-users-modal" name="unbanall"
                                            class="text-white bg-red-600 hover:bg-red-800 focus:ring-4 focus:outline-none focus:ring-red-300 font-medium rounded-lg text-sm inline-flex items-center px-5 py-2.5 text-center mr-2">
                                            Yes, I'm sure
                                        </button>
                                        <button data-modal-hide="unban-all-users-modal" type="button"
                                            class="inline-flex text-white bg-gray-700 hover:opacity-60 focus:ring-0 font-medium rounded-lg text-sm px-5 py-2.5 mb-2 transition duration-200">No,
                                            cancel</button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- End Unban All Users Modal -->

                    <!-- Ban User Modal -->
                    <div id="ban-user" tabindex="-1" aria-hidden="true"
                        class="fixed grid place-items-center hidden h-screen bg-black bg-opacity-60 z-50 p-4 overflow-x-hidden overflow-y-auto md:inset-0 h-[calc(100%-1rem)] max-h-full">
                        <div class="relative w-full max-w-md max-h-full">
                            <!-- Modal content -->
                            <div class="relative bg-[#0f0f17] rounded-lg border border-[#1d4ed8] shadow">
                                <div class="px-6 py-6 lg:px-8">
                                    <h3 class="mb-4 text-xl font-medium text-white-900">Ban User</h3>
                                    <form class="space-y-6" method="POST">
                                        <div>

                                            <div class="relative mb-4">
                                                <input type="text" id="reason" name="reason"
                                                    class="block px-2.5 pb-2.5 pt-4 w-full text-sm text-white bg-transparent rounded-lg border-1 border-gray-700 appearance-none focus:ring-0  peer"
                                                    placeholder="" autocomplete="on" maxlength="99" required>
                                                <input type="hidden" class="banuser" name="un">
                                                <label for="users"
                                                    class="absolute text-sm text-gray-400 duration-300 transform -translate-y-4 scale-75 top-2 z-10 origin-[0] bg-[#0f0f17] px-2 peer-focus:px-2 peer-focus:text-blue-600 peer-placeholder-shown:scale-100 peer-placeholder-shown:-translate-y-1/2 peer-placeholder-shown:top-1/2 peer-focus:top-2 peer-focus:scale-75 peer-focus:-translate-y-4 left-1">Ban
                                                    Reason:</label>
                                            </div>
                                        </div>
                                        <div style="display: flex;">
                                            <button onclick="closeModal('ban-user')"
                                                class="w-full text-white bg-gray-700 hover:bg-gray-800 focus:ring-4 focus:outline-none focus:ring-gray-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center"
                                                style="margin-right: 10px;">Cancel</button>

                                            <button name="banuser"
                                                class="w-full text-white bg-red-700 hover:bg-red-800 focus:ring-4 focus:outline-none focus:ring-red-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center"
                                                style="margin-right: 10px;">Ban User</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- End Ban User Modal -->

                    <!-- START TABLE -->
                    <div class="relative overflow-x-auto shadow-md sm:rounded-lg pt-5">
                        <table id="kt_datatable_users" class="w-full text-sm text-left text-white">
                            <thead>
                                <tr class="fw-bolder fs-6 text-blue-700 px-7">
                                    <th class="px-6 py-3">Select</th>
                                    <th class="px-6 py-3">Username</th>
                                    <th class="px-6 py-3">HWID</th>
                                    <th class="px-6 py-3">IP</th>
                                    <th class="px-6 py-3">Creation Date</th>
                                    <th class="px-6 py-3">Last Login Date</th>
                                    <th class="px-6 py-3">Banned?</th>
                                    <th class="px-6 py-3">Actions</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                    <p class="text-xs text-red-600">Dropdown actions in <b>RED</b> do not show a confirmation!<a
                            class="text-blue-700"> Dropdown actions in <b>BLUE</b> will show a confirmation!</a></p>

                    <!-- Include the jQuery library -->
                    

                    <script>
                    function banuser(username) {
                        var banuser = $('.banuser');

                        // Set the value of the element with the class 'banuser'
                        banuser.attr('value', username);

                        // Open the ban-user modal
                        openModal('ban-user');
                    }

                    // JavaScript function to open the modal
                    </script>

                    <!-- Generate Random Password -->
                    <script>
                    function updateGeneratedPassword(value) {
                        const password = generateRandomPassword(value);

                        document.getElementById('genpw').value = password;
                        document.getElementById('password').value = password;
                        document.getElementById('length').value = "Current Password Length " + password.length;
                    }

                    function generateRandomPassword(length) {
                        const charset = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789!@#$%^&*()_+";
                        let password = "";
                        for (let i = 0; i < length; i++) {
                            const randomIndex = Math.floor(Math.random() * charset.length);
                            password += charset.charAt(randomIndex);
                        }
                        return password;
                    }

                    updateGeneratedPassword(document.getElementById('default-range').value);
                    </script>

                    <!-- Copy Generated Password To Clipboard -->
                    <script>
                    function copyContent(targetId) {
                        const codeBlock = document.getElementById(targetId);
                        const codeContent = codeBlock.value; // Use .value to get the content of a textarea

                        const tempTextarea = document.createElement("textarea");
                        tempTextarea.value = codeContent;
                        document.body.appendChild(tempTextarea);

                        tempTextarea.select();
                        document.execCommand("copy");

                        document.body.removeChild(tempTextarea);

                        alert("Copied Password To Clipboard!");
                    }

                    // Attach click event listeners to all copy buttons
                    const copyButtons = document.querySelectorAll(".copy-button");
                    copyButtons.forEach(button => {
                        button.addEventListener("click", () => {
                            const targetId = button.getAttribute("data-copy-target");
                            copyContent(targetId);
                        });
                    });
                    </script>