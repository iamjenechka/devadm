
function ajax_getmenu_taskact_res(xmlhttp) {
	if(xmlhttp.status == 200) {
		var ajaxout=ajax_getoutputelement();
		ajaxout.innerHTML=xmlhttp.responseText;
	} else {
		ajax_notify('Communication error. Try once more.');
	}
}
function ajax_getmenu_taskadd() {
	var post = 'cmd=task_addrequest';
	return ajaxcall(AJAX_MIDCMD, "controlstable", post, ajax_getmenu_taskact_res);
}
function ajax_getmenu_taskedit(task_id) {
	var post = 'cmd=task_editrequest&ID='+task_id;
	return ajaxcall(AJAX_MIDCMD, "issueslist", post, ajax_getmenu_taskact_res);
}
function ajax_getmenu_subtaskadd(task_id) {
	var post = 'cmd=task_addsubtaskrequest&PARENT='+task_id;
	return ajaxcall(AJAX_MIDCMD, "issueslist", post, ajax_getmenu_taskact_res);
}
function ajax_getmenu_logwork(task_id) {
	var post = 'cmd=task_logworkrequest&ID='+task_id;
	return ajaxcall(AJAX_MIDCMD, "issueslist", post, ajax_getmenu_taskact_res);
}

function ajax_task_logwork_res(xmlhttp) {
	if(xmlhttp.status == 200) {
		var answer=unserialize(xmlhttp.responseText);
		switch(answer['result']) {
			case 'OK':
				var ajaxout=document.getElementById("issueslist");
				ajaxout.innerHTML=Base64.decode(answer['tasks']);
				ajax_clear();
				break;
			case 'ERR':
				ajax_notify('Error: '+answer['errmsg']);
				break;
		}
	} else {
		ajax_notify('Communication error. Try once more.');
	}
}

function ajax_task_logwork(form) {
	var post = 'cmd=task_logwork&ID='+form.elements['ID'].value+'&HUMANHOURS_add='+form.elements['HUMANHOURS_add'].value+'&issuestatus='+form.elements['issuestatus'].value+'&RESOLUTION='+form.elements['RESOLUTION'].value;
	return ajaxcall(AJAX_MIDCMD, "issueslist", post, ajax_task_logwork_res)
}

function ajax_show_tasks_res(xmlhttp) {
	if(xmlhttp.status == 200) {
		var ajaxout=document.getElementById("issueslist");
		ajaxout.innerHTML=xmlhttp.responseText;
	} else {
		ajax_notify('Communication error. Try once more.');
	}
}

function ajax_switch_page_res(xmlhttp) {
	if(xmlhttp.status == 200) {
		var answer=unserialize(xmlhttp.responseText);
		switch(answer['result']) {
			case 'OK':
				var ajaxout=document.getElementById("issueslist");
				ajaxout.innerHTML=Base64.decode(answer['tasks']);
				if(answer['oldpagenum'] != answer['pagenum']) {
					var buttons=document.getElementsByClassName('pageswitchsubmit'+answer['oldpagenum']);
					for (var i = 0; (button = buttons[i]) != null; i++) {
						button.disabled=false;
					}
					var buttons=document.getElementsByClassName('pageswitchsubmit'+answer['pagenum']);
					for (var i = 0; (button = buttons[i]) != null; i++) {
						button.disabled=true;
					}
				}
				break;
			case 'ERR':
				ajax_notify('Error: '+answer['errmsg']);
				break;
		}
	} else {
		ajax_notify('Communication error. Try once more.');
	}
	
}

function ajax_switch_page(page) {
	var post = 'cmd=switchpage&page='+page;
	return ajaxcall(AJAX_PRECMD|AJAX_MIDCMD, "issueslist", post, ajax_switch_page_res);
}


