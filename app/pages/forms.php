<?php 
if ($formBanned){
    dashboard\primary\error("You were banned from submitting forms.");
    die();
}

include 'application-functions.php';

if (isset($_POST['submitStaffApplication'])){

    if (isset($_POST['appliedBefore'])) {
        $appliedBeforeValue = "Yes";
    } else {
        $appliedBeforeValue = "No";
    }

    if (isset($_POST['previousStaff'])) {
        $previousStaffValue = "Yes";
    } else {
        $previousStaffValue = "No";
    }

    if (isset($_POST['ticketAssist'])) {
        $ticketAssistValue = "Yes";
    } else {
        $ticketAssistValue = "No";
    }

    $color = 345342;
    $message = 
        "\n **KeyAuth Username:** \n" . $_SESSION['username'] .
        "\n\n **Telegram, or Email:** \n" . $_POST['staffContactName'] .
        "\n\n **Member Duration:** \n" . $_POST['memberDuration'] . 
        "\n\n **Timezone:** \n" . $_POST['timezone'] . 
        "\n\n **Programming Languages:** \n" . $_POST['programLang'] . 
        "\n\n **Join Reason:** \n" . $_POST['joinReason'] . 
        "\n\n **Additional Info:** \n" . $_POST['additionalInfo'] . 
        "\n\n **Applied Before?:** \n" . $appliedBeforeValue . 
        "\n\n **Previous Staff?:** \n" . $previousStaffValue . 
        "\n\n **Ticket Assistance?:** \n" . $ticketAssistValue . 
        "\n\n ** --- MAKE SURE YOUR REACT IF YOU ACCEPT/DENY SOMEONE AND GIVE A REASON! --- **";
    sendStaffApplication($color, $_SESSION['username'], $message);
}

if (isset($_POST['submitSuggestion'])){
    $suggestionTag = isset($_POST['suggestionType']) ? $_POST['suggestionType'] : '';
    match($suggestionTag){
        "Website" => $suggestionTag = "Website",
        "Telegram Bot" => $suggestionTag = "Telegram Bot",
        "Discord Bot" => $suggestionTag = "Discord Bot",
        "Documentation" => $suggestionTag = "Documentation",
        "Examples" => $suggestionTag = "Examples",
        "Telegram Server" => $suggestionTag = "Telegram Server",
        "API" => $suggestionTag = "API",
        "Seller API" => $suggestionTag = "Seller API",
        default => $suggestionTag = "Unhandled Error!"
    };
    MakeSuggestion($_POST['suggestionDescription'], "Videos/Images \n" . $_POST['suggestionLinks'] . "\n\nContact Information: \n" . $_POST['suggestionContactName'], $suggestionTag);
}

if (isset($_POST['reportBug'])){
    $bugTag = isset($_POST['bugType']) ? $_POST['bugType'] : '';
    match($bugTag){
        "Website" => $bugTag = "Website",
        "Telegram Bot" => $bugTag = "Telegram Bot",
        "Discord Bot" => $bugTag = "Discord Bot",
        "Documentation" => $bugTag = "Documentation",
        "Examples" => $bugTag = "Examples",
        "API" => $bugTag = "API",
        "Seller API" => $bugTag = "Seller API",
        default => $bugTag = "Unhandled Error!"
    };
    ReportBug($_POST['bugDescription'], "Videos/Images \n" . $_POST['bugLinks'] . "\n\nContact Information: \n" . $_POST['bugContactName'], $bugTag);
}

if (isset($_POST['submitReport'])){
    if (!isset($_POST['reportHonesty'])){
        dashboard\primary\error("You must be honest on your report!");
    }

    if (!isset($_POST['reportUnderstanding'])){
        dashboard\primary\error("You must fully understand you being banned if you fail to be truthful to submit your report!");
    }

    $color = 880808;
    $message = 
        "\n **KeyAuth Username:** \n" . $_SESSION['username'] . 
        "\n\n **Contact Method:** \n" . $_POST['reportcontactName'] . 
        "\n\n **Staff or Member:** \n" . $_POST['reportType'] . 
        "\n\n **Report Reason:** \n" . $_POST['reportReason'] . 
        "\n\n **Users (Reported) Contact:** \n" . $_POST['usersContact'];
    sendReport($color, $_SESSION['username'], $message);
}

if (isset($_POST['fileDmca'])){
    if (!isset($_POST["dmcaHonesty"])){
        dashboard\primary\error("You must be honest on your report!");
    }

    if (!isset($_POST["dmcaalerted"])){
        dashboard\primary\error("You must attempt to reach out to the user before submitting a DMCA request!");
    }

    if (isset($_POST['reachoutFailure'])) {
        $reachoutFailureValue = "Yes";
    } else {
        $reachoutFailureValue = "No";
    }

    $color = 880808;
    $message = 
        "\n **KeyAuth Username:** \n" . $_SESSION['username'] . 
        "\n\n **Contact Method:** \n" . $_POST['dmcaContactName'] . 
        "\n\n **DMCA Type:** \n" . $_POST['dmcaType'] . 
        "\n\n **Report Reason:** \n" . $_POST['dmcaDescription'] . 
        "\n\n **Proof:** \n" . $_POST['dmcaProof'] .
        "\n\n **User failed to respond to reach out?** \n" . $reachoutFailureValue;
    submitDMCA($color, $_SESSION['username'], $message);
}
?>

