<?php
if ($_SESSION["role"] != 'Reseller'){
    dashboard\primary\error("Only resellers can access this page.");
    die();
}

$query = misc\mysql\query("SELECT `keylevels` FROM `accounts` WHERE `username` = ?",[$_SESSION['username']]);
$row = mysqli_fetch_array($query->result);
$keylevels = $row['keylevels'];

if (isset($_SESSION['keys_array'])) {
    $list = $_SESSION['keys_array'];
    $keys = NULL;
    for ($i = 0; $i < count($list); $i++) {
        $keys .= "" . $list[$i] . "<br>";
    }
    echo "<div class=\"card\"> <div class=\"card-body\" id=\"multi-keys\"> $keys </div> </div> <br>";
    echo "<button onclick=\"copyToClipboard()\" class=\"inline-flex text-white bg-blue-700 hover:opacity-60 focus:ring-0 font-medium rounded-lg text-sm px-5 py-2.5 mb-2 transition duration-200\">Copy new license(s)</button>";
    unset($_SESSION['keys_array']);
}
    if (isset($_POST['genkeys'])) {
        $key = misc\license\createLicense($_POST['amount'], $_POST['mask'], 1, $_POST['level'], $_POST['note'], $_POST['expiry']);
        switch ($key) {
            case 'max_keys':
                dashboard\primary\error("You can only generate 100 licenses at a time");
                break;
            case 'no_negative':
                dashboard\primary\error("Amount can't be negative!");
                break;
            case 'unauthed_level':
                dashboard\primary\error("Not authorized to make licenses with that level!");
                break;
            case 'invalid_exp':
                dashboard\primary\error("Invalid expiry!");
                break;
            case 'insufficient_balance':
                dashboard\primary\error("Not enough balance to create license(s)");
                break;
            case 'dupe_custom_key':
                dashboard\primary\error("Can't do custom key with amount greater than one");
                break;
            default:
                if (misc\etc\sanitize($_POST['amount']) > 1) {
                    $_SESSION['keys_array'] = $key;
                    echo "<meta http-equiv='Refresh' Content='0;'>";
                } else {
                    echo "<script>navigator.clipboard.writeText('" . array_values($key)[0] . "');</script>";
                    dashboard\primary\success("License Created And Copied To Clipboard!");
                    misc\auditLog\send("Created a new license");
                }
                break;
        }
    }

    if (isset($_POST['deletekey'])) {
        $userToo = ($_POST['delUserToo'] == "on") ? 1 : 0;
        $resp = misc\license\deleteSingular($_POST['deletekey'], $userToo);
        switch ($resp) {
            case 'failure':
                dashboard\primary\error("Failed to delete license!");
                break;
            case 'nope':
                misc\auditLog\send("Attempted (and failed) to delete license he doesn't own " . $_POST['deletekey']);
                dashboard\primary\error("Nope, nice try!");
                break;
            case 'success':
                misc\auditLog\send("Deleted license key " . $_POST['deletekey']);
                dashboard\primary\success("Successfully deleted license!");
                break;
            default:
                dashboard\primary\error("Unhandled Error! Contact us if you need help");
                break;
        }
    }
    if (isset($_POST['bankey'])) {
        $userToo = ($_POST['banUserToo'] == "on") ? 1 : 0;
        $resp = misc\license\ban($_POST['bankey'], $_POST['reason'], $userToo);
        switch ($resp) {
            case 'failure':
                dashboard\primary\error("Failed to ban license!");
                break;
            case 'nope':
                misc\auditLog\send("Attempted (and failed) to ban license he doesn't own " . $_POST['bankey']);
                dashboard\primary\error("Nope, nice try!");
                break;
            case 'success':
                misc\auditLog\send("Banned license key " . $_POST['bankey']);
                dashboard\primary\success("Successfully banned license!");
                break;
            default:
                dashboard\primary\error("Unhandled Error! Contact us if you need help");
                break;
        }
    }
    if (isset($_POST['unbankey'])) {
        $resp = misc\license\unban($_POST['unbankey']);
        switch ($resp) {
            case 'failure':
                dashboard\primary\error("Failed to unban license!");
                break;
            case 'nope':
                misc\auditLog\send("Attempted (and failed) to unban license he doesn't own " . $_POST['unbankey']);
                dashboard\primary\error("Nope, nice try!");
                break;
            case 'success':
                misc\auditLog\send("Unbanned license key " . $_POST['unbankey']);
                dashboard\primary\success("Successfully unbanned license!");
                break;
            default:
                dashboard\primary\error("Unhandled Error! Contact us if you need help");
                break;
        }
    }
    ?>

