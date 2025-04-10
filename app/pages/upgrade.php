<?php
    if ($_SESSION['role'] == "Reseller" || $_SESSION['role'] == "Manager"){
        misc\auditLog\send("Attempted (and failed) to view upgrade.");
        dashboard\primary\error("Resellers or Managers aren't allowed here.");
        echo "<meta http-equiv='Refresh' Content='3; url=?page=manage-apps'>";
        die();
    }

    if ($_SESSION['username'] == "demoseller" || $_SESSION['username'] == "demodeveloper"){
        dashboard\primary\error("You can not upgrade a demo account!");
        echo "<meta http-equiv='Refresh' Content='3; url=?page=manage-apps'>";
        die();
    }

    if (str_contains($ua, 'iPhone') || str_contains($ua, 'Mac OS') || str_contains($ua, 'Mobile')){
        dashboard\primary\error("Like Spotify, you can't uprade in the app. We know, it's not ideal.");
        echo "<meta http-equiv='Refresh' Content='3; url=?page=manage-apps'>";
        die();
    }
?>
<title>KeyAuth - Upgrade</title>
<script src="https://cdn.sellix.io/static/js/embed.js"></script>
<link href="https://cdn.sellix.io/static/css/embed.css" rel="stylesheet" />

<div class="p-4 bg-[#09090d] block sm:flex items-center justify-between lg:mt-1.5">
    <div class="mb-1 w-full bg-[#0f0f17] rounded-xl">
        <div class="mb-4 p-4">
            <?php require '../app/layout/breadcrumb.php'; ?>
            <h1 class="text-xl font-semibold text-white-900 sm:text-2xl ">Upgrade</h1>
            <p class="text-xs text-gray-500">Upgrade your account today!</p>
            <div class="p-4 flex flex-col">
                <div class="overflow-x-auto">
                    <!-- Alert Box -->
                    <div id="alert-4" class="flex items-center p-4 mb-4 text-red-600 rounded-lg bg-[#09090d]"
                        role="alert">
                        <svg class="flex-shrink-0 w-4 h-4" aria-hidden="true" xmlns="http://www.w3.org/2000/svg"
                            fill="currentColor" viewBox="0 0 20 20">
                            <path
                                d="M10 .5a9.5 9.5 0 1 0 9.5 9.5A9.51 9.51 0 0 0 10 .5ZM9.5 4a1.5 1.5 0 1 1 0 3 1.5 1.5 0 0 1 0-3ZM12 15H8a1 1 0 0 1 0-2h1v-3H8a1 1 0 0 1 0-2h2a1 1 0 0 1 1 1v4h1a1 1 0 0 1 0 2Z" />
                        </svg>
                        <div class="ml-3 text-sm font-medium">
                            <b>Fraud Notice:</b> Committing fraud will result in your account being banned! There is also a risk rating on each payment. If it reaches a specific amount, we have the right to refund you and downgrade your account.
                        </div>
                    </div>
                    <!-- End Alert Box -->

                    <!-- Alert Box -->
                    <div id="alert-4" class="flex items-center p-4 mb-4 text-red-600 rounded-lg bg-[#09090d]"
                        role="alert">
                        <svg class="flex-shrink-0 w-4 h-4" aria-hidden="true" xmlns="http://www.w3.org/2000/svg"
                            fill="currentColor" viewBox="0 0 20 20">
                            <path
                                d="M10 .5a9.5 9.5 0 1 0 9.5 9.5A9.51 9.51 0 0 0 10 .5ZM9.5 4a1.5 1.5 0 1 1 0 3 1.5 1.5 0 0 1 0-3ZM12 15H8a1 1 0 0 1 0-2h1v-3H8a1 1 0 0 1 0-2h2a1 1 0 0 1 1 1v4h1a1 1 0 0 1 0 2Z" />
                        </svg>
                        <div class="ml-3 text-sm font-medium">
                            <b>Russian Blockages Notice:</b> We do not block any users in Russia, but Paypal & credit
                            cards do.
                            Use Binance, Cryptocurrency, or Perfect Money instead.
                        </div>
                    </div>
                    <!-- End Alert Box -->

                    <!-- Alert Box -->
                    <div id="alert-4" class="flex items-center p-4 mb-4 text-green-600 rounded-lg bg-[#09090d]"
                        role="alert">
                        <svg class="flex-shrink-0 w-4 h-4" aria-hidden="true" xmlns="http://www.w3.org/2000/svg"
                            fill="currentColor" viewBox="0 0 20 20">
                            <path
                                d="M10 .5a9.5 9.5 0 1 0 9.5 9.5A9.51 9.51 0 0 0 10 .5ZM9.5 4a1.5 1.5 0 1 1 0 3 1.5 1.5 0 0 1 0-3ZM12 15H8a1 1 0 0 1 0-2h1v-3H8a1 1 0 0 1 0-2h2a1 1 0 0 1 1 1v4h1a1 1 0 0 1 0 2Z" />
                        </svg>
                        <div class="ml-3 text-sm font-medium">
                            <b>Discount:</b> If you currently have the Developer subscription(yearly only!), you can use code
                            <b>alreadydev</b> to get 50% off when purchasing the seller subscription.
                            Attempting to use the code while having the tester subscription will result in you only
                            receiving the developer subscription.
                        </div>
                    </div>
                    <!-- End Alert Box -->

                    <section id="plans">
                        <div class="max-w-screen-xl px-4 py-8 mx-auto lg:px-6 sm:py-16 lg:py-24">
                            <div class="mx-auto max-w-screen-md text-center">
                                <div class="flex items-center justify-center mb-12 -mt-6">
                                    <label class="relative inline-flex items-center mr-5 cursor-pointer">
                                        <input type="checkbox" id="pricing_anually_month" class="sr-only peer"
                                            checked>
                                        <div
                                            class="w-11 h-6 border-2 border-blue-700 bg-[#0f0f17] rounded-full peer peer-focus:ring-0 peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-0.5 after:left-[2px] after:bg-white after:border-[#0C0C12] after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-[#0C0C12]">
                                        </div>
                                        <span class="ml-3 text-sm font-medium text-gray-300">Annual pricing <span
                                                class="text-xs">(save
                                                50% +)</span></span>
                                    </label>
                                </div>
                            </div>
                            <div class="flex flex-wrap -m-7">
                                <div class="w-full md:w-1/3 p-7 animate-on-scroll">
                                    <div class="h-full p-8 border border-white-700 rounded-xl bg-[#09090d]">
                                        <div class="flex flex-wrap justify-between border-b border-gray-800 pb-7 mb-7">
                                            <div class="w-full xl:w-auto">
                                                <h3 class="font-bold text-2xl text-white">Tester</h3>
                                            </div>
                                            <div class="w-full xl:w-auto xl:text-right">
                                                <h3 class="mb-0.5 font-bold text-2xl text-white">
                                                    <span class="text-sm mr-0.5">$</span> <span id="tester_mode"></span>
                                                </h3>
                                                <p class="text-sm text-gray-400">
                                                    Limited Access for those looking to experiment implementing KeyAuth
                                                </p>
                                            </div>
                                        </div>
                                        <ul class="mb-8">
                                            <li class="flex items-center mb-4 font-medium text-base text-white">
                                                <svg class="h-5 w-5 mr-2.5 text-green-500" fill="currentColor" viewBox="0 0 24 24"
                                                    xmlns="http://www.w3.org/2000/svg">
                                                    <path
                                                        d="M12 2C6.486 2 2 6.486 2 12s4.486 10 10 10 10-4.486 10-10S17.514 2 12 2Zm-1.999 14.413-3.713-3.705L7.7 11.292l2.299 2.295 5.294-5.294 1.414 1.414-6.706 6.706Z">
                                                    </path>
                                                </svg>
                                                <p>
                                                    Users: 10
                                                </p>
                                            </li>
                                            <li class="flex items-center mb-4 font-medium text-base text-white">
                                                <svg class="h-5 w-5 mr-2.5 text-green-500" fill="currentColor" viewBox="0 0 24 24"
                                                    xmlns="http://www.w3.org/2000/svg">
                                                    <path
                                                        d="M12 2C6.486 2 2 6.486 2 12s4.486 10 10 10 10-4.486 10-10S17.514 2 12 2Zm-1.999 14.413-3.713-3.705L7.7 11.292l2.299 2.295 5.294-5.294 1.414 1.414-6.706 6.706Z">
                                                    </path>
                                                </svg>
                                                <p>
                                                    Upload Files: (10 MB)
                                                </p>
                                            </li>      
                                            <li class="flex items-center mb-4 font-medium text-base text-white">
                                                <svg class="h-5 w-5 mr-2.5 text-green-500" fill="currentColor" viewBox="0 0 24 24"
                                                    xmlns="http://www.w3.org/2000/svg">
                                                    <path
                                                        d="M12 2C6.486 2 2 6.486 2 12s4.486 10 10 10 10-4.486 10-10S17.514 2 12 2Zm-1.999 14.413-3.713-3.705L7.7 11.292l2.299 2.295 5.294-5.294 1.414 1.414-6.706 6.706Z">
                                                    </path>
                                                </svg>
                                                <p>
                                                    Global Variables: 5
                                                </p>
                                            </li>
                                            <li class="flex items-center mb-4 font-medium text-base text-white">
                                                <svg class="h-5 w-5 mr-2.5 text-green-500" fill="currentColor" viewBox="0 0 24 24"
                                                    xmlns="http://www.w3.org/2000/svg">
                                                    <path
                                                        d="M12 2C6.486 2 2 6.486 2 12s4.486 10 10 10 10-4.486 10-10S17.514 2 12 2Zm-1.999 14.413-3.713-3.705L7.7 11.292l2.299 2.295 5.294-5.294 1.414 1.414-6.706 6.706Z">
                                                    </path>
                                                </svg>
                                                <p>
                                                    User Variables
                                                </p>
                                            </li>
                                            <li class="flex items-center mb-4 font-medium text-base text-white">
                                                <svg class="h-5 w-5 mr-2.5 text-green-500" fill="currentColor" viewBox="0 0 24 24"
                                                    xmlns="http://www.w3.org/2000/svg">
                                                    <path
                                                        d="M12 2C6.486 2 2 6.486 2 12s4.486 10 10 10 10-4.486 10-10S17.514 2 12 2Zm-1.999 14.413-3.713-3.705L7.7 11.292l2.299 2.295 5.294-5.294 1.414 1.414-6.706 6.706Z">
                                                    </path>
                                                </svg>
                                                <p>
                                                    Logs (includes Discord logs): 20
                                                </p>
                                            </li>
                                            <li class="flex items-center mb-4 font-medium text-base text-white">
                                                <svg class="h-5 w-5 mr-2.5 text-green-500" fill="currentColor" viewBox="0 0 24 24"
                                                    xmlns="http://www.w3.org/2000/svg">
                                                    <path
                                                        d="M12 2C6.486 2 2 6.486 2 12s4.486 10 10 10 10-4.486 10-10S17.514 2 12 2Zm-1.999 14.413-3.713-3.705L7.7 11.292l2.299 2.295 5.294-5.294 1.414 1.414-6.706 6.706Z">
                                                    </path>
                                                </svg>
                                                <p>
                                                    Hardware/IP Blacklist/Whitelist
                                                </p>
                                            </li>
                                            <li class="flex items-center mb-4 font-medium text-base text-white">
                                                <svg class="h-5 w-5 mr-2.5 text-green-500" fill="currentColor" viewBox="0 0 24 24"
                                                    xmlns="http://www.w3.org/2000/svg">
                                                    <path
                                                        d="M12 2C6.486 2 2 6.486 2 12s4.486 10 10 10 10-4.486 10-10S17.514 2 12 2Zm-1.999 14.413-3.713-3.705L7.7 11.292l2.299 2.295 5.294-5.294 1.414 1.414-6.706 6.706Z">
                                                    </path>
                                                </svg>
                                                <p>
                                                    Web Loader
                                                </p>
                                            </li>        
                                            <li class="flex items-center mb-4 font-medium text-base text-white">
                                                <svg class="h-5 w-5 mr-2.5 text-red-500" fill="currentColor" viewBox="0 0 24 24"
                                                    xmlns="http://www.w3.org/2000/svg">
                                                    <path
                                                        d="M12 2C6.486 2 2 6.486 2 12s4.486 10 10 10 10-4.486 10-10S17.514 2 12 2Zm4.207 12.793-1.414 1.414L12 13.414l-2.793 2.793-1.414-1.414L10.586 12 7.793 9.207l1.414-1.414L12 10.586l2.793-2.793 1.414 1.414L13.414 12l2.793 2.793Z">
                                                    </path>
                                                </svg>
                                                <p>
                                                    Create Webhooks
                                                </p>
                                            </li>
                                            <li class="flex items-center mb-4 font-medium text-base text-white">
                                                <svg class="h-5 w-5 mr-2.5 text-red-500" fill="currentColor" viewBox="0 0 24 24"
                                                    xmlns="http://www.w3.org/2000/svg">
                                                    <path
                                                        d="M12 2C6.486 2 2 6.486 2 12s4.486 10 10 10 10-4.486 10-10S17.514 2 12 2Zm4.207 12.793-1.414 1.414L12 13.414l-2.793 2.793-1.414-1.414L10.586 12 7.793 9.207l1.414-1.414L12 10.586l2.793-2.793 1.414 1.414L13.414 12l2.793 2.793Z">
                                                    </path>
                                                </svg>
                                                <p>
                                                    Reseller & Manager Access
                                                </p>
                                            </li>
                                            <li class="flex items-center mb-4 font-medium text-base text-white">
                                                <svg class="h-5 w-5 mr-2.5 text-red-500" fill="currentColor" viewBox="0 0 24 24"
                                                    xmlns="http://www.w3.org/2000/svg">
                                                    <path
                                                        d="M12 2C6.486 2 2 6.486 2 12s4.486 10 10 10 10-4.486 10-10S17.514 2 12 2Zm4.207 12.793-1.414 1.414L12 13.414l-2.793 2.793-1.414-1.414L10.586 12 7.793 9.207l1.414-1.414L12 10.586l2.793-2.793 1.414 1.414L13.414 12l2.793 2.793Z">
                                                    </path>
                                                </svg>
                                                <p>
                                                    Chatrooms
                                                </p>
                                            </li>
                                            <li class="flex items-center mb-4 font-medium text-base text-white">
                                                <svg class="h-5 w-5 mr-2.5 text-red-500" fill="currentColor" viewBox="0 0 24 24"
                                                    xmlns="http://www.w3.org/2000/svg">
                                                    <path
                                                        d="M12 2C6.486 2 2 6.486 2 12s4.486 10 10 10 10-4.486 10-10S17.514 2 12 2Zm4.207 12.793-1.414 1.414L12 13.414l-2.793 2.793-1.414-1.414L10.586 12 7.793 9.207l1.414-1.414L12 10.586l2.793-2.793 1.414 1.414L13.414 12l2.793 2.793Z">
                                                    </path>
                                                </svg>
                                                <p>
                                                    SellerAPI Access
                                                </p>
                                            </li>
                                            <li class="flex items-center mb-4 font-medium text-base text-white">
                                                <svg class="h-5 w-5 mr-2.5 text-red-500" fill="currentColor" viewBox="0 0 24 24"
                                                    xmlns="http://www.w3.org/2000/svg">
                                                    <path
                                                        d="M12 2C6.486 2 2 6.486 2 12s4.486 10 10 10 10-4.486 10-10S17.514 2 12 2Zm4.207 12.793-1.414 1.414L12 13.414l-2.793 2.793-1.414-1.414L10.586 12 7.793 9.207l1.414-1.414L12 10.586l2.793-2.793 1.414 1.414L13.414 12l2.793 2.793Z">
                                                    </path>
                                                </svg>
                                                <p>
                                                    Discord Bot
                                                </p>
                                            </li>
                                        </ul>

                                        <?php if ($_SESSION["role"] !== "tester"){ ?>
                                        <p class="text-sm text-gray-400">
                                            This is the default subsription. But you have the <?= $_SESSION["role"]; ?>
                                            subscription.
                                        </p>
                                        <?php } else { ?>
                                        <p class="text-sm text-gray-400">
                                            This is the default subsription, and your current one.
                                        </p>
                                        <?php } ?>
                                    </div>
                                </div>

                                <div class="w-full md:w-1/3 p-7 animate-on-scroll-in-up">
                                    <div class="h-full p-8 border border-green-700 rounded-xl bg-[#09090d]">
                                        <div class="flex flex-wrap justify-between border-b border-gray-800 pb-7 mb-7">
                                            <div class="w-full xl:w-auto">
                                                <h3 class="font-bold text-2xl text-white">Developer</h3>
                                            </div>
                                            <div class="w-full xl:w-auto xl:text-right">
                                                <h3
                                                    class="mb-0.5 font-bold text-2xl text-transparent bg-clip-text bg-gradient-to-r to-[#4fea74] from-[#1db233]">
                                                    <span class="text-sm mr-0.5">$</span><span
                                                        id="developer_mode"></span>
                                                </h3>
                                                <p class="text-sm text-gray-400">
                                                    Ample limits plus full access to reseller system. Most folks start
                                                    here.
                                                </p>
                                            </div>
                                        </div>
                                        <ul class="mb-8">
                                            <li class="flex items-center mb-4 font-medium text-base text-white">
                                                <svg class="h-5 w-5 mr-2.5 text-green-500" fill="currentColor" viewBox="0 0 24 24"
                                                    xmlns="http://www.w3.org/2000/svg">
                                                    <path
                                                        d="M12 2C6.486 2 2 6.486 2 12s4.486 10 10 10 10-4.486 10-10S17.514 2 12 2Zm-1.999 14.413-3.713-3.705L7.7 11.292l2.299 2.295 5.294-5.294 1.414 1.414-6.706 6.706Z">
                                                    </path>
                                                </svg>
                                                <p>
                                                    Users: Unlimited
                                                </p>
                                            </li>
                                            <li class="flex items-center mb-4 font-medium text-base text-white">
                                                <svg class="h-5 w-5 mr-2.5 text-green-500" fill="currentColor" viewBox="0 0 24 24"
                                                    xmlns="http://www.w3.org/2000/svg">
                                                    <path
                                                        d="M12 2C6.486 2 2 6.486 2 12s4.486 10 10 10 10-4.486 10-10S17.514 2 12 2Zm-1.999 14.413-3.713-3.705L7.7 11.292l2.299 2.295 5.294-5.294 1.414 1.414-6.706 6.706Z">
                                                    </path>
                                                </svg>
                                                <p>
                                                    Upload Files: (50 MB)
                                                </p>
                                            </li>
                                            <li class="flex items-center mb-4 font-medium text-base text-white">
                                                <svg class="h-5 w-5 mr-2.5 text-green-500" fill="currentColor" viewBox="0 0 24 24"
                                                    xmlns="http://www.w3.org/2000/svg">
                                                    <path
                                                        d="M12 2C6.486 2 2 6.486 2 12s4.486 10 10 10 10-4.486 10-10S17.514 2 12 2Zm-1.999 14.413-3.713-3.705L7.7 11.292l2.299 2.295 5.294-5.294 1.414 1.414-6.706 6.706Z">
                                                    </path>
                                                </svg>
                                                <p>
                                                    Global Variables: Unlimited
                                                </p>
                                            </li>
                                            <li class="flex items-center mb-4 font-medium text-base text-white">
                                                <svg class="h-5 w-5 mr-2.5 text-green-500" fill="currentColor" viewBox="0 0 24 24"
                                                    xmlns="http://www.w3.org/2000/svg">
                                                    <path
                                                        d="M12 2C6.486 2 2 6.486 2 12s4.486 10 10 10 10-4.486 10-10S17.514 2 12 2Zm-1.999 14.413-3.713-3.705L7.7 11.292l2.299 2.295 5.294-5.294 1.414 1.414-6.706 6.706Z">
                                                    </path>
                                                </svg>
                                                <p>
                                                    User Variables
                                                </p>
                                            </li>
                                            <li class="flex items-center mb-4 font-medium text-base text-white">
                                                <svg class="h-5 w-5 mr-2.5 text-green-500" fill="currentColor" viewBox="0 0 24 24"
                                                    xmlns="http://www.w3.org/2000/svg">
                                                    <path
                                                        d="M12 2C6.486 2 2 6.486 2 12s4.486 10 10 10 10-4.486 10-10S17.514 2 12 2Zm-1.999 14.413-3.713-3.705L7.7 11.292l2.299 2.295 5.294-5.294 1.414 1.414-6.706 6.706Z">
                                                    </path>
                                                </svg>
                                                <p>
                                                    Logs (includes Discord logs): Unlimited
                                                </p>
                                            </li>
                                            <li class="flex items-center mb-4 font-medium text-base text-white">
                                                <svg class="h-5 w-5 mr-2.5 text-green-500" fill="currentColor" viewBox="0 0 24 24"
                                                    xmlns="http://www.w3.org/2000/svg">
                                                    <path
                                                        d="M12 2C6.486 2 2 6.486 2 12s4.486 10 10 10 10-4.486 10-10S17.514 2 12 2Zm-1.999 14.413-3.713-3.705L7.7 11.292l2.299 2.295 5.294-5.294 1.414 1.414-6.706 6.706Z">
                                                    </path>
                                                </svg>
                                                <p>
                                                    Hardware/IP Blacklist/Whitelist
                                                </p>
                                            </li>
                                            <li class="flex items-center mb-4 font-medium text-base text-white">
                                                <svg class="h-5 w-5 mr-2.5 text-green-500" fill="currentColor" viewBox="0 0 24 24"
                                                    xmlns="http://www.w3.org/2000/svg">
                                                    <path
                                                        d="M12 2C6.486 2 2 6.486 2 12s4.486 10 10 10 10-4.486 10-10S17.514 2 12 2Zm-1.999 14.413-3.713-3.705L7.7 11.292l2.299 2.295 5.294-5.294 1.414 1.414-6.706 6.706Z">
                                                    </path>
                                                </svg>
                                                <p>
                                                    Web Loader
                                                </p>
                                            </li>      
                                            <li class="flex items-center mb-4 font-medium text-base text-white">
                                                <svg class="h-5 w-5 mr-2.5 text-green-500" fill="currentColor" viewBox="0 0 24 24"
                                                    xmlns="http://www.w3.org/2000/svg">
                                                    <path
                                                        d="M12 2C6.486 2 2 6.486 2 12s4.486 10 10 10 10-4.486 10-10S17.514 2 12 2Zm-1.999 14.413-3.713-3.705L7.7 11.292l2.299 2.295 5.294-5.294 1.414 1.414-6.706 6.706Z">
                                                    </path>
                                                </svg>
                                                <p>
                                                    Create Webhooks
                                                </p>
                                            </li>
                                            <li class="flex items-center mb-4 font-medium text-base text-white">
                                                <svg class="h-5 w-5 mr-2.5 text-green-500" fill="currentColor" viewBox="0 0 24 24"
                                                    xmlns="http://www.w3.org/2000/svg">
                                                    <path
                                                        d="M12 2C6.486 2 2 6.486 2 12s4.486 10 10 10 10-4.486 10-10S17.514 2 12 2Zm-1.999 14.413-3.713-3.705L7.7 11.292l2.299 2.295 5.294-5.294 1.414 1.414-6.706 6.706Z">
                                                    </path>
                                                </svg>
                                                <p>
                                                    Reseller & Manager Access
                                                </p>
                                            </li>
                                            <li class="flex items-center mb-4 font-medium text-base text-white">
                                                <svg class="h-5 w-5 mr-2.5 text-red-500" fill="currentColor" viewBox="0 0 24 24"
                                                    xmlns="http://www.w3.org/2000/svg">
                                                    <path
                                                        d="M12 2C6.486 2 2 6.486 2 12s4.486 10 10 10 10-4.486 10-10S17.514 2 12 2Zm4.207 12.793-1.414 1.414L12 13.414l-2.793 2.793-1.414-1.414L10.586 12 7.793 9.207l1.414-1.414L12 10.586l2.793-2.793 1.414 1.414L13.414 12l2.793 2.793Z">
                                                    </path>
                                                </svg>
                                                <p>
                                                    Chatrooms
                                                </p>
                                            </li>
                                            <li class="flex items-center mb-4 font-medium text-base text-white">
                                                <svg class="h-5 w-5 mr-2.5 text-red-500" fill="currentColor" viewBox="0 0 24 24"
                                                    xmlns="http://www.w3.org/2000/svg">
                                                    <path
                                                        d="M12 2C6.486 2 2 6.486 2 12s4.486 10 10 10 10-4.486 10-10S17.514 2 12 2Zm4.207 12.793-1.414 1.414L12 13.414l-2.793 2.793-1.414-1.414L10.586 12 7.793 9.207l1.414-1.414L12 10.586l2.793-2.793 1.414 1.414L13.414 12l2.793 2.793Z">
                                                    </path>
                                                </svg>
                                                <p>
                                                    SellerAPI Access
                                                </p>
                                            </li>
                                            <li class="flex items-center mb-4 font-medium text-base text-white">
                                                <svg class="h-5 w-5 mr-2.5 text-red-500" fill="currentColor" viewBox="0 0 24 24"
                                                    xmlns="http://www.w3.org/2000/svg">
                                                    <path
                                                        d="M12 2C6.486 2 2 6.486 2 12s4.486 10 10 10 10-4.486 10-10S17.514 2 12 2Zm4.207 12.793-1.414 1.414L12 13.414l-2.793 2.793-1.414-1.414L10.586 12 7.793 9.207l1.414-1.414L12 10.586l2.793-2.793 1.414 1.414L13.414 12l2.793 2.793Z">
                                                    </path>
                                                </svg>
                                                <p>
                                                    Discord Bot
                                                </p>
                                            </li>
                                        </ul>

                                        <?php if ($_SESSION["role"] == "seller") { ?>
                                        <p class="text-sm text-gray-400 mb-5">
                                            You already have this or a higher subscription.
                                        </p>
                                        <?php } ?>
                                        <button type="button"
                                        class="text-green-700 border-2 border-green-700 hover:bg-green-700 hover:text-white focus:ring-0 focus:outline-none transition duration-200 font-medium rounded-lg text-sm px-5 py-2.5 text-center items-center mb-3 w-full">
                                        <a href="https://paddle.keyauth.cc/developer/?ownerid=<?= urlencode($_SESSION['ownerid']); ?>" target="_blank">
                                            <span class="inline-flex">
                                                Purchase Developer Now (PayPal/Card)
                                                <svg class="w-3.5 h-3.5 ml-2 mt-0.5" aria-hidden="true"
                                                    xmlns="http://www.w3.org/2000/svg" fill="none"
                                                    viewBox="0 0 14 10">
                                                    <path stroke="currentColor" stroke-linecap="round"
                                                        stroke-linejoin="round" stroke-width="2"
                                                        d="M1 5h12m0 0L9 1m4 4L9 9" />
                                                </svg></span>
                                        </a>
                                        </button>
                                    
                                        <button id="sellixButtonDeveloper" data-sellix-product="6011e41969f3d" type="submit" data-sellix-custom-username="'.urlencode($_SESSION['ownerid']).'"
                                        class="text-green-700 border-2 border-green-700 hover:bg-green-700 hover:text-white focus:ring-0 focus:outline-none transition duration-200 font-medium rounded-lg text-sm px-5 py-2.5 text-center items-center mb-3 w-full">                                
                                        <span class="inline-flex">
                                                Purchase Developer Now (Crypto)
                                                <svg class="w-3.5 h-3.5 ml-2 mt-0.5" aria-hidden="true"
                                                    xmlns="http://www.w3.org/2000/svg" fill="none"
                                                    viewBox="0 0 14 10">
                                                    <path stroke="currentColor" stroke-linecap="round"
                                                        stroke-linejoin="round" stroke-width="2"
                                                        d="M1 5h12m0 0L9 1m4 4L9 9" />
                                                </svg>
                                            </span>
                                        </button>

                                        <button type="button"
                                            class="text-green-700 border-2 border-green-700 hover:bg-green-700 hover:text-white focus:ring-0 focus:outline-none transition duration-200 font-medium rounded-lg text-sm px-5 py-2.5 text-center items-center mb-3 w-full"
                                            data-modal-target="cashapp-purchase" data-modal-toggle="cashapp-purchase">
                                                <span class="inline-flex">
                                                    Purchase Developer Now (Cashapp)
                                                    <svg class="w-3.5 h-3.5 ml-2 mt-0.5" aria-hidden="true"
                                                        xmlns="http://www.w3.org/2000/svg" fill="none"
                                                        viewBox="0 0 14 10">
                                                        <path stroke="currentColor" stroke-linecap="round"
                                                            stroke-linejoin="round" stroke-width="2"
                                                            d="M1 5h12m0 0L9 1m4 4L9 9" />
                                                    </svg></span>
                                            </a>
                                        </button>
                                    </div>
                                </div>

                                <div class="w-full md:w-1/3 p-7 animate-on-scroll-right">
                                    <div class="h-full p-8 border border-cyan-700 rounded-xl bg-[#09090d]">
                                        <div class="flex flex-wrap justify-between border-b border-gray-800 pb-7 mb-7">
                                            <div class="w-full xl:w-auto">
                                                <h3 class="font-bold text-2xl text-white">Seller</h3>
                                            </div>
                                            <div class="w-full xl:w-auto xl:text-right">
                                                <h3
                                                    class="mb-0.5 font-bold text-2xl text-transparent bg-clip-text bg-gradient-to-r to-[#36e2dd] from-[#1e96fc]">
                                                    <span class="text-sm mr-0.5">$</span><span id="seller_mode"></span>
                                                </h3>
                                                <p class="text-sm text-gray-400">
                                                    Full-fledged supporter, we appreciate you for keeping our servers
                                                    running!
                                                </p>
                                            </div>
                                        </div>
                                        <ul class="mb-8">
                                            <li class="flex items-center mb-4 font-medium text-base text-white">
                                                <svg class="h-5 w-5 mr-2.5 text-green-500" fill="currentColor" viewBox="0 0 24 24"
                                                    xmlns="http://www.w3.org/2000/svg">
                                                    <path
                                                        d="M12 2C6.486 2 2 6.486 2 12s4.486 10 10 10 10-4.486 10-10S17.514 2 12 2Zm-1.999 14.413-3.713-3.705L7.7 11.292l2.299 2.295 5.294-5.294 1.414 1.414-6.706 6.706Z">
                                                    </path>
                                                </svg>
                                                <p>
                                                    Users: Unlimited
                                                </p>
                                            </li>
                                            <li class="flex items-center mb-4 font-medium text-base text-white">
                                                <svg class="h-5 w-5 mr-2.5 text-green-500" fill="currentColor" viewBox="0 0 24 24"
                                                    xmlns="http://www.w3.org/2000/svg">
                                                    <path
                                                        d="M12 2C6.486 2 2 6.486 2 12s4.486 10 10 10 10-4.486 10-10S17.514 2 12 2Zm-1.999 14.413-3.713-3.705L7.7 11.292l2.299 2.295 5.294-5.294 1.414 1.414-6.706 6.706Z">
                                                    </path>
                                                </svg>
                                                <p>
                                                    Upload Files: (75 MB)
                                                </p>
                                            </li>
                                            <li class="flex items-center mb-4 font-medium text-base text-white">
                                                <svg class="h-5 w-5 mr-2.5 text-green-500" fill="currentColor" viewBox="0 0 24 24"
                                                    xmlns="http://www.w3.org/2000/svg">
                                                    <path
                                                        d="M12 2C6.486 2 2 6.486 2 12s4.486 10 10 10 10-4.486 10-10S17.514 2 12 2Zm-1.999 14.413-3.713-3.705L7.7 11.292l2.299 2.295 5.294-5.294 1.414 1.414-6.706 6.706Z">
                                                    </path>
                                                </svg>
                                                <p>
                                                    Global Variables: Unlimited
                                                </p>
                                            </li>
                                            <li class="flex items-center mb-4 font-medium text-base text-white">
                                                <svg class="h-5 w-5 mr-2.5 text-green-500" fill="currentColor" viewBox="0 0 24 24"
                                                    xmlns="http://www.w3.org/2000/svg">
                                                    <path
                                                        d="M12 2C6.486 2 2 6.486 2 12s4.486 10 10 10 10-4.486 10-10S17.514 2 12 2Zm-1.999 14.413-3.713-3.705L7.7 11.292l2.299 2.295 5.294-5.294 1.414 1.414-6.706 6.706Z">
                                                    </path>
                                                </svg>
                                                <p>
                                                    User Variables
                                                </p>
                                            </li>
                                            <li class="flex items-center mb-4 font-medium text-base text-white">
                                                <svg class="h-5 w-5 mr-2.5 text-green-500" fill="currentColor" viewBox="0 0 24 24"
                                                    xmlns="http://www.w3.org/2000/svg">
                                                    <path
                                                        d="M12 2C6.486 2 2 6.486 2 12s4.486 10 10 10 10-4.486 10-10S17.514 2 12 2Zm-1.999 14.413-3.713-3.705L7.7 11.292l2.299 2.295 5.294-5.294 1.414 1.414-6.706 6.706Z">
                                                    </path>
                                                </svg>
                                                <p>
                                                    Logs (includes Discord logs): Unlimited
                                                </p>
                                            </li>
                                            <li class="flex items-center mb-4 font-medium text-base text-white">
                                                <svg class="h-5 w-5 mr-2.5 text-green-500" fill="currentColor" viewBox="0 0 24 24"
                                                    xmlns="http://www.w3.org/2000/svg">
                                                    <path
                                                        d="M12 2C6.486 2 2 6.486 2 12s4.486 10 10 10 10-4.486 10-10S17.514 2 12 2Zm-1.999 14.413-3.713-3.705L7.7 11.292l2.299 2.295 5.294-5.294 1.414 1.414-6.706 6.706Z">
                                                    </path>
                                                </svg>
                                                <p>
                                                    Hardware/IP Blacklist/Whitelist
                                                </p>
                                            </li>
                                            <li class="flex items-center mb-4 font-medium text-base text-white">
                                                <svg class="h-5 w-5 mr-2.5 text-green-500" fill="currentColor" viewBox="0 0 24 24"
                                                    xmlns="http://www.w3.org/2000/svg">
                                                    <path
                                                        d="M12 2C6.486 2 2 6.486 2 12s4.486 10 10 10 10-4.486 10-10S17.514 2 12 2Zm-1.999 14.413-3.713-3.705L7.7 11.292l2.299 2.295 5.294-5.294 1.414 1.414-6.706 6.706Z">
                                                    </path>
                                                </svg>
                                                <p>
                                                    Web Loader
                                                </p>
                                            </li>                                      
                                            <li class="flex items-center mb-4 font-medium text-base text-white">
                                                <svg class="h-5 w-5 mr-2.5 text-green-500" fill="currentColor" viewBox="0 0 24 24"
                                                    xmlns="http://www.w3.org/2000/svg">
                                                    <path
                                                        d="M12 2C6.486 2 2 6.486 2 12s4.486 10 10 10 10-4.486 10-10S17.514 2 12 2Zm-1.999 14.413-3.713-3.705L7.7 11.292l2.299 2.295 5.294-5.294 1.414 1.414-6.706 6.706Z">
                                                    </path>
                                                </svg>
                                                <p>
                                                    Create Webhooks
                                                </p>
                                            </li>
                                            <li class="flex items-center mb-4 font-medium text-base text-white">
                                                <svg class="h-5 w-5 mr-2.5 text-green-500" fill="currentColor" viewBox="0 0 24 24"
                                                    xmlns="http://www.w3.org/2000/svg">
                                                    <path
                                                        d="M12 2C6.486 2 2 6.486 2 12s4.486 10 10 10 10-4.486 10-10S17.514 2 12 2Zm-1.999 14.413-3.713-3.705L7.7 11.292l2.299 2.295 5.294-5.294 1.414 1.414-6.706 6.706Z">
                                                    </path>
                                                </svg>
                                                <p>
                                                    Reseller & Manager Access
                                                </p>
                                            </li>
                                            <li class="flex items-center mb-4 font-medium text-base text-white">
                                                <svg class="h-5 w-5 mr-2.5 text-green-500" fill="currentColor" viewBox="0 0 24 24"
                                                    xmlns="http://www.w3.org/2000/svg">
                                                    <path
                                                        d="M12 2C6.486 2 2 6.486 2 12s4.486 10 10 10 10-4.486 10-10S17.514 2 12 2Zm-1.999 14.413-3.713-3.705L7.7 11.292l2.299 2.295 5.294-5.294 1.414 1.414-6.706 6.706Z">
                                                    </path>
                                                </svg>
                                                <p>
                                                    Chatrooms
                                                </p>
                                            </li>
                                            <li class="flex items-center mb-4 font-medium text-base text-white">
                                                <svg class="h-5 w-5 mr-2.5 text-green-500" fill="currentColor" viewBox="0 0 24 24"
                                                    xmlns="http://www.w3.org/2000/svg">
                                                    <path
                                                        d="M12 2C6.486 2 2 6.486 2 12s4.486 10 10 10 10-4.486 10-10S17.514 2 12 2Zm-1.999 14.413-3.713-3.705L7.7 11.292l2.299 2.295 5.294-5.294 1.414 1.414-6.706 6.706Z">
                                                    </path>
                                                </svg>
                                                <p>
                                                    SellerAPI Access
                                                </p>
                                            </li>
                                            <li class="flex items-center mb-4 font-medium text-base text-white">
                                                <svg class="h-5 w-5 mr-2.5 text-green-500" fill="currentColor" viewBox="0 0 24 24"
                                                    xmlns="http://www.w3.org/2000/svg">
                                                    <path
                                                        d="M12 2C6.486 2 2 6.486 2 12s4.486 10 10 10 10-4.486 10-10S17.514 2 12 2Zm-1.999 14.413-3.713-3.705L7.7 11.292l2.299 2.295 5.294-5.294 1.414 1.414-6.706 6.706Z">
                                                    </path>
                                                </svg>
                                                <p>
                                                    Discord Bot
                                                </p>
                                            </li>
                                        </ul>

                                        <?php if ($_SESSION["role"] == "seller") { ?>
                                        <p class="text-sm text-gray-400 mb-5">
                                            You already have this or a higher subscription.
                                        </p>
                                        <?php } ?>
                                        <button type="button"
                                        class="text-cyan-700 border-2 border-cyan-700 hover:bg-cyan-700 hover:text-white focus:ring-0 focus:outline-none transition duration-200 font-medium rounded-lg text-sm px-5 py-2.5 text-center items-center mb-3 w-full">
                                        <a href="https://paddle.keyauth.cc/seller/?ownerid=<?= urlencode($_SESSION['ownerid']); ?>"   target="_blank">
                                            <span class="inline-flex">
                                                Purchase Seller Now (PayPal/Card)
                                                <svg class="w-3.5 h-3.5 ml-2 mt-0.5" aria-hidden="true"
                                                    xmlns="http://www.w3.org/2000/svg" fill="none"
                                                    viewBox="0 0 14 10">
                                                    <path stroke="currentColor" stroke-linecap="round"
                                                        stroke-linejoin="round" stroke-width="2"
                                                        d="M1 5h12m0 0L9 1m4 4L9 9" />
                                                </svg></span>
                                        </a>
                                        </button>
                                    
                                        <button id="sellixButtonSeller" data-sellix-product="6011e42262457" type="submit" data-sellix-custom-username="'.urlencode($_SESSION['ownerid']).'"
                                        class="text-cyan-700 border-2 border-cyan-700 hover:bg-cyan-700 hover:text-white focus:ring-0 focus:outline-none transition duration-200 font-medium rounded-lg text-sm px-5 py-2.5 text-center items-center mb-3 w-full">                                
                                        <span class="inline-flex">
                                                Purchase Seller Now (Crypto)
                                                <svg class="w-3.5 h-3.5 ml-2 mt-0.5" aria-hidden="true"
                                                    xmlns="http://www.w3.org/2000/svg" fill="none"
                                                    viewBox="0 0 14 10">
                                                    <path stroke="currentColor" stroke-linecap="round"
                                                        stroke-linejoin="round" stroke-width="2"
                                                        d="M1 5h12m0 0L9 1m4 4L9 9" />
                                                </svg>
                                            </span>
                                        </button>

                                        <button type="button"
                                            class="text-cyan-700 border-2 border-cyan-700 hover:bg-cyan-700 hover:text-white focus:ring-0 focus:outline-none transition duration-200 font-medium rounded-lg text-sm px-5 py-2.5 text-center items-center mb-3 w-full"
                                            data-modal-target="cashapp-purchase" data-modal-toggle="cashapp-purchase">
                                                <span class="inline-flex">
                                                    Purchase Seller Now (Cashapp)
                                                    <svg class="w-3.5 h-3.5 ml-2 mt-0.5" aria-hidden="true"
                                                        xmlns="http://www.w3.org/2000/svg" fill="none"
                                                        viewBox="0 0 14 10">
                                                        <path stroke="currentColor" stroke-linecap="round"
                                                            stroke-linejoin="round" stroke-width="2"
                                                            d="M1 5h12m0 0L9 1m4 4L9 9" />
                                                    </svg></span>
                                            </a>
                                        </button>
                                    </div>
                                </div>

                                <div class="w-full p-7 animate-on-scroll-in-up">
                                    <div class="h-full p-8 border border-yellow-700 rounded-xl bg-[#09090d]">
                                        <div class="flex flex-wrap justify-between border-b border-gray-800 pb-7 mb-7">
                                            <div class="w-full xl:w-auto">
                                                <h3 class="font-bold text-2xl text-white">Enterprise <span
                                                        class="text-xs">(Source
                                                        Code)</span></h3>
                                            </div>
                                            <div class="w-full xl:w-auto xl:text-right">
                                                <h3
                                                    class="mb-0.5 font-bold text-2xl text-transparent bg-clip-text bg-gradient-to-r to-[#eeba0b] from-[#f4e409]">
                                                    <span class="text-sm mr-0.5">$</span>79.99 / One-Time
                                                </h3>
                                                <p class="text-sm text-gray-400">
                                                    Opt for large-scale projects at $79.99 one-time cost. <br>Access
                                                    paid KeyAuth
                                                    features' source code, one-on-one setup support, and more.
                                                </p>
                                            </div>
                                        </div>
                                        <ul class="mb-8 md:grid md:grid-cols-2">
                                            <li class="flex items-center mb-4 font-medium text-base text-white">
                                                <svg class="h-5 w-5 mr-2.5 text-red-500" fill="currentColor"
                                                    viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                                    <path
                                                        d="M12 2C6.486 2 2 6.486 2 12s4.486 10 10 10 10-4.486 10-10S17.514 2 12 2Zm4.207 12.793-1.414 1.414L12 13.414l-2.793 2.793-1.414-1.414L10.586 12 7.793 9.207l1.414-1.414L12 10.586l2.793-2.793 1.414 1.414L13.414 12l2.793 2.793Z">
                                                    </path>
                                                </svg>
                                                <p>
                                                    Cloud Hosted Subscription (KeyAuth Servers)
                                                </p>
                                            </li>
                                            <li class="flex items-center mb-4 font-medium text-base text-white">
                                                <svg class="h-5 w-5 mr-2.5 text-green-500" fill="currentColor"
                                                    viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                                    <path
                                                        d="M12 2C6.486 2 2 6.486 2 12s4.486 10 10 10 10-4.486 10-10S17.514 2 12 2Zm-1.999 14.413-3.713-3.705L7.7 11.292l2.299 2.295 5.294-5.294 1.414 1.414-6.706 6.706Z">
                                                    </path>
                                                </svg>
                                                <p>
                                                    Source code of paid KeyAuth features
                                                </p>
                                            </li>
                                            <li class="flex items-center mb-4 font-medium text-base text-white">
                                                <svg class="h-5 w-5 mr-2.5 text-green-500" fill="currentColor"
                                                    viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                                    <path
                                                        d="M12 2C6.486 2 2 6.486 2 12s4.486 10 10 10 10-4.486 10-10S17.514 2 12 2Zm-1.999 14.413-3.713-3.705L7.7 11.292l2.299 2.295 5.294-5.294 1.414 1.414-6.706 6.706Z">
                                                    </path>
                                                </svg>
                                                <p>
                                                    One-on-one setup support
                                                </p>
                                            </li>
                                            <li class="flex items-center mb-4 font-medium text-base text-white">
                                                <svg class="h-5 w-5 mr-2.5 text-green-500" fill="currentColor"
                                                    viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                                    <path
                                                        d="M12 2C6.486 2 2 6.486 2 12s4.486 10 10 10 10-4.486 10-10S17.514 2 12 2Zm-1.999 14.413-3.713-3.705L7.7 11.292l2.299 2.295 5.294-5.294 1.414 1.414-6.706 6.706Z">
                                                    </path>
                                                </svg>
                                                <p>
                                                    Tutorial video showing how to host KeyAuth for 100% free
                                                </p>
                                            </li>
                                        </ul>
                                        <?php 
                                        echo '<button data-sellix-product="6361c20e415e9" type="submit"
                                        class="text-[#eeba0b] border-2 border-[#eeba0b] hover:bg-[#eeba0b] hover:text-white focus:ring-0 focus:outline-none transition duration-200 font-medium rounded-lg text-sm px-5 py-2.5 text-center items-center mb-3 w-full">                                
                                        <span class="inline-flex">
                                                Purchase Enterprise Now
                                                <svg class="w-3.5 h-3.5 ml-2 mt-0.5" aria-hidden="true"
                                                    xmlns="http://www.w3.org/2000/svg" fill="none"
                                                    viewBox="0 0 14 10">
                                                    <path stroke="currentColor" stroke-linecap="round"
                                                        stroke-linejoin="round" stroke-width="2"
                                                        d="M1 5h12m0 0L9 1m4 4L9 9" />
                                                </svg>
                                            </span>
                                        </button>

                                        <button type="button"
                                            class="text-[#eeba0b] border-2 border-[#eeba0b] hover:bg-[#eeba0b] hover:text-black focus:ring-0 focus:outline-none transition duration-200 font-medium rounded-lg text-sm px-5 py-2.5 text-center items-center mb-3 w-full">
                                            <a href="https://paddle.keyauth.cc/enterprise/?ownerid=' . urlencode($_SESSION['username']) . '" target="_blank">
                                                <span class="inline-flex">
                                                    Purchase Enterprise Now (PayPal)
                                                    <svg class="w-3.5 h-3.5 ml-2 mt-0.5" aria-hidden="true"
                                                        xmlns="http://www.w3.org/2000/svg" fill="none"
                                                        viewBox="0 0 14 10">
                                                        <path stroke="currentColor" stroke-linecap="round"
                                                            stroke-linejoin="round" stroke-width="2"
                                                            d="M1 5h12m0 0L9 1m4 4L9 9" />
                                                    </svg>
                                                </span>
                                            </a>
                                        </button>';
                                        ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </section>

                    <!-- cashapp-purchase Modal -->
                    <div id="cashapp-purchase" tabindex="-1"
                        class="fixed top-0 left-0 right-0 z-50 hidden p-4 overflow-x-hidden overflow-y-auto md:inset-0 h-[calc(100%-1rem)] max-h-full">
                        <div class="relative w-full max-w-md max-h-full">
                            <div class="relative bg-[#0f0f17] border border-red-700 rounded-lg shadow">
                                <div class="p-6 text-center">
                                    <form method="post">
                                        <div class="flex items-center p-4 mb-4 text-sm text-white border border-yellow-500 rounded-lg bg-[#0f0f17]"
                                            role="alert">
                                            <span class="sr-only">Info</span>
                                            <div>
                                                <span class="font-medium">Notice!</span> Paying with CashApp does not automatically upgrade you! <b>It can take up to 2 days to upgrade your account if you decide to purchase via CashApp</b>.
                                            </div>
                                        </div>

                                        <div class="relative mb-4">
                                            <div class="grid place-items-center">
                                                <img class="h-auto max-w-xl rounded-lg shadow-xl" src="https://cdn.keyauth.cc/v3/imgs/KeyAuthLLC CashAppImg.png" alt="Failed to load CashTag QR Code">
                                            </div>
                                        </div>


                                        <p class="text-sm text-gray-400 mb-5">
                                            Scan the QR code to pay with CashApp. Make sure you include your owner ID in the note so that you are upgraded successfully!
                                            You must also contact support and send them a screenshot of your order, so that the process can go faster. 
                                        </p>
                                        <button data-modal-hide="cashapp-purchase" type="button"
                                            class="inline-flex text-white bg-gray-700 hover:opacity-60 focus:ring-0 font-medium rounded-lg text-sm px-5 py-2.5 mb-2 transition duration-200">No,
                                            cancel</button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- cashapp-purchase Modal -->

                    <!--Flowbite JS-->
                    <script src="https://cdn.keyauth.cc/v3/dist/flowbite.js"></script>

                    <!-- jqeury -->
                    <script src="https://cdn.keyauth.cc/v3/scripts/jquery.min.js"></script>

                    <script>
                    $(document).ready(function() {
                        // Function to update text and button attribute
                        function updateTextAndButtonAttribute() {
                            if ($("#pricing_anually_month").is(':checked')) {
                                $("#tester_mode").text("0 / Year");
                                $("#developer_mode").text("14.99 / Year");
                                $("#seller_mode").text("24.99 / Year");
                                $("#sellixButtonSeller").attr("data-sellix-product", "6011e42262457");
                                $("#sellixButtonDeveloper").attr("data-sellix-product", "6011e41969f3d");
                            } else {
                                $("#tester_mode").text("0 / Year");
                                $("#developer_mode").text("2.99 / Month");
                                $("#seller_mode").text("4.99 / Month");
                                $("#sellixButtonSeller").attr("data-sellix-product", "65475b6dbe8e2");
                                $("#sellixButtonDeveloper").attr("data-sellix-product", "6547557555143");
                            }
                        }

                        // Initial setup
                        updateTextAndButtonAttribute();

                        // Toggle event handler
                        $("#pricing_anually_month").click(function() {
                            updateTextAndButtonAttribute();
                        });
                    });
                    </script>