$('.carousel').carousel({
  interval: 5000
});

function goToByScroll(id){
		$('html,body').animate({scrollTop: $("."+id).offset().top},'slow');
}

var currentBackground = 0;
var backgrounds = [];
backgrounds[0] = $projectTemplatePath+'images/icons/image/ffc-spotlight-slide4.jpg';
backgrounds[1] = $projectTemplatePath+'images/icons/image/ffc-spotlight-slide2.jpg';
backgrounds[2] = $projectTemplatePath+'images/icons/image/ffc-spotlight-slide3.jpg';
backgrounds[3] = $projectTemplatePath+'images/icons/image/ffc-spotlight-slide2_1.jpg';

function changeBackground() {
    currentBackground++;
    if(currentBackground > 3) currentBackground = 0;

    $('.spotlight-hm').fadeOut(1000,function() {
        $('.spotlight-hm').css({
            'background-image' : "url('" + backgrounds[currentBackground] + "')"
        });
        $('.spotlight-hm').fadeIn(1000);
    });


    setTimeout(changeBackground, 5000);
}

$(document).ready(function() {
    setTimeout(changeBackground, 5000);        
});