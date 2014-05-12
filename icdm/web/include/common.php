<?php
/**
 * Common functionality across web pages
 */

function startValidSession() {
    session_start();
    if ( !isset($_SESSION['logged_in']) || $_SESSION['logged_in']!=true ) {
        redirectTo('login');
    }
}

function redirectTo($target) {
    // Pass on query parameters
    $qstring = http_build_query($_GET);
    if(!empty($qstring)) {
        $target = $target.'?'.$qstring;
    }
    header('Location:'.$target);
    exit;
}
?>