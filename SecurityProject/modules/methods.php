<?php
class DES{

}

class RSA{

  function __construct() {}

  function findRandomPrime($p)
  {
      $min = 2;
      $max = 999;
      for ($i=rand($min, $max); $i < $max; $i++) {
          if ($this->isPrime($i) && $i != $p) {
              return $i;
          }
      }
  }
  
  function isPrime($num){
      if($num % 2 == 0) {
          return false;
      }
      
      for($i = 3; $i <= ceil(sqrt($num)); $i = $i + 2) {
          if($num % $i == 0)
              return false;
      }
      return true;
  }

  function compute_n($p, $q){
    return $p * $q;
  }

  function eular_z($p, $q){
    return ($p - 1) * ($q - 1);
  }

  function find_e($z){
    for($i = 2; $i < $z; $i++){
      if($this->coprime($i, $z)){
        return $i;
      }
    }
  }

  function gcd($e, $z){
      if ($e == 0 || $z == 0)
          return 0;

      if ($e == $z)
          return $e;

      if ($e > $z)
          return $this->gcd($e - $z, $z);

      return $this->gcd($e, $z - $e);
  }

  function coprime($e, $z){
      if ($this->gcd($e, $z) == 1)
        return true;
      return false;
  }

  function find_d($e, $z) {
    for($d=1;;$d++){
      if(($d * $e % $z) == 1){
        return $d;
      }
    }
  }

  function encrypt($m, $e, $n){
    $c = "";
    $newChar = "";
    $everySeparate = "";
    for($i = 0; $i < strlen($m); $i++){
      $newChar = bcpowmod(ord($m[$i]), $e, $n);
      $everySeparate.=strlen($newChar);
      $c.=$newChar;
    }
    return array($c, $everySeparate);
  }

  function decrypt($c, $d, $n, $everySeparate){
    $m = "";
    // echo strlen($c)."  ".$everySeparate[0]."  ".$everySeparate[1]. "  ";
    for($i = 0, $ct = 0; $i < strlen($c); $i+=$everySeparate[$ct], $ct++){
      $cc = $this->getTheCurrentChar($c, $i, $everySeparate[$ct]);
      // echo $cc. "  ";
      $m.=chr(bcpowmod($cc, $d, $n));
    }
    return $m;
  }

  function getTheCurrentChar($c, $from, $to){
    $current = "";
    for($i = 0, $j = $from; $i < $to; $i++, $j++){
      $current.=$c[$j];
    }
    return intval($current);
  }
}

class AES{
      //128 bits, key = 128 (least security) or 192 or 256 (highest security) bits [SOLVED] ==> str_split
      //Text XOR Key
    
      function keyGen($key, int $rounds)
      {
          $nibble =     array(0x00,0x01,0x02,0x03,0x04,0x05,0x06,0x07,0x08,0x09,0x0a,0x0b,0x0c,0x0d,0x0e,0x0f);
          $sboxKey  =   array(0x09,0x04,0x0a,0x0b,0x0d,0x01,0x08,0x05,0x06,0x02,0x00,0x03,0x0c,0x0e,0x0f,0x07);

          
          $keys = array();

          $allkeys = array();

          $w = str_split($key,8);
          $ct = 0;
          $w0 = $w[0];
          $w1 = $w[1];

          //echo ("<br> Key0 is: ".bindec($key)." >> [$key]<br> w0= ".bindec($w[0])." >>  [$w[0]]<br> w1= ".bindec($w[1])." >>  [$w[1]] <br>");
          //array_push($allkeys, $w0.$w1); 2 4 6 8 9 10 12 14 16 18 20

          for ($i = 0; $i < ($rounds*2); ++$i)
          {
              //$$this->RotNib = $this->RotNib(bindec($w[0]));
              $$this->RotNib = $this->RotNib(bindec($w1));
              $nibs = str_split($$this->RotNib,4);
            // echo "<br>nib0= " . $nibs[0];
              //echo "<br>nib1= " . $nibs[1];
              $nibIndex = array_search($nibs[0], $nibble);
              $x = $sboxKey[$nibIndex];

              $nibIndex = array_search($nibs[1], $nibble);
              $y = $sboxKey[$nibIndex];
              
              $subnib = $x.$y;

              //echo "<br>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>> W[$i]= $w0 ^ ".(128)." ^ ".($subnib);
              $Round_Key = ($w0) ^ dechex(128 );
              $Round_Key = $Round_Key^($subnib);

              //echo "<br>----------------- ".str_pad(decbin($Round_Key),8,"0",STR_PAD_LEFT);
              array_push($keys,$w0.$w1);


              $w0 = $w1;
              $w1 = ($Round_Key);
          }

          array_push($keys,$w0.$w1);
        // echo "<br> <br> $ct (8bit-W): ".(implode(", ",$keys)).".";

          $keysHEX = array();
          for ($i = 0; $i < count($keys); $i+=2)
          {
              array_push($allkeys,$keys[$i]);
              array_push($keysHEX,base_convert(($keys[$i]),2,16));
              //echo "<br>Round $ct: " . $keys[$i];
              $ct+=1;
          }

          //echo "<br> ----> ".(implode(", ",$allkeys)).".";
          return $keysHEX;
      }

      
      function RotNib($w)
      {
          //echo"<br>w= " . base_convert($w,16,2);
          $SR = $w >> 4; //shift Right
          $SL = $w << 4; //shift Left


          $padL = str_pad(decbin($SL),8,"0",STR_PAD_RIGHT);
          
          $L= substr($padL,strlen($padL)-8);
          $R= str_pad(decbin($SR),8,"0",STR_PAD_LEFT);

          $$this->RotNib = $SL | $SR;

        // echo("<br><br> ShiftL = ".$L."<br>ShiftR = ".$R."<br>    $this->RotNib = $$this->RotNib <br>");
          return $$this->RotNib;
      }

      function multiplication($a, $b)
    {

      $row = count($a);
      $col = count($a[0]);
      
      $result = array_fill(0, $row, array_fill(0, $col, 0));
      for ($i = 0; $i < $row; ++$i)
      {
        for ($j = 0; $j < $col; ++$j)
        {
          $result[$i][$j] = 0;
          for ($k = 0; $k < $row; ++$k)
          {
            $result[$i][$j] += $a[$i][$k] * $b[$k][$j];
          }
        }
      }

          return $result;
    }

      function addBinary($a, $b)
      {
          $result = ""; // Initialize result
          $s = 0;     // Initialize digit sum
      
          // Traverse both strings starting
          // from last characters
          $i = strlen($a) - 1;
          $j = strlen($b) - 1;
          while ($i >= 0 || $j >= 0 || $s == 1)
          {
              // Comput sum of last digits and carry
              $s += (($i >= 0)? ord($a[$i]) -
                              ord('0'): 0);
              $s += (($j >= 0)? ord($b[$j]) -
                              ord('0'): 0);
      
              // If current digit sum is 1 or 3,
              // add 1 to result
              $result = chr($s % 2 + ord('0')) . $result;
      
              // Compute carry
              $s = (int)($s / 2);
      
              // Move to next digits
              $i--; $j--;
          }
          return $result;
      }


