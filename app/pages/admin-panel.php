<?php
if (!$admin) {
    ob_clean();
    http_response_code(403);
    require '../404_error.html';
    die();
}

date_default_timezone_set('UTC');

if (!$twofactor) {
    dashboard\primary\error("Admin accounts must have 2FA enabled.");
    die("Admin accounts must have 2FA enabled");
}

if (isset($_POST['searchemail'])) {
    $email = misc\etc\sanitize($_POST['email']);
    header("Location:./?page=admin-panel&email=" . $email);
    die();
}


if (isset($_POST['searchusername'])) {
    $un = misc\etc\sanitize($_POST['un']);
    header("Location: ./?page=admin-panel&username=" . urlencode($un));
    die();
}

if (isset($_POST['searchownerid'])){
    $ownerid = misc\etc\sanitize($_POST['ownerid']);
    header("Location: ./?page=admin-panel&ownerid=" . urlencode($ownerid));
    die();
}

if (isset($_POST['banacc'])) {
    if(misc\cache\rateLimit("KeyAuthAdminBans:" . $_SESSION['username'], 1, 3600, 3)) {
        misc\mysql\query("UPDATE `accounts` SET `admin` = 0 WHERE `username` = ?", [$_SESSION['username']]);
        dashboard\primary\wh_log($adminwebhook, " the admin `{$username}` has been removed for banning too many people", $adminwebhookun, "41db10", "<@1268915723330392096>");
        dashboard\primary\error("You banned too many accounts too frequently, your admin is removed");
        return;
    }

    $un = misc\etc\sanitize($_POST['banacc']);
    $reason = misc\etc\sanitize($_POST['reason']);

    $query = misc\mysql\query("SELECT `admin`, `banned` FROM `accounts` WHERE `username` = ?", [$un]);
    if ($query->num_rows < 1) {
        echo '<meta http-equiv="refresh" content="2">';
        dashboard\primary\error("Account not found!");
        return;
    }
    if (!is_null(mysqli_fetch_array($query->result)['banned'])) {
        echo '<meta http-equiv="refresh" content="2">';
        dashboard\primary\error("User is already banned!");
        return;
    }
    if (mysqli_fetch_array($query->result)['admin']) {
        echo '<meta http-equiv="refresh" content="2">';
        dashboard\primary\error("User is an admin!");
        return;
    }

    misc\mysql\query("UPDATE `accounts` SET `banned` = ? WHERE `username` = ?", [$reason, $un]); // set account to banned
    misc\mysql\query("UPDATE `apps` SET `banned` = '1' WHERE `owner` = ?", [$un]); // ban all apps owned by account

    dashboard\primary\wh_log($adminwebhook, "Admin `{$username}` has banned user `{$un}` for reason `{$reason}`", $adminwebhookun, "41db10", "No ping");

    dashboard\primary\success("Account Banned!");

    $query = misc\mysql\query("SELECT `name`, `ownerid` FROM `apps` WHERE `owner` = ?", [$un]);
    $rows = array();
    while ($r = mysqli_fetch_assoc($query->result)) {
        $rows[] = $r;
    }
    foreach ($rows as $row) {
        misc\cache\purge('KeyAuthApp:' . $row['name'] . ':' . $row['ownerid']);
    }

    $query = misc\mysql\query("SELECT `role` FROM `accounts` WHERE `username` = ?", [$un]);
    $row = mysqli_fetch_array($query->result);
    if ($row['role'] == "seller") {
        $query = misc\mysql\query("SELECT `sellerkey` FROM `apps` WHERE `owner` = ?", [$un]);
        $rows = array();
        while ($r = mysqli_fetch_assoc($query->result)) {
            $rows[] = $r;
        }
        foreach ($rows as $row) {
            misc\cache\purge('KeyAuthAppSeller:' . $row['sellerkey']);
        }
    }
}

if (isset($_POST['unbanacc'])) {
    $un = misc\etc\sanitize($_POST['unbanacc']);

    $query = misc\mysql\query("SELECT `admin`,`banned` FROM `accounts` WHERE `username` = ?", [$un]);
    if ($query->num_rows < 1) {
        echo '<meta http-equiv="refresh" content="2">';
        dashboard\primary\error("Account not found!");
        return;
    }
    if (is_null(mysqli_fetch_array($query->result)['banned'])) {
        echo '<meta http-equiv="refresh" content="2">';
        dashboard\primary\error("User isn't banned!");
        return;
    }
    if (mysqli_fetch_array($query->result)['admin']) {
        echo '<meta http-equiv="refresh" content="2">';
        dashboard\primary\error("User is an admin!");
        return;
    }

    misc\mysql\query("UPDATE `accounts` SET `banned` = NULL WHERE `username` = ?", [$un]); // set account to not banned
    misc\mysql\query("UPDATE `apps` SET `banned` = '0' WHERE `owner` = ?", [$un]); // unban all apps owned by account

    dashboard\primary\wh_log($adminwebhook, "Admin `{$username}` has unbanned user `{$un}`", $adminwebhookun, "41db10", "No ping");

    dashboard\primary\success("Account Unbanned!");

    $query = misc\mysql\query("SELECT `name`, `ownerid` FROM `apps` WHERE `owner` = ?", [$un]);
    $rows = array();
    while ($r = mysqli_fetch_assoc($query->result)) {
        $rows[] = $r;
    }
    foreach ($rows as $row) {
        misc\cache\purge('KeyAuthApp:' . $row['name'] . ':' . $row['ownerid']);
    }

    $query = misc\mysql\query("SELECT `role` FROM `accounts` WHERE `username` = ?", [$un]);
    $row = mysqli_fetch_array($query->result);
    if ($row['role'] == "seller") {
        $query = misc\mysql\query("SELECT `sellerkey` FROM `apps` WHERE `owner` = ?", [$un]);
        $rows = array();
        while ($r = mysqli_fetch_assoc($query->result)) {
            $rows[] = $r;
        }
        foreach ($rows as $row) {
            misc\cache\purge('KeyAuthAppSeller:' . $row['sellerkey']);
        }
    }
}

