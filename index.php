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
    <div class="flex justify-between">

      <h2 class="text-[20px]" id='title'>
        Documents forwarded to your office
      </h2>
      <a href="add_doc.php" class="btn btn-outline-secondary flex items-baseline"><span class="material-symbols-outlined">
          add
        </span></a>
    </div>

    <table class="table table-striped">
      <thead>
        <tr>
          <th>Document ID</th>
          <th>Document Title</th>
          <th>Originating Office</th>
          <th>Status</th>
          <th>Action</th>
        </tr>
      </thead>
      <tbody id='table_body'>

      </tbody>
    </table>
  </div>




  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <script src="http://ajax.googleapis.com/ajax/libs/jquery/1.7.1/jquery.min.js" type="text/javascript"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.min.js" integrity="sha384-BBtl+eGJRgqQAUMxJ7pMwbEyER4l1g+O15P+16Ep7Q9Q+zqX6gSbd85u4mG4QzX+" crossorigin="anonymous"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous"></script>
  <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js" integrity="sha384-I7E8VVD/ismYTF4hNIPjVp/Zjvgyol6VFvRkX/vR+Vc4jQkC+hVqc2pM8ODewa9r" crossorigin="anonymous"></script>
  <script src="https://cdn.tailwindcss.com"></script>


  <script>
    var offices = <?php echo json_encode($offices); ?>;
    console.log(offices);
    var officeId = localStorage.getItem('office');



    window.addEventListener('DOMContentLoaded', async () => {
      if (!officeId) {
        var {
          value: officeSelected
        } = await Swal.fire({
          title: "Select Offices",
          input: "select",
          inputOptions: generateOfficeOptions(offices),
          inputPlaceholder: "Select your office",
          showCancelButton: true,
        });
        localStorage.setItem('officeId', officeSelected);
        officeId = officeSelected
        console.log(officeId);
      } else {
        getDocumentsByOfficeId(officeId);
      }

      $('#title').text(`Documents forwarded to ${offices[officeId-1].OfficeName}`)
    });

    function generateOfficeOptions(offices) {

      var officeOptions = {};
      offices.forEach((office) => {
        officeOptions[office.OfficeID] = office.OfficeName;
      });

      return officeOptions;
    }

    async function getDocumentsByOfficeId(officeID) {
      response = await fetch(
        `function_call.php?function=getDocumentsForOffice&office_id=${officeID}`
      )
      populateTable(await response.json())
    }

    function populateTable(documents) {
      const tableBody = $('#table_body');


      tableBody.empty();


      documents.forEach(document => {
        const row = $('<tr>');
        row.append(`<td>${document.DocumentID}</td>`);
        row.append(`<td>${document.Title}</td>`);
        row.append(`<td>${document.OriginatingOffice}</td>`);
        row.append(`<td>${document.TerminationStatus}</td>`);
        row.append(`
            <td>
              <a href="document_detail.php?document_id=${document.DocumentID}" class="btn text-green-500  m-0 p-[0px]">
                <span class="material-symbols-outlined">
                visibility
                </span>
              </a>
              <a onclick="editDocumentStatus(${document.DocumentID})" class="btn text-blue-600  m-0 p-[0px]">
                <span class="material-symbols-outlined">
                  edit
                </span>
              </a>
              <a onclick="deleteDocument(${document.DocumentID})"  class="btn text-red-600 m-0 p-[0px]">
                <span class="material-symbols-outlined">
                  delete
                </span>
              </a>
            </td>`);

        tableBody.append(row);
      });
    }

    function editDocumentStatus(documentID) {
      Swal.fire({
        title: "Select field validation",
        input: "select",
        inputOptions: {
          Pending: "Pending",
          Completed: "Completed",
          Cancelled: "Cancelled",

        },
        inputPlaceholder: "Select new status",
        showCancelButton: true,
      }).then((response, reject) => {
        console.log(response);
        if (response.isConfirmed) {
          fetch(`function_call.php?function=editDocumentStatus&document_id=${documentID}&status=${response.value}`).then(() => {
            Swal.fire({
              title: "Status Updated",
              icon: "success"
            }).then(
              location.reload()
            )
          })
        }
      });
    }

    function deleteDocument(documentID) {
      Swal.fire({
        title: "Are you sure?",
        text: "You won't be able to revert this!",
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#3085d6",
        cancelButtonColor: "#d33",
        confirmButtonText: "Yes, delete it!"
      }).then((result) => {
        if (result.isConfirmed) {
          fetch(`function_call.php?function=deleteDocument&document_id=${documentID}`).then(() => {
            Swal.fire({
              title: "Deleted!",
              text: "Your file has been deleted.",
              icon: "success"
            }).then(() => {
              location.reload();
            })
          })
        }
      });
    }
  </script>
</body>

</html>