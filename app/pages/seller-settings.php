<?php
if ($_SESSION['role'] != "seller") {
    misc\auditLog\send("Attempted (and failed) to view seller settings.");
    dashboard\primary\error("Non-Sellers aren't allowed here.");
    die();
}
if(!isset($_SESSION['app'])) {
    dashboard\primary\error("Application not selected");
    die("Application not selected.");
}
if (isset($_POST['refreshseller'])) {
    $algos = array(
        'ripemd128',
        'md5',
        'md4',
        'tiger128,4',
        'haval128,3',
        'haval128,4',
        'haval128,5'
    );
    $sellerkey = hash($algos[array_rand($algos)], misc\etc\generateRandomString());
    misc\mysql\query("UPDATE `apps` SET `sellerkey` = ? WHERE `secret` = ?",[$sellerkey, $_SESSION['app']]);
    dashboard\primary\success("Successfully reset seller key");
    misc\cache\purge('KeyAuthAppSeller:' . $_SESSION['sellerkey']);
}

if (isset($_POST['whitelist'])) {
    $whitelistedIp = misc\etc\sanitize($_POST['whitelistedIp']);
    $whitelistedRestrictions = misc\etc\sanitize($_POST['restrictions']);
    if (!filter_var($whitelistedIp, FILTER_VALIDATE_IP) && !is_null($whitelistedIp)) {
        dashboard\primary\error("You didn't enter a valid IP address");
    } else {
        misc\mysql\query("UPDATE `apps` SET `sellerApiWhitelist` = ? WHERE `secret` = ?",[$whitelistedIp, $_SESSION['app']]);
        dashboard\primary\success("Successfully whitelisted IP");
        misc\cache\purge('KeyAuthAppSeller:' . $_SESSION['sellerkey']);
    }
}

if (isset($_POST['delSellerWhitelist'])){
    $query = misc\mysql\query("UPDATE `apps` SET `sellerApiWhitelist` = NULL WHERE `secret` = ?", [$_SESSION['app']]);
    if ($query !== false) {
        dashboard\primary\success("Successfully removed the IP from the Seller API whitelist!");
        misc\cache\purge('KeyAuthAppSeller:' . $_SESSION['sellerkey']);
    } else {
        dashboard\primary\error("Failed to remove the IP from the Seller API whitelist!");
    }
}

