<?php
session_start();
header('Content-Type: application/json');

// 1. Control de seguridad: solo usuarios autenticados
if (!isset($_SESSION['usuario_id'])) {
    echo json_encode(['results' => []]);
    exit;
}

$search = $_GET['search'] ?? '';
if (empty($search)) {
    echo json_encode(['results' => []]);
    exit;
}

// 🔑 TUS CREDENCIALES DE TWITCH/IGDB (Reemplaza con las tuyas)
$clientId = '';
$clientSecret = '';

// 2. Obtener el Token de acceso OAuth de Twitch
$tokenUrl = "https://id.twitch.tv/oauth2/token?client_id=$clientId&client_secret=$clientSecret&grant_type=client_credentials";
$ch = curl_init($tokenUrl);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
$tokenResponse = curl_exec($ch);
curl_close($ch);

$tokenData = json_decode($tokenResponse, true);
$accessToken = $tokenData['access_token'] ?? '';

if (!$accessToken) {
    echo json_encode(['results' => [], 'error' => 'No se pudo autenticar con IGDB']);
    exit;
}

// 3. Consultar la API de IGDB usando el lenguaje Apicalypse
$igdbUrl = "https://api.igdb.com/v4/games";
// Buscamos por nombre, pedimos la portada, la fecha, la valoración y las plataformas
$query = 'search "' . addslashes($search) . '"; fields name, cover.url, first_release_date, total_rating, platforms.name; limit 5;';

$ch = curl_init($igdbUrl);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    "Client-ID: $clientId",
    "Authorization: Bearer $accessToken",
    "Content-Type: text/plain"
]);
curl_setopt($ch, CURLOPT_POSTFIELDS, $query);
$igdbResponse = curl_exec($ch);
curl_close($ch);

$games = json_decode($igdbResponse, true);
$formattedGames = [];

// 4. Adaptar el formato de IGDB para que coincida exactamente con lo que espera tu JavaScript
if (is_array($games)) {
    foreach ($games as $game) {
        $coverUrl = 'assets/portada-placeholder.svg';
        if (isset($game['cover']['url'])) {
            // IGDB devuelve imágenes en miniatura (t_thumb), las cambiamos por una resolución mayor (t_cover_big)
            // Y aseguramos que empiece por https:
            $coverUrl = 'https:' . str_replace('t_thumb', 't_cover_big', $game['cover']['url']);
        }

        // Convertir la fecha timestamp de IGDB a año o fecha YYYY-MM-DD
        $releasedDate = isset($game['first_release_date']) ? date('Y-m-d', $game['first_release_date']) : null;

        // IGDB da valoraciones de 0 a 100, las pasamos a escala de 0 a 5 estrellas para mantener consistencia
        $rating = isset($game['total_rating']) ? round($game['total_rating'] / 20, 1) : null;

        $formattedGames[] = [
            'id' => $game['id'],
            'name' => $game['name'],
            'background_image' => $coverUrl,
            'released' => $releasedDate,
            'rating' => $rating,
            'playtime' => 0, // IGDB no da horas promedio de juego directas, por defecto 0
            'platforms' => $game['platforms'] ?? []
        ];
    }
}

echo json_encode(['results' => $formattedGames]);