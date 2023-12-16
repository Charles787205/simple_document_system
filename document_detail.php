<?php
require_once('Database.php');
$db = new Database();
$offices = $db->getOffices();
$document = $db->getDocumentById($_GET['document_id']);
$title = $document['Title'];
$originatingOffice = '';
foreach ($offices as $office) {
  if ($office['OfficeID'] == $document['OriginatingOffice']) {
    $originatingOffice = $office['OfficeName'];
  }
}
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
        Document Details
      </h2>

    </div>
    <form action="#" method="POST" onsubmit="forwardDoc(event)">
      <input type="text" name="office_id" id="office_id" hidden>
      <div class="mb-3">
        <label for="document-title" class="form-label">Title</label>

        <input disabled value="<?php echo $title ?>" type="text" name="document-title" class="form-control" id="document-title">
      </div>
      <div class="mb-3">
        <label for="document-body" class="form-label">Document Body</label>
        <textarea disabled class="form-control" name="document-body" id="document-body" rows="8"><?php echo $document['Text'] ?></textarea>
      </div>
      <label for="office-select" class="form-label">Originating Office</label>
      <select class="form-select" disabled name="office-select" id='office-select' aria-label="Default select example">


        <option selected><?php echo $originatingOffice ?>
      </select>
      <button class="btn btn-secondary mt-4 float-right">Forward Document</button>
    </form>

  </div>




  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <script src="http://ajax.googleapis.com/ajax/libs/jquery/1.7.1/jquery.min.js" type="text/javascript"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.min.js" integrity="sha384-BBtl+eGJRgqQAUMxJ7pMwbEyER4l1g+O15P+16Ep7Q9Q+zqX6gSbd85u4mG4QzX+" crossorigin="anonymous"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous"></script>
  <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js" integrity="sha384-I7E8VVD/ismYTF4hNIPjVp/Zjvgyol6VFvRkX/vR+Vc4jQkC+hVqc2pM8ODewa9r" crossorigin="anonymous"></script>
  <script src="https://cdn.tailwindcss.com"></script>


  <script>
    var offices = <?php echo json_encode($offices); ?>;
    var documentID = <?php echo $_GET['document_id'] ?>;

    function addOfficeIdToForm() {
      $('#office_id').val(localStorage.getItem('office'));
    }
    document.addEventListener('DOMContentLoaded', () => {
      addOfficeIdToForm();
    })
    async function forwardDoc(e) {
      e.preventDefault();
      Swal.fire({
        title: "Pick office to send",
        input: "select",
        inputOptions: generateOfficeOptions(offices),
        inputPlaceholder: "Select your office",
        showCancelButton: true,
      }).then((officeSelected) => {
        console.log("office", officeSelected.value)
        console.log(documentID);
        fetch(`function_call.php?function=addDocumentTrail&document_id=${documentID}&office_id=${officeSelected.value}`).then((response) => {
          Swal.fire({
            title: "Document Sent",

            icon: "success"
          }).then(() => {
            location.href = '/database-final-exam/'
          })
        });
      })

    }

    function generateOfficeOptions(offices) {
      // Create an object with office IDs as keys and office names as values
      var officeOptions = {};
      offices.forEach((office) => {
        officeOptions[office.OfficeID] = office.OfficeName;
      });

      return officeOptions;
    }
  </script>
</body>

</html>
<?php

?>