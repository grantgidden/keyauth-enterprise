<?php
if ($_SESSION["role"] != 'Reseller'){
    dashboard\primary\error("Only resellers can access this page.");
    die();
}

if (isset($_POST['deleteuser'])) {
    $resp = misc\user\deleteSingular($_POST['deleteuser']);
    switch($resp) {
        case 'nope':
            misc\auditLog\send("Attempted (and failed) to delete user he doesn't own " . $_POST['deleteuser']);
            dashboard\primary\error("You don't own this user!");
            break;
        case 'success':
            misc\auditLog\send("Deleted user " . $_POST['deleteuser']);
            dashboard\primary\success("User Successfully Deleted!");
            break;
        case 'failure':
            dashboard\primary\error("Failed To Delete User!");
            break;
        default:
            dashboard\primary\error("Unhandled Error! Contact us if you need help");
            break;
    }
}
if (isset($_POST['resetuser'])) {
    $resp = misc\user\resetSingular($_POST['resetuser']);
    switch($resp) {
        case 'nope':
            misc\auditLog\send("Attempted (and failed) to reset HWID of user he doesn't own " . $_POST['resetuser']);
            dashboard\primary\error("You don't own this user!");
            break;
        case 'success':
            misc\auditLog\send("Reset HWID for user " . $_POST['resetuser']);
            dashboard\primary\success("User Successfully Reset!");
            break;
        case 'failure':
            misc\auditLog\send("Attempted (and failed) to reset a user");
            dashboard\primary\error("Failed To Reset User!");
            break;
        default:
            dashboard\primary\error("Unhandled Error! Contact us if you need help");
            break;
    }
}
if (isset($_POST['banuser'])) {
    $resp = misc\user\ban($_POST['un'], $_POST['reason']);
    switch($resp) {
        case 'nope':
            misc\auditLog\send("Attempted (and failed) to ban user he doesn't own " . $_POST['banuser']);
            dashboard\primary\error("You don't own this user!");
            break;
        case 'missing':
            dashboard\primary\error("User not Found!");
            break;
        case 'success':
            misc\auditLog\send("Banned user " . $_POST['banuser']);
            dashboard\primary\success("User Successfully Banned!");
            break;
        case 'failure':
            misc\auditLog\send("Attempted (and failed) to ban a user");
            dashboard\primary\error("Failed To Ban User!");
            break;
        default:
            dashboard\primary\error("Unhandled Error! Contact us if you need help");
            break;
    }
}