if (isset($_POST['purgeRedis'])) 
{
    $redisKey = misc\etc\sanitize($_POST['redisKey']);
    if(str_contains(strtolower($redisKey), "keyauthadminbans")) {
        echo '<meta http-equiv="refresh" content="2">';
        dashboard\primary\error("You're not allowed to purge this key");
        return;
    }
    if(str_contains(strtolower($redisKey), "keyauthadminedits")) {
        echo '<meta http-equiv="refresh" content="2">';
        dashboard\primary\error("You're not allowed to purge this key");
        return;
    }
    misc\cache\purge($redisKey);
    dashboard\primary\success("Purged redis key");
    dashboard\primary\wh_log($adminwebhook, "Admin `{$username}` has purged a redis key for `{$redisKey}`", $adminwebhookun, "41db10", "No ping");
}

if (isset($_POST['purgeRedisPattern'])) {
    $redisKey = misc\etc\sanitize($_POST['redisKeyPattern']);
    if(str_contains(strtolower($redisKey), "keyauthadminbans")) {
        echo '<meta http-equiv="refresh" content="2">';
        dashboard\primary\error("You're not allowed to purge this key");
        return;
    }
    if(str_contains(strtolower($redisKey), "keyauthadminedits")) {
        echo '<meta http-equiv="refresh" content="2">';
        dashboard\primary\error("You're not allowed to purge this key");
        return;
    }
    misc\cache\purgePattern($redisKey);
    dashboard\primary\success("Purged redis key(s)");
    dashboard\primary\wh_log($adminwebhook, "Admin `{$username}` has purged a redis pattern for `{$redisKey}`", $adminwebhookun, "41db10", "No ping");
}

if(isset($_POST['searchOrder'])) 
{
    $orderID = misc\etc\sanitize($_POST['orderID']);
    $query = misc\mysql\query("SELECT `username` FROM `orders` WHERE `orderID` = ?", [$orderID]);
    if($query->num_rows < 1) {
        dashboard\primary\error("Order not found!");
    } else {
        $row = mysqli_fetch_array($query->result);
    }
}

if (isset($_POST['saveacc'])) {
    if(misc\cache\rateLimit("KeyAuthAdminEdits:" . $_SESSION['username'], 1, 3600, 3)) {
        misc\mysql\query("UPDATE `accounts` SET `admin` = 0 WHERE `username` = ?", [$_SESSION['username']]);
        dashboard\primary\wh_log($adminwebhook, "the admin `{$username}` has been removed for editing too many people", $adminwebhookun, "db1010", "<@1268915723330392096> ");
        dashboard\primary\error("You edited too many accounts too frequently, your admin is removed");
        return;
    }

    $un = misc\etc\sanitize($_POST['saveacc']);
    $email = misc\etc\sanitize($_POST['email']);
    $role = misc\etc\sanitize($_POST['role']);
    $secKey = misc\etc\sanitize($_POST['secKey']);
    $totp = misc\etc\sanitize($_POST['totp']);
    $staff = misc\etc\sanitize($_POST['staff']);
    $alertMsg = misc\etc\sanitize($_POST['alert']);
    $formBanned = misc\etc\sanitize($_POST['formBanned']);

    $query = misc\mysql\query("SELECT `role`,`admin` FROM `accounts` WHERE `username` = ?", [$un]);
    $row = mysqli_fetch_array($query->result);

    if ($row['admin']) {
        echo '<meta http-equiv="refresh" content="2">';
        dashboard\primary\error("User is an admin!");
        return;
    }

    if ($row['role'] == "seller" || $role == "seller") { // purge cache for SellerAPI check if user was seller and getting downgraded, or was another plan and getting upgraded to seller.
        misc\cache\purge('KeyAuthSellerCheck:' . $un);
    }

    switch ($role) {
        case 'seller':
            $expires = time() + (int) $_POST['expiry'];
            break;
        case 'developer':
            $expires = time() + (int) $_POST['expiry'];
            break;
        case 'tester':
            $expires = NULL;
            break;
        case 'reseller':
            $expires = NULL;
            break;
        case 'manager':
            $expires = NULL;
            break;
        default:
            dashboard\primary\error("Invalid role!");
            echo "<meta http-equiv='Refresh' Content='2'>";
            return;
    }

    misc\mysql\query("UPDATE `accounts` SET `role` = ?, `securityKey` = ?, `twofactor` = ?, `alert` = ? WHERE `username` = ?", [$role, $secKey, $totp, $alertMsg, $un]);
    if (strlen($email) != 40) {
        misc\mysql\query("UPDATE `accounts` SET `email` = SHA1(?) WHERE `username` = ?", [$email, $un]);
    }

    if ($row['role'] != $role) {
        misc\mysql\query("UPDATE `accounts` SET `expires` = NULLIF(?, '') WHERE `username` = ?", [$expires, $un]);
    }

    if ($row['staff'] != $staff){
        misc\mysql\query("UPDATE `accounts` SET `staff` = NULLIF(?, '') WHERE `username` = ?", [$staff, $un]);
    }

    if ($row['formBanned'] != $formBanned){
        misc\mysql\query("UPDATE `accounts` SET `formBanned` = NULLIF(?, '') WHERE `username` = ?", [$formBanned, $un]);
    }

    if (isset($_POST['resetSecWordsCB'])){
        misc\mysql\query("UPDATE `accounts` SET `secWords` = NULL WHERE `username` = ?", [$un]);
    }

    dashboard\primary\wh_log($adminwebhook, "Admin `{$username}` has updated user `{$un}` \n email to: `{$email}`, \n role to: `{$role}`, \n 2FA status to: `{$totp}`, \n staff status to: `{$staff}`, \n form banned status to: `{$formBanned}`, \n alert to: `{$alertMsg}`", $adminwebhookun, "41db10", "No ping");

    dashboard\primary\success("Updated Account!");
}

