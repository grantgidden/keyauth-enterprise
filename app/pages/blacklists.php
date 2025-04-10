<?php
if ($_SESSION['role'] == "Reseller") {
    header("location: ./?page=reseller-licenses");
    die();
}
if ($role == "Manager" && !($permissions & 512)) {
    misc\auditLog\send("Attempted (and failed) to view blacklists.");
    dashboard\primary\error("You weren't granted permission to view this page!");
    die();
}
if (!isset($_SESSION['app'])) {
    dashboard\primary\error("Application not selected");
    die("Application not selected.");
}
if (isset($_POST['addblack'])) {
    $resp = misc\blacklist\add($_POST['blackdata'], $_POST['blacktype']);
    match($resp){
        'invalid' => dashboard\primary\error("Invalid blacklist type!"),
        'failure' => dashboard\primary\error("Failed to add blacklists!"),
        'success' => dashboard\primary\success("Successfully added blacklists!"),
        default => dashboard\primary\error("Unhandled Error! Contact us if you need help")
    };
}
if (isset($_POST['delblacks'])) {
    $resp = misc\blacklist\deleteAll();
    match($resp){
        'failure' => dashboard\primary\error("Failed to delete all whitelists!"),
        'success' => dashboard\primary\success("Successfully deleted all whitelists!"),
        default => dashboard\primary\error("Unhandled Error! Contact us if you need help")
    };
}

if (isset($_POST['deleteblack'])) {
    $resp = misc\blacklist\deleteSingular($_POST['deleteblack'], $_POST['type']);
    match($resp){
        'invalid' => dashboard\primary\error("Invalid blacklist type!"),
        'failure' => dashboard\primary\error("Failed to delete blacklist!"),
        'success' => dashboard\primary\success("Successfully deleted blacklist!"),
        default => dashboard\primary\error("Unhandled Error! Contact us if you need help")
    };
}

if (isset($_POST["selected"]) && isset($_POST["action"])) {
    if ($_POST["action"] == "delete") {
        $data = json_decode($_POST["selected"], true);

        $array = array_map('urldecode', $data);
        
        foreach ($array as $item) {
            $decoded = json_decode($item, true);
            $resp = misc\blacklist\deleteSingular($decoded['value'], $decoded['type']);
            match($resp){
                'invalid' => dashboard\primary\error("Invalid blacklist type!"),
                'failure' => dashboard\primary\error("Failed to delete blacklist!"),
                'success' => dashboard\primary\success("Successfully deleted blacklist!"),
                default => dashboard\primary\error("Unhandled Error! Contact us if you need help")
            };
        }
    }else {
        dashboard\primary\error("Unhandled Error! Contact us if you need help - Action:BLPxDB");
    }
}
?>