      function MixColumns($xa)
      {   
          $M = array(array(0x02,0x01,0x01,0x03),
                    array(0x03,0x02,0x01,0x01),
                    array(0x01,0x03,0x02,0x01),
                    array(0x01,0x01,0x03,0x02));


          //a5
          $mul2 = array(  0x00,0x02,0x04,0x06,0x08,0x0a,0x0c,0x0e,0x10,0x12,0x14,0x16,0x18,0x1a,0x1c,0x1e,
                          0x20,0x22,0x24,0x26,0x28,0x2a,0x2c,0x2e,0x30,0x32,0x34,0x36,0x38,0x3a,0x3c,0x3e,
                          0x40,0x42,0x44,0x46,0x48,0x4a,0x4c,0x4e,0x50,0x52,0x54,0x56,0x58,0x5a,0x5c,0x5e,
                          0x60,0x62,0x64,0x66,0x68,0x6a,0x6c,0x6e,0x70,0x72,0x74,0x76,0x78,0x7a,0x7c,0x7e,	
                          0x80,0x82,0x84,0x86,0x88,0x8a,0x8c,0x8e,0x90,0x92,0x94,0x96,0x98,0x9a,0x9c,0x9e,
                          0xa0,0xa2,0xa4,0xa6,0xa8,0xaa,0xac,0xae,0xb0,0xb2,0xb4,0xb6,0xb8,0xba,0xbc,0xbe,
                          0xc0,0xc2,0xc4,0xc6,0xc8,0xca,0xcc,0xce,0xd0,0xd2,0xd4,0xd6,0xd8,0xda,0xdc,0xde,
                          0xe0,0xe2,0xe4,0xe6,0xe8,0xea,0xec,0xee,0xf0,0xf2,0xf4,0xf6,0xf8,0xfa,0xfc,0xfe,
                          0x1b,0x19,0x1f,0x1d,0x13,0x11,0x17,0x15,0x0b,0x09,0x0f,0x0d,0x03,0x01,0x07,0x05,
                          0x3b,0x39,0x3f,0x3d,0x33,0x31,0x37,0x35,0x2b,0x29,0x2f,0x2d,0x23,0x21,0x27,0x25,
                          0x5b,0x59,0x5f,0x5d,0x53,0x51,0x57,0x55,0x4b,0x49,0x4f,0x4d,0x43,0x41,0x47,0x45,
                          0x7b,0x79,0x7f,0x7d,0x73,0x71,0x77,0x75,0x6b,0x69,0x6f,0x6d,0x63,0x61,0x67,0x65,
                          0x9b,0x99,0x9f,0x9d,0x93,0x91,0x97,0x95,0x8b,0x89,0x8f,0x8d,0x83,0x81,0x87,0x85,
                          0xbb,0xb9,0xbf,0xbd,0xb3,0xb1,0xb7,0xb5,0xab,0xa9,0xaf,0xad,0xa3,0xa1,0xa7,0xa5,
                          0xdb,0xd9,0xdf,0xdd,0xd3,0xd1,0xd7,0xd5,0xcb,0xc9,0xcf,0xcd,0xc3,0xc1,0xc7,0xc5,
                          0xfb,0xf9,0xff,0xfd,0xf3,0xf1,0xf7,0xf5,0xeb,0xe9,0xef,0xed,0xe3,0xe1,0xe7,0xe5);


          $mul3 = array( 0x00,0x03,0x06,0x05,0x0c,0x0f,0x0a,0x09,0x18,0x1b,0x1e,0x1d,0x14,0x17,0x12,0x11,
                        0x30,0x33,0x36,0x35,0x3c,0x3f,0x3a,0x39,0x28,0x2b,0x2e,0x2d,0x24,0x27,0x22,0x21,
                        0x60,0x63,0x66,0x65,0x6c,0x6f,0x6a,0x69,0x78,0x7b,0x7e,0x7d,0x74,0x77,0x72,0x71,
                        0x50,0x53,0x56,0x55,0x5c,0x5f,0x5a,0x59,0x48,0x4b,0x4e,0x4d,0x44,0x47,0x42,0x41,	
                        0xc0,0xc3,0xc6,0xc5,0xcc,0xcf,0xca,0xc9,0xd8,0xdb,0xde,0xdd,0xd4,0xd7,0xd2,0xd1,
                        0xf0,0xf3,0xf6,0xf5,0xfc,0xff,0xfa,0xf9,0xe8,0xeb,0xee,0xed,0xe4,0xe7,0xe2,0xe1,
                        0xa0,0xa3,0xa6,0xa5,0xac,0xaf,0xaa,0xa9,0xb8,0xbb,0xbe,0xbd,0xb4,0xb7,0xb2,0xb1,
                        0x90,0x93,0x96,0x95,0x9c,0x9f,0x9a,0x99,0x88,0x8b,0x8e,0x8d,0x84,0x87,0x82,0x81,
                        0x9b,0x98,0x9d,0x9e,0x97,0x94,0x91,0x92,0x83,0x80,0x85,0x86,0x8f,0x8c,0x89,0x8a,
                        0xab,0xa8,0xad,0xae,0xa7,0xa4,0xa1,0xa2,0xb3,0xb0,0xb5,0xb6,0xbf,0xbc,0xb9,0xba,
                        0xfb,0xf8,0xfd,0xfe,0xf7,0xf4,0xf1,0xf2,0xe3,0xe0,0xe5,0xe6,0xef,0xec,0xe9,0xea,
                        0xcb,0xc8,0xcd,0xce,0xc7,0xc4,0xc1,0xc2,0xd3,0xd0,0xd5,0xd6,0xdf,0xdc,0xd9,0xda,
                        0x5b,0x58,0x5d,0x5e,0x57,0x54,0x51,0x52,0x43,0x40,0x45,0x46,0x4f,0x4c,0x49,0x4a,
                        0x6b,0x68,0x6d,0x6e,0x67,0x64,0x61,0x62,0x73,0x70,0x75,0x76,0x7f,0x7c,0x79,0x7a,
                        0x3b,0x38,0x3d,0x3e,0x37,0x34,0x31,0x32,0x23,0x20,0x25,0x26,0x2f,0x2c,0x29,0x2a,
                        0x0b,0x08,0x0d,0x0e,0x07,0x04,0x01,0x02,0x13,0x10,0x15,0x16,0x1f,0x1c,0x19,0x1a);


          


          for($i = 0 ; $i<count($M) ; $i++)
          {
              for($j = 0 ; $j<count($M[$i]) ; $j++)
              {
                  $M[$i][$j] = dechex($M[$i][$j]);
              }
          }

          for($i = 0 ; $i<count($mul2) ; $i++)
          {
              $mul2[$i] = dechex($mul2[$i]);
          }

          for($i = 0 ; $i<count($mul3) ; $i++)
          {
              $mul3[$i] = dechex($mul3[$i]);
          }


          $row = count($xa);
          $col = count($xa[0]);
          
          $result = array_fill(0, $row, array_fill(0, $col, 0)); 

          for($i = 0; $i<count($M) ; $i++)
          {
              for($j = 0; $j<count($M[$i]) ; $j++)
              {
                  $M[$i][$j] = str_pad($M[$i][$j],2,'0',STR_PAD_LEFT);
              }
          }

              // echo "<br><br> ".dechex(10)." X ".dechex(10)." = ". dechex(bcmul(hexdec(0x0a) , hexdec(0x0a)));
              // echo "<br><br>".($xa[0][0]).",  ".hexdec($xa[0][0]);
              // echo "<br><br>".($M[0][0]).",  ".hexdec($M[0][0]);
              // echo "<br><br> ".$xa[0][0]." X ".$M[0][0]." = ". dechex(bcmul(hexdec($xa[0][0]) , hexdec($M[0][0])));

              // [0][0] 0
              // [0][1] 1
              // [0][2] 2
              // [0][3] 3
              // [1][0] 4
              // [1][1] 5
              // [1][2] 6
              // [1][3] 7
              // [2][0] 8
              // [2][1] 9
              // [2][2] 10
              // [2][3] 11
              // [3][0] 12
              // [3][1] 13
              // [3][2] 14 
              // [3][3] 15

              //echo "mul2: ".hexdec($xa[0][0])." - ".$mul2[hexdec($xa[0][0])];

              $result[0][0] = dechex(hexdec($mul2[hexdec($xa[0][0])]       )^hexdec(        $mul3[hexdec($xa[0][1])]    )^hexdec(   $xa[0][2]       )^hexdec(       $xa[0][3]));
              $result[0][1] = dechex(hexdec($xa[0][0]      )^hexdec(        $mul2[hexdec($xa[0][1])]   )^hexdec(    $mul3[hexdec($xa[0][2])]       )^hexdec(        $xa[0][3]));
              $result[0][2] = dechex(hexdec($xa[0][0]      )^hexdec(        $xa[0][1]  )^hexdec(    $mul2[hexdec($xa[0][2])]      )^hexdec(     $mul3[hexdec($xa[0][3])]));
              $result[0][3] = dechex(hexdec($mul3[hexdec($xa[0][0])]       )^hexdec(        $xa[0][1]   )^hexdec(   $xa[0][2]      )^hexdec(        $mul2[hexdec($xa[0][3])]));
              
              $result[1][0] = dechex(hexdec($mul2[hexdec($xa[1][0])]       )^hexdec(        $mul3[hexdec($xa[1][1])    ]    )^hexdec(   $xa[1][2]       )^hexdec(       $xa[1][3]));
              $result[1][1] = dechex(hexdec($xa[1][0]      )^hexdec(        $mul2[hexdec($xa[1][1])]   )^hexdec(    $mul3[hexdec($xa[1][2])]       )^hexdec(        $xa[1][3]));
              $result[1][2] = dechex(hexdec($xa[1][0]      )^hexdec(        $xa[1][1]  )^hexdec(    $mul2[hexdec($xa[1][2])]      )^hexdec(     $mul3[hexdec($xa[1][3])]));
              $result[1][3] = dechex(hexdec($mul3[hexdec($xa[1][0])]       )^hexdec(        $xa[1][1]   )^hexdec(   $xa[1][2]      )^hexdec(        $mul2[hexdec($xa[1][3])]));
              
              $result[2][0] = dechex(hexdec($mul2[hexdec($xa[2][0])]       )^hexdec(        $mul3[hexdec($xa[2][1])]    )^hexdec(   $xa[2][2]       )^hexdec(       $xa[2][3]));
              $result[2][1] = dechex(hexdec($xa[2][0]      )^hexdec(        $mul2[hexdec($xa[2][1])]   )^hexdec(    $mul3[hexdec($xa[2][2])]       )^hexdec(        $xa[2][3]));
              $result[2][2] = dechex(hexdec($xa[2][0]      )^hexdec(        $xa[2][1]  )^hexdec(    $mul2[hexdec($xa[2][2])]      )^hexdec(     $mul3[hexdec($xa[2][3])]));
              $result[2][3] = dechex(hexdec($mul3[hexdec($xa[2][0])]       )^hexdec(        $xa[2][1]   )^hexdec(   $xa[2][2]      )^hexdec(        $mul2[hexdec($xa[2][3])]));
              
              $result[3][0] = dechex(hexdec($mul2[hexdec($xa[3][0])]       )^hexdec(        $mul3[hexdec($xa[3][1])]    )^hexdec(   $xa[3][2]       )^hexdec(       $xa[3][3]));
              $result[3][1] = dechex(hexdec($xa[3][0]      )^hexdec(        $mul2[hexdec($xa[3][1])]   )^hexdec(    $mul3[hexdec($xa[3][2])]       )^hexdec(        $xa[3][3]));
              $result[3][2] = dechex(hexdec($xa[3][0]      )^hexdec(        $xa[3][1]  )^hexdec(    $mul2[hexdec($xa[3][2])]      )^hexdec(     $mul3[hexdec($xa[3][3])]));
              $result[3][3] = dechex(hexdec($mul3[hexdec($xa[3][0])]       )^hexdec(        $xa[3][1]   )^hexdec(   $xa[3][2]      )^hexdec(        $mul2[hexdec($xa[3][3])]));

      
      
          return $result;
      }


