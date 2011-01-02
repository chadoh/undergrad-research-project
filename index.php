<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en" dir="ltr">
    <head>
        <title><?php include("title.php");?></title>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
        <meta name="MSSmartTagsPreventParsing" content="true" />
        <link rel="icon" type="image/vnd.microsoft.icon" href="http://chad-oh.com/images/favicon.ico"/>
        <link rel="stylesheet" type="text/css" href="styles/all.css"/>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8">

		<script language="javascript" type="text/javascript" src="scripts/jquery.js"></script>
		<!--[if IE]><script language="javascript" type="text/javascript" src="scripts/flot/excanvas.pack.js"></script><![endif]-->
        <script language="javascript" type="text/javascript" src="scripts/flot/jquery.flot.js"></script>
		<script language="javascript" type="text/javascript" src="scripts/scripts.js"></script>
    </head>
    <body>
    	<?php
		/*Сheck to see if a file was uploaded
		-----------------------------------------------------------------------*/
		if(!empty($_FILES["file"])) {
			//Check if the file is txt it's size is less than 10Kb (other file formats??)
			$filename = basename($_FILES['file']['name']);
			$ext = substr($filename, strrpos($filename, '.') + 1);
			if (($ext == "txt") && ($_FILES["file"]["type"] == "text/plain") && ($_FILES["file"]["size"] < 10000)) {
				//Determine the path to which we want to save this file
					$newname = dirname(__FILE__).'/experiments/xp-'.$_POST['file_xp'].',xn-'.$_POST['file_xn'].',NA-'.$_POST['file_NA'].',ND-'.$_POST['file_ND'].',T-'.$_POST['file_T'].',A-'.$_POST['file_A'].';from_'.$_POST['file_Vrlow'].'V_to_'.$_POST['file_Vrhigh'].'V';
					//This name is unmemorable, so create a random name to make the file name more memorable
					$consonants='b c d f g h j k l m n p r s t v w x z';$consonants=explode(' ',$consonants);
					$vowels='a e i o u y';$vowels=explode(' ',$vowels);$randomName='';
					for ($i=0;$i<3;$i++){
						$randomName.=$consonants[rand(0,sizeof($consonants)-1)];
						$randomName.=$vowels[rand(0,sizeof($vowels)-1)];
					}
					$randomName=ucfirst($randomName);//making the first letter upper case
					//add the randomName to the newname, for usability:
					$newname.=' ('.$randomName.')';
					//while a file already exists with this name, increase the 'number' of the name
					$i=2;
					while(file_exists($newname.'.txt')){
						$newname.=$i;
						$i++;
					}
					$newname.='.txt';
					//save the uploaded file to the new location
					if (move_uploaded_file($_FILES['file']['tmp_name'],$newname)){
						//tell the user the name of the new file, in a readable fashion
						$readableName=substr($newname,strrpos($newname,'experiments/')+12);
						echo '<div class="fileUploadMsg">The file has been saved as "'.$readableName.'"</div>';
					}else{
						echo '<div class="fileUploadMsg">The file uploaded incorrectly. Try again, maybe.</div>';
					};
		  } else {
				echo "<div class='fileUploadMsg'>Only .txt files under 10Kb are accepted for upload.</div>";
			}
		} elseif ($_FILES['file']['error']!=0) {
			echo "<div class='fileUploadMsg'>The file uploaded incorrectly. Try again, maybe.</div>";
		}
		/*---------------------------------------------------------------------*/
		
		//And the normal page begins!
		include("header.php"); ?>
		<h1>In<sub>1-x</sub>Ga<sub>x</sub>As HeteroJunction Diode Reverse-Bias Current Calculator</h1>
		<h2 style="margin:0 auto;font-size:100%;color:#ccc;text-transform:none;">
			<a id="showAbstract" style="color:#ccc;" href="thesis.pdf">
				Exploration of novel heterostructure semiconductors to create energy efficient, tunnel-based FETs
			</a>
		</h2>
		<div id="abstract">A simple program to calculate device characteristics of a reverse-biased, 
		heterojunction InGaAs diode. This calculator allows for quick estimates of ideal device currents given various 
		design parameters, as well as allowing experimental results to be plotted along with ideal results for 
		comparison. Additionally, the calculator allows for the estimation of series resistance inherent in an actual 
		device due to making contact to it. This calculator uses only software freely available to the general public. 
		<br/><a href="thesis.pdf">View full description</a>
		</div>
		<div id="content">
		    <!--begin content-->
		    <div id="main">
		        <!--begin main-->
				<p>Independent Variables:</p>
				<?php //need to check to see if files were included in the url
				if (!empty($_GET['NA'])) $NA=$_GET['NA']; else $NA=5.5e19;
				if (!empty($_GET['xn'])) $xn=$_GET['xn']; else $xn=.47;
				if (!empty($_GET['T']))  $T =$_GET['T'];  else $T=300;
				if (!empty($_GET['VL'])) $VL=$_GET['VL']; else $VL=0;
				if (!empty($_GET['A']))  $A =$_GET['A'];  else $A=12000;
				if (!empty($_GET['ND'])) $ND=$_GET['ND']; else $ND=1.2e19;
				if (!empty($_GET['xp'])) $xp=$_GET['xp']; else $xp=.47;
				if (!empty($_GET['m']))  $m =$_GET['m'];  else $m=.042;
				if (!empty($_GET['VH'])) $VH=$_GET['VH']; else $VH=0.5;
				if (!empty($_GET['r']))  $r =$_GET['r'];  else $r=5;
				if (!empty($_GET['delEc'])) $delEc=$_GET['delEc']; else $delEc=0;
				if (!empty($_GET['experFile'])) $experFile=$_GET['experFile']; 
					else $experFile='xp-.47,xn-.47,NA-5.5e19,ND-1.2e19,T-300,A-12000;from_0V_to_-0.5V (Yoboba).txt';
				?>
				<table class="variables">
					<tr>
						<td>N<sub>A</sub>:<input type="text" name="NA" value="<?php echo $NA; ?>" size=8 />cm<sup>-3</sup></td>
						<td>x<sub>n</sub>:<input type="text" name="xn" value="<?php echo $xn; ?>" size=8 /></td>
						<td>T:<input type="text" name="T" value="<?php echo $T; ?>" size=8 />K</td>
						<td>V<sub>r,low</sub>:<input type="text" name="VL" value="<?php echo $VL; ?>" size=8 />V</td>
						<td>Area:<input type="text" name="A" value="<?php echo $A; ?>" size=8 />&mu;m<sup>2</sup></td>
					</tr>
					<tr>
						<td>N<sub>D</sub>:<input type="text" name="ND" value="<?php echo $ND; ?>" size=8 />cm<sup>-3</sup></td>
						<td>x<sub>p</sub>:<input type="text" name="xp" value="<?php echo $xp; ?>" size=8 /></td>
						<td>m<sup>*</sup>:<input type="text" name="m" value="<?php echo $m; ?>" size=8 /></td>
						<td>V<sub>r,high</sub>:<input type="text" name="VH" value="<?php echo $VH; ?>" size=8 />V</td>
						<td>R<sub>series</sub>:<input type="text" name="r" value="<?php echo $r; ?>" size=8 />&Omega;</td>
					</tr>
					<tr>
						<td colspan=3 style="text-align:left;">&nbsp;</td>
						<td colspan=2>
							&Delta;E<sub>c</sub>:<input type="text" name="delEc" id="delEc" value="<?php echo $delEc; ?>" size=8 />
							(or <a href="#" id="calcDelEc" title=" to 0th-order accuracy ">Calculate</a>)
						</td>
					</tr>
					<tr>
						<td style="text-align:left" colspan=5>Experimental Results:
							<select name='experFile'>
								<?php
									$directory='experiments';
									if (is_dir($directory)){
										if ($handler=opendir($directory)){
											$files=array();
											while ($nextfile=readdir($handler))
											{
												if (substr($nextfile, 0, 1) != ".")
                    							{
                    								$files[]=$nextfile;
												}
											}
										closedir($handler);
										}
									}
									else echo '<option value="poo">FAILURE!</option>';
									
									sort($files);
									
									for ($i=0;$i<sizeof($files);$i++){
										echo '<option value="'.$files[$i].'"';
										if ($files[$i]==$experFile)
											echo ' selected="selected">';
											else echo '>';
										echo $files[$i].'</option>';
									}
								?>
							</select>
							<br />or <a id="fileUploadButton" href="#">upload your own data</a>.<!--script this!-->
						</td>
					</tr>
				</table>
				<fieldset id="fileUpload">
					<legend>File Upload</legend>
					Your file will be named by the following data. It will also be given a randomly generated
					name (like Yoboba) to make it more memorable.
					<form enctype="multipart/form-data" action="index.php" method="post">
						x<sub>p</sub>: <input name='file_xp' type='text' size='3'>Ga,
						x<sub>n</sub>: <input name='file_xn' type='text' size='3'>Ga,
						N<sub>A</sub>: <input name='file_NA' type='text' size='3'>cm<sup>-3</sup>,
						N<sub>D</sub>: <input name='file_ND' type='text' size='3'>cm<sup>-3</sup>,
						<br/>
						T: <input name='file_T' type='text' size='3'>K,
						A: <input name='file_A' type='text' size='3'>&mu;m<sup>2</sup>; 
						from <input name='file_Vrlow' type='text' size='3'>V
						to  <input name='file_Vrhigh' type='text' size='3'>V
						<br/>
						<br/>
						<input type="hidden" name="MAX_FILE_SIZE" value="1000000" />
					    <input name="file" type="file" id="file" />
					    <input class='button' type="submit" value="Upload" />
						<!--Check that all fields are filled before submission!-->
					</form>
				</fieldset>
	        	<div id="prePlotMatter">
		        	<input class='button' type="button" id="plotButton" value="Plot"style="float:left;" />
					<div style="float:right;">
						<a id="showPageLink"href="#" title="Get a link to share these results">Link</a>
					</div>
	        	</div>
				<div id="pageLink">
					<input name="linkToShare" type="text" value="" size="105" style="text-align:left;padding:2px 2px 2px 5px;"/>
				</div>
		        <div id="plot">
					<h3>Current Density vs. Voltage</h3>
					<div class="x-axis-label">
						<div class="chart" id="j-vs-v"></div>
						<p>Volts</p>
					</div>
		        </div>
				<div id="depvars">
					<table style="width:auto;">
						<tr><td colspan="3">Dependent Variables</td></tr>
						<tr><td>E<sub>g,n</sub>:</td><td id="Egn"></td><td>eV</td></tr>
						<tr><td>E<sub>g,p</sub>:</td><td id="Egp"></td><td>eV</td></tr>
						<tr><td>V<sub>bi</sub>:</td><td id="Vbi"></td><td>V</td></tr>
						<tr><td>N<sub>c</sub>:</td><td id="Nc"></td><td>cm<sup>-3</sup></td></tr>
						<tr><td>N<sub>v</sub>:</td><td id="Nv"></td><td>cm<sup>-3</sup></td></tr>
						<tr><td>E<sub>f,n</sub> - E<sub>c,n</sub>:&nbsp;</td><td id="Efn"></td><td>eV</td></tr>
						<tr><td>E<sub>v,p</sub> - E<sub>f,p</sub>:</td><td id="Efp"></td><td>eV</td></tr>
						<tr><td>&eta;<sub>r,n</sub>:&nbsp;</td><td colspan="2"id="er_n"></td></tr>
						<tr><td>&eta;<sub>r,p</sub>:</td><td colspan="2"id="er_p"></td></tr>
					</table>
					<p id="overview-intro">Click and drag to zoom.</p>
					<!--input type='button' id='zoom' value='Zoom Out'/-->
					<div id="overview"></div>
					<p id="overviewLegend" style="margin-left:10px"></p>
					<!--MAKE SWITCHING FROM V TO E EASY (POSSIBLE, EVEN!)!-->
				</div>
				<div id="band-diagrams">
					<h3>Band Diagrams</h3>
					<p>In the following graphs, the midway-point between the n-side valence band and the p-side
					conduction band is taken to be zero energy.</p>
					<div id="zero-bias">
						<h4>Bands under zero bias</h4>
						<div class="x-axis-label">
							<div class="chart" id="band-zero-bias"></div>
							<p>arbitrary length units</p>
						</div>
					</div>
					<div id="band-legend"></div>
					<div id="full-bias">
						<h4>Bands under an applied reverse bias of <span id="Vhigh"></span>V</h4>
						<div class="x-axis-label">
							<div class="chart" id="band-full-bias"></div>
							<p>arbitrary length units</p>
						</div>
					</div>
				</div>
			</div>
			<!--End #main-->
		</div>
		<!--End #content-->
	        <!-- Begin #footer -->
		<div id="footer">
		    <hr/>
		    <p>
		        download the <!--a href="LINK TO CODE DIRECTORY!"-->code<!--/a-->
		    </p>
		</div>
		<!-- End #footer -->
    </body>
</html>
