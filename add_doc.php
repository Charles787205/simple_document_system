<?php
require_once('Database.php');
$db = new Database();
$offices = $db->getOffices();
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
  <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@24,400,0,0" />
  <title>Document</title>
</head>

<body class="bg-slate-300">
  <div class="shadow-lg w-[80%] m-auto mt-5 bg-slate-100 p-4 flex flex-col gap-4">
    <h1 class="text-[30px]">Document Routing System</h1>
    <div class="flex items-center">
      <a href="index.php" class="btn btn-outline-secondary flex items-baseline">
        <span class="material-symbols-outlined">
          arrow_back
        </span>
      </a>
      <h2 class="ml-5 text-[20px]" id='title'>
        Add Document
      </h2>

    </div>
    <form action="#" method="POST">
      <input type="text" name="office_id" id="office_id" hidden>
      <div class="mb-3">
        <label for="document-title" class="form-label">Title</label>
        <input type="text" name="document-title" class="form-control" id="document-title" placeholder="Document Title">
      </div>
      <div class="mb-3">
        <label for="document-body" class="form-label">Document Body</label>
        <textarea class="form-control" name="document-body" id="document-body" rows="8"></textarea>
      </div>
      <label for="office-select" class="form-label">Send document to</label>
      <select class="form-select" name="office-select" id='office-select' aria-label="Default select example">
        <option selected>Open offices</option>
        <?php foreach ($offices as $office) : ?>
          <option value=<?php echo $office['OfficeID'] ?>><?php echo $office['OfficeName'] ?></option>
        <?php endforeach ?>

      </select>
      <button class="btn btn-secondary mt-3 float-right">
        Send Document
      </button>
    </form>

  </div>




  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <script src="http://ajax.googleapis.com/ajax/libs/jquery/1.7.1/jquery.min.js" type="text/javascript"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.min.js" integrity="sha384-BBtl+eGJRgqQAUMxJ7pMwbEyER4l1g+O15P+16Ep7Q9Q+zqX6gSbd85u4mG4QzX+" crossorigin="anonymous"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous"></script>
  <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js" integrity="sha384-I7E8VVD/ismYTF4hNIPjVp/Zjvgyol6VFvRkX/vR+Vc4jQkC+hVqc2pM8ODewa9r" crossorigin="anonymous"></script>
  <script src="https://cdn.tailwindcss.com"></script>


  <script>
    function addOfficeIdToForm() {
      $('#office_id').val(localStorage.getItem('office'));
    }
    document.addEventListener('DOMContentLoaded', () => {
      addOfficeIdToForm();
    })
  </script>
</body>

</html>
<?php
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
  $db = new Database();
  $title = $_POST['document-title'];
  $body = $_POST['document-body'];
  $sendToOffice = $_POST['office-select'];
  $originatingOffice = $_POST['office_id'];
  $document_id = $db->addDocument($originatingOffice, $title, $body);
  $db->addDocumentTrail($document_id, $sendToOffice);
}
?>