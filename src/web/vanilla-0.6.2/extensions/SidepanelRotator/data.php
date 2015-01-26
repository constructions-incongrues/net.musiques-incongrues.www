<?php
$url_ws = 'http://data.musiques-incongrues.net/collections/link/segments/images/get?limit=1&sort_field=random&format=php';
$curl = curl_init($url_ws);
curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
$response = curl_exec($curl);
$image_data = unserialize($response);
curl_close($curl);
?>
<a href="http://www.musiques-incongrues.net/forum/discussion/<?php echo $image_data[0]['discussion_id'] ?>/" title="Extrait de <?php echo $image_data[0]['discussion_name']?>">
  <img src="<?php echo $image_data[0]['url']?>" />
</a>