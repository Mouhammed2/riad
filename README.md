# Riad

A riad booking website built with plain PHP and MySQL: a public site for rooms,
excursions and testimonials, plus an admin panel to manage rooms, bookings, users
and reviews.

## Features

- Public homepage, rooms listing and room details pages
- Client registration, login and logout
- Booking flow with a confirmation page and a generated PDF invoice
- Testimonials from guests
- Admin panel with authentication and a dashboard
- Room management with multi-image uploads
- Bookings, users and testimonials management

## Stack

- PHP (procedural, `mysqli`)
- MySQL
- Bootstrap 5, custom CSS, vanilla JavaScript
- FPDF for invoice generation

## Project layout

```
admin_panel/     admin dashboard, auth, CRUD screens
css/ js/ fonts/  front-end assets
html/            static design mockups
image/ images/   photography and icons
includ/          shared header and footer
uploads/         runtime uploads
```

## Setup

1. Create a MySQL database named `riad`.
2. Import the schema (tables: `rooms`, `room_images`, `bookings`, `users`,
   `testimonials`, `events`).
3. Point a web server at the project root and adjust the credentials in
   `db_conection.php`.
4. Open `index.php` in the browser; the admin panel lives in `admin_panel/`.

## Notes

- Images under `images/` follow the upload naming scheme
  `<random>_<original-name>.<ext>`.
- `db_conection.php` ships with local development defaults (empty root password,
  port 3307) — change them for any real deployment.