<div class="p-4 bg-[#09090d] block sm:flex items-center justify-between lg:mt-1.5">
    <div class="mb-1 w-full bg-[#0f0f17] rounded-xl">
        <div class="mb-4 p-4">
            <?php require '../app/layout/breadcrumb.php'; ?>
            <h1 lang class="text-xl font-semibold text-white-900 sm:text-2xl">Reseller Licenses</h1>
            <p class="text-xs text-gray-500">Manage your licenses here.</p>
            <br>
            <div class="p-4 flex flex-col">
                <div class="overflow-x-auto">

                    <button type="button"
                        class="inline-flex text-white bg-blue-700 hover:opacity-60 focus:ring-0 font-medium rounded-lg text-sm px-5 py-2.5 mb-2 transition duration-200"
                        data-modal-toggle="create-key-modal" data-modal-target="create-key-modal">
                        <i class="lni lni-circle-plus mr-2 mt-1"></i> Create Keys
                    </button>

                    <!-- Create Key Modal -->
                    <div id="create-key-modal" tabindex="-1" aria-hidden="true"
                        class="fixed top-0 left-0 right-0 z-50 hidden w-full p-4 overflow-x-hidden overflow-y-auto md:inset-0 h-[calc(100%-1rem)] max-h-full">
                        <div class="relative w-full max-w-md max-h-full">
                            <?php
                $query = misc\mysql\query("SELECT `format`, `amount`, `lvl`, `note`, `duration`, `unit` FROM `apps` WHERE `secret` = ?",[$_SESSION['app']]);
                $row = mysqli_fetch_array($query->result);

                $format = $row['format'];
                $amt = $row['amount'];
                $lvl = $row['lvl'];
                $note = $row['note'];
                $dur = $row['duration'];
                $unit = $row['unit'];
                ?>
                            <!-- Modal content -->
                            <div class="relative bg-[#0f0f17] rounded-lg border border-[#1d4ed8] shadow">
                                <div class="px-6 py-6 lg:px-8">
                                    <h3 class="mb-4 text-xl font-medium text-white-900">Create A New Key</h3>
                                    <form class="space-y-6" method="POST">
                                        <div>
                                            <div class="relative mb-4">
                                                <input type="text" inputmode="numeric" min="1" max="100" id="amount"
                                                    name="amount"
                                                    class="block px-2.5 pb-2.5 pt-4 w-full text-sm text-white bg-transparent rounded-lg border-1 border-gray-700 appearance-none focus:ring-0  peer"
                                                    autocomplete="on" required>
                                                <label for="amount"
                                                    class="absolute text-sm text-gray-400 duration-300 transform -translate-y-4 scale-75 top-2 z-10 origin-[0] bg-[#0f0f17] px-2 peer-focus:px-2 peer-focus:text-blue-600 peer-placeholder-shown:scale-100 peer-placeholder-shown:-translate-y-1/2 peer-placeholder-shown:top-1/2 peer-focus:top-2 peer-focus:scale-75 peer-focus:-translate-y-4 left-1">License
                                                    Amount</label>
                                            </div>

                                            <div class="relative mb-4">
                                                <input type="text" maxlength="49" id="mask" name="mask"
                                                    class="block px-2.5 pb-2.5 pt-4 w-full text-sm text-white bg-transparent rounded-lg border-1 border-gray-700 appearance-none focus:ring-0  peer"
                                                    placeholder="*****-*****-*****-*****" autocomplete="on"
                                                    value="*****-*****-*****-*****" required>
                                                <label for="mask"
                                                    class="absolute text-sm text-gray-400 duration-300 transform -translate-y-4 scale-75 top-2 z-10 origin-[0] bg-[#0f0f17] px-2 peer-focus:px-2 peer-focus:text-blue-600 peer-placeholder-shown:scale-100 peer-placeholder-shown:-translate-y-1/2 peer-placeholder-shown:top-1/2 peer-focus:top-2 peer-focus:scale-75 peer-focus:-translate-y-4 left-1">License
                                                    Mask</label>
                                            </div>

                                            <div class="flex items-center mb-4">
                                                <input id="lowercaseLetters" name="lowercaseLetters" type="checkbox"
                                                    class="w-4 h-4 text-blue-600 bg-[#0f0f17] border-gray-300 rounded focus:ring-blue-500 focus:ring-2"
                                                    checked>
                                                <label for="lowercaseLetters"
                                                    class="ml-2 text-sm font-medium text-white-900">Include
                                                    Lowercase Letters</label>
                                            </div>
                                            <div class="flex items-center">
                                                <input checked id="checked-checkbox" name="capitalLetters" type="checkbox"
                                                    class="w-4 h-4 text-blue-600 bg-[#0f0f17] border-gray-300 rounded focus:ring-blue-500 focus:ring-2"
                                                    checked>
                                                <label for="checked-checkbox"
                                                    class="ml-2 text-sm font-medium text-white-900">Include
                                                    Uppercase Letters</label>
                                            </div>

                                            <div class="relative mb-4 pt-3">
                                                <select id="level" name="level"
                                                    class="bg-[#0f0f17] border border-gray-700 text-white-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5">
                                                    <?php

