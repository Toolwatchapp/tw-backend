oPlugin = {
	PluginWidth:80,
	PluginHeight:1,
	SlideBig:'Small',
	LabelBgColor:'#f7f7f7',
	BgColor:'#fefefe',
	Color:'#6A6A6A',
	FontSize:'11',
	LinkColor:'#587cc8',
	Timer:null,
	LastBox:null,
	Browser: {
		Explorer: !!(navigator.appName=='Microsoft Internet Explorer'),
		Other:  !!!(navigator.appName=='Microsoft Internet Explorer'),
		Chrome:  !!(navigator.userAgent.toLowerCase().indexOf('chrome') > -1)
	},
	
boxDialog: function(sId){

	if(document.getElementById('content_'+sId).style.display=='block'){
	
		if(oPlugin.LastBox){
		
			if(document.getElementById('boxDialog_'+oPlugin.LastBox)){
			
					jQuery('#boxDialog_'+oPlugin.LastBox).hide('slow');
					document.getElementById('boxDialog_'+oPlugin.LastBox).style.zIndex=0;
			}	
			
			jQuery('#content_'+oPlugin.LastBox).show('fast');
				
				
			document.getElementById('arrow_'+oPlugin.LastBox).src=jQuery("#skyscraper_dir").val()+'images/toolbar/arrow.png';		
		}
		if(document.getElementById('boxDialog_'+sId)){
		
			jQuery('#boxDialog_'+sId).show('slow');
			document.getElementById('boxDialog_'+sId).style.zIndex=10;	
		}	
		
		jQuery('#content_'+sId).hide('fast');
		oPlugin.LastBox=sId;
		
		document.getElementById('arrow_'+sId).src=jQuery("#skyscraper_dir").val()+'images/toolbar/arrow_s.png';
		
	}else{
		
		oPlugin.LastBox=null;
		
		if(document.getElementById('boxDialog_'+sId)){
		
			jQuery('#boxDialog_'+sId).hide('slow');
		}	
	
		
		jQuery('#content_'+sId).show('fast');
		
		document.getElementById('arrow_'+sId).src=jQuery("#skyscraper_dir").val()+'images/toolbar/arrow.png';
	}	
	
},
boxDialogShow: function(sId){
	if(document.getElementById('content_'+sId).style.display=='block'){
		if(oPlugin.LastBox){
			if(document.getElementById('boxDialog_'+oPlugin.LastBox)){
						jQuery('#boxDialog_'+oPlugin.LastBox).hide('slow');
						document.getElementById('boxDialog_'+oPlugin.LastBox).style.zIndex=0;
			}	
			
					jQuery('#content_'+oPlugin.LastBox).show('fast');
				
			
				document.getElementById('arrow_'+oPlugin.LastBox).src=jQuery("#skyscraper_dir").val()+'images/toolbar/arrow.png';		
		}
		if(document.getElementById('boxDialog_'+sId)){
			jQuery('#boxDialog_'+sId).show('slow');
			document.getElementById('boxDialog_'+sId).style.zIndex=10;	
		}	
		jQuery('#content_'+sId).hide('fast');
		oPlugin.LastBox=sId;
		
		document.getElementById('arrow_'+sId).src=jQuery("#skyscraper_dir").val()+'images/toolbar/arrow_s.png';
	}	
	
},
hideShowToolbar: function(){
	if(document.getElementById('wpPlugin-MainFrame').style.display=='none'){
		jQuery('#wpPlugin-MainFrame').show(1500);
		document.getElementById('wpSocciable').className= 'wpSocciable'+oPlugin.SlideBig+'';			document.getElementById('wpSocciable').style.backgroundImage="url("+jQuery("#skyscraper_dir").val()+"images/toolbar/slide-sociable.png)";
		document.getElementById('wpSocciableClose').className= 'wpSocciableClose';		document.getElementById('wpSocciableClose').style.backgroundImage="url("+jQuery("#skyscraper_dir").val()+"images/toolbar/slide-close.png)";
			document.getElementById('wpSocciable').style.marginLeft = '-20px';
			document.getElementById('wpSocciableClose').style.marginLeft = '-20px';
			
	}else{
	
				document.getElementById('wpPlugin-MainFrame').style.display='none';
				document.getElementById('wpSocciable').className= 'wpSocciable'+oPlugin.SlideBig+'-c';				document.getElementById('wpSocciable').style.backgroundImage="url("+jQuery("#skyscraper_dir").val()+"images/toolbar/slide-sociable-c.png)";
				
					document.getElementById('wpSocciableClose').className= 'wpSocciableClose-c';										document.getElementById('wpSocciableClose').style.backgroundImage="url("+jQuery("#skyscraper_dir").val()+"images/toolbar/slide-close-c.png)";
				
				
	}
},
focus:function(obj){
	obj.style.opacity='1';
	obj.style.filter="alpha(opacity='100')";
},
blur:function(obj){
	obj.style.opacity='0.4';
	obj.style.filter="alpha(opacity='40')";
	
},
refreshPosition: function(){
		
		var scrolltop = jQuery('html,body').scrollTop();
		
		if (oPlugin.Browser.Chrome){
			
			scrolltop = document.body.scrollTop;
		}
		
		
		 var div = jQuery('#wpPlugin-BOX');
		var start = jQuery(div).offset().top;
	 
		
			var p = jQuery(window).scrollTop();
			jQuery(div).css('position','fixed');
			

		
		//document.getElementById('wpPlugin-BOX').style.top = (scrolltop+40) + 'px'; 
		/*if(parseInt(document.getElementById('wpPlugin-BOX').style.top.replace('px',''))!=(scrolltop+40)){
			document.getElementById('wpPlugin-BOX').style.display='none';
		
			document.getElementById('wpPlugin-BOX').style.top = (scrolltop+40) + 'px'; 
			
			if(parseInt(document.getElementById('wpPlugin-BOX').style.top.replace('px',''))>(scrolltop+40)){
				document.getElementById('wpPlugin-BOX').style.top = (parseInt(document.getElementById('wpPlugin-BOX').style.top.replace('px',''))-1) + 'px';
			}else{
				document.getElementById('wpPlugin-BOX').style.top = (parseInt(document.getElementById('wpPlugin-BOX').style.top.replace('px',''))+1) + 'px';			
			}	
			
			document.getElementById('wpPlugin-BOX').style.display='';			
		}else{
			clearInterval(oPlugin.Timer);
			oPlugin.Timer=null;
		}
		*/
},
toolbarStart: function(sID_Parent,sMove,sHeight,sWidth,sBgColor,sLabelBgColor,sSlideBig,sColor,sFontSize,sLinkColor){
					
				
		if(sLinkColor){
			oPlugin.LinkColor = sLinkColor;		
		}
		if(sFontSize){
			oPlugin.FontSize = sFontSize;
		}		
				
		if(sColor){
			oPlugin.Color = sColor;		
		}
		if(sBgColor){
			oPlugin.BgColor = sBgColor;		
		}
		if(sSlideBig){
			oPlugin.SlideBig = "Big";
		}//If Null == Small!
		
		if(sLabelBgColor){
		oPlugin.LabelBgColor = sLabelBgColor;
		}
		if(sWidth){
		oPlugin.PluginWidth = sWidth;
		
		}
		if(sHeight){
			oPlugin.PluginHeight = sHeight;
		
		}
		
		if(sMove){
			window.onscroll = function (e) {
				if(!oPlugin.Timer){
			
					oPlugin.Timer = setInterval("oPlugin.refreshPosition();", 50);			
				}
			}
		}			
			jQuery(document).ready(function(){
			
			document.getElementById('wpPlugin-BOX').style.marginLeft =  (21 - jQuery("body").css("padding-left").replace('px','')) + 'px'; 
		
			});
				
		var Base =
		'<div align="center" class="wpPlugin-MainFrame" id="wpPlugin-BOX"  style="width:'+(oPlugin.PluginWidth+2)+'px;position:absolute;margin-top:40px;display:block;height:'+oPlugin.PluginHeight+'px;" >'+		
		'<a href="http://blogplay.com" target="_blank" ><div id="wpSocciable" class="wpSocciable'+oPlugin.SlideBig+'" ></div></a>'+
		'<div id="wpSocciableClose" class="wpSocciableClose"   onclick="oPlugin.hideShowToolbar();"></div>'+
		'<table id="wpPlugin-MainFrame"  class="shape" cellspacing="0" cellpadding="0"  width="100%">'+
		'<tr>'+
			'<td class="top-left" /><td class="top" /><td class="top-right" />'+
		'</tr>'+
		'<tr valign="top"  >'+
			'<td class="left" />'+
			'<td align="left" class="frame" style="background-color:'+oPlugin.BgColor+';width:'+(oPlugin.PluginWidth-7+7)+'px;" >'+
				'<ul class="toolbar" style="color:'+oPlugin.Color+';font-size:'+oPlugin.FontSize+'px;width:'+(oPlugin.PluginWidth-7+7)+'px;" id="toolbar" >'+
				'</ul>'+
			'</td>'+
			'<td class="right" />'+
		'</tr>'+
		'<tr>'+
			'<td class="bottom-left" /><td class="bottom" /><td class="bottom-right" />'+
		'</tr>'+
		'</table></div>';
		
		document.getElementById(sID_Parent).innerHTML = Base + document.getElementById(sID_Parent).innerHTML;
		
					oPlugin.InitializeShowObj = 'wpPlugin-MainFrame';
					document.getElementById(oPlugin.InitializeShowObj).style.display='none';
					oPlugin.InitializeShow = setInterval("jQuery('#'+oPlugin.InitializeShowObj).show(3000);clearInterval(oPlugin.InitializeShow);", 1000);					
					//oPlugin.blur(document.getElementById(oPlugin.InitializeShowObj));
					
					 //onmouseover="oPlugin.focus(this);"   onmouseout="oPlugin.blur(this);"  onmouseover"this.style.opacity=1;" onmouseout"this.style.opacity=0.4;"
				/*if(!oPlugin.Browser.Explorer){
					
					document.getElementById(sID_Parent).style.display='none';
					oPlugin.InitializeShowObj = sID_Parent;
					oPlugin.InitializeShow = setInterval("jQuery('#'+oPlugin.InitializeShowObj).show(3000);clearInterval(oPlugin.InitializeShow);", 1000);			
					
					
				}else{
					document.getElementById('wpPlugin-MainFrame').style.filter='alpha(opacity=40)';
				
				}*/
			
		document.getElementById('wpPlugin-BOX').style.top = '20px';		document.getElementById('wpSocciable').style.backgroundImage="url("+jQuery("#skyscraper_dir").val()+"images/toolbar/slide-sociable.png)";						document.getElementById('wpSocciableClose').style.backgroundImage="url("+jQuery("#skyscraper_dir").val()+"images/toolbar/slide-close.png)";
		
},
 CreateGoToTop:function(sId,sTitle,sContent){
 
		oLi = oPlugin.createElement('li',sId,'item',null,'',null);
		
		
		
		var oLi_Functions;
		
					oLi_Functions = Array();
					oLi_Functions[0]= new Array();
					oLi_Functions[0][0] = 'click';
					oLi_Functions[0][1] = function(){ 
						//	var scrolltop = jQuery('html,body').scrollTop();
							
							if (oPlugin.Browser.Chrome){
								
								document.body.scrollTop = 0;
							}
							else{
								jQuery('html,body').scrollTop(0);
							}
							
							};
		
			oLabel  = oPlugin.createElement('div','title_' +sId,'title',oLi_Functions,'<img id="arrow_'+sId+'" src="'+jQuery("#skyscraper_dir").val()+'images/toolbar/arrow.png" /><span>'+sTitle+'</span>',null);
		oLabel.style.background=oPlugin.LabelBgColor;
		oLi.appendChild(oLabel);
		
		oLi_Content = oPlugin.createElement('div','content_' +sId,'content',oLi_Functions,sContent,null);
		if(sContent==''){oLi_Content.style.lineHeight='1px';oLi_Content.style.height='1px';oLi_Content.innerHTML='';}
			oLi_Content.align='center';
			oLi_Content.style.display='block';
			oLi_Content.style.cursor='pointer';
		oLi.appendChild(oLi_Content);
		
		document.getElementById('toolbar').appendChild(oLi);
		
 },
 
 CreateSimpleNode:function(sId,sTitle,sContent,display){
 
		oLi = oPlugin.createElement('li',sId,'item',null,'',null);
		
		
		
		var oLi_Functions;
		
					oLi_Functions = Array();
					oLi_Functions[0]= new Array();
					oLi_Functions[0][0] = 'click';
					oLi_Functions[0][1] = function(){ 
								if(document.getElementById('content_'+sId).style.display=='none'){
									
									document.getElementById('content_'+sId).style.display='block';
									document.getElementById('arrow_'+sId).src=jQuery("#skyscraper_dir").val()+'images/toolbar/arrow.png';
								
								}else{
									document.getElementById('content_'+sId).style.display='none';
									document.getElementById('arrow_'+sId).src=jQuery("#skyscraper_dir").val()+'images/toolbar/arrow_s.png';
								}
							};
		
		if(display){
				arrow='';
			}else{
				arrow='_s';
			}
		oLabel  = oPlugin.createElement('div','title_' +sId,'title',oLi_Functions,'<img id="arrow_'+sId+'" src="'+jQuery("#skyscraper_dir").val()+'images/toolbar/arrow'+arrow+'.png" /><span>'+sTitle+'</span>',null);
		oLabel.style.background=oPlugin.LabelBgColor;
		oLi.appendChild(oLabel);
		
		oLi_Content = oPlugin.createElement('div','content_' +sId,'content',oLi_Functions,sContent,null);
		if(sContent==''){oLi_Content.style.lineHeight='1px';oLi_Content.style.height='1px';oLi_Content.innerHTML='';}
			
			
			oLi_Content.align='center';
			if(display){
				oLi_Content.style.display='block';
			}else{
				oLi_Content.style.display='none';
			}
		oLi.appendChild(oLi_Content);
		
		document.getElementById('toolbar').appendChild(oLi);
		
 },
 
 CreateGoToHome:function(sId,sTitle,sContent){
 
		oLi = oPlugin.createElement('li',sId,'item',null,'',null);
		
		
		
		var oLi_Functions;
		
					oLi_Functions = Array();
					oLi_Functions[0]= new Array();
					oLi_Functions[0][0] = 'click';
					oLi_Functions[0][1] = function(){document.location.href ='http://'+ document.location.href.split("/")[2];};
		
			oLabel  = oPlugin.createElement('div','title_' +sId,'title',oLi_Functions,'<img id="arrow_'+sId+'" src="'+jQuery("#skyscraper_dir").val()+'images/toolbar/arrow.png" /><span>'+sTitle+'</span>',null);
		oLabel.style.background=oPlugin.LabelBgColor;
		oLi.appendChild(oLabel);
		
		oLi_Content = oPlugin.createElement('div','content_' +sId,'content',oLi_Functions,sContent,null);
		if(sContent==''){oLi_Content.style.lineHeight='1px';oLi_Content.style.height='1px';oLi_Content.innerHTML='';}
			oLi_Content.align='center';
			oLi_Content.style.display='block';
			oLi_Content.style.cursor='pointer';
		oLi.appendChild(oLi_Content);
		
		document.getElementById('toolbar').appendChild(oLi);
		
 },
 CreateNode: function(sId,sTitle,sContent,sContentDialog,sContentDialogStyle,dialogHeight,dialogWidth){

	oLi = oPlugin.createElement('li',sId,'item',null,'',null);
		
	var oLi_Functions;
	
	oLi_Functions = Array();
	oLi_Functions[0]= new Array();
			
	if (sContentDialogStyle == "banner"){
	
		oLi_Functions[0][0] = '';
		oLi_Functions[0][1] = '';
		
		oLabel  = oPlugin.createElement('div','title_' +sId,'title',oLi_Functions,'<img style="display:none" id="arrow_'+sId+'" src="" />',null);		
		oLabel.style.display = 'none';
	}
	else{
		
		oLi_Functions[0][0] = 'click';
		oLi_Functions[0][1] = function(){oPlugin.boxDialog(sId);};
		
		oLabel  = oPlugin.createElement('div','title_' +sId,'title',oLi_Functions,'<img id="arrow_'+sId+'" src="'+jQuery("#skyscraper_dir").val()+'images/toolbar/arrow.png" /><span>'+sTitle+'</span>',null);
		oLabel.style.background = oPlugin.LabelBgColor;
	}
	
	
	oLi.appendChild(oLabel);
		
	
	if(sContentDialog){
		var oLi_BoxShape = '<table class="shape" cellspacing="0" cellpadding="0" height="'+(dialogHeight)+'" width="100%"  >'+
							'<tr>'+
								'<td class="top-left" /><td class="top" /><td class="top-right" />'+
							'</tr>'+
							'<tr valign="top">'+
								'<td class="left"  style="font-size:10px;" >&nbsp;</td>'+
								'<td align="left"  style="background-color:'+oPlugin.BgColor+';width:'+(dialogWidth+50)+'px;" class="frame"  >';
		oLi_BoxShape += '';
		oLi_BoxShape += '<span style="color:'+oPlugin.Color+';position:absolute;margin-left:';
		
		if(oPlugin.Browser.Explorer){
					oLi_BoxShape += (oPlugin.PluginWidth+dialogWidth-oPlugin.PluginWidth-12)+'px;';
				}else{
					oLi_BoxShape += (oPlugin.PluginWidth+dialogWidth-oPlugin.PluginWidth)+'px;';
				}
		
		
		oLi_BoxShape += 'margin-top:5px;font-size:11px;font-weight:bold;cursor:pointer;border:solid 1px '+oPlugin.Color+';padding:2px;line-height:9px;padding-top:0px;padding-bottom:2px;" onclick="oPlugin.boxDialog(\''+sId+'\')" >x</span>';
 		oLi_BoxShape += '<div class="boxTitleContent" style="font-size:'+(oPlugin.FontSize+2)+'px;color:'+oPlugin.Color+';font-weight:bold;background:'+oPlugin.LabelBgColor+';';
		
		if(oPlugin.Browser.Explorer){
			oLi_BoxShape += 'width:'+(dialogWidth+8)+'px;';
		}else{
			oLi_BoxShape += 'width:'+(dialogWidth+13)+'px;';
		}
		
		oLi_BoxShape += '" >&nbsp;&nbsp;&nbsp;&nbsp;'+sTitle+'</div>';
		
		switch(sContentDialogStyle){
		
			case 'List':
			
				oLi_BoxShape += '<div align="left" style="height:'+(dialogHeight)+'px;margin:10px;margin-bottom:0px;padding-bottom:0px;width:100%;float:none;clear:both;" >';
				oLi_BoxShape += '<ul align="left" >';
				for(i=0;i<sContentDialog.length;i++){
					
					oLi_BoxShape += '<li style="position:relative;color:#c1c1c1;margin-left:-20px;margin-bottom:5px;font-size:'+oPlugin.FontSize+'px;padding-bottom:5px;list-style:disc;font-weight:bold;">'; 
					oLi_BoxShape += '<span style="color:'+oPlugin.Color+';" >'+sContentDialog[i][0]+'</span>&nbsp;&nbsp;';
					oLi_BoxShape += '<span style="height:'+(oPlugin.FontSize+2)+'px;color:'+oPlugin.Color+';opacity:0.4;filter: alpha(opacity=40);" >'+sContentDialog[i][1]+'</span>&nbsp;&nbsp;';
					oLi_BoxShape += '<a style="color:'+oPlugin.LinkColor+';" >'+sContentDialog[i][2]+'</a>';
					oLi_BoxShape += '</li>';
			
				}
				oLi_BoxShape += '</ul>';
				oLi_BoxShape += '</div>';
				
			  break;
			  	
  			case 'Urls':
  			
				oLi_BoxShape += '<div align="left" style="height:'+(dialogHeight)+'px;margin:10px;margin-bottom:0px;padding-bottom:0px;width:100%;float:none;clear:both;" >';
				oLi_BoxShape += '<ul align="left" >';
				for(i=0;i<sContentDialog.length;i++){
				
					oLi_BoxShape += '<li style="color:#c1c1c1;margin-left:-20px;margin-bottom:5px;font-size:'+oPlugin.FontSize+'px;padding-bottom:5px;list-style:disc;font-weight:bold;" >'; 
					oLi_BoxShape += '<a href="'+sContentDialog[i]+'" style="color:'+oPlugin.LinkColor+';" >'+sContentDialog[i]+'</a>';
					oLi_BoxShape += '</li>';
				
				}
				oLi_BoxShape += '</ul>';
				oLi_BoxShape += '</div>';
				
			  break;	
			  			  
			case 'Notice':
			
				//oPlugin.Browser.Explorer
				oLi_BoxShape += '<ul  style="margin-top:10px;margin-bottom:10px;margin-left:10px;padding-left:15px;width:'+(dialogWidth-50)+'px;" >';
				for(i=0;i<sContentDialog.length;i++){
				
					oLi_BoxShape += '<li style="font-size:'+oPlugin.FontSize+'px;';
					
					if(i != sContentDialog.length-1){
					
						oLi_BoxShape += 'padding-bottom:10px;border-bottom:solid 1px #c1c1c1;';
					}
					
					oLi_BoxShape +='list-style:disc;font-weight:bold;color:#c1c1c1;">'; 
					oLi_BoxShape += '<a style="color:'+oPlugin.LinkColor+';" >'+sContentDialog[i][0]+'</a> ';
					oLi_BoxShape += '<span style="height:'+(oPlugin.FontSize+2)+'px;color:'+oPlugin.Color+'; filter: alpha(opacity=40);opacity:0.4;" >'+sContentDialog[i][1]+'</span><br/>';
					oLi_BoxShape += '<span style="height:'+(oPlugin.FontSize+2)+'px;color:'+oPlugin.Color+';filter: alpha(opacity=70);opacity:0.7;" >'+sContentDialog[i][2]+'';
					oLi_BoxShape += ' - <a style="color:'+oPlugin.LinkColor+';" href="'+sContentDialog[i][3]+'" >'+sContentDialog[i][3]+'</a></span><br/>';
					oLi_BoxShape += '<span style="height:'+(oPlugin.FontSize+2)+'px;color:'+oPlugin.Color+';opacity:0.4;filter: alpha(opacity=40);" >'+sContentDialog[i][4]+'</span>';
					oLi_BoxShape += '</li>';
				
				}
				oLi_BoxShape += '</ul>';
			break;
			
			case 'Item':
			
				oLi_BoxShape += '<div align="left" style="opacity:0.8;filter: alpha(opacity=80);height:'+(dialogHeight)+'px;margin:10px;margin-bottom:0px;margin-top:5px;padding-bottom:0px;width:100%;float:none;clear:both;" >';
				oLi_BoxShape += '<ul align="left" >';
		
					oLi_BoxShape += '<li style="color:#c1c1c1;margin-left:-20px;font-size:'+oPlugin.FontSize+'px;padding-bottom:5px;list-style:disc;font-weight:bold;" >'; 
					oLi_BoxShape += '<span style="color:'+oPlugin.Color+';" >'+sContentDialog+'</span>';
					oLi_BoxShape += '</li>';
				
			
				oLi_BoxShape += '</ul>';
				oLi_BoxShape += '</div>';
			
			break;	
				
			default:
			
				oLi_BoxShape += '<div align="center" style="height:'+(dialogHeight)+'px;width:100%;" ><div  style="height:'+(dialogHeight)+'px;color:'+oPlugin.Color+';font-size:'+(oPlugin.FontSize-1)+'px;margin:5px;padding:0px;width:95%;float:none;clear:both;" >';
				oLi_BoxShape += sContentDialog ;
				oLi_BoxShape += '</div></div>';
			break;		
		
		}
		
		
		
		oLi_BoxShape += '<td class="right" style="font-size:10px;" >&nbsp;</td>'+
						'</tr>'+
						'<tr>'+
							'<td class="bottom-left" /><td class="bottom" /><td class="bottom-right" />'+
						'</tr>'+
						'</table>';
		
		
		oLi_BoxDialog = oPlugin.createElement('div','boxDialog_' +sId,'boxDialog',null,oLi_BoxShape,null);
		oLi_BoxDialog.align='left';
			
			
		if(dialogWidth){
			oLi_BoxDialog.style.width=(dialogWidth+10)+'px';
		}
		
		if(dialogHeight){
			if(oPlugin.Browser.Explorer){
				oLi_BoxDialog.style.height=(dialogHeight+100)+'px';	
			}else{
				oLi_BoxDialog.style.height=(dialogHeight+10)+'px';	
			}
		}
			
		oLi_BoxDialog.style.display='none';
		
		
		oLi_BoxDialog.style.left=(oPlugin.PluginWidth-7+7)+'px';
		oLi_BoxDialog.style.marginTop='-35px';
			
		oLi.appendChild(oLi_BoxDialog);
	}
	
	
	oLi_Content = oPlugin.createElement('div','content_' +sId,'content',null,sContent,null);
	if(sContent==''){oLi_Content.style.lineHeight='1px';oLi_Content.style.height='1px';oLi_Content.innerHTML='';}
		oLi_Content.align='center';
		oLi_Content.style.display='block';
	oLi.appendChild(oLi_Content);

	
	document.getElementById('toolbar').appendChild(oLi);
},
createElement: function(sType,sId,sClass,aFunctions,sHtml,oParent){

		var oElement;			
		
		oElement=document.createElement(sType);
		oElement.setAttribute('id',sId);
		oElement.className=sClass;			
		
		if(sHtml)
		{						
			oElement.innerHTML=sHtml;
		}
		
		if (aFunctions)
		{
			for (iFunctions=0;iFunctions< aFunctions.length;iFunctions++)
			{															
				if (oPlugin.Browser.Other)
				{												
					oElement.addEventListener(aFunctions[iFunctions][0],eval(aFunctions[iFunctions][1]),false);					
				}
				if (oPlugin.Browser.Explorer)
				{					
					oElement['on' + aFunctions[iFunctions][0]]= aFunctions[iFunctions][1];
				}
			}
		}
		
		if (oParent)
		{
			oParent.appendChild(oElement);
		}else{
			return oElement;
		}
	}
};
function twitter(url,title){
    return '<a href="https://twitter.com/share" data-text="'+ title +' (via http://www.fueto.com)" data-url="'+ url + '" class="twitter-share-button" data-count="horizontal">Tweet</a><scr'+'ipt type="text/javascript" src="//platform.twitter.com/widgets.js"></scr'+'ipt>';
}

