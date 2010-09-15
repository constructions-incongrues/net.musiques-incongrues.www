var request;
var form;
var comprev;

function createrequest_post(url, params, func)
{
	request = false;
	if(window.XMLHttpRequest)
	{
		request = new XMLHttpRequest();
		if(request.overrideMimeType) request.overrideMimeType('text/xml');
	}
	else if(window.ActiveXObject)
	{
		try
		{
			request = new ActiveXObject('Msxml2.XMLHTTP');
		}
		catch(error)
		{
			try
			{
				request = new ActiveXObject('Microsoft.XMLHTTP');
			}
			catch(error) {}
		}
	}
	
	if(!request) return false;
	
	request.onreadystatechange = func;
	request.open('POST', url, true);
	request.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
	request.setRequestHeader('Content-length', params.length);
	request.setRequestHeader('Connection', 'close');
	request.send(params);
	
	return true;
}

function getform()
{
	var frm = document.getElementById('frmPostDiscussion');
	if(!frm) frm = document.getElementById('frmPostComment');
	
	return frm;//$('#frmPostDiscussion, #frmPostComment');
}

function _showpreview()
{
	if(request.readyState == 4 && request.status == 200)
	{
		comprev.innerHTML = request.responseText;
		
		getform().btnPreview.value = 'Refresh Preview';
		comprev.parentNode.scrollIntoView(); //scroll to the Comment_Preview li
	}
}

function showpreview(baseurl, user)
{
	var comlist, obj, text, type, i, f, whisperto, astr;
	
	astr = '<a href="' + baseurl + 'account.php?u=' + user.id + '">' + user.name + '</a>';
	
	form = getform();
	if(form)
	{
		//our new 'comment' dosn't exist yet, so we need to first create it
		if(!(comprev = document.getElementById('CommentBody_Preview')))
		{
			if(!(comlist = document.getElementById('Comments'))) //we're in a 'new discussion' page
			{
				comlist = document.createElement('ul');
				comlist.id = 'Comments';
				comlist.className = 'Preview';
				comlist.style.marginBottom = '10px';
				document.getElementById('Content').insertBefore(comlist, document.getElementById('Form'));
			}
			
			obj = document.createElement('li');
			obj.id = 'Comment_Preview';
			obj.innerHTML = '<div class="CommentHeader"><ul><li id="_cp_userinfo"></li><li> right now</li></ul><span>' + 
				'<a href="javascript:getform().submit();">' + 
				'submit comment</a></span></div><div id="CommentBody_Preview" class="CommentBody"></div>';
			
			comlist.appendChild(obj);
			comprev = document.getElementById('CommentBody_Preview');
			
			comprev.parentNode.style.backgroundImage = 
				'url(' + baseurl + 'extensions/PreviewPost/preview.gif)';
		}
    	
    	//set whisper styling
    	whisperto = form.WhisperUsername ? form.WhisperUsername.value : '';
    	comprev.parentNode.className = whisperto.length ? 'WhisperFrom' : '';
    	document.getElementById('_cp_userinfo').innerHTML = astr + (whisperto.length ? (' to ' + whisperto) : '');
    	
    	//get/set text
    	if(typeof(FCKeditorAPI) != 'undefined') text = FCKeditorAPI.GetInstance('Body').GetXHTML(true);
    	else text = form.Body.value;
		
		//encode text
		text = (encodeURIComponent) ? encodeURIComponent(text) : 
			escape(text.replace(/\+/g, '%2B'));
		
		//find format type
		if(!form.FormatType.length) type = form.FormatType.value;
		else
		{
			for(i = f = 0; i < form.FormatType.length; i++)
			{
				if(form.FormatType[i].checked)
				{
					f = 1;
					break;
				}
			}
			if(!f) i = 0;
			type = escape(form.FormatType[i].value);
		}
		
		//make POST request
		if(!createrequest_post(baseurl+'extensions/PreviewPost/ajax.php', 'Data='+text+'&Type='+type, _showpreview)) 
			alert('An error occured while attempting to set up request');
	}
	else alert('Unable to find form');
	
	return;
}