      function Inverse_MixColumns($xa)
      {   
          //a5
          $mul9 =   array(0x00,0x09,0x12,0x1b,0x24,0x2d,0x36,0x3f,0x48,0x41,0x5a,0x53,0x6c,0x65,0x7e,0x77,
                          0x90,0x99,0x82,0x8b,0xb4,0xbd,0xa6,0xaf,0xd8,0xd1,0xca,0xc3,0xfc,0xf5,0xee,0xe7,
                          0x3b,0x32,0x29,0x20,0x1f,0x16,0x0d,0x04,0x73,0x7a,0x61,0x68,0x57,0x5e,0x45,0x4c,
                          0xab,0xa2,0xb9,0xb0,0x8f,0x86,0x9d,0x94,0xe3,0xea,0xf1,0xf8,0xc7,0xce,0xd5,0xdc,
                          0x76,0x7f,0x64,0x6d,0x52,0x5b,0x40,0x49,0x3e,0x37,0x2c,0x25,0x1a,0x13,0x08,0x01,
                          0xe6,0xef,0xf4,0xfd,0xc2,0xcb,0xd0,0xd9,0xae,0xa7,0xbc,0xb5,0x8a,0x83,0x98,0x91,
                          0x4d,0x44,0x5f,0x56,0x69,0x60,0x7b,0x72,0x05,0x0c,0x17,0x1e,0x21,0x28,0x33,0x3a,
                          0xdd,0xd4,0xcf,0xc6,0xf9,0xf0,0xeb,0xe2,0x95,0x9c,0x87,0x8e,0xb1,0xb8,0xa3,0xaa,	
                          0xec,0xe5,0xfe,0xf7,0xc8,0xc1,0xda,0xd3,0xa4,0xad,0xb6,0xbf,0x80,0x89,0x92,0x9b,	
                          0x7c,0x75,0x6e,0x67,0x58,0x51,0x4a,0x43,0x34,0x3d,0x26,0x2f,0x10,0x19,0x02,0x0b,
                          0xd7,0xde,0xc5,0xcc,0xf3,0xfa,0xe1,0xe8,0x9f,0x96,0x8d,0x84,0xbb,0xb2,0xa9,0xa0,
                          0x47,0x4e,0x55,0x5c,0x63,0x6a,0x71,0x78,0x0f,0x06,0x1d,0x14,0x2b,0x22,0x39,0x30,
                          0x9a,0x93,0x88,0x81,0xbe,0xb7,0xac,0xa5,0xd2,0xdb,0xc0,0xc9,0xf6,0xff,0xe4,0xed,
                          0x0a,0x03,0x18,0x11,0x2e,0x27,0x3c,0x35,0x42,0x4b,0x50,0x59,0x66,0x6f,0x74,0x7d,	
                          0xa1,0xa8,0xb3,0xba,0x85,0x8c,0x97,0x9e,0xe9,0xe0,0xfb,0xf2,0xcd,0xc4,0xdf,0xd6,
                          0x31,0x38,0x23,0x2a,0x15,0x1c,0x07,0x0e,0x79,0x70,0x6b,0x62,0x5d,0x54,0x4f,0x46);


          $mulB   = array(0x00,0x0b,0x16,0x1d,0x2c,0x27,0x3a,0x31,0x58,0x53,0x4e,0x45,0x74,0x7f,0x62,0x69,
                          0xb0,0xbb,0xa6,0xad,0x9c,0x97,0x8a,0x81,0xe8,0xe3,0xfe,0xf5,0xc4,0xcf,0xd2,0xd9,
                          0x7b,0x70,0x6d,0x66,0x57,0x5c,0x41,0x4a,0x23,0x28,0x35,0x3e,0x0f,0x04,0x19,0x12,
                          0xcb,0xc0,0xdd,0xd6,0xe7,0xec,0xf1,0xfa,0x93,0x98,0x85,0x8e,0xbf,0xb4,0xa9,0xa2,
                          0xf6,0xfd,0xe0,0xeb,0xda,0xd1,0xcc,0xc7,0xae,0xa5,0xb8,0xb3,0x82,0x89,0x94,0x9f,
                          0x46,0x4d,0x50,0x5b,0x6a,0x61,0x7c,0x77,0x1e,0x15,0x08,0x03,0x32,0x39,0x24,0x2f,
                          0x8d,0x86,0x9b,0x90,0xa1,0xaa,0xb7,0xbc,0xd5,0xde,0xc3,0xc8,0xf9,0xf2,0xef,0xe4,
                          0x3d,0x36,0x2b,0x20,0x11,0x1a,0x07,0x0c,0x65,0x6e,0x73,0x78,0x49,0x42,0x5f,0x54,
                          0xf7,0xfc,0xe1,0xea,0xdb,0xd0,0xcd,0xc6,0xaf,0xa4,0xb9,0xb2,0x83,0x88,0x95,0x9e,
                          0x47,0x4c,0x51,0x5a,0x6b,0x60,0x7d,0x76,0x1f,0x14,0x09,0x02,0x33,0x38,0x25,0x2e,
                          0x8c,0x87,0x9a,0x91,0xa0,0xab,0xb6,0xbd,0xd4,0xdf,0xc2,0xc9,0xf8,0xf3,0xee,0xe5,
                          0x3c,0x37,0x2a,0x21,0x10,0x1b,0x06,0x0d,0x64,0x6f,0x72,0x79,0x48,0x43,0x5e,0x55,
                          0x01,0x0a,0x17,0x1c,0x2d,0x26,0x3b,0x30,0x59,0x52,0x4f,0x44,0x75,0x7e,0x63,0x68,
                          0xb1,0xba,0xa7,0xac,0x9d,0x96,0x8b,0x80,0xe9,0xe2,0xff,0xf4,0xc5,0xce,0xd3,0xd8,
                          0x7a,0x71,0x6c,0x67,0x56,0x5d,0x40,0x4b,0x22,0x29,0x34,0x3f,0x0e,0x05,0x18,0x13,
                          0xca,0xc1,0xdc,0xd7,0xe6,0xed,0xf0,0xfb,0x92,0x99,0x84,0x8f,0xbe,0xb5,0xa8,0xa3);


          $mulD   = array(0x00,0x0d,0x1a,0x17,0x34,0x39,0x2e,0x23,0x68,0x65,0x72,0x7f,0x5c,0x51,0x46,0x4b,
                          0xd0,0xdd,0xca,0xc7,0xe4,0xe9,0xfe,0xf3,0xb8,0xb5,0xa2,0xaf,0x8c,0x81,0x96,0x9b,
                          0xbb,0xb6,0xa1,0xac,0x8f,0x82,0x95,0x98,0xd3,0xde,0xc9,0xc4,0xe7,0xea,0xfd,0xf0,
                          0x6b,0x66,0x71,0x7c,0x5f,0x52,0x45,0x48,0x03,0x0e,0x19,0x14,0x37,0x3a,0x2d,0x20,
                          0x6d,0x60,0x77,0x7a,0x59,0x54,0x43,0x4e,0x05,0x08,0x1f,0x12,0x31,0x3c,0x2b,0x26,
                          0xbd,0xb0,0xa7,0xaa,0x89,0x84,0x93,0x9e,0xd5,0xd8,0xcf,0xc2,0xe1,0xec,0xfb,0xf6,
                          0xd6,0xdb,0xcc,0xc1,0xe2,0xef,0xf8,0xf5,0xbe,0xb3,0xa4,0xa9,0x8a,0x87,0x90,0x9d,
                          0x06,0x0b,0x1c,0x11,0x32,0x3f,0x28,0x25,0x6e,0x63,0x74,0x79,0x5a,0x57,0x40,0x4d,
                          0xda,0xd7,0xc0,0xcd,0xee,0xe3,0xf4,0xf9,0xb2,0xbf,0xa8,0xa5,0x86,0x8b,0x9c,0x91,
                          0x0a,0x07,0x10,0x1d,0x3e,0x33,0x24,0x29,0x62,0x6f,0x78,0x75,0x56,0x5b,0x4c,0x41,
                          0x61,0x6c,0x7b,0x76,0x55,0x58,0x4f,0x42,0x09,0x04,0x13,0x1e,0x3d,0x30,0x27,0x2a,
                          0xb1,0xbc,0xab,0xa6,0x85,0x88,0x9f,0x92,0xd9,0xd4,0xc3,0xce,0xed,0xe0,0xf7,0xfa,
                          0xb7,0xba,0xad,0xa0,0x83,0x8e,0x99,0x94,0xdf,0xd2,0xc5,0xc8,0xeb,0xe6,0xf1,0xfc,
                          0x67,0x6a,0x7d,0x70,0x53,0x5e,0x49,0x44,0x0f,0x02,0x15,0x18,0x3b,0x36,0x21,0x2c,
                          0x0c,0x01,0x16,0x1b,0x38,0x35,0x22,0x2f,0x64,0x69,0x7e,0x73,0x50,0x5d,0x4a,0x47,
                          0xdc,0xd1,0xc6,0xcb,0xe8,0xe5,0xf2,0xff,0xb4,0xb9,0xae,0xa3,0x80,0x8d,0x9a,0x97);


          $mulE   = array(0x00,0x0e,0x1c,0x12,0x38,0x36,0x24,0x2a,0x70,0x7e,0x6c,0x62,0x48,0x46,0x54,0x5a,
                          0xe0,0xee,0xfc,0xf2,0xd8,0xd6,0xc4,0xca,0x90,0x9e,0x8c,0x82,0xa8,0xa6,0xb4,0xba,
                          0xdb,0xd5,0xc7,0xc9,0xe3,0xed,0xff,0xf1,0xab,0xa5,0xb7,0xb9,0x93,0x9d,0x8f,0x81,
                          0x3b,0x35,0x27,0x29,0x03,0x0d,0x1f,0x11,0x4b,0x45,0x57,0x59,0x73,0x7d,0x6f,0x61,
                          0xad,0xa3,0xb1,0xbf,0x95,0x9b,0x89,0x87,0xdd,0xd3,0xc1,0xcf,0xe5,0xeb,0xf9,0xf7,
                          0x4d,0x43,0x51,0x5f,0x75,0x7b,0x69,0x67,0x3d,0x33,0x21,0x2f,0x05,0x0b,0x19,0x17,
                          0x76,0x78,0x6a,0x64,0x4e,0x40,0x52,0x5c,0x06,0x08,0x1a,0x14,0x3e,0x30,0x22,0x2c,
                          0x96,0x98,0x8a,0x84,0xae,0xa0,0xb2,0xbc,0xe6,0xe8,0xfa,0xf4,0xde,0xd0,0xc2,0xcc,
                          0x41,0x4f,0x5d,0x53,0x79,0x77,0x65,0x6b,0x31,0x3f,0x2d,0x23,0x09,0x07,0x15,0x1b,
                          0xa1,0xaf,0xbd,0xb3,0x99,0x97,0x85,0x8b,0xd1,0xdf,0xcd,0xc3,0xe9,0xe7,0xf5,0xfb,
                          0x9a,0x94,0x86,0x88,0xa2,0xac,0xbe,0xb0,0xea,0xe4,0xf6,0xf8,0xd2,0xdc,0xce,0xc0,
                          0x7a,0x74,0x66,0x68,0x42,0x4c,0x5e,0x50,0x0a,0x04,0x16,0x18,0x32,0x3c,0x2e,0x20,
                          0xec,0xe2,0xf0,0xfe,0xd4,0xda,0xc8,0xc6,0x9c,0x92,0x80,0x8e,0xa4,0xaa,0xb8,0xb6,
                          0x0c,0x02,0x10,0x1e,0x34,0x3a,0x28,0x26,0x7c,0x72,0x60,0x6e,0x44,0x4a,0x58,0x56,
                          0x37,0x39,0x2b,0x25,0x0f,0x01,0x13,0x1d,0x47,0x49,0x5b,0x55,0x7f,0x71,0x63,0x6d,
                          0xd7,0xd9,0xcb,0xc5,0xef,0xe1,0xf3,0xfd,0xa7,0xa9,0xbb,0xb5,0x9f,0x91,0x83,0x8d);

          

          for($i = 0; $i<count($xa) ; $i++)
          {
              for($j = 0; $j<count($xa[$i]) ; $j++)
              {
                  $xa[$i][$j] = str_pad($xa[$i][$j],2,'0',STR_PAD_LEFT);
              }
          }
                  
          for($i = 0 ; $i<count($mulB) ; $i++)
          {
              $mulB[$i] = dechex($mulB[$i]);
          }

          for($i = 0 ; $i<count($mul9) ; $i++)
          {
              $mul9[$i] = dechex($mul9[$i]);
          }
  
          for($i = 0 ; $i<count($mulD) ; $i++)
          {
              $mulD[$i] = dechex($mulD[$i]);
          }
          
          for($i = 0 ; $i<count($mulE) ; $i++)
          {
              $mulE[$i] = dechex($mulE[$i]);
          }

          $row = count($xa);
          $col = count($xa[0]);

          $result = array_fill(0, $row, array_fill(0, $col, 0)); 

          $result[0][0] = dechex(hexdec(  $mulE[hexdec($xa[0][0])]  )  ^   hexdec(  $mulB[hexdec($xa[0][1])]  )   ^   hexdec(    $mulD[hexdec($xa[0][2])]   )   ^   hexdec(    $mul9[hexdec($xa[0][3])] ));
          $result[0][1] = dechex(hexdec(  $mul9[hexdec($xa[0][0])]  )  ^   hexdec(  $mulE[hexdec($xa[0][1])]  )   ^   hexdec(    $mulB[hexdec($xa[0][2])]   )   ^   hexdec(    $mulD[hexdec($xa[0][3])] ));
          $result[0][2] = dechex(hexdec(  $mulD[hexdec($xa[0][0])]  )  ^   hexdec(  $mul9[hexdec($xa[0][1])]  )   ^   hexdec(    $mulE[hexdec($xa[0][2])]   )   ^   hexdec(    $mulB[hexdec($xa[0][3])] ));
          $result[0][3] = dechex(hexdec(  $mulB[hexdec($xa[0][0])]  )  ^   hexdec(  $mulD[hexdec($xa[0][1])]  )   ^   hexdec(    $mul9[hexdec($xa[0][2])]   )   ^   hexdec(    $mulE[hexdec($xa[0][3])] ));
          
          $result[1][0] = dechex(hexdec(  $mulE[hexdec($xa[1][0])]  )  ^   hexdec(  $mulB[hexdec($xa[1][1])]  )   ^   hexdec(    $mulD[hexdec($xa[1][2])]   )   ^   hexdec(    $mul9[hexdec($xa[1][3])] ));
          $result[1][1] = dechex(hexdec(  $mul9[hexdec($xa[1][0])]  )  ^   hexdec(  $mulE[hexdec($xa[1][1])]  )   ^   hexdec(    $mulB[hexdec($xa[1][2])]   )   ^   hexdec(    $mulD[hexdec($xa[1][3])] ));
          $result[1][2] = dechex(hexdec(  $mulD[hexdec($xa[1][0])]  )  ^   hexdec(  $mul9[hexdec($xa[1][1])]  )   ^   hexdec(    $mulE[hexdec($xa[1][2])]   )   ^   hexdec(    $mulB[hexdec($xa[1][3])] ));
          $result[1][3] = dechex(hexdec(  $mulB[hexdec($xa[1][0])]  )  ^   hexdec(  $mulD[hexdec($xa[1][1])]  )   ^   hexdec(    $mul9[hexdec($xa[1][2])]   )   ^   hexdec(    $mulE[hexdec($xa[1][3])] ));

          $result[2][0] = dechex(hexdec(  $mulE[hexdec($xa[2][0])]  )  ^   hexdec(  $mulB[hexdec($xa[2][1])]  )   ^   hexdec(    $mulD[hexdec($xa[2][2])]   )   ^   hexdec(    $mul9[hexdec($xa[2][3])] ));
          $result[2][1] = dechex(hexdec(  $mul9[hexdec($xa[2][0])]  )  ^   hexdec(  $mulE[hexdec($xa[2][1])]  )   ^   hexdec(    $mulB[hexdec($xa[2][2])]   )   ^   hexdec(    $mulD[hexdec($xa[2][3])] ));
          $result[2][2] = dechex(hexdec(  $mulD[hexdec($xa[2][0])]  )  ^   hexdec(  $mul9[hexdec($xa[2][1])]  )   ^   hexdec(    $mulE[hexdec($xa[2][2])]   )   ^   hexdec(    $mulB[hexdec($xa[2][3])] ));
          $result[2][3] = dechex(hexdec(  $mulB[hexdec($xa[2][0])]  )  ^   hexdec(  $mulD[hexdec($xa[2][1])]  )   ^   hexdec(    $mul9[hexdec($xa[2][2])]   )   ^   hexdec(    $mulE[hexdec($xa[2][3])] ));

          $result[3][0] = dechex(hexdec(  $mulE[hexdec($xa[3][0])]  )  ^   hexdec(  $mulB[hexdec($xa[3][1])]  )   ^   hexdec(    $mulD[hexdec($xa[3][2])]   )   ^   hexdec(    $mul9[hexdec($xa[3][3])] ));
          $result[3][1] = dechex(hexdec(  $mul9[hexdec($xa[3][0])]  )  ^   hexdec(  $mulE[hexdec($xa[3][1])]  )   ^   hexdec(    $mulB[hexdec($xa[3][2])]   )   ^   hexdec(    $mulD[hexdec($xa[3][3])] ));
          $result[3][2] = dechex(hexdec(  $mulD[hexdec($xa[3][0])]  )  ^   hexdec(  $mul9[hexdec($xa[3][1])]  )   ^   hexdec(    $mulE[hexdec($xa[3][2])]   )   ^   hexdec(    $mulB[hexdec($xa[3][3])] ));
          $result[3][3] = dechex(hexdec(  $mulB[hexdec($xa[3][0])]  )  ^   hexdec(  $mulD[hexdec($xa[3][1])]  )   ^   hexdec(    $mul9[hexdec($xa[3][2])]   )   ^   hexdec(    $mulE[hexdec($xa[3][3])] ));

          return $result;
      }


      
      function Inverse_SBOX_mat($arr)
      {
          $sbox =   array(array(0x52 ,    0x09 ,	0x6a ,	0xd5 ,	0x30 ,	0x36 ,	0xa5 ,	0x38 ,	0xbf ,	0x40 ,	0xa3 ,	0x9e ,	0x81 ,	0xf3 ,	0xd7 ,	0xfb),
                          array(0x7c ,	0xe3 ,	0x39 ,	0x82 ,	0x9b ,	0x2f ,	0xff ,	0x87 ,	0x34 ,	0x8e ,	0x43 ,	0x44 ,	0xc4 ,	0xde ,	0xe9 ,	0xcb),
                          array(0x54 ,	0x7b ,	0x94 ,	0x32 ,	0xa6 ,	0xc2 ,	0x23 ,	0x3d ,	0xee ,	0x4c ,	0x95 ,	0x0b ,	0x42 ,	0xfa ,	0xc3 ,	0x4e),
                          array(0x08 ,	0x2e ,	0xa1 ,	0x66 ,	0x28 ,	0xd9 ,	0x24 ,	0xb2 ,	0x76 ,	0x5b ,	0xa2 ,	0x49 ,	0x6d ,	0x8b ,	0xd1 ,	0x25),
                          array(0x72 ,	0xf8 ,	0xf6 ,	0x64 ,	0x86 ,	0x68 ,	0x98 ,	0x16 ,	0xd4 ,	0xa4 ,	0x5c ,	0xcc ,	0x5d ,	0x65 ,	0xb6 ,	0x92),
                          array(0x6c ,	0x70 ,	0x48 ,	0x50 ,	0xfd ,	0xed ,	0xb9 ,	0xda ,	0x5e ,	0x15 ,	0x46 ,	0x57 ,	0xa7 ,	0x8d ,	0x9d ,	0x84),
                          array(0x90 ,	0xd8 ,	0xab ,	0x00 ,	0x8c ,	0xbc ,	0xd3 ,	0x0a ,	0xf7 ,	0xe4 ,	0x58 ,	0x05 ,	0xb8 ,	0xb3 ,	0x45 ,	0x06),
                          array(0xd0 ,	0x2c ,	0x1e ,	0x8f ,	0xca ,	0x3f ,	0x0f ,	0x02 ,	0xc1 ,	0xaf ,	0xbd ,	0x03 ,	0x01 ,	0x13 ,	0x8a ,	0x6b),
                          array(0x3a ,	0x91 ,	0x11 ,	0x41 ,	0x4f ,	0x67 ,	0xdc ,	0xea ,	0x97 ,	0xf2 ,	0xcf ,	0xce ,	0xf0 ,	0xb4 ,	0xe6 ,	0x73),
                          array(0x96 ,	0xac ,	0x74 ,	0x22 ,	0xe7 ,	0xad ,	0x35 ,	0x85 ,	0xe2 ,	0xf9 ,	0x37 ,	0xe8 ,	0x1c ,	0x75 ,	0xdf ,	0x6e),
                          array(0x47 ,	0xf1 ,	0x1a ,	0x71 ,	0x1d ,	0x29 ,	0xc5 ,	0x89 ,	0x6f ,	0xb7 ,	0x62 ,	0x0e ,	0xaa ,	0x18 ,	0xbe ,	0x1b),
                          array(0xfc ,	0x56 ,	0x3e ,	0x4b ,	0xc6 ,	0xd2 ,	0x79 ,	0x20 ,	0x9a ,	0xdb ,	0xc0 ,	0xfe ,	0x78 ,	0xcd ,	0x5a ,	0xf4),
                          array(0x1f ,	0xdd ,	0xa8 ,	0x33 ,	0x88 ,	0x07 ,	0xc7 ,	0x31 ,	0xb1 ,	0x12 ,	0x10 ,	0x59 ,	0x27 ,	0x80 ,	0xec ,	0x5f),
                          array(0x60 ,	0x51 ,	0x7f ,	0xa9 ,	0x19 ,	0xb5 ,	0x4a ,	0x0d ,	0x2d ,	0xe5 ,	0x7a ,	0x9f ,	0x93 ,	0xc9 ,	0x9c ,	0xef),
                          array(0xa0 ,	0xe0 ,	0x3b ,	0x4d ,	0xae ,	0x2a ,	0xf5 ,	0xb0 ,	0xc8 ,	0xeb ,	0xbb ,	0x3c ,	0x83 ,	0x53 ,	0x99 ,	0x61),
                          array(0x17, 	0x2b ,	0x04 ,	0x7e ,	0xba ,	0x77, 	0xd6, 	0x26 ,	0xe1 ,	0x69 ,	0x14 ,	0x63 ,	0x55 ,	0x21 ,	0x0c ,	0x7d) );
              //hard-coded S-box
          
          // for($i = 0 ; $i<count($arr) ; $i++)
          // {
          //     for($j = 0 ; $j<count($arr[$i]) ; $j++)
          //     {
          //         $arr[$i][$j] = dechex($arr[$i][$j]);
          //     }
          // }

          for($i = 0; $i<count($arr) ; $i++)
          {
              for($j = 0; $j<count($arr[$i]) ; $j++)
              {
                  $arr[$i][$j] = str_pad($arr[$i][$j],2,'0',STR_PAD_LEFT);
              }
          }
          
          $row = count($arr);
          $col = count($arr[0]);
          
          $result = array_fill(0, $row, array_fill(0, $col, 0)); 

          for($i = 0; $i< $row ; $i++)
          {
              for($j=0 ; $j < $col ; $j++)
              {
                  $compare = $arr[$i][$j];
                  //echo "<br> mat[$i][$j] = ".$compare;
                  
                  $result[$i][$j] = dechex( $sbox[hexdec($compare[0])][hexdec($compare[1])] );
              }
          }

          // for($i = 0 ; $i<count($result) ; $i++)
          // {
          //     echo "<br>S-BOX[$i] = ".(implode(" ",$result[$i]));
          // }


          
          return $result;
      }


