<?php

$page = isset($_GET['page']) ? $_GET['page'] : "manage-apps";

?>

<style>
/* Hide vertical scrollbar for Webkit-based browsers (e.g., Chrome, Safari) */
*::-webkit-scrollbar {
    display: none;
}
</style>

<aside id="sidebar"
    class="flex hidden fixed top-0 left-0 z-20 flex-col flex-shrink-0 pt-16 w-64 h-full duration-200 lg:flex transition-width"
    aria-label="Sidebar">
    <div class="flex relative flex-col flex-1 pt-0 min-h-0 bg-[#0f0f17] border-r border-[#0f0f17]">
        <div class="flex overflow-y-auto flex-col flex-1 pt-5 pb-4">
            <div class="flex-1 px-3 space-y-1 bg-[#0f0f17] mt-8">
                <?php require '../app/layout/profile.php';?>
                <div class="mb-4 border-b border-[#0f0f17]">
                    <ul class="grid grid-cols-3 -mb-px text-sm font-medium text-center" id="myTab"
                        data-tabs-toggle="#myTabContent" role="tablist">
                        <?php if (isset($_SESSION["app"])){ ?>
                        <li class="mr-2" role="presentation">
                            <button class="inline-block p-4 rounded-t-lg hover:opacity-60" id="app-tab"
                                data-tabs-target="#app" type="button" role="tab" aria-controls="app" data-tbt="app"
                                aria-selected="false" data-popover-target="app-popover">App</button>
                                <?php dashboard\primary\popover("app-popover", "Application", "Find everything related to your application here"); ?>
                        </li>
                        <?php } ?>
                        <?php if ($role == "seller" && isset($_SESSION["app"])){ ?>
                        <li class="mr-2" role="presentation">
                            <button class="inline-block p-4 rounded-t-lg hover:opacity-60" id="seller-tab"
                                data-tabs-target="#seller" type="button" role="tab" data-tbt="seller"
                                aria-controls="seller" aria-selected="false"
                                data-popover-target="seller-popover">Seller</button>
                                <?php dashboard\primary\popover("seller-popover", "Seller", "Everything related to the Seller API can be found here."); ?>
                        </li>
                        <?php } ?>
                        <li class="mr-2" role="presentation">
                            <button class="inline-block p-4 rounded-t-lg hover:opacity-60" id="account-tab"
                                data-tabs-target="#account" type="button" role="tab" data-tbt="account"
                                aria-controls="account" aria-selected="false"
                                data-popover-target="account-popover">Account</button>
                                <?php dashboard\primary\popover("account-popover", "Account", "Find everything related to your Account here."); ?>
                        </li>
                    </ul>
                </div>
                <div id="myTabContent">
                    <div class="hidden p-4 rounded-lg" id="app" role="tabpanel" aria-labelledby="app-tab">
                        <ul class="space-y-2 font-medium">
                            <?php if (!($role == "Manager" && !($permissions & 1))){ ?>
                            <li>
                                <a href="?page=licenses"
                                    class="flex items-center p-2 rounded-lg text-gray-300 hover:opacity-60 hover:bg-blue-700 group">
                                    <i class="lni lni-key"></i>
                                    <span class="ml-3">Licenses</span>
                                </a>
                            </li>
                            <?php } ?>

                            <?php if (!($role == "Manager" && !($permissions & 2))){ ?>
                            <li>
                                <a href="?page=users"
                                    class="flex items-center p-2 rounded-lg text-gray-300 hover:opacity-60 hover:bg-blue-700 group">
                                    <i class="lni lni-users"></i>
                                    <span class="ml-3">Users</span>
                                </a>
                            </li>
                            <?php } ?>

                            <?php if (!($role == "Manager" && !($permissions & 2048))){ ?>
                            <li>
                                <a href="?page=tokens"
                                    class="flex items-center p-2 rounded-lg text-gray-300 hover:opacity-60 hover:bg-blue-700 group">
                                    <i class="lni lni-tag"></i>
                                    <span class="ml-3">Tokens</span>
                                </a>
                            </li>
                            <?php } ?>

                            <?php if (!($role == "Manager" && !($permissions & 4))){ ?>
                            <li>
                                <a href="?page=subscriptions"
                                    class="flex items-center p-2 rounded-lg text-gray-300 hover:opacity-60 hover:bg-blue-700 group">
                                    <i class="lni lni-crown"></i>
                                    <span class="ml-3">Subscriptions</span>
                                </a>
                            </li>
                            <?php } ?>

                            <?php if (!($role == "Manager" && !($permissions & 8))){ ?>
                            <li>
                                <a href="?page=chats"
                                    class="flex items-center p-2 rounded-lg text-gray-300 hover:opacity-60 hover:bg-blue-700 group">
                                    <i class="lni lni-popup"></i>
                                    <span class="ml-3">Chats</span>
                                </a>
                            </li>
                            <?php } ?>

                            <?php if (!($role == "Manager" && !($permissions & 16))){ ?>
                            <li>
                                <a href="?page=sessions"
                                    class="flex items-center p-2 rounded-lg text-gray-300 hover:opacity-60 hover:bg-blue-700 group">
                                    <i class="lni lni-timer"></i>
                                    <span class="ml-3">Sessions</span>
                                </a>
                            </li>
                            <?php } ?>

                            <?php if (!($role == "Manager" && !($permissions & 32))){ ?>
                            <li>
                                <a href="?page=webhooks"
                                    class="flex items-center p-2 rounded-lg text-gray-300 hover:opacity-60 hover:bg-blue-700 group">
                                    <i class="lni lni-webhooks"></i>
                                    <span class="ml-3">Webhooks</span>
                                </a>
                            </li>
                            <?php } ?>

                            <?php if (!($role == "Manager" && !($permissions & 64))){ ?>
                            <li>
                                <a href="?page=files"
                                    class="flex items-center p-2 rounded-lg text-gray-300 hover:opacity-60 hover:bg-blue-700 group">
                                    <i class="lni lni-files"></i>
                                    <span class="ml-3">Files</span>
                                </a>
                            </li>
                            <?php } ?>

                            <?php if (!($role == "Manager" && !($permissions & 128))){ ?>
                            <li>
                                <a href="?page=vars"
                                    class="flex items-center p-2 rounded-lg text-gray-300 hover:opacity-60 hover:bg-blue-700 group">
                                    <i class="lni lni-code"></i>
                                    <span class="ml-3">Variables</span>
                                </a>
                            </li>
                            <?php } ?>

                            <?php if (!($role == "Manager" && !($permissions & 256))){ ?>
                            <li>
                                <a href="?page=logs"
                                    class="flex items-center p-2 rounded-lg text-gray-300 hover:opacity-60 hover:bg-blue-700 group">
                                    <i class="lni lni-archive"></i>
                                    <span class="ml-3">Logs</span>
                                </a>
                            </li>
                            <?php } ?>

                            <?php if (!($role == "Manager" && !($permissions & 512))){ ?>
                            <li>
                                <a href="?page=blacklists"
                                    class="flex items-center p-2 rounded-lg text-gray-300 hover:opacity-60 hover:bg-blue-700 group">
                                    <i class="lni lni-ban"></i>
                                    <span class="ml-3">Blacklists</span>
                                </a>
                            </li>
                            <?php } ?>

                            <?php if (!($role == "Manager" && !($permissions & 4096))){ ?>
                            <li>
                                <a href="?page=whitelists"
                                    class="flex items-center p-2 rounded-lg text-gray-300 hover:opacity-60 hover:bg-blue-700 group">
                                    <i class="lni lni-checkmark"></i>
                                    <span class="ml-3">Whitelists</span>
                                </a>
                            </li>
                            <?php } ?>

                            <?php if (!($role == "Manager" && !($permissions & 1024))){ ?>
                            <li>
                                <a href="?page=app-settings"
                                    class="flex items-center p-2 rounded-lg text-gray-300 hover:opacity-60 hover:bg-blue-700 group">
                                    <i class="lni lni-cog"></i>
                                    <span class="ml-3">Settings</span>
                                </a>
                            </li>
                            <?php } ?>

                            <?php if ($role != "Manager"){ ?>
                            <li>
                                <a href="?page=audit-logs"
                                    class="flex items-center p-2 rounded-lg text-gray-300 hover:opacity-60 hover:bg-blue-700 group">
                                    <i class="lni lni-archive"></i>
                                    <span class="ml-3">Audit Logs</span>
                                </a>
                            </li>
                            <?php } ?>
                        </ul>
                    </div>
                    <div class="hidden p-4 rounded-lg " id="seller" role="tabpanel" aria-labelledby="seller-tab">
                        <ul class="space-y-2 font-medium">
                            <li>
                                <a href="?page=seller-settings"
                                    class="flex items-center p-2 rounded-lg text-gray-300 hover:opacity-60 hover:bg-blue-700 group">
                                    <i class="lni lni-cog"></i>
                                    <span class="ml-3">Settings</span>
                                </a>
                            </li>
                            <li>
                                <a href="?page=webloader"
                                    class="flex items-center p-2 rounded-lg text-gray-300 hover:opacity-60 hover:bg-blue-700 group">
                                    <i class="lni lni-world"></i>
                                    <span class="ml-3">Web Loader</span>
                                </a>
                            </li>
                            <li>
                                <a href="?page=seller-logs"
                                    class="flex items-center p-2 rounded-lg text-gray-300 hover:opacity-60 hover:bg-blue-700 group">
                                    <i class="lni lni-archive"></i>
                                    <span class="ml-3">Logs</span>
                                </a>
                            </li>
                        </ul>
                    </div>
                    <div class="hidden p-4 rounded-lg " id="account" role="tabpanel" aria-labelledby="account-tab">
                        <ul class="space-y-2 font-medium">
                            <?php
                            if (!$formBanned){ ?>
                            <li>
                                <a href="?page=forms"
                                    class="flex items-center p-2 rounded-lg text-gray-300 hover:opacity-60 hover:bg-blue-700 group">
                                    <i class="lni lni-clipboard"></i>
                                    <span class="ml-3">Forms</span>
                                </a>
                            </li>
                            <?php } ?>

                            <?php if ($role == "developer" || $role == "seller"){?>
                            <li>
                                <a href="?page=manage-accs"
                                    class="flex items-center p-2 rounded-lg text-gray-300 hover:opacity-60 hover:bg-blue-700 group">
                                    <i class="lni lni-network"></i>
                                    <span class="ml-3">Reseller | Manager</span>
                                </a>
                            </li>
                            <?php }
                            
                            $ua = $_SERVER['HTTP_USER_AGENT'];
                            ?>

                            <?php if ($role != "Manager" && $username != "demoseller" && !str_contains($ua, 'iPhone') && !str_contains($ua, 'Mac OS') && !str_contains($ua, 'Mobile')){ ?>
                            <li>
                                <a href="?page=upgrade"
                                    class="flex items-center p-2 rounded-lg text-gray-300 hover:opacity-60 hover:bg-blue-700 group">
                                    <i class="lni lni-investment"></i>
                                    <span class="ml-3">Upgrade</span>
                                </a>
                            </li>
                            <?php } ?>

                            <li>
                                <a href="?page=account-settings"
                                    class="flex items-center p-2 rounded-lg text-gray-300 hover:opacity-60 hover:bg-blue-700 group">
                                    <i class="lni lni-cog"></i>
                                    <span class="ml-3">Settings</span>
                                </a>
                            </li>

                            <?php if ($staff || $admin){ ?>
                            <li>
                                <a href="?page=staff-panel"
                                    class="flex items-center p-2 rounded-lg text-gray-300 hover:opacity-60 hover:bg-blue-700 group">
                                    <i class="lni lni-star-half"></i>
                                    <span class="ml-3">Staff Panel</span>
                                </a>
                            </li>
                            <?php } ?>

                            <?php if ($admin){ ?>
                            <li>
                                <a href="?page=admin-panel"
                                    class="flex items-center p-2 rounded-lg text-gray-300 hover:opacity-60 hover:bg-blue-700 group">
                                    <i class="lni lni-sketch"></i>
                                    <span class="ml-3">Admin Panel</span>
                                </a>
                            </li>
                            <?php } ?>

                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</aside>