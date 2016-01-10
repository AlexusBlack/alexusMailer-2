function serviceFunc() {
	checkOutOfLimit();
	setInterval(checkOutOfLimit,30000);
}
function checkOutOfLimit() {
	$.get("?limit",{},function(data) {
		var limit=parseInt(data);
		if(limit<=0)
			$("#sendlimit").html("%sendavailable%");
		else if(limit<60)
			$("#sendlimit").html("%sendlessminute%");
		else
			$("#sendlimit").html("%sendafter%"+parseInt(limit/60)+"%sendafter2%");
	});
}