      function SBOX_mat($arr)
      {
          $sbox =   array(array(0x63 ,0x7c ,0x77 ,0x7b ,0xf2 ,0x6b ,0x6f ,0xc5 ,0x30 ,0x01 ,0x67 ,0x2b ,0xfe ,0xd7 ,0xab ,0x76),
                          array(0xca ,0x82 ,0xc9 ,0x7d ,0xfa ,0x59 ,0x47 ,0xf0 ,0xad ,0xd4 ,0xa2 ,0xaf ,0x9c ,0xa4 ,0x72 ,0xc0),
                          array(0xb7 ,0xfd ,0x93 ,0x26 ,0x36 ,0x3f ,0xf7 ,0xcc ,0x34 ,0xa5 ,0xe5 ,0xf1 ,0x71 ,0xd8 ,0x31 ,0x15),
                          array(0x04 ,0xc7 ,0x23 ,0xc3 ,0x18 ,0x96 ,0x05 ,0x9a ,0x07 ,0x12 ,0x80 ,0xe2 ,0xeb ,0x27 ,0xb2 ,0x75),
                          array(0x09 ,0x83 ,0x2c ,0x1a ,0x1b ,0x6e ,0x5a ,0xa0 ,0x52 ,0x3b ,0xd6 ,0xb3 ,0x29 ,0xe3 ,0x2f ,0x84),
                          array(0x53 ,0xd1 ,0x00 ,0xed ,0x20 ,0xfc ,0xb1 ,0x5b ,0x6a ,0xcb ,0xbe ,0x39 ,0x4a ,0x4c ,0x58 ,0xcf),
                          array(0xd0 ,0xef ,0xaa ,0xfb ,0x43 ,0x4d ,0x33 ,0x85 ,0x45 ,0xf9 ,0x02 ,0x7f ,0x50 ,0x3c ,0x9f ,0xa8),
                          array(0x51 ,0xa3 ,0x40 ,0x8f ,0x92 ,0x9d ,0x38 ,0xf5 ,0xbc ,0xb6 ,0xda ,0x21 ,0x10 ,0xff ,0xf3 ,0xd2),
                          array(0xcd ,0x0c ,0x13 ,0xec ,0x5f ,0x97 ,0x44 ,0x17 ,0xc4 ,0xa7 ,0x7e ,0x3d ,0x64 ,0x5d ,0x19 ,0x73),
                          array(0x60 ,0x81 ,0x4f ,0xdc ,0x22 ,0x2a ,0x90 ,0x88 ,0x46 ,0xee ,0xb8 ,0x14 ,0xde ,0x5e ,0x0b ,0xdb),
                          array(0xe0 ,0x32 ,0x3a ,0x0a ,0x49 ,0x06 ,0x24 ,0x5c ,0xc2 ,0xd3 ,0xac ,0x62 ,0x91 ,0x95 ,0xe4 ,0x79),
                          array(0xe7 ,0xc8 ,0x37 ,0x6d ,0x8d ,0xd5 ,0x4e ,0xa9 ,0x6c ,0x56 ,0xf4 ,0xea ,0x65 ,0x7a ,0xae ,0x08),
                          array(0xba ,0x78 ,0x25 ,0x2e ,0x1c ,0xa6 ,0xb4 ,0xc6 ,0xe8 ,0xdd ,0x74 ,0x1f ,0x4b ,0xbd ,0x8b ,0x8a),
                          array(0x70 ,0x3e ,0xb5 ,0x66 ,0x48 ,0x03 ,0xf6 ,0x0e ,0x61 ,0x35 ,0x57 ,0xb9 ,0x86 ,0xc1 ,0x1d ,0x9e),
                          array(0xe1 ,0xf8 ,0x98 ,0x11 ,0x69 ,0xd9 ,0x8e ,0x94 ,0x9b ,0x1e ,0x87 ,0xe9 ,0xce ,0x55 ,0x28 ,0xdf),
                          array(0x8c ,0xa1 ,0x89 ,0x0d ,0xbf ,0xe6 ,0x42 ,0x68 ,0x41 ,0x99 ,0x2d ,0x0f ,0xb0 ,0x54 ,0xbb ,0x16) );
              //hard-coded S-box
          
          // for($i = 0 ; $i<count($sbox) ; $i++)
          // {
          //     for($j = 0 ; $j<count($sbox[$i]) ; $j++)
          //     {
          //         $sbox[$i][$j] = dechex($sbox[$i][$j]);
          //     }
          // }

          for($i = 0; $i<count($arr) ; $i++)
          {
              for($j = 0; $j<count($arr[$i]) ; $j++)
              {
                  $arr[$i][$j] = str_pad($arr[$i][$j],2,'0',STR_PAD_LEFT);
              }
          }
          
          $row = count($arr);
          $col = count($arr[0]);
          
          $result = array_fill(0, $row, array_fill(0, $col, 0)); 

          for($i = 0; $i< $row ; $i++)
          {
              for($j=0 ; $j < $col ; $j++)
              {
                  $compare = $arr[$i][$j];
                  //echo "<br> mat[$i][$j] = ".$compare;
                  
                  $result[$i][$j] = dechex( $sbox[hexdec($compare[0])][hexdec($compare[1])] );
              }
          }

          // for($i = 0 ; $i<count($result) ; $i++)
          // {
          //     echo "<br>S-BOX[$i] = ".(implode(" ",$result[$i]));
          // }


          
          return $result;
      }


