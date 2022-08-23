var stage,
    layer,
    drawLayer, 
    points = [],
    zones = [],
    drills = [],
    current_zone = {},
    current_drill = {},
    colorPalette = [
      ["#000","#444","#666","#999","#ccc","#eee","#f3f3f3","#fff"],
      ["#f00","#f90","#ff0","#0f0","#0ff","#00f","#90f","#f0f"],
      ["#f4cccc","#fce5cd","#fff2cc","#d9ead3","#d0e0e3","#cfe2f3","#d9d2e9","#ead1dc"],
      ["#ea9999","#f9cb9c","#ffe599","#b6d7a8","#a2c4c9","#9fc5e8","#b4a7d6","#d5a6bd"],
      ["#e06666","#f6b26b","#ffd966","#93c47d","#76a5af","#6fa8dc","#8e7cc3","#c27ba0"],
      ["#c00","#e69138","#f1c232","#6aa84f","#45818e","#3d85c6","#674ea7","#a64d79"],
      ["#900","#b45f06","#bf9000","#38761d","#134f5c","#0b5394","#351c75","#741b47"],
      ["#600","#783f04","#7f6000","#274e13","#0c343d","#073763","#20124d","#4c1130"]
    ], 
    color,
    drill_icon = "{{ URL::asset('/assets/images/icon/drilldown__icon_transparent.png') }}";

function setupKonvaElement(selector) {
  stage = new Konva.Stage({
    container: selector,
    width: 0,
    height: 0
  });

  layer = new Konva.Layer();
  drawLayer = new Konva.Layer();

  stage.add(layer, drawLayer)
    .on("contextmenu", function(e) {
      e.evt.preventDefault();
    });
}

function resetKonvaElement(w, h) {
  if (stage != undefined) stage.setWidth(w).setHeight(h);
  if (layer != undefined ) layer.removeChildren().draw();
  if (drawLayer != undefined ) drawLayer.removeChildren().draw();
}

function calculatePoints(m, p = [], ow, oh, rw, rh) {                
  var calPoints = [];
  for (var i = 0; i < p.length; i += 2) {
      var calX = calY = 0;
      calX = m == 'display' ? (p[i] / ow) * rw : (p[i] / rw) * ow;
      calY = m == 'display' ? (p[i + 1] / oh) * rh : (p[i + 1] / rh) * oh;
      
      calPoints.push(calX);
      calPoints.push(calY);
  }
  return calPoints;
}

function searchById(id, arr) {
  for(index in arr) {
      if (arr[index].id === id) {
          return index;
      }
  }
  return 0;
}

function drawZones() {
  if (layer != undefined) {
    for (index in zones) {
      if (zones[index].id !== current_zone.id) {
        drawZone(index, zones[index].points, zones[index].color);
      }
    }
  }
}

function drawZone(index, points, color, border_color = "black", name = "zone") {  
  var zone = new Konva.Line({
    points: points,
    stroke: border_color,
    strokeWidth: 4,
    fill: color + "66",  
    closed : true,
    id: 'zone_' + index,
    name: name,
    onFinish: function() {
      zone.destroy();
    }
  });

  layer.add(zone).draw();
  zone.moveToBottom();
  layer.draw();

  return zone;
}

function drawLine() {
  if (drawLayer != undefined) {
    drawLayer.removeChildren().draw();
    for (var i = 0; i < points.length; i += 2) {
      var rect = new Konva.Rect({
        x: points[i] - 5,
        y: points[i+1] - 5,
        width: 10,
        height: 10,
        stroke: 'black',
        strokeWidth: 1,     
        fill: 'lightgray',
        draggable: true,
        name: 'point_' + i,
        onFinish: function() {
          rect.destroy();
        },
        dragBoundFunc: function(pos) {
          var newX, newY,
              posX = pos.x;
              posY = pos.y; 
          if (posX >= 0 && posX <= stage.width() - 10) {
            newX = posX;
          } else if (posX < 0) {
            newX = 0;
          } else if (posX > stage.width() - 10) {
            newX = stage.width() - 10;
          }

          if (posY >= 0 && posY <= stage.height() - 10) {
            newY = posY;
          } else if (posY < 0) {
            newY = 0;
          } else if (posY > stage.height() - 10) {
            newY = stage.height() - 10;
          }

          return { x: newX, y: newY };
        }
      }).on("dragend", function(e) {
          var activePoint = e.target.attrs.name.split('_')[1];   
          points[parseInt(activePoint)] = Math.round(this.x() + 5);
          points[parseInt(activePoint) + 1] = Math.round(this.y() + 5);        
          drawLine();
      }).on('contextmenu', function(e) {
          e.evt.preventDefault();
          var activePoint = e.target.attrs.name.split('_')[1];
          points.splice(activePoint, 2);      
          drawLine();
          return false;
      });
      drawLayer.add(rect);
    }

    var zone = new Konva.Line({
      points: points,
      stroke: 'black',
      strokeWidth: 1,
      fill: color + "66",  
      closed : true,
      onFinish: function() {
        zone.destroy();
      }
    });

    drawLayer.add(zone).draw();
    zone.moveToBottom();
    drawLayer.draw();
  }
}

