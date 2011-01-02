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
        <?php include("header.php"); ?>
		<h1>In<sub>1-x</sub>Ga<sub>x</sub>As HeteroJunction Diode Reverse-Bias Current Calculator</h1>
		<h2 style="margin:0 auto;font-size:100%;color:#ccc;text-transform:none;"><!--a href="LINK TO THESIS PDF!"-->Exploration of novel heterostructure semiconductors to create energy efficient, tunnel-based FETs<!--/a--></h2>
		<div id="content">
		    <!--begin content-->
		    <div id="main">
		        <!--begin main-->
				<p>Independent Variables:</p>
				<table class="variables">
					<tr>
						<td>N<sub>A</sub>:<input type="text" name="NA" value="5.5e19" size=8 />cm<sup>-3</sup></td>
						<td>x<sub>n</sub>:<input type="text" name="xn" value=".47" size=8 /></td>
						<td>T:<input type="text" name="T" value="300" size=8 />K</td>
						<td>V<sub>r,low</sub>:<input type="text" name="Vrlow" value="0" size=8 />V</td>
						<td>Area:<input type="text" name="area" value="12000" size=8 />&mu;m<sup>2</sup></td>
					</tr>
					<tr>
						<td>N<sub>D</sub>:<input type="text" name="ND" value="1.2e19" size=8 />cm<sup>-3</sup></td>
						<td>x<sub>p</sub>:<input type="text" name="xp" value=".47" size=8 /></td>
						<td>m<sup>*</sup>:<input type="text" name="meff" value=".042" size=8 /></td>
						<td>V<sub>r,high</sub>:<input type="text" name="Vrhigh" value="0.5" size=8 />V</td>
						<td>R<sub>series</sub>:<input type="text" name="rseries" value="5" size=8 />&Omega;</td>
					</tr>
				</table>
		        <div id="plot">
		        	<input type="button" id="plotButton" value="Plot" />
					<div id="placeholder"></div>
		        </div>
				<div id="depvars">
					<table style="width:auto;">
						<tr><td colspan="3">Dependent Variables</td></tr>
						<tr><td>E<sub>gn</sub>:</td><td id="Egn"></td><td>eV</td></tr>
						<tr><td>E<sub>gp</sub>:</td><td id="Egp"></td><td>eV</td></tr>
						<tr><td>V<sub>bi</sub>:</td><td id="Vbi"></td><td>V</td></tr>
						<tr><td>N<sub>c</sub>:</td><td id="Nc"></td><td>cm<sup>-3</sup></td></tr>
						<tr><td>N<sub>v</sub>:</td><td id="Nv"></td><td>cm<sup>-3</sup></td></tr>
						<tr><td>E<sub>f,n</sub>:&nbsp;</td><td id="Efn"></td><td>eV</td></tr>
						<tr><td>E<sub>f,p</sub>:</td><td id="Efp"></td><td>eV</td></tr>
						<tr><td>&eta;<sub>r,n</sub>:&nbsp;</td><td colspan="2"id="er_n"></td></tr>
						<tr><td>&eta;<sub>r,p</sub>:</td><td colspan="2"id="er_p"></td></tr>
						<tr><td>&Delta;E<sub>c</sub>:</td><td colspan="2"><input type="text" name="delEc" id="delEc" value="FILL IN INITIAL VALUE!" size=8 /></td></tr>
					</table>
					<div id="overview"></div>
					<p id="overviewLegend" style="margin-left:10px"></p>
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
