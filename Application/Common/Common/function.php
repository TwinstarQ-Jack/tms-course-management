<?php
/**
 * Created by PhpStorm.
 * User: TwinstarQ
 * AIM TO: Public Functions
 */

function show($status, $message, $data = array()) {
    $result = array(
        'status' => $status,
        'message' => $message,
        'data' => $data,
    );
    exit(json_encode($result));
}

function getPHPTime() {
    return date("Y-m-d H:i:s");
}

function getPHPTime_num() {
    return date("YmdHis");
}

function getPHPTime_YYYYMM() {
    return date('Ym');
}

function getPHPTime_dHis() {
    return date('dHis');
}