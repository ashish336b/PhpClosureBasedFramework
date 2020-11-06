<?php

namespace ashish336b\PhpCBF;

class Utility
{
   /**
    * pushArr
    * Description : push array and return final result. pushArr([1,2] , [3]) => returns [1,2,3]
    * @param  mixed $array
    * @param  mixed $arrToPush
    * @return array
    */
   public function pushArr($array, $arrToPush): array
   {
      array_push($array, ...$arrToPush);
      return $array;
   }
   /**
    * getPlaceholderName
    * Description : getPlaceHolderName("/ashish/{hello}/{id}") => returns ['hello' , 'id']
    * @param  mixed $pattern
    * @return void
    */
   public function getPlaceholderName($pattern)
   {
      if (preg_match_all('/\{[a-z]+[?]?\}/', $pattern, $matches)) {
         $matches[0] = array_map(function ($value) {
            return preg_replace('/[{\?}]/', "", $value);
         }, $matches[0]);
         return $matches[0];
      }
   }
   /**
    * replaceWithRegex
    * Description : replace /user/{id} => \/user\/([\w]*)
    * @param  mixed $patternToReplace
    * @param  mixed $replaceWith
    * @param  mixed $string
    * @return void
    */
   public function replaceWithRegex($patternToReplace, $replaceWith, $string)
   {
      if (preg_match($patternToReplace, $string, $matches)) {
         return preg_replace($patternToReplace, $replaceWith, $string);
      }
      return $string;
   }
   /**
    * combineArr
    * Description : convert two array into key value pair key is content of first array value is * *               content of sencond array 
    *               combineArr([id , userid] , [1 ,2]) => return [id=>1 , userid => 2]
    * @param  mixed $keys
    * @param  mixed $values
    * @return void
    */
   public function combineArr($keys, $values = [])
   {
      if (!count($values) && !$keys) {
         return false;
      }
      if (count($keys) > count($values)) {
         $fill = array_fill(count($values), count($keys) - count($values), null);
         array_push($values, ...$fill);
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
   /**
    * parseURI
    * Description : return url pattern into regex form : 
    *               /hello/ok/{userid}/{id?} => \/hello\/ok\/([\w]*)(?:\/([\w]*))?
    * @param  mixed $pattern
    * @return void
    */
   public function parseURI($pattern)
   {
      $pattern = str_replace("/", "\/", $pattern);
      // below line check if /{id} is in the string and replace with regex
      $regix = $this->replaceWithRegex('/\/\{[\w]*\}/', "/([\w]+)", $pattern);
      // below line check if /{id?} is in the string and replace with apporiate regex
      $regix = $this->replaceWithRegex("/([\\\]\/\{[a-z]+\?\})/", '(?:\/([\w]+))?', $regix);
      // below line check if /{id:num} is in the string and replace with apporiate regex
      $regix = $this->replaceWithRegex('/\/\{[\w]*:num\}/', '(\/[\d]+)?', $regix);
      return $regix;
   }
}
