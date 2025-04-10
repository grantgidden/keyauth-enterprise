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
	$sel = misc\mysql\query("select count(1) as allcount from `logs` where logapp = ?", [$_SESSION['app']]);
	$records = mysqli_fetch_assoc($sel->result);
	$totalRecords = $records['allcount'];

	$totalRecordwithFilter = $totalRecords;
	if (!is_null($searchValue)) { // don't double query if no search value was provided
		## Total number of record with filtering
		$sel = misc\mysql\query("select count(1) as allcount from `logs` WHERE 1  and (`logdata` like ? or `credential` like ? or `pcuser` like ? ) and logapp = ?", ["%" . $searchValue . "%", "%" . $searchValue . "%", "%" . $searchValue . "%", $_SESSION['app']]);
		$records = mysqli_fetch_assoc($sel->result);
		$totalRecordwithFilter = $records['allcount'];
	}

        // whitelist certain column names and sort orders to prevent SQL injection
        if (!in_array($columnName, array("logdate", "logdata", "credential", "pcuser"))) {
                die("Column name is not whitelisted.");
        }

        if (!in_array($columnSortOrder, array("desc", "asc"))) {
                die("Column sort order is not whitelisted.");
        }

        if (!is_null($searchValue)) {
                $query = misc\mysql\query("select * from `logs` WHERE (`logdata` like ? or `credential` like ? or `pcuser` like ? ) and logapp = ? order by `" . $columnName . "` " . $columnSortOrder . " limit " . $row . "," . $rowperpage, ["%" . $searchValue . "%", "%" . $searchValue . "%", "%" . $searchValue . "%", $_SESSION['app']]);
        }
        else {
                $query = misc\mysql\query("select * from `logs` WHERE logapp = ? order by `" . $columnName . "` " . $columnSortOrder . " limit " . $row . "," . $rowperpage, [$_SESSION['app']]);
        }
        
        $data = array();

        while ($row = mysqli_fetch_assoc($query->result)) {
                $data[] = array(
                        "logdate" => '<div id="' . $row['id'] . '-logdate"><script>document.getElementById("' . $row['id'] . '-logdate").textContent=convertTimestamp(' . $row["logdate"] . ');</script></div>',
                        "logdata" => $row['logdata'],
                        "credential" => $row['credential'] ?? 'N/A',
                        "pcuser" => $row['pcuser'] ?? 'N/A',
                );
        }

        ## Response
        $response = array(
                "draw" => intval($draw),
                "iTotalRecords" => $totalRecords,
		"iTotalDisplayRecords" => $totalRecordwithFilter,
                "aaData" => $data
        );

        die(json_encode($response));
}

die("Request not from datatables, aborted.");