if (isset($_POST['editacc'])) {
    $un = misc\etc\sanitize($_POST['editacc']);

    $query = misc\mysql\query("SELECT * FROM `accounts` WHERE `username` = ?", [$un]);
    $row = mysqli_fetch_array($query->result);
    $role = $row['role'];
    $secKey = $row['securityKey'];
    $totp = $row['twofactor'];
    $formBanned = $row['formBanned'];
    $alertMsg = $row['alert'];
    $secWords = $row['secWords'];
    $staff = $row['staff'];
    $admin = $row['admin'];
    if ($admin) {
        echo '<meta http-equiv="refresh" content="2">';
        dashboard\primary\error("User is an admin!");
        return;
    }
?>
<!-- Edit Acc Modal -->
<div id="edit-acc" aria-hidden="true"
    class="fixed grid place-items-center h-screen bg-black bg-opacity-60 z-50 p-4 overflow-x-hidden overflow-y-auto md:inset-0 h-[calc(100%-1rem)] max-h-full">
    <div class="relative w-full max-w-md max-h-full">
        <!-- Modal content -->
        <div class="relative bg-[#0f0f17] rounded-lg border border-[#1d4ed8] shadow">
            <div class="px-6 py-6 lg:px-8">
                <h3 class="mb-4 text-xl font-medium text-white-900">Admin - Edit User</h3>
                <form class="space-y-6" method="POST">
                    <div>
                        <div class="relative mb-4">
                            <input type="text" id="email" name="email"
                                class="block px-2.5 pb-2.5 pt-4 w-full text-sm text-white bg-transparent rounded-lg border-1 border-gray-700 appearance-none focus:ring-0  peer"
                                placeholder=" " autocomplete="on" value="<?= $row['email']; ?>" required>
                            <label for="email"
                                class="absolute text-sm text-gray-400 duration-300 transform -translate-y-4 scale-75 top-2 z-10 origin-[0] bg-[#0f0f17] px-2 peer-focus:px-2 peer-focus:text-blue-600 peer-placeholder-shown:scale-100 peer-placeholder-shown:-translate-y-1/2 peer-placeholder-shown:top-1/2 peer-focus:top-2 peer-focus:scale-75 peer-focus:-translate-y-4 left-1">Email</label>
                        </div>

                        <div class="relative mb-4">
                            <select id="role" name="role"
                                class="bg-[#0f0f17] border border-gray-700 text-white-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5">
                                <option value="seller" <?= $role == 'seller' ? 'selected="selected"' : ''; ?>>
                                    Seller
                                </option>
                                <option value="developer" <?= $role == 'developer' ? 'selected="selected"' : ''; ?>>
                                    Developer
                                </option>
                                <option value="tester" <?= $role == 'tester' ? 'selected="selected"' : ''; ?>>
                                    Tester
                                </option>
                                <option value="reseller" <?= $role == 'reseller' ? 'selected="selected"' : ''; ?>>
                                    Reseller
                                </option>
                                <option value="manager" <?= $role == 'manager' ? 'selected="selected"' : ''; ?>>
                                    Manager
                                </option>
                            </select>
                            <label for="role"
                                class="absolute text-sm text-gray-400 duration-300 transform -translate-y-4 scale-75 top-2 z-10 origin-[0] bg-[#0f0f17] px-2 peer-focus:px-2 peer-focus:text-blue-600 peer-placeholder-shown:scale-100 peer-placeholder-shown:-translate-y-1/2 peer-placeholder-shown:top-1/2 peer-focus:top-2 peer-focus:scale-75 peer-focus:-translate-y-4 left-1">Role</label>
                        </div>

                        <div class="relative mb-4">
                            <select id="expiry" name="expiry"
                                class="bg-[#0f0f17] border border-gray-700 text-white-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5">
                                <option value="31556926">
                                    Yearly
                                </option>
                                <option value="2629743">
                                    Monthly
                                </option>
                            </select>
                            <label for="expiry"
                                class="absolute text-sm text-gray-400 duration-300 transform -translate-y-4 scale-75 top-2 z-10 origin-[0] bg-[#0f0f17] px-2 peer-focus:px-2 peer-focus:text-blue-600 peer-placeholder-shown:scale-100 peer-placeholder-shown:-translate-y-1/2 peer-placeholder-shown:top-1/2 peer-focus:top-2 peer-focus:scale-75 peer-focus:-translate-y-4 left-1">Expiration</label>
                        </div>

                        <div class="relative mb-4">
                            <select id="secKey" name="secKey"
                                class="bg-[#0f0f17] border border-gray-700 text-white-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5">
                                <option value="0" <?= $secKey == 0 ? 'selected="selected"' : ''; ?>>
                                    false
                                </option>
                                <option value="1" <?= $secKey == 1 ? 'selected="selected"' : ''; ?>>
                                    true
                                </option>
                            </select>
                            <label for="secKey"
                                class="absolute text-sm text-gray-400 duration-300 transform -translate-y-4 scale-75 top-2 z-10 origin-[0] bg-[#0f0f17] px-2 peer-focus:px-2 peer-focus:text-blue-600 peer-placeholder-shown:scale-100 peer-placeholder-shown:-translate-y-1/2 peer-placeholder-shown:top-1/2 peer-focus:top-2 peer-focus:scale-75 peer-focus:-translate-y-4 left-1">Security Key</label>
                        </div>

                        <div class="relative mb-4">
                            <select id="totp" name="totp"
                                class="bg-[#0f0f17] border border-gray-700 text-white-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5">
                                <option value="0" <?= $totp == 0 ? 'selected="selected"' : ''; ?>>
                                    false
                                </option>
                                <option value="1" <?= $totp == 1 ? 'selected="selected"' : ''; ?>>
                                    true
                                </option>
                            </select>
                            <label for="totp"
                                class="absolute text-sm text-gray-400 duration-300 transform -translate-y-4 scale-75 top-2 z-10 origin-[0] bg-[#0f0f17] px-2 peer-focus:px-2 peer-focus:text-blue-600 peer-placeholder-shown:scale-100 peer-placeholder-shown:-translate-y-1/2 peer-placeholder-shown:top-1/2 peer-focus:top-2 peer-focus:scale-75 peer-focus:-translate-y-4 left-1">2FA
                                Status</label>
                        </div>

                        <div class="relative mb-4">
                            <select id="staff" name="staff"
                                class="bg-[#0f0f17] border border-gray-700 text-white-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5">
                                <option value="0" <?= $staff == 0 ? 'selected="selected"' : ''; ?>>
                                    false
                                </option>
                                <option value="1" <?= $staff == 1 ? 'selected="selected"' : ''; ?>>
                                    true
                                </option>
                            </select>
                            <label for="staff"
                                class="absolute text-sm text-gray-400 duration-300 transform -translate-y-4 scale-75 top-2 z-10 origin-[0] bg-[#0f0f17] px-2 peer-focus:px-2 peer-focus:text-blue-600 peer-placeholder-shown:scale-100 peer-placeholder-shown:-translate-y-1/2 peer-placeholder-shown:top-1/2 peer-focus:top-2 peer-focus:scale-75 peer-focus:-translate-y-4 left-1">Staff
                                Status</label>
                        </div>

                        <div class="relative mb-4">
                            <select id="formBanned" name="formBanned"
                                class="bg-[#0f0f17] border border-gray-700 text-white-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5"
                                data-popover-target="formBanned-popover">
                                <option value="0" <?= $formBanned == 0 ? 'selected="selected"' : ''; ?>>
                                    false
                                </option>
                                <option value="1" <?= $formBanned == 1 ? 'selected="selected"' : ''; ?>>
                                    true
                                </option>
                            </select>
                            <label for="formBanned"
                                class="absolute text-sm text-gray-400 duration-300 transform -translate-y-4 scale-75 top-2 z-10 origin-[0] bg-[#0f0f17] px-2 peer-focus:px-2 peer-focus:text-blue-600 peer-placeholder-shown:scale-100 peer-placeholder-shown:-translate-y-1/2 peer-placeholder-shown:top-1/2 peer-focus:top-2 peer-focus:scale-75 peer-focus:-translate-y-4 left-1">Form
                                Banned</label>

                            <?php dashboard\primary\popover("formBanned-popover", "Form Banned", "Used to ban the user from submitting any forms (suggestion, bug, staff, etc)."); ?>
                        </div>

                        <div class="relative mb-4">
                            <input type="text" id="alert" name="alert"
                                class="block px-2.5 pb-2.5 pt-4 w-full text-sm text-white bg-transparent rounded-lg border-1 border-gray-700 appearance-none focus:ring-0  peer"
                                placeholder=" " autocomplete="on" value="<?= $row['alert']; ?>"
                                data-popover-target="alert-popover">
                            <label for="alert"
                                class="absolute text-sm text-gray-400 duration-300 transform -translate-y-4 scale-75 top-2 z-10 origin-[0] bg-[#0f0f17] px-2 peer-focus:px-2 peer-focus:text-blue-600 peer-placeholder-shown:scale-100 peer-placeholder-shown:-translate-y-1/2 peer-placeholder-shown:top-1/2 peer-focus:top-2 peer-focus:scale-75 peer-focus:-translate-y-4 left-1">Alert</label>

                            <?php dashboard\primary\popover("alert-popover", "Alert", "Used to send a message to the users manage-apps page."); ?>
                        </div>

                        <div class="relative mb-4">
                            <input datepicker datepicker-autohide type="datetime-local"
                                    name="expiryType" class="bg-[#0f0f17] border border-gray-700 text-white-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full pl-10 p-2.5 "
                                    placeholder="Subscription Expiry" 
                                    value="<?= date('Y-m-d H:i:s', $row['expires']);?>" required readonly>
                        </div>

                        <div class="relative mb-4">
                            <p class="text-xs text-white-500"><b>Security Words:</b> <?= $secWords ?? "N/A"; ?>   
                        </div>

                        <?php if ($secWords != null) {?>
                        <div class="flex items-center mb-4">
                            <input id="resetSecWordsCB" name="resetSecWordsCB" type="checkbox"
                                    class="w-4 h-4 text-blue-600 bg-[#0f0f17] border-gray-300 rounded focus:ring-blue-500 focus:ring-2"
                                    unchecked data-popover-target="resetSecWordsCB-popover">
                            <label for="resetSecWordsCB"
                                    class="ml-2 text-sm font-medium text-white-900">
                                    Reset Security Words?</label>

                                    <?php dashboard\primary\popover("resetSecWordsCB-popover", "Reset Security Words", "If a user forgot their security words, or gave them to you to verify their account, they must be reset. "); ?>
                        </div>
                        <?php } ?>
                    </div>
                    <button name="saveacc" value="<?= $un; ?>"
                        class="w-full text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center">Update
                        Account</button>
                    <button onClick="window.location.href=window.location.href"
                        class="w-full text-white bg-red-700 hover:bg-red-800 focus:ring-4 focus:outline-none focus:ring-red-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center">Cancel</button>
                </form>
            </div>
        </div>
    </div>
</div>
<!-- End Edit Acc Modal -->
<?php }
?>

