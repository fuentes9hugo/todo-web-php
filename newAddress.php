<?php

  require 'database.php';

  session_start();

  // If not logged in, redirected to login page
  if (!isset($_SESSION["user"])) {
    header("Location: login.php");
    return;
  }

  $id = $_GET["id"];

  $statement = $conn->prepare("SELECT * FROM contacts WHERE id = :id LIMIT 1");
  $statement->execute([":id" => $id]);

  if ($statement->rowCount() == 0) {
    http_response_code(404);
    echo("HTTP 404 NOT FOUND");
    return;
  }
  
  $contact = $statement->fetch(PDO::FETCH_ASSOC);

  // Cannot add an address belonging to other user's contacts.
  if ($contact["user_id"] != $_SESSION["user"]["id"]) {
    http_response_code(403);
    echo("HTTP 403 UNAUTHORIZED");
    return;
  }

  $error = null;

  if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (empty($_POST["address_name"])) {
      $error = "Please, fill address name's field."; 
    } else {
      $conn->prepare("INSERT INTO addresses (address_name, contact_id) VALUES (:address_name, :contact_id)")->execute([":address_name" => $_POST["address_name"], ":contact_id" => $id]);
      
      $_SESSION["flash"] = ["message" => "Address {$address['address_name']} added."]; // Set a flash message for the use
      
      header("Location: addresses.php?id={$contact['id']}");
    }
  }
?>

<?php require "partials/header.php" ?>

  <div class="container pt-5">
    <div class="row justify-content-center">
      <div class="col-md-8">
        <div class="card">
          <div class="card-header">Add New Address</div>
          <div class="card-body">
            <?php if ($error): ?>
              <p class="text-danger">
                <?= $error ?>
              </p>
            <?php endif ?>
            <form method="POST" action="newAddress.php?id=<?= $contact["id"] ?>">
              <div class="mb-3 row">
                <label for="address_name" class="col-md-4 col-form-label text-md-end">Address Name</label>
  
                <div class="col-md-6">
                  <input id="address_name" type="text" class="form-control" name="address_name" required autocomplete="address_name" autofocus>
                </div>
              </div>
  
              <div class="mb-3 row">
                <div class="col-md-6 offset-md-4">
                  <button type="submit" class="btn btn-primary">Submit</button>
                </div>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>
  </div>

<?php require "partials/footer.php" ?>
