<?php

// API URL
$apiUrl = "http://localhost/crm/public/api/leads/add";

// Sample Lead Data
$data = [
    "organization_id"     => 1,
    "created_by"          => 2,
    "name"                => "Core PHP Test Lead",
    "mobile"              => "9998887776",
    "email"               => "core@test.com",
    "upload_via"          => "bulk",
    "source_type"         => "bulk_cron",
    "application_form_id" => 1,   // ✅ Added compulsory field
    "institute_id"        => 1,
    "programme_id"        => 1,
      "lead_marketing_event"=> 1,   // ✅ Added this
	  "faculty_id"          => 1
];

// Convert to JSON
$jsonData = json_encode($data);

// Initialize cURL
$ch = curl_init($apiUrl);

curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    "Content-Type: application/json",
    "Content-Length: " . strlen($jsonData)
]);
curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonData);

// Execute request
$response = curl_exec($ch);

// Error handling
if (curl_errno($ch)) {
    echo "cURL Error: " . curl_error($ch);
} else {
    echo "<h3>API Response:</h3>";
    echo "<pre>";
    print_r(json_decode($response, true));
    echo "</pre>";
}

curl_close($ch);
?>