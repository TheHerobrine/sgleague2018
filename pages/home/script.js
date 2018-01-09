function lightGames()
{
	document.getElementById("games").scrollIntoView({behavior: 'smooth'});
	document.getElementById("games").style["boxShadow"] = "0px 0px 50px rgba(0, 0, 0, 0.5);";

	var darkdiv = d3.select("body")
		.append("div")
		.classed("darkbg", true)
		.attr("onclick", "destroyDark()");

	window.getComputedStyle(darkdiv.node()).opacity;

	darkdiv.style("opacity", "0.6");
}

function destroyDark()
{
	d3.selectAll(".darkbg").remove();
}

function zero(num)
{
	if (num < 10)
	{
		return "0"+num;
	}
	else
	{
		return num;
	}
}
/*
var countDownDate = new Date("May 27, 2017 13:30:00").getTime();

var x = setInterval(function()
{

	var now = new Date().getTime();
	var distance = countDownDate - now;

	var d = Math.floor(distance / (1000 * 60 * 60 * 24));
	var h = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
	var m = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
	var s = Math.floor((distance % (1000 * 60)) / 1000);
	var ms = Math.floor((distance % (1000)) / 10);

	document.getElementById("mainlink").innerHTML = "J-" + d + " " + zero(h) + ":" + zero(m) + ":" + zero(s) + ":" + zero(ms);

	if (distance < 0)
	{
		clearInterval(x);
		document.getElementById("demo").innerHTML = "EXPIRED";
	}
}, 10);
*/