      function SBOX_8bit($arr)
      {
          $sbox =   array(array(0x63 ,0x7c ,0x77 ,0x7b ,0xf2 ,0x6b ,0x6f ,0xc5 ,0x30 ,0x01 ,0x67 ,0x2b ,0xfe ,0xd7 ,0xab ,0x76),
                          array(0xca ,0x82 ,0xc9 ,0x7d ,0xfa ,0x59 ,0x47 ,0xf0 ,0xad ,0xd4 ,0xa2 ,0xaf ,0x9c ,0xa4 ,0x72 ,0xc0),
                          array(0xb7 ,0xfd ,0x93 ,0x26 ,0x36 ,0x3f ,0xf7 ,0xcc ,0x34 ,0xa5 ,0xe5 ,0xf1 ,0x71 ,0xd8 ,0x31 ,0x15),
                          array(0x04 ,0xc7 ,0x23 ,0xc3 ,0x18 ,0x96 ,0x05 ,0x9a ,0x07 ,0x12 ,0x80 ,0xe2 ,0xeb ,0x27 ,0xb2 ,0x75),
                          array(0x09 ,0x83 ,0x2c ,0x1a ,0x1b ,0x6e ,0x5a ,0xa0 ,0x52 ,0x3b ,0xd6 ,0xb3 ,0x29 ,0xe3 ,0x2f ,0x84),
                          array(0x53 ,0xd1 ,0x00 ,0xed ,0x20 ,0xfc ,0xb1 ,0x5b ,0x6a ,0xcb ,0xbe ,0x39 ,0x4a ,0x4c ,0x58 ,0xcf),
                          array(0xd0 ,0xef ,0xaa ,0xfb ,0x43 ,0x4d ,0x33 ,0x85 ,0x45 ,0xf9 ,0x02 ,0x7f ,0x50 ,0x3c ,0x9f ,0xa8),
                          array(0x51 ,0xa3 ,0x40 ,0x8f ,0x92 ,0x9d ,0x38 ,0xf5 ,0xbc ,0xb6 ,0xda ,0x21 ,0x10 ,0xff ,0xf3 ,0xd2),
                          array(0xcd ,0x0c ,0x13 ,0xec ,0x5f ,0x97 ,0x44 ,0x17 ,0xc4 ,0xa7 ,0x7e ,0x3d ,0x64 ,0x5d ,0x19 ,0x73),
                          array(0x60 ,0x81 ,0x4f ,0xdc ,0x22 ,0x2a ,0x90 ,0x88 ,0x46 ,0xee ,0xb8 ,0x14 ,0xde ,0x5e ,0x0b ,0xdb),
                          array(0xe0 ,0x32 ,0x3a ,0x0a ,0x49 ,0x06 ,0x24 ,0x5c ,0xc2 ,0xd3 ,0xac ,0x62 ,0x91 ,0x95 ,0xe4 ,0x79),
                          array(0xe7 ,0xc8 ,0x37 ,0x6d ,0x8d ,0xd5 ,0x4e ,0xa9 ,0x6c ,0x56 ,0xf4 ,0xea ,0x65 ,0x7a ,0xae ,0x08),
                          array(0xba ,0x78 ,0x25 ,0x2e ,0x1c ,0xa6 ,0xb4 ,0xc6 ,0xe8 ,0xdd ,0x74 ,0x1f ,0x4b ,0xbd ,0x8b ,0x8a),
                          array(0x70 ,0x3e ,0xb5 ,0x66 ,0x48 ,0x03 ,0xf6 ,0x0e ,0x61 ,0x35 ,0x57 ,0xb9 ,0x86 ,0xc1 ,0x1d ,0x9e),
                          array(0xe1 ,0xf8 ,0x98 ,0x11 ,0x69 ,0xd9 ,0x8e ,0x94 ,0x9b ,0x1e ,0x87 ,0xe9 ,0xce ,0x55 ,0x28 ,0xdf),
                          array(0x8c ,0xa1 ,0x89 ,0x0d ,0xbf ,0xe6 ,0x42 ,0x68 ,0x41 ,0x99 ,0x2d ,0x0f ,0xb0 ,0x54 ,0xbb ,0x16) );
              //hard-coded S-box

              // 14 2e 4b 43
          
          // for($i = 0 ; $i<count($sbox) ; $i++)
          // {
          //     for($j = 0 ; $j<count($sbox[$i]) ; $j++)
          //     {
          //         $sbox[$i][$j] = dechex($sbox[$i][$j]);
          //     }
          // }

          $result = array();

          for ($i=0 ; $i < count($arr) ; $i++)
          {   
              //echo "<br>". hexdec($arr[$i][0]).",   ".hexdec($arr[$i][1]);
              //echo ",  s-box= ". dechex($sbox[hexdec($arr[$i][0])][hexdec($arr[$i][1])]);
              array_push( $result, dechex($sbox[hexdec($arr[$i][0])][hexdec($arr[$i][1])]) );
          }
          return $result;
      }


