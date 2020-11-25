<?php
/**
 * This script is used to fetch Instagram posts via Instagram API.
 * To prevent overloading the API with each visitor, we will save
 * the request result in a json file and read the posts from there.
 *
 * The script must be executed less than 200 time each day.
 * 
 * @param $token // Info on how to retrieve the token: https://www.gsarigiannidis.gr/instagram-feed-api-after-june-2020/
 * @param $user // User ID can be found using the Facebook debug tool: https://developers.facebook.com/tools/debug/accesstoken/
 * @param int $limit // Add a limit to prevent excessive calls.
 */
// Set parameters
$token = "YOUR_TOKEN";
$user_id = "YOUR_USER_ID";

// Limit of post fetch 
$limit = 8;

// Refresh our long time token to make sure it is valid
$refresh_request_url = 'https://graph.instagram.com/refresh_access_token?grant_type=ig_refresh_token&access_token=' . $token;
$refresh_response = file_get_contents($refresh_request_url);

// Send a request to the Instagram API to fetch the latest posts
$medias_request_url = 'https://graph.instagram.com/' . $user_id . '?fields=media&access_token=' . $token;
$medias_response = json_decode(file_get_contents($medias_request_url));
$medias = $medias_response->media->data;
$posts = [];
$i = 0;
foreach ($medias as $media) {
    if ($i < $limit) {
        // Get details on each posts
        $posts_request_url = "https://graph.instagram.com/" . $media->id . "?fields=thumbnail_url,media_url,permalink,media_type,caption&access_token=" . $token;
        $posts_response = json_decode(file_get_contents($posts_request_url));
        $post = [
            'media_url' => $posts_response->media_url,
            'permalink' => $posts_response->permalink,
        ];
        $posts[] = $post;
    }
    $i++;
}

// Encode datas in json file
$content = json_encode($posts);

// Save datas in instagram-posts.json
$src_path = dirname(__DIR__) . '/instagram-posts.json';
file_put_contents($src_path, $content);

return 0;

?>