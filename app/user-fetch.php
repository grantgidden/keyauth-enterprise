<?php
include '../includes/misc/autoload.phtml';

set_exception_handler(function ($exception) {
        error_log("\n--------------------------------------------------------------\n");
        error_log($exception);
        error_log("\nRequest data:");
        error_log(print_r($_POST, true));
        error_log("\n--------------------------------------------------------------");
        http_response_code(500);
        global $databaseUsername;
        $errorMsg = str_replace($databaseUsername, "REDACTED", $exception->getMessage());
        die("Error: " . $errorMsg);
});

if (session_status() === PHP_SESSION_NONE) {
        session_start();
}

if ($_SESSION['role'] == "Reseller") {
        die("Resellers can't access this.");
}

if (!isset($_SESSION['app'])) {
        dashboard\primary\error("Application not selected");
        die("Application not selected.");
}

if (isset($_POST['draw'])) {

        // credits to https://makitweb.com/datatables-ajax-pagination-with-search-and-sort-php/

        $draw = intval($_POST['draw']);
        $row = intval($_POST['start']);
        $rowperpage = intval($_POST['length']); // Rows display per page
        $columnIndex = misc\etc\sanitize($_POST['order'][0]['column']); // Column index
        $columnName = misc\etc\sanitize($_POST['columns'][$columnIndex]['data']); // Column name
        $columnSortOrder = misc\etc\sanitize($_POST['order'][0]['dir']); // asc or desc
        $searchValue = misc\etc\sanitize($_POST['search']['value']); // Search value

        ## Total number of records without filtering
	$sel = misc\mysql\query("select count(1) as allcount from `sessions` where app = ?", [$_SESSION['app']]);
	$records = mysqli_fetch_assoc($sel->result);
	$totalRecords = $records['allcount'];

	$totalRecordwithFilter = $totalRecords;
	if (!is_null($searchValue)) { // don't double query if no search value was provided
		## Total number of record with filtering
		$sel = misc\mysql\query("select count(1) as allcount from `sessions` WHERE 1  and (`id` like ? or `credential` like ? or `ip` like ? ) and app = ?", ["%" . $searchValue . "%", "%" . $searchValue . "%", "%" . $searchValue . "%", $_SESSION['app']]);
		$records = mysqli_fetch_assoc($sel->result);
		$totalRecordwithFilter = $records['allcount'];
	}

        ## Total number of records without filtering
	$sel = misc\mysql\query("select count(1) as allcount from `users` where app = ?", [$_SESSION['app']]);
	$records = mysqli_fetch_assoc($sel->result);
	$totalRecords = $records['allcount'];

	$totalRecordwithFilter = $totalRecords;
	if (!is_null($searchValue)) { // don't double query if no search value was provided
		## Total number of record with filtering
		$sel = misc\mysql\query("select count(1) as allcount from `users` WHERE 1  and (`username` like ? or `hwid` like ? or `ip` like ? or `banned` like ? ) and app = ?", ["%" . $searchValue . "%", "%" . $searchValue . "%", "%" . $searchValue . "%", "%" . $searchValue . "%", $_SESSION['app']]);
		$records = mysqli_fetch_assoc($sel->result);
		$totalRecordwithFilter = $records['allcount'];
	}

        // whitelist certain column names and sort orders to prevent SQL injection
        if (!in_array($columnName, array("username", "hwid", "ip", "createdate", "lastlogin", "banned"))) {
                die("Column name is not whitelisted.");
        }

        if (!in_array($columnSortOrder, array("desc", "asc"))) {
                die("Column sort order is not whitelisted.");
        }

        if (!is_null($searchValue)) {
                $query = misc\mysql\query("select * from `users` WHERE (`username` like ? or `hwid` like ? or `ip` like ? or `banned` like ? ) and app = ? order by `" . $columnName . "` " . $columnSortOrder . " limit " . $row . "," . $rowperpage, ["%" . $searchValue . "%", "%" . $searchValue . "%", "%" . $searchValue . "%", "%" . $searchValue . "%", $_SESSION['app']]);
        }
        else {
                $query = misc\mysql\query("select * from `users` WHERE app = ? order by `" . $columnName . "` " . $columnSortOrder . " limit " . $row . "," . $rowperpage, [$_SESSION['app']]);
        }
        
        $data = array();

        while ($row = mysqli_fetch_assoc($query->result)) {

                ## If User is banned show only unban and if not show only ban.
                $banBtns = "";
                if ($row['banned'] ? true : false) {
                        $banned = $row['banned'];
                        $banBtns = '<button class="block hover:opacity-60 whitespace-no-wrap py-2 px-4 hover:text-red-700" name="unbanuser" value="' . urlencode($row['username']) . '">Unban User</button>';
                } else {
                        $banned = "N/A";
                        $banBtns = '<button class="block hover:opacity-60 whitespace-no-wrap py-2 px-4 hover:text-blue-700" type="button" onclick="banuser(\'' . urlencode($row["username"]) . '\')">Ban User</button>';
                                   
                }

                ## Add Extra Margin to buttons if value is 1 or 2, because datatables with ajax breaks it.
                $MarginManager = "";
                if ($query->num_rows < 2) {
                        $MarginManager = "margin-bottom: 120px;";
                } else {
                        $MarginManager = "margin-bottom: 0px;";
                }

                $data[] = array(
                        "checkbox" => '<input type="checkbox" style="outline:none;"class="w-4 h-4 text-blue-600 bg-[#0f0f17] border-gray-300 rounded focus:ring-blue-500 checkbox" value="' . $row["username"] .'">',
                        "username" => $row['username'],
                        "hwid" => ($_SESSION['role'] == "Manager" || $_SESSION['role'] == "Reseller") ? 'REDACTED' : '<span class="blur-sm hover:blur-none">' . ($row['hwid'] ?? 'N/A') . '</span>',
                        "ip" => ($_SESSION['role'] == "Manager" || $_SESSION['role'] == "Reseller") ? 'REDACTED' : '<span class="blur-sm hover:blur-none">' . ($row['ip'] ?? 'N/A') . '</span>',
                        "createdate" => '<div id="' . $row['username'] . '-createdate"><script>document.getElementById("' . $row['username'] . '-createdate").textContent=convertTimestamp(' . $row["createdate"] . ');</script></div>',
                        "lastlogin" => '<div id="' . $row['username'] . '-lastlogin"><script>document.getElementById("' . $row['username'] . '-lastlogin").textContent=convertTimestamp(' . $row["lastlogin"] . ');</script></div>',
                        "banned" => $banned,
                        "actions" => '<tr><form method="POST">
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
                                                                <button name="deleteuser" class="block hover:opacity-60 whitespace-no-wrap py-2 px-4 hover:text-red-700"
                                                                value="' . urlencode($row["username"]) . '">
                                                                Delete User
                                                                </button>
                                                        </li>
                                                        <li>
                                                                <button name="resetuser" class="block hover:opacity-60 whitespace-no-wrap py-2 px-4 hover:text-red-700"
                                                                value="' . $row["username"] . '">
                                                                Reset User HWID
                                                                </button>
                                                        </li>
                                                        <li>
                                                                ' . $banBtns . '
                                                        </li>
                                                        <li>
                                                                <button name="pauseuser" class="block hover:opacity-60 whitespace-no-wrap py-2 px-4 hover:text-red-700"
                                                                value="' . $row['username'] . '">
                                                                Pause User
                                                                </button>
                                                        </li>
                                                        <li>
                                                                <button name="unpauseuser" class="block hover:opacity-60 whitespace-no-wrap py-2 px-4 hover:text-red-700"
                                                                value="' . $row['username'] . '">
                                                                Unpause User
                                                                </button>
                                                        </li>
                                                        <li>
                                                                <button name="edituser" class="block hover:opacity-60 whitespace-no-wrap py-2 px-4 hover:text-blue-700"
                                                                value="' . urlencode($row['username']) . '">
                                                                Edit User
                                                                </button>
                                                        </li>
                                                </ul>
                                                </div>
                        </td>
                </tr>
                    </form>'
                        );
        }

        ## Response
        $response = array(
                "draw" => $draw,
                "iTotalRecords" => $totalRecords,
		"iTotalDisplayRecords" => $totalRecordwithFilter,
                "aaData" => $data
        );

        die(json_encode($response));
}

die("Request not from datatables, aborted.");