var nodes = [];
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
				$(".grid[px=5][py=5]").addClass("start");
				$(".grid[px=13][py=10]").addClass("stop");
				nodes=[
					[
						{x:parseInt($(".start").attr('px')),y:parseInt($(".start").attr('py'))}
					]
				];
			},
			loop : function(){
				var start = $(".start");
				var stop = $(".start");
				
				var nnodes = [];
				
				var rejoin = function(node,new_member){
					var cnode = new Array();
					for(i in node){
						cnode.push(node[i]);
					}
					cnode.push(new_member);
					return cnode;
				}
				
				for(i in nodes){
					var node = nodes[i];	
					var pnode = [];
					var sx = parseInt(node[0].x);
					var sy = parseInt(node[0].y);
					var lx = parseInt(node[node.length-1].x);
					var ly = parseInt(node[node.length-1].y);
					
					var nx = null;
					var ny = null;
					
					nx = lx-1; ny = ly; //Left
					if($(".grid[px="+nx+"][py="+ny+"]").length){
						pnode.push(rejoin(node,{x:nx,y:ny}));
						$(".grid[px="+nx+"][py="+ny+"]").addClass("path");
					}
					
					nx = lx; ny = ly-1; //Up
					if($(".grid[px="+nx+"][py="+ny+"]").length){
						pnode.push(rejoin(node,{x:nx,y:ny}));
						$(".grid[px="+nx+"][py="+ny+"]").addClass("path");
					}
					
					nx = lx+1; ny = ly; //Right
					if($(".grid[px="+nx+"][py="+ny+"]").length){
						pnode.push(rejoin(node,{x:nx,y:ny}));
						$(".grid[px="+nx+"][py="+ny+"]").addClass("path");
					}
					
					nx = lx; ny = ly+1; //Down
					if($(".grid[px="+nx+"][py="+ny+"]").length){
						pnode.push(rejoin(node,{x:nx,y:ny}));
						$(".grid[px="+nx+"][py="+ny+"]").addClass("path");
					}
					
					for(i in pnode){
						nnodes.push(pnode[i]);
					}
				}
				
				var nclean = [];
				for(i in nnodes){
					var xLast = nnodes[i][nnodes[i].length-1].x;
					var yLast = nnodes[i][nnodes[i].length-1].y;
					var exist = false;
					for(j in nclean){
						var xc = nclean[j][nclean[j].length-1].x;
						var yc = nclean[j][nclean[j].length-1].y;
						
						if(xc==xLast && yc==yLast){
							exist = true;
						}	
					}
					if(!exist){
						nclean.push(nnodes[i]);
					}
				}
				
				nodes = nclean;
				console.log(nclean);
				
				/*
				var sx = node[i].x;
						var sy = node[i].y;
						console.log(node[i].length);
						var lx = node[node[i].length-1].x;
						var ly = node[node[i].length-1].y;
						
						var nx = null;
						var ny = null;
						
						nx = lx-1; ny = ly; //Left
						if($(".grid[px="+nx+"][py="+ny+"]").length){
							var cnode = node[i];
							cnode.push({x:nx,y:ny});
							pnode.push(cnode);
						}
						
						nx = lx; ny = ly-1; //Up
						if($(".grid[px="+nx+"][py="+ny+"]").length){
							var cnode = node[i];
							cnode.push({x:nx,y:ny});
							pnode.push(cnode);
						}
						
						nx = lx+1; ny = ly; //Right
						if($(".grid[px="+nx+"][py="+ny+"]").length){
							var cnode = node[i];
							cnode.push({x:nx,y:ny});
							pnode.push(cnode);
						}
						
						nx = lx; ny = ly+1; //Down
						if($(".grid[px="+nx+"][py="+ny+"]").length){
							var cnode = node[i];
							cnode.push({x:nx,y:ny});
							pnode.push(cnode);
						}
						
						for(i in pnode){
							nnode.push(pnode[i]);
						}
						console.log(nnode);
				
				*/
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
						if($(".grid[px="+nx+"][py="+ny+"]").length){
							var cnode = node[i];
							cnode.push({x:nx,y:ny});
							pnode.push(cnode);
						}
						
						nx = lx; ny = ly-1; //Up
						if($(".grid[px="+nx+"][py="+ny+"]").length){
							var cnode = node[i];
							cnode.push({x:nx,y:ny});
							pnode.push(cnode);
						}
						
						nx = lx+1; ny = ly; //Right
						if($(".grid[px="+nx+"][py="+ny+"]").length){
							var cnode = node[i];
							cnode.push({x:nx,y:ny});
							pnode.push(cnode);
						}
						
						nx = lx; ny = ly+1; //Down
						if($(".grid[px="+nx+"][py="+ny+"]").length){
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




