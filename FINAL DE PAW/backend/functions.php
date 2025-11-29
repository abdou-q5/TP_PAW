<?php
// backend/functions.php
require_once __DIR__.'/config.php';


function json_response($data){
header('Content-Type: application/json; charset=utf-8');
echo json_encode($data);
exit;
}


function require_role($role) {
if (!isset($_SESSION['user'])) {
http_response_code(401);
json_response(['error' => 'Not authenticated']);
}
if ($_SESSION['user']['role'] !== $role) {
http_response_code(403);
json_response(['error' => 'Forbidden']);
}
}


function require_roles(array $roles){
if (!isset($_SESSION['user'])) json_response(['error'=>'Not authenticated']);
if (!in_array($_SESSION['user']['role'],$roles)) json_response(['error'=>'Forbidden']);
}