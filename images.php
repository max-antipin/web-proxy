<?php
$json = stream_get_contents(STDIN);
$data = json_decode($json);
// if (!is_array($data))
$image = $data[0];
//var_dump($image);
var_dump($image->Id);
var_dump($image->RepoTags);
var_dump($image->Config->Env);
var_dump($image->Config->Labels);
var_dump($image->Size);
var_dump($image->VirtualSize);
var_dump($image->RootFS);
