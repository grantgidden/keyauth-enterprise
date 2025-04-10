<?php
if (!$staff) 
{
    ob_clean();
    http_response_code(403);
    require '../404_error.html';
    die();
}

if (!$twofactor) 
{
    dashboard\primary\error("Staff accounts mut have 2FA enabled.");
    die("Staff accounts must have 2FA enabled");
}

 if (isset($_POST['changeEmail'])) {
    $orderId = misc\etc\sanitize($_POST['orderID']);
    $newEmail = misc\etc\sanitize($_POST['email']);

    if (!is_null($orderId)) {
        $query = misc\mysql\query("SELECT `username` FROM `orders` WHERE `orderID` = ?", [$orderId]);
        if ($query->num_rows < 1) {
            dashboard\primary\error("Order ID not found. Should be from shoppy, sellapp, sellix, or orders.keyauth.cc. NOT PayPal etc. Contact us https://chatting.page/gjqkqirygnfslvce2m5eycuo7tfxrhxl");
            return;
        }
        $username = mysqli_fetch_array($query->result)['username'];

        $query = misc\mysql\query("SELECT `username` FROM `accounts` WHERE `email` = SHA(?)", [$newEmail]);
        if ($query->num_rows < 1) {
            $query = misc\mysql\query("UPDATE `accounts` SET `email` = SHA1(?) WHERE `username` = ?", [$newEmail, $username]);
            if ($query->affected_rows > 0) {
                dashboard\primary\success("Successfully changed email.");
                return;
            } else {
                dashboard\primary\error("Failed to change email. Perhaps the user changed their username after purchasing. Contact us https://chatting.page/gjqkqirygnfslvce2m5eycuo7tfxrhxl");
                return;
            }
        } else {
            dashboard\primary\error("Email already used by username " . mysqli_fetch_array($query->result)['username'] . "");
            return;
        }
    }

    $query = misc\mysql\query("SELECT 1 FROM `accounts` WHERE `email` = SHA1(?)", [$newEmail]);
    if ($query->num_rows > 0) {
        dashboard\primary\error("Email already used! You must specify another email");
        return;
    }
}
if (isset($_POST['checkExistence'])){
    $username = misc\etc\sanitize($_POST['username']);

    $query = misc\mysql\query("SELECT `username` FROM `accounts` WHERE `username` = ?",[$username]);
    if ($query->num_rows > 0){
        dashboard\primary\success("User exists");
    } else{
        dashboard\primary\error("User does not exist!");
    }
}
?>