if (isset($_POST['edituser'])) {
    $un = misc\etc\sanitize($_POST['edituser']);
    $query = misc\mysql\query("SELECT * FROM `users` WHERE `username` = ? AND `app` = ? AND `owner` = ?",[$un, $_SESSION['app'], $_SESSION['username']]);
    if ($query->num_rows < 1) {
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
                            <input type="password" id="pass" name="pass"
                                class="block px-2.5 pb-2.5 pt-4 w-full text-sm text-white bg-transparent rounded-lg border-1 border-gray-700 appearance-none focus:ring-0  peer"
                                placeholder=" " autocomplete="on">
                            <label for="pass"
                                class="absolute text-sm text-gray-400 duration-300 transform -translate-y-4 scale-75 top-2 z-10 origin-[0] bg-[#0f0f17] px-2 peer-focus:px-2 peer-focus:text-blue-600 peer-placeholder-shown:scale-100 peer-placeholder-shown:-translate-y-1/2 peer-placeholder-shown:top-1/2 peer-focus:top-2 peer-focus:scale-75 peer-focus:-translate-y-4 left-1">Password:</label>
                        </div>

                        <div class="relative mb-4 pt-3">
                            <select id="sub" name="sub"
                                class="bg-[#0f0f17] border border-gray-700 text-white-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5">
                                <?php
                                                    $query = misc\mysql\query("SELECT * FROM `subs` WHERE `user` = ? AND `app` = ? AND `expiry` > ?",[$un, $_SESSION['app'], time()]);
                                                    $rows = array();
                                                    while ($r = mysqli_fetch_assoc($query->result)) {
                                                        $rows[] = $r;
                                                    }
                                                    foreach ($rows as $subrow) {
                                                        $sub = $subrow['subscription'];
                                                        $value = "[" . $subrow['subscription'] . "] - Expires: <script>document.write(convertTimestamp(" . $subrow["expiry"] . "));</script>";
                                                    ?>
                                <option value="<?= $sub; ?>"><?= $value; ?></option>
                                <?php
                                                    }
                                                    ?>
                            </select>
                            <label for="sub"
                                class="absolute text-sm text-gray-400 duration-300 transform -translate-y-4 scale-75 top-2 z-10 origin-[0] bg-[#0f0f17] px-2 peer-focus:px-2 peer-focus:text-blue-600 peer-placeholder-shown:scale-100 peer-placeholder-shown:-translate-y-1/2 peer-placeholder-shown:top-1/2 peer-focus:top-2 peer-focus:scale-75 peer-focus:-translate-y-4 left-1">User
                                Subscription</label>
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
                    <div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 10px;">

                        <button name="deletesub"
                            class="w-full text-white bg-orange-700 hover:bg-orange-800 focus:ring-4 focus:outline-none focus:ring-orange-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center"
                            value="<?= $un; ?>">Delete Subscription</button>

                        <button name="saveuser"
                            class="w-full text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center"
                            value="<?= $un; ?>">Save Changes</button>

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


if (isset($_POST['saveuser'])) {
    $un = misc\etc\sanitize($_POST['saveuser']);

    $hwid = misc\etc\sanitize($_POST['hwid']);

    $pass = misc\etc\sanitize($_POST['pass']);

    if (isset($hwid) && trim($hwid) != '') {
        $query = misc\mysql\query("SELECT `hwid` FROM `users` WHERE `username` = ? AND `app` = ? AND `owner` = ?",[$un, $_SESSION['app'], $_SESSION['username']]);
        $row = mysqli_fetch_array($query->result);
        $oldHwid = $row["hwid"];

        $oldHwid = $oldHwid .= $hwid;

        misc\mysql\query("UPDATE `users` SET `hwid` = ? WHERE `username` = ? AND `app` = ? AND `owner` = ?",[$oldHwid, $un, $_SESSION['app'], $_SESSION['username']]);
    }

    if (isset($pass) && trim($pass) != '') {
        misc\mysql\query("UPDATE `users` SET `password` = ? WHERE `username` = ? AND `app` = ? AND `owner` = ?",[password_hash($pass, PASSWORD_BCRYPT), $un, $_SESSION['app'], $_SESSION['username']]);
    }

    dashboard\primary\success("Successfully Updated User");
    misc\auditLog\send("Successfully Updated User $un");
    misc\cache\purge('KeyAuthUser:'.$_SESSION['app'].':'.$un);
}

if (isset($_POST['deletesub'])) {
    $un = misc\etc\sanitize($_POST['deletesub']);

    $query = misc\mysql\query("SELECT * FROM `users` WHERE `username` = ? AND `app` = ? AND `owner` = ?",[$un, $_SESSION['app'], $_SESSION['username']]);
    if ($query->num_rows < 1) {
        dashboard\primary\error("User not Found!");
        echo "<meta http-equiv='Refresh' Content='2'>";
        return;
    }

    $sub = misc\etc\sanitize($_POST['sub']);

    function get_string_between($string, $start, $end)
    {
        $string = ' ' . $string;
        $ini = strpos($string, $start);
        if ($ini < 1) return '';
        $ini += strlen($start);
        $len = strpos($string, $end, $ini) - $ini;
        return substr($string, $ini, $len);
    }

    $sub = get_string_between($sub, '[', ']');

    $query = misc\mysql\query("DELETE FROM `subs` WHERE `app` = ? AND `user` = ? AND `subscription` = ?",[$_SESSION['app'], $un, $sub]);
    if ($query->affected_rows != 0) {
        misc\auditLog\send("Deleted the subscription \"{$sub}\" from $un");
        dashboard\primary\success("Successfully Deleted User's Subscription");
    } else {
        dashboard\primary\error("Failed To Delete User's Subscription!");
    }
}

if (isset($_POST['delusers'])) {
    $query = misc\mysql\query("DELETE FROM `users` WHERE `app` = ? AND `owner` = ?",[$_SESSION['app'], $_SESSION['username']]);
    if ($query->affected_rows != 0) {
        misc\auditLog\send("Deleted all users");
        dashboard\primary\success("Users Successfully Deleted!");
        misc\cache\purgePattern('KeyAuthUser:' . $_SESSION['app']);
        misc\cache\purge('KeyAuthUsernames:' . $_SESSION['app']);
        misc\cache\purge('KeyAuthUsers:' . $_SESSION['app']);
    } else {
        dashboard\primary\error("Failed To Delete Users!");
    }
}

if (isset($_POST['resetall'])) {
    $query = misc\mysql\query("UPDATE `users` SET `hwid` = '' WHERE `app` = ? AND `owner` = ?",[$_SESSION['app'], $_SESSION['username']]);
    if ($query->affected_rows != 0) {
        misc\auditLog\send("Reset all users' HWIDs");
        dashboard\primary\success("Users Successfully Reset!");
        misc\cache\purgePattern('KeyAuthUser:' . $_SESSION['app']);
        misc\cache\purge('KeyAuthUsernames:' . $_SESSION['app']);
        misc\cache\purge('KeyAuthUsers:' . $_SESSION['app']);
    } else {
        dashboard\primary\error("Failed To Reset Users!");
    }
}
?>

<div class="p-4 bg-[#09090d] block sm:flex items-center justify-between lg:mt-1.5">
    <div class="mb-1 w-full bg-[#0f0f17] rounded-xl">
        <div class="mb-4 p-4">
            <?php require '../app/layout/breadcrumb.php'; ?>
            <h1 lang class="text-xl font-semibold text-white-900 sm:text-2xl">Reseller Users</h1>
            <p class="text-xs text-gray-500">Manage your users here.</p>
            <br>
            <div class="p-4 flex flex-col">
                <div class="overflow-x-auto">
                    <button
                        class="inline-flex text-white bg-red-700 hover:opacity-60 focus:ring-0 font-medium rounded-lg text-sm px-5 py-2.5 mb-2 transition duration-200"
                        data-modal-toggle="del-all-users-modal" data-modal-target="del-all-users-modal">
                        <i class="lni lni-trash-can mr-2 mt-1"></i>Delete All Users
                    </button>
                    <button
                        class="inline-flex text-white bg-red-700 hover:opacity-60 focus:ring-0 font-medium rounded-lg text-sm px-5 py-2.5 mb-2 transition duration-200"
                        data-modal-toggle="reset-all-users-hwid-modal" data-modal-target="reset-all-users-hwid-modal">
                        <i class="lni lni-reload mr-2 mt-1"></i>Reset All Users HWID
                    </button>

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

                    <!-- Ban User Modal -->
                    <div id="ban-user-modal" tabindex="-1" aria-hidden="true"
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
                                            <button onclick="closeModal('ban-user-modal')"
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
                        <table id="kt_datatable_reseller_users" class="w-full text-sm text-left white">
                            <thead>
                                <tr class="fw-bolder fs-6 text-blue-700 px-7">
                                    <th class="px-6 py-3">Username</th>
                                    <th class="px-6 py-3">HWID</th>
                                    <th class="px-6 py-3">IP</th>
                                    <th class="px-6 py-3">Creation Date</th>
                                    <th class="px-6 py-3">Last Login Date</th>
                                    <th class="px-6 py-3">Status</th>
                                    <th class="px-6 py-3">Actions</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                    <p class="text-xs text-red-600">Dropdown actions in <b>RED</b> do not show a confirmation!<a
                            class="text-blue-700"> Dropdown actions in <b>BLUE</b> will show a confirmation!</a></p>

                    <script>
                    function banuser(username) {
                        var banuser = $('.banuser');
                        banuser.attr('value', username);

                        openModal('ban-user-modal');
                    }
                    </script>

                    <!-- Include the jQuery library -->
                    