if (isset($_GET['cust'])) {
    $botToken = "MTI0MzkzODIxMTUwNDMyODcyNA.G0nDql.lXtoY2Miqqg2WvLXeXIJ1GBDwsQqAV0fc0oy6w";

    $url = "https://discord.com/api/applications/@me";

    $commands = [
        [
            'name' => 'add-blacklist',
            'type' => 1,
            'description' => 'Add blacklist',
            'default_member_permissions' => 0,
            'options' => [
                [
                    'name' => 'ip',
                    'description' => 'IP Address you want to blacklist',
                    'type' => 3,
                ],
                [
                    'name' => 'hwid',
                    'description' => 'Hardware-ID you want to blacklist',
                    'type' => 3,
                ],
            ],
        ],
        [
            'name' => 'delete-all-blacklists',
            'type' => 1,
            'description' => 'Delete All Blacklists',
            'default_member_permissions' => 0,
        ],
        [
            'name' => 'add-channel',
            'type' => 1,
            'description' => 'Add chat channel',
            'default_member_permissions' => 0,
            'options' => [
                [
                    'name' => 'name',
                    'description' => 'Chat channel name',
                    'type' => 3,
                    'required' => true,
                ],
                [
                    'name' => 'delay',
                    'description' => 'Chat channel delay (how often user can send messages in seconds)',
                    'type' => 3,
                    'required' => true,
                ],
            ],
        ],
        [
            'name' => 'delete-channel',
            'type' => 1,
            'description' => 'Delete chat channel',
            'default_member_permissions' => 0,
            'options' => [
                [
                    'name' => 'name',
                    'description' => 'Chat channel name',
                    'type' => 3,
                    'required' => true,
                ],
            ],
        ],
        [
            'name' => 'edit-channel',
            'type' => 1,
            'description' => 'Edit Channel',
            'default_member_permissions' => 0,
            'options' => [
                [
                    'name' => 'name',
                    'description' => 'The name of the channel you would like to edit.',
                    'type' => 3,
                    'required' => true,
                ],
                [
                    'name' => 'delay',
                    'description' => 'The delay between messages.',
                    'type' => 4,
                    'required' => true,
                ],
            ],
        ],
        [
            'name' => 'mute-user',
            'type' => 1,
            'description' => 'Mute user from sending messages in chat channels',
            'default_member_permissions' => 0,
            'options' => [
                [
                    'name' => 'user',
                    'description' => 'The user\'s username',
                    'type' => 3,
                    'required' => true,
                ],
                [
                    'name' => 'time',
                    'description' => 'Time in seconds user is muted for',
                    'type' => 3,
                    'required' => true,
                ],
            ],
        ],
        [
            'name' => 'purge-chat',
            'type' => 1,
            'description' => 'Purge chat channel\'s messages',
            'default_member_permissions' => 0,
            'options' => [
                [
                    'name' => 'name',
                    'description' => 'Chat channel name',
                    'type' => 3,
                    'required' => true,
                ],
            ],
        ],
        [
            'name' => 'set-user-cooldown',
            'type' => 1,
            'description' => 'Set User\'s Cooldown',
            'default_member_permissions' => 0,
            'options' => [
                [
                    'name' => 'user',
                    'description' => 'The username of the user you would like to apply the cooldown for.',
                    'type' => 3,
                    'required' => true,
                ],
                [
                    'name' => 'cooldown',
                    'description' => 'The duration of the cooldown in seconds. Default 120 seconds.',
                    'type' => 4,
                    'required' => true,
                ],
            ],
        ],
        [
            'name' => 'unmute-user',
            'type' => 1,
            'description' => 'Unmute user from chat channel',
            'default_member_permissions' => 0,
            'options' => [
                [
                    'name' => 'user',
                    'description' => 'The user\'s username',
                    'type' => 3,
                    'required' => true,
                ],
            ],
        ],
        [
            'name' => 'delete-file',
            'type' => 1,
            'description' => 'Delete Existing File',
            'default_member_permissions' => 0,
            'options' => [
                [
                    'name' => 'fileid',
                    'description' => 'The file id of the file you would like to delete.',
                    'type' => 3,
                    'required' => true,
                ],
            ],
        ],
        [
            'name' => 'delete-files',
            'type' => 1,
            'description' => 'Delete All Files',
            'default_member_permissions' => 0,
        ],
        [
            'name' => 'upload-file',
            'type' => 1,
            'description' => 'Upload a file',
            'default_member_permissions' => 0,
            'options' => [
                [
                    'name' => 'url',
                    'description' => 'The direct download link of the file you would like to upload.',
                    'type' => 3,
                    'required' => true,
                ],
            ],
        ],
        [
            'name' => 'add-license',
            'type' => 1,
            'description' => 'Add a license key',
            'default_member_permissions' => 0,
            'options' => [
                [
                    'name' => 'expiry',
                    'description' => 'How many days?',
                    'type' => 3,
                ],
                [
                    'name' => 'level',
                    'description' => 'What level?',
                    'type' => 3,
                ],
                [
                    'name' => 'amount',
                    'description' => 'What amount?',
                    'type' => 3,
                ],
                [
                    'name' => 'character',
                    'description' => '1 = Random, 2 = Uppercase, 3 = Lowercase',
                    'type' => 3,
                ],
                [
                    'name' => 'note',
                    'description' => 'Note, Default is "Added by KeyAuth Discord Bot"',
                    'type' => 3,
                ],
            ],
        ],
        [
            'name' => 'add-time',
            'type' => 1,
            'description' => 'Add time to unused keys(use extend for used keys aka users)',
            'default_member_permissions' => 0,
            'options' => [
                [
                    'name' => 'time',
                    'description' => 'Number of days',
                    'type' => 3,
                    'required' => true,
                ],
            ],
        ],
        [
            'name' => 'ban-license',
            'type' => 1,
            'description' => 'Ban license key',
            'default_member_permissions' => 0,
            'options' => [
                [
                    'name' => 'key',
                    'description' => 'Key you wish to ban',
                    'type' => 3,
                    'required' => true,
                ],
                [
                    'name' => 'reason',
                    'description' => 'Reason for the ban',
                    'type' => 3,
                    'required' => true,
                ],
                [
                    'name' => 'usertoo',
                    'description' => 'Ban user too?',
                    'type' => 5,
                ],
            ],
        ],
        [
            'name' => 'delete-all-licenses',
            'type' => 1,
            'description' => 'Delete All Licenses',
            'default_member_permissions' => 0,
        ],
        [
            'name' => 'delete-license',
            'type' => 1,
            'description' => 'Delete a key',
            'default_member_permissions' => 0,
            'options' => [
                [
                    'name' => 'license',
                    'description' => 'Specify key you would like deleted',
                    'type' => 3,
                    'required' => true,
                ],
                [
                    'name' => 'usertoo',
                    'description' => 'Delete from user too?',
                    'type' => 5,
                ],
            ],
        ],
        [
            'name' => 'delete-multiple-licenses',
            'type' => 1,
            'description' => 'Delete multiple licenses',
            'default_member_permissions' => 0,
            'options' => [
                [
                    'name' => 'licenses',
                    'description' => 'Specify key you would like deleted (seperate with comma and space)',
                    'type' => 3,
                    'required' => true,
                ],
                [
                    'name' => 'usertoo',
                    'description' => 'Delete from user too?',
                    'type' => 5,
                ],
            ],
        ],
        [
            'name' => 'delete-unused-licenses',
            'type' => 1,
            'description' => 'Delete Unused Licenses',
            'default_member_permissions' => 0,
        ],
        [
            'name' => 'delete-used-licenses',
            'type' => 1,
            'description' => 'Delete Used Licenses',
            'default_member_permissions' => 0,
        ],
        [
            'name' => 'license-info',
            'type' => 1,
            'description' => 'Info On key',
            'default_member_permissions' => 0,
            'options' => [
                [
                    'name' => 'license',
                    'description' => 'Specify key',
                    'type' => 3,
                    'required' => true,
                ],
            ],
        ],
        [
            'name' => 'set-license-note',
            'type' => 1,
            'description' => 'Set a note for a key',
            'default_member_permissions' => 0,
            'options' => [
                [
                    'name' => 'note',
                    'description' => 'Note to set',
                    'type' => 3,
                    'required' => true,
                ],
                [
                    'name' => 'license',
                    'description' => 'License to set note of',
                    'type' => 3,
                    'required' => true,
                ],
            ],
        ],
        [
            'name' => 'unban-license',
            'type' => 1,
            'description' => 'Unban license key',
            'default_member_permissions' => 0,
            'options' => [
                [
                    'name' => 'key',
                    'description' => 'Key you wish to unban',
                    'type' => 3,
                    'required' => true,
                ],
            ],
        ],
        [
            'name' => 'verify-license',
            'type' => 1,
            'description' => 'Verify license exists',
            'default_member_permissions' => 0,
            'options' => [
                [
                    'name' => 'license',
                    'description' => 'License key you would like to check the existence of',
                    'type' => 3,
                    'required' => true,
                ],
            ],
        ],
        [
            'name' => 'add-reseller-balance',
            'type' => 1,
            'description' => 'Add balance to reseller accounts.',
            'default_member_permissions' => 0,
            'options' => [
                [
                    'name' => 'username',
                    'description' => 'Username of the account',
                    'type' => 3,
                    'required' => true,
                ],
                [
                    'name' => 'days',
                    'description' => 'Number of days',
                    'type' => 3,
                    'required' => true,
                ],
                [
                    'name' => 'weeks',
                    'description' => 'Number of weeks',
                    'type' => 3,
                    'required' => true,
                ],
                [
                    'name' => 'months',
                    'description' => 'Number of months',
                    'type' => 3,
                    'required' => true,
                ],
                [
                    'name' => 'threemonths',
                    'description' => 'Number of threemonths',
                    'type' => 3,
                    'required' => true,
                ],
                [
                    'name' => 'sixmonths',
                    'description' => 'Number of sixmonths',
                    'type' => 3,
                    'required' => true,
                ],
                [
                    'name' => 'lifetimes',
                    'description' => 'Number of lifetimes',
                    'type' => 3,
                    'required' => true,
                ],
            ],
        ],
        [
            'name' => 'kill-all-sessions',
            'type' => 1,
            'description' => 'Kill All Existing Sessions',
            'default_member_permissions' => 0,
        ],
        [
            'name' => 'kill-session',
            'type' => 1,
            'description' => 'End Selected Session',
            'default_member_permissions' => 0,
            'options' => [
                [
                    'name' => 'sessid',
                    'description' => 'The session id you would like to end.',
                    'type' => 3,
                    'required' => true,
                ],
            ],
        ],
        [
            'name' => 'add-application',
            'type' => 1,
            'description' => 'Add an application / seller key to the database.',
            'default_member_permissions' => 0,
        ],
        [
            'name' => 'set-seller',
            'type' => 1,
            'description' => 'Add an application / seller key to the database.',
            'default_member_permissions' => 0,
        ],
        [
            'name' => 'del-application',
            'type' => 1,
            'description' => 'Delete an application or seller key from the bot.',
            'default_member_permissions' => 0,
        ],
        [
            'name' => 'fetch-application-settings',
            'type' => 1,
            'description' => 'Get Current Settings',
            'default_member_permissions' => 0,
        ],
        [
            'name' => 'pause-application',
            'type' => 1,
            'description' => 'Pause Application',
            'default_member_permissions' => 0,
        ],
        [
            'name' => 'select-application',
            'type' => 1,
            'description' => 'Select an application / seller key to use.',
            'default_member_permissions' => 0,
        ],
        [
            'name' => 'unpause-application',
            'type' => 1,
            'description' => 'Unpause Application',
            'default_member_permissions' => 0,
        ],
        [
            'name' => 'add-subscription',
            'type' => 1,
            'description' => 'Add Subscription',
            'default_member_permissions' => 0,
            'options' => [
                [
                    'name' => 'name',
                    'description' => 'Subscription Name?',
                    'type' => 3,
                    'required' => true,
                ],
                [
                    'name' => 'level',
                    'description' => 'Subscription Level?',
                    'type' => 3,
                    'required' => true,
                ],
            ],
        ],
        [
            'name' => 'count-subscriptions',
            'type' => 1,
            'description' => 'Retrieve user variable',
            'default_member_permissions' => 0,
            'options' => [
                [
                    'name' => 'name',
                    'description' => 'The subscription name',
                    'type' => 3,
                    'required' => true,
                ],
            ],
        ],
        [
            'name' => 'delete-application-subscriptions',
            'type' => 1,
            'description' => 'Delete Exisiting Subscription from application',
            'default_member_permissions' => 0,
            'options' => [
                [
                    'name' => 'name',
                    'description' => 'The name of the subscription you would like to delete.',
                    'type' => 3,
                    'required' => true,
                ],
            ],
        ],
        [
            'name' => 'edit-application-subscriptions',
            'type' => 1,
            'description' => 'Edit Subscription',
            'default_member_permissions' => 0,
            'options' => [
                [
                    'name' => 'sub',
                    'description' => 'The subscription you would like to edit.',
                    'type' => 3,
                    'required' => true,
                ],
                [
                    'name' => 'level',
                    'description' => 'The new level for the subscription.',
                    'type' => 3,
                    'required' => true,
                ],
            ],
        ],
        [
            'name' => 'pause-application-subscriptions',
            'type' => 1,
            'description' => 'Pause Subscription(s)',
            'default_member_permissions' => 0,
            'options' => [
                [
                    'name' => 'subscription',
                    'description' => 'Default \'subscription\'',
                    'type' => 3,
                    'required' => true,
                ],
            ],
        ],
        [
            'name' => 'unpause-subscription',
            'type' => 1,
            'description' => 'Unpause Subscription(s)',
            'default_member_permissions' => 0,
            'options' => [
                [
                    'name' => 'subscription',
                    'description' => 'Default \'subscription\'',
                    'type' => 3,
                    'required' => true,
                ],
            ],
        ],
        [
            'name' => 'activate',
            'type' => 1,
            'description' => 'Activate License Key',
            'default_member_permissions' => 0,
            'options' => [
                [
                    'name' => 'username',
                    'description' => 'Enter username to register',
                    'type' => 3,
                    'required' => true,
                ],
                [
                    'name' => 'license',
                    'description' => 'Enter Valid License',
                    'type' => 3,
                    'required' => true,
                ],
                [
                    'name' => 'password',
                    'description' => 'Enter Password',
                    'type' => 3,
                    'required' => true,
                ],
            ],
        ],
        [
            'name' => 'add-user',
            'type' => 1,
            'description' => 'Add user to application',
            'default_member_permissions' => 0,
            'options' => [
                [
                    'name' => 'user',
                    'description' => 'Username of user you\'re creating',
                    'type' => 3,
                    'required' => true,
                ],
                [
                    'name' => 'sub',
                    'description' => 'Name of subscription you want to assign user upon creation',
                    'type' => 3,
                    'required' => true,
                ],
                [
                    'name' => 'expires',
                    'description' => 'Number in days until subscription assigned upon creation expires',
                    'type' => 3,
                    'required' => true,
                ],
                [
                    'name' => 'pass',
                    'description' => 'Password for user (optional) if not set, will be set later on login',
                    'type' => 3,
                ],
            ],
        ],
        [
            'name' => 'assign-user-variable',
            'type' => 1,
            'description' => 'Assign variable to user(s)',
            'default_member_permissions' => 0,
            'options' => [
                [
                    'name' => 'name',
                    'description' => 'User variable name',
                    'type' => 3,
                    'required' => true,
                ],
                [
                    'name' => 'data',
                    'description' => 'User variable data',
                    'type' => 3,
                    'required' => true,
                ],
                [
                    'name' => 'user',
                    'description' => 'User to set variable of. If you leave blank, all users will be assigned user variable',
                    'type' => 3,
                    'required' => true,
                ],
                [
                    'name' => 'readonly',
                    'description' => 'Whether user var can be changed from program (0 = no, 1 = yes)',
                    'type' => 4,
                ],
            ],
        ],
        [
            'name' => 'ban-user',
            'type' => 1,
            'description' => 'Ban user',
            'default_member_permissions' => 0,
            'options' => [
                [
                    'name' => 'user',
                    'description' => 'User you wish to ban',
                    'type' => 3,
                    'required' => true,
                ],
                [
                    'name' => 'reason',
                    'description' => 'Reason for the ban',
                    'type' => 3,
                    'required' => true,
                ],
            ],
        ],
        [
            'name' => 'delete-all-users',
            'type' => 1,
            'description' => 'Delete All Users',
            'default_member_permissions' => 0,
        ],
        [
            'name' => 'delete-blacklist',
            'type' => 1,
            'description' => 'Delete blacklist',
            'default_member_permissions' => 0,
            'options' => [
                [
                    'name' => 'data',
                    'description' => 'Blacklist data here.',
                    'type' => 3,
                    'required' => true,
                ],
                [
                    'name' => 'blacktype',
                    'description' => 'IP or HWID.',
                    'type' => 3,
                    'required' => true,
                ],
            ],
        ],
        [
            'name' => 'delete-expired-users',
            'type' => 1,
            'description' => 'Delete users with no active subscriptions',
            'default_member_permissions' => 0,
        ],
        [
            'name' => 'delete-user',
            'type' => 1,
            'description' => 'Delete user',
            'default_member_permissions' => 0,
            'options' => [
                [
                    'name' => 'username',
                    'description' => 'Enter Username',
                    'type' => 3,
                    'required' => true,
                ],
            ],
        ],
        [
            'name' => 'delete-user-subscription',
            'type' => 1,
            'description' => 'Delete user\'s subscription',
            'default_member_permissions' => 0,
            'options' => [
                [
                    'name' => 'user',
                    'description' => 'Username of user you\'re deleting subscription from',
                    'type' => 3,
                    'required' => true,
                ],
                [
                    'name' => 'name',
                    'description' => 'Name of subscription you\'re deleting from user',
                    'type' => 3,
                    'required' => true,
                ],
            ],
        ],
        [
            'name' => 'delete-user-variable',
            'type' => 1,
            'description' => 'Delete user variable',
            'default_member_permissions' => 0,
            'options' => [
                [
                    'name' => 'user',
                    'description' => 'Username of user variable you wish to delete',
                    'type' => 3,
                    'required' => true,
                ],
                [
                    'name' => 'name',
                    'description' => 'Name of user variable you wish to delete',
                    'type' => 3,
                    'required' => true,
                ],
            ],
        ],
        [
            'name' => 'edit-email',
            'type' => 1,
            'description' => 'Change User\'s Email Address',
            'default_member_permissions' => 0,
            'options' => [
                [
                    'name' => 'user',
                    'description' => 'The username of the user you would like to change email for.',
                    'type' => 3,
                    'required' => true,
                ],
                [
                    'name' => 'email',
                    'description' => 'New email address for the user to use in forgot() function',
                    'type' => 3,
                    'required' => true,
                ],
            ],
        ],
        [
            'name' => 'edit-username',
            'type' => 1,
            'description' => 'Change User\'s Username',
            'default_member_permissions' => 0,
            'options' => [
                [
                    'name' => 'currentusername',
                    'description' => 'The username of the user you would like to change username for.',
                    'type' => 3,
                    'required' => true,
                ],
                [
                    'name' => 'newusername',
                    'description' => 'Default \'newusername\'',
                    'type' => 3,
                    'required' => true,
                ],
                [
                    'name' => 'sessionid',
                    'description' => 'Default \'sessionid\'',
                    'type' => 3,
                    'required' => true,
                ],
            ],
        ],
        [
            'name' => 'extend-users',
            'type' => 1,
            'description' => 'Extend Users',
            'default_member_permissions' => 0,
            'options' => [
                [
                    'name' => 'username',
                    'description' => 'Enter username to extend',
                    'type' => 3,
                    'required' => true,
                ],
                [
                    'name' => 'subname',
                    'description' => 'Enter Subscription Name',
                    'type' => 3,
                    'required' => true,
                ],
                [
                    'name' => 'expiry',
                    'description' => 'Enter Days Subscription Should Last',
                    'type' => 3,
                    'required' => true,
                ],
                [
                    'name' => 'activeonly',
                    'description' => 'Extend only active subscribers with matching subscriptions: 1 for yes, 0 or omit parameter for no.',
                    'type' => 4,
                    'required' => true,
                ],
            ],
        ],
        [
            'name' => 'get-license-from-user',
            'type' => 1,
            'description' => 'Get License from user',
            'default_member_permissions' => 0,
            'options' => [
                [
                    'name' => 'username',
                    'description' => 'Username where you want the license',
                    'type' => 3,
                    'required' => true,
                ],
            ],
        ],
        [
            'name' => 'pause-user',
            'type' => 1,
            'description' => 'Pause User',
            'default_member_permissions' => 0,
            'options' => [
                [
                    'name' => 'user',
                    'description' => 'The username of the user you would like to pause.',
                    'type' => 3,
                    'required' => true,
                ],
            ],
        ],
        [
            'name' => 'reset-all-users',
            'type' => 1,
            'description' => 'Reset All User\'s HWID',
            'default_member_permissions' => 0,
        ],
        [
            'name' => 'reset-password',
            'type' => 1,
            'description' => 'Reset password of user',
            'default_member_permissions' => 0,
            'options' => [
                [
                    'name' => 'user',
                    'description' => 'Username of user you\'re resetting password of',
                    'type' => 3,
                    'required' => true,
                ],
                [
                    'name' => 'pass',
                    'description' => 'Password for user (optional) if not set, will be set later on login - (default = passwd)',
                    'type' => 3,
                ],
            ],
        ],
        [
            'name' => 'reset-user',
            'type' => 1,
            'description' => 'Reset a user',
            'default_member_permissions' => 0,
            'options' => [
                [
                    'name' => 'username',
                    'description' => 'Username of the user your are resetting HWID for.',
                    'type' => 3,
                    'required' => true,
                ],
            ],
        ],
        [
            'name' => 'subtract',
            'type' => 1,
            'description' => 'Subtract From User Subscription',
            'default_member_permissions' => 0,
            'options' => [
                [
                    'name' => 'user',
                    'description' => 'The username of the user you are subtracting time form.',
                    'type' => 3,
                    'required' => true,
                ],
                [
                    'name' => 'sub',
                    'description' => 'Their subscription name',
                    'type' => 3,
                    'required' => true,
                ],
                [
                    'name' => 'seconds',
                    'description' => 'Time to subtract from their subscription',
                    'type' => 4,
                    'required' => true,
                ],
            ],
        ],
        [
            'name' => 'unpause-user',
            'type' => 1,
            'description' => 'Unpause User',
            'default_member_permissions' => 0,
            'options' => [
                [
                    'name' => 'username',
                    'description' => 'The username of the user you would like to unpause.',
                    'type' => 3,
                    'required' => true,
                ],
            ],
        ],
        [
            'name' => 'unban-user',
            'type' => 1,
            'description' => 'Unban User',
            'default_member_permissions' => 0,
            'options' => [
                [
                    'name' => 'username',
                    'description' => 'The username of the user you would like to unban.',
                    'type' => 3,
                    'required' => true,
                ],
            ],
        ],
        [
            'name' => 'retrieve-user-data',
            'type' => 1,
            'description' => 'Retrieve info from a user',
            'default_member_permissions' => 0,
            'options' => [
                [
                    'name' => 'user',
                    'description' => 'Specify user to lookup',
                    'type' => 3,
                    'required' => true,
                ],
            ],
        ],
        [
            'name' => 'verify-user',
            'type' => 1,
            'description' => 'Verify user exists',
            'default_member_permissions' => 0,
            'options' => [
                [
                    'name' => 'user',
                    'description' => 'Username of user you would like to check the existence of',
                    'type' => 3,
                    'required' => true,
                ],
            ],
        ],
        [
            'name' => 'add-application-hash',
            'type' => 1,
            'description' => 'Add Additional Hash to your Application',
            'default_member_permissions' => 0,
            'options' => [
                [
                    'name' => 'hash',
                    'description' => 'MD5 hash you want to add',
                    'type' => 3,
                    'required' => true,
                ],
            ],
        ],
        [
            'name' => 'add-hardwareid',
            'type' => 1,
            'description' => 'Add HWID',
            'default_member_permissions' => 0,
            'options' => [
                [
                    'name' => 'username',
                    'description' => 'Enter Username',
                    'type' => 3,
                    'required' => true,
                ],
                [
                    'name' => 'hwid',
                    'description' => 'Enter Additional HWID',
                    'type' => 3,
                    'required' => true,
                ],
            ],
        ],
        [
            'name' => 'fetch',
            'type' => 1,
            'description' => 'Fetch * All Things',
            'default_member_permissions' => 0,
            'options' => [
                [
                    'name' => 'licenses',
                    'description' => 'Fetch All Licenses',
                    'type' => 1,
                ],
                [
                    'name' => 'users',
                    'description' => 'Fetch All Users',
                    'type' => 1,
                ],
                [
                    'name' => 'user-vars',
                    'description' => 'Fetch All User\'s Variables',
                    'type' => 1,
                ],
                [
                    'name' => 'usernames',
                    'description' => 'Fetch All Usernames',
                    'type' => 1,
                ],
                [
                    'name' => 'subs',
                    'description' => 'Fetch All Subs',
                    'type' => 1,
                ],
                [
                    'name' => 'chats',
                    'description' => 'Fetch All Chats',
                    'type' => 1,
                ],
                [
                    'name' => 'sessions',
                    'description' => 'Fetch All Sessions',
                    'type' => 1,
                ],
                [
                    'name' => 'files',
                    'description' => 'Fetch All Files',
                    'type' => 1,
                ],
                [
                    'name' => 'vars',
                    'description' => 'Fetch All Vars',
                    'type' => 1,
                ],
                [
                    'name' => 'blacklists',
                    'description' => 'Fetch All Blacklists',
                    'type' => 1,
                ],
                [
                    'name' => 'webhooks',
                    'description' => 'Fetch All Webhooks',
                    'type' => 1,
                ],
                [
                    'name' => 'buttons',
                    'description' => 'Fetch All Buttons',
                    'type' => 1,
                ],
                [
                    'name' => 'mutes',
                    'description' => 'Fetch All Mutes',
                    'type' => 1,
                ],
                [
                    'name' => 'channels',
                    'description' => 'Fetch All Channels',
                    'type' => 1,
                ],
                [
                    'name' => 'appdetails',
                    'description' => 'Fetch Application Details',
                    'type' => 1,
                ],
            ],
        ],
        [
            'name' => 'fetch-application-stats',
            'type' => 1,
            'description' => 'Application Statistics',
            'default_member_permissions' => 0,
        ],
        [
            'name' => 'help',
            'type' => 1,
            'description' => 'Help command for bot',
            'default_member_permissions' => 0,
        ],
        [
            'name' => 'reset-application-hash',
            'type' => 1,
            'description' => 'Reset app hash',
            'default_member_permissions' => 0,
        ],
        [
            'name' => 'add-variable',
            'type' => 1,
            'description' => 'Add application variable',
            'default_member_permissions' => 0,
            'options' => [
                [
                    'name' => 'name',
                    'description' => 'Variable Name?',
                    'type' => 3,
                    'required' => true,
                ],
                [
                    'name' => 'value',
                    'description' => 'Variable Value?',
                    'type' => 3,
                    'required' => true,
                ],
                [
                    'name' => 'authed',
                    'description' => 'Determines whether user needs to be logged in (1) or not (0)',
                    'type' => 3,
                    'required' => true,
                ],
            ],
        ],
        [
            'name' => 'delete-all-variables',
            'type' => 1,
            'description' => 'Delete All Variables',
            'default_member_permissions' => 0,
        ],
        [
            'name' => 'delete-variable',
            'type' => 1,
            'description' => 'Delete Variable',
            'default_member_permissions' => 0,
            'options' => [
                [
                    'name' => 'name',
                    'description' => 'The var name you would like to delete.',
                    'type' => 3,
                    'required' => true,
                ],
            ],
        ],
        [
            'name' => 'edit-variable',
            'type' => 1,
            'description' => 'Edit Variable',
            'default_member_permissions' => 0,
            'options' => [
                [
                    'name' => 'name',
                    'description' => 'Variable Name?',
                    'type' => 3,
                    'required' => true,
                ],
                [
                    'name' => 'value',
                    'description' => 'Variable Value?',
                    'type' => 3,
                    'required' => true,
                ],
            ],
        ],
        [
            'name' => 'mass-delete-user-variables',
            'type' => 1,
            'description' => 'Delete All User Variables With Name',
            'default_member_permissions' => 0,
            'options' => [
                [
                    'name' => 'name',
                    'description' => 'The name of the subscription.',
                    'type' => 3,
                    'required' => true,
                ],
            ],
        ],
        [
            'name' => 'retrieve-user-variable',
            'type' => 1,
            'description' => 'Retrieve user variable',
            'default_member_permissions' => 0,
            'options' => [
                [
                    'name' => 'user',
                    'description' => 'Username of user you want to retrieve user variable from',
                    'type' => 3,
                    'required' => true,
                ],
                [
                    'name' => 'name',
                    'description' => 'Name of user variable you want to retrieve',
                    'type' => 3,
                    'required' => true,
                ],
            ],
        ],
        [
            'name' => 'retrieve-variable',
            'type' => 1,
            'description' => 'Retrieve Variable',
            'default_member_permissions' => 0,
            'options' => [
                [
                    'name' => 'name',
                    'description' => 'The var name you would like to retrieve.',
                    'type' => 3,
                    'required' => true,
                ],
            ],
        ],
        [
            'name' => 'add-webloader-button',
            'type' => 1,
            'description' => 'Create New Web Loader Button',
            'default_member_permissions' => 0,
            'options' => [
                [
                    'name' => 'value',
                    'description' => 'Value of the web loader button',
                    'type' => 3,
                    'required' => true,
                ],
                [
                    'name' => 'text',
                    'description' => 'The text for the web loader button.',
                    'type' => 3,
                    'required' => true,
                ],
            ],
        ],
        [
            'name' => 'del-webloader-button',
            'type' => 1,
            'description' => 'Delete Web Loader Button',
            'default_member_permissions' => 0,
            'options' => [
                [
                    'name' => 'value',
                    'description' => 'The value of the web loader button',
                    'type' => 3,
                ],
            ],
        ],
        [
            'name' => 'create-webhook',
            'type' => 1,
            'description' => 'Add webhook to application',
            'default_member_permissions' => 0,
            'options' => [
                [
                    'name' => 'baseurl',
                    'description' => 'URL that\'s hidden on keyauth server',
                    'type' => 3,
                    'required' => true,
                ],
                [
                    'name' => 'useragent',
                    'description' => 'User agent, optional. If not set, it will default to keyauth ',
                    'type' => 3,
                    'required' => true,
                ],
                [
                    'name' => 'authed',
                    'description' => 'Determines whether user needs to be logged in (1) or not (0)',
                    'type' => 3,
                    'required' => true,
                ],
            ],
        ],
        [
            'name' => 'delete-all-webhooks',
            'type' => 1,
            'description' => 'Delete All Webhooks',
            'default_member_permissions' => 0,
        ],
        [
            'name' => 'add-whitelist',
            'type' => 1,
            'description' => 'Delete Existing Whitelist',
            'default_member_permissions' => 0,
            'options' => [
                [
                    'name' => 'ip',
                    'description' => 'The IP you would like to delete from whitelist.',
                    'type' => 3,
                    'required' => true,
                ],
            ],
        ],
        [
            'name' => 'delete-whitelist',
            'type' => 1,
            'description' => 'Delete Existing Whitelist',
            'default_member_permissions' => 0,
            'options' => [
                [
                    'name' => 'ip',
                    'description' => 'The IP you would like to delete from whitelist.',
                    'type' => 3,
                    'required' => true,
                ],
            ],
        ],
    ];

    $curl = curl_init($url);
    curl_setopt($curl, CURLOPT_URL, $url);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

    $headers = array(
        "Authorization: Bot $botToken",
        "user-agent: KeyAuth"
    );
    curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
    $resp = curl_exec($curl);
    $httpcode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
    $json = json_decode($resp);

    if ($httpcode > 399) {
        dashboard\primary\error("Discord API error fetching application. Code: $httpcode");
    } else {
        $clientId = $json->id;
        $pubKey = $json->verify_key;

        $query = misc\mysql\query("INSERT INTO `customBots` (`clientId`, `token`, `pubKey`, `app`) VALUES (?, ?, ?, ?)", [$clientId, $botToken, $pubKey, $_SESSION['app']]);
        if ($query->affected_rows > 0) {
            $url = "https://discord.com/api/applications/@me";

            $curl = curl_init($url);
            curl_setopt($curl, CURLOPT_URL, $url);
            curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "PATCH");
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode(array("interactions_endpoint_url" => "https://keyauth.win/api/discord-interactions")));

            $headers = array(
                "Authorization: Bot $botToken",
                "user-agent: KeyAuth",
                "Content-Type: application/json"
            );
            curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);

            $resp = curl_exec($curl);
            $httpcode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
            if ($httpcode < 399) {
                $url = "https://discord.com/api/applications/$clientId/commands";

                $curl = curl_init($url);
                curl_setopt($curl, CURLOPT_URL, $url);
                curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "PUT");
                curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($commands));

                $headers = array(
                    "Authorization: Bot $botToken",
                    "user-agent: KeyAuth",
                    "Content-Type: application/json"
                );
                curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);

                $resp = curl_exec($curl);
                $httpcode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
            }
        } else {
            dashboard\primary\error("Unable to insert custom bot, please try again.");
        }
    }
}
?>

