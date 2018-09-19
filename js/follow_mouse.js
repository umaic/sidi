var offsetfrommouse=[0,35]; //image x,y offsets from cursor position in pixels. Enter 0,0 for no offset
var displayduration=0; //duration in seconds image should remain visible. 0 for always.
var currentimageheight = 10;	// maximum image size.
var div = document.getElementById('enlarge');

function truebody(){
	return (!window.opera && document.compatMode && document.compatMode!="BackCompat")? document.documentElement : document.body
}


function followmouse(e){

	div = document.getElementById('info_mouse');
	var xcoord=offsetfrommouse[0];
	var ycoord=offsetfrommouse[1];

	var docwidth=document.all? truebody().scrollLeft+truebody().clientWidth : pageXOffset+window.innerWidth-15
	var docheight=document.all? Math.min(truebody().scrollHeight, truebody().clientHeight) : Math.min(document.body.offsetHeight, window.innerHeight)

	
	if (typeof e != "undefined"){
		if (docwidth - e.pageX < 10){
			xcoord = e.pageX - xcoord - 10; // Move to the left side of the cursor
		} else {
			xcoord += e.pageX;
		}
		if (docheight - e.pageY < (currentimageheight + 110)){
			ycoord += e.pageY - Math.max(0,(110 + currentimageheight + e.pageY - docheight - truebody().scrollTop));
		} else {
			ycoord += e.pageY;
		}

	} else if (typeof window.event != "undefined"){
		if (docwidth - event.clientX < 10){
			xcoord = event.clientX + truebody().scrollLeft - xcoord - 10; // Move to the left side of the cursor
		} else {
			xcoord += truebody().scrollLeft+event.clientX
		}
		if (docheight - event.clientY < (currentimageheight + 110)){
			ycoord += event.clientY + truebody().scrollTop - Math.max(0,(110 + currentimageheight + event.clientY - docheight));
		} else {
			ycoord += truebody().scrollTop + event.clientY;
		}
	}

	div.style.left=xcoord+"px";
	div.style.top=ycoord+"px";

}

function docwidth(){
	if (self.innerWidth)
	{
		WidthW = self.innerWidth;
	}
	else if (document.documentElement && document.documentElement.clientWidth)
	{
		WidthW = document.documentElement.clientWidth;
	}
	else if (document.body)
	{
		WidthW = document.body.clientWidth;
	}

	WidthS = document.body.scrollWidth;

	if (WidthW > WidthS)	return WidthW;
	else					return WidthS;

}

function docheight(){

	if (self.innerWidth)
	{
		HeightW = self.innerHeight;
	}
	else if (document.documentElement && document.documentElement.clientHeight)
	{
		HeightW = document.documentElement.clientHeight;
	}
	else if (document.body)
	{
		HeightW = document.body.clientHeight;
	}

	HeightS = document.body.scrollHeight;

	if (HeightW > HeightS)	return HeightW;
	else					return HeightS;
}