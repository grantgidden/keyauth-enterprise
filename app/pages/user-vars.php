<?php
if ($_SESSION['role'] == "Reseller") {
    header("location: ./?page=reseller-licenses");
    die();
}
if ($role == "Manager" && !($permissions & 2)) {
    misc\auditLog\send("Attempted (and failed) to view user variables.");
    dashboard\primary\error("You weren't granted permission to view this page.");
    die();
}
if(!isset($_SESSION['app'])) {
    dashboard\primary\error("Application not selected");
    die("Application not selected.");
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

if (isset($_POST['delvars'])) {
    $query = misc\mysql\query("DELETE FROM `uservars` WHERE `app` = ?",[$_SESSION['app']]);
    if ($query->affected_rows > 0) {
        dashboard\primary\success("Successfully deleted all variables");
    } else {
        dashboard\primary\error("Failed to delete all variables");
    }
}

if (isset($_POST['deletevar'])) {
    $varId = misc\etc\sanitize($_POST['deletevar']);

    $query = misc\mysql\query("DELETE FROM `uservars` WHERE `app` = ? AND `id` = ?", [$_SESSION['app'], $varId]);

    if ($query->affected_rows > 0) {
        dashboard\primary\success("Successfully deleted variable");
    }
    else {
        dashboard\primary\error("Failed to delete variable");
    }
}

// edit modal
if (isset($_POST['editvar'])) {
    $varId = misc\etc\sanitize($_POST['editvar']);

    $query = misc\mysql\query("SELECT * FROM `uservars` WHERE `id` = ? AND `app` = ?",[$varId, $_SESSION['app']]);
    if ($query->num_rows < 1) {
        dashboard\primary\error("Variable not Found!");
        echo "<meta http-equiv='Refresh' Content='2'>";
        return;
    }

    $row = mysqli_fetch_array($query->result);

    $data = $row["data"];
    $readOnly = $row["readOnly"];

    echo  '
    <div id="edit-variable-modal" tabindex="-1" aria-hidden="true"
        class="fixed grid place-items-center h-screen bg-black bg-opacity-60 z-50 p-4 overflow-x-hidden overflow-y-auto md:inset-0 h-[calc(100%-1rem)] max-h-full">
        <div class="relative w-full max-w-md max-h-full">
            <!-- Modal content -->
            <div class="relative bg-[#0f0f17] rounded-lg border border-[#1d4ed8] shadow">
                <div class="px-6 py-6 lg:px-8">
                    <h3 class="mb-4 text-xl font-medium text-white-900">Edit Variable</h3>
                    <form class="space-y-6" method="POST">
                        <div>

                        <div class="relative mb-4">
                            <label for="data" name="data"
                                class="absolute text-sm text-gray-400 duration-300 transform -translate-y-4 scale-75 top-2 z-10 origin-[0] bg-[#0f0f17] px-2 peer-focus:px-2 peer-focus:text-blue-600 peer-placeholder-shown:scale-100 peer-placeholder-shown:-translate-y-1/2 peer-placeholder-shown:top-1/2 peer-focus:top-2 peer-focus:scale-75 peer-focus:-translate-y-4 left-1">
                                Variable Data</label>
                            <textarea id="data" name="data" rows="4"
                                class="block p-2.5 w-full text-sm text-white-900 bg-[#0f0f17] rounded-lg border border-gray-700 focus:ring-blue-500 focus:border-blue-500"
                                placeholder="" maxlength="1000" required>' . $data . '</textarea>
                        </div>

                        </div>
                        <div class="flex items-center mb-4">
                        <input id="readOnly" name="readOnly" type="checkbox"
                            class="w-4 h-4 text-blue-600 bg-[#0f0f17] border-gray-300 rounded focus:ring-blue-500 focus:ring-2"
                            ' . ($readOnly ? "checked" : "") . '>
                        <label for="readOnly"
                            class="ml-2 text-sm font-medium text-white-900">Read only</label>
                    </div>

                        <button name="savevar" value="' . $varId . '"
                            class="w-full text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center">Save
                            Changes</button>
                        <button
                            class="w-full text-white bg-red-700 hover:bg-red-800 focus:ring-4 focus:outline-none focus:ring-red-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center" onClick="window.location.href=window.location.href">Cancel
                            </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <!-- End Edit Var Modal -->';
}

if (isset($_POST['savevar'])) {
    
    $readOnly = misc\etc\sanitize($_POST['readOnly']) == NULL ? 0 : 1;
    $varData = misc\etc\sanitize($_POST['data']);
    $varId = misc\etc\sanitize($_POST['savevar']);

    if(strlen($varData) > 500) {
        dashboard\primary\error("Variable too long! Must be 500 characters or less");
        echo "<meta http-equiv='Refresh' Content='2'>";
        return;
    }

    misc\mysql\query("UPDATE `uservars` SET `data` = ?, `readOnly` = ? WHERE `id` = ? AND `app` = ?", [$varData, $readOnly, $varId, $_SESSION['app']]);

    if ($query->affected_rows > 0) {
        dashboard\primary\success("Successfully edited variable");
    }
    else {
        dashboard\primary\error("Failed to edit variable");
    }
}
?>

<div class="p-4 bg-[#09090d] block sm:flex items-center justify-between lg:mt-1.5">
    <div class="mb-1 w-full bg-[#0f0f17] rounded-xl">
        <div class="mb-4 p-4">
            <?php require '../app/layout/breadcrumb.php'; ?>
            <h1 class="text-xl font-semibold text-white-900 sm:text-2xl ">User Variables</h1>
            <p class="text-xs text-gray-500">Pass, assign, obtain data uniquely for each user. <a
                    href="https://keyauthdocs.apidog.io/sellerapi/users/retrive-user-variable-data" target="_blank"
                    class="text-blue-600  hover:underline">Learn More</a>.</p>
            <br>
            <div class="overflow-x-auto">

                <!-- Alert Box -->
                <div id="alert-4" class="flex items-center p-4 mb-4 text-yellow-800 rounded-lg bg-[#09090d]"
                    role="alert">
                    <svg class="flex-shrink-0 w-4 h-4" aria-hidden="true" xmlns="http://www.w3.org/2000/svg"
                        fill="currentColor" viewBox="0 0 20 20">
                        <path
                            d="M10 .5a9.5 9.5 0 1 0 9.5 9.5A9.51 9.51 0 0 0 10 .5ZM9.5 4a1.5 1.5 0 1 1 0 3 1.5 1.5 0 0 1 0-3ZM12 15H8a1 1 0 0 1 0-2h1v-3H8a1 1 0 0 1 0-2h2a1 1 0 0 1 1 1v4h1a1 1 0 0 1 0 2Z" />
                    </svg>
                    <span class="sr-only">Info</span>
                    <div class="ml-3 text-sm font-medium text-yellow-500">
                        These are user variables. You must use getvar() and setvar() functions in the examples to access these.
                    </div>
                </div>
                <!-- End Alert Box -->

                <!-- User Variable Functions -->
                <button
                    class="inline-flex text-white bg-blue-700 hover:opacity-60 focus:ring-0 font-medium rounded-lg text-sm px-5 py-2.5 mb-2 transition duration-200"
                    data-modal-toggle="set-user-var-modal" data-modal-target="set-user-var-modal">
                    <i class="lni lni-circle-plus mr-2 mt-1"></i>Set User Variable
                </button>
                <!-- End User Variable Functions -->

                <br>

                <!-- Delete User Variable Functions -->
                <button
                    class="inline-flex text-white bg-red-700 hover:opacity-60 focus:ring-0 font-medium rounded-lg text-sm px-5 py-2.5 mb-2 transition duration-200"
                    data-modal-toggle="delete-all-vars-modal" data-modal-target="delete-all-vars-modal">
                    <i class="lni lni-trash-can mr-2 mt-1"></i>Delete All Variables
                </button>
                <!-- End Delete User Variable Functions -->

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

                <!-- Delete All Vars Modal -->
                <div id="delete-all-vars-modal" tabindex="-1"
                    class="fixed top-0 left-0 right-0 z-50 hidden p-4 overflow-x-hidden overflow-y-auto md:inset-0 h-[calc(100%-1rem)] max-h-full">
                    <div class="relative w-full max-w-md max-h-full">
                        <div class="relative bg-[#0f0f17] border border-red-700 rounded-lg shadow">
                            <div class="p-6 text-center">
                                <div class="flex items-center p-4 mb-4 text-sm text-white border border-yellow-500 rounded-lg bg-[#0f0f17]"
                                    role="alert">
                                    <span class="sr-only">Info</span>
                                    <div>
                                        <span class="font-medium">Notice!</span> You're about to delete all of your
                                        user variables. This can not be undone.
                                    </div>
                                </div>
                                <h3 class="mb-5 text-lg font-normal text-gray-200">Are you sure you want to delete
                                    all of your user variables?</h3>
                                <form method="POST">
                                    <button data-modal-hide="delete-all-vars-modal" name="delvars"
                                        class="text-white bg-red-600 hover:bg-red-800 focus:ring-4 focus:outline-none focus:ring-red-300 font-medium rounded-lg text-sm inline-flex items-center px-5 py-2.5 text-center mr-2">
                                        Yes, I'm sure
                                    </button>
                                    <button data-modal-hide="delete-all-vars-modal" type="button"
                                        class="inline-flex text-white bg-gray-700 hover:opacity-60 focus:ring-0 font-medium rounded-lg text-sm px-5 py-2.5 mb-2 transition duration-200">No,
                                        cancel</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- End Delete All Vars Modal -->

                <!-- START TABLE -->
                <div class="relative overflow-x-auto shadow-md sm:rounded-lg pt-5">
                    <table id="kt_datatable_user_vars" class="w-full text-sm text-left text-white">
                        <thead>
                            <tr class="fw-bolder fs-6 text-blue-700 px-7">
                                <th class="px-6 py-3">Variable Name</th>
                                <th class="px-6 py-3">Variable Data</th>
                                <th class="px-6 py-3">Username</th>
                                <th class="px-6 py-3">Read Only</th>
                                <th class="px-6 py-3">Actions</th>
                            </tr>
                        </thead>
                    </table>
                </div>
                <p class="text-xs text-red-600">Dropdown actions in <b>RED</b> do not show a confirmation!<a
                            class="text-blue-700"> Dropdown actions in <b>BLUE</b> will show a confirmation!</a></p>
                            <script>
    // Target the specific scrollable container
    let scrollableElement = document.querySelector('.relative.overflow-x-auto.shadow-md.sm\\:rounded-lg.pt-5');

    // Function to scroll to the bottom of the element
    function scrollToBottom(el) {
        el.scrollTop = el.scrollHeight;
    }

    // Monitor clicks on the "Actions" button and apply a delay before checking scroll position
    document.querySelectorAll('#kt_datatable_user_vars button[type="button"]').forEach(function(button) {
        button.addEventListener('click', function() {
            // Wait for a second to account for any DOM updates or animations
            setTimeout(() => {
                // Check if the current scroll position is not at the bottom after updates
                if (scrollableElement.scrollHeight - scrollableElement.clientHeight > scrollableElement.scrollTop) {
                    scrollToBottom(scrollableElement);
                }
            }, 1000); // Adjust this delay as necessary
        });
    });
</script>



