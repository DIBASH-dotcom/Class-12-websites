<?php
header('Content-Type: text/plain');

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_POST['question'])) {
    http_response_code(400);
    echo "Invalid request";
    exit;
}

$question = trim($_POST['question']);
if ($question === '') {
    echo "Please provide a question.";
    exit;
}

$api_key = "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";
$model = "tiiuae/falcon-7b-instruct";

$data = json_encode([
    "inputs" => $question,
    "parameters" => [ "max_new_tokens" => 150 ]
]);

$ch = curl_init("https://api-inference.huggingface.co/models/$model");
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    "Authorization: Bearer $api_key",
    "Content-Type: application/json"
]);
curl_setopt($ch, CURLOPT_POSTFIELDS, $data);

$response = curl_exec($ch);

if ($response === false) {
    echo "Error connecting to Hugging Face API: " . curl_error($ch);
    curl_close($ch);
    exit;
}

curl_close($ch);

$responseData = json_decode($response, true);

echo "Raw response:\n";
print_r($responseData);
echo "\n\n";

if (isset($responseData[0]['generated_text'])) {
    echo "✅ AI उत्तर:\n";
    echo trim($responseData[0]['generated_text']);
} elseif (isset($responseData['error'])) {
    echo "❌ Hugging Face Error: " . $responseData['error'];
} else {
    echo "❌ Failed to get AI response.";
}
