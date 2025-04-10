<?php

namespace dashboard\primary;

use misc\mysql;

function time2str($date)
{
    $now = time();
    $diff = $now - $date;
    if ($diff < 60) {
        return sprintf($diff > 1 ? '%s seconds' : 'second', $diff);
    }
    $diff = floor($diff / 60);
    if ($diff < 60) {
        return sprintf($diff > 1 ? '%s minutes' : 'minute', $diff);
    }
    $diff = floor($diff / 60);
    if ($diff < 24) {
        return sprintf($diff > 1 ? '%s hours' : 'hour', $diff);
    }
    $diff = floor($diff / 24);
    if ($diff < 7) {
        return sprintf($diff > 1 ? '%s days' : 'day', $diff);
    }
    if ($diff < 30) {
        $diff = floor($diff / 7);
        return sprintf($diff > 1 ? '%s weeks' : 'week', $diff);
    }
    $diff = floor($diff / 30);
    if ($diff < 12) {
        return sprintf($diff > 1 ? '%s months' : 'month', $diff);
    }
    $diff = date('Y', $now) - date('Y', $date);
    return sprintf($diff > 1 ? '%s years' : 'year', $diff);
}
function expireCheck($username, $expires)
{
    if ($expires < time()) {
        $_SESSION['role'] = "tester";
        $query = mysql\query("UPDATE `accounts` SET `role` = 'tester' WHERE `username` = ?",[$username]);
    }
    if ($expires - time() < 2629743) // check if account expires in month or less
    {
        return true;
    } else {
        return false;
    }
}
function wh_log($webhook_url, $msg, $un, $color, $pingList)
{
    $json_data = json_encode([
        "content" => $pingList . "\n" . $un . " > " . $msg   
    ], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
    $ch = curl_init($webhook_url);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
        'Content-type: application/json'
    ));
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $json_data);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
    curl_setopt($ch, CURLOPT_HEADER, 0);
    curl_exec($ch);
    curl_close($ch);
}

function error($msg){
    $msg = addslashes($msg);

    echo '<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
          <link href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css" rel="stylesheet"/>
          <script type="text/javascript">
              toastr.options = {
                  "closeButton": true,
                  "progressBar": true,
                  "positionClass": "toast-bottom-right",
                  "timeOut": "5000",
                  "showDuration": "300",
                  "hideDuration": "1000",
                  "extendedTimeOut": "1000"
              };
              toastr.error("' . $msg . '");
          </script>';
}

function success($msg) {
    $msg = addslashes($msg);

    echo '<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
          <link href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css" rel="stylesheet"/>
          <script type="text/javascript">
              toastr.options = {
                  "closeButton": true,
                  "progressBar": true,
                  "positionClass": "toast-bottom-right",
                  "timeOut": "5000",
                  "showDuration": "300",
                  "hideDuration": "1000",
                  "extendedTimeOut": "1000"
              };
              toastr.success("' . $msg . '");
          </script>';
}

/*function error($msg)
{
    echo '<script src="https://cdn.jsdelivr.net/npm/notyf@3/notyf.min.js"></script><link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/notyf@3/notyf.min.css"><script type=\'text/javascript\'>

                

                            const notyf = new Notyf();

                            notyf

                              .error({

                                message: \'' . addslashes($msg) . '\',

                                duration: 3500,

                                dismissible: true

                              });               

                

                </script>';
}
function success($msg)
{
    echo '<script src="https://cdn.jsdelivr.net/npm/notyf@3/notyf.min.js"></script><link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/notyf@3/notyf.min.css"><script type=\'text/javascript\'>

                

                            const notyf = new Notyf();

                            notyf

                              .success({

                                message: \'' . addslashes($msg) . '\',

                                duration: 3500,

                                dismissible: true

                              });               

                

                </script>';
}*/

function popover($id, $title, $msg){
    echo '<div data-popover id="' . $id . '" role="tooltip"
            class="absolute z-10 invisible inline-block w-64 text-sm text-gray-500 transition-opacity duration-300 bg-[#09090d] rounded-lg shadow-sm opacity-0">
            <div class="px-3 py-2 bg-[#09090d]/70 rounded-t-lg">
                <h3 class="font-semibold text-white">' . $title . '</h3>
            </div>
            <div class="px-3 py-2">
                <p>' . $msg . '</p>
            </div>
            <div data-popper-arrow></div>
        </div>';
}
