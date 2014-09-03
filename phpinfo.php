
<?php 

// $a =1;
// $b=2;
// function Sum(){
// 	global $a,$b;
// 	$b = $a+$b;
// }
// Sum(); echo '</br>'; echo $b;

function test()
{
	static $a = 0;
	$a++;
	return $a;
}
for ($i=0;$i<=5;$i++){
	$r = test();
}
echo $r;

  
  echo    mb_substr('5.00',-1,-5);
?>