      function shift_row($matrix)
      {
          $tmpmat= $matrix;

          $tmpmat[0][1] = $matrix[1][1];
          $tmpmat[1][1] = $matrix[2][1];
          $tmpmat[2][1] = $matrix[3][1];
          $tmpmat[3][1] = $matrix[0][1];


          $tmpmat[0][2] = $matrix[2][2];
          $tmpmat[1][2] = $matrix[3][2];
          $tmpmat[2][2] = $matrix[0][2];
          $tmpmat[3][2] = $matrix[1][2];


          $tmpmat[0][3] = $matrix[3][3];
          $tmpmat[1][3] = $matrix[0][3];
          $tmpmat[2][3] = $matrix[1][3];
          $tmpmat[3][3] = $matrix[2][3];
          

          return $tmpmat;
      }

      function inv_shift_row($matrix)
      {
          $tmpmat= $matrix;

          $tmpmat[0][1] = $matrix[3][1];
          $tmpmat[1][1] = $matrix[0][1];
          $tmpmat[2][1] = $matrix[1][1];
          $tmpmat[3][1] = $matrix[2][1];


          $tmpmat[0][2] = $matrix[2][2];
          $tmpmat[1][2] = $matrix[3][2];
          $tmpmat[2][2] = $matrix[0][2];
          $tmpmat[3][2] = $matrix[1][2];


          $tmpmat[0][3] = $matrix[1][3];
          $tmpmat[1][3] = $matrix[2][3];
          $tmpmat[2][3] = $matrix[3][3];
          $tmpmat[3][3] = $matrix[0][3];

          return $tmpmat;
      }


