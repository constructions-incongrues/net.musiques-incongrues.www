if (!window.Vanilla){
var Vanilla = new PathFinder();
Vanilla.webRoot = Vanilla.getRootPath('script', 'src', 'js/global.js');
Vanilla.styleUrl = Vanilla.getRootPath('link', 'href', 'vanilla.css');
Vanilla.baseURL = Vanilla.params.httpMethod + Vanilla.params.domain + Vanilla.webRoot;
}
var theImages = new Array()
theImages[0] = '<img src=\"'+Vanilla.webRoot+'/extensions/GuestPost/codes/secret1.gif\" border=\"0\"><input type=\"hidden\" name=\"very\" value=\"WPITS\">'
theImages[1] = '<img src=\"'+Vanilla.webRoot+'/extensions/GuestPost/codes/secret2.gif\" border=\"0\"><input type=\"hidden\" name=\"very\" value=\"AAFHH\">'
theImages[2] = '<img src=\"'+Vanilla.webRoot+'/extensions/GuestPost/codes/secret3.gif\" border=\"0\"><input type=\"hidden\" name=\"very\" value=\"126TB\">'
theImages[3] = '<img src=\"'+Vanilla.webRoot+'/extensions/GuestPost/codes/secret4.gif\" border=\"0\"><input type=\"hidden\" name=\"very\" value=\"WZFPQ\">'
theImages[4] = '<img src=\"'+Vanilla.webRoot+'/extensions/GuestPost/codes/secret5.gif\" border=\"0\"><input type=\"hidden\" name=\"very\" value=\"GUTSW\">'
theImages[5] = '<img src=\"'+Vanilla.webRoot+'/extensions/GuestPost/codes/secret6.gif\" border=\"0\"><input type=\"hidden\" name=\"very\" value=\"B223X\">'
theImages[6] = '<img src=\"'+Vanilla.webRoot+'/extensions/GuestPost/codes/secret7.gif\" border=\"0\"><input type=\"hidden\" name=\"very\" value=\"U99ST\">'
theImages[7] = '<img src=\"'+Vanilla.webRoot+'/extensions/GuestPost/codes/secret8.gif\" border=\"0\"><input type=\"hidden\" name=\"very\" value=\"PH667\">'
theImages[8] = '<img src=\"'+Vanilla.webRoot+'/extensions/GuestPost/codes/secret9.gif\" border=\"0\"><input type=\"hidden\" name=\"very\" value=\"LX998\">'
theImages[9] = '<img src=\"'+Vanilla.webRoot+'/extensions/GuestPost/codes/secret10.gif\" border=\"0\"><input type=\"hidden\" name=\"very\" value=\"XL876\">'
var j = 0
var p = theImages.length;
var preBuffer = new Array()
for (i = 0; i < p; i++){
   preBuffer[i] = new Image()
//   preBuffer[i].src = theImages[i] //preloads
}
var whichImage = Math.round(Math.random()*(p-1));
function showImage(){
document.write(theImages[whichImage]);
}
