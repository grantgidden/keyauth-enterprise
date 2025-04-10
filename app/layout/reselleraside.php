<?php
$page = isset($_GET['page']) ? $_GET['page'] : "index";
?>

<aside id="sidebar"
    class="flex hidden fixed top-0 left-0 z-20 flex-col flex-shrink-0 pt-16 w-64 h-full duration-200 lg:flex transition-width"
    aria-label="Sidebar">
    <div
        class="flex relative flex-col flex-1 pt-0 min-h-0 bg-[#0f0f17] border-r border-[#0f0f17]">
        <div class="flex overflow-y-auto flex-col flex-1 pt-5 pb-4">
            <div class="flex-1 px-3 space-y-1 bg-[#0f0f17]">
            <?php require '../app/layout/profile.php';?>
                <div class="mb-4 border-b border-[#0f0f17]">
                    <ul class="flex flex-wrap -mb-px text-sm font-medium text-center" id="myTab" data-tabs-toggle="#myTabContent" role="tablist">
                        <li class="mr-2" role="presentation">
                            <button class="inline-block p-4 rounded-t-lg hover:opacity-60" id="app-tab" data-tabs-target="#app" type="button" role="tab" aria-controls="app" aria-selected="false" data-popover-target="app-popover">Reseller</button>
                            <div data-popover id="app-popover" role="tooltip" class="absolute z-10 invisible inline-block w-64 text-sm text-gray-500 transition-opacity duration-300 bg-[#09090d] rounded-lg shadow-sm opacity-0">
                                <div class="px-3 py-2 bg-[#09090d]/70 rounded-t-lg">
                                    <h3 class="font-semibold text-white">Reseller</h3>
                                </div>
                                <div class="px-3 py-2">
                                    <p>Find everything related to your reseller account here</p>
                                </div>
                                <div data-popper-arrow></div>
                            </div>
                        </li>
                    </ul>
                </div>
                <div id="myTabContent">
                    <div class="hidden p-4 rounded-lg" id="app" role="tabpanel" aria-labelledby="app-tab">
                    <ul class="space-y-2 font-medium">
                        <li>
                            <a href="?page=reseller-licenses" class="flex items-center p-2 rounded-lg text-gray-300 hover:opacity-60 hover:bg-blue-700 group">
                            <i class="lni lni-key"></i>
                            <span class="ml-3">Licenses</span>
                            </a>
                        </li>

                        <li>
                            <a href="?page=reseller-users" class="flex items-center p-2 rounded-lg text-gray-300 hover:opacity-60 hover:bg-blue-700 group">
                            <i class="lni lni-users"></i>
                            <span class="ml-3">Users</span>
                            </a>
                        </li>

                        <li>
                            <a href="?page=reseller-balance" class="flex items-center p-2 rounded-lg text-gray-300 hover:opacity-60 hover:bg-blue-700 group">
                            <i class="lni lni-tag"></i>
                            <span class="ml-3">Balance</span>
                            </a>
                        </li>
                    </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</aside>

