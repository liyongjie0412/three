var mySwiper = new Swiper('.swiper-container', {
	direction: 'horizontal',
	autoplay:2000,
	loop: true,
	speed:1000,
	
	// 如果需要分页器
	pagination: '.swiper-pagination',
})
var i = 0;
var msg = ['大减价了','跳楼大甩卖'];
setInterval(function(){
	$('#msg').text(msg[i])
	i++;
	if(i == msg.length){
		i=0;
	}
},2000)
