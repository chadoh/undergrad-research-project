function showTooltip(x, y, contents) {
    $('<div id="tooltip">' + contents + '</div>').css( {
        position: 'absolute',
        display: 'none',
        top: y + 15,
        left: x - 50,
        border: '1px dotted #9ad',
        padding: '2px',
		color: '#000',
        'background-color': '#ddd',
        opacity: 0.90
    }).appendTo("body").fadeIn(200);
}

function makePlot(plotData){
	//drawing the main plot
	var options={legend:{show:false},
			lines:{show: true},
		    points:{show: true},
			xaxis:{tickDecimals:2},
			yaxis: {tickFormatter: function(v, axis){
				return "10<sup>"+v.toFixed(axis.tickDecimals)+"</sup> mA/&mu;m<sup>2</sup>"},
				tickDecimals:0
				},
		    grid: {hoverable: true, backgroundColor: "#fffaff"},
			selection: { mode: "xy" }
	}
	var plot=$.plot($("#j-vs-v"), plotData, options);
	
	//drawing the overview
	var liloptions={legend: { show: true, container:$("#overviewLegend")},
	        lines: { show: true, lineWidth: 1 },
	        shadowSize: 0,
	        xaxis: { ticks: 2,tickDecimals:1 },
	        yaxis: { ticks: 3,tickFormatter: function(v, axis){
			 	return "10<sup>"+v.toFixed(axis.tickDecimals)+"</sup>"
				},
				tickDecimals:0},
				
	        grid: { color: "#999",backgroundColor: "#fffaff"},
	        selection: { mode: "xy" }
			}
	var overview=$.plot($("#overview"),plotData,liloptions);
					
	// now connect the two:    
    $("#j-vs-v").bind("plotselected", function (event, ranges) {
        // clamp the zooming to prevent eternal zoom
        if (ranges.xaxis.to - ranges.xaxis.from < 0.00001)
            ranges.xaxis.to = ranges.xaxis.from + 0.00001;
        if (ranges.yaxis.to - ranges.yaxis.from < 0.00001)
            ranges.yaxis.to = ranges.yaxis.from + 0.00001;
        
        // do the zooming
        plot = $.plot($("#j-vs-v"), plotData,
                      $.extend(true, {}, options, {
                          xaxis: { min: ranges.xaxis.from, max: ranges.xaxis.to },
                          yaxis: { min: ranges.yaxis.from, max: ranges.yaxis.to }
                      }));
        
        // don't fire event on the overview to prevent eternal loop
        overview.setSelection(ranges, true);
    });
    $("#overview").bind("plotselected", function (event, ranges) {
        plot.setSelection(ranges);
    });
	
	//making hoverable tooltips show up
	var previousPoint = null;
	$("#j-vs-v").bind("plothover", function (event, pos, item) {
		if (item) {
	        if (previousPoint != item.datapoint) {
	            previousPoint = item.datapoint;
	            
	            $("#tooltip").remove();
	            var x = item.datapoint[0].toFixed(4),
	                y = item.datapoint[1].toFixed(4);
	            
	            showTooltip(item.pageX, item.pageY,
	                        "J<sub>p</sub> = 10<sup>"+y+"</sup>mA/&mu;m<sup>2</sup> at "+x+"V for the"+item.series.label);
	        }
        }
        else {
            $("#tooltip").remove();
            previousPoint = null;            
        }
	});
}
function plotBandDiagrams(Vbi,Egn,Egp,Efp,Efn,VH){
	var cBandZeroBias=[[-3,(Egn+Vbi)/2],[-0.5,(Egn+Vbi)/2],[0.5,-(Egn+Vbi)/2+Egn],[3,-(Egn+Vbi)/2+Egn]];
	var vBandZeroBias=[[-3,(Egn+Vbi)/2-Egp],[-0.5,(Egn+Vbi)/2-Egp],[0.5,-(Egn+Vbi)/2],[3,-(Egn+Vbi)/2]];
	var fBandZeroBias=[[-3,(Egn+Vbi)/2-Egp-Efp],[-0.5,(Egn+Vbi)/2-Egp-Efp],[0.5,-(Egn+Vbi)/2+Egn+Efn],[3,-(Egn+Vbi)/2+Egn+Efn]];
	var cBandFullBias=[[-3,(Egn+Vbi+VH)/2],[-0.5,(Egn+Vbi+VH)/2],[0.5,-(Egn+Vbi+VH)/2+Egn],[3,-(Egn+Vbi+VH)/2+Egn]];
	var vBandFullBias=[[-3,(Egn+Vbi+VH)/2-Egp],[-0.5,(Egn+Vbi+VH)/2-Egp],[0.5,-(Egn+Vbi+VH)/2],[3,-(Egn+Vbi+VH)/2]];
	var fBandFullBias=[[-3,(Egn+Vbi+VH)/2-Egp-Efp],[-0.5,(Egn+Vbi+VH)/2-Egp-Efp],null,[0.5,-(Egn+Vbi+VH)/2+Egn+Efn],[3,-(Egn+Vbi+VH)/2+Egn+Efn]];
	var zeroBiasData=[{label:'&nbsp;Fermi<br/>$nbsp;Level',data:fBandZeroBias},
					  {label:'&nbsp;Conduction<br/>&nbsp;Band',data:cBandZeroBias},
					  {label:'&nbsp;Valence<br/>&nbsp;Band',data:vBandZeroBias}];
	var fullBiasData=[{label:'&nbsp;Fermi<br/>&nbsp;Level',data:fBandFullBias},
					  {label:'&nbsp;Conduction<br/>&nbsp;Band',data:cBandFullBias},
					  {label:'&nbsp;Valence<br/>&nbsp;Band',data:vBandFullBias}];
	
	var options={legend: { show: true,container:$("#band-legend")},
	        lines: { show: true, lineWidth: 3 },
			shadowSize:0,
	        xaxis: { ticks: 0},
	        yaxis: {tickFormatter: function(v, axis){
				return v.toFixed(axis.tickDecimals)+"eV"},
				tickDecimals:1
				},
	        grid: { color: "#999",backgroundColor: "#fffaff"},
	        selection: { mode: "x" }
			}
	
	var plotZeroBiasBands=$.plot($("#band-zero-bias"),zeroBiasData,options);
	var plotFullBiasBands=$.plot($("#band-full-bias"),fullBiasData,options);
	
	$("#Vhigh").html(VH);
}

