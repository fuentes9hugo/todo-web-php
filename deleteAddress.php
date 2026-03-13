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

  // Cannot delete addresses belonging to other users' contacts.
  if ($_SESSION["user"]["id"] != $contact["user_id"]) {
    http_response_code(403);
    echo("HTTP 403 UNAUTHORIZED");
    return;
  }

  $conn->prepare("DELETE * FROM addresses WHERE id = :id")->execute([":id" => $id]);

  $_SESSION["flash"] = ["message" => "Address {$address['address_name']} deleted."]; // Set a flash message for the user

  header("Location: addresses.php?id={$contact['id']}");
