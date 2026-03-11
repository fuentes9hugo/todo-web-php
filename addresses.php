<?php

  require "database.php";

  session_start();

  // If not logged in, redirected to login page
  if (!isset($_SESSION["user"])) {
    header("Location: login.php");
    return;
  }

  // If it's not a user's contact, deny access
  $statement = $conn->prepare("SELECT * FROM contacts WHERE user_id = :id");
  $statement->execute([":id" => $_SESSION["user"]["id"]]);

  $contacts = $statement->fetchAll();

  $contactIds = array_column($contacts, "id");

  if (!in_array((int)$_GET["id"], $contactIds)) {
    http_response_code(403);
    echo("HTTP 403 UNAUTHORIZED");
    return;
  }

  // Get all contact's addresses
  $statement = $conn->prepare("SELECT * FROM addresses WHERE contact_id = :id");
  $statement->execute([":id" => $_GET["id"]]);

  $addresses = $statement->fetchAll();

  ?>

<?php require "partials/header.php" ?>

  <div class="container pt-4 p-3">
    <div class="row">
      
      <?php if(count($addresses) == 0): ?>
        <div class="col-md-4 mx-auto">
          <div class="card card-body text-center">
            <p>No addresses saved for this contact</p>
            <a href="newAddress.php">Add One!</a>
          </div>
        </div>
      <?php endif ?>
      <?php foreach($addresses as $address): ?>
        <div class="col-md-4 mb-3">
          <div class="card text-center">
            <div class="card-body">
              <h3 class="card-title text-capitalize"><?=$address["address_name"] ?></h3>
              <a href="edit.php?id=<?= $address["id"] ?>" class="btn btn-secondary mb-2">Edit Address</a>
              <a href="delete.php?id=<?= $address["id"] ?>" class="btn btn-danger mb-2">Delete Address</a>
            </div>
          </div>
        </div>
      <?php endforeach ?>

    </div>
  </div>

<?php require "partials/footer.php" ?>