/*this is the main function of the page--------------------------------------------*/
function getGraphPoints(){
	//create link to the page
	var link='http://www.chad-oh.com/thesis/index.php?NA='+$("input[name=NA]").val()+'&ND='+$("input[name=ND]").val()+'&xn='+$("input[name=xn]").val()+'&xp='+$("input[name=xp]").val()+'&T='+$("input[name=T]").val()+'&m='+$("input[name=m]").val()+'&VL='+$("input[name=VL]").val()+'&VH='+$("input[name=VH]").val()+'&A='+$("input[name=A]").val()+'&r='+$("input[name=r]").val()+'&delEc='+$("input[name=delEc]").val()+'&experFile='+$("select[name=experFile]").val();
	//clearing plus signs, because they are interpretted to be spaces when in URIs
	link=link.replace(/\+/g,'');//this uses regex, where the //'s denote what to look for, the \+ tells it a plus (and not a special character), and the g tells it to replace all of them in the string.
	//set the link to the page, so that it always matches the actual plot
	$("input[name=linkToShare]").val(link);
	
	var theVariables=new Object();
    /*Scraping the input fields for the values they contain*/
    $(':input').each(function(){ /*cycles through all the input, textarea, and select elements on the page - note the colon*/
        var key = $(this).attr('name'); /*gets the name of that input (NA, ND, xn, xp, etc... */
        var val = $(this).val(); /* gets the value of that input */
        theVariables[key] = val; /*populates your array with this information */
    });
    //send theVariables to the backend, which calculates all of the data to be plotted
	//it encodes this with the json process
	//it sends its computed, json-encoded data to the getGraphPointsCallback function, below
    $.post('backend.php', theVariables, getGraphPointsCallback, 'json');
}