function drawDrill(index, posX, posY, draggable = false) {
  var imageObj = new Image();
  imageObj.src = drill_icon;

  var image = new Konva.Image({
    x: posX - 20,
    y: posY - 40,
    image: imageObj,
    width: 40,
    height: 40,
    id: 'drill_' + index,
    name: 'drill',
    draggable: draggable,
    onFinish: function() {
      image.destroy();
    },
    dragBoundFunc: function(pos) {
      var newX, newY;
      var posX = pos.x;
      var posY = pos.y;
      if (posX >= 0 && posX <= stage.width() - 40) {
        newX = posX;
      } else if (posX < 0) {
        newX = 0;
      } else if (posX > stage.width() - 40) {
        newX = stage.width() - 40;
      }

      if (posY >= 0 && posY <= stage.height() - 40) {
        newY = posY;
      } else if (posY < 0) {
        newY = 0;
      } else if (posY > stage.height() - 40) {
        newY = stage.height() - 40;
      }

      return {
          x: newX,
          y: newY
      };
    }
  });

  layer.add(image).draw();
  image.moveToTop()
  layer.draw();

  return image;
}

function drawIssue(index, posX, posY, icon, name, draggable) {
  var imageObj = new Image();   
  imageObj.src = icon;

  var image = new Konva.Image({
    x: posX,
    y: posY - 40,
    width: 40,
    height: 40,
    id: 'issue_' + index,
    name: name,
    draggable: draggable,
    onFinish: function() {
      image.destroy();
    }
  });

  imageObj.onload = function() {
    image.setImage(imageObj);  
    layer.add(image).draw();
    image.moveToTop();
    layer.draw();
  }

  return image;
}

function getRandomColor() {
  var letters = '0123456789ABCDEF';
  var color = '#';
  for (var i = 0; i < 6; i++) {
    color += letters[Math.floor(Math.random() * 16)];
  }
  return color;
}

function formatToZones(element, ow, oh, pw, ph) {
  zones.push({
    'id': element.id,
    'name': element.name,
    'reference': element.reference,
    'points': calculatePoints('display', element.points.split(','), ow, oh, pw, ph),
    'color' : element.color,
    'normal_form' : element.normal_form == null ? [] : element.normal_form.split(','),
    'normal_group_form' : element.normal_group_form == null ? [] : element.normal_group_form.split(','),
    'main_form' : element.main_form == null ? [] : element.main_form.split(','),
    'main_group_form' : element.main_group_form == null ? [] : element.main_group_form.split(','),
  });
}

function formatToDrills(element, ow, oh, pw, ph) {
  var coordinate = calculatePoints('display', [element.position_x, element.position_y], ow, oh, pw, ph);

  drills.push({
    'id': element.id,
    'posX': coordinate[0],
    'posY': coordinate[1],
  });
}

function slope(a, b) {
  if (a[0] == b[0]) {
      return null;
  }

  return (b[1] - a[1]) / (b[0] - a[0]);
}

function intercept(point, slope) {
  if (slope === null) {
      return point[0];
  }

  return point[1] - slope * point[0];
}

function checkLineCoordinate(pointA, pointB, $layer) {
  var m = slope(pointA, pointB);
  var b = intercept(pointA, m);

  var sx = pointA[0] > pointB[0] ? pointB[0] : pointA[0];
  var ex = pointA[0] > pointB[0] ? pointA[0] : pointB[0];
  for (var x = sx; x <= ex; x++) {
      var y = m * x + b;
      var $elem = $layer.getIntersection({x: x, y: y});

      if ($elem && $elem.getClassName() == "Line") {
          return false;
      }
  }
  return true;
}

function checkOverlay($points, $layer) {
  var $x = null, $y = null;
  for (var i = 0; i < $points.length; i += 2) {
    if (i == 0) {
      $x = $points[i];
      $y = $points[i + 1];
    } else {
      var check = checkLineCoordinate([$x, $y], [$points[i], $points[i + 1]], $layer);
      if (!check) { return false; }

      $x = $points[i];
      $y = $points[i + 1];
    }
  }
  
  var check = checkLineCoordinate([$x, $y], [$points[0], $points[1]], $layer);
  if (!check) { return false; }

  return true;
}
