<?php
if ($_SESSION["role"] != 'Reseller'){
    dashboard\primary\error("Only resellers can access this page.");
    die();
}
?>

<script src="https://shoppy.gg/api/embed.js"></script>

<script src="https://cdn.sellix.io/static/js/embed.js"></script>

<link href="https://cdn.sellix.io/static/css/embed.css" rel="stylesheet" />
<div class="p-4 bg-[#09090d] block sm:flex items-center justify-between lg:mt-1.5">
    <div class="mb-1 w-full bg-[#0f0f17] rounded-xl">
        <div class="mb-4 p-4">
            <?php require '../app/layout/breadcrumb.php'; ?>
            <h1 lang class="text-xl font-semibold text-white-900 sm:text-2xl">Reseller Balance</h1>
            <p class="text-xs text-gray-500">Check your balance here.</p>
            <br>
            <div class="p-4 flex flex-col">
                <div class="overflow-x-auto">
                    <?php
                $query = misc\mysql\query("SELECT * FROM `apps` WHERE `secret` = ?",[$_SESSION['app']]);

                $row = mysqli_fetch_array($query->result);

                if ($row["sellappsecret"] != NULL) {

                    if ($row["sellappdayproduct"] != NULL) {
                        $dayproduct_holder = '<a href="' . $row["sellappdayproduct"] . $_SESSION['username'] . '" target="_blank">
                            <p class="text-sm text-yellow-700 hover:underline transition duration-200 cursor-pointer">
                                <b>Purchase Day Keys (SellApp)</b>
                            </p>
                        </a>'; 
                    } else { $dayproduct_holder = "N/A"; }

                    if ($row["sellappweekproduct"] != NULL) {
                        $weekproduct_holder = '<a href="' . $row["sellappweekproduct"] . $_SESSION['username'] . '" target="_blank">
                            <p class="text-sm text-yellow-700 hover:underline transition duration-200 cursor-pointer">
                                <b>Purchase Week Keys (SellApp)</b>
                            </p>
                        </a>'; 
                    } else { $weekproduct_holder = "N/A"; }

                    if ($row["sellappmonthproduct"] != NULL) {
                        $monthproduct_holder = '<a href="' . $row["sellappmonthproduct"] . $_SESSION['username'] . '" target="_blank">
                            <p class="text-sm text-yellow-700 hover:underline transition duration-200 cursor-pointer">
                                <b>Purchase Month Keys (SellApp)</b>
                            </p>
                        </a>'; 
                    } else { $monthproduct_holder = "N/A"; }

                    if ($row["sellapplifetimeproduct"] != NULL) {
                        $lifetimeproduct_holder = '<a href="' . $row["sellapplifetimeproduct"] . $_SESSION['username'] . '" target="_blank">
                            <p class="text-sm text-yellow-700 hover:underline transition duration-200 cursor-pointer">
                                <b>Purchase Lifetime Keys (SellApp)</b>
                            </p>
                        </a>'; 
                    } else { $lifetimeproduct_holder = "N/A"; }
                }

                if ($row["sellixsecret"] != NULL) {

                    if ($row["sellixdayproduct"] != NULL) {
                        $dayproduct_holder = '<a data-sellix-product="' . $row["sellixdayproduct"] . '" data-sellix-custom-Username=' . $_SESSION['username'] . '">
                            <p class="text-sm text-purple-700 hover:underline transition duration-200 cursor-pointer">
                                <b>Purchase Day Keys (Sellix)</b>
                            </p>
                        </a>';
                    } else { $dayproduct_holder = "N/A"; }

                    if ($row["sellixweekproduct"] != NULL) {
                        $weekproduct_holder = '<a data-sellix-product="' . $row["sellixweekproduct"] . '" data-sellix-custom-Username=' . $_SESSION['username'] . '">
                            <p class="text-sm text-purple-700 hover:underline transition duration-200 cursor-pointer">
                                <b>Purchase Week Keys (Sellix)</b>
                            </p>
                        </a>';
                    } else { $weekproduct_holder = "N/A"; }

                    if ($row["sellixmonthproduct"] != NULL) {
                        $monthproduct_holder = '<a data-sellix-product="' . $row["sellixmonthproduct"] . '" data-sellix-custom-Username=' . $_SESSION['username'] . '">
                            <p class="text-sm text-purple-700 hover:underline transition duration-200 cursor-pointer">
                                <b>Purchase Month Keys (Sellix)</b>
                            </p>
                        </a>';
                    } else { $monthproduct_holder = "N/A"; }

                    if ($row["sellixlifetimeproduct"] != NULL) {
                        $lifetimeproduct_holder = '<a data-sellix-product="' . $row["sellixlifetimeproduct"] . '" data-sellix-custom-Username=' . $_SESSION['username'] . '">
                            <p class="text-sm text-purple-700 hover:underline transition duration-200 cursor-pointer">
                                <b>Purchase Lifetime Keys (Sellix)</b>
                            </p>
                        </a>'; 
                    } else { $lifetimeproduct_holder = "N/A"; }
                }

                if ($row["shoppysecret"] != NULL) {

                    if ($row["shoppydayproduct"] != NULL) {
                        $dayproduct_holder =  '<a data-shoppy-product="' . $row["shoppydayproduct"] . '" data-shoppy-username=' . $_SESSION['username'] . '">
                            <p class="text-sm text-green-700 hover:underline transition duration-200 cursor-pointer">
                                <b>Purchase Day Keys (Shoppy)</b>
                            </p>
                        </a>'; 
                    } else { $dayproduct_holder = "N/A"; }

                    if ($row["shoppyweekproduct"] != NULL) {
                        $weekproduct_holder = '<a data-shoppy-product="' . $row["shoppyweekproduct"] . '" data-shoppy-username=' . $_SESSION['username'] . '">
                            <p class="text-sm text-green-700 hover:underline transition duration-200 cursor-pointer">       
                                <b>Purchase Week Keys (Shoppy)</b>
                            </p>
                        </a>'; 
                    } else { $weekproduct_holder = "N/A"; }

                    if ($row["shoppymonthproduct"] != NULL) {
                        $monthproduct_holder = '<a data-shoppy-product="' . $row["shoppymonthproduct"] . '" data-shoppy-username=' . $_SESSION['username'] . '">
                            <p class="text-sm text-green-700 hover:underline transition duration-200 cursor-pointer">
                                <b>Purchase Month Keys (Shoppy)</b>
                            </p>
                        </a>'; 
                    } else { $monthproduct_holder = "N/A"; }

                    if ($row["shoppylifetimeproduct"] != NULL) {
                        $lifetimeproduct_holder = '<a data-shoppy-product="' . $row["shoppylifetimeproduct"] . '" data-shoppy-username=' . $_SESSION['username'] . '">
                            <p class="text-sm text-green-700 hover:underline transition duration-200 cursor-pointer">
                                <b>Purchase Lifetime Keys (Shoppy)</b>
                            </p>
                        </a>'; 
                    } else { $lifetimeproduct_holder = "N/A"; }
                }


                if ($row["resellerstore"] != NULL) {
                    echo '<a href="' . $row["resellerstore"] . '" target="resellerstore">
                        <button class="inline-flex text-white bg-blue-700 hover:opacity-60 focus:ring-0 font-medium rounded-lg text-sm px-5 py-2.5 mb-2 transition duration-200">
                            <i class="lni lni-money-protection mr-2 mt-1"></i>Purchase Lifetime Keys (General Store)
                        </button>
                    </a>'; 
                     
                }
                ?>
                    <?php
