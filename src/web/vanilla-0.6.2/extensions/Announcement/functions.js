function ExecuteEffect(ElementID, EffectFunction, Speed) {
	 if (document.getElementById(ElementID))
		  EffectTimer = setInterval (EffectFunction+"('"+ElementID+"');", Speed);
}
function HideEffect(ElementID) {
    var el = document.getElementById(ElementID);
    if (el && Height > 1) {
        Height = Height - 2;
        if (Height < 1) Height = 1;
        el.style.height = Height + "px";
    } else if (el && el.offsetHeight > 0 && Height == -1) {
        Height = el.offsetHeight;
        el.style.overflow = "hidden";
    } else {
        el.style.display = "none";
        ClearTimer();
    }
}
function ClearTimer() {
	clearInterval(EffectTimer);
	EffectTimer = null;
	Height = 0;
}
