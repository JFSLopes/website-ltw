<?php
function get_coordinates_locationiq($address) {
    $api_key = 'MOCKED_API_KEY';
    // Format the address for the URL
    $formatted_address = urlencode($address);
    
    // Build the URL for the request to the LocationIQ API
    $url = "https://us1.locationiq.com/v1/search.php?key={$api_key}&q={$formatted_address}&format=json&limit=1";
    
    // Make the HTTP request
    $response = @file_get_contents($url);
    
    // Check if the response is valid
    if ($response === FALSE) {
        echo "Error: Could not get a response from the LocationIQ API.\n";
        return null;
    }
    
    // Decode the JSON response
    $data = json_decode($response, true);
    
    // Check if the decoding was successful
    if ($data === null && json_last_error() !== JSON_ERROR_NONE) {
        echo "Error: Failed to decode the JSON response.\n";
        return null;
    }
    
    // Check if there are results
    if (count($data) > 0) {
        // Extract the coordinates
        $location = $data[0];
        return array('lat' => $location['lat'], 'lng' => $location['lon']);
    } else {
        echo "Error: No results found.\n";
        return null;
    }
}
?>
