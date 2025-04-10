<?php
if ($_SESSION['role'] != "seller") {
    misc\auditLog\send("Attempted (and failed) to view webloader.");
    dashboard\primary\error("Non-Sellers aren't allowed here.");
    die();
}
if(!isset($_SESSION['app'])) {
    dashboard\primary\error("Application not selected");
    die("Application not selected.");
}
if (isset($_POST['addButton'])) {
    $resp = misc\button\addButton($_POST['text'], $_POST['value']);
    match($resp){
        'success' => dashboard\primary\success("Successfully added button!"),
        'failure' => dashboard\primary\error("Failed to add button!"),
        default => dashboard\primary\error("Unhandled Error! Contact us if you need help")
    };
}

if (isset($_POST['delButton'])) {
    $resp = misc\button\deleteButton($_POST['delButton']);
    match($resp){
        'success' => dashboard\primary\success("Successfully deleted button!"),
        'failure' => dashboard\primary\error("Failed to delete button!"),
        default => dashboard\primary\error("Unhandled Error! Contact us if you need help")
    };
}

if (isset($_POST["selected"]) && isset($_POST["action"])) {
    $selected = json_decode($_POST["selected"]);

    if ($_POST["action"] == "delete"){
        foreach ($selected as $buttons){
            $resp = misc\button\deleteButton($buttons);
            match($resp){
                'success' => dashboard\primary\success("Successfully deleted button!"),
                'failure' => dashboard\primary\error("Failed to delete button!"),
                default => dashboard\primary\error("Unhandled Error! Contact us if you need help")
            };
        }
    } else {
        dashboard\primary\error("Invalid action!");
    }
}
?>
<div class="p-4 bg-[#09090d] block sm:flex items-center justify-between lg:mt-1.5">
    <div class="mb-1 w-full bg-[#0f0f17] rounded-xl">
        <div class="mb-4 p-4">
            <?php require '../app/layout/breadcrumb.php'; ?>
            <h1 class="text-xl font-semibold text-white-900 sm:text-2xl ">Web Loader</h1>
            <br>
            <div class="p-4 flex flex-col">
                <div class="overflow-x-auto">
                    <!-- Webloader Functions -->
                    <button
                        class="inline-flex text-white bg-blue-700 hover:opacity-60 focus:ring-0 font-medium rounded-lg text-sm px-5 py-2.5 mb-2 transition duration-200"
                        data-modal-toggle="create-button-modal" data-modal-target="create-button-modal">
                        <i class="lni lni-circle-plus mr-2 mt-1"></i>Create Button
                    </button>
                    <br>
                    <button
                        class="inline-flex text-white bg-red-700 hover:opacity-60 focus:ring-0 font-medium rounded-lg text-sm px-5 py-2.5 mb-2 transition duration-200"
                        data-modal-toggle="delete-all-buttons-modal" data-modal-target="delete-all-buttons-modal">
                        <i class="lni lni-trash-can mr-2 mt-1"></i>Delete All Buttons
                    </button>

                    <button id="dropdownselection" data-dropdown-toggle="webloaderdropdown" 
                        class="inline-flex text-white bg-red-700 hover:opacity-60 focus:ring-0 font-medium rounded-lg text-sm px-5 py-2.5 mb-2 transition duration-200 hidden"
                        type="button">Selection Options
                    </button>

                    <div id="webloaderdropdown" class="z-10 hidden bg-[#09090d] divide-y divide-gray-100 rounded-lg shadow w-44">
                        <ul class="py-2 text-sm text-white">
                            <li>
                                <form method="post">
                                    <input type="hidden" name="action" value="delete">
                                    <input type="hidden" id="selected" name="selected">
                                    <a href="#" onclick="this.parentNode.submit();" class="block px-4 py-2 focus:bold ml-2 hover:text-red-700">Delete Selected</a>
                                </form>
                            </li>
                        </ul>
                    </div>
                    <!-- End Webloader Functions -->

                    <!-- Create Button Modal -->
                    <div id="create-button-modal" tabindex="-1" aria-hidden="true"
                        class="fixed top-0 left-0 right-0 z-50 hidden w-full p-4 overflow-x-hidden overflow-y-auto md:inset-0 h-[calc(100%-1rem)] max-h-full">
                        <div class="relative w-full max-w-md max-h-full">
                            <!-- Modal content -->
                            <div class="relative bg-[#0f0f17] rounded-lg border border-[#1d4ed8] shadow">
                                <button type="button"
                                    class="absolute top-3 right-2.5 text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm w-8 h-8 ml-auto inline-flex justify-center items-center"
                                    data-modal-hide="create-button-modal">
                                    <span class="sr-only">Close modal</span>
                                </button>
                                <div class="px-6 py-6 lg:px-8">
                                    <h3 class="mb-4 text-xl font-medium text-white-900">Create Webloader Button</h3>
                                    <hr class="h-px mb-4 mt-4 bg-gray-700 border-0">
                                    <form class="space-y-6" method="POST">
                                        <div>

                                            <div class="relative mb-4">
                                                <input type="text" id="text" name="text"
                                                    class="block px-2.5 pb-2.5 pt-4 w-full text-sm text-white bg-transparent rounded-lg border-1 border-gray-700 appearance-none focus:ring-0  peer"
                                                    placeholder=" " autocomplete="on" required>
                                                <label for="text"
                                                    class="absolute text-sm text-gray-400 duration-300 transform -translate-y-4 scale-75 top-2 z-10 origin-[0] bg-[#0f0f17] px-2 peer-focus:px-2 peer-focus:text-blue-600 peer-placeholder-shown:scale-100 peer-placeholder-shown:-translate-y-1/2 peer-placeholder-shown:top-1/2 peer-focus:top-2 peer-focus:scale-75 peer-focus:-translate-y-4 left-1">Name
                                                </label>
                                            </div>

                                            <div class="relative mb-4">
                                                <input type="text" id="value" name="value"
                                                    class="block px-2.5 pb-2.5 pt-4 w-full text-sm text-white bg-transparent rounded-lg border-1 border-gray-700 appearance-none focus:ring-0  peer"
                                                    placeholder=" " autocomplete="on" required>
                                                <label for="value"
                                                    class="absolute text-sm text-gray-400 duration-300 transform -translate-y-4 scale-75 top-2 z-10 origin-[0] bg-[#0f0f17] px-2 peer-focus:px-2 peer-focus:text-blue-600 peer-placeholder-shown:scale-100 peer-placeholder-shown:-translate-y-1/2 peer-placeholder-shown:top-1/2 peer-focus:top-2 peer-focus:scale-75 peer-focus:-translate-y-4 left-1">Value</label>
                                            </div>


                                        </div>
                                        <button type="submit" name="addButton"
                                            class="w-full text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center">Create
                                            Webloader Button</button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- End Create Button Modal -->

                    <!-- Delete All Buttons Modal -->
                    <div id="delete-all-buttons-modal" tabindex="-1"
                        class="fixed top-0 left-0 right-0 z-50 hidden p-4 overflow-x-hidden overflow-y-auto md:inset-0 h-[calc(100%-1rem)] max-h-full">
                        <div class="relative w-full max-w-md max-h-full">
                            <div class="relative bg-[#0f0f17] border border-red-700 rounded-lg shadow">
                                <div class="p-6 text-center">
                                    <div class="flex items-center p-4 mb-4 text-sm text-white border border-yellow-500 rounded-lg bg-[#0f0f17]"
                                        role="alert">
                                        <span class="sr-only">Info</span>
                                        <div>
                                            <span class="font-medium">Notice!</span> You're about to delete all of
                                            your webloader buttons. <b>This can
                                                NOT be undone</b>
                                        </div>
                                    </div>
                                    <h3 class="mb-5 text-lg font-normal text-gray-200">Are you sure
                                        you want
                                        to
                                        delete your webloader buttons?</h3>
                                    <form method="POST">
                                        <button data-modal-hide="delete-all-buttons-modal" name="delallbuttons"
                                            class="text-white bg-red-600 hover:bg-red-800 focus:ring-4 focus:outline-none focus:ring-red-300 font-medium rounded-lg text-sm inline-flex items-center px-5 py-2.5 text-center mr-2">
                                            Yes, I'm sure
                                        </button>
                                        <button data-modal-hide="delete-all-buttons-modal" type="button"
                                            class="inline-flex text-white bg-gray-700 hover:opacity-60 focus:ring-0 font-medium rounded-lg text-sm px-5 py-2.5 mb-2 transition duration-200">No,
                                            cancel</button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- End Delete All Buttons Modal -->

                    <!-- START TABLE -->
                    <div class="relative overflow-x-auto shadow-md sm:rounded-lg pt-5">
                        <table id="kt_datatable_webloader" class="w-full text-sm text-left text-white">
                            <thead>
                                <tr class="fw-bolder fs-6 text-blue-700 px-7">
                                    <th class="px-6 py-3">Select</th>
                                    <th class="px-6 py-3">Button Text</th>
                                    <th class="px-6 py-3">Button Value</th>
                                    <th class="px-6 py-3">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                if ($_SESSION['app']) {
                                    $query = misc\mysql\query("SELECT * FROM `buttons` WHERE `app` = ?",[$_SESSION['app']]);
                                    if ($query->num_rows > 0) {
                                        while ($row = mysqli_fetch_array($query->result)) {

                                            echo "<tr>";

                                            echo '<td> <input type="checkbox" style="outline:none;"class="w-4 h-4 text-blue-600 bg-[#0f0f17] border-gray-300 rounded focus:ring-blue-500 checkbox" value="' . $row["value"] .'">' . '</td>';

                                            echo "  <td>" . $row["text"] . "</td>";

                                            echo "  <td>" . $row["value"] . "</td>";

                                            echo '<form method="POST">
                                <td>
                                <div x-data="{ open: false }" class="z-0">
                                <button x-on:click="open = true" class="flex items-center border border-gray-700 rounded-lg focus:opacity-60 text-white focus:text-white font-semibold rounded focus:outline-none focus:shadow-inner py-2 px-4" type="button">
                                        <span class="mr-1">Actions</span>
                                        <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"  style="margin-top:3px">
                                        <path d="M9.293 12.95l.707.707L15.657 8l-1.414-1.414L10 10.828 5.757 6.586 4.343 8z"/>
                                        </svg>
                                </button>
                                <ul x-show="open" x-on:click.away="open = false" class="bg-[#09090d] text-white rounded shadow-lg absolute py-2 mt-1" style="min-width:15rem">
                                        <li>
                                                <button name="delButton" class="block hover:opacity-60 whitespace-no-wrap py-2 px-4 hover:text-red-700"
                                                value="' . $row["value"] . '">
                                                Delete Button
                                                </button>
                                        </li>
                                    </ul>
                                    </div>
                                </td></tr></form>';
                                        }
                                    }
                                }

                                ?>
                            </tbody>
                        </table>
                    </div>
                    <p class="text-xs text-red-600">Dropdown actions in <b>RED</b> do not show a confirmation!<a
                            class="text-blue-700"> Dropdown actions in <b>BLUE</b> will show a confirmation!</a></p>