<?php
    $query = misc\mysql\query("SELECT `sellerkey`, `sellerApiWhitelist` FROM `apps` WHERE `secret` = ?",[$_SESSION['app']]);
    if ($query->num_rows > 0) {
        while ($row = mysqli_fetch_array($query->result)) {
            $sellerkey = $row['sellerkey'];
            $whitelistedIp = $row['sellerApiWhitelist'];
        }
    }
?>

<div class="p-4 bg-[#09090d] block sm:flex items-center justify-between lg:mt-1.5">
    <div class="mb-1 w-full bg-[#0f0f17] rounded-xl">
        <div class="mb-4 p-4">
            <?php require '../app/layout/breadcrumb.php'; ?>
            <h1 class="text-xl font-semibold text-white-900 sm:text-2xl ">Seller Settings</h1>
            <p class="text-xs text-gray-500">Manage your seller settings here. <a
                    href="https://keyauthdocs.apidog.io/sellerapi/licenses/create-new-license" target="_blank"
                    class="text-blue-600  hover:underline">Learn More</a>.</p>
            <!-- Alert Box -->
            <div id="alert" class="flex items-center p-4 mb-4 text-yellow-800 rounded-lg bg-[#09090d]"
                role="alert">
                <svg class="flex-shrink-0 w-4 h-4" aria-hidden="true" xmlns="http://www.w3.org/2000/svg"
                    fill="currentColor" viewBox="0 0 20 20">
                    <path
                        d="M10 .5a9.5 9.5 0 1 0 9.5 9.5A9.51 9.51 0 0 0 10 .5ZM9.5 4a1.5 1.5 0 1 1 0 3 1.5 1.5 0 0 1 0-3ZM12 15H8a1 1 0 0 1 0-2h1v-3H8a1 1 0 0 1 0-2h2a1 1 0 0 1 1 1v4h1a1 1 0 0 1 0 2Z" />
                </svg>
                <span class="sr-only">Info</span>
                <div class="ml-3 text-sm font-medium text-yellow-500">
                    Check out <a
                        href="https://vaultcord.com/?utm_source=keyauth" target="_blank"
                        class="font-semibold underline hover:no-underline">VaultCord</a> - Free Discord backup bot. Backup all members & your entire Discord server! Avoid Discord term waves and server nukes <a
                        href="https://vaultcord.com/?utm_source=keyauth" target="_blank"
                        class="font-semibold underline hover:no-underline">(click here)</a>
                </div>
            </div>
            <!-- End Alert Box -->

            <div class="mb-4 border-b border-gray-200  ">
                <ul class="flex flex-wrap -mb-px text-sm font-medium text-center" id="myTab"
                    data-tabs-toggle="#myTabContent" role="tablist">
                    <li class="mr-2" role="presentation">
                        <button class="inline-flex p-4 border-b-2 rounded-t-lg" id="sellerkey-tab"
                            data-tabs-target="#sellerkey" type="button" role="tab" aria-controls="sellerkey"
                            aria-selected="false" data-popover-target="sellerkey-popover">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 mr-1" viewBox="0 0 24 24">
                                <path fill="currentColor"
                                    d="M6 22q-.825 0-1.412-.587T4 20V10q0-.825.588-1.412T6 8h1V6q0-2.075 1.463-3.537T12 1q2.075 0 3.538 1.463T17 6v2h1q.825 0 1.413.588T20 10v10q0 .825-.587 1.413T18 22zm6-5q.825 0 1.413-.587T14 15q0-.825-.587-1.412T12 13q-.825 0-1.412.588T10 15q0 .825.588 1.413T12 17M9 8h6V6q0-1.25-.875-2.125T12 3q-1.25 0-2.125.875T9 6z" />
                            </svg>

                            Seller Key
                        </button>
                        <?php dashboard\primary\popover("sellerkey-popover", "Seller key", "View seller key & control it"); ?>
                    </li>
                    <li class="mr-2" role="presentation">
                        <button class="inline-flex p-4 border-b-2 rounded-t-lg" id="discord-tab"
                            data-tabs-target="#discordtab" type="button" role="tab" aria-controls="discordtab"
                            aria-selected="false" data-popover-target="discord-popover">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 mr-1" viewBox="0 0 24 24">
                                <path fill="currentColor"
                                    d="M19.27 5.33C17.94 4.71 16.5 4.26 15 4a.09.09 0 0 0-.07.03c-.18.33-.39.76-.53 1.09a16.09 16.09 0 0 0-4.8 0c-.14-.34-.35-.76-.54-1.09c-.01-.02-.04-.03-.07-.03c-1.5.26-2.93.71-4.27 1.33c-.01 0-.02.01-.03.02c-2.72 4.07-3.47 8.03-3.1 11.95c0 .02.01.04.03.05c1.8 1.32 3.53 2.12 5.24 2.65c.03.01.06 0 .07-.02c.4-.55.76-1.13 1.07-1.74c.02-.04 0-.08-.04-.09c-.57-.22-1.11-.48-1.64-.78c-.04-.02-.04-.08-.01-.11c.11-.08.22-.17.33-.25c.02-.02.05-.02.07-.01c3.44 1.57 7.15 1.57 10.55 0c.02-.01.05-.01.07.01c.11.09.22.17.33.26c.04.03.04.09-.01.11c-.52.31-1.07.56-1.64.78c-.04.01-.05.06-.04.09c.32.61.68 1.19 1.07 1.74c.03.01.06.02.09.01c1.72-.53 3.45-1.33 5.25-2.65c.02-.01.03-.03.03-.05c.44-4.53-.73-8.46-3.1-11.95c-.01-.01-.02-.02-.04-.02M8.52 14.91c-1.03 0-1.89-.95-1.89-2.12s.84-2.12 1.89-2.12c1.06 0 1.9.96 1.89 2.12c0 1.17-.84 2.12-1.89 2.12m6.97 0c-1.03 0-1.89-.95-1.89-2.12s.84-2.12 1.89-2.12c1.06 0 1.9.96 1.89 2.12c0 1.17-.83 2.12-1.89 2.12" />
                            </svg>

                            Discord Bot
                        </button>
                        <?php dashboard\primary\popover("discord-popover", "Discord Bot", "Use KeyAuth's public bot or your own custom bot"); ?>
                    </li>
                </ul>
            </div>

            <form method="post">
                <div id="myTabContent">
                    <div class="hidden p-4 rounded-lg grid gap-4" id="sellerkey" role="tabpanel"
                        aria-labelledby="sellerkey-tab">
                        <a href="https://keyauthdocs.apidog.io/sellerapi/licenses/create-new-license" type="button"
                            target="_blank"
                            class="inline-flex text-white bg-blue-700 hover:opacity-60 focus:ring-0 font-medium rounded-lg text-sm px-5 py-2.5 transition duration-200">
                            <i class="lni lni-website mr-2 mt-1"></i>View Documentation
                        </a>

                        <button type="button"
                            class="inline-flex text-white bg-green-700 hover:opacity-60 focus:ring-0 font-medium rounded-lg text-sm px-5 py-2.5 transition duration-200"
                            data-modal-target="whitelist-seller-ip" data-modal-toggle="whitelist-seller-ip">
                            <i class="lni lni-circle-plus mr-2 mt-1"></i>Whitelist IP
                        </button>

                        <button type="button"
                            class="inline-flex text-white bg-red-700 hover:opacity-60 focus:ring-0 font-medium rounded-lg text-sm px-5 py-2.5 mb-2 transition duration-200"
                            data-modal-toggle="refresh-seller-key-modal" data-modal-target="refresh-seller-key-modal">
                            <i class="lni lni-reload mr-2 mt-1"></i>Reset Seller Key
                        </button>

                        <div class="relative mb-4">
                            <input type="url" id="sellerLink" name="sellerLink"
                                class="transition duration-500 block px-2.5 pb-2.5 pt-4 w-full text-sm text-white-900 bg-transparent rounded-lg border-1 border-gray-700 appearance-none    focus:outline-none focus:ring-0 focus:border-blue-600 peer blur-sm hover:blur-none"
                                value="<?= 'https://' . (($_SERVER['HTTP_HOST'] == "keyauth.cc") ? "keyauth.win" : $_SERVER['HTTP_HOST']) . '/api/seller/?sellerkey=' . $sellerkey . '&type=add&expiry=1&mask=******-******-******-******-******-******&level=1&amount=1&format=text' ?>"
                                placeholder=" " / data-popover-target="sellerLink-popover" disabled>
                            <label for="sellerLink"
                                class="absolute text-sm text-gray-500 duration-300 transform -translate-y-4 scale-75 top-2 z-10 origin-[0] bg-[#0f0f17] px-2 peer-focus:px-2 peer-focus:text-blue-600 peer-focus:text-blue-500 peer-placeholder-shown:scale-100 peer-placeholder-shown:-translate-y-1/2 peer-placeholder-shown:top-1/2 peer-focus:top-2 peer-focus:scale-75 peer-focus:-translate-y-4 left-1">Seller
                                API Link</label>
                            <div data-popover id="sellerLink-popover" role="tooltip"
                                class="absolute z-10 invisible inline-block w-64 text-sm text-gray-500 transition-opacity duration-300 bg-[#09090d] rounded-lg shadow-sm opacity-0">
                                <div class="px-3 py-2 bg-[#09090d]/70 rounded-t-lg">
                                    <h3 class="font-semibold text-white">Seller API Link</h3>
                                </div>
                                <div class="px-3 py-2">
                                    <p>Use this URL in Sellix or SellApp to generate key on-demand.</p>
                                </div>
                                <div data-popper-arrow></div>
                            </div>
                        </div>
                        <div class="relative mb-4">
                        <input type="text" id="sellerkeydisplay" name="sellerkeydisplay"
                            class="transition duration-500 block px-2.5 pb-2.5 pt-4 w-full text-sm text-white bg-transparent rounded-lg border-1 border-gray-700 appearance-none focus:ring-0 peer blur-sm hover:blur-none"
                            value="<?= $sellerkey; ?>" readonly></input>
                        <label for="sellerkeydisplay"
                            class="absolute text-sm text-gray-400 duration-300 transform -translate-y-4 scale-75 top-2 z-10 origin-[0] bg-[#0f0f17] px-2 peer-focus:px-2 peer-focus:text-blue-600 peer-placeholder-shown:scale-100 peer-placeholder-shown:-translate-y-1/2 peer-placeholder-shown:top-1/2 peer-focus:top-2 peer-focus:scale-75 peer-focus:-translate-y-4 left-1">Seller
                            Key</label>
                    </div>
                    </div>
                    <div class="hidden p-4 rounded-lg grid gap-7" id="discordtab" role="tabpanel"
                        aria-labelledby="discord-tab">

                        <div id="alert" class="flex items-center p-4 mb-4 text-yellow-800 rounded-lg bg-[#09090d]"
                            role="alert">
                            <svg class="flex-shrink-0 w-4 h-4" aria-hidden="true" xmlns="http://www.w3.org/2000/svg"
                                fill="currentColor" viewBox="0 0 20 20">
                                <path
                                    d="M10 .5a9.5 9.5 0 1 0 9.5 9.5A9.51 9.51 0 0 0 10 .5ZM9.5 4a1.5 1.5 0 1 1 0 3 1.5 1.5 0 0 1 0-3ZM12 15H8a1 1 0 0 1 0-2h1v-3H8a1 1 0 0 1 0-2h2a1 1 0 0 1 1 1v4h1a1 1 0 0 1 0 2Z" />
                            </svg>
                            <div class="ml-3 text-sm font-medium text-yellow-500">
                                When using the Discord bot, you must first use the "/add-application" command then the "/select-application" command, lastly make sure
                                you have a role called "perms"
                            </div>
                        </div>
                        <a type="button"
                            class="inline-flex text-white bg-blue-700 hover:opacity-60 focus:ring-0 font-medium rounded-lg text-sm px-3 py-2.5 mb-2 transition duration-200 cursor-pointer"
                            target="popup" rel="noreferrer"
                            onclick="window.open(' https://discord.com/oauth2/authorize?client_id=1275231421790945353','popup','width=500,height=800,noopener'); return false;">
                            <i class="lni lni-discord-alt mr-2 mt-1"></i> Add Discord Bot
                        </a>
                    </div>
                </div>
            </div>
        </form>

        <!-- Refresh Seller Key Modal -->
        <div id="refresh-seller-key-modal" tabindex="-1"
            class="fixed top-0 left-0 right-0 z-50 hidden p-4 overflow-x-hidden overflow-y-auto md:inset-0 h-[calc(100%-1rem)] max-h-full">
            <div class="relative w-full max-w-md max-h-full">
                <div class="relative bg-[#0f0f17] border border-red-700 rounded-lg shadow">
                    <div class="p-6 text-center">
                        <div class="flex items-center p-4 mb-4 text-sm text-white border border-yellow-500 rounded-lg bg-[#0f0f17]"
                            role="alert">
                            <span class="sr-only">Info</span>
                            <div>
                                <span class="font-medium">Notice!</span> You're about to reset your seller
                                key. This can not be undone.
                                </b>
                            </div>
                        </div>
                        <h3 class="mb-5 text-lg font-normal text-gray-200">Are you sure you want to reset
                            your seller key?</h3>
                        <form method="POST">
                            <button data-modal-hide="refresh-seller-key-modal" name="refreshseller"
                                class="text-white bg-red-600 hover:bg-red-800 focus:ring-4 focus:outline-none focus:ring-red-300 font-medium rounded-lg text-sm inline-flex items-center px-5 py-2.5 text-center mr-2">
                                Yes, I'm sure
                            </button>
                            <button data-modal-hide="refresh-seller-key-modal" type="button"
                                class="inline-flex text-white bg-gray-700 hover:opacity-60 focus:ring-0 font-medium rounded-lg text-sm px-5 py-2.5 mb-2 transition duration-200">No,
                                cancel</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <!-- End Delete All Unused Keys Modal -->

        <!-- Whitelist IP Seller Key Modal -->
        <div id="whitelist-seller-ip" tabindex="-1"
            class="fixed top-0 left-0 right-0 z-50 hidden p-4 overflow-x-hidden overflow-y-auto md:inset-0 h-[calc(100%-1rem)] max-h-full">
            <div class="relative w-full max-w-md max-h-full">
                <div class="relative bg-[#0f0f17] border border-red-700 rounded-lg shadow">
                    <div class="p-6 text-center">
                        <form method="post">
                            <div class="flex items-center p-4 mb-4 text-sm text-white border border-yellow-500 rounded-lg bg-[#0f0f17]"
                                role="alert">
                                <span class="sr-only">Info</span>
                                <div>
                                    <span class="font-medium">Notice!</span> You're about to whitelist this
                                    IP,
                                    restricting all other IPs access to any seller api function with your
                                    seller
                                    key!
                                    </b>
                                </div>
                            </div>

                            <div class="relative mb-4">
                                <input type="text" id="whitelistedIp" name="whitelistedIp"
                                    class="block px-2.5 pb-2.5 pt-4 w-full text-sm text-white bg-transparent rounded-lg border-1 border-gray-700 appearance-none focus:ring-0 peer blur-sm hover:blur-none"
                                    value="<?= $whitelistedIp;?>" placeholder="0.0.0.0">

                                <label for="whitelistedIp"
                                    class="absolute text-sm text-gray-400 duration-300 transform -translate-y-4 scale-75 top-2 z-10 origin-[0] bg-[#0f0f17] px-2 peer-focus:px-2 peer-focus:text-blue-600 peer-placeholder-shown:scale-100 peer-placeholder-shown:-translate-y-1/2 peer-placeholder-shown:top-1/2 peer-focus:top-2 peer-focus:scale-75 peer-focus:-translate-y-4 left-1">Whitelist
                                    IP</label>
                            </div>

                            <button data-modal-hide="whitelist-seller-ip" name="whitelist"
                                class="text-white bg-green-600 hover:bg-green-800 focus:ring-4 focus:outline-none focus:ring-green-300 font-medium rounded-lg text-sm inline-flex items-center px-5 py-2.5 text-center mr-2">
                                Yes, I'm sure
                            </button>
                            <button name="delSellerWhitelist"
                                class="text-white bg-red-600 hover:bg-red-800 focus:ring-4 focus:outline-none focus:ring-red-300 font-medium rounded-lg text-sm inline-flex items-center px-5 py-2.5 text-center mr-2">
                                Clear Current IP
                            </button>
                            <button data-modal-hide="whitelist-seller-ip" type="button"
                                class="inline-flex text-white bg-gray-700 hover:opacity-60 focus:ring-0 font-medium rounded-lg text-sm px-5 py-2.5 mb-2 transition duration-200">No,
                                cancel</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <!-- End Whitelist IP Seller Key Modal -->