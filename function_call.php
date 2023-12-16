<?php
require_once('database.php');
header("Content-Type: application/json");

$function = $_GET['function'];

$db = new Database();
switch ($function) {
  case 'getDocumentsForOffice':
    $officeId = $_GET['office_id'];
    $response = $db->getDocumentsForOffice($officeId);
    echo json_encode($response);
    break;
  case 'addDocumentTrail':

    $officeId = $_GET['office_id'];
    $document_id = $_GET['document_id'];

    echo $db->addDocumentTrail($document_id, $officeId);
    break;
  case 'editDocumentStatus':
    $document_id = $_GET['document_id'];
    $status = $_GET['status'];
    $db->editDocumentStatus($document_id, $status);
    break;
  case 'deleteDocument':
    $document_id = $_GET['document_id'];
    $db->deleteDocument($document_id);
    break;
}
