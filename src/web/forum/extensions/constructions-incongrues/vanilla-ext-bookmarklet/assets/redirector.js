var title = document.title;
var txt = '';
if (window.getSelection) {
    txt = window.getSelection();
} else if (document.getSelection) {
    txt = document.getSelection();
} else if (document.selection) {
    txt = document.selection.createRange().text;
}
txt = txt.toString();
var ogDescription = document.querySelector('meta[property=\'og:description\']');
if (ogDescription) {
    if (ogDescription.getAttribute('content').length > txt.length) {
        txt = ogDescription.getAttribute('content');
    }
}
var metaDescription = document.querySelector('meta[name=\'Description\']');
if (metaDescription) {
    if (metaDescription.getAttribute('content').length > txt.length) {
        txt = metaDescription.getAttribute('content');
    }
}
if (txt.length > 4096) {
    txt = txt.substring(0, 4093) + '...';
}
var logo = '';
var ogImage = document.querySelector('meta[property=\'og:image\']');
if (ogImage) {
    logo = ogImage.getAttribute('content');
}
var url = document.location + '';
var canonicalUrl = document.querySelector('link[rel=\'canonical\']');
if (canonicalUrl) {
    url = canonicalUrl.href;
} else {
    var ogUrl = document.querySelector('meta[property=\'og:url\']');
    if (ogUrl) {
        url = ogUrl.getAttribute('content');
    }
}
console.log(txt);
void (btw = window.open('http://vanilla.musiques-incongrues.vagrant.test/forum/post/?Title=' + encodeURIComponent(title) + '&Via=' + encodeURIComponent(url.replace('%2520', '+')) + '&Image=' + encodeURIComponent(logo.replace('%2520', '+')) + '&Description=' + encodeURIComponent(txt.trim())))
