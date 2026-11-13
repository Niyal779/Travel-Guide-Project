# Travel Guide

Travel Guide is a PHP and MySQL web app for discovering destination guides, saving places for later, and sharing local travel knowledge. Scouts submit destination requests; an administrator reviews them before they are published.

## Features

- Browse approved destinations and filter by country, experience, and budget.
- Open destination details, read and add comments, and estimate a trip budget.
- Save and remove destinations from a personal wishlist.
- Register as a Traveller or Scout. New accounts wait for administrator approval.
- Let Scouts submit, edit, and track destination requests, and request changes to published guides.
- Let administrators verify accounts and approve or reject destination requests.
- Update profile details, profile picture, and password.
- Responsive interface for desktop and mobile screens.

## Roles

| Role | What they can do |
| --- | --- |
| Traveller | Browse destinations, comment, estimate trip costs, and manage a wishlist. |
| Scout | Submit destination guides, follow request status, and request changes to published guides. |
| Admin | Verify new accounts and review Scout submissions before publication. |

## Requirements

- PHP 7.4 or newer with PDO MySQL and Fileinfo enabled.
- MySQL 5.7+ or a compatible MariaDB release.
- Apache (XAMPP is suitable for local development).
- A browser with JavaScript enabled.

The project has no Composer or Node.js dependencies.

## Local setup with XAMPP

1. Clone or copy the project into the XAMPP `htdocs` directory. For example:

   ```text
   C:\xampp\htdocs\Travel-Guide
   ```

2. Start **Apache** and **MySQL** from the XAMPP Control Panel.

3. Create the database and tables by importing [`database/schema.sql`](database/schema.sql) in phpMyAdmin, or run it with the MySQL command-line client. The script creates a database named `travel_guide` and is intended for a fresh installation.

4. Check the database settings in [`model/db.php`](model/db.php). The defaults are for a typical local XAMPP setup (`127.0.0.1`, user `root`, no password, database `travel_guide`). Change them if your local MySQL credentials differ.

5. Open the app in a browser. If your project folder is named `Travel-Guide`, use:

   ```text
   http://localhost/Travel-Guide/
   ```

   If the folder has another name, use that folder name in the URL.

## Create the first administrator

Public registration supports Traveller and Scout accounts only. To create the first administrator:

1. Register a normal account through the site.
2. In phpMyAdmin, open the `travel_guide` database and run the following query with the email used during registration:

   ```sql
   UPDATE users
   SET role = 'admin', is_verified = 1
   WHERE email = 'your-admin-email@example.com';
   ```

3. Log out and log back in. The **Admin desk** will appear in the navigation.

After that, use the Admin desk to verify Traveller and Scout accounts and review destination submissions.

## Typical content workflow

1. A Scout registers and waits for approval.
2. An administrator verifies the Scout account.
3. The Scout submits a destination guide.
4. An administrator approves the request. The approved guide then appears in Explore and on the home page.
5. Verified Travellers can save places, comment, and use the trip cost estimator.

## Project structure

```text
asset/        CSS, JavaScript, logo, and uploaded images
config/       Authentication helpers
controller/   Authentication, profile, Scout, wishlist, and admin actions
database/     Fresh-install database schema
model/        Database access and application queries
view/         Shared layouts and user-facing pages
index.php     Home page
```

## Database tables

The fresh-install schema creates:

- `users` — account, role, verification, profile, and remember-token data.
- `posts` — approved destination guides.
- `post_requests` — Scout submissions and change requests awaiting review.
- `wishlist` — saved destinations for Travellers.
- `comments` — comments attached to destination guides.
- `cost_estimates` — optional per-destination base-cost overrides.

When there is no row in `cost_estimates`, the app uses a fallback estimate based on the destination's cost level.

## Uploaded images

Destination images are stored under `asset/uploads/posts/`; profile pictures are stored under `asset/img/`. These directories must be writable by PHP. Uploads are limited to 2 MB and accepted image types are JPG, PNG, GIF, and WEBP.

## Notes

- New Traveller and Scout accounts start unverified and need an administrator to approve them.
- The schema file is for a **fresh database**. Back up an existing database before changing its structure.
- The database credentials in `model/db.php` are local development defaults. Use environment-specific credentials for any hosted deployment and do not publish production secrets.
- No license file is included yet; add a license before distributing the project if you intend to grant reuse rights.
