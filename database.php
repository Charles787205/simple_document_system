<?php
class Database
{
  private $servername = "localhost";
  private $username = "root";
  private $password = null;
  private $dbname = "document_routing_system";
  public $conn;
  function __construct()
  {

    $this->conn = new mysqli($this->servername, $this->username, $this->password, $this->dbname);
    if ($this->conn->connect_error) {
      die("Connection failed: " . $this->conn->connect_error);
    }
  }

  function getOffices()
  {
    $offices = array();

    $sql = "SELECT * FROM Office";
    $result = $this->conn->query($sql);


    while ($row = $result->fetch_assoc()) {
      $office = array(
        'OfficeID' => $row['OfficeID'],
        'OfficeName' => $row['OfficeName'],
        'Location' => $row['Location']
      );
      $offices[] = $office;
    }


    return $offices;
  }
  function getDocumentsForOffice($officeId)
  {
    $documents = array();


    $sql = "SELECT d.*, o.OfficeName AS OriginatingOfficeName
            FROM Document d
            INNER JOIN DocumentTrail dt ON d.DocumentID = dt.DocumentID
            INNER JOIN Office o ON d.OriginatingOffice = o.OfficeID
            WHERE dt.OfficeID = $officeId
            AND dt.time = (
                SELECT MAX(time)
                FROM DocumentTrail
                WHERE DocumentID = d.DocumentID
            )";

    $result = $this->conn->query($sql);

    while ($row = $result->fetch_assoc()) {
      $document = array(
        'DocumentID' => $row['DocumentID'],
        'OriginatingOffice' => $row['OriginatingOfficeName'],
        'TerminationStatus' => $row['TerminationStatus'],
        'Title' => $row['Title'],
        'Text' => $row['Text']
        // Add more fields as needed
      );
      $documents[] = $document;
    }

    return $documents;
  }

  public function addDocument($originatingOffice, $title, $text)
  {
    $terminationStatus = 'Pending';
    $originatingOffice = $this->conn->real_escape_string($originatingOffice);
    $terminationStatus = $this->conn->real_escape_string($terminationStatus);
    $title = $this->conn->real_escape_string($title);
    $text = $this->conn->real_escape_string($text);

    $sql = "INSERT INTO Document (OriginatingOffice, TerminationStatus, Title, Text)
            VALUES ('$originatingOffice', '$terminationStatus', '$title', '$text')";

    if ($this->conn->query($sql)) {

      return $this->conn->insert_id;
    } else {
      return false;
    }
  }
  public function addDocumentTrail($documentID, $officeID)
  {

    $documentID = $this->conn->real_escape_string($documentID);
    $officeID = $this->conn->real_escape_string($officeID);


    $sql = "INSERT INTO DocumentTrail (DocumentID, OfficeID)
            VALUES ('$documentID', '$officeID')";

    if ($this->conn->query($sql)) {
      return true;
    } else {
      return false;
    }
  }
  public function getDocumentById($documentID)
  {
    $documentID = $this->conn->real_escape_string($documentID);

    $sql = "SELECT * FROM Document WHERE DocumentID = $documentID";
    $result = $this->conn->query($sql);

    if ($result->num_rows > 0) {
      $row = $result->fetch_assoc();

      $document = array(
        'DocumentID' => $row['DocumentID'],
        'OriginatingOffice' => $row['OriginatingOffice'],
        'TerminationStatus' => $row['TerminationStatus'],
        'Title' => $row['Title'],
        'Text' => $row['Text'],

      );

      return $document;
    } else {
      return null;
    }
  }
  public function editDocumentStatus($documentID, $newStatus)
  {
    $documentID = $this->conn->real_escape_string($documentID);
    $newStatus = $this->conn->real_escape_string($newStatus);

    $sql = "UPDATE Document SET TerminationStatus = '$newStatus' WHERE DocumentID = '$documentID'";

    if ($this->conn->query($sql)) {
      return true;
    } else {
      return false;
    }
  }
  public function deleteDocument($documentID)
  {
    $documentID = $this->conn->real_escape_string($documentID);

    $sql = "DELETE FROM Document WHERE DocumentID = '$documentID'";

    return $this->conn->query($sql);
  }
}
