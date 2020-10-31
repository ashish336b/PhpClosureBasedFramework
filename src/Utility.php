<?php

namespace ashish336b\PhpCBF;

class Utility
{
   public function pushArr($array, $arrToPush): array
   {
      array_push($array, ...$arrToPush);
      return $array;
   }
   public function getPlaceholderName($pattern)
   {
      if (preg_match_all('/\{[a-z]+[?]?\}/', $pattern, $matches)) {
         $matches[0] = array_map(function ($value) {
            return preg_replace('/[{\?}]/', "", $value);
         }, $matches[0]);
         return $matches[0];
      }
   }
   public function replaceWithRegex($patternToReplace, $replaceWith, $string)
   {
      if (preg_match($patternToReplace, $string, $matches)) {
         return preg_replace($patternToReplace, $replaceWith, $string);
      }
      return $string;
   }
   public function combineArr($keys, $values = [])
   {
      if (!count($values) && !$keys) {
         return false;
      }
      $result = array();
      foreach ($keys as $i => $k) {
         $result[$k][] = $values[$i];
      }
      array_walk($result, function (&$v) {
         $v = (count($v) == 1) ? array_pop($v) : $v;
      });
      return $result;
   }
   public function parseURI($pattern)
   {
      $pattern = str_replace("/", "\/", $pattern);
      // below line check if /{id} is in the string and replace with regex
      $regix = $this->replaceWithRegex('/\/\{[\w]*\}/', "/([\w]*)", $pattern);
      // below line check if /{id?} is in the string and replace with apporiate regex
      $regix = $this->replaceWithRegex("/([\\\]\/\{[a-z]+\?\})/", '(?:\/([\w]*))?', $regix);
      // below line check if /{id:num} is in the string and replace with apporiate regex
      $regix = $this->replaceWithRegex('/\/\{[\w]*:num\}/', '(\/[\d]*)?', $regix);
      return $regix;
   }
}
