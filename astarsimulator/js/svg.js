var max_x = 10;
var max_y = 5;

var draw = SVG('drawing').size('100%','100%');

for(i=1;i<=max_x;i++){
	for(j=1;j<=max_y;j++){
		draw.rect(100, 100).move(100*i,100*(j-1)).fill("#fff").stroke({ color: '#000', opacity: 1, width: 4 });
		draw.text(i + "," + j).font({family:   'Helvetica',  anchor:   'middle'}).move((100*i)+50,(100*(j-1))+45);
	}
}

var robot_x = 1;
var robot_y = 1;
var speed = 100;
var robot = draw.rect(40,40).fill("#FFA500").move((robot_x*100)+30,((robot_y-1)*100)+30).stroke({ color: '#000', opacity: 1, width: 4 });


$(document).keydown(function(e) {
	switch(e.which) {
		case 37: // left
			if(robot_x > 1){
				robot_x--;
				robot.animate(speed).move((robot_x*100)+30,((robot_y-1)*100)+30);
			}
			
			break;
		
		case 38: // up
			if(robot_y > 1){
				robot_y--;
				robot.animate(speed).move((robot_x*100)+30,((robot_y-1)*100)+30);
			}
			break;

		case 39: // right
			if(robot_x<max_x){
				robot_x++;
				robot.animate(speed).move((robot_x*100)+30,((robot_y-1)*100)+30);
			}
			break;

		case 40: // down
			if(robot_y<max_y){
				robot_y++;
				robot.animate(speed).move((robot_x*100)+30,((robot_y-1)*100)+30);
			}
			
			break;
		default: return; // exit this handler for other keys
	}
	e.preventDefault(); // prevent the default action (scroll / move caret)
});


$(document).ready(function(){
    $('#drawing').bind('mousewheel', function(e){
		var scale=$(this).css("zoom");
		var margin = 0.01;
		if(typeof scale=="undefined")scale=1;
        if(e.originalEvent.wheelDelta /120 > 0) {
			console.log(scale);
			$(this).css("zoom",(parseFloat(scale)+margin));
        }
        else{
            $(this).css("zoom",(parseFloat(scale)-margin));
        }
		return false;
    });
});

/*
rect.attr({
  fill: '#f06'
, 'fill-opacity': 0.5
, stroke: '#000'
, 'stroke-width': 10
})

*/