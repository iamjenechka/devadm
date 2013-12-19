
// Contributed :)

function getXmlHttp() {
	var xmlhttp;
	try {
		xmlhttp = new ActiveXObject("Msxml2.XMLHTTP");
	} catch (e) {
		try {
			xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
		} catch (E) {
			xmlhttp = false;
		}
	}
	if(!xmlhttp && typeof XMLHttpRequest!="undefined") {
		xmlhttp = new XMLHttpRequest();
	}
	return xmlhttp;
}

// EndOf contributed

function cyclebar(el) {
	var cbar=document.createElement('div');
	cbar.setAttribute('id', 'cyclebar_'+el.id);
	cbar.style.width = 31;
	cbar.style.height = 31;
	cbar.style.backgroundImage = 'url(/template/default/images/progressbar_small.gif)';
	cbar.style.backgroundRepeat = 'no-repeat';
	cbar.style.position = 'fixed';
	var xy=findPos(el);
	cbar.style.top  = xy[1] + el.offsetHeight/2 - 16;
	cbar.style.left = xy[0] + el.offsetWidth/2  - 16;
	cbar.style.zIndex = 50;
	document.body.appendChild(cbar);
	return cbar;
}
function removecyclebar(cbar) {
	document.body.removeChild(cbar);
	return;
}
function ajaxcall(ajaxmask, elid, post, funct, path) {
	var xmlhttp = getXmlHttp();
	var el = document.getElementById(elid);
	var cbar = cyclebar(el);
	if(typeof(path) == 'undefined') {
		path=window.location.pathname;
	}
	xmlhttp.open('POST', path, true);
	xmlhttp.setRequestHeader("Content-Type","application/x-www-form-urlencoded");
	xmlhttp.onreadystatechange = function() {
		if(xmlhttp.readyState == 4) {
			if(xmlhttp.status == 200) {
				funct(xmlhttp);
			} else {
				alert('Communication error. Try once more.');
			}
			removecyclebar(cbar);
			//updateGeom();
			delete xmlhttp;
		}
	}
	xmlhttp.send(post+'&ajax='+ajaxmask);
	return false;
}
function ajax_notify(str) {
	alert(str);
	return;
}

function ajax_getoutputelement() {
	return document.getElementById("ajaxblocks");
}

function ajax_clear() {
	var ajaxout=ajax_getoutputelement();
	ajaxout.innerHTML='';
	return false;
}

