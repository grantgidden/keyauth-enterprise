<?php
$query = misc\mysql\query("SELECT * FROM `accounts` WHERE `username` = ?", [$_SESSION['username']]);

if ($query->num_rows > 0) {
    while ($row_ = mysqli_fetch_array($query->result)) {
        $acclogs = $row_['acclogs'];
        $expiry = $row_["expires"];
        $emailVerify = $row_["emailVerify"];
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['logout'])) {
        session_destroy();
        header('Location: /login');
        exit;
    }
}

?>

<div class="w-full max-w-sm border border-gray-700 rounded-lg shadow">
    <div class="flex justify-end px-4 pt-2">
        <button id="dropdownButton" data-dropdown-toggle="dropdown" class="inline-block text-gray-500 hover:opacity-60 focus:ring-0 p-1.5" type="button">
            <span class="sr-only">Open dropdown</span>
            <svg class="w-5 h-5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 16 3">
                <path d="M2 0a1.5 1.5 0 1 1 0 3 1.5 1.5 0 0 1 0-3Zm6.041 0a1.5 1.5 0 1 1 0 3 1.5 1.5 0 0 1 0-3ZM14 0a1.5 1.5 0 1 1 0 3 1.5 1.5 0 0 1 0-3Z" />
            </svg>
        </button>
        <!-- Dropdown menu -->
        <form method="post">
            <div id="dropdown" class="z-10 hidden text-base list-none bg-[#09090d] rounded-lg shadow w-44">
                <ul class="py-2" aria-labelledby="dropdownButton">
                    <li>
                        <a href="?page=account-settings" class="block px-4 py-2 text-sm text-white hover:bg-blue-700">Account Settings</a>
                    </li>
                    <li>
                        <a href="?page=account-logs" class="block px-4 py-2 text-sm text-white hover:bg-blue-700">Account Logs</a>
                    </li>
                    <li>
                        <a href="?page=logout" class="block px-4 py-2 text-sm text-white hover:bg-blue-700">Log Out</a>
                    </li>
                </ul>
            </div>
        </form>
    </div>

    <div class="flex items-center px-4 pb-4 ml-6">
        <img class="w-12 h-12 rounded-full mr-3" src="<?= $_SESSION["img"]; ?>" alt="profile image" />

        <div class="ml-4 flex flex-col mr-4">
            <?php if ($role == "seller") { ?>
                <h5 class="mb-1 text-lg font-medium bg-blue-700 stars ml-5"><?= $_SESSION["username"]; ?></h5>
            <?php } else { ?>
                <h5 class="text-lg font-medium text-blue-700 ml-5"><?= $_SESSION["username"]; ?></h5>
            <?php } ?>

            <?php
            $display = match ($role) {
                'tester' => '<label class="text-sm text-gray-400"><b>Expires:</b> Never </label>',
                'developer' => '<label class="text-sm text-gray-400"><b>Expires:</b> <span id="expiryLabel"></span> days</label>',
                'seller' => '<label class="text-sm text-gray-400"><b>Expires:</b> <span id="expiryLabel"></span> days</label>',
                'Reseller' => '<label class="text-sm text-gray-400"><b>Expires:</b> Owner Decides </label>',
                'Manager' => '<label class="text-sm text-gray-400"><b>Expires:</b> Owner Decides </label>',
                'default' => '<label class="text-sm text-gray-400"><b>Expires:</b> Never </label>'
            };
            echo $display;

            if ($role === 'developer' || $role === 'seller') {
                echo '<script>';
                echo 'document.getElementById("expiryLabel").textContent = calculateDaysLeft(' . $expiry . ');';
                echo 'function calculateDaysLeft(expiryTimestamp) {';
                echo '    const currentDate = new Date();';
                echo '    const expiryDate = new Date(expiryTimestamp * 1000);'; // Convert Unix timestamp to milliseconds
                echo '    const differenceInTime = expiryDate - currentDate;';
                echo '    const differenceInDays = Math.ceil(differenceInTime / (1000 * 60 * 60 * 24));';
                echo '    return differenceInDays > 0 ? differenceInDays : "Expired";';
                echo '}';
                echo '</script>';
            }
            ?>
        </div>
    </div>

    <div class="px-4 pb-4">
        <?php
        $cssClasses = "text-center text-transparent bg-clip-text bg-gradient-to-r to-blue-600 from-sky-400 text-xs font-black px-1.5 py-0.5 rounded border mb-2";

        if ($role == "seller") {
            $cssClasses .= " border-blue-400";
        } elseif ($role == "developer") {
            $cssClasses .= " border-white-400";
        }

        echo "<p class=\"$cssClasses\">" . strtoupper($role) . " PLAN</p>";
        ?>
    </div>
</div>
<br>