<div class="p-4 bg-[#09090d] block sm:flex items-center justify-between lg:mt-1.5">
    <div class="mb-1 w-full bg-[#0f0f17] rounded-xl">
        <div class="mb-4 p-4">
            <?php require '../app/layout/breadcrumb.php'; ?>
            <h1 class="text-xl font-semibold text-white-900 sm:text-2xl">Admin Panel</h1>
            <p class="text-xs text-gray-500">Welcome <a class="text-blue-700"><?= $username; ?></a> To The Admin Panel!
            </p>
            <br>
            <div class="p-4 flex flex-col">
                <div class="overflow-x-auto">
                    <!-- Admin Functions -->
                    <button
                        class="inline-flex text-white bg-blue-700 hover:opacity-60 focus:ring-0 font-medium rounded-lg text-sm px-5 py-2.5 mb-2 transition duration-200"
                        data-modal-toggle="search-by-email-modal" data-modal-target="search-by-email-modal">
                        <i class="lni lni-search mr-2 mt-1"></i>Search By Email
                    </button>

                    <button
                        class="inline-flex text-white bg-blue-700 hover:opacity-60 focus:ring-0 font-medium rounded-lg text-sm px-5 py-2.5 mb-2 transition duration-200"
                        data-modal-toggle="search-by-username-modal" data-modal-target="search-by-username-modal">
                        <i class="lni lni-search mr-2 mt-1"></i>Search By Username
                    </button>

                    <button
                        class="inline-flex text-white bg-blue-700 hover:opacity-60 focus:ring-0 font-medium rounded-lg text-sm px-5 py-2.5 mb-2 transition duration-200"
                        data-modal-toggle="search-by-ownerid-modal" data-modal-target="search-by-ownerid-modal">
                        <i class="lni lni-search mr-2 mt-1"></i>Search By Owner ID
                    </button>

                    <button
                        class="inline-flex text-white bg-blue-700 hover:opacity-60 focus:ring-0 font-medium rounded-lg text-sm px-5 py-2.5 mb-2 transition duration-200"
                        data-modal-toggle="search-by-orderid-modal" data-modal-target="search-by-orderid-modal">
                        <i class="lni lni-search mr-2 mt-1"></i>Search By Order ID
                    </button>
                    <!-- End Admin Functions -->

                    <br>

                    <!-- Admin Redis Functions -->
                    <button
                        class="inline-flex text-white bg-orange-500 hover:opacity-60 focus:ring-0 font-medium rounded-lg text-sm px-5 py-2.5 mb-2 transition duration-200"
                        data-modal-toggle="purge-redis-key-modal" data-modal-target="purge-redis-key-modal">
                        <i class="lni lni-eraser mr-2 mt-1"></i>Purge Redis Key
                    </button>

                    <button
                        class="inline-flex text-white bg-orange-500 hover:opacity-60 focus:ring-0 font-medium rounded-lg text-sm px-5 py-2.5 mb-2 transition duration-200"
                        data-modal-toggle="purge-redis-pattern-modal" data-modal-target="purge-redis-pattern-modal">
                        <i class="lni lni-eraser mr-2 mt-1"></i>Purge Redis Pattern
                    </button>
                    <!-- End Admin Redis Functions -->

                    <!-- Search By Email Modal -->
                    <div id="search-by-email-modal" tabindex="-1" aria-hidden="true"
                        class="fixed top-0 left-0 right-0 z-50 hidden w-full p-4 overflow-x-hidden overflow-y-auto md:inset-0 h-[calc(100%-1rem)] max-h-full">
                        <div class="relative w-full max-w-md max-h-full">
                            <!-- Modal content -->
                            <div class="relative bg-[#0f0f17] rounded-lg border border-blue-700 shadow">
                                <div class="px-6 py-6 lg:px-8">
                                    <h3 class="mb-4 text-xl font-medium text-white-900">Search By Email</h3>
                                    <form class="space-y-6" method="POST">
                                        <div>
                                            <div class="relative mb-4">
                                                <input type="text" id="email" name="email"
                                                    class="block px-2.5 pb-2.5 pt-4 w-full text-sm text-white bg-transparent rounded-lg border-1 border-gray-600 appearance-none focus:ring-0  peer"
                                                    placeholder=" " autocomplete="on" required="">
                                                <label for="email"
                                                    class="absolute text-sm text-gray-400 duration-300 transform -translate-y-4 scale-75 top-2 z-10 origin-[0] bg-[#0f0f17] px-2 peer-focus:px-2 peer-focus:text-blue-600 peer-placeholder-shown:scale-100 peer-placeholder-shown:-translate-y-1/2 peer-placeholder-shown:top-1/2 peer-focus:top-2 peer-focus:scale-75 peer-focus:-translate-y-4 left-1">Users
                                                    Email</label>
                                            </div>
                                        </div>
                                        <button name="searchemail"
                                            class="w-full text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center">Search</button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- End Search By Email Modal -->

                    <!-- Search By Username Modal -->
                    <div id="search-by-username-modal" tabindex="-1" aria-hidden="true"
                        class="fixed top-0 left-0 right-0 z-50 hidden w-full p-4 overflow-x-hidden overflow-y-auto md:inset-0 h-[calc(100%-1rem)] max-h-full">
                        <div class="relative w-full max-w-md max-h-full">
                            <!-- Modal content -->
                            <div class="relative bg-[#0f0f17] rounded-lg border border-blue-700 shadow">
                                <div class="px-6 py-6 lg:px-8">
                                    <h3 class="mb-4 text-xl font-medium text-white-900">Search By Username</h3>
                                    <form class="space-y-6" method="POST">
                                        <div>
                                            <div class="relative mb-4">
                                                <input type="text" id="un" name="un"
                                                    class="block px-2.5 pb-2.5 pt-4 w-full text-sm text-white bg-transparent rounded-lg border-1 border-gray-600 appearance-none focus:ring-0  peer"
                                                    placeholder=" " autocomplete="on" required="">
                                                <label for="un"
                                                    class="absolute text-sm text-gray-400 duration-300 transform -translate-y-4 scale-75 top-2 z-10 origin-[0] bg-[#0f0f17] px-2 peer-focus:px-2 peer-focus:text-blue-600 peer-placeholder-shown:scale-100 peer-placeholder-shown:-translate-y-1/2 peer-placeholder-shown:top-1/2 peer-focus:top-2 peer-focus:scale-75 peer-focus:-translate-y-4 left-1">Users
                                                    Username</label>
                                            </div>
                                        </div>
                                        <button name="searchusername"
                                            class="w-full text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center">Search</button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- End Search By Username Modal -->

                    <!-- Search By Ownerid Modal -->
                    <div id="search-by-ownerid-modal" tabindex="-1" aria-hidden="true"
                        class="fixed top-0 left-0 right-0 z-50 hidden w-full p-4 overflow-x-hidden overflow-y-auto md:inset-0 h-[calc(100%-1rem)] max-h-full">
                        <div class="relative w-full max-w-md max-h-full">
                            <!-- Modal content -->
                            <div class="relative bg-[#0f0f17] rounded-lg border border-blue-700 shadow">
                                <div class="px-6 py-6 lg:px-8">
                                    <h3 class="mb-4 text-xl font-medium text-white-900">Search By Ownerid</h3>
                                    <form class="space-y-6" method="POST">
                                        <div>
                                            <div class="relative mb-4">
                                                <input type="text" id="ownerid" name="ownerid"
                                                    class="block px-2.5 pb-2.5 pt-4 w-full text-sm text-white bg-transparent rounded-lg border-1 border-gray-600 appearance-none focus:ring-0  peer"
                                                    placeholder=" " autocomplete="on" required>
                                                <label for="ownerid"
                                                    class="absolute text-sm text-gray-400 duration-300 transform -translate-y-4 scale-75 top-2 z-10 origin-[0] bg-[#0f0f17] px-2 peer-focus:px-2 peer-focus:text-blue-600 peer-placeholder-shown:scale-100 peer-placeholder-shown:-translate-y-1/2 peer-placeholder-shown:top-1/2 peer-focus:top-2 peer-focus:scale-75 peer-focus:-translate-y-4 left-1">Users
                                                    Ownerid</label>
                                            </div>
                                        </div>
                                        <button name="searchownerid"
                                            class="w-full text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center">Search</button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- End Search By Ownerid Modal -->

                    <!-- Search By OrderID Modal -->
                    <div id="search-by-orderid-modal" tabindex="-1" aria-hidden="true"
                        class="fixed top-0 left-0 right-0 z-50 hidden w-full p-4 overflow-x-hidden overflow-y-auto md:inset-0 h-[calc(100%-1rem)] max-h-full">
                        <div class="relative w-full max-w-md max-h-full">
                            <!-- Modal content -->
                            <div class="relative bg-[#0f0f17] rounded-lg border border-blue-700 shadow">
                                <div class="px-6 py-6 lg:px-8">
                                    <h3 class="mb-4 text-xl font-medium text-white-900">Search By Order ID</h3>
                                    <form class="space-y-6" method="POST">
                                        <div>
                                            <div class="relative mb-4">
                                                <input type="text" id="orderID" name="orderID"
                                                    class="block px-2.5 pb-2.5 pt-4 w-full text-sm text-white bg-transparent rounded-lg border-1 border-gray-600 appearance-none focus:ring-0 peer"
                                                    placeholder=" " autocomplete="on" required>
                                                <label for="orderID"
                                                    class="absolute text-sm text-gray-400 duration-300 transform -translate-y-4 scale-75 top-2 z-10 origin-[0] bg-[#0f0f17] px-2 peer-focus:px-2 peer-focus:text-blue-600 peer-placeholder-shown:scale-100 peer-placeholder-shown:-translate-y-1/2 peer-placeholder-shown:top-1/2 peer-focus:top-2 peer-focus:scale-75 peer-focus:-translate-y-4 left-1">Users
                                                    OrderID</label>
                                            </div>
                                        </div>
                                        <button name="searchOrder"
                                            class="w-full text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center">Search</button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- End Search By OrderID Modal -->

                    <!-- Purge Redis Key Modal -->
                    <div id="purge-redis-key-modal" tabindex="-1" aria-hidden="true"
                        class="fixed top-0 left-0 right-0 z-50 hidden w-full p-4 overflow-x-hidden overflow-y-auto md:inset-0 h-[calc(100%-1rem)] max-h-full">
                        <div class="relative w-full max-w-md max-h-full">
                            <!-- Modal content -->
                            <div class="relative bg-[#0f0f17] rounded-lg border border-orange-700 shadow">
                                <div class="px-6 py-6 lg:px-8">
                                    <h3 class="mb-4 text-xl font-medium text-white-900">Purge Redis Key</h3>
                                    <form class="space-y-6" method="POST">
                                        <div>
                                            <div class="relative mb-4">
                                                <input type="text" id="un" name="purgeRedis"
                                                    class="block px-2.5 pb-2.5 pt-4 w-full text-sm text-white bg-transparent rounded-lg border-1 border-gray-600 appearance-none focus:ring-0  peer focus:border-orange-700"
                                                    placeholder=" " autocomplete="on" required="">
                                                <label for="purgeRedis"
                                                    class="absolute text-sm text-gray-400 duration-300 transform -translate-y-4 scale-75 top-2 z-10 origin-[0] bg-[#0f0f17] px-2 peer-focus:px-2 peer-focus:text-orange-600 peer-placeholder-shown:scale-100 peer-placeholder-shown:-translate-y-1/2 peer-placeholder-shown:top-1/2 peer-focus:top-2 peer-focus:scale-75 peer-focus:-translate-y-4 left-1">Redis
                                                    Key</label>
                                            </div>
                                        </div>
                                        <button name="redisKey"
                                            class="w-full text-white bg-orange-700 hover:bg-orange-800 focus:ring-4 focus:outline-none focus:ring-orange-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center">Purge</button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- End Purge Redis Key Modal -->

                    <!-- Purge Redis Key Pattern Modal -->
                    <div id="purge-redis-pattern-modal" tabindex="-1" aria-hidden="true"
                        class="fixed top-0 left-0 right-0 z-50 hidden w-full p-4 overflow-x-hidden overflow-y-auto md:inset-0 h-[calc(100%-1rem)] max-h-full">
                        <div class="relative w-full max-w-md max-h-full">
                            <!-- Modal content -->
                            <div class="relative bg-[#0f0f17] rounded-lg border border-orange-700 shadow">
                                <div class="px-6 py-6 lg:px-8">
                                    <h3 class="mb-4 text-xl font-medium text-white-900">Purge Redis Key Pattern</h3>
                                    <form class="space-y-6" method="POST">
                                        <div>
                                            <div class="relative mb-4">
                                                <input type="text" id="un" name="purgeRedis"
                                                    class="block px-2.5 pb-2.5 pt-4 w-full text-sm text-white bg-transparent rounded-lg border-1 border-gray-600 appearance-none focus:ring-0  peer focus:border-orange-700"
                                                    placeholder=" " autocomplete="on" required="">
                                                <label for="purgeRedis"
                                                    class="absolute text-sm text-gray-400 duration-300 transform -translate-y-4 scale-75 top-2 z-10 origin-[0] bg-[#0f0f17] px-2 peer-focus:px-2 peer-focus:text-orange-600 peer-placeholder-shown:scale-100 peer-placeholder-shown:-translate-y-1/2 peer-placeholder-shown:top-1/2 peer-focus:top-2 peer-focus:scale-75 peer-focus:-translate-y-4 left-1">Redis
                                                    Key Pattern</label>
                                            </div>
                                        </div>
                                        <button name="redisKey"
                                            class="w-full text-white bg-orange-700 hover:bg-orange-800 focus:ring-4 focus:outline-none focus:ring-orange-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center">Purge</button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- End Purge Redis Key Pattern Modal -->

                    <!-- Ban User Modal -->
                    <div id="ban-user-modal" tabindex="-1" aria-hidden="true"
                        class="fixed top-0 left-0 right-0 z-50 hidden w-full p-4 overflow-x-hidden overflow-y-auto md:inset-0 h-[calc(100%-1rem)] max-h-full">
                        <div class="relative w-full max-w-md max-h-full">
                            <!-- Modal content -->
                            <div class="relative bg-[#0f0f17] rounded-lg border border-blue-700 shadow">
                                <div class="px-6 py-6 lg:px-8">
                                    <h3 class="mb-4 text-xl font-medium text-white-900">Ban User - Reason</h3>
                                    <form class="space-y-6" method="POST">
                                        <div>
                                            <div class="relative mb-4">
                                                <input type="text" id="reason" name="reason"
                                                    class="block px-2.5 pb-2.5 pt-4 w-full text-sm text-white bg-transparent rounded-lg border-1 border-gray-600 appearance-none focus:ring-0  peer"
                                                    placeholder=" " autocomplete="on" required>
                                                <label for="reason"
                                                    class="absolute text-sm text-gray-400 duration-300 transform -translate-y-4 scale-75 top-2 z-10 origin-[0] bg-[#0f0f17] px-2 peer-focus:px-2 peer-focus:text-blue-600 peer-placeholder-shown:scale-100 peer-placeholder-shown:-translate-y-1/2 peer-placeholder-shown:top-1/2 peer-focus:top-2 peer-focus:scale-75 peer-focus:-translate-y-4 left-1">Ban
                                                    Reason</label>
                                            </div>
                                        </div>
                                        <button name="banacc"
                                            class="w-full text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center banacc">Ban
                                            User</button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- End Ban User Modal -->

                    <!-- START TABLE -->
                    <div class="relative overflow-x-auto shadow-md sm:rounded-lg pt-5">
                        <table id="kt_datatable_admin" class="w-full text-sm text-left text-white">
                            <thead>
                                <tr class="fw-bolder fs-6 text-blue-700 px-7">
                                    <th class="px-6 py-3">Username</th>
                                    <th class="px-6 py-3">Owner ID</th>
                                    <th class="px-6 py-3">Email</th>
                                    <th class="px-6 py-3">Role</th>
                                    <th class="px-6 py-3">Staff</th>
                                    <th class="px-6 py-3">Ban Status</th>
                                    <th class="py-6 py-3">Security Key</th>
                                    <th class="px-6 py-3">2FA Status</th>
                                    <th class="px-6 py-3">Register IP</th>
                                    <th class="px-6 py-3">Last IP</th>
                                    <th class="px-6 py-3">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                    $un = misc\etc\sanitize($_GET['username']);
                                    $email = misc\etc\sanitize($_GET['email']);
                                    $ownerid = misc\etc\sanitize($_GET['ownerid']);
                                    $query = misc\mysql\query("SELECT * FROM `accounts` WHERE (`username` = ? OR `email` = SHA1(?)) OR `ownerid` = ? AND `admin` = 0", [$un, $email, $ownerid]);
                                    $rows = array();
                                    while ($r = mysqli_fetch_assoc($query->result)){
                                        $rows[] = $r;
                                    }
                                    foreach ($rows as $row){
                                        $un = $row['username'];
                                        $ban = $row['banned'] == NULL ? 'False' : 'True';
                                        $secKey = (($row['securityKey'] ? 1 : 0) ? 'True' : 'False');
                                        $totp = (($row['twofactor'] ? 1 : 0) ? 'True' : 'False');
                                        $staff = (($row['staff'] ? 1 : 0) ? 'True' : 'False');
                                    ?>
                                <tr>
                                    <td><?= $un; ?></td>
                                    <td><?= $row["ownerid"]; ?></td>
                                    <td><?= $row["email"]; ?></td>
                                    <td><?= $row["role"]; ?></td>
                                    <td><?= $staff; ?></td>
                                    <td><?= $ban; ?></td>
                                    <td><?= $secKey; ?></td>
                                    <td><?= $totp; ?></td>
                                    <td><a class="blur-sm hover:blur-none"><?= $row["registrationip"] ?? "None"; ?></a>
                                    </td>
                                    <td><a class="blur-sm hover:blur-none"><?= $row["lastip"] ?? "None"; ?></a></td>
                                    <form method="POST">
                                        <td>

                                            <div x-data="{ open: false }" class="z-0">
                                                <button x-on:click="open = true"
                                                    class="flex items-center border border-gray-700 rounded-lg focus:opacity-60 text-white focus:text-white font-semibold rounded focus:outline-none focus:shadow-inner py-2 px-4"
                                                    type="button">
                                                    <span class="mr-1">Actions</span>
                                                    <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg"
                                                        viewBox="0 0 20 20" style="margin-top:3px">
                                                        <path
                                                            d="M9.293 12.95l.707.707L15.657 8l-1.414-1.414L10 10.828 5.757 6.586 4.343 8z" />
                                                    </svg>
                                                </button>
                                                <ul x-show="open" x-on:click.away="open = false"
                                                    class="bg-[#09090d] text-white rounded shadow-lg absolute py-2 mt-1"
                                                    style="min-width:15rem">
                                                    <li>
                                                        <button type="button" data-modal-target="ban-user-modal" class="block hover:opacity-60 whitespace-no-wrap py-2 px-4 hover:text-blue-700"
                                                            data-modal-toggle="ban-user-modal"
                                                            onclick="banacc('<?= $un; ?>')">
                                                            Ban
                                                        </button>
                                                    </li>
                                                    <li>
                                                        <button name="unbanacc"
                                                            class="block hover:opacity-60 whitespace-no-wrap py-2 px-4 hover:text-blue-700"
                                                            value="<?= $un; ?>">
                                                            Unban
                                                        </button>
                                                    </li>
                                                    <li>
                                                        <button name="editacc"
                                                            class="block hover:opacity-60 whitespace-no-wrap py-2 px-4 hover:text-blue-700"
                                                            value="<?= $un; ?>">
                                                            Edit
                                                        </button>
                                                    </li>
                                                </ul>
                                            </div>
                                        </td>
                                </tr>
                                </form>
                                <?php
                                    }
                                    ?>

                            </tbody>
                        </table>

                        <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

                        <script>
                        function banacc(un) {
                            var banacc = $('.banacc');
                            banacc.attr('value', un);
                        }
                    </script>
                        