<div class="p-4 bg-[#09090d] block sm:flex items-center justify-between lg:mt-1.5">
    <div class="mb-1 w-full bg-[#0f0f17] rounded-xl">
        <div class="mb-4 p-4">
            <?php require '../app/layout/breadcrumb.php'; ?>
            <h1 class="text-xl font-semibold text-white-900 sm:text-2xl">Blacklists</h1>
            <p class="text-xs text-gray-500">Block access from certain IPs/HWIDs <a
                    href="https://keyauthdocs.apidog.io/api/features/check-blacklist" target="_blank"
                    class="text-blue-600 hover:underline">Learn More</a>.</p>
            <br>
            <div class="p-4 flex flex-col">
                <div class="overflow-x-auto">
                    <!-- Blacklists Functions -->
                    <button
                        class="inline-flex text-white bg-blue-700 hover:opacity-60 focus:ring-0 font-medium rounded-lg text-sm px-5 py-2.5 mb-2 transition duration-200"
                        data-modal-toggle="add-blacklist-modal" data-modal-target="add-blacklist-modal">
                        <i class="lni lni-circle-plus mr-2 mt-1"></i>Create Blacklist
                    </button>
                    <!-- End Blacklists Functions -->

                    <br>

                    <!-- Delete Blacklists Functions -->
                    <button
                        class="inline-flex text-white bg-red-700 hover:opacity-60 focus:ring-0 font-medium rounded-lg text-sm px-5 py-2.5 mb-2 transition duration-200"
                        data-modal-toggle="delete-all-blacklists-modal" data-modal-target="delete-all-blacklists-modal">
                        <i class="lni lni-trash-can mr-2 mt-1"></i>Delete All Blacklists
                    </button>

                    <button id="dropdownselection" data-dropdown-toggle="blacklistdropdown" 
                        class="inline-flex text-white bg-red-700 hover:opacity-60 focus:ring-0 font-medium rounded-lg text-sm px-5 py-2.5 mb-2 transition duration-200 hidden"
                        type="button">Selection Options
                    </button>

                    <div id="blacklistdropdown" class="z-10 hidden bg-[#09090d] divide-y divide-gray-100 rounded-lg shadow w-44">
                        <ul class="py-2 text-sm text-white">
                            <li>
                                <form method="post">
                                    <input type="hidden" name="action" value="delete">
                                    <input type="hidden" id="selected" name="selected">
                                    <a href="#" onclick="this.parentNode.submit();" class="block px-4 py-2 focus:bold ml-2 hover:text-red-700">Delete selected</a>
                                </form>
                            </li>
                        </ul>
                    </div>
                    <!-- End Delete Blacklists Functions -->

                    <!-- Add To Blacklist Modal -->
                    <div id="add-blacklist-modal" tabindex="-1" aria-hidden="true"
                        class="fixed top-0 left-0 right-0 z-50 hidden w-full p-4 overflow-x-hidden overflow-y-auto md:inset-0 h-[calc(100%-1rem)] max-h-full">
                        <div class="relative w-full max-w-md max-h-full">
                            <!-- Modal content -->
                            <div class="relative bg-[#0f0f17] rounded-lg border border-[#1d4ed8] shadow">
                                <div class="px-6 py-6 lg:px-8">
                                    <h3 class="mb-4 text-xl font-medium text-white-900">Add Blacklist</h3>
                                    <hr class="h-px mb-4 mt-4 bg-gray-700 border-0">
                                    <form class="space-y-6" method="POST">
                                        <div>

                                            <div class="relative mb-4  ">
                                                <select id="blacktype" name="blacktype"
                                                    class="bg-[#0f0f17] border border-gray-700 text-white-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5">
                                                    <option value="IP Address" selected>IP Address</option>
                                                    <option value="Hardware ID">Hardware ID</option>
                                                    <option value="region">Region/State</option>
                                                    <option value="country">Country Code</option>
                                                    <option value="asn">ASN Number</option>
                                                </select>
                                                <label for="blacktype"
                                                    class="absolute text-sm text-gray-400 duration-300 transform -translate-y-4 scale-75 top-2 z-10 origin-[0] bg-[#0f0f17] px-2 peer-focus:px-2 peer-focus:text-blue-600 peer-placeholder-shown:scale-100 peer-placeholder-shown:-translate-y-1/2 peer-placeholder-shown:top-1/2 peer-focus:top-2 peer-focus:scale-75 peer-focus:-translate-y-4 left-1">Blacklist
                                                    Type</label>
                                            </div>

                                            <div class="relative mb-4">
                                                <input type="text" id="blackdata" name="blackdata"
                                                    class="block px-2.5 pb-2.5 pt-4 w-full text-sm text-white bg-transparent rounded-lg border-1 border-gray-700 appearance-none focus:ring-0  peer"
                                                    placeholder=" " autocomplete="on" required>
                                                <label for="blackdata"
                                                    class="absolute text-sm text-gray-400 duration-300 transform -translate-y-4 scale-75 top-2 z-10 origin-[0] bg-[#0f0f17] px-2 peer-focus:px-2 peer-focus:text-blue-600 peer-placeholder-shown:scale-100 peer-placeholder-shown:-translate-y-1/2 peer-placeholder-shown:top-1/2 peer-focus:top-2 peer-focus:scale-75 peer-focus:-translate-y-4 left-1">
                                                    Enter blacklist data
                                                </label>
                                            </div>

                                        </div>
                                        <button type="submit" name="addblack"
                                            class="w-full text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center">Blacklist</button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- End Add Blacklist Modal -->

                    <!-- Delete All Blacklists Modal -->
                    <div id="delete-all-blacklists-modal" tabindex="-1"
                        class="fixed top-0 left-0 right-0 z-50 hidden p-4 overflow-x-hidden overflow-y-auto md:inset-0 h-[calc(100%-1rem)] max-h-full">
                        <div class="relative w-full max-w-md max-h-full">
                            <div class="relative bg-[#0f0f17] border border-red-700 rounded-lg shadow">
                                <div class="p-6 text-center">
                                    <div class="flex items-center p-4 mb-4 text-sm text-white border border-yellow-500 rounded-lg bg-[#0f0f17]"
                                        role="alert">
                                        <span class="sr-only">Info</span>
                                        <div>
                                            <span class="font-medium">Notice!</span> You're about to delete all of your
                                            Blacklists. Are you sure you want to continue?
                                            </b>
                                        </div>
                                    </div>
                                    <h3 class="mb-5 text-lg font-normal text-gray-200">Are you sure you want to delete
                                        all of your Blacklists? This can not be undone.</h3>
                                    <form method="POST">
                                        <button data-modal-hide="delete-all-blacklists-modal" name="delblacks"
                                            class="text-white bg-red-600 hover:bg-red-800 focus:ring-4 focus:outline-none focus:ring-red-300 font-medium rounded-lg text-sm inline-flex items-center px-5 py-2.5 text-center mr-2">
                                            Yes, I'm sure
                                        </button>
                                        <button data-modal-hide="delete-all-blacklists-modal" type="button"
                                            class="inline-flex text-white bg-gray-700 hover:opacity-60 focus:ring-0 font-medium rounded-lg text-sm px-5 py-2.5 mb-2 transition duration-200">No,
                                            cancel</button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- End Delete All Blacklists Modal -->

                    <!-- Dynamically change placeholder for blacklist, so users have examples of what data to enter -->
                    <script>
                        $(document).ready(function() {
                            $('#blacktype').on('change', function() {
                                var selectedValue = $(this).val();
                                var placeholderText = '';
                            
                                switch (selectedValue) {
                                    case 'IP Address':
                                        placeholderText = 'IP Address (example: 142.250.64.206)';
                                        break;
                                    case 'Hardware ID':
                                        placeholderText = 'Hardware ID (example: S-1-5-21-1085031214-1563985344-725345543)';
                                        break;
                                    case 'region':
                                        placeholderText = 'Region/State (example: ENG for England)';
                                        break;
                                    case 'country':
                                        placeholderText = 'Country Code (example: IT for Italy)';
                                        break;
                                    case 'asn':
                                        placeholderText = 'ASN Number (example: 15169 for Google LLC)';
                                        break;
                                    default:
                                        placeholderText = ''; // Default placeholder if needed
                                }
                            
                                $('#blackdata').attr('placeholder', placeholderText);
                            });
                        
                            // Trigger change event on page load to set the initial placeholder
                            $('#blacktype').trigger('change');
                        });
                    </script>

                    <!-- START TABLE -->
                    <div class="relative overflow-x-auto shadow-md sm:rounded-lg pt-5">
                        <table id="kt_datatable_blacklists"
                            class="w-full text-sm text-left text-white">
                            <thead>
                                <tr class="fw-bolder fs-6 text-blue-700 px-7">
                                    <th class="px-6 py-3">Selection</th>
                                    <th class="px-6 py-3">Blacklist Data</th>
                                    <th class="px-6 py-3">Blacklist Type</th>
                                    <th class="px-6 py-3">Actions</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                    <p class="text-xs text-red-600">Dropdown actions in <b>RED</b> do not show a confirmation!<a class="text-blue-700"> Dropdown actions in <b>BLUE</b> will show a confirmation!</a></p>
                    