      function XOR_arr($a,$b)
      {   
          $wtemp=array();
          for($i = 0; $i<count($a) ; $i++)
          {
              //echo "<br>>>>>>>>>>>>>>>>>>>>>>>>>>XOR $a[$i] and $b[$i]";
              $b[$i] = str_pad($b[$i],2,'0',STR_PAD_LEFT);
              $a[$i] = str_pad($a[$i],2,'0',STR_PAD_LEFT);
          }
          for($i = 0; $i<count($a) ; $i++)
          {
              array_push( $wtemp, dechex(hexdec($a[$i]) ^ hexdec($b[$i])));
          }
          return $wtemp;
      }

      function XOR_mat($a,$b)
      {   
          for($i = 0; $i<count($a) ; $i++)
          {
              for($j = 0; $j<count($a[$i]) ; $j++)
              {
                  
                  $b[$i][$j] = str_pad($b[$i][$j],2,'0',STR_PAD_LEFT);
                  $a[$i][$j] = str_pad($a[$i][$j],2,'0',STR_PAD_LEFT);
                  //echo "<br>>>>>>>>>>>>>>>>>>>>>>>>>>XOR ". $a[$i][$j] ." and ". $b[$i][$j];
              }
          }


          $row = count($a);
          $col = count($a[0]);
      
          $wtemp = array_fill(0, $row, array_fill(0, $col, 0)); 
          $ct = 0;
          for($i = 0; $i<count($a) ; $i++)
          {
              for($c = 0; $c<count($a[$i]) ; $c++)
              {

                  $res = (hexdec($a[$i][$c]) ^ hexdec($b[$i][$c]));
                  // echo "<br>values: a[$i][$c] , b[$i][$c] = " . $a[$i][$c] . " ^ ". $b[$i][$c] . "= " . $res;
                  $wtemp[$i][$c] = dechex($res);
                  
              }
          }


          for($i = 0; $i<count($wtemp) ; $i++)
          {
              for($j = 0; $j<count($wtemp[$i]) ; $j++)
              {
                  $wtemp[$i][$j] = str_pad($wtemp[$i][$j],2,'0',STR_PAD_LEFT);
                  //echo "<br>>>>>>>>>>>>>>>>>>>>>>>>>>XOR ". $a[$i][$j] ." and ". $b[$i][$j];
              }
          }


          return $wtemp;
      }

      function Adding($a,$b)
      {    
          //echo "<br>count: ". count($b).", ".$b[0];

          for($j = 0; $j<count($b) ; $j++)
          {
              $b[$j] = str_pad($b[$j],2,'0',STR_PAD_LEFT);
          }

          // echo "<br>----------= " . implode(" ",$b);
          
          $res = $a;
          
          // echo "<br>bant= " . implode(" ",$roundb);

          for($i = 0; $i<count($a) ; $i++)
          {
              $s =($b[$i]);
              $res[$i] = dechex(hexdec($a[$i]) ^ hexdec($s));
              // echo "<br>$a[$i] ^ $s = $res[$i]";
          }

          return $res;
      }

      function generate_round($w,int $round_count)
      {

          $M = array(array(0x01,0x00,0x00,0x00),
                    array(0x02,0x00,0x00,0x00),
                    array(0x04,0x00,0x00,0x00),
                    array(0x08,0x00,0x00,0x00),
                    array(0x10,0x00,0x00,0x00),
                    array(0x20,0x00,0x00,0x00),
                    array(0x40,0x00,0x00,0x00),
                    array(0x80,0x00,0x00,0x00),
                    array(0x1b,0x00,0x00,0x00),
                    array(0x36,0x00,0x00,0x00));

          // for($i = 0 ; $i<count($w) ; $i++)
          // {
          //     echo "<br>key[$i]= " . implode(" ",$w[$i]);
          // }

          for($i = 0 ; $i<count($M) ; $i++)
          {
              for($j = 0 ; $j<count($M[$i]) ; $j++)
              {
                  $M[$i][$j] = dechex($M[$i][$j]);
              }
          }

          $tmp = $w[3];

          array_push($w[3], array_shift($w[3]));

          //echo "<br>Left shift of w[3]= " . implode(" ",$w[3]);
          // echo "<br>";
          

          // for($i = 0 ; $i<count($w) ; $i++)
          // {
          //     echo "<br>shift[$i]= " . implode(" ",$w[$i]);
          // }

          $w[3] = $this->SBOX_8bit($w[3]);
          // echo "<br>";

          for($i = 0; $i<count($w) ; $i++)
          {
              for($j = 0; $j<count($w[$i]) ; $j++)
              {
                  $w[$i][$j] = str_pad($w[$i][$j],2,'0',STR_PAD_LEFT);
              }
          }
          // for($i = 0 ; $i<count($w) ; $i++)
          // {
          //     echo "<br>sbox[$i]= " . implode(" ",$w[$i]);
          // }

          
          //echo "<br>RCON---------------".$M[$round_count][0]." -> ".dechex($M[$round_count][0]);

          $w[3] = $this->Adding($w[3], $M[$round_count]);
          //echo "<br>";

          // for($i = 0; $i<count($w) ; $i++)
          // {
          //     for($j = 0; $j<count($w[$i]) ; $j++)
          //     {
          //         $w[$i][$j] = str_pad($w[$i][$j],2,'0',STR_PAD_LEFT);
          //     }
          // }
          
          // for($i = 0 ; $i<count($w) ; $i++)
          // {
          //     echo "<br>XOR[$i]= " . implode(" ",$w[$i]);
          // }

        // echo "<br>subtracting round constant= " . implode(" ",$w[3]);

        for($i = 0; $i<count($w) ; $i++)
        {
            for($j = 0; $j<count($w[$i]) ; $j++)
            {
                $w[$i][$j] = str_pad($w[$i][$j],2,'0',STR_PAD_LEFT);
            }
        }
          array_push($w, $this->XOR_arr($w[3],$w[0])); //w4
          array_push($w, $this->XOR_arr($w[4],$w[1])); //w5
          array_push($w, $this->XOR_arr($w[5],$w[2])); //w6
          array_push($w, $this->XOR_arr($w[6],$tmp));  //w7

          // echo "<br>";
          // echo "<br>";
          // for($i = 0 ; $i<4 ; $i++)
          // {
          //     echo "<br>gen[$i]= " . implode(" ",$w[$i]);
          // }
          // echo "<br>";
          // for($i = 4 ; $i<8 ; $i++)
          // {
          //     echo "<br>gen[$i]= " . implode(" ",$w[$i]);
          // }
  

          for($i = 0; $i<count($w) ; $i++)
          {
              for($j = 0; $j<count($w[$i]) ; $j++)
              {
                  $w[$i][$j] = str_pad($w[$i][$j],2,'0',STR_PAD_LEFT);
              }
          }

          // for($i = 0 ; $i<count($w) ; $i++)
          // {
          //     echo "<br>aw[$i]= " . implode(" ",$w[$i]);
          // } 
          $roundkey = "";
          for($i = 4 ; $i<count($w) ; $i++)
          {
              $roundkey = $roundkey . implode("",$w[$i]);
          }

          //$roundkey = str_pad($roundkey,128,'0',STR_PAD_LEFT);
          return $roundkey;
      }

      function str2Hexmat($string)
      {
          // echo "<br>string ++++++++++++++++++++++++++++ $string";

          $mat = str_split( $string , 8 );

          for($i = 0; $i<count($mat) ; $i++)
          {
              $mat[$i] = str_split($mat[$i],2);
          }

          return $mat;
      }

      function mat2str($a)
      {
          $res = '';
          for($i = 0 ; $i<count($a) ;$i++)
          {
              $res =$res . implode("",$a[$i]);
          }
          return $res;
      }

