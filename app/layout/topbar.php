<nav class="fixed z-30 w-full bg-[#0f0f17] border-[#09090d]">
    <div class="py-3 px-3 lg:px-5 lg:pl-3">
        <div class="flex justify-between items-center">
            <div class="flex justify-start items-center">
                <div
                    class="hidden p-2 text-white rounded cursor-pointer lg:inline hover:opacity-60 transition duration-200 -ml-8">
                    <div class="w-6 h-6">
                    </div>
                </div>

                <button type="button" id="toggleSidebarMobile" aria-expanded="true" aria-controls="sidebar"
                    class="p-2 mr-2 text-white rounded cursor-pointer lg:hidden hover:opacity-60 focus:ring-0">
                    <svg id="toggleSidebarMobileHamburger" class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20"
                        xmlns="http://www.w3.org/2000/svg">
                        <path fill-rule="evenodd"
                            d="M3 5a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zM3 10a1 1 0 011-1h6a1 1 0 110 2H4a1 1 0 01-1-1zM3 15a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1z"
                            clip-rule="evenodd"></path>
                    </svg>
                    <svg id="toggleSidebarMobileClose" class="hidden w-6 h-6" fill="currentColor" viewBox="0 0 20 20"
                        xmlns="http://www.w3.org/2000/svg">
                        <path fill-rule="evenodd"
                            d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z"
                            clip-rule="evenodd"></path>
                    </svg>
                </button>

                <img src="https://cdn.keyauth.cc/v3/imgs/KeyauthBanner.png" alt="KeyAuth Icon"
                    style="max-width: 100px; height: auto;">
            </div>


            <div class="hidden md:block">
                <a href="https://keyauthdocs.apidog.io" target="_blank" type="button"
                    class="inline-flex text-white bg-[#0f0f17] hover:opacity-60 hover:text-blue-700 focus:ring-0 font-medium rounded-lg text-sm px-5 py-2.5 mb-2 transition duration-200 pt-3"
                    style="margin-left: 150px;">
                    <i class="lni lni-code mr-2 mt-1"></i>Documentation
                </a>

                <a href="https://github.com/keyauth" target="_blank" type="button"
                    class="inline-flex text-white bg-[#0f0f17] hover:opacity-60 hover:text-blue-700 focus:ring-0 font-medium rounded-lg text-sm px-5 py-2.5 mb-2 transition duration-200 pt-3"
                    style="margin-left: 75;">
                    <i class="lni lni-github-original mr-2 mt-1"></i>Examples
                </a>

                <a href="https://youtube.com/keyauth" target="_blank" type="button"
                    class="inline-flex text-white bg-[#0f0f17] hover:opacity-60 hover:text-blue-700 focus:ring-0 font-medium rounded-lg text-sm px-5 py-2.5 mb-2 transition duration-200 pt-3"
                    style="margin-left: 75;">
                    <i class="lni lni-youtube mr-2 mt-1"></i>YouTube
                </a>

                <a href="https://t.me/keyauth" target="_blank" type="button"
                    class="inline-flex text-white bg-[#0f0f17] hover:opacity-60 hover:text-blue-700 focus:ring-0 font-medium rounded-lg text-sm px-5 py-2.5 mb-2 transition duration-200 pt-3"
                    style="margin-left: 75;">
                    <i class="lni lni-telegram-original mr-2 mt-1"></i>Telegram
                </a>

                <a href="https://twitter.com/keyauth" target="_blank" type="button"
                    class="inline-flex text-white bg-[#0f0f17] hover:opacity-60 hover:text-blue-700 focus:ring-0 font-medium rounded-lg text-sm px-5 py-2.5 mb-2 transition duration-200 pt-3"
                    style="margin-left: 75;">
                    <i class="lni lni-twitter-original mr-2 mt-1"></i>Twitter
                </a>

                <a href="https://instagram.com/keyauthllc" target="_blank" type="button"
                    class="inline-flex text-white bg-[#0f0f17] hover:opacity-60 hover:text-blue-700 focus:ring-0 font-medium rounded-lg text-sm px-5 py-2.5 mb-2 transition duration-200 pt-3"
                    style="margin-left: 75;">
                    <i class="lni lni-instagram-original mr-2 mt-1"></i>Instagram
                </a>

                <a href="https://tiktok.com/@keyauth" target="_blank" type="button"
                    class="inline-flex text-white bg-[#0f0f17] hover:opacity-60 hover:text-blue-700 focus:ring-0 font-medium rounded-lg text-sm px-5 py-2.5 mb-2 transition duration-200 pt-3"
                    style="margin-left: 75;">
                    <i class="lni lni-tiktok-alt mr-2 mt-1"></i>TikTok
                </a>

                <a href="https://vaultcord.com/?utm_source=keyauth" target="_blank" type="button"
                    class="inline-flex text-white bg-[#0f0f17] hover:opacity-60 hover:text-blue-700 focus:ring-0 font-medium rounded-lg text-sm px-5 py-2.5 mb-2 transition duration-200 pt-3"
                    style="margin-left: 75;">
                    <i class="lni lni-discord-alt mr-2 mt-1"></i>VaultCord - FREE Discord bot
                </a>
            </div>

            <div id="dropdownDotsHorizontal"
                class="z-10 hidden bg-[#09090d] divide-y-2 divide-[#1c64f2] border-2 border-[#1c64f2] rounded-lg shadow w-44">
                <ul class="py-2 text-white text-xs" aria-labelledby="dropdownMenuIconHorizontalButton">
                    <li class="">
                        <a href="https://keyauthdocs.apidog.io" target="_blank"
                            class="block px-4 py-2 hover:opacity-60 inline-flex">
                            <i class="lni lni-code mr-2 mt-1"></i>Documentation
                        </a>
                    </li>
                    <li>
                        <a href="https://github.com/keyauth" target="_blank"
                            class="block px-4 py-2 hover:opacity-60 inline-flex">
                            <i class="lni lni-github-original mr-2 mt-1"></i>Examples
                        </a>
                    </li>
                    <li>
                        <a href="https://youtube.com/keyauth" target="_blank"
                            class="block px-4 py-2 hover:opacity-60 inline-flex">
                            <i class="lni lni-youtube mr-2 mt-1"></i>YouTube
                        </a>
                    </li>
                    <li>
                        <a href="https://t.me/keyauth" target="_blank"
                            class="block px-4 py-2 hover:opacity-60 inline-flex">
                            <i class="lni lni-telegram-original mr-2 mt-1"></i>Telegram
                        </a>
                    </li>
                    <li>
                        <a href="https://twitter.com/keyauth" target="_blank"
                            class="block px-4 py-2 hover:opacity-60 inline-flex">
                            <i class="lni lni-twitter-original mr-2 mt-1"></i>Twitter
                        </a>
                    </li>
                    <li>
                        <a href="https://instagram.com/keyauthllc" target="_blank"
                            class="block px-4 py-2 hover:opacity-60 inline-flex">
                            <i class="lni lni-instagram-original mr-2 mt-1"></i>Instagram
                        </a>
                    </li>
                    <li>
                        <a href="https://vaultcord.com" target="_blank"
                            class="block px-4 py-2 hover:opacity-60 inline-flex">
                            <i class="lni lni-instagram-original mr-2 mt-1"></i>VaultCord
                        </a>
                    </li>
                </ul>
            </div>

            <div class="ml-auto flex items-center">
                <a type="button" data-canny-changelog style="margin-right: 5px; cursor: pointer;" class="mb-1">
                    <svg xmlns="http://www.w3.org/2000/svg" height="1.25em" viewBox="0 0 576 512" class="text-white" fill="currentColor">
                        <!--! Font Awesome Free 6.4.2 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license (Commercial License) Copyright 2023 Fonticons, Inc. -->
                        <path
                            d="M224 0c-17.7 0-32 14.3-32 32V51.2C119 66 64 130.6 64 208v18.8c0 47-17.3 92.4-48.5 127.6l-7.4 8.3c-8.4 9.4-10.4 22.9-5.3 34.4S19.4 416 32 416H416c12.6 0 24-7.4 29.2-18.9s3.1-25-5.3-34.4l-7.4-8.3C401.3 319.2 384 273.9 384 226.8V208c0-77.4-55-142-128-156.8V32c0-17.7-14.3-32-32-32zm45.3 493.3c12-12 18.7-28.3 18.7-45.3H224 160c0 17 6.7 33.3 18.7 45.3s28.3 18.7 45.3 18.7s33.3-6.7 45.3-18.7z" />
                    </svg>
                </a>

                <a class="block md:hidden" type="button" id="dropdownMenuIconHorizontalButton"
                    data-dropdown-toggle="dropdownDotsHorizontal" style="cursor: pointer;">
                    <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path
                            d="M8.465 11.293c1.133-1.133 3.109-1.133 4.242 0l.707.707 1.414-1.414-.707-.707a4.965 4.965 0 0 0-3.535-1.465A4.965 4.965 0 0 0 7.05 9.88L4.929 12a5.008 5.008 0 0 0 0 7.071 4.984 4.984 0 0 0 3.535 1.462A4.984 4.984 0 0 0 12 19.071l.707-.707-1.414-1.414-.707.707a3.007 3.007 0 0 1-4.243 0 3.005 3.005 0 0 1 0-4.243l2.122-2.12Z">
                        </path>
                        <path
                            d="m12 4.93-.707.708 1.414 1.414.707-.707a3.007 3.007 0 0 1 4.243 0 3.005 3.005 0 0 1 0 4.243l-2.122 2.12c-1.133 1.134-3.11 1.134-4.242 0l-.707-.706-1.414 1.414.707.707a4.965 4.965 0 0 0 3.535 1.465 4.965 4.965 0 0 0 3.535-1.465l2.122-2.121a5.008 5.008 0 0 0 0-7.071 5.006 5.006 0 0 0-7.071 0Z">
                        </path>
                    </svg>
                </a>
            </div>
        </div>
    </div>
</nav>