<?php
if ($_SESSION['username'] == "demodeveloper" || $_SESSION['username'] == "demoseller") 
{
   dashboard\primary\error("Demo accounts do not have access here... view manage apps for your owner ID!");
   die();
}

$twofactor = $row['twofactor'];

require_once '../auth/GoogleAuthenticator.php';
$gauth = new GoogleAuthenticator();
if ($row["googleAuthCode"] == NULL) {
    $code_2factor = $gauth->createSecret();
    misc\mysql\query("UPDATE `accounts` SET `googleAuthCode` = ? WHERE `username` = ?", [$code_2factor, $_SESSION['username']]);
} else {
    $code_2factor = $row["googleAuthCode"];
}

$google_QR_Code = $gauth->getQRCodeGoogleUrl($_SESSION['username'], $code_2factor, 'KeyAuth');

$query = misc\mysql\query("SELECT * FROM `accounts` WHERE `username` = ?", [$_SESSION['username']]);

if ($query->num_rows > 0) {
    while ($row = mysqli_fetch_array($query->result)) {
        $acclogs = $row['acclogs'];
        $expiry = $row["expires"];
        $emailVerify = $row["emailVerify"];
    }
}

if (isset($_POST['updatesettings'])) {
    $pfp = misc\etc\sanitize($_POST['pfp']);
    $acclogs = misc\etc\sanitize($_POST['acclogs']);
    $emailVerify = misc\etc\sanitize($_POST['emailVerify']);
    misc\mysql\query("UPDATE `accounts` SET `acclogs` = ? WHERE `username` = ?", [$acclogs, $_SESSION['username']]);

    if ($acclogs == 0) {
        misc\mysql\query("DELETE FROM `acclogs` WHERE `username` = ?", [$_SESSION['username']]); // delete all account logs
    }

    misc\mysql\query("UPDATE `accounts` SET `emailVerify` = ? WHERE `username` = ?", [$emailVerify, $_SESSION['username']]);
    if (isset($_POST['pfp']) && trim($_POST['pfp']) != '') {
        if (!filter_var($pfp, FILTER_VALIDATE_URL)) {
            dashboard\primary\error("Invalid Url For Profile Image!");
            echo "<meta http-equiv='Refresh' Content='2;'>";
            return;
        }
        if (strpos($pfp, "file:///") !== false) {
            dashboard\primary\error("Url must start with https://");
            echo "<meta http-equiv='Refresh' Content='2;'>";
            return;
        }

        $_SESSION['img'] = $pfp;
        misc\mysql\query("UPDATE `accounts` SET `img` = ? WHERE `username` = ?", [$pfp, $_SESSION['username']]);
    }

    dashboard\primary\success("Updated Account Settings!");
    }

    if (isset($_POST['dlacc'])) {
        echo "<meta http-equiv='Refresh' Content='0; url=download-types.php?type=account'>";
    }

    if (isset($_POST['submit_code'])) {
        if($_SESSION['discordLogin']) {
            echo "<meta http-equiv='Refresh' Content='3;'>";
            dashboard\primary\error("You can't use 2FA when you sign-in with different website! Use 2FA on that website instead");
            return;
        }

        $code = misc\etc\sanitize($_POST['scan_code1'] . ($_POST['scan_code2']) . ($_POST['scan_code3']) . ($_POST['scan_code4']) . ($_POST['scan_code5']) . ($_POST['scan_code6']));

        $query = misc\mysql\query("SELECT `googleAuthCode` from `accounts` WHERE `username` = ?", [$_SESSION['username']]);

        while ($row = mysqli_fetch_array($query->result)) {
            $secret_code = $row['googleAuthCode'];
        }

        $checkResult = $gauth->verifyCode($secret_code, $code, 2);

        if ($checkResult) {
            $query = misc\mysql\query("UPDATE `accounts` SET `twofactor` = '1' WHERE `username` = ?", [$_SESSION['username']]);
            if ($query->affected_rows > 0) {
                echo "<meta http-equiv='Refresh' Content='2;'>";
                dashboard\primary\success("Two-factor security has been successfully activated on your account!");
                dashboard\primary\wh_log($logwebhook, "{$username} has enabled 2FA", $webhookun, "3586ee", "No ping");
            } else {
                echo "<meta http-equiv='Refresh' Content='2;'>";
                dashboard\primary\wh_log($logwebhook, "{$username} has disabled 2FA", $webhookun, "3586ee", "No ping");;
                dashboard\primary\success("Two-factor security has been successfully disabled on your account!");
            }
        } else {
            dashboard\primary\error("Invalid 2FA code! Make sure your device time settings are synced.");
        }
    }

    if (isset($_POST['downloadSecWords'])){
        if ($secwords == NULL){
           
            function getRandomWords($count = 10) {
                $url = "https://random-word-api.herokuapp.com/word?number={$count}";
                $ch = curl_init($url);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                $response = curl_exec($ch);
                curl_close($ch);
            
                if ($response) {
                    return json_decode($response);
                } else {
                    return false;
                }
            }

            // Get 10 random words
            $randomWords = getRandomWords(10);
            
            if ($randomWords) {
                // Format the words into a comma-separated string
                $wordString = implode(', ', $randomWords);
                $query = misc\mysql\query("UPDATE `accounts` SET `secwords` =? WHERE `username` = ?", [$wordString, $_SESSION['username']]);
                if ($query->affected_rows > 0){
                    dashboard\primary\success("Successfully generated new security words!"); 
                } else {
                    dashboard\primary\error("Failed to generate new security words!");
                }
            }
             
        } else {
            dashboard\primary\error("Security words already generated. Contact staff if you need to reset them.");
        }
    }

    if (isset($_POST['submit_code_disable'])) 
    {
        $code = misc\etc\sanitize($_POST['scan_code']);

        $query = misc\mysql\query("SELECT `googleAuthCode` from `accounts` WHERE `username` = ?", [$_SESSION['username']]);

        while ($row = mysqli_fetch_array($query->result)) {
            $secret_code = $row['googleAuthCode'];
        }

        $checkResult = $gauth->verifyCode($secret_code, $code, 2);

        if ($checkResult) 
        {
            $query = misc\mysql\query("UPDATE `accounts` SET `twofactor` = '0', `googleAuthCode` = NULL WHERE `username` = ?", [$_SESSION['username']]);

            if ($query->affected_rows > 0) {
                dashboard\primary\success("Successfully disabled 2FA!");
            } else {
                dashboard\primary\error("Failed to disable 2FA!");
            }
        } else {
            dashboard\primary\error("Invalid 2FA code! Make sure your device time settings are synced.");
        }
    }

    if (isset($_POST['deleteWebauthn'])) 
    {
        $name = misc\etc\sanitize($_POST['deleteWebauthn']);

        $query = misc\mysql\query("DELETE FROM `securityKeys` WHERE `name` = ? AND `username` = ?", [$name, $_SESSION['username']]);

        if ($query->affected_rows > 0) {
            $query = misc\mysql\query("SELECT 1 FROM `securityKeys` WHERE `username` = ?", [$_SESSION['username']]);
            if ($query->num_rows == 0) {
                misc\mysql\query("UPDATE `accounts` SET `securityKey` = 0 WHERE `username` = ?", [$_SESSION['username']]);
            }
            dashboard\primary\success("Successfully deleted security key");
        } else {
            dashboard\primary\error("Failed to delete security key!");
        }
    }