function getGraphPointsCallback(data, textStatus){
    if (data['successful']) {
		//getting the data from the backend
        var Volt = data['Volt'];
        var E = data['E'];
        var Jp = data['Jp'];
        var I = data['I'];
        var experVolt = data['experVolt'];
        var experE = data['experE'];
        var experJ = data['experJ'];
		//cleansing the data and making sure it's properly formatted
        for (i in Volt) {
			Volt[i] = parseFloat(Volt[i]);
			E[i] = parseFloat(E[i]);
			Jp[i] = parseFloat(Jp[i]);
			I[i] = parseFloat(I[i]);
		}
        for (i in experVolt) {
			experVolt[i] = parseFloat(experVolt[i]);
			experE[i] = parseFloat(experE[i]);
			experJ[i] = parseFloat(experJ[i]);
		}
		//turning the simple data arrays into plottable arrays
		var JvV = new Array();
		var IvV = new Array();
		var experJvexperV = new Array();
		var JvE = new Array();
		var IvE = new Array();
		var experJvexperE = new Array();
        for (i in Volt) {
            JvV.push([-Volt[i], Jp[i]]);
            IvV.push([-Volt[i], I[i]]);
            JvE.push([-E[i], Jp[i]]);
            IvE.push([-E[i], I[i]]);
        }
        for (i in experVolt) {
            experJvexperV.push([experVolt[i], experJ[i]]);
            experJvexperE.push([experE[i], experJ[i]]);
        }
		//putting the data into the form the plot uses, with labels
		var plotData=[{label: "&nbsp;experimental results",data: experJvexperV}, 
					  {label: "&nbsp;calculated I<sub>tunnel</sub> without R<sub>series</sub>",data: JvV}, 
					  {label: "&nbsp;calculated I<sub>tunnel</sub> with R<sub>series</sub>",data: IvV}];
		//sending this data to the function created above, which makes a plot from it
		makePlot(plotData);
		//filling in the dependent variables used in the calculation for the user to see
		var q=1.602e-19;
		var Egn=data['Egn']/q,Egp=data['Egp']/q;
		$('#Egn').text(Egn.toPrecision(5));
		$('#Egp').text(Egp.toPrecision(5));
		$('#Vbi').text(data['Vbi'].toPrecision(5));
		$('#Nc').text(data['Nc'].toPrecision(5));
		$('#Nv').text(data['Nv'].toPrecision(5));
		$('#er_n').text(data['er_n'].toPrecision(5));
		$('#er_p').text(data['er_p'].toPrecision(5));
		$('#Efn').text(data['Efn'].toPrecision(5));
		$('#Efp').text(data['Efp'].toPrecision(5));
		$('#delEc').val(data['delEc']);
		plotBandDiagrams(data['Vbi'],Egn,Egp,parseFloat(data['Efp']),parseFloat(data['Efn']),parseFloat($("input[name=VH]").val()));
    }
    else {
        alert(data['error']);
    }
}

/*This part will run once the page is fully loaded (that is, once the DOM is ready)---------*/
$(document).ready(function(){
    getGraphPoints();
	$("#pageLink").hide();
	$("#plotButton").click(getGraphPoints);
	$("#fileUpload").hide();
	$("#fileUploadButton").click(function(e){
		//prevent default functionality
		e.preventDefault();
		//show the file upload box
		$("#fileUpload").slideToggle();
	});
	$("#calcDelEc").click(function(e){
		//prevent default functionality
		e.preventDefault();
		//calculate delEc to 0th order accuracy, as explained in thesis document
		var xn=parseFloat($("input[name=xn]").val());
		var xp=parseFloat($("input[name=xp]").val());
		var delEc=Math.abs((4.9-0.83*xn)-(4.9-0.83*xp));
		//abs((4.9-0.83*$xn)-(4.9-0.83*$xp))
		$("input[name=delEc]").val(delEc.toPrecision(4));
	})
	$("#abstract").hide();
	$("#showAbstract").click(function(e){
		//prevent default
		e.preventDefault();
		//show the abstract
		$("#abstract").slideToggle();
	})
	$("#showPageLink").click(function(e){
		e.preventDefault();
		$("#pageLink").slideToggle();
		$("#input[name=linkToShare]").select();
	})
	/*$("#zoom").click(function(){
		$.plot($("#j-vs-v"), plotData, options);
	});*/
});