document.write('<link rel="stylesheet" type="text/css" href="modules/Web_tv/web_tv_v1.css">');

$(document).ready( function () {
    	$("ul.subMenu:not('.this_day')").hide();
    	$("li.toggleTv span").each( function () {
        	var TexteSpan = $(this).text();
        	$(this).replaceWith('<a href="javascript:void(0);" title="Afficher le programme">' + TexteSpan + '</a>') ;
    	});
    	$("li.toggleTv > a").click( function () {
        	if ($(this).next("ul.subMenu:visible").length != 0) {
            		$(this).next("ul.subMenu").slideUp("normal", function () {
            			$(this).parent().removeClass("open");
            		});
        	} else {
            		$("ul.subMenu").slideUp("normal", function () {
            			$(this).parent().removeClass("open");
            		});
            		$(this).next("ul.subMenu").slideDown("normal", function () {
            			$(this).parent().addClass("open");
            		});
        	}
        	return false;
    	});
});


function toggle_programme_tv(etat) {	if(etat == 'show') {		//$("#programme_du_jour").show();
		document.getElementById('programme_du_jour').style.display = 'table';
		document.getElementById('programme_du_jour').style.width = '90%';
		document.getElementById('p_off').style.display = 'inline';
		document.getElementById('p_on').style.display = 'none';
	} else {
		//$("#programme_du_jour").hide();
		document.getElementById('programme_du_jour').style.display = 'none';
		document.getElementById('p_on').style.display = 'inline';
		document.getElementById('p_off').style.display = 'none';
	}
}

function getXhr(){var f=null;if(window.XMLHttpRequest){f=new XMLHttpRequest()}else{if(window.ActiveXObject){try{f=new ActiveXObject("Msxml2.XMLHTTP")}catch(c){f=new ActiveXObject("Microsoft.XMLHTTP")}}else{alert("Votre navigateur ne supporte pas les objets XMLHTTPRequest...");f=false}}return f}

function check_url() {
     	var xhr = getXhr();
     	xhr.onreadystatechange = function() {
          	if(xhr.readyState == 4) {
	        	if(xhr.status == 200) {
	            		leselect = xhr.responseText;
		      		document.getElementById('check_url').innerHTML = leselect;
	        	} else {
		      		document.getElementById('check_url').innerHTML = "Erreur !"
	        	}
	   	} else {
	       		document.getElementById('check_url').innerHTML = "<img src='modules/Web_tv/images/loading.gif' alt=''/>"
	   	}
     	}
     	xhr.open("POST","index.php?file=Web_tv&page=admin&nuked_nude=admin&op=check_url",true);
     	xhr.setRequestHeader('Content-Type','application/x-www-form-urlencoded');
     	var idurl  = document.getElementById('url').value;
        a = document.getElementById('type');
     	var idtype = a.options[a.selectedIndex].value;
     	xhr.send("url="+encodeURIComponent(idurl)+"&type="+idtype+"");
}

function historique_programme_tv(id_tv,page) {	var xhr = getXhr();
     	xhr.onreadystatechange = function() {
          	if(xhr.readyState == 4) {
	        	if(xhr.status == 200) {
	            		leselect = xhr.responseText;
		      		document.getElementById('hpt').innerHTML = leselect;
	        	} else {
		      		document.getElementById('hpt').innerHTML = "Erreur !"
	        	}
	   	} else {
	       		document.getElementById('hpt').innerHTML = "<img src='modules/Web_tv/images/loading.gif' alt=''/>"
	   	}
     	}
        xhr.open("GET","index.php?file=Web_tv&nuked_nude=index&op=historique_programme&id="+id_tv+"&p="+page,true);
        xhr.send(null)}