<div class="p-4 bg-[#09090d] block sm:flex items-center justify-between lg:mt-1.5">
    <div class="mb-1 w-full bg-[#0f0f17] rounded-xl">
        <div class="mb-4 p-4">
            <?php require '../app/layout/breadcrumb.php'; ?>
            <h1 class="text-xl font-semibold text-white-900 sm:text-2xl">Forms</h1>
            <p class="text-xs text-gray-500">Just about any form you need.</p>
            <br>
            <div class="p-4 flex flex-col">
                <div class="overflow-x-auto">

                    <!-- Alert Box -->
                    <div id="alert-4" class="flex items-center p-4 mb-1 text-red-600 rounded-lg bg-[#09090d]"
                        role="alert">
                        <svg class="flex-shrink-0 w-4 h-4" aria-hidden="true" xmlns="http://www.w3.org/2000/svg"
                            fill="currentColor" viewBox="0 0 20 20">
                            <path
                                d="M10 .5a9.5 9.5 0 1 0 9.5 9.5A9.51 9.51 0 0 0 10 .5ZM9.5 4a1.5 1.5 0 1 1 0 3 1.5 1.5 0 0 1 0-3ZM12 15H8a1 1 0 0 1 0-2h1v-3H8a1 1 0 0 1 0-2h2a1 1 0 0 1 1 1v4h1a1 1 0 0 1 0 2Z" />
                        </svg>
                        <span class="sr-only">Info</span>
                        <div class="ml-3 text-sm font-medium">
                            Lying, spamming, or abusing any of these forms will result in you being banned from
                            submitting any form.
                        </div>
                    </div>
                    <!-- End Alert Box -->


                    <div class="mt-3 gap-1.5 grid grid-cols-1 sm:grid-cols-2 md:block md:grid-cols-0">
                        <button
                            class="inline-flex text-white bg-blue-700 hover:opacity-60 focus:ring-0 font-medium rounded-lg text-sm px-5 py-2.5 transition duration-200"
                            id="staffButton">
                            <i class="lni lni-pencil-alt mr-2 mt-1"></i>
                            Apply For Staff
                        </button>

                        <button
                            class="inline-flex text-white bg-purple-500 hover:opacity-60 focus:ring-0 font-medium rounded-lg text-sm px-5 py-2.5 transition duration-200"
                            id="suggestionButton">
                            <i class="lni lni-bulb mr-2 mt-1"></i>
                            Make A Suggestion
                        </button>

                        <button
                            class="inline-flex text-white bg-orange-500 hover:opacity-60 focus:ring-0 font-medium rounded-lg text-sm px-5 py-2.5 transition duration-200"
                            id="bugButton">
                            <i class="lni lni-bug mr-2 mt-1"></i>
                            Report A Bug
                        </button>

                        <a href="?page=bug-bounty"
                            class="inline-flex text-white bg-green-700 hover:opacity-60 focus:ring-0 font-medium rounded-lg text-sm px-5 py-2.5 transition duration-200">
                            <i class="lni lni-warning mr-2 mt-1"></i>
                            Report A Vulnerability
                        </a>

                        <button
                            class="inline-flex text-white bg-cyan-700 hover:opacity-60 focus:ring-0 font-medium rounded-lg text-sm px-5 py-2.5 transition duration-200"
                            id="fileReportButton">
                            <i class="lni lni-folder mr-2 mt-1"></i>
                            File A Report
                        </button>

                        <button
                            class="inline-flex text-white bg-red-700 hover:opacity-60 focus:ring-0 font-medium rounded-lg text-sm px-5 py-2.5 transition duration-200"
                            id="dmcaButton">
                            <i class="lni lni-trash-can mr-2 mt-1"></i>
                            DMCA
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <div class="bg-[#09090d] block sm:flex items-center justify-between lg:mt-5">
            <div class="mb-1 w-full bg-[#0f0f17] mt-4 md:mt-2 rounded-xl">
                <div class="mb-4 p-4">

                    <div id="sect1" class="hidden">
                        <h1 class="text-xl font-semibold text-white-900 sm:text-2xl">Apply For Staff</h1>
                        <p class="text-xs text-gray-500">Join our team! As a member of staff, you will be able to assist
                            the community with any issues they are having, receive the seller subscription for free, and
                            get $10 off of Enterprise.</p>
                        <form method="POST">
                            <div class="relative mb-4 mt-5">
                                <input type="text" id="staffContactName" name="staffContactName"
                                    class="block px-2.5 pb-2.5 pt-4 w-full text-sm text-white bg-transparent rounded-lg border-1 border-gray-700 appearance-none focus:ring-0  peer"
                                    autocomplete="on" placeholder="" required>
                                <label for="staffContactName"
                                    class="absolute text-sm text-gray-400 duration-300 transform -translate-y-4 scale-75 top-2 z-10 origin-[0] bg-[#0f0f17] px-2 peer-focus:px-2 peer-focus:text-blue-600 peer-placeholder-shown:scale-100 peer-placeholder-shown:-translate-y-1/2 peer-placeholder-shown:top-1/2 peer-focus:top-2 peer-focus:scale-75 peer-focus:-translate-y-4 left-1">Telegram Link,
                                     or Email</label>
                            </div>

                            <div class="relative mb-4  ">
                                <select id="memberDuration" name="memberDuration"
                                    class="bg-[#0f0f17] border border-gray-700 text-white-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5">
                                    <option value="Less than 1 week" selected>Less than 1 week</option>
                                    <option value="More than 1 month">More than 1 month</option>
                                    <option value="More than 3 months">More than 3 months</option>
                                    <option value="More than 6 months">More than 6 months</option>
                                    <option value="More than 9 months">More than 9 months</option>
                                    <option value="More than 1 year">More than 1 year</option>
                                </select>
                                <label for="memberDuration"
                                    class="absolute text-sm text-gray-400 duration-300 transform -translate-y-4 scale-75 top-2 z-10 origin-[0] bg-[#0f0f17] px-2 peer-focus:px-2 peer-focus:text-blue-600 peer-placeholder-shown:scale-100 peer-placeholder-shown:-translate-y-1/2 peer-placeholder-shown:top-1/2 peer-focus:top-2 peer-focus:scale-75 peer-focus:-translate-y-4 left-1">How
                                    long have you been a KeyAuth member?</label>
                            </div>

                            <div class="relative mb-4">
                                <input type="text" id="timezone" name="timezone"
                                    class="block px-2.5 pb-2.5 pt-4 w-full text-sm text-white bg-transparent rounded-lg border-1 border-gray-700 appearance-none focus:ring-0  peer"
                                    autocomplete="on" placeholder="" required>
                                <label for="timezone"
                                    class="absolute text-sm text-gray-400 duration-300 transform -translate-y-4 scale-75 top-2 z-10 origin-[0] bg-[#0f0f17] px-2 peer-focus:px-2 peer-focus:text-blue-600 peer-placeholder-shown:scale-100 peer-placeholder-shown:-translate-y-1/2 peer-placeholder-shown:top-1/2 peer-focus:top-2 peer-focus:scale-75 peer-focus:-translate-y-4 left-1">What
                                    is your timezone?</label>
                            </div>

                            <div class="relative mb-4">
                                <input type="text" id="programLang" name="programLang"
                                    class="block px-2.5 pb-2.5 pt-4 w-full text-sm text-white bg-transparent rounded-lg border-1 border-gray-700 appearance-none focus:ring-0  peer"
                                    autocomplete="on" placeholder="" required>
                                <label for="programLang"
                                    class="absolute text-sm text-gray-400 duration-300 transform -translate-y-4 scale-75 top-2 z-10 origin-[0] bg-[#0f0f17] px-2 peer-focus:px-2 peer-focus:text-blue-600 peer-placeholder-shown:scale-100 peer-placeholder-shown:-translate-y-1/2 peer-placeholder-shown:top-1/2 peer-focus:top-2 peer-focus:scale-75 peer-focus:-translate-y-4 left-1">What programming
                                    languages do you know? (must be experienced in them for more than 6 months to be considered known)</label>
                            </div>

                            <div class="relative mb-4">
                                <input type="text" id="joinReason" name="joinReason"
                                    class="block px-2.5 pb-2.5 pt-4 w-full text-sm text-white bg-transparent rounded-lg border-1 border-gray-700 appearance-none focus:ring-0  peer"
                                    autocomplete="on" placeholder="" required>
                                <label for="joinReason"
                                    class="absolute text-sm text-gray-400 duration-300 transform -translate-y-4 scale-75 top-2 z-10 origin-[0] bg-[#0f0f17] px-2 peer-focus:px-2 peer-focus:text-blue-600 peer-placeholder-shown:scale-100 peer-placeholder-shown:-translate-y-1/2 peer-placeholder-shown:top-1/2 peer-focus:top-2 peer-focus:scale-75 peer-focus:-translate-y-4 left-1">Why
                                    do you want to join staff?</label>
                            </div>

                            <div class="relative mb-4">
                                <input type="text" id="additionalInfo" name="additionalInfo"
                                    class="block px-2.5 pb-2.5 pt-4 w-full text-sm text-white bg-transparent rounded-lg border-1 border-gray-700 appearance-none focus:ring-0  peer"
                                    autocomplete="on" placeholder="">
                                <label for="additionalInfo"
                                    class="absolute text-sm text-gray-400 duration-300 transform -translate-y-4 scale-75 top-2 z-10 origin-[0] bg-[#0f0f17] px-2 peer-focus:px-2 peer-focus:text-blue-600 peer-placeholder-shown:scale-100 peer-placeholder-shown:-translate-y-1/2 peer-placeholder-shown:top-1/2 peer-focus:top-2 peer-focus:scale-75 peer-focus:-translate-y-4 left-1">Additional
                                    Info?</label>
                            </div>

                            <input checked id="appliedBefore" name="appliedBefore" type="checkbox"
                                class="w-4 h-4 text-blue-600 bg-[#0f0f17] border-gray-300 rounded focus:ring-blue-500 focus:ring-2">
                            <label for="appliedBefore" class="ml-1 text-sm font-medium text-white-900">I have applied
                                before</label>

                            <input checked id="previousStaff" name="previousStaff" type="checkbox"
                                class="w-4 h-4 text-blue-600 bg-[#0f0f17] border-gray-300 rounded focus:ring-blue-500 focus:ring-2">
                            <label for="previousStaff" class="ml-1 text-sm font-medium text-white-900">I am a previous
                                staff member</label>

                            <input checked id="ticketAssist" name="ticketAssist" type="checkbox"
                                class="w-4 h-4 text-blue-600 bg-[#0f0f17] border-gray-300 rounded focus:ring-blue-500 focus:ring-2">
                            <label for="ticketAssist" class="ml-1 text-sm font-medium text-white-900">I understand I must assist with Web</label>
                            <div class="flex justify-end">

                                <button type="submit"
                                    class="inline-flex text-white bg-blue-700 hover:opacity-60 focus:ring-0 font-medium rounded-lg text-sm px-5 py-2.5 transition duration-200"
                                    name="submitStaffApplication">
                                    <i class="lni lni-arrow-right mr-2 mt-1"></i>
                                    Submit Staff Application
                                </button>

                            </div>
                        </form>
                    </div>

                    <div id="sect2" class="hidden">
                        <h1 class="text-xl font-semibold text-white-900 sm:text-2xl">Make A Suggestion</h1>
                        <p class="text-xs text-gray-500">Have a suggestion? Feel free to let us know!</p>
                        <br>
                        <!-- Alert Box -->
                        <div id="alert-4" class="flex items-center p-4 mb-4 text-yellow-800 rounded-lg bg-[#09090d]"
                            role="alert">
                            <svg class="flex-shrink-0 w-4 h-4" aria-hidden="true" xmlns="http://www.w3.org/2000/svg"
                                fill="currentColor" viewBox="0 0 20 20">
                                <path
                                    d="M10 .5a9.5 9.5 0 1 0 9.5 9.5A9.51 9.51 0 0 0 10 .5ZM9.5 4a1.5 1.5 0 1 1 0 3 1.5 1.5 0 0 1 0-3ZM12 15H8a1 1 0 0 1 0-2h1v-3H8a1 1 0 0 1 0-2h2a1 1 0 0 1 1 1v4h1a1 1 0 0 1 0 2Z" />
                            </svg>
                            <span class="sr-only">Info</span>
                            <div class="ml-3 text-sm font-medium text-yellow-500">
                                This is for new feature requests. 
                                NOT anything else. 
                                If you need account support, please use the <b><u>Chat with us</u></b>
                                on the right-hand side of your screen.
                            </div>
                        </div>
                        <!-- End Alert Box -->
                        <form method="POST">
                            <div class="relative mb-4 mt-5">
                                <input type="text" id="suggestionContactName" name="suggestionContactName"
                                    class="block px-2.5 pb-2.5 pt-4 w-full text-sm text-white bg-transparent rounded-lg border-1 border-gray-700 appearance-none focus:ring-0  peer"
                                    autocomplete="on" placeholder="" required>
                                <label for="suggestionContactName"
                                    class="absolute text-sm text-gray-400 duration-300 transform -translate-y-4 scale-75 top-2 z-10 origin-[0] bg-[#0f0f17] px-2 peer-focus:px-2 peer-focus:text-blue-600 peer-placeholder-shown:scale-100 peer-placeholder-shown:-translate-y-1/2 peer-placeholder-shown:top-1/2 peer-focus:top-2 peer-focus:scale-75 peer-focus:-translate-y-4 left-1">Telegram Link,
                                    or Email</label>
                            </div>

                            <div class="relative mb-4  ">
                                <select id="suggestionType" name="suggestionType"
                                    class="bg-[#0f0f17] border border-gray-700 text-white-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5">
                                    <option value="Website" selected>Website</option>
                                    <option value="Telegram Bot">Telegram Bot</option>
                                    <option value="Discord Bot">Discord Bot</option>
                                    <option value="Documentation">Documentation</option>
                                    <option value="Examples">Examples</option>
                                    <option value="Telegram Server">Telegram Server</option>
                                    <option value="API">API</option>
                                    <option value="Seller API">Seller API</option>
                                </select>
                                <label for="suggestionType"
                                    class="absolute text-sm text-gray-400 duration-300 transform -translate-y-4 scale-75 top-2 z-10 origin-[0] bg-[#0f0f17] px-2 peer-focus:px-2 peer-focus:text-blue-600 peer-placeholder-shown:scale-100 peer-placeholder-shown:-translate-y-1/2 peer-placeholder-shown:top-1/2 peer-focus:top-2 peer-focus:scale-75 peer-focus:-translate-y-4 left-1">What
                                    are you making a suggestion for?</label>
                            </div>

                            <div class="relative mb-4">
                                <input type="text" id="suggestionDescription" name="suggestionDescription"
                                    class="block px-2.5 pb-2.5 pt-4 w-full text-sm text-white bg-transparent rounded-lg border-1 border-gray-700 appearance-none focus:ring-0  peer"
                                    autocomplete="on" placeholder="" required>
                                <label for="suggestionDescription"
                                    class="absolute text-sm text-gray-400 duration-300 transform -translate-y-4 scale-75 top-2 z-10 origin-[0] bg-[#0f0f17] px-2 peer-focus:px-2 peer-focus:text-blue-600 peer-placeholder-shown:scale-100 peer-placeholder-shown:-translate-y-1/2 peer-placeholder-shown:top-1/2 peer-focus:top-2 peer-focus:scale-75 peer-focus:-translate-y-4 left-1">Describe
                                    the suggestion</label>
                            </div>

                            <div class="relative mb-4">
                                <input type="text" id="suggestionLinks" name="suggestionLinks"
                                    class="block px-2.5 pb-2.5 pt-4 w-full text-sm text-white bg-transparent rounded-lg border-1 border-gray-700 appearance-none focus:ring-0  peer"
                                    autocomplete="on" placeholder="">
                                <label for="suggestionLinks"
                                    class="absolute text-sm text-gray-400 duration-300 transform -translate-y-4 scale-75 top-2 z-10 origin-[0] bg-[#0f0f17] px-2 peer-focus:px-2 peer-focus:text-blue-600 peer-placeholder-shown:scale-100 peer-placeholder-shown:-translate-y-1/2 peer-placeholder-shown:top-1/2 peer-focus:top-2 peer-focus:scale-75 peer-focus:-translate-y-4 left-1">Provide
                                    links to images/videos (optional)</label>
                            </div>

                            <input checked id="firstSuggestion" name="firstSuggestion" type="checkbox"
                                class="w-4 h-4 text-blue-600 bg-[#0f0f17] border-gray-300 rounded focus:ring-blue-500 focus:ring-2">
                            <label for="firstSuggestion" class="ml-1 text-sm font-medium text-white-900">This is my
                                first
                                time
                                making this suggestion</label>

                            <input checked id="existingSuggestion" name="existingSuggestion" type="checkbox"
                                class="w-4 h-4 text-blue-600 bg-[#0f0f17] border-gray-300 rounded focus:ring-blue-500 focus:ring-2">
                            <label for="existingSuggestion" class="ml-1 text-sm font-medium text-white-900">This
                                suggestion
                                does
                                NOT exist yet</label>

                            <div class="flex justify-end">
                                <button type="submit"
                                    class="inline-flex text-white bg-purple-500 hover:opacity-60 focus:ring-0 font-medium rounded-lg text-sm px-5 py-2.5 transition duration-200"
                                    name="submitSuggestion">
                                    <i class="lni lni-arrow-right mr-2 mt-1"></i>
                                    Submit Suggestion
                                </button>
                            </div>
                        </form>
                    </div>

                    <div id="sect3" class="hidden">
                        <h1 class="text-xl font-semibold text-white-900 sm:text-2xl">Report A Bug</h1>
                        <p class="text-xs text-gray-500">Found a bug? Report it so that we can fix it ASAP!</p>
                        <br>
                        <!-- Alert Box -->
                        <div id="alert-4" class="flex items-center p-4 mb-4 text-yellow-800 rounded-lg bg-[#09090d]"
                            role="alert">
                            <svg class="flex-shrink-0 w-4 h-4" aria-hidden="true" xmlns="http://www.w3.org/2000/svg"
                                fill="currentColor" viewBox="0 0 20 20">
                                <path
                                    d="M10 .5a9.5 9.5 0 1 0 9.5 9.5A9.51 9.51 0 0 0 10 .5ZM9.5 4a1.5 1.5 0 1 1 0 3 1.5 1.5 0 0 1 0-3ZM12 15H8a1 1 0 0 1 0-2h1v-3H8a1 1 0 0 1 0-2h2a1 1 0 0 1 1 1v4h1a1 1 0 0 1 0 2Z" />
                            </svg>
                            <span class="sr-only">Info</span>
                            <div class="ml-3 text-sm font-medium text-yellow-500">
                                This is ONLY for issues found with DEFAULT settings and happens after testing many ways.
                                If you need account support, please use the <b><u>Chat with us</u></b>
                                on the right-hand side of your screen.
                            </div>
                        </div>
                        <!-- End Alert Box -->
                        <form method="POST">
                            <div class="relative mb-4 mt-5">
                                <input type="text" id="bugContactName" name="bugContactName"
                                    class="block px-2.5 pb-2.5 pt-4 w-full text-sm text-white bg-transparent rounded-lg border-1 border-gray-700 appearance-none focus:ring-0  peer"
                                    autocomplete="on" placeholder="" required>
                                <label for="bugContactName"
                                    class="absolute text-sm text-gray-400 duration-300 transform -translate-y-4 scale-75 top-2 z-10 origin-[0] bg-[#0f0f17] px-2 peer-focus:px-2 peer-focus:text-blue-600 peer-placeholder-shown:scale-100 peer-placeholder-shown:-translate-y-1/2 peer-placeholder-shown:top-1/2 peer-focus:top-2 peer-focus:scale-75 peer-focus:-translate-y-4 left-1">Telegram Link,
                                    or Email</label>
                            </div>

                            <div class="relative mb-4  ">
                                <select id="bugType" name="bugType"
                                    class="bg-[#0f0f17] border border-gray-700 text-white-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5">
                                    <option value="Website" selected>Website</option>
                                    <option value="Telegram Bot">Telegram Bot</option>
                                    <option value="Discord Bot">Discord Bot</option>
                                    <option value="Documentation">Documentation</option>
                                    <option value="Examples">Examples</option>
                                    <option value="API">API</option>
                                    <option value="Seller API">Seller API</option>
                                </select>
                                <label for="bugType"
                                    class="absolute text-sm text-gray-400 duration-300 transform -translate-y-4 scale-75 top-2 z-10 origin-[0] bg-[#0f0f17] px-2 peer-focus:px-2 peer-focus:text-blue-600 peer-placeholder-shown:scale-100 peer-placeholder-shown:-translate-y-1/2 peer-placeholder-shown:top-1/2 peer-focus:top-2 peer-focus:scale-75 peer-focus:-translate-y-4 left-1">What
                                    are you making a bug for?</label>
                            </div>

                            <div class="relative mb-4">
                                <input type="text" id="bugDescription" name="bugDescription"
                                    class="block px-2.5 pb-2.5 pt-4 w-full text-sm text-white bg-transparent rounded-lg border-1 border-gray-700 appearance-none focus:ring-0  peer"
                                    autocomplete="on" placeholder="" required>
                                <label for="bugDescription"
                                    class="absolute text-sm text-gray-400 duration-300 transform -translate-y-4 scale-75 top-2 z-10 origin-[0] bg-[#0f0f17] px-2 peer-focus:px-2 peer-focus:text-blue-600 peer-placeholder-shown:scale-100 peer-placeholder-shown:-translate-y-1/2 peer-placeholder-shown:top-1/2 peer-focus:top-2 peer-focus:scale-75 peer-focus:-translate-y-4 left-1">Describe
                                    the bug</label>
                            </div>

                            <div class="relative mb-4">
                                <input type="text" id="bugLinks" name="bugLinks"
                                    class="block px-2.5 pb-2.5 pt-4 w-full text-sm text-white bg-transparent rounded-lg border-1 border-gray-700 appearance-none focus:ring-0  peer"
                                    autocomplete="on" placeholder="">
                                <label for="bugLinks"
                                    class="absolute text-sm text-gray-400 duration-300 transform -translate-y-4 scale-75 top-2 z-10 origin-[0] bg-[#0f0f17] px-2 peer-focus:px-2 peer-focus:text-blue-600 peer-placeholder-shown:scale-100 peer-placeholder-shown:-translate-y-1/2 peer-placeholder-shown:top-1/2 peer-focus:top-2 peer-focus:scale-75 peer-focus:-translate-y-4 left-1">Provide
                                    links to images/videos (optional)</label>
                            </div>

                            <input checked id="firstBug" name="firstBug" type="checkbox"
                                class="w-4 h-4 text-blue-600 bg-[#0f0f17] border-gray-300 rounded focus:ring-blue-500 focus:ring-2">
                            <label for="firstBug" class="ml-1 text-sm font-medium text-white-900">This is my first time
                                making this bug report</label>

                            <div class="flex justify-end">
                                <button
                                    class="inline-flex text-white bg-orange-500 hover:opacity-60 focus:ring-0 font-medium rounded-lg text-sm px-5 py-2.5 transition duration-200"
                                    name="reportBug">
                                    <i class="lni lni-arrow-right mr-2 mt-1"></i>
                                    Report Bug
                                </button>
                            </div>
                        </form>
                    </div>

                    <div id="sect4" class="hidden">
                        <h1 class="text-xl font-semibold text-white-900 sm:text-2xl">File A Report</h1>
                        <p class="text-xs text-gray-500">Need to report a staff member or a member of the community?
                            Feel free to do so here.</p>
                        <form method="POST">
                            <div class="relative mb-4 mt-5">
                                <input type="text" id="reportcontactName" name="reportcontactName"
                                    class="block px-2.5 pb-2.5 pt-4 w-full text-sm text-white bg-transparent rounded-lg border-1 border-gray-700 appearance-none focus:ring-0  peer"
                                    autocomplete="on" placeholder="" required>
                                <label for="reportcontactName"
                                    class="absolute text-sm text-gray-400 duration-300 transform -translate-y-4 scale-75 top-2 z-10 origin-[0] bg-[#0f0f17] px-2 peer-focus:px-2 peer-focus:text-blue-600 peer-placeholder-shown:scale-100 peer-placeholder-shown:-translate-y-1/2 peer-placeholder-shown:top-1/2 peer-focus:top-2 peer-focus:scale-75 peer-focus:-translate-y-4 left-1">Telegram Link,
                                    or Email</label>
                            </div>

                            <div class="relative mb-4  ">
                                <select id="reportType" name="reportType"
                                    class="bg-[#0f0f17] border border-gray-700 text-white-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5">
                                    <option value="A Community Member" selected>A Community Member</option>
                                    <option value="A Staff Member">A Staff Member</option>
                                </select>
                                <label for="reportType"
                                    class="absolute text-sm text-gray-400 duration-300 transform -translate-y-4 scale-75 top-2 z-10 origin-[0] bg-[#0f0f17] px-2 peer-focus:px-2 peer-focus:text-blue-600 peer-placeholder-shown:scale-100 peer-placeholder-shown:-translate-y-1/2 peer-placeholder-shown:top-1/2 peer-focus:top-2 peer-focus:scale-75 peer-focus:-translate-y-4 left-1">Who
                                    are you reporting?</label>
                            </div>

                            <div class="relative mb-4">
                                <input type="text" id="reportReason" name="reportReason"
                                    class="block px-2.5 pb-2.5 pt-4 w-full text-sm text-white bg-transparent rounded-lg border-1 border-gray-700 appearance-none focus:ring-0  peer"
                                    autocomplete="on" placeholder="" required>
                                <label for="reportReason"
                                    class="absolute text-sm text-gray-400 duration-300 transform -translate-y-4 scale-75 top-2 z-10 origin-[0] bg-[#0f0f17] px-2 peer-focus:px-2 peer-focus:text-blue-600 peer-placeholder-shown:scale-100 peer-placeholder-shown:-translate-y-1/2 peer-placeholder-shown:top-1/2 peer-focus:top-2 peer-focus:scale-75 peer-focus:-translate-y-4 left-1">Reason
                                    for the report?</label>
                            </div>

                            <div class="relative mb-4">
                                <input type="text" id="usersContact" name="usersContact"
                                    class="block px-2.5 pb-2.5 pt-4 w-full text-sm text-white bg-transparent rounded-lg border-1 border-gray-700 appearance-none focus:ring-0  peer"
                                    autocomplete="on" placeholder="" required>
                                <label for="usersContact"
                                    class="absolute text-sm text-gray-400 duration-300 transform -translate-y-4 scale-75 top-2 z-10 origin-[0] bg-[#0f0f17] px-2 peer-focus:px-2 peer-focus:text-blue-600 peer-placeholder-shown:scale-100 peer-placeholder-shown:-translate-y-1/2 peer-placeholder-shown:top-1/2 peer-focus:top-2 peer-focus:scale-75 peer-focus:-translate-y-4 left-1">Provide
                                    a way we can contact them.</label>
                            </div>

                            <input checked id="reportHonesty" name="reportHonesty" type="checkbox"
                                class="w-4 h-4 text-blue-600 bg-[#0f0f17] border-gray-300 rounded focus:ring-blue-500 focus:ring-2">
                            <label for="reportHonesty" class="ml-1 text-sm font-medium text-white-900">My report is 100%
                                honest!</label>

                            <input checked id="reportUnderstanding" name="reportUnderstanding" type="checkbox"
                                class="w-4 h-4 text-blue-600 bg-[#0f0f17] border-gray-300 rounded focus:ring-blue-500 focus:ring-2">
                            <label for="reportUnderstanding" class="ml-1 text-sm font-medium text-white-900">I
                                understand I
                                will be banned from submitting forms if I am caught lying on this form.</label>

                            <div class="flex justify-end">
                                <button type="submit"
                                    class="inline-flex text-white bg-cyan-700 hover:opacity-60 focus:ring-0 font-medium rounded-lg text-sm px-5 py-2.5 transition duration-200"
                                    name="submitReport">
                                    <i class="lni lni-arrow-right mr-2 mt-1"></i>
                                    Submit Report
                                </button>
                            </div>
                        </form>
                    </div>


                    <div id="sect5" class="hidden">
                        <h1 class="text-xl font-semibold text-white-900 sm:text-2xl">File A DMCA Request</h1>
                        <p class="text-xs text-gray-500">Found someone breaking KeyAuth license/TOS or stealing your
                            content? Report it so we can review it.</p>

                        <form method="POST">
                            <div class="relative mb-4 mt-5">
                                <input type="text" id="dmcaContactName" name="dmcaContactName"
                                    class="block px-2.5 pb-2.5 pt-4 w-full text-sm text-white bg-transparent rounded-lg border-1 border-gray-700 appearance-none focus:ring-0  peer"
                                    autocomplete="on" placeholder="" required>
                                <label for="dmcaContactName"
                                    class="absolute text-sm text-gray-400 duration-300 transform -translate-y-4 scale-75 top-2 z-10 origin-[0] bg-[#0f0f17] px-2 peer-focus:px-2 peer-focus:text-blue-600 peer-placeholder-shown:scale-100 peer-placeholder-shown:-translate-y-1/2 peer-placeholder-shown:top-1/2 peer-focus:top-2 peer-focus:scale-75 peer-focus:-translate-y-4 left-1">Telegram Link,
                                    or Email</label>
                            </div>

                            <div class="relative mb-4  ">
                                <select id="dmcaType" name="dmcaType"
                                    class="bg-[#0f0f17] border border-gray-700 text-white-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5">
                                    <option value="KeyAuth Content" selected>KeyAuth Content</option>
                                    <option value="My Content">My Content</option>
                                </select>
                                <label for="dmcaType"
                                    class="absolute text-sm text-gray-400 duration-300 transform -translate-y-4 scale-75 top-2 z-10 origin-[0] bg-[#0f0f17] px-2 peer-focus:px-2 peer-focus:text-blue-600 peer-placeholder-shown:scale-100 peer-placeholder-shown:-translate-y-1/2 peer-placeholder-shown:top-1/2 peer-focus:top-2 peer-focus:scale-75 peer-focus:-translate-y-4 left-1">What
                                    would you like to file a DMCA for?</label>
                            </div>

                            <div class="relative mb-4">
                                <input type="text" id="dmcaProof" name="dmcaProof"
                                    class="block px-2.5 pb-2.5 pt-4 w-full text-sm text-white bg-transparent rounded-lg border-1 border-gray-700 appearance-none focus:ring-0  peer"
                                    autocomplete="on" placeholder="" required>
                                <label for="dmcaProof"
                                    class="absolute text-sm text-gray-400 duration-300 transform -translate-y-4 scale-75 top-2 z-10 origin-[0] bg-[#0f0f17] px-2 peer-focus:px-2 peer-focus:text-blue-600 peer-placeholder-shown:scale-100 peer-placeholder-shown:-translate-y-1/2 peer-placeholder-shown:top-1/2 peer-focus:top-2 peer-focus:scale-75 peer-focus:-translate-y-4 left-1">Provide
                                    links to images/videos/sites.</label>
                            </div>

                            <div class="relative mb-4">
                                <input type="text" id="dmcaDescription" name="dmcaDescription"
                                    class="block px-2.5 pb-2.5 pt-4 w-full text-sm text-white bg-transparent rounded-lg border-1 border-gray-700 appearance-none focus:ring-0  peer"
                                    autocomplete="on" placeholder="" required>
                                <label for="dmcaDescription"
                                    class="absolute text-sm text-gray-400 duration-300 transform -translate-y-4 scale-75 top-2 z-10 origin-[0] bg-[#0f0f17] px-2 peer-focus:px-2 peer-focus:text-blue-600 peer-placeholder-shown:scale-100 peer-placeholder-shown:-translate-y-1/2 peer-placeholder-shown:top-1/2 peer-focus:top-2 peer-focus:scale-75 peer-focus:-translate-y-4 left-1">Explain
                                    the reason for the request</label>
                            </div>

                            <input checked id="dmcaHonesty" name="dmcaHonesty" type="checkbox"
                                class="w-4 h-4 text-blue-600 bg-[#0f0f17] border-gray-300 rounded focus:ring-blue-500 focus:ring-2">
                            <label for="dmcaHonesty" class="ml-1 text-sm font-medium text-white-900">All info on this
                                request is 100% honest</label>

                            <input checked id="dmcaalerted" name="dmcaalerted" type="checkbox"
                                class="w-4 h-4 text-blue-600 bg-[#0f0f17] border-gray-300 rounded focus:ring-blue-500 focus:ring-2">
                            <label for="dmcaalerted" class="ml-1 text-sm font-medium text-white-900">I have alerted the
                                user
                                to take it down already</label>

                            <input checked id="reachoutFailure" name="reachoutFailure" type="checkbox"
                                class="w-4 h-4 text-blue-600 bg-[#0f0f17] border-gray-300 rounded focus:ring-blue-500 focus:ring-2">
                            <label for="reachoutFailure" class="ml-1 text-sm font-medium text-white-900">The user
                                acknowledged my reach out, but failed to respond</label>

                            <div class="flex justify-end">
                                <button type="submit"
                                    class="inline-flex text-white bg-red-700 hover:opacity-60 focus:ring-0 font-medium rounded-lg text-sm px-5 py-2.5 transition duration-200"
                                    name="fileDmca">
                                    <i class="lni lni-arrow-right mr-2 mt-1"></i>
                                    File DMCA
                                </button>
                            </div>
                        </form>
                    </div>

                    <script>
                    // Function to show the corresponding section based on the button clicked
                    function showSection(sectionId) {
                        // Hide all sections
                        const sections = document.querySelectorAll('div[id^="sect"]');
                        sections.forEach(section => {
                            section.classList.add('hidden');
                        });

                        // Show the selected section
                        const selectedSection = document.getElementById(sectionId);
                        if (selectedSection) {
                            selectedSection.classList.remove('hidden');
                        }
                    }

                    // Add event listeners to the buttons
                    const staffButton = document.getElementById('staffButton');
                    const sectSuggestion = document.getElementById('suggestionButton');
                    const sectBug = document.getElementById('bugButton');
                    const sectReport = document.getElementById('fileReportButton');
                    const sectDmca = document.getElementById('dmcaButton');

                    staffButton.addEventListener('click', function() {
                        showSection('sect1');
                    });

                    suggestionButton.addEventListener('click', function() {
                        showSection('sect2');
                    });

                    bugButton.addEventListener('click', function() {
                        showSection('sect3');
                    });

                    sectReport.addEventListener('click', function() {
                        showSection('sect4');
                    });

                    dmcaButton.addEventListener('click', function() {
                        showSection('sect5');
                    });
                    </script>