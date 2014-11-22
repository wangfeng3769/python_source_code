<?php

class Location {

	private $mResults = array();

	public $latitude = 0.0;
	public $longitude = 0.0;
	public $altitude = 0.0;
	public $accuracy = 0.0;
	public $time = 0;

	// Cache the inputs and outputs of computeDistanceAndBearing
	// so calls to distanceTo() and bearingTo() can share work
	private $mLat1 = 0.0;
	private $mLon1 = 0.0;
	private $mLat2 = 0.0;
	private $mLon2 = 0.0;
	private $mDistance = 0.0;

	public function distanceTo($dest) {
		if ($this->latitude != $this->mLat1 || $this->longitude != $this->mLon1
		|| $dest->mLatitude != $this->mLat2 || $dest->mLongitude != $this->mLon2) {
			self::computeDistanceAndBearing($this->latitude, $this->longitude,
			$dest->mLatitude, $dest->mLongitude, $this->mResults);
			$this->mLat1 = $this->latitude;
			$this->mLon1 = $this->longitude;
			$this->mLat2 = $dest->mLatitude;
			$this->mLon2 = $dest->mLongitude;
			$this->mDistance = $this->mResults[0];
		}
		return $this->mDistance;
	}

	private static function computeDistanceAndBearing($lat1, $lon1,
		$lat2, $lon2, &$results) {
		// Based on http://www.ngs.noaa.gov/PUBS_LIB/inverse.pdf
		// using the "Inverse Formula" (section 4)

		$MAXITERS = 20;
		// Convert lat/long to radians
		$lat1 *= M_PI / 180.0;
		$lat2 *= M_PI / 180.0;
		$lon1 *= M_PI / 180.0;
		$lon2 *= M_PI / 180.0;

		$a = 6378137.0; // WGS84 major axis
		$b = 6356752.3142; // WGS84 semi-major axis
		$f = ($a - $b) / $a;
		$aSqMinusBSqOverBSq = ($a * $a - $b * $b) / ($b * $b);

		$L = $lon2 - $lon1;
		$A = 0.0;
		$U1 = atan((1.0 - $f) * tan($lat1));
		$U2 = atan((1.0 - $f) * tan($lat2));

		$cosU1 = cos($U1);
		$cosU2 = cos($U2);
		$sinU1 = sin($U1);
		$sinU2 = sin($U2);
		$cosU1cosU2 = $cosU1 * $cosU2;
		$sinU1sinU2 = $sinU1 * $sinU2;

		$sigma = 0.0;
		$deltaSigma = 0.0;
		$cosSqAlpha = 0.0;
		$cos2SM = 0.0;
		$cosSigma = 0.0;
		$sinSigma = 0.0;
		$cosLambda = 0.0;
		$sinLambda = 0.0;

		$lambda = $L; // initial guess
		for ($iter = 0; $iter < $MAXITERS; $iter++) {
			$lambdaOrig = $lambda;
			$cosLambda = cos($lambda);
			$sinLambda = sin($lambda);
			$t1 = $cosU2 * $sinLambda;
			$t2 = $cosU1 * $sinU2 - $sinU1 * $cosU2 * $cosLambda;
			$sinSqSigma = $t1 * $t1 + $t2 * $t2; // (14)
			$sinSigma = sqrt($sinSqSigma);
			$cosSigma = $sinU1sinU2 + $cosU1cosU2 * $cosLambda; // (15)
			$sigma = atan2($sinSigma, $cosSigma); // (16)
			$sinAlpha = ($sinSigma == 0) ? 0.0 : $cosU1cosU2 * $sinLambda / $sinSigma; // (17)
			$cosSqAlpha = 1.0 - $sinAlpha * $sinAlpha;
			$cos2SM = ($cosSqAlpha == 0) ? 0.0 : $cosSigma - 2.0 * $sinU1sinU2 / $cosSqAlpha; // (18)

			$uSquared = $cosSqAlpha * $aSqMinusBSqOverBSq; // defn
			$A = 1 + ($uSquared / 16384.0) * (4096.0 + $uSquared * (-768 + $uSquared * (320.0 - 175.0 * $uSquared)));
			$B = ($uSquared / 1024.0) * (256.0 + $uSquared * (-128.0 + $uSquared * (74.0 - 47.0 * $uSquared)));
			$C = ($f / 16.0) * $cosSqAlpha * (4.0 + $f * (4.0 - 3.0 * $cosSqAlpha)); // (10)
			$cos2SMSq = $cos2SM * $cos2SM;
			$deltaSigma = $B * $sinSigma * ($cos2SM + ($B / 4.0) * ($cosSigma * (-1.0 + 2.0 * $cos2SMSq) - ($B / 6.0) * $cos2SM
			* (-3.0 + 4.0 * $sinSigma * $sinSigma)	* (-3.0 + 4.0 * $cos2SMSq)));

			$lambda = $L + (1.0 - $C) * $f	* $sinAlpha	* ($sigma + $C * $sinSigma	* ($cos2SM + $C * $cosSigma
			* (-1.0 + 2.0 * $cos2SM * $cos2SM))); // (11)

			$delta = ($lambda - $lambdaOrig) / $lambda;
			if (abs($delta) < 1.0e-12) {
				break;
			}
		}

		$distance = (float) ($b * $A * ($sigma - $deltaSigma));
		$results[0] = $distance;
		if (count( $results ) > 1) {
			$initialBearing = (float) atan2($cosU2 * $sinLambda, $cosU1
			* $sinU2 - $sinU1 * $cosU2 * $cosLambda);
			$initialBearing *= 180.0 / M_PI;
			$results[1] = $initialBearing;
			if (count( $results ) > 2) {
				$finalBearing = (float) atan2($cosU1 * $sinLambda,
				-$sinU1 * $cosU2 + $cosU1 * $sinU2 * $cosLambda);
				$finalBearing *= 180.0 / M_PI;
				$results[2] = $finalBearing;
			}
		}
	}
}

?>