?>

<div class="p-4 bg-[#09090d] block sm:flex items-center justify-between lg:mt-1.5">
    <div class="mb-1 w-full bg-[#0f0f17] rounded-xl">
        <div class="mb-4 p-4">
            <?php require '../app/layout/breadcrumb.php'; ?>
            <h1 class="text-xl font-semibold text-white-900 sm:text-2xl">Account Settings</h1>
            <p class="text-xs text-gray-500">Manage your account. Tip: You can press Ctrl + S to save settings!</p>
            <div class="p-4 flex flex-col">
                <div class="overflow-x-auto">
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

                    <form method="post">
                        <div id="lol" class="grid grid-cols-1 lg:grid-cols-2 2xl:grid-cols-8 gap-2">
                            <div class="relative">
                                <select id="acclogs" name="acclogs"
                                    class="bg-[#0f0f17] border border-gray-700 text-white-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5">
                                    <option value="1" <?= $acclogs == 1 ? ' selected="selected"' : ''; ?>>Enabled
                                    </option>
                                    <option value="0" <?= $acclogs == 0 ? ' selected="selected"' : ''; ?>>Disabled
                                    </option>
                                </select>
                                <label for="acclogs"
                                    class="absolute text-sm text-gray-400 duration-300 transform -translate-y-4 scale-75 top-2 z-10 origin-[0] bg-[#0f0f17] px-2 peer-focus:px-2 peer-focus:text-blue-600 peer-placeholder-shown:scale-100 peer-placeholder-shown:-translate-y-1/2 peer-placeholder-shown:top-1/2 peer-focus:top-2 peer-focus:scale-75 peer-focus:-translate-y-4 left-1">Account
                                    Logs</label>
                            </div>

                            <div class="relative">
                                <select id="emailVerify" name="emailVerify"
                                    class="bg-[#0f0f17] border border-gray-700 text-white-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5">
                                    <option value="1" <?= $emailVerify == 1 ? ' selected="selected"' : ''; ?>>Enabled
                                    </option>
                                    <option value="0" <?= $emailVerify == 0 ? ' selected="selected"' : ''; ?>>Disabled
                                    </option>
                                </select>
                                <label for="emailVerify"
                                    class="absolute text-sm text-gray-400 duration-300 transform -translate-y-4 scale-75 top-2 z-10 origin-[0] bg-[#0f0f17] px-2 peer-focus:px-2 peer-focus:text-blue-600 peer-placeholder-shown:scale-100 peer-placeholder-shown:-translate-y-1/2 peer-placeholder-shown:top-1/2 peer-focus:top-2 peer-focus:scale-75 peer-focus:-translate-y-4 left-1">New
                                    Location Alerts
                                </label>
                            </div>
                        </div>

                        <div class="relative mb-4 mt-4">
                            <input type="text" name="username"
                                class="block px-2.5 pb-2.5 pt-4 w-full text-sm text-white bg-transparent rounded-lg border-1 border-gray-700 appearance-none focus:ring-0  peer"
                                placeholder=" " autocomplete="on" value="<?= $_SESSION['username'];?>" readonly>
                            <label for="username"
                                class="absolute text-sm text-gray-400 duration-300 transform -translate-y-4 scale-75 top-2 origin-[0] bg-[#0f0f17] px-2 peer-focus:px-2 peer-focus:text-blue-600 peer-placeholder-shown:scale-100 peer-placeholder-shown:-translate-y-1/2 peer-placeholder-shown:top-1/2 peer-focus:top-2 peer-focus:scale-75 peer-focus:-translate-y-4 left-1">Username</label>
                        </div>

                        <?php 
                            if ($_SESSION["role"] != "Reseller"){ ?>
                        <div class="relative mb-4 mt-4">
                            <input type="text" name="ownerid"
                                class="block px-2.5 pb-2.5 pt-4 w-full text-sm text-white bg-transparent rounded-lg border-1 border-gray-700 appearance-none focus:ring-0  peer"
                                placeholder=" " autocomplete="on"
                                value="<?= $_SESSION['ownerid'] ?? "Manager or Reseller accounts do not have OwnerIDs as they can't create applications.";?>"
                                readonly>
                            <label for="ownerid"
                                class="absolute text-sm text-gray-400 duration-300 transform -translate-y-4 scale-75 top-2 origin-[0] bg-[#0f0f17] px-2 peer-focus:px-2 peer-focus:text-blue-600 peer-placeholder-shown:scale-100 peer-placeholder-shown:-translate-y-1/2 peer-placeholder-shown:top-1/2 peer-focus:top-2 peer-focus:scale-75 peer-focus:-translate-y-4 left-1">OwnerID
                            </label>
                        </div>
                        <?php } ?>

                        <div class="relative mb-4 mt-4">
                            <input type="text" name="expires"
                                class="block px-2.5 pb-2.5 pt-4 w-full text-sm text-white bg-transparent rounded-lg border-1 border-gray-700 appearance-none focus:ring-0  peer"
                                placeholder=" " value="<?php if ($_SESSION["role"] == "tester")?> Never "
                                autocomplete="on" readonly>
                            <?php if($_SESSION["role"] == "tester"){ ?>
                            <label for="expires"
                                class="absolute text-sm text-gray-400 duration-300 transform -translate-y-4 scale-75 top-2 origin-[0] bg-[#0f0f17] px-2 peer-focus:px-2 peer-focus:text-blue-600 peer-placeholder-shown:scale-100 peer-placeholder-shown:-translate-y-1/2 peer-placeholder-shown:top-1/2 peer-focus:top-2 peer-focus:scale-75 peer-focus:-translate-y-4 left-1">Subscription
                                Expires: </label>
                            <?php } ?>
                            <label for="expires"
                                class="absolute text-sm text-gray-400 duration-300 transform -translate-y-4 scale-75 top-2 origin-[0] bg-[#0f0f17] px-2 peer-focus:px-2 peer-focus:text-blue-600 peer-placeholder-shown:scale-100 peer-placeholder-shown:-translate-y-1/2 peer-placeholder-shown:top-1/2 peer-focus:top-2 peer-focus:scale-75 peer-focus:-translate-y-4 left-1">Subscription
                                Expires </label>
                        </div>

                        <script>
                        var expiryInput = document.querySelector('input[name="expires"]');
                        var expiryValue = <?= $expiry ?>;
                        expiryInput.value = convertTimestamp(expiryValue);
                        </script>

                        <div class="relative mb-4 mt-4">
                            <input type="url" name="pfp" id="pfp"
                                class="block px-2.5 pb-2.5 pt-4 w-full text-sm text-white bg-transparent rounded-lg border-1 border-gray-700 appearance-none focus:ring-0  peer"
                                placeholder=" " max="200" value="<?= $_SESSION['img']; ?>" autocomplete="on">
                            <label for="pfp"
                                class="absolute text-sm text-gray-400 duration-300 transform -translate-y-4 scale-75 top-2 origin-[0] bg-[#0f0f17] px-2 peer-focus:px-2 peer-focus:text-blue-600 peer-placeholder-shown:scale-100 peer-placeholder-shown:-translate-y-1/2 peer-placeholder-shown:top-1/2 peer-focus:top-2 peer-focus:scale-75 peer-focus:-translate-y-4 left-1">Profile
                                Picture URL</label>
                        </div>
                        <?php if($_SESSION["role"] != "Manager"){?>
                        <h1 class="text-sm font-semibold text-white-900 sm:text-sm mb-3">Security Words</h1>

                        <a class="block max-w-full p-6 bg-[#09090d] border-1 border-gray-300 rounded-lg shadow mb-5">
                            <p class="text-regular text-white-500">Security Words: 10 random words
                                that you can use to regain access to your account if you forget your order ID, email,
                                password, and have no other way to verify the account belongs to you!</p>
                            <br>
                            <ul class="max space-y-1 text-gray-500 list-disc list-inside">
                                <li>Can only be generated <b>1 TIME!</b> (unless you get a new list from an admin)</li>
                                <li>Should be stored in 2 locations! (USB and PC for example)</li>
                                <li>Should NOT be called something obvious like "KeyAuthSecurityWords.txt". Prevent
                                    theft! Be smart!</li>
                                <li>Should never be shared!</li>
                                <li>Only admins can give you new words</li>
                                <li>Remember, your words will appear below once generated. Once you refresh the page,
                                    the words will disappear.</li>
                            </ul>

                            <p class="text-md text-blue-700 pt-5">
                                <?= $wordString; ?>
                            </p>

                            <div class="flex justify-end">
                                <?php if ($secwords == NULL){ ?>
                                <button
                                    class="inline-flex text-white bg-blue-700 hover:opacity-60 focus:ring-0 font-medium rounded-lg text-sm px-5 py-2.5 mb-2 transition duration-200"
                                    name="downloadSecWords">
                                    <i class="lni lni-download mr-2 mt-1"></i>Generate Words
                                </button>
                                <?php } else {?>
                                <p class="text-sm text-red-700">
                                    You already generated your security words. Please contact support if you forgot
                                    them.
                                </p>
                                <?php } ?>
                            </div>
                        </a>
                        <?php } ?>

                        <!-- Button Functions -->
                        <button
                            class="inline-flex text-white bg-blue-700 hover:opacity-60 focus:ring-0 font-medium rounded-lg text-sm px-5 py-2.5 mb-2 transition duration-200"
                            name="updatesettings" id="updatesettings">
                            <i class="lni lni-circle-plus mr-2 mt-1"></i>Save
                        </button>

                        <button
                            class="inline-flex text-white bg-blue-700 hover:opacity-60 focus:ring-0 font-medium rounded-lg text-sm px-5 py-2.5 mb-2 transition duration-200"
                            data-popover-target="linkDiscord-popover"
                            onclick="window.open('https://<?php echo ($_SERVER['HTTP_HOST'] ?? $_SERVER['SERVER_NAME']) ?>/api-php/discord/','popup','width=600,height=600'); return false;">
                            <i class="lni lni-discord-alt mr-2 mt-1"></i>Link Discord
                        </button>
                        <?php dashboard\primary\popover("linkDiscord-popover", "Link Discord", "Linking to Discord will allow you to get a role if you have a paid subscription and be able to talk in the Discord Server."); ?>

                        <?php if (!$twofactor){
                            echo '<a class="inline-flex text-white bg-blue-700 hover:opacity-60 focus:ring-0 font-medium rounded-lg text-sm px-5 py-2.5 mb-2 transition duration-200 cursor-pointer"
                            data-modal-target="enable-2fa-modal" data-modal-toggle="enable-2fa-modal"><i class="lni lni-shield mr-2 mt-1"></i>Enable 2FA</a>';
                        } else {
                            echo '<a class="inline-flex text-white bg-red-700 hover:opacity-60 focus:ring-0 font-medium rounded-lg text-sm px-5 py-2.5 mb-2 transition duration-200 cursor-pointer" 
                            data-modal-target="disable-2fa-modal" data-modal-toggle="disable-2fa-modal"><i class="lni lni-shield mr-2 mt-1"></i>Disable 2FA</a>';
                        }
                        ?>

                        <button name="dlacc"
                            class="inline-flex text-white bg-purple-700 hover:opacity-60 focus:ring-0 font-medium rounded-lg text-sm px-5 py-2.5 mb-2 transition duration-200 cursor-none" disabled>
                            <i class="lni lni-download mr-2 mt-1"></i>Export Account
                        </button>

                        <button type="button"
                            class="inline-flex text-white bg-purple-700 hover:opacity-60 focus:ring-0 font-medium rounded-lg text-sm px-5 py-2.5 mb-2 transition duration-200"
                            data-modal-target="fido2-modal" data-modal-toggle="fido2-modal">
                            <i class="lni lni-shield mr-2 mt-1"></i>FIDO2 Webauthn (Security Key)
                        </button>

                        <a href="https://<?= $_SERVER['HTTP_HOST'] ?? $_SERVER['SERVER_NAME'] ?>/forgot/"
                            target="_blank" type="button"
                            class="inline-flex text-white bg-orange-500 hover:opacity-60 focus:ring-0 font-medium rounded-lg text-sm px-5 py-2.5 mb-2 transition duration-200">
                            <i class="lni lni-reload mr-2 mt-1"></i>Change Password
                        </a>
                        <a href="https://<?= $_SERVER['HTTP_HOST'] ?? $_SERVER['SERVER_NAME']?>/changeEmail/"
                            target="_blank" type="button"
                            class="inline-flex text-white bg-orange-500 hover:opacity-60 focus:ring-0 font-medium rounded-lg text-sm px-5 py-2.5 mb-2 transition duration-200">
                            <i class="lni lni-reload mr-2 mt-1"></i>Change Email
                        </a>
                        <a href="https://<?= $_SERVER['HTTP_HOST'] ?? $_SERVER['SERVER_NAME']?>/changeUsername/"
                            target="_blank" type="button"
                            class="inline-flex text-white bg-orange-500 hover:opacity-60 focus:ring-0 font-medium rounded-lg text-sm px-5 py-2.5 mb-2 transition duration-200">
                            <i class="lni lni-reload mr-2 mt-1"></i>Change Username
                        </a>
                        <a href="https://<?= $_SERVER['HTTP_HOST'] ?? $_SERVER['SERVER_NAME']?>/deleteAccount/"
                            target="_blank" type="button"
                            class="inline-flex text-white bg-red-700 hover:opacity-60 focus:ring-0 font-medium rounded-lg text-sm px-5 py-2.5 mb-2 transition duration-200">
                            <i class="lni lni-trash-can mr-2 mt-1"></i>Delete Account
                        </a>
                    </form>
                    <!-- End Button Functions -->

                    <?php 
                    if (!$twofactor) {?>
                    <!-- Enable 2fa Modal -->
                    <div id="enable-2fa-modal" tabindex="-1" aria-hidden="true"
                        class="fixed top-0 left-0 right-0 z-50 hidden w-full p-4 overflow-x-hidden overflow-y-auto md:inset-0 h-[calc(100%-1rem)] max-h-full">
                        <div class="relative w-full max-w-md max-h-full">
                            <!-- Modal content -->
                            <div class="relative bg-[#0f0f17] rounded-lg border-[#1d4ed8] shadow">
                                <div class="px-6 py-6 lg:px-8">
                                    <h3 class="mb-4 text-xl font-medium text-white-900">Enable 2FA (Two Factor
                                        Authentication)</h3>
                                    <form class="space-y-6" method="POST">
                                        <div>
                                            <label class="mb-5">Scan this QR code into your 2FA App.</label>
                                            <img class="mb-5 mt-5" src="<?= $google_QR_Code ?>" />
                                            <label class="mb-5">Can't scan the QR code? Manually set it instead, code:
                                                <code class="text-blue-700"><?= $code_2factor ?></code></label>
                                            <br><br>
                                            <div id="otp" class="tfa-container">
                                                <input type="text" inputmode="numeric" maxlength="1" id="scan_code1"
                                                    name="scan_code1"
                                                    class="block px-2.5 pb-2.5 pt-4 w-full text-lg text-white bg-transparent rounded-lg border-1 border-gray-600 appearance-none focus:ring-0 peer"
                                                    placeholder=" " autocomplete="on" onpaste="handlePaste(event)"
                                                    required>

                                                <input type="text" inputmode="numeric" maxlength="1" id="scan_code2"
                                                    name="scan_code2"
                                                    class="block px-2.5 pb-2.5 pt-4 w-full text-lg text-white bg-transparent rounded-lg border-1 border-gray-600 appearance-none focus:ring-0 peer"
                                                    placeholder=" " autocomplete="on" required>

                                                <input type="text" inputmode="numeric" maxlength="1" id="scan_code3"
                                                    name="scan_code3"
                                                    class="block px-2.5 pb-2.5 pt-4 w-full text-lg text-white bg-transparent rounded-lg border-1 border-gray-600 appearance-none focus:ring-0 peer"
                                                    placeholder=" " autocomplete="on" required>

                                                <input type="text" inputmode="numeric" maxlength="1" id="scan_code4"
                                                    name="scan_code4"
                                                    class="block px-2.5 pb-2.5 pt-4 w-full text-lg text-white bg-transparent rounded-lg border-1 border-gray-600 appearance-none focus:ring-0 peer"
                                                    placeholder=" " autocomplete="on" required>

                                                <input type="text" inputmode="numeric" maxlength="1" id="scan_code5"
                                                    name="scan_code5"
                                                    class="block px-2.5 pb-2.5 pt-4 w-full text-lg text-white bg-transparent rounded-lg border-1 border-gray-600 appearance-none focus:ring-0 peer"
                                                    placeholder=" " autocomplete="on" required>

                                                <input type="text" inputmode="numeric" maxlength="1" id="scan_code6"
                                                    name="scan_code6"
                                                    class="block px-2.5 pb-2.5 pt-4 w-full text-lg text-white bg-transparent rounded-lg border-1 border-gray-600 appearance-none focus:ring-0 peer"
                                                    placeholder=" " autocomplete="on" required>
                                            </div>

                                            <script
                                                src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js">
                                            </script>
                                            <script>



                                            </script>
                                        </div>
                                        <button name="submit_code" id="submit_code"
                                            class="w-full text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center"
                                            >Enable 2FA</button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- End Enable 2fa Modal -->
                    <?php } ?>

                    <!-- Disable 2fa Modal -->
                    <div id="disable-2fa-modal" tabindex="-1" aria-hidden="true"
                        class="fixed top-0 left-0 right-0 z-50 hidden w-full p-4 overflow-x-hidden overflow-y-auto md:inset-0 h-[calc(100%-1rem)] max-h-full">
                        <div class="relative w-full max-w-md max-h-full">
                            <!-- Modal content -->
                            <div class="relative bg-[#0f0f17] rounded-lg border-[#1d4ed8] shadow">
                                <button type="button"
                                    class="absolute top-3 right-2.5 text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm w-8 h-8 ml-auto inline-flex justify-center items-center"
                                    data-modal-hide="disable-2fa-modal">
                                    <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg"
                                        fill="none" viewBox="0 0 14 14">
                                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                            stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6" />
                                    </svg>
                                    <span class="sr-only">Close modal</span>
                                </button>
                                <div class="px-6 py-6 lg:px-8">
                                    <h3 class="mb-4 text-xl font-medium text-white-900">Disable 2FA (Two Factor
                                        Authentication)</h3>
                                    <form class="space-y-6" method="POST">
                                        <div>
                                            <div class="relative mb-4 mt-5">
                                                <input type="text" id="scan_code" name="scan_code"
                                                    class="block px-2.5 pb-2.5 pt-4 w-full text-sm text-white bg-transparent rounded-lg border-1 border-gray-600 appearance-none focus:ring-0  peer"
                                                    placeholder=" " autocomplete="on" required="">
                                                <label for="scan_code"
                                                    class="absolute text-sm text-gray-400 duration-300 transform -translate-y-4 scale-75 top-2 z-10 origin-[0] bg-[#0f0f17] px-2 peer-focus:px-2 peer-focus:text-blue-600 peer-placeholder-shown:scale-100 peer-placeholder-shown:-translate-y-1/2 peer-placeholder-shown:top-1/2 peer-focus:top-2 peer-focus:scale-75 peer-focus:-translate-y-4 left-1">6
                                                    Digit Code from your 2FA app</label>
                                            </div>
                                        </div>
                                        <button name="submit_code_disable"
                                            class="w-full text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center">Disable
                                            2FA</button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- End Disable 2fa Modal -->

                    <!-- FIDO2 Modal -->
                    <form class="space-y-6" method="POST">
                        <div id="fido2-modal" tabindex="-1" aria-hidden="true"
                            class="fixed top-0 left-0 right-0 z-50 hidden w-full p-4 overflow-x-hidden overflow-y-auto md:inset-0 h-[calc(100%-1rem)] max-h-full">
                            <div class="relative w-full max-w-md max-h-full">
                                <div class="relative bg-[#0f0f17] rounded-lg border border-[#1d4ed8] shadow">
                                    <div class="px-6 py-6 lg:px-8">
                                        <h3 class="mb-4 text-xl font-medium text-white-900">FIDO2 WebAuthn (Security
                                            Keys)</h3>
                                        <hr class="h-px mb-4 mt-4 bg-gray-700 border-0">
                                        <div>
                                            <div class="relative mb-4">
                                                <input type="text" id="webauthn_name" name="webauthn_name"
                                                    placeholder=" " maxlength="99"
                                                    class="block px-2.5 pb-2.5 pt-4 w-full text-sm text-white bg-transparent rounded-lg border-1 border-gray-700 appearance-none focus:ring-0 peer"
                                                    autocomplete="on">
                                                <label for="webauthn_name"
                                                    class="absolute text-sm text-gray-400 duration-300 transform -translate-y-4 scale-75 top-2 z-10 origin-[0] bg-[#0f0f17] px-2 peer-focus:px-2 peer-focus:text-blue-600 peer-placeholder-shown:scale-100 peer-placeholder-shown:-translate-y-1/2 peer-placeholder-shown:top-1/2 peer-focus:top-2 peer-focus:scale-75 peer-focus:-translate-y-4 left-1">
                                                    New Security Key Name</label>
                                            </div>
                                        </div>
                                        <button type="button" onclick="newregistration()"
                                            class="w-full text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center mb-5">Register
                                            New Security Key</button>

                                        <h3 class="mb-4 text-xl font-medium text-white-900">Existing Security Keys</h3>
                                        <?php
                                                $query = misc\mysql\query("SELECT * FROM `securityKeys` WHERE `username` = ?", [$_SESSION['username']]);
                                                    if ($query->num_rows > 0) {
                                                        while ($row = mysqli_fetch_array($query->result)) {
                                                            ?>
                                        <button type="submit" name="deleteWebauthn" value="<?= $row["name"]; ?>"
                                            onclick="return confirm('Are you sure you want to delete your security key? This cannot be undone.')"
                                            class="w-full text-white bg-red-700 hover:bg-red-800 focus:ring-4 focus:outline-none focus:ring-red-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center mb-5">Delete
                                            Security Key: <?= $row["name"]; ?></button>
                                        <?php
                                                            }
                                                        }
                                            ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                    <!-- End FIDO2 Modal -->

                    <script>
                    $(document).keydown(function(event) {
                            if (event.ctrlKey && event.key === 's') {
                                    $("#updatesettings").click();
                                    event.preventDefault();
                            }
                    });

                    function handlePaste(event) {
                        event.preventDefault();
                        var clipboardData = event.clipboardData || window.clipboardData;
                        var pastedData = clipboardData.getData('text');
                        var inputs = document.querySelectorAll(".tfa-container input");

                        // Check if the pasted data is exactly 6 digits long
                        if (/^\d{6}$/.test(pastedData)) {
                            for (var i = 0; i < 6; i++) {
                                inputs[i].value = pastedData.charAt(i);
                            }
                        }
                    }

                    function updateButtonState(inputs) {
                        var allNumeric = true;

                        // Check if all input fields have numeric values
                        for (var i = 0; i < inputs.length; i++) {
                            if (!/^\d$/.test(inputs[i].value)) {
                                allNumeric = false;
                                break;
                            }
                        }

                        // Enable or disable the button based on the input values
                        var button = document.getElementById('submit_code');
                        button.disabled = !allNumeric;
                    }

                    // Add event listeners to each input field to monitor changes
                    var inputs = document.querySelectorAll(".tfa-container input");
                    for (var i = 0; i < inputs.length; i++) {
                        (function(index){
                            inputs[index].addEventListener('input', function(e) {
                                // Move to the next field if the current one has a value
                                if (this.value.length === this.maxLength) {
                                    if (index < inputs.length - 1) {
                                        inputs[index + 1].focus(); // Move focus to the next input field
                                    }
                                }
                            });
                        
                            inputs[index].addEventListener('keydown', function(e) {
                                // Move to the previous field if backspace is pressed on an empty field
                                if (e.key === "Backspace" && this.value.length === 0) {
                                    if (index > 0) {
                                        inputs[index - 1].focus(); // Move focus to the previous input field
                                    }
                                }
                            });
                        })(i);

                        inputs[i].addEventListener('input', function() {
                            updateButtonState(inputs);
                        });
                    }
                    </script>