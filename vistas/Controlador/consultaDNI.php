<?php

$tipo = $_POST['tipo1'];
$documento = $_POST['dni'];

if ($tipo == 'DNI') {
    $url = 'https://api.apis.net.pe/v1/dni?numero=' . $documento;
    $token = 'apis-token-3372.azsOLY3QmzDwBvXMKCFwIvMv9pacMcW8';
} else if ($tipo == 'RUC') {
    $url = 'https://api.apis.net.pe/v1/ruc?numero=' . $documento;
    $token = 'apis-token-1.aTSI1U7KEuT-6bbbCguH-4Y8TI6KS73N';
} else {
    // Si el tipo de documento no es válido, retorna un mensaje de error
    echo json_encode(array('error' => 'El tipo de documento no es válido'));
    exit;
}

// Iniciar llamada a API
$curl = curl_init();

curl_setopt_array($curl, array(
    CURLOPT_URL => $url,
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_ENCODING => '',
    CURLOPT_MAXREDIRS => 10,
    CURLOPT_TIMEOUT => 30,
    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
    CURLOPT_CUSTOMREQUEST => 'GET',
    CURLOPT_HTTPHEADER => array(
        'Authorization: Bearer ' . $token
    ),
));

$response = curl_exec($curl);
$err = curl_error($curl);

curl_close($curl);

if ($err) {
    // Si hay un error en la conexión a la API, retorna un mensaje de error
    echo json_encode(array('error' => 'Error de conexión a la API'));
} else {
    // Si la respuesta de la API es válida, retorna los datos del usuario
//    $data = json_decode($response, true);
//    echo json_encode(array(
//        'nombre' => $data['nombres'] . ' ' . $data['apellidoPaterno'] . ' ' . $data['apellidoMaterno'],
//        'numeroDocumento' => $data['numeroDocumento']
//    ));
echo  $response;
}

?>