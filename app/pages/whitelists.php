<?php
if ($_SESSION['role'] == "Reseller") {
    header("location: ./?page=reseller-licenses");
    die();
}
if ($role == "Manager" && !($permissions & 4096)) {
    misc\auditLog\send("Attempted (and failed) to view whitelists.");
    dashboard\primary\error("You weren't granted permission to view this page!");
    die();
}
if (!isset($_SESSION['app'])) {
    dashboard\primary\error("Application not selected");
    die("Application not selected.");
}
if (isset($_POST['addwhite'])) {
    $resp = misc\whitelist\addWhite($_POST['ip']);
    match($resp){
        'failure' => dashboard\primary\error("Failed to add whitelist"),
        'success' => dashboard\primary\success("Successfully added whitelist"),
        default => dashboard\primary\error("Unhandled Error! Contact us if you need help")
    };
}
if (isset($_POST['deletewhite'])){
    $resp = misc\whitelist\deleteWhite($_POST['deletewhite']);
    match($resp){
        'failure' => dashboard\primary\error("Failed to delete whitelist"),
        'success' => dashboard\primary\success("Successfully deleted whitelist"),
        default => dashboard\primary\error("Unhandled Error! Contact us if you need help")
    };
}
if (isset($_POST['delallwhites'])) {
    $resp = misc\whitelist\deleteAll();
    match($resp){
        'failure' => dashboard\primary\error("Failed to delete all whitelists!"),
        'success' => dashboard\primary\success("Successfully deleted all whitelists!"),
        default => dashboard\primary\error("Unhandled Error! Contact us if you need help")
    };
}
?>

<div class="p-4 bg-[#09090d] block sm:flex items-center justify-between lg:mt-1.5">
    <div class="mb-1 w-full bg-[#0f0f17] rounded-xl">
        <div class="mb-4 p-4">
            <?php require '../app/layout/breadcrumb.php'; ?>
            <h1 class="text-xl font-semibold text-white-900 sm:text-2xl">Whitelists</h1>
            <p class="text-xs text-gray-500">Allow access from certain IPs <a
                    href="https://keyauthdocs.apidog.io/sellerapi/whitelists/add-ip-to-whitelist" target="_blank"
                    class="text-blue-600 hover:underline">Learn More</a>.</p>
            <br>
            <div class="p-4 flex flex-col">
                <div class="overflow-x-auto">
                    <!-- Whitelists Functions -->
                    <button
                        class="inline-flex text-white bg-blue-700 hover:opacity-60 focus:ring-0 font-medium rounded-lg text-sm px-5 py-2.5 mb-2 transition duration-200"
                        data-modal-toggle="add-whitelist-modal" data-modal-target="add-whitelist-modal">
                        <i class="lni lni-circle-plus mr-2 mt-1"></i>Create Whitelist
                    </button>
                    <!-- End Whitelists Functions -->

                    <br>

                    <!-- Delete Whitelists Functions -->
                    <button
                        class="inline-flex text-white bg-red-700 hover:opacity-60 focus:ring-0 font-medium rounded-lg text-sm px-5 py-2.5 mb-2 transition duration-200"
                        data-modal-toggle="delete-all-whitelists-modal" data-modal-target="delete-all-whitelists-modal">
                        <i class="lni lni-trash-can mr-2 mt-1"></i>Delete All Whitelists
                    </button>
                    <!-- End Delete Blacklists Functions -->

                    <!-- Add To Whitelist Modal -->
                    <div id="add-whitelist-modal" tabindex="-1" aria-hidden="true"
                        class="fixed top-0 left-0 right-0 z-50 hidden w-full p-4 overflow-x-hidden overflow-y-auto md:inset-0 h-[calc(100%-1rem)] max-h-full">
                        <div class="relative w-full max-w-md max-h-full">
                            <!-- Modal content -->
                            <div class="relative bg-[#0f0f17] rounded-lg border border-[#1d4ed8] shadow">
                                <div class="px-6 py-6 lg:px-8">
                                    <h3 class="mb-4 text-xl font-medium text-white-900">Add Whitelist</h3>
                                    <hr class="h-px mb-4 mt-4 bg-gray-700 border-0">
                                    <form class="space-y-6" method="POST">
                                        <div>
                                            <div class="relative mb-4">
                                                <input type="text" id="ip" name="ip"
                                                    class="block px-2.5 pb-2.5 pt-4 w-full text-sm text-white bg-transparent rounded-lg border-1 border-gray-700 appearance-none focus:ring-0  peer"
                                                    placeholder="" autocomplete="on" required>
                                                <label for="ip"
                                                    class="absolute text-sm text-gray-400 duration-300 transform -translate-y-4 scale-75 top-2 z-10 origin-[0] bg-[#0f0f17] px-2 peer-focus:px-2 peer-focus:text-blue-600 peer-placeholder-shown:scale-100 peer-placeholder-shown:-translate-y-1/2 peer-placeholder-shown:top-1/2 peer-focus:top-2 peer-focus:scale-75 peer-focus:-translate-y-4 left-1">IP
                                                </label>
                                            </div>
                                        </div>
                                        <button type="submit" name="addwhite"
                                            class="w-full text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center">Whitelist</button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- End Add Whitelist Modal -->
         
                    <!-- Delete All Whitelists Modal -->
                    <div id="delete-all-whitelists-modal" tabindex="-1"
                        class="fixed top-0 left-0 right-0 z-50 hidden p-4 overflow-x-hidden overflow-y-auto md:inset-0 h-[calc(100%-1rem)] max-h-full">
                        <div class="relative w-full max-w-md max-h-full">
                            <div class="relative bg-[#0f0f17] border border-red-700 rounded-lg shadow">
                                <div class="p-6 text-center">
                                    <div class="flex items-center p-4 mb-4 text-sm text-white border border-yellow-500 rounded-lg bg-[#0f0f17]"
                                        role="alert">
                                        <span class="sr-only">Info</span>
                                        <div>
                                            <span class="font-medium">Notice!</span> You're about to delete all of your
                                            Whitelists. Are you sure you want to continue?
                                            </b>
                                        </div>
                                    </div>
                                    <h3 class="mb-5 text-lg font-normal text-gray-200">Are you sure you want to delete
                                        all of your Whitelists? This can not be undone.</h3>
                                    <form method="POST">
                                        <button data-modal-hide="delete-all-whitelists-modal" name="delallwhites"
                                            class="text-white bg-red-600 hover:bg-red-800 focus:ring-4 focus:outline-none focus:ring-red-300 font-medium rounded-lg text-sm inline-flex items-center px-5 py-2.5 text-center mr-2">
                                            Yes, I'm sure
                                        </button>
                                        <button data-modal-hide="delete-all-whitelists-modal" type="button"
                                            class="inline-flex text-white bg-gray-700 hover:opacity-60 focus:ring-0 font-medium rounded-lg text-sm px-5 py-2.5 mb-2 transition duration-200">No,
                                            cancel</button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- End Delete All Whitelists Modal -->

                    <!-- START TABLE -->
                    <div class="relative overflow-x-auto shadow-md sm:rounded-lg pt-5">
                        <table id="kt_datatable_whitelists"
                            class="w-full text-sm text-left text-white">
                            <thead>
                                <tr class="fw-bolder fs-6 text-blue-700 px-7">
                                    <th class="px-6 py-3">IP</th>
                                    <th class="px-6 py-3">Actions</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                    <p class="text-xs text-red-600">Dropdown actions in <b>RED</b> do not show a confirmation!<a class="text-blue-700"> Dropdown actions in <b>BLUE</b> will show a confirmation!</a></p>
                    