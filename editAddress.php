<?php

  require 'database.php';

  session_start();

  // If not logged in, redirected to login page
  if (!isset($_SESSION["user"])) {
    header("Location: login.php");
    return;
  }

  $id = $_GET["id"];

  $statement = $conn->prepare("SELECT * FROM addresses WHERE id = :id LIMIT 1");
  $statement->execute([":id" => $id]);

  if ($statement->rowCount() == 0) {
    http_response_code(404);
    echo("HTTP 404 NOT FOUND");
    return;
  }

  $address = $statement->fetch(PDO::FETCH_ASSOC);

  $statement = $conn->prepare("SELECT * FROM contacts WHERE id = :contact_id LIMIT 1");
  $statement->execute([":contact_id" => $address["contact_id"]]);

  $contact = $statement->fetch(PDO::FETCH_ASSOC);

  if ($_SESSION["user"]["id"] != $contact["user_id"]) {
    http_response_code(403);
    echo("HTTP 403 UNAUTHORIZED");
    return;
  }

  $error = null;

  if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (empty($_POST["address_name"])) {
      $error = "Please fill address name's field.";
    } else {
      $address_name = $_POST["address_name"];

      $statement = $conn->prepare("UPDATE addresses SET address_name = :address_name WHERE id = :id");
      $statement->execute([
        ":address_name" => $address_name,
        ":id" => $id
      ]);

      $_SESSION["flash"] = ["message" => "Contact {$_POST['address_name']} updated."]; // Set a flash message for the user

      header("Location: addresses.php?id={$contact['id']}");
      return;
    }
  }

?>


<?php require "partials/header.php" ?>

  <div class="container pt-5">
    <div class="row justify-content-center">
      <div class="col-md-8">
        <div class="card">
          <div class="card-header">Edit Address</div>
          <div class="card-body">
            <?php if ($error): ?>
              <p class="text-danger">
                <?= $error ?>
              </p>
            <?php endif ?>
            <form method="POST" action="editAddress.php?id=<?= $address["id"] ?>">
              <div class="mb-3 row">
                <label for="address_name" class="col-md-4 col-form-label text-md-end">Address Name</label>
  
                <div class="col-md-6">
                  <input value="<?= $address["address_name"] ?>" id="address_name" type="text" class="form-control" name="address_name" required autocomplete="address_name" autofocus>
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
