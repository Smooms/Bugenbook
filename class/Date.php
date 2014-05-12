<?php

/**
 * 
 * @author Steffen Krohn
 *
 */
class Date
{
	
	public static function toMysqlDate($day, $month, $year)
	{
		$date = $year . '-' . $month . '-' . $day;
		return $date;
	}
	
	public static function date2GermanDate($date)
	{
		return date('d.m.Y' , strtotime($date));
	}
	
	public static function germanDate2Date($date)
	{
		return date('Y-m-d' , strtotime($date));
	}
	
	public static function getEaster($year)
	{
		$a = $year % 19;

		$b = (int)($year / 100);
		
		$c = $year % 100;
		
		$d = (int)($b / 4);
		
		$e = $b % 4;
		
		$f = (int)( ($b + 8) / 25);
		
		$g = (int)( ( $b - $f + 1 ) / 3 );
		
		$h = ( ($a * 19) + $b + 15 - $d - $g ) % 30;
		
		$j = $c % 4;
		
		$i = (int)($c / 4);
		
		$k = ((($e + $i) * 2) + 32 - $h - $j) % 7;
		
		$l = (int)((((($k * 2) + $h) * 11) + $a) / 451);

		$n = (($h + $k + 114) - ($l * 7) ) % 31;
		
		$m = (int)((($h + $k + 114) - ($l * 7) ) / 31);
		
		$tag = $n + 1;
		$monat = $m;
		
		
		return "$tag.$monat.$year";
	}

	public static function getWeekdayByGermanDate($date)
	{
		$array = explode('.', $date);
		
		$tag = $array[0];
		$monat = $array[1];
		$jahr = $array[2];
		
		$monatsziffer_array = array(
			1 => 0,
			2 => 3,
			3 => 3,
			4 => 6,
			5 => 1,
			6 => 4,
			7 => 6,
			8 => 2,
			9 => 5,
			10 => 0,
			11 => 3,
			12 => 5
		);
		
		$tagesziffer = $tag % 7;
		$monatsziffer = $monatsziffer_array[$monat];
		
		$jahr_12 = substr($jahr, 0 , 2);
		$jahr_34 = substr($jahr, 2 , 2);
		
		$jahresziffer = ($jahr_34 + ( $jahr_34 / 4 )) % 7;
		
		$jahrhunderziffer = ( 3 - ( $jahr_12 % 4 ) ) * 2;
		
		if(Date::isLeapYear($jahr))
		{
			$schaltjahr = 6;
		}
		else
		{
			$schaltjahr = 0;
		}
		
		return ( $tagesziffer + $monatsziffer + $jahrhunderziffer + $jahresziffer + $schaltjahr ) % 7;
	}
	
	public static function isLeapYear($year)
	{
		return ((($year % 4) == 0) && ((($year % 100) != 0) || (($year %400) == 0)));
	}
	
	public static function checkTime($time)
	{
		
		$array = explode(':', $time);
		
		foreach ($array as $key => $value) 
		{
			echo preg_match('/^[0-9]+[0-9]$/', $value);
			
			if ($key == 0) 
			{
				if ( $value <= 0 AND $value >= 23 )
				{
					return false;
				}
			}
			else 
			{
				if ( $value <= 0 AND $value >= 59 )
				{
					return false;
				}
			}
		}
		return true;
	}
	
	
}