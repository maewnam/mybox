var fn = {
	app : {
		whisim : {
			config : {
				max_x : 30,
				max_y : 15,
				cell_height : 40,
				cell_width : 40,
			},
			init : function(){
				var config = fn.app.whisim.config;
				var draw = SVG('drawing').size('100%',config.max_y*config.cell_height);
				fn.app.whisim.engine.create_map(draw);
			},
			select_obstackle : function(){
				$(".area").selectable();
				$("#b1").addClass("d-none");$("#b2").removeClass("d-none");
			},
			confirm_obstackle :function(){
				fn.app.whisim.engine.recreate_map();
				$("#b2").addClass("d-none");$("#b3").removeClass("d-none");
				$( ".area" ).selectable( "destroy" );
				$(".grid").click(function(){
					$(".grid").removeClass("start");
					$(this).addClass("start");
				});
			},
			select_start :function(){
				$(".grid").unbind( "click" );
				$("#b3").addClass("d-none");$("#b4").removeClass("d-none");
				$(".grid").click(function(){
					$(".grid").removeClass("stop");
					$(this).addClass("stop");
				});
			},
			select_stop :function(){
				$("#b4").addClass("d-none");$("#b5").removeClass("d-none");
			},
			simulate : function(){
				var config = fn.app.whisim.config;
				var start = $(".start");
				var stop = $(".start");
				
				var nodes=[
					[
						{x:$(".start").attr('px'),y:$(".start").attr('py')}
					]
				];				
				
				var loop_posible = function(node,config){ //Find Nodes;
					var nnode = [];
					for(i in node){
						var cnode = node[i]; //Current Node;
						var pnode = []; //Posible Nodes
						
						var sx = node[i].x;
						var sy = node[i].y;
						console.log(node[i].length);
						var lx = node[node[i].length-1].x;
						var ly = node[node[i].length-1].y;
						
						var nx = null;
						var ny = null;
						
						nx = lx-1; ny = ly; //Left
						if($(".grid_text[px="+nx+"][py="+ny+"]").length){
							var cnode = node[i];
							cnode.push({x:nx,y:ny});
							pnode.push(cnode);
						}
						
						
				
						
						nx = lx; ny = ly-1; //Up
						if($(".grid_text[px="+nx+"][py="+ny+"]").length){
							var cnode = node[i];
							cnode.push({x:nx,y:ny});
							pnode.push(cnode);
						}
						
						nx = lx+1; ny = ly; //Right
						if($(".grid_text[px="+nx+"][py="+ny+"]").length){
							var cnode = node[i];
							cnode.push({x:nx,y:ny});
							pnode.push(cnode);
						}
						
						nx = lx; ny = ly+1; //Down
						if($(".grid_text[px="+nx+"][py="+ny+"]").length){
							var cnode = node[i];
							cnode.push({x:nx,y:ny});
							pnode.push(cnode);
						}
						
						for(i in pnode){
							nnode.push(pnode[i]);
						}
						console.log(nnode);
						
					}
					return nnode;
				}
				
				
				/*
				var is_ending = function(config,node){
					var result = false;
					for(i in node){
						
					}
					
					return false;
					
				}
				
				
				var looking = true;
				while(looking){
					var item = loop_posible(node,config);
					if(ending = true;){
						
					}
				}
				
				for(i in nodes){
					var node = nodes[i];
					loop_posible(node,config);
					
				}
				*/
				
			},
			engine : {
				create_map : function(draw){
					var config = fn.app.whisim.config;
					var group_text = draw.group().addClass('text');
					var group = draw.group().addClass('area');
					for(i=0;i<config.max_x;i++){
						for(j=0;j<config.max_y;j++){
							group_text.add(draw.text(i + "," + j)
								.font({family:   'Helvetica'})
								.move(config.cell_width*i,config.cell_height*j)
								.attr("px",i).attr("py",j)
								.addClass("grid_text")
							);
							group.add(draw.rect(config.cell_width,config.cell_height)
								.fill("#fff").attr('opacity','.5')
								.stroke({ color: '#000', opacity: 1, width: 2 })
								.move(config.cell_width*i,config.cell_height*j)
								.attr("px",i).attr("py",j)
								.addClass("grid")
							);
						}
					}
				},
				recreate_map : function(){
					$(".area .ui-selected").each(function(){
						var px = $(this).attr("px");
						var py = $(this).attr("py");
						$(".grid_text[px="+px+"][py="+py+"]").remove();
						this.instance.remove();
					});
				},
				simulate : function(){
					
				}
			}

		}
	}
}