$query = misc\mysql\query("SELECT `balance` FROM `accounts` WHERE `username` = ?",[$_SESSION['username']]);


$row = mysqli_fetch_array($query->result);


$balance = $row["balance"];


$balance = explode("|", $balance);


$day = $balance[0];

$week = $balance[1];

$month = $balance[2];

$threemonth = $balance[3];

$sixmonth = $balance[4];

$life = $balance[5];

$year = $balance[6];

echo '<br><br>

<div class="relative overflow-x-auto shadow-md sm:rounded-lg">
    <table class="w-full text-sm text-left text-gray-500">
        <thead class="text-xs text-blue-700 uppercase">
            <tr>
                <th scope="col" class="px-6 py-3">
                    Duration
                </th>
                <th scope="col" class="px-6 py-3">
                    Amount
                </th>
                <th scope="col" class="px-6 py-3">
                    Restock
                </th>
            </tr>
        </thead>
        <tbody>
            <tr class="border-b bg-[#0f0f17]">
                <th scope="row" class="px-6 py-4 font-medium text-white whitespace-nowrap">
                    Day
                </th>
                <td class="px-6 py-4 text-white">
                    ' . $day . '
                </td>
                <td class="px-6 py-4 text-white">
                    <p>' . $dayproduct_holder . '</p>
                </td>
            </tr>
            <tr class="border-b bg-[#0f0f17]">
                <th scope="row" class="px-6 py-4 font-medium text-white whitespace-nowrap">
                    Week
                </th>
                <td class="px-6 py-4 text-white">
                    ' . $week . '
                </td>
                <td class="px-6 py-4 text-white">
                    <p>' . $weekproduct_holder . '</p>
                </td>
            </tr>
            <tr class="border-b bg-[#0f0f17]">
                <th scope="row" class="px-6 py-4 font-medium text-white whitespace-nowrap">
                    Month
                </th>
                <td class="px-6 py-4 text-white">
                    ' . $month . '
                </td>
                <td class="px-6 py-4 text-white">
                    <p>' . $monthproduct_holder . '</p>
                </td>
            </tr>
            <tr class="border-b bg-[#0f0f17]">
                <th scope="row" class="px-6 py-4 font-medium text-white whitespace-nowrap">
                    Three Month
                </th>
                <td class="px-6 py-4 text-white">
                    ' . $threemonth . '
                </td>
                <td class="px-6 py-4 text-white">
                    <p>N/A</p>
                </td>
            </tr>
            <tr class="border-b bg-[#0f0f17]">
                <th scope="row" class="px-6 py-4 font-medium text-white whitespace-nowrap">
                    Six Month
                </th>
                <td class="px-6 py-4 text-white">
                    ' . $sixmonth . '
                </td>
                <td class="px-6 py-4 text-white">
                    <p>N/A</p>
                </td>
            </tr>
            <tr class="border-b bg-[#0f0f17]">
                <th scope="row" class="px-6 py-4 font-medium text-white whitespace-nowrap">
                    Year
                </th>
                <td class="px-6 py-4 text-white">
                    ' . $year . '
                </td>
                <td class="px-6 py-4 text-white">
                    <p>N/A</p>
                </td>
            </tr>
            <tr class="border-b bg-[#0f0f17]">
                <th scope="row" class="px-6 py-4 font-medium text-white whitespace-nowrap">
                    Lifetime
                </th>
                <td class="px-6 py-4 text-white">
                    ' . $life . '
                </td>
                <td class="px-6 py-4 text-white">
                    <p>' . $lifetimeproduct_holder . '</p>
                </td>
            </tr>
        </tbody>
    </table>
</div>
';
                    ?>