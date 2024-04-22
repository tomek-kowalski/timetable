<?php


Time_Table_Template::custom_header();

$data = Flight::getFlightsdata();

$html  = '<header>';
$html .=  '<h1 class="title">Flight Timetable</h1>';
$html .=  '</header>';

$html .= '<table class="time-table">';
$html .= '<tr class="table-row"><th>Agent</th><th>Departure</th><th>Destination</th><th>Departure Date</th><th>Return Date</th><th>Airline</th><th>Price</th></tr>';

foreach ($data as $flight) {
    $agentId        = isset($flight['agentId']) ? $flight['agentId'] : '';
    $departure      = isset($flight['flight']['departure']['name']) ? $flight['flight']['departure']['name'] : '';
    $destination    = isset($flight['flight']['destination']['name']) ? $flight['flight']['destination']['name'] : '';
    $departureDate  = isset($flight['flight']['departure']['date']) ? $flight['flight']['departure']['date'] : '';
    $returnDate     = isset($flight['flight']['returnDate']) ? $flight['flight']['returnDate'] : '';
    $airline        = isset($flight['flight']['airline']['code']) ? $flight['flight']['airline']['code'] : '';
    $price          = isset($flight['price']) ? $flight['price'] : '';

    $html .= '<tr><td>' . $agentId . '</td><td>' . $departure . '</td><td>' . $destination . '</td>';
    $html .= '<td>' . $departureDate . '</td><td>' . $returnDate . '</td><td>' . $airline . '</td><td>' . $price . '</td></tr>';
}

$html .= '</table>';

echo $html;


Time_Table_Template::custom_footer();