function facebook(url){

return '<iframe src="//www.facebook.com/plugins/like.php?href='+url+'&send=false&layout=button_count&show_faces=false&action=like&colorscheme=light&font" scrolling="no" frameborder="0" style="border:none; overflow:hidden;height:32px;width:115px" allowTransparency="true"></iframe>'
}

function plus(url){
    return '<g:plusone annotation="bubble" href="'+url+'" size="medium"></g:plusone>';

} 

function counters(){

  jQuery("span#socialstats").each(  
 
  function(i) {
        var url = jQuery(this).attr("url");
        var title = jQuery(this).attr("title");
        var append = '<table border=0"><tr>';
        append += '<td valign="top">'+twitter(url,title)+'</td>' ;     
  	    append += '<td valign="top">'+plus(url)+'</td>' ;      
        append += '<td valign="baseline">'+facebook(url)+'</td>' ;             
        append += '</tr></table>';
        jQuery(this).html(append);
 
 
	});        
 } 
  
var repeatBanner = 0;  
  
function showBanner(timer, colorBack, colorLabel, colorFont, fontSize){
 	
 	if (repeatBanner < 2){
 	
	 	jQuery('#boxDialog_New_Id_14 .frame').css("background-color", colorBack);
		jQuery('#boxDialog_New_Id_14 .boxTitleContent').css("background-color", colorLabel);
		jQuery('#boxDialog_New_Id_14 .boxTitleContent').css("color", colorFont);
		jQuery('#boxDialog_New_Id_14 .boxTitleContent').css("font-size", fontSize);
	
		oPlugin.boxDialog('New_Id_14');
	 
		setTimeout('showBanner('+timer+')',  timer);
		
		repeatBanner ++;
	}
}
     