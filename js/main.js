var peopleids,pepid;
var mode = "show";
$(document).ready(function(){
	peopleids = new Array();
	for(var i=0;i<people.length;i++){
		peopleids.push(people[i].id);
	}

	$('#addFormC,#addFormC2').draggable().hide();
	$('#add').off().on('click',function(){
		$('#addFormC').show();
		setTimeout(function(){
			$('#name').focus();
		},50);
	})
	$('#del').off().on('click',delContact);
	$('#close').off().on('click',function(){
		$('#addFormC').hide();
	})
	$('#close2').off().on('click',function(){
		$('#addFormC2').hide();
	})
	$('.person').on('click',info);
	$('#addFormC2 input,#addFormC2 textarea,#addFormC2 #send2').hide();
	$('#edit').off().on('click',switchMode);
	
	$(document).keydown(function(evt) {
		if((evt.ctrlKey || evt.metaKey) && evt.which == 70) {
			evt.preventDefault();
			$('#search').focus();
			return false;
		}
	});
});

function switchMode(){
	if(mode=='show'){
		mode = 'edit';
		$('#addFormC2 input,#addFormC2 textarea,#addFormC2 #send2').show();
		$('#addFormC2 .input,#addFormC2 .textarea').hide();
		$('#edit').html('NO EDITAR');
		$('#addFormC2').removeClass('noedit').addClass('edit');
	} else {
		mode = 'show';
		$('#addFormC2 input,#addFormC2 textarea,#addFormC2 #send2').hide();
		$('#addFormC2 .input,#addFormC2 .textarea').show();
		$('#edit').html('EDITAR');
		$('#addFormC2').removeClass('edit').addClass('noedit');
	}
	$('div[data-id="'+pepid+'"]').trigger('click');
}
function info(){
	pepid = $(this).data('id');
	var person = people[peopleids.indexOf(pepid)];
	$('#name2').val(person.name);
	$('#lastname2').val(person.apedillos);
	$('#phones2').val(person.tfnos);
	$('#addresses2').val(person.addresses);
	$('#emails2').val(person.emails);
	$('#webs2').val(person.webs);
	$('#notes2').val(person.notas);
	
	$('#name2d').html(person.name);
	$('#lastname2d').html(person.apedillos);
	
	var i, regex; 
	var text = person.tfnos.split('\n').join('<br/>');
	for(i=0;i<phoneRegexes.length;i++){
		regex = phoneRegexes[i];
		if(regex.test(text)){
			text = text.replace(regex, "<a href=\"tel:$&\">$&</a>");
			break;
		}
	}
	$('#phones2d').html(text).removeClass('empty');
	if(text==''&&mode=='show'){
		$('#phones2d,label[for=phones2]').removeClass('empty').addClass('empty');
	} else {
		$("label[for=phones2]").removeClass('empty');
	}
	
	$('#addresses2d').html(person.addresses.split('\n').join('<br/>')).removeClass('empty');;
	if(person.addresses==''&&mode=='show'){
		$('#addresses2d,label[for=addresses2]').removeClass('empty').addClass('empty');
	} else {
		$("label[for=addresses2]").removeClass('empty');
	}
	
	text = person.emails.split('\n').join('<br/>');//.toLowerCase();
	text = text.replace(/([a-zA-Z0-9._-]+@[a-zA-Z0-9._-]+\.[a-zA-Z0-9._-]+)/gi,'<a href=mailto:$1>$1</a>');
	$('#emails2d').html(text).removeClass('empty');
	if(text==''&&mode=='show'){
		$('#emails2d,label[for=emails2]').removeClass('empty').addClass('empty');
	} else {
		$("label[for=emails2]").removeClass('empty');
	}

	regex = /^(?:(?:https?|ftp):\/\/)(?:\S+(?::\S*)?@)?(?:(?!(?:10|127)(?:\.\d{1,3}){3})(?!(?:169\.254|192\.168)(?:\.\d{1,3}){2})(?!172\.(?:1[6-9]|2\d|3[0-1])(?:\.\d{1,3}){2})(?:[1-9]\d?|1\d\d|2[01]\d|22[0-3])(?:\.(?:1?\d{1,2}|2[0-4]\d|25[0-5])){2}(?:\.(?:[1-9]\d?|1\d\d|2[0-4]\d|25[0-4]))|(?:(?:[a-z\u00a1-\uffff0-9]-*)*[a-z\u00a1-\uffff0-9]+)(?:\.(?:[a-z\u00a1-\uffff0-9]-*)*[a-z\u00a1-\uffff0-9]+)*(?:\.(?:[a-z\u00a1-\uffff]{2,}))\.?)(?::\d{2,5})?(?:[/?#]\S*)?$/i;
	text = person.webs.split('\n').join('<br/>');
	text = text.replace(regex, "<a href=\"$&\">$&</a>");
	$('#webs2d').html(text).removeClass('empty');;
	if(text==''&&mode=='show'){
		$('#webs2d,label[for=webs2]').removeClass('empty').addClass('empty');
	} else {
		$("label[for=webs2]").removeClass('empty');
	}
	
	text = person.notas.split('\n').join('<br/>');
	text = text.replace(regex, "<a href=\"$&\">$&</a>"); // match urls
	regex = /^(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/g;
	text = text.replace(regex, "<a href=\"mailto:$&\">$&</a>"); // match emails

	for(i=0;i<phoneRegexes.length;i++){// match phone numbers
		regex = phoneRegexes[i];
		if(regex.test(text)){
			text = text.replace(regex, "<a href=\"tel:$&\">$&</a>");
			break;
		}
	}
	$('#notes2d').html(text).removeClass('empty');;
	if(text==''&&mode=='show'){
		$('#notes2d,label[for=notes2]').removeClass('empty').addClass('empty');
	} else {
		$("label[for=notes2]").removeClass('empty');
	}

	$('#uid').val(person.id);
	var isVisible = $('#addFormC2').css('display') != 'none';
	if(!isVisible) $('#addFormC2').css({display:'block',opacity:.01});
	$('#nedit').html('#addFormC2:after {height: '+(Math.round($('#addFormC2').height()) + 20)+'px;');
	if(!isVisible)$('#addFormC2').css({display:'none',opacity:1});
	$('#addFormC2').fadeIn();
}
function stripQuotes(txt){
	txt = txt.split("'").join("’");
	var doContinue = true;
	while(doContinue){
		var olds = txt;
		txt = txt.replace('"','“');
		txt = txt.replace('"','”');
		if(txt===olds)doContinue=false;
	}
	return txt;
}
function updateContact(){ // onsubmit
	addContact('isUpdate');
	return false;
}
var isUpdate, upid;
function addContact(a){
	isUpdate = (a=='isUpdate');
	upid = $('#uid').val() * 1;
	var addon = isUpdate ? '2' : '';
	var name = stripQuotes($('#name'+addon).val());
	var lastname = stripQuotes($('#lastname'+addon).val());
	var phones = stripQuotes($('#phones'+addon).val());
	var addresses = stripQuotes($('#addresses'+addon).val());
	var emails = stripQuotes($('#emails'+addon).val());
	var webs = stripQuotes($('#webs'+addon).val());
	var notes = stripQuotes($('#notes'+addon).val());
	
	$('#send'+addon).attr('disabled',true);
	$.ajax({
		url: "func.php",
		method: "POST",
		data: {
			method:'addContact',
			name:name,
			lastname:lastname,
			phones:phones,
			addresses:addresses,
			emails:emails,
			webs:webs,
			notes:notes,
			update:isUpdate?'yes':'no',
			uid:isUpdate?upid:'no'
		},
		success:function(response) {
			if(response&&response.indexOf('KO')!=0){
	console.log(emails)
				if(isUpdate){
					people[peopleids.indexOf(upid)] = {
						name:name,
						apedillos:lastname,
						tfnos:phones,
						emails:emails,
						addresses:addresses,
						webs:webs,
						notas:notes
					}
					console.log(people[peopleids.indexOf(upid)])
					$('<div class="check">&check;</div>').css({position:'absolute',top:$('#send2').position().top-60,left:$('#send2').position().left}).appendTo('#addFormC2').animate({top:'-=200',opacity:0},500,function(){$('.check').remove()})
					$('div[data-id="'+upid+'"]').trigger('click');
					$('#send'+addon).attr('disabled',false);
					return false;
				} else {
					var id = response * 1;
					people.push({id: id, name: name, apedillos: lastname, tfnos: phones, emails: emails, addresses:addresses,webs:webs,notas:notes});
					peopleids.push(id);
					$('<div class="person" data-id="'+id+'">	<div class="pname">'+name+' '+lastname+'</div></div>').on('click',info).appendTo('#gentes');
					$('#addFormC').hide();
					document.getElementById('addForm').reset();
				}
			} else {
				alert('ERROR:\n\n'+response.toString().substring(2,1000));
			}
			$('#send'+addon).attr('disabled',false);
		},
		error:function(e) {
			alert('ERROR\n'+e.responseText);
			$('#send'+addon).attr('disabled',false);
		}
	});
	return false;
}
function delContact(){
	if(!confirm(DELETE_CONFIRM)) return;
	$('#send2,#del').attr('disabled',true);
	$.ajax({
		url: "func.php",
		method: "POST",
		data: {
			method:'delContact',
			uid:$('#uid').val()
		},
		success:function(response) {
			if(response&&response.indexOf('KO')!=0){
				window.location.reload();
			} else {
				alert('ERROR:\n\n'+response.toString().substring(2,1000));
			}
			$('#send2,#del').attr('disabled',false);
		},
		error:function(e) {
			alert('ERROR\n'+e.responseText);
			$('#send2,#del').attr('disabled',false);
		}
	});
	return false;
}