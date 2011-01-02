<?php
    $FALSE_VALUE = "false";
	$TRUE_VALUE = "true";
	
	$jsonResponse = array ();
	$jsonResponse['successful'] = $FALSE_VALUE;
	
	if (! isset ($_POST['NA']) || ! isset ($_POST['ND'])|| ! isset ($_POST['xn'])|| ! isset ($_POST['xp'])|| ! isset ($_POST['Vrlow'])|| ! isset ($_POST['Vrhigh'])|| ! isset ($_POST['meff'])|| ! isset ($_POST['area'])|| ! isset ($_POST['rseries'])) {
		$jsonResponse['error'] = "One or more of the variables were not received.";
	} else {
		/*Input Variables (static for now)
		 * -------------------------------------------------------------------------*/
		/*
		$NA = 5.5e19;//cm^-3
		$ND = 1.2e19;//cm^-3
		$xn = .47;//fraction of GALLIUM on n-side, NOT indium
		$xp = .47;//fraction of GALLIUM on p-side, NOT indium
		$T = 300;//Kelvin
		$Vrlow = 0;//Volts
		$Vrhigh = .5;//negative Volts
		$meff = .047;//effective/reduced mass
		$meff*=9.11e-31;//to get it into kg
		$area = 12000;//um^2, for use with series r when importing data from file
		$rseries = 5;//Ohms, for use with series r when importing data from file
		*/
		$NA = $_POST['NA'];//cm^-3
		$ND = $_POST['ND'];//cm^-3
		$xn = $_POST['xn'];//fraction of GALLIUM on n-side, NOT indium
		$xp = $_POST['xp'];//fraction of GALLIUM on p-side, NOT indium
		$T = $_POST['T'];//Kelvin
		$Vrlow = $_POST['Vrlow'];//Volts
		$Vrhigh = $_POST['Vrhigh'];//negative Volts
		$meff = $_POST['meff'];//effective/reduced mass
		$meff*=9.11e-31;//to get it into kg
		$area = $_POST['area'];//um^2, for use with series r when importing data from file
		$rseries = $_POST['rseries'];//Ohms, for use with series r when importing data from file
		
		if ($NA == null || !is_numeric($NA) || $ND == null || !is_numeric($ND) || $xn == null || !is_numeric($xn) || $xp == null || !is_numeric($xp) || $T == null || !is_numeric($T) || $meff == null || !is_numeric($meff) || $Vrlow == null || !is_numeric($Vrlow) || $Vrhigh == null || !is_numeric($Vrhigh) || $area == null || !is_numeric($area) || $rseries == null || !is_numeric($rseries)) {
			$jsonResponse['error'] = "One or more of the variables were not valid.";
		} else {
			/*Global Variables (constants of nature)
			 * --------------------------------------------------------------------------*/
			$q = 1.602e-19;//C
			$eo = 8.854e-12;//C^2 s^2 m^3 kg^-1
			$k = 1.38e-23;//kg m s^-2 K^-1
			$hbar = 1.054e-34;//kg m^2 s^-1
			
			/*Calculated (Dependent) Variables
			 * --------------------------------------------------------------------------*/
			$Egn = 0.42+.625*$xn-((5.8/($T+300))-4.19/($T+271))*pow(10, -4)*pow($T, 2)*$xn-4.19*pow(10, -4)*pow($T, 2)/($T+271)+0.475*pow($xn, 2);//eV
			$Egn*=$q;//kg m^2 s^-2
			$Egp = 0.42+.625*$xp-((5.8/($T+300))-4.19/($T+271))*pow(10, -4)*pow($T, 2)*$xp-4.19*pow(10, -4)*pow($T, 2)/($T+271)+0.475*pow($xp, 2);//eV
			$Egp*=$q;//kg m^2 s^-2
			$Nc = 4.82e15*pow(0.023+0.037*$xn+0.003*pow($xn, 2), 3/2)*pow($T, 3/2);//cm^-3
			$Nv = 4.82e15*pow(0.41-0.1*$xp, 3/2)*pow($T, 3/2);
			$er_n = 15.1-2.87*$xn+0.67*pow($xn, 2);
			$er_n*=$eo;//because we'll rarely use it alone
			$er_p = 15.1-2.87*$xp+0.67*pow($xp, 2);
			$er_p*=$eo;//because we'll rarely use it alone
			$delEc = abs((4.9-0.83*$xn)-(4.9-0.83*$xp));//SET THIS IN THE FRONT END--NEEDS TO BE USER-EDITABLE
			/*Calculating Vbi:
			 *We know that etan=(Efn-Ecn)/(k*T)
			 *and likewise, etap=(Efp-Evp)/(k*T)
			 *
			 * We do not yet, however, know either eta.
			 * 
			 * Using the fermi function, however, will let us 
			 * iteratively solve for eta using the fact that
			 * n/Nc=fermi(.5,etan) & p/Nv=fermi(.5,etap)
			 * where n=ND & p=NA*/
			require("fermi.php");
			$from=-10;$to=60;$step=1;$minn=1e6;$minp=1e6;
			for ($eta=$from;$eta<=$to;$eta+=$step)
			{
				$sol=fermi(.5,$eta);
				if (abs($sol-$ND/$Nc) < $minn)
				{
					$etan=$eta;
					$minn=abs($sol-$ND/$Nc);
				}
				if (abs($sol-$NA/$Nv) < $minp)
				{
					$etap=$eta;
					$minp=abs($sol-$NA/$Nv);
				}
			}
			$Efn=$etan*$k*$T/$q;//eV
			$Efp=$etap*$k*$T/$q;//eV
			$Vbi=$Efn+$Efp+$Egp/$q;
		                
			/*Opening Experimental Data & Parsing
			 * --------------------------------------------------------------------------*/
			$file=fopen("large-homo.txt","r");//THIS ASSUMES a voltage sweep from low (-ve) voltage to higher voltages!!!
			$experVolt=array();$experJ=array();$i=0;
			while(! feof($file))//feof checks for end of file at each iteration
			{
				$experDataLine=fgets($file);
				$experData=explode("\t",$experDataLine);//works for tab-separated values, but needs to work for csv, too
				if (sizeof($experData)==1) $experData=explode(", ",$experDataLine);//functions for csv, also, now	
				
				if(is_numeric($experData[0]))//ignore empty lines
				{
					$lastColumn=sizeof($experData)-1;//  sizeof($experData)-1 because php begins counting at 0
					array_push($experVolt,$experData[0]);
					array_push($experJ,$experData[$lastColumn]);
					$experJ[$i]=trim($experJ[$i]);
					$i++;
				}
			}
			
			//the program only works for reverse current so all positive values must be cut. 
			//$Vrlow is the magnitude of the low bound on the reverse voltage
			$length=sizeof($experVolt)-1;
			while ($experVolt[$length]>-$Vrlow) 
			{
				array_pop($experVolt);array_pop($experJ);//pops off all values at end of arrays that break the above rule
				$length=sizeof($experVolt)-1;
			}
			while($experVolt[0]<-$Vrhigh)//similar idea for Vrhigh
			{
				array_shift($experVolt);array_shift($experJ); //shifts array 'to the left', cutting out first element
			}
			
			//Calculating the electric field for the experimental data
			$experE=array();$size=sizeof($experVolt);$Vp2=array();
			for($i=0;$i<$size;$i++){
				array_push($Vp2,-$experVolt[$i]+$Vbi);//creating an array $Vp, the next value of which is $V+Vbi
				$W=sqrt(2*$er_n*$er_p*$Vp2[$i]*pow($NA+$ND,2)/($q*($er_n*$ND+$er_p*$NA)*$NA*$ND));
				array_push($experE,$Vp2[$i]/$W);//in V/m
				$experE[$i]/=-1e8;// gets it into MV/cm
			}
			
			/*Calculating the ideal Reverse Tunneling Current, given the input variables
			 * --------------------------------------------------------------------------*/
			$i=0;$precision=100;$Volt=array();$Vp=array();$E=array();$E2=array();$Jp=array();$I=array();$NA*=1e6;$ND*=1e6;
			for ($V=$Vrlow;$V<=$Vrhigh;$V+=abs($Vrhigh-$Vrlow)/($precision))
			{
				array_push($Volt,$V);//creating an array $Volt, the next value of which is $V
				array_push($Vp,$V+$Vbi);//creating an array $Vp, the next value of which is $V+Vbi
				$W=sqrt(2*$er_n*$er_p*$Vp[$i]*pow($NA+$ND,2)/($q*($er_n*$ND+$er_p*$NA)*$NA*$ND));
				array_push($E,($Vp[$i]+$delEc)/$W);
				$exponent=-4*sqrt(2*$meff)*pow(($Egn+$Egp)/2,3/2)/(3*$q*$hbar*$E[$i]);
				$coeff=sqrt(2*$meff)*pow($q,3)*$E[$i]*$V/(4*pow(M_PI,3)*pow($hbar,2)*sqrt(($Egn+$Egp)/2));
				array_push($Jp,$coeff*exp($exponent));//calculating reverse tunneling current Jp in A/m^2
								
				//correcting for series r, given input resistance
				$index=0; //this loops through and finds the experimental voltage closest to the current $V
				for ($j=0;$j<$size;$j++){
					if (abs($experVolt[$j]+$V)<abs($experVolt[$index]+$V)) $index=$j;
				}
				$reducedVoltage=$rseries*$area*1e-3*$experJ[$index];//  ohm*um^2*mA/um^2*1e-3A/mA=V
			    $Vp[$i]=$Vp[$i]-$reducedVoltage;
			    $W=sqrt(2*$er_n*$er_p*$Vp[$i]*pow($NA+$ND,2)/($q*($er_n*$ND+$er_p*$NA)*$NA*$ND));
			    array_push($E2,($Vp[$i]+$delEc)/$W);//  kg^.5 V^.5 s^-1
			    $exponent=-4*sqrt(2*$meff)*pow(($Egn+$Egp)/2,3/2)/(3*$q*$hbar*$E2[$i]);
				$coeff=sqrt(2*$meff)*pow($q,3)*$E2[$i]*$V/(4*pow(M_PI,3)*pow($hbar,2)*sqrt(($Egn+$Egp)/2));
			    array_push($I,$coeff*exp($exponent));//  A/m^2
			    $Jp[$i]*=1e-9;$I[$i]*=1e-9;//   A/m^2*(1m^2/1e12um^2 *1e3mA/A) = mA/um^2
				$i++;
			}
			for ($i=0;$i<sizeof($experJ);$i++) {$experJ[$i]=log($experJ[$i],10);} //semilog axis
			for ($i=0;$i<=$precision;$i++) {
				$E[$i]/=-1e8;$E2[$i]/=-1e8;//   V/m*1m/100cm*1MV/1e6V = MV/cm
				$Jp[$i]=log($Jp[$i],10);$I[$i]=log($I[$i],10);//  to plot on a semilog axis
				if ($Jp[$i]<-1e50) $Jp[$i]=min($experJ);
				if ($I[$i]<-1e50) $I[$i]=min($experJ);
			}
		}
		
		//getting the variables in a form the front-end can use
		$jsonResponse['Volt'] = $Volt;
		$jsonResponse['E'] = $E;
		$jsonResponse['Jp'] = $Jp;
		$jsonResponse['I'] = $I;
		$jsonResponse['experVolt'] = $experVolt;
		$jsonResponse['experE'] = $experE;
		$jsonResponse['experJ'] = $experJ;
		$jsonResponse['Egn'] = $Egn/$q;
		$jsonResponse['Egp'] = $Egp/$q;
		$jsonResponse['Vbi'] = $Vbi;
		$jsonResponse['Nc'] = $Nc;
		$jsonResponse['Nv'] = $Nv;
		$jsonResponse['er_n'] = $er_n/$eo;
		$jsonResponse['er_p'] = $er_p/$eo;
		$jsonResponse['Efn'] = $Efn;
		$jsonResponse['Efp'] = $Efp;
		$jsonResponse['delEc'] = $delEc;
		$jsonResponse['successful'] = $TRUE_VALUE;
	}
//return the variables to the front-end
echo json_encode($jsonResponse);
?>
				
