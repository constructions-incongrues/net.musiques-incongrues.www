javascript:(function()%7Bvar%20title%3Ddocument.title%2Ctxt%3D%22%22%3Bwindow.getSelection%3Ftxt%3Dwindow.getSelection()%3Adocument.getSelection%3Ftxt%3Ddocument.getSelection()%3Adocument.selection%26%26(txt%3Ddocument.selection.createRange().text)%2Ctxt%3D%22%22%2Btxt%3Bvar%20ogDescription%3Ddocument.querySelector(%22meta%5Bproperty%3D'og%3Adescription'%5D%22)%3BogDescription%26%26ogDescription.getAttribute(%22content%22).length%3Etxt.length%26%26(txt%3DogDescription.getAttribute(%22content%22))%3Bvar%20metaDescription%3Ddocument.querySelector(%22meta%5Bname%3D'Description'%5D%22)%3BmetaDescription%26%26metaDescription.getAttribute(%22content%22).length%3Etxt.length%26%26(txt%3DmetaDescription.getAttribute(%22content%22))%2Ctxt.length%3E4096%26%26(txt%3Dtxt.substring(0%2C4093)%2B%22...%22)%3Bvar%20logo%3D%22%22%2CogImage%3Ddocument.querySelector(%22meta%5Bproperty%3D'og%3Aimage'%5D%22)%3BogImage%26%26(logo%3DogImage.getAttribute(%22content%22))%3Bvar%20url%3Ddocument.location%2B%22%22%2CcanonicalUrl%3Ddocument.querySelector(%22link%5Brel%3D'canonical'%5D%22)%3Bif(canonicalUrl)url%3DcanonicalUrl.href%3Belse%7Bvar%20ogUrl%3Ddocument.querySelector(%22meta%5Bproperty%3D'og%3Aurl'%5D%22)%3BogUrl%26%26(url%3DogUrl.getAttribute(%22content%22))%7Dvoid(btw%3Dwindow.open(%22http%3A%2F%2Fwww.musiques-incongrues.net%2Fforum%2Fpost%2F%3Ftitle%3D%22%2BencodeURIComponent(title)%2B%22%26url%3D%22%2BencodeURIComponent(url.replace(%22%252520%22%2C%22%2B%22))%2B%22%26image%3D%22%2BencodeURIComponent(logo.replace(%22%252520%22%2C%22%2B%22))%2B%22%26description%3D%22%2BencodeURIComponent(txt.trim())))%3B%7D)()