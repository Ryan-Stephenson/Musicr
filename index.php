<!DOCTYPE html>
<html>
<head>
  <title>Music Player</title>
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
  <link href="css/bootstrap.css" rel="stylesheet">
  <link href="style.css" type="text/css" rel="stylesheet"/>
  <script src="js/bootstrap.js"></script>
  <script src="js/jquery.js"></script>
  <script type="text/javascript">
      jQuery(document).ready(function($) {
      $(".scroll").click(function(event){   
        event.preventDefault();
        $('html,body').animate({scrollTop:$(this.hash).offset().top}, 500);
      });
    });

    function changeSong(name)
    {
      audio = document.getElementById("player");
      audio.src = name;
      audio.load();
      audio.play();
    }
  </script>
</head>
<body style="position:absolute;left:50%; margin-left:-350px;">
<div class="player">
  <audio id="player" preload="auto" autoplay controls></audio>
</div>
<?php
class song_data{}
#Read music data from directory
$song_list = glob('music/*/*/*.{mp3,m4a}', GLOB_BRACE);

#generate song item data
$increment = 0;
foreach($song_list as &$song_data)
{
  $song_item[$increment] = new song_data;
  $song_item[$increment]->url = $song_data;
  $song_item[$increment]->exploded_url = explode('/',$song_item[$increment]->url);
  $song_item[$increment]->artist = $song_item[$increment]->exploded_url[1];
  $song_item[$increment]->album = $song_item[$increment]->exploded_url[2];
  $song_item[$increment]->song = $song_item[$increment]->exploded_url[3];
  $increment+=1;
}

#using song item data create song data arrays
$artist = null;
$album = null;
$increment = -1;
foreach($song_item as &$song_data)
{
  if($artist != $song_data->artist)
  {
    $increment+=1;
    $artist = $song_data->artist;
    $artist_array[$increment] = $song_data->artist;
  }
  if($album != $song_data->album)
  {
    $album = $song_data->album;
    $album_array[$artist][] = $song_data->album;
  }
  $music_array[$artist][$album][] = $song_data->song;
}

#get album covers
$cover_array = glob('music/*/*/*.{jpg,png}', GLOB_BRACE);
foreach($cover_array as $cover_url)
{
  $cover_url_exploded = explode('/',$cover_url);
  $cover_artist = $cover_url_exploded[1];
  $cover_album = $cover_url_exploded[2];
  $cover_filename = $cover_url_exploded[3];
  $cover_image_array[$cover_artist][$cover_album] = $cover_filename;
}


?>
<div id="album_display">
<?php
foreach($artist_array as &$artist_name){
  foreach($album_array[$artist_name] as &$album_name)
  {
    $album_prepared_underscore = str_replace(' ','_',$album_name);
    echo '<a href="#'.$album_prepared_underscore.'" class="scroll"><div class="album_div">';
    $cover_url = './music/'.$artist_name.'/'.$album_name.'/'.$cover_image_array[$artist_name][$album_name];
    echo'<img src="'.$cover_url.'" width="50px" height="50px" style="padding-right:10px;">';
    echo '</div></a>';
  }
}
echo '<div style="clear:both"></div><br>';
?>
</div>


<table class="table table-striped music_table">
<?php

$artist=null;
$album=null;
$increment = -1;
foreach($artist_array as &$artist){
  echo '<tr class="artist_row"><td style="padding-top:25px;"><strong>'.$artist.'</strong></td>';
  foreach($album_array[$artist] as &$album)
  {
    if ($album != reset($album_array[$artist]))
    {
      echo '<tr class="artist_row"><td></td>';
    }
    $cover_url = './music/'.$artist.'/'.$album.'/'.$cover_image_array[$artist][$album];
    $album_prepared_underscore = str_replace(' ','_',$album);
    $album_prepared_plus = str_replace(' ','+',$album);
    $wikipedia_article = 'http://en.wikipedia.org/wiki/'.$album_prepared_underscore;
    $search_youtube = 'http://www.youtube.com/results?search_query='.$album_prepared_plus;
    $search_lastfm = 'http://www.last.fm/search?q='.$album_prepared_plus;
    echo '<td id="'.$album_prepared_underscore.'"><img src="'.$cover_url.'" width="50px" height="50px" style="padding-right:10px;"><i style="padding-top:25px;">'.$album.'</i><a href="#album_display" style="margin-top:15px;" class="scroll btn btn-mini return">Return</a></td>';
    echo '<tr>
          <td> 
            <a target="_blank" href='.$wikipedia_article.'><input type="button" class="btn btn-mini" value="Wikipedia Album"></a><br><br>
            <a target="_blank" href='.$search_youtube.'><input type="button" class="btn btn-mini" value="Search Album Youtube"></a><br><br>
            <a target="_blank" href='.$search_lastfm.'><input type="button" class="btn btn-mini" value="Search Album Last.fm"></a><br><br></td>
          <td class="song_background">';
    foreach($music_array[$artist][$album] as &$song)
    {
      $length_song_name = strlen($song);
      $song_no_extension = substr($song,0,($length_song_name-4));
      echo ('<div class="song">
        <input type="button" class="btn btn-mini" value="Play" onclick="changeSong(\'./music/'.$artist.'/'.$album.'/'.$song.'\')">');
      if($song != end($music_array[$artist][$album]))
      {
         echo ' '.$song_no_extension.'</div>';
      }else
      {
         echo ' '.$song_no_extension.'</div></td></tr>';
      }
    }
    echo '</td>';
  }
}
?>
</table>  
</body>
</html>
