<?php
if ($_SESSION['role'] != "seller") {
    misc\auditLog\send("Attempted (and failed) to view seller logs.");
    dashboard\primary\error("Non-Sellers aren't allowed here.");
    die();
}
if(!isset($_SESSION['app'])) {
    dashboard\primary\error("Application not selected");
    die("Application not selected.");
}

if (isset($_POST['enableLogs'])) {
    $query = misc\mysql\query("UPDATE `apps` SET `sellerLogs` = 1 WHERE `secret` = ?",[$_SESSION['app']]);
    if ($query->affected_rows > 0) {
        misc\cache\purge('KeyAuthAppSeller:' . $_SESSION['sellerkey']);
        dashboard\primary\success("Successfully enabled seller logs!");
    }
    else {
        dashboard\primary\success("Failed to enable seller logs.");
    }
}

if (isset($_POST['disableLogs'])) {
    $query = misc\mysql\query("UPDATE `apps` SET `sellerLogs` = 0 WHERE `secret` = ?",[$_SESSION['app']]);
    if ($query->affected_rows > 0) {
        misc\cache\purge('KeyAuthAppSeller:' . $_SESSION['sellerkey']);
        dashboard\primary\success("Successfully disabled seller logs!");
    }
    else {
        dashboard\primary\success("Failed to disable seller logs.");
    }
}
?>

<div class="p-4 bg-[#09090d] block sm:flex items-center justify-between lg:mt-1.5">
    <div class="mb-1 w-full bg-[#0f0f17] rounded-xl">
        <div class="mb-4 p-4">
            <?php require '../app/layout/breadcrumb.php'; ?>
            <h1 class="text-xl font-semibold text-white-900 sm:text-2xl ">Seller Logs</h1>
            <p class="text-xs text-gray-500">View seller key activity. <a
                    href="https://docs.keyauth.cc/website/seller/seller-logs" target="_blank"
                    class="text-blue-600  hover:underline">Learn More</a>.</p>
            <br>
            <div class="p-4 flex flex-col">
                <div class="overflow-x-auto">
                    <form method="post">
                        <?php
                            $query = misc\mysql\query("SELECT `sellerLogs` FROM `apps` WHERE `secret` = ?",[$_SESSION['app']]);

                            $row = mysqli_fetch_array($query->result);
                            $enabled = $row['sellerLogs'];   

                        if (!$enabled){
                        ?>
                        <button
                            class="inline-flex text-white bg-blue-700 hover:opacity-60 focus:ring-0 font-medium rounded-lg text-sm px-5 py-2.5 mb-2 transition duration-200"
                            name="enableLogs">
                            <i class="lni lni-power-switch mr-2 mt-1"></i>Enable Logging
                        </button>
                        <?php } else { ?>
                            <button
                                class="inline-flex text-white bg-red-700 hover:opacity-60 focus:ring-0 font-medium rounded-lg text-sm px-5 py-2.5 mb-2 transition duration-200"
                                name="disableLogs">
                            <i class="lni lni-power-switch mr-2 mt-1"></i>Disable Logging
                        </button>
                        <?php } ?>

                    </form>


                    <!-- START TABLE -->
                    <div class="relative overflow-x-auto shadow-md sm:rounded-lg pt-5">
                        <table id="kt_datatable_seller_logs" class="w-full text-sm text-left text-white">
                            <thead>
                                <tr class="fw-bolder fs-6 text-blue-700 px-7">
                                    <th class="px-6 py-3">IP Address</th>
                                    <th class="px-6 py-3">URL</th>
                                    <th class="px-6 py-3">Date</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                    if ($_SESSION['app']) {
                                        $query = misc\mysql\query("SELECT * FROM `sellerLogs` WHERE `app` = ? ORDER BY `date` DESC",[$_SESSION['app']]);
                                        if ($query->num_rows > 0) {
                                            while ($row = mysqli_fetch_array($query->result)) {

                                                echo "<tr>";

                                                echo "  <td>" . $row["ip"] . "</td>";

                                                echo "  <td><a class=\"blur-sm hover:blur-none\" href=\"".$row["path"]."\" target=\"_blank\">". urldecode($row["path"]) . "</a></td>";

                                                echo "  <td><script>document.write(convertTimestamp(".$row["date"]."));</script></td>";
                                                
                                                echo "</tr>";
                                            }
                                        }
                                    }
                                ?>
                            </tbody>
                        </table>
                    </div>