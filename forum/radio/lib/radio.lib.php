<?php
// Helpers
/**
 * Truncates +text+ to the length of +length+ and replaces the last three characters with the +truncate_string+
 * if the +text+ is longer than +length+.
 */
function truncate_text($text, $length = 30, $truncate_string = '...', $truncate_lastspace = false)
{
	if ($text == '')
	{
		return '';
	}

	$mbstring = extension_loaded('mbstring');
	if($mbstring)
	{
		$old_encoding = mb_internal_encoding();
		@mb_internal_encoding(mb_detect_encoding($text));
	}
	$strlen = ($mbstring) ? 'mb_strlen' : 'strlen';
	$substr = ($mbstring) ? 'mb_substr' : 'substr';

	if ($strlen($text) > $length)
	{
		$truncate_text = $substr($text, 0, $length - $strlen($truncate_string));
		if ($truncate_lastspace)
		{
			$truncate_text = preg_replace('/\s+?(\S+)?$/', '', $truncate_text);
		}
		$text = $truncate_text.$truncate_string;
	}

	if($mbstring)
	{
		@mb_internal_encoding($old_encoding);
	}

	return $text;
}

function CleanupString($InString) {
	$Code = explode(',', '&lt;,&gt;,&#039;,&amp;,&quot;,À,Á,Â,Ã,Ä,&Auml;,Å,Ā,Ą,Ă,Æ,Ç,Ć,Č,Ĉ,Ċ,Ď,Đ,Ð,È,É,Ê,Ë,Ē,Ę,Ě,Ĕ,Ė,Ĝ,Ğ,Ġ,Ģ,Ĥ,Ħ,Ì,Í,Î,Ï,Ī,Ĩ,Ĭ,Į,İ,Ĳ,Ĵ,Ķ,Ł,Ľ,Ĺ,Ļ,Ŀ,Ñ,Ń,Ň,Ņ,Ŋ,Ò,Ó,Ô,Õ,Ö,&Ouml;,Ø,Ō,Ő,Ŏ,Œ,Ŕ,Ř,Ŗ,Ś,Š,Ş,Ŝ,Ș,Ť,Ţ,Ŧ,Ț,Ù,Ú,Û,Ü,Ū,&Uuml;,Ů,Ű,Ŭ,Ũ,Ų,Ŵ,Ý,Ŷ,Ÿ,Ź,Ž,Ż,Þ,Þ,à,á,â,ã,ä,&auml;,å,ā,ą,ă,æ,ç,ć,č,ĉ,ċ,ď,đ,ð,è,é,ê,ë,ē,ę,ě,ĕ,ė,ƒ,ĝ,ğ,ġ,ģ,ĥ,ħ,ì,í,î,ï,ī,ĩ,ĭ,į,ı,ĳ,ĵ,ķ,ĸ,ł,ľ,ĺ,ļ,ŀ,ñ,ń,ň,ņ,ŉ,ŋ,ò,ó,ô,õ,ö,&ouml;,ø,ō,ő,ŏ,œ,ŕ,ř,ŗ,š,ù,ú,û,ü,ū,&uuml;,ů,ű,ŭ,ũ,ų,ŵ,ý,ÿ,ŷ,ž,ż,ź,þ,ß,ſ,А,Б,В,Г,Д,Е,Ё,Ж,З,И,Й,К,Л,М,Н,О,П,Р,С,Т,У,Ф,Х,Ц,Ч,Ш,Щ,Ъ,Ы,Э,Ю,Я,а,б,в,г,д,е,ё,ж,з,и,й,к,л,м,н,о,п,р,с,т,у,ф,х,ц,ч,ш,щ,ъ,ы,э,ю,я');
	$Translation = explode(',', ',,,,,A,A,A,A,Ae,A,A,A,A,A,Ae,C,C,C,C,C,D,D,D,E,E,E,E,E,E,E,E,E,G,G,G,G,H,H,I,I,I,I,I,I,I,I,I,IJ,J,K,K,K,K,K,K,N,N,N,N,N,O,O,O,O,Oe,Oe,O,O,O,O,OE,R,R,R,S,S,S,S,S,T,T,T,T,U,U,U,Ue,U,Ue,U,U,U,U,U,W,Y,Y,Y,Z,Z,Z,T,T,a,a,a,a,ae,ae,a,a,a,a,ae,c,c,c,c,c,d,d,d,e,e,e,e,e,e,e,e,e,f,g,g,g,g,h,h,i,i,i,i,i,i,i,i,i,ij,j,k,k,l,l,l,l,l,n,n,n,n,n,n,o,o,o,o,oe,oe,o,o,o,o,oe,r,r,r,s,u,u,u,ue,u,ue,u,u,u,u,u,w,y,y,y,z,z,z,t,ss,ss,A,B,V,G,D,E,YO,ZH,Z,I,Y,K,L,M,N,O,P,R,S,T,U,F,H,C,CH,SH,SCH,Y,Y,E,YU,YA,a,b,v,g,d,e,yo,zh,z,i,y,k,l,m,n,o,p,r,s,t,u,f,h,c,ch,sh,sch,y,y,e,yu,ya');
	$sReturn = $InString;
	$sReturn = str_replace($Code, $Translation, $sReturn);
	$sReturn = urldecode($sReturn);
	$sReturn = preg_replace('/[^A-Za-z0-9 ]/', '', $sReturn);
	$sReturn = str_replace(' ', '-', $sReturn);
	return strtolower(str_replace('--', '-', $sReturn));
}

function guessTitle(array $link) {
	// Thanks to soundcloud url formalism, we can guess the track title
	if ($link['domain_fqdn'] == 'soundcloud.com') {
		$urlParts = array_reverse(explode('/', $link['url']));
		$link['title'] = sprintf('%s - %s', str_replace('-', ' ', ucfirst($urlParts[2])), str_replace('-', ' ', ucfirst($urlParts[1])));
	} else {
		$link['title'] = urldecode(basename($link['url'], '.mp3'));
		$link['title'] = urldecode($link['title']);
		$link['title'] = str_replace('_', ' ', $link['title']);
	}

	return $link['title'];
}

// Get informations about playlists
function callService($filterFieldName, $filterFieldValue) {
	$url = sprintf('http://data.musiques-incongrues.net/collections/links/segments/mp3/get?format=json&%s=%s&sort_field=random&limit=1', $filterFieldName, $filterFieldValue);
	$curl = curl_init($url);
	curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
	$response = json_decode(curl_exec($curl), true);
	return $response;
}

function callServiceUrl($url) {
	$curl = curl_init($url);
	curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
	$response = curl_exec($curl);
	$links = json_decode($response, true);
	return $links;
}