if ($keylevels != "N/A") {

    $keylevels = explode("|", $keylevels);



    foreach ($keylevels as $levels) {
        ?>
                                                    <option value="<?= $levels; ?>"><?= $levels; ?>
                                                    </option>
                                                    <?php
    }
} else {
    $query = misc\mysql\query("SELECT DISTINCT `level` FROM `subscriptions` WHERE `app` = ? ORDER BY `level` ASC",[$_SESSION['app']]);
    if ($query->num_rows > 0) {
        while ($row = mysqli_fetch_array($query->result)) {
    
            $queryName = misc\mysql\query("SELECT `name` FROM `subscriptions` WHERE `level` = ? AND `app` = ?",[$row["level"], $_SESSION['app']]);
            
            $name = " (";
            $count = 0;
            while ($rowSubs = mysqli_fetch_array($queryName->result)) {
                $count++;
                if($count > 1) {
                    $name .= ", " . $rowSubs["name"];
                }
                else {
                    $name .= $rowSubs["name"];
                }
            }
            $name .= ")";
    
    ?>
                                                    <option value="<?= $row["level"]; ?>">
                                                        <?= $row["level"] . $name; ?></option>
                                                    <?php
        }
    }
}
?>
                                                </select>
                                            </div>

                                            <div class="relative mb-4">
                                                <input type="text" maxlength="69" id="note" name="note"
                                                    class="block px-2.5 pb-2.5 pt-4 w-full text-sm text-white bg-transparent rounded-lg border-1 border-gray-700 appearance-none focus:ring-0  peer"
                                                    placeholder=" " autocomplete="on">
                                                <label for="note"
                                                    class="absolute text-sm text-gray-400 duration-300 transform -translate-y-4 scale-75 top-2 z-10 origin-[0] bg-[#0f0f17] px-2 peer-focus:px-2 peer-focus:text-blue-600 peer-placeholder-shown:scale-100 peer-placeholder-shown:-translate-y-1/2 peer-placeholder-shown:top-1/2 peer-focus:top-2 peer-focus:scale-75 peer-focus:-translate-y-4 left-1">License
                                                    Note</label>
                                            </div>

                                            <div class="relative mb-4">
                                                <select id="expiry" name="expiry"
                                                    class="bg-[#0f0f17] border border-gray-700 text-white-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5">
                                                    <option value="1 Day">
                                                        1 Day
                                                    </option>
                                                    <option value="1 Week">
                                                        1 Week
                                                    </option>
                                                    <option value="1 Month">
                                                        1 Month
                                                    </option>
                                                    <option value="3 Month">
                                                        3 Months
                                                    </option>
                                                    <option value="6 Month">
                                                        6 Months
                                                    </option>
                                                    <option value="1 Year">
                                                        1 Year
                                                    </option>
                                                    <option value="1 Lifetime">
                                                        Lifetime
                                                    </option>
                                                </select>
                                                <label for="expiry"
                                                    class="absolute text-sm text-gray-400 duration-300 transform -translate-y-4 scale-75 top-2 z-10 origin-[0] bg-[#0f0f17] px-2 peer-focus:px-2 peer-focus:text-blue-600 peer-placeholder-shown:scale-100 peer-placeholder-shown:-translate-y-1/2 peer-placeholder-shown:top-1/2 peer-focus:top-2 peer-focus:scale-75 peer-focus:-translate-y-4 left-1">License
                                                    Expiry Unit</label>
                                            </div>
                                        </div>
                                        <button type="submit" name="genkeys"
                                            class="w-full text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center">Generate
                                            Keys</button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- End Create Key Modal -->

                    <!-- Ban License Modal Actions-->
                    <div id="ban-key-modal" tabindex="-1"
                        class="modal fixed inset-0 flex items-center justify-center z-50 hidden">
                        <div class="relative w-full max-w-md max-h-full">
                            <div class="relative bg-[#0f0f17] border border-red-700 rounded-lg shadow">
                                <div class="p-6 text-center">
                                    <div class="flex items-center p-4 mb-4 text-sm text-red-800 border border-red-700 rounded-lg bg-[#0f0f17]"
                                        role="alert">
                                        <span class="sr-only">Info</span>
                                        <div>
                                            <span class="font-medium text-red-400">Notice! This will not ban the user
                                                (prevent them from logging in) unless you check Ban User Too</b></span>
                                        </div>
                                    </div>
                                    <h3 class="mb-5 text-lg font-normal text-gray-200">Are you sure you want to ban this
                                        license?
                                        <form method="POST">
                                            <div>
                                                <div class="relative mb-4">
                                                    <input type="text" id="reason" name="reason"
                                                        class="block px-2.5 pb-2.5 pt-4 w-full text-sm text-white bg-transparent rounded-lg border-1 border-gray-700 appearance-none focus:ring-0  peer focus:border-red-700"
                                                        placeholder=" " autocomplete="on" required>
                                                    <label for="reason"
                                                        class="absolute text-sm text-gray-400 duration-300 transform -translate-y-4 scale-75 top-2 z-10 origin-[0] bg-[#0f0f17] px-2 peer-focus:px-2 peer-focus:text-blue-600 peer-placeholder-shown:scale-100 peer-placeholder-shown:-translate-y-1/2 peer-placeholder-shown:top-1/2 peer-focus:top-2 peer-focus:scale-75 peer-focus:-translate-y-4 left-1">Ban
                                                        Reason</label>
                                                </div>
                                            </div>
                                            <div class="flex items-center mb-4">
                                                <input id="banUserToo" name="banUserToo" type="checkbox"
                                                    class="w-4 h-4 text-blue-600 bg-[#0f0f17] border-gray-300 rounded focus:ring-blue-500 focus:ring-2"
                                                    checked>
                                                <label for="banUserToo"
                                                    class="ml-2 text-sm font-medium text-white-900">Ban User
                                                    Too?</label>
                                            </div>
                                            <button name="bankey"
                                                class="text-white bg-red-600 hover:bg-red-800 focus:ring-4 focus:outline-none focus:ring-red-300 font-medium rounded-lg text-sm inline-flex items-center px-5 py-2.5 text-center mr-2 bankey">
                                                Yes, I'm sure
                                            </button>
                                            <button type="button" onclick="closeModal('ban-key-modal')"
                                                class="inline-flex text-white bg-gray-700 hover:opacity-60 focus:ring-0 font-medium rounded-lg text-sm px-5 py-2.5 mb-2 transition duration-200">No,
                                                cancel</button>
                                        </form>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- End Ban License Modal Actions-->

                    <!-- Delete Key Modal -->
                    <div id="del-key" tabindex="-1"
                        class="modal fixed inset-0 flex items-center justify-center z-50 hidden">
                        <div class="relative w-full max-w-md max-h-full">
                            <div class="relative bg-[#0f0f17] border border-red-700 rounded-lg shadow">
                                <div class="p-6 text-center">
                                    <div class="flex items-center p-4 mb-4 text-sm text-white border border-yellow-500 rounded-lg bg-[#0f0f17]"
                                        role="alert">
                                        <span class="sr-only">Info</span>
                                        <div>
                                            <span class="font-medium">Notice!</span> This will not delete the user
                                            (prevent them from logging in) unless you check Delete User Too
                                            </b>
                                        </div>
                                    </div>
                                    <h3 class="mb-5 text-lg font-normal text-gray-200">Are you sure you want to delete
                                        this key? This can not be undone.</h3>
                                    <div class="flex items-center mb-4">
                                        <input id="lowercaseLetters" name="lowercaseLetters" type="checkbox"
                                            class="w-4 h-4 text-blue-600 bg-[#0f0f17] border-gray-300 rounded focus:ring-blue-500 focus:ring-2"
                                            checked>
                                        <label for="lowercaseLetters"
                                            class="ml-2 text-sm font-medium text-white-900">Delete user too</label>
                                    </div>
                                    <form method="POST">
                                        <button name="deletekey"
                                            class="text-white bg-red-600 hover:bg-red-800 focus:ring-4 focus:outline-none focus:ring-red-300 font-medium rounded-lg text-sm inline-flex items-center px-5 py-2.5 text-center mr-2 delkey">
                                            Yes, I'm sure
                                        </button>
                                        <button type="button" onclick="closeModal('del-key')"
                                            class="inline-flex text-white bg-gray-700 hover:opacity-60 focus:ring-0 font-medium rounded-lg text-sm px-5 py-2.5 mb-2 transition duration-200">No,
                                            cancel</button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- End Delete All Unused Keys Modal -->


                    <!-- START TABLE -->
                    <div class="relative overflow-x-auto shadow-md sm:rounded-lg pt-5">
                        <table id="kt_datatable_reseller_licenses" class="w-full text-sm text-left text-white">
                            <thead>
                                <tr class="fw-bolder fs-6 text-blue-700 px-7">
                                    <th class="px-6 py-3">Key</th>
                                    <th class="px-6 py-3">Creation Date</th>
                                    <th class="px-6 py-3">Duration</th>
                                    <th class="px-6 py-3">Note</th>
                                    <th class="px-6 py-3">Used On</th>
                                    <th class="px-6 py-3">Used By</th>
                                    <th class="px-6 py-3">Status</th>
                                    <th class="px-6 py-3">Actions</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                    <p class="text-xs text-red-600">Dropdown actions in <b>RED</b> do not show a confirmation!<a
                            class="text-blue-700"> Dropdown actions in <b>BLUE</b> will show a confirmation!</a></p>
                    <script>
                    function bankey(key) {
                        var bankey = $('.bankey');
                        bankey.attr('value', key);

                        openModal('ban-key-modal');
                    }

                    function delkey(key) {
                        var delkey = $('.delkey');
                        delkey.attr('value', key);

                        openModal('del-key');
                    }

                    function copyToClipboard() {
                        const cardBodyContent = document.getElementById('multi-keys').innerText;

                        const formattedContent = cardBodyContent.replace(/<br>/g, '\n');

                        const textarea = document.createElement('textarea');
                        textarea.value = formattedContent;

                        textarea.style.position = 'fixed';
                        textarea.style.opacity = 0;

                        document.body.appendChild(textarea);

                        textarea.select();
                        document.execCommand('copy');

                        document.body.removeChild(textarea);

                        alert('Copied new licenses!');
                    }
                    </script>