      function AES_ENCTYPT(string $text,string $key)
      {
          $text = str_pad($text,16,'#',STR_PAD_LEFT);
          $key = str_pad($key,16,'#',STR_PAD_LEFT);

          $Rkeys = array();

          $text = bin2hex($text);
      
          // echo "PlainText >>>>> ".$text; Important
          $key = bin2hex($key);

          // echo "<br>Key >>>>> ". $key; Important


          

          array_push($Rkeys, $key);
          // echo "<br>--roundkey[0]: ". $Rkeys[0];  Important
          //echo "<br>+++++++cipher[0]: ". $text;
          $textmat = $this->str2Hexmat($text);

          $original_keymat = $this->str2Hexmat($key);




          
          $str = $this->generate_round($original_keymat,0);

          $keytmp = $this->str2Hexmat($str);

          // echo "<br>--roundkey[1]: ". $str;   Important




          array_push($Rkeys, $str);
          
          $state_matrix = $this->XOR_mat($textmat,$original_keymat);
          $ci = $this->mat2str($state_matrix);

          // for($i = 0 ; $i<count($w) ; $i++)
          // {
          //     echo "<br>aw[$i]= " . implode(" ",$w[$i]);
          // } 

          //echo "<br>+++++++cipher[1]: ". $ci;
          // for($i = 0 ; $i<count($state_matrix) ; $i++)
          // {
          //     echo "<br>aw[$i]= " . implode(" ",$state_matrix[$i]);
          // } 

          for($x = 1; $x<10 ; $x++)
          {
              //padding (for safety)
                  for($i = 0; $i<count($state_matrix) ; $i++)
                  {
                      for($j = 0; $j<count($state_matrix[$i]) ; $j++)
                      {
                          $state_matrix[$i][$j] = str_pad($state_matrix[$i][$j],2,'0',STR_PAD_LEFT);
                      }
                  }


            //sbox:
                  $s = $this->SBOX_mat($state_matrix);
                  $state_matrix = $s;


              //shiftrows:
                  $shiftrow = $this->shift_row($s);
                  $state_matrix = $shiftrow;

              //mixcolumns:
                  $mixcol = $this->MixColumns($state_matrix);
                  $state_matrix = $mixcol;


              //XOR roundkey:
                  
                  $state_matrix = $this->XOR_mat($state_matrix,$keytmp);
                  
                  
                  $item = $this->str2Hexmat($Rkeys[$x]);
                  $rounded = $this->generate_round($item, $x);
                  $altkey = $this->str2Hexmat($rounded);
                  array_push($Rkeys, $rounded);
                  $keytmp = $altkey;
                  
                  // echo "<br>--roundkey[".($x+1)."]: ". $rounded;  Important
                  $ci = $this->mat2str($state_matrix);
                  //echo "<br>+++++++cipher[".($x+1)."]: ". $ci;
                  
                  //break;
          }
        
          //padding (for safety)
          for($i = 0; $i<count($state_matrix) ; $i++)
          {
              for($j = 0; $j<count($state_matrix[$i]) ; $j++)
              {
                  $state_matrix[$i][$j] = str_pad($state_matrix[$i][$j],2,'0',STR_PAD_LEFT);
              }
          }

        //sbox:
              $s = $this->SBOX_mat($state_matrix);

              $state_matrix = $s;

          //shiftrows:
              $shiftrow = $this->shift_row($s);
              $state_matrix = $shiftrow;



          //XOR roundkey:
              
              $state_matrix = $this->XOR_mat($state_matrix,$keytmp);
              $final = $this->mat2str($state_matrix);
              $keytmp = $this->mat2str($keytmp);
              
          //echo "<br>------------------- HEXcipher: ". $final;

          return $final; 
      }


      function AES_DECRYPT(string $cipher,string $key)
      {
          $key = str_pad($key,16,'#',STR_PAD_LEFT);

          $Rkeys = array();

          $text = bin2hex($cipher);
      
          // echo "PlainText >>>>> ".$text;  Important
          $key = bin2hex($key);

          // echo "<br>Key >>>>> ". $key;  Important

          array_push($Rkeys, $key);

          $textmat = $this->str2Hexmat($text);
          $original_keymat = $this->str2Hexmat($key);

          $str = $this->generate_round($original_keymat,0);
          $keytmp = $this->str2Hexmat($str);
          array_push($Rkeys, $str);
          
          

          for($i= 1; $i<10 ; $i++)
          {
              $item = $this->str2Hexmat($Rkeys[$i]);
              $rounded = $this->generate_round($item, $i);
              array_push($Rkeys, $rounded);

          }

          $item = $this->str2Hexmat($Rkeys[10]);
          
          $state_matrix = $this->XOR_mat($textmat,$item);

          //shiftrows:
              $shiftrow = $this->inv_shift_row($state_matrix);
              $state_matrix = $shiftrow;
              
          //sbox:
              $s = $this->Inverse_SBOX_mat($state_matrix);
              $state_matrix = $s;
          

          for($i = 0; $i<count($state_matrix) ; $i++)
          {
              for($j = 0; $j<count($state_matrix[$i]) ; $j++)
              {
                  $state_matrix[$i][$j] = str_pad($state_matrix[$i][$j],2,'0',STR_PAD_LEFT);
              }
          }

          $ci = $this->mat2str($state_matrix);
          //echo "<br>+++++++cipher[10]: ". $ci;

          $ct = 0;
          for($x = 9; $x>=1 ; $x--)
          {   
              // echo "<br>--roundkey[".($x)."]: ". $Rkeys[$x];  Important
              

              $item = $this->str2Hexmat($Rkeys[$x]);

              // for($i = 0 ; $i<count($item) ; $i++)
              // {
              //     echo "<br>aw[$i]= " . implode(" ",$item[$i]);
              // } 

              // for($i = 0 ; $i<count($state_matrix) ; $i++)
              // {
              //     echo "<br>s[$i]= " . implode(" ",$state_matrix[$i]);
              // } 

              $state_matrix = $this->XOR_mat($state_matrix,$item);

            
              //mixcolumns:
              $mixcol = $this->Inverse_MixColumns($state_matrix);
              $state_matrix = $mixcol;

              //shiftrows:
                  $shiftrow = $this->inv_shift_row($state_matrix);
                  $state_matrix = $shiftrow;
                  
              //sbox:
                  $s = $this->Inverse_SBOX_mat($state_matrix);
                  $state_matrix = $s;

                  for($i = 0; $i<count($state_matrix) ; $i++)
                  {
                      for($j = 0; $j<count($state_matrix[$i]) ; $j++)
                      {
                          $state_matrix[$i][$j] = str_pad($state_matrix[$i][$j],2,'0',STR_PAD_LEFT);
                      }
                  }

              $ci = $this->mat2str($state_matrix);
              //echo "<br>+++++++cipher[".($x)."]: ". $ci;    

              //padding (for safety)
              
              // break;
                //echo "<br>-----------------roundkey[".($x+1)."]: ". $rounded;
                  
          }
          // echo "<br>--roundkey[".($x)."]: ". $Rkeys[$x];  Important
        
              //padding (for safety)
              for($i = 0; $i<count($state_matrix) ; $i++)
              {
                  for($j = 0; $j<count($state_matrix[$i]) ; $j++)
                  {
                      $state_matrix[$i][$j] = str_pad($state_matrix[$i][$j],2,'0',STR_PAD_LEFT);
                  }
              }

              $item = $this->str2Hexmat($Rkeys[$x]);
              $state_matrix = $this->XOR_mat($state_matrix,$item);
              $final = $this->mat2str($state_matrix);


              // for($i = 0 ; $i<count($state_matrix) ; $i++)
              // {
              //     echo "<br>s[$i]= " . implode(" ",$state_matrix[$i]);
              // } 


              $ci = $this->mat2str($state_matrix);
              //echo "<br>+++++++cipher[".($x)."]: ". $ci;    

          return $final; 
      }

      function testmix()
      {
          $state = array(array(0x87, 0xf2, 0x4d, 0x97),
                          array(0x6e, 0x4c, 0x90, 0xec),
                          array(0x46, 0xe7, 0x4a, 0xc3),
                          array(0xa6, 0x8c, 0xd8, 0x95));


          for($i = 0 ; $i<count($state) ; $i++)
          {
              for($j = 0 ; $j<count($state[$i]) ; $j++)
              {
                  $state[$i][$j] = dechex($state[$i][$j]);
              }
          }

          for($i = 0 ; $i<count($state) ; $i++)
          {
              echo "<br>state[$i]= " . implode(" ",$state[$i]);
          }
          echo "<br>";
          $mix = $this->SBOX_mat($state);

          for($i = 0 ; $i<count($mix) ; $i++)
          {
              echo "<br>sbox[$i]= " . implode(" ",$mix[$i]);
          }
          echo "<br>";

          $mix = $this->Inverse_SBOX_mat($mix);

          for($i = 0 ; $i<count($mix) ; $i++)
          {
              echo "<br>Inverse_sbox[$i]= " . implode(" ",$mix[$i]);
          }
          echo "<br>";
          echo "<br>------------------------------------------";
          echo "<br>";

          for($i = 0 ; $i<count($state) ; $i++)
          {
              echo "<br>state[$i]= " . implode(" ",$state[$i]);
          }
          echo "<br>";
          $mix = $this->MixColumns($state);

          for($i = 0 ; $i<count($mix) ; $i++)
          {
              echo "<br>mix[$i]= " . implode(" ",$mix[$i]);
          }
          echo "<br>";

          $mix = $this->Inverse_MixColumns($mix);

          for($i = 0 ; $i<count($mix) ; $i++)
          {
              echo "<br>Inverse_mix[$i]= " . implode(" ",$mix[$i]);
          }

          echo "<br>";
          echo "<br>------------------------------------------";
          echo "<br>";
          
          for($i = 0 ; $i<count($state) ; $i++)
          {
              echo "<br>state[$i]= " . implode(" ",$state[$i]);
          }
          echo "<br>";
          $mix = $this->shift_row($state);

          for($i = 0 ; $i<count($mix) ; $i++)
          {
              echo "<br>shift[$i]= " . implode(" ",$mix[$i]);
          }
          echo "<br>";

          $mix = $this->inv_shift_row($mix);

          for($i = 0 ; $i<count($mix) ; $i++)
          {
              echo "<br>inverse_shift[$i]= " . implode(" ",$mix[$i]);
          }
      }
}
?>