<?php
while (($line = fgets(STDIN)) !== false) {
    $layer = json_decode($line);
    if ('' === $layer->Comment) {
        continue;
    }
    var_dump($layer);
}
return;
$json = stream_get_contents(STDIN);
$data = json_decode($json);
// if (!is_array($data))
//$image = $data[0];
var_dump($json);
/*var_dump($image->Id);
var_dump($image->RepoTags);
var_dump($image->Config->Env);
var_dump($image->Config->Labels);
var_dump($image->Size);
var_dump($image->VirtualSize);
var_dump($image->RootFS);*/
