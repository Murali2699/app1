<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");
date_default_timezone_set('Asia/Calcutta');

$appeal_doc_fetch = 'https://kmutappeal.tnega.org/file_uploads/efs/esevai_appeal/uploads/';
$upload_RDO_review_doc = '/var/www/html/file_uploads/efs/admin_appeal/uploads/appeal_RDO_review_upload';
$upload_RDO_recommed_doc = '/var/www/html/file_uploads/efs/admin_appeal/uploads/appeal_RDO_recommend_upload';
$upload_sss_doc = '/var/www/html/file_uploads/efs/admin_appeal/uploads/appeal_SSS_upload';