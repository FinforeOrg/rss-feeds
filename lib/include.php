<?php

function gen_unique_id($id = NULL, $salt = "NLKne3tlwknsedl", $length = 15)
{
  $id = ($id == NULL) ? uniqid(hash("sha512", rand()), TRUE) : $id;
  $code = hash("sha512", $id . $salt);
  return $length == NULL ? $code : substr($code, 0, $length);
}

?>