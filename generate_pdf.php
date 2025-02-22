<?php
require_once('tcpdf/tcpdf.php');
require 'db_conection.php';

// Get room ID from URL
$room_id = (int)$_GET['room_id'];

// Fetch room details
$room_query = "SELECT * FROM rooms WHERE id = ?";
$stmt = $conn->prepare($room_query);
$stmt->bind_param("i", $room_id);
$stmt->execute();
$room = $stmt->get_result()->fetch_assoc();
$stmt->close();

// Create new PDF document
// $pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

// Set document information
// $pdf->SetCreator(PDF_CREATOR);
$pdf->SetAuthor('Your Hotel Name');
$pdf->SetTitle('Room Booking Details - ' . $room['room_number']);
$pdf->SetSubject('Room Booking Information');

// Add a page
$pdf->AddPage();

// Set font
$pdf->SetFont('helvetica', 'B', 20);
$pdf->Cell(0, 15, 'Room Booking Details', 0, 1, 'C');

// Room information
$pdf->SetFont('helvetica', 'B', 14);
$pdf->Cell(0, 10, 'Room Information', 0, 1);
$pdf->SetFont('helvetica', '', 12);

$html = '<table border="1" cellpadding="5">
    <tr>
        <th width="30%">Room Number</th>
        <td width="70%">' . htmlspecialchars($room['room_number']) . '</td>
    </tr>
    <tr>
        <th>Bed Type</th>
        <td>' . htmlspecialchars($room['bed_type']) . '</td>
    </tr>
    <tr>
        <th>Price Per Night</th>
        <td>$' . number_format($room['price'], 2) . '</td>
    </tr>
</table>';

$pdf->writeHTML($html, true, false, true, false, '');

// Booking information
$pdf->SetFont('helvetica', 'B', 14);
$pdf->Cell(0, 10, 'Booking Information', 0, 1);
$pdf->SetFont('helvetica', '', 12);

$html = '<table border="1" cellpadding="5">
    <tr>
        <th width="30%">Check-in Date</th>
        <td width="70%">' . htmlspecialchars($_GET['check_in']) . '</td>
    </tr>
    <tr>
        <th>Check-out Date</th>
        <td>' . htmlspecialchars($_GET['check_out']) . '</td>
    </tr>
    <tr>
        <th>Number of Nights</th>
        <td>' . htmlspecialchars($_GET['nights']) . '</td>
    </tr>
    <tr>
        <th>Total Amount</th>
        <td>$' . number_format($_GET['total_amount'], 2) . '</td>
    </tr>
</table>';

$pdf->writeHTML($html, true, false, true, false, '');

// Output the PDF
$pdf->Output('booking_details_' . $room['room_number'] . '.pdf', 'D');
?>