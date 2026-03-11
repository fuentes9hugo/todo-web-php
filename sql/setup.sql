DROP DATABASE IF EXISTS contacts_app;

CREATE DATABASE contacts_app;

USE contacts_app;

CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255),
    email VARCHAR(255) UNIQUE,
    password VARCHAR(255)
);

CREATE TABLE contacts (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255),
    user_id INT NOT NULL,
    phone_number VARCHAR(255),

    FOREIGN KEY (user_id) REFERENCES users(id)
);

CREATE TABLE addresses(
    id INT AUTO_INCREMENT PRIMARY KEY,
    address_name VARCHAR(255),
    contact_id INT NOT NULL,

    FOREIGN KEY (contact_id) REFERENCES contacts(id) ON DELETE CASCADE
);  --'Cascade' to be able to delete a contact which has addresses