<div class="p-4 bg-[#09090d] block sm:flex items-center justify-between lg:mt-1.5">
    <div class="mb-1 w-full bg-[#0f0f17] rounded-xl">
        <div class="mb-4 p-4">
            <?php require '../app/layout/breadcrumb.php'; ?>
            <h1 class="text-xl font-semibold text-white-900 sm:text-2xl ">Staff Panel</h1>
            <p class="text-xs text-gray-500">Welcome Staff! If you need help, or don't understand something, please
                contact an Admin. Thank You!</p>
            <br>
            <div class="p-4 flex flex-col">
                <div class="overflow-x-auto">
                    <!-- Staff Functions -->
                    <button
                        class="inline-flex text-white bg-blue-700 hover:opacity-60 focus:ring-0 font-medium rounded-lg text-sm px-5 py-2.5 mb-2 transition duration-200"
                        data-modal-toggle="check-existence-modal" data-modal-target="check-existence-modal">
                        <i class="lni lni-user mr-2 mt-1"></i>Check Existence
                    </button>

                    <button
                        class="inline-flex text-white bg-orange-500 hover:opacity-60 focus:ring-0 font-medium rounded-lg text-sm px-5 py-2.5 mb-2 transition duration-200"
                        data-modal-toggle="change-users-email-modal" data-modal-target="change-users-email-modal">
                        <i class="lni lni-reload mr-2 mt-1"></i>Change Users Email
                    </button>
                    <!-- End Staff Functions -->

                    <!-- Search By OrderID Modal -->
                    <div id="change-users-email-modal" tabindex="-1" aria-hidden="true"
                        class="fixed top-0 left-0 right-0 z-50 hidden w-full p-4 overflow-x-hidden overflow-y-auto md:inset-0 h-[calc(100%-1rem)] max-h-full">
                        <div class="relative w-full max-w-md max-h-full">
                            <!-- Modal content -->
                            <div class="relative bg-[#0f0f17] rounded-lg border border-orange-700 shadow">
                                <div class="px-6 py-6 lg:px-8">
                                    <h3 class="mb-4 text-xl font-medium text-white-900">Change Users Email</h3>
                                    <form class="space-y-6" method="POST">
                                        <div>
                                            <div class="relative mb-4">
                                                <input type="text" id="un" name="orderID"
                                                    class="block px-2.5 pb-2.5 pt-4 w-full text-sm text-white bg-transparent rounded-lg border- border-gray-600 appearance-none focus:ring-0 peer focus:border-orange-700"
                                                    placeholder=" " autocomplete="on" required>
                                                <label for="orderID"
                                                    class="absolute text-sm text-gray-400 duration-300 transform -translate-y-4 scale-75 top-2 z-10 origin-[0] bg-[#0f0f17] px-2 peer-focus:px-2 peer-focus:text-orange-600 peer-placeholder-shown:scale-100 peer-placeholder-shown:-translate-y-1/2 peer-placeholder-shown:top-1/2 peer-focus:top-2 peer-focus:scale-75 peer-focus:-translate-y-4 left-1">Users
                                                    OrderID</label>
                                            </div>
                                            <div class="relative mb-4">
                                                <input type="text" id="email" name="email"
                                                    class="block px-2.5 pb-2.5 pt-4 w-full text-sm text-white bg-transparent rounded-lg border- border-gray-600 appearance-none focus:ring-0 peer focus:border-orange-700"
                                                    placeholder=" " autocomplete="on" required>
                                                <label for="email"
                                                    class="absolute text-sm text-gray-400 duration-300 transform -translate-y-4 scale-75 top-2 z-10 origin-[0] bg-[#0f0f17] px-2 peer-focus:px-2 peer-focus:text-orange-600 peer-placeholder-shown:scale-100 peer-placeholder-shown:-translate-y-1/2 peer-placeholder-shown:top-1/2 peer-focus:top-2 peer-focus:scale-75 peer-focus:-translate-y-4 left-1">New Email</label>
                                            </div>
                                        </div>
                                        <button name="changeEmail"
                                            class="w-full text-white bg-orange-700 hover:bg-orange-800 focus:ring-4 focus:outline-none focus:ring-orange-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center">Change Email</button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- End Search By OrderID Modal -->

                    <!-- Check Existence Modal -->
                    <div id="check-existence-modal" tabindex="-1" aria-hidden="true"
                        class="fixed top-0 left-0 right-0 z-50 hidden w-full p-4 overflow-x-hidden overflow-y-auto md:inset-0 h-[calc(100%-1rem)] max-h-full">
                        <div class="relative w-full max-w-md max-h-full">
                            <!-- Modal content -->
                            <div class="relative bg-[#0f0f17] rounded-lg border border-[#1d4ed8] shadow">
                                <div class="px-6 py-6 lg:px-8">
                                    <h3 class="mb-4 text-xl font-medium text-white-900">Check User Existence</h3>
                                    <form class="space-y-6" method="POST">
                                        <div>
                                            <div class="relative mb-4">
                                                <input type="text" id="username" name="username"
                                                    class="block px-2.5 pb-2.5 pt-4 w-full text-sm text-white bg-transparent rounded-lg border-1 border-gray-600 appearance-none focus:ring-0  peer"
                                                    placeholder=" " autocomplete="off" required="">
                                                <label for="username"
                                                    class="absolute text-sm text-gray-400 duration-300 transform -translate-y-4 scale-75 top-2 z-10 origin-[0] bg-[#0f0f17] px-2 peer-focus:px-2 peer-focus:text-blue-600 peer-placeholder-shown:scale-100 peer-placeholder-shown:-translate-y-1/2 peer-placeholder-shown:top-1/2 peer-focus:top-2 peer-focus:scale-75 peer-focus:-translate-y-4 left-1">Username</label>
                                            </div>
                                        </div>
                                        <button name="checkExistence"
                                            class="w-full text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center">Check Existence</button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- End Check Existance Modal -->