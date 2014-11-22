<!DOCTYPE html>
<html>
  <head>
    <!-- <meta http-equiv="Content-Type" content="text/html; charset=utf-8"> -->
    <meta name="viewport" content="initial-scale=1.0, user-scalable=no">
    <meta charset="utf-8">
    <title>Complex polylines</title>
    <!-- <link href="default.css" rel="stylesheet"> -->
    <link rel="stylesheet" href="css/jquery-ui.css" />
    <style type="text/css">
    html, body 
    {
      height: 100%;
      margin: 0;
      padding: 0;
    }
    
    #map-canvas, #map_canvas 
    {
      height: 100%;
    }
    
    @media print 
    {
      html, body
      { 
        height: auto;
      }
    
      #map-canvas, #map_canvas 
      {
        height: 650px;
      }
    }
    
    #panel 
    {
        position: absolute;
        top: 5px;
        left: 40%;
        margin-left: -180px;
        z-index: 5;
        background-color: #fff;
        padding: 5px;
        border: 0px solid #999;
        box-shadow: 0 2px 2px rgba(33, 33, 33, 0.4);
    }
    </style>

    <script type="text/javascript" src="js/jquery-1.9.0.js"></script>
    <script type="text/javascript" src="js/jquery-ui.js"></script>
    <script type="text/javascript" src="js/jquery-ui-timepicker-addon.js"></script>
    <script src="https://maps.googleapis.com/maps/api/js?v=3.exp&sensor=false"></script>
    <script>

        var poly;
        var map;
        
        function initialize() 
        {
          //var chicago = new google.maps.LatLng(41.879535, -87.624333);
          var xian = new google.maps.LatLng(34.26667,108.95000);
          var mapOptions = {
            zoom: 5,
            center: xian,
            mapTypeId: google.maps.MapTypeId.ROADMAP
          };


          map = new google.maps.Map(document.getElementById('map-canvas'), mapOptions);
          var polyOptions = {
            strokeColor: '#FF0000',
            strokeOpacity: 1.0,
            strokeWeight: 3
          }
        
          poly = new google.maps.Polyline(polyOptions);
          poly.setMap(map);
          // Add a listener for the click event
          //google.maps.event.addListener(map, 'click', addLatLng);
        }

        /**
         * Handles click events on a map, and adds a new point to the Polyline.
         * @param {google.maps.MouseEvent} event
         */
        function addLatLng(latLng) 
        {
        
          var path = poly.getPath();
          // Because path is an MVCArray, we can simply append a new coordinate
          // and it will automatically appear
          path.push(latLng);
          // Add a new marker at the new plotted point on the polyline.
         /* var marker = new google.maps.Marker(
          {
            position: latLng,
            title: '#' + path.getLength(),
            map: map
          });*/
        }
        google.maps.event.addDomListener(window, 'load', initialize);
    </script>

  </head>

  <body>
    <div id="panel">

      <b>CarNumber: </b>
      <input name="carNumber" id="car-number" type="text"/>

      <b>StartTime: </b>
      <input name="start_time" type="text" class="start-time-input"/>

      <b>EndTime: </b>
      <input name="end_time" type="text" class="end-time-input"/>
  
      <input type="button" class="button" id="Submit" name="commit" tabindex="3" value="Submit"/>
    </div>

    <div id="map-canvas"></div>
  </body>

  <script type="text/javascript">

    function getUrlParam(name) 
    {
      var reg = new RegExp("(^|&)" + name + "=([^&]*)(&|$)");
      var r = window.location.search.substr(1).match(reg);
      if (r != null)
      return decodeURI(r[2]);
      return null;
    }

    $('.start-time-input').datetimepicker(
      {
        'altTimeFormat':'Y-m-d','onSelect':(function(e)
        {
          $(".order-start-time").text(e);
        })                          
      });
  
  $('.end-time-input').datetimepicker(
    {
      'altTimeFormat':'Y-m-d','onSelect':(function(e)
      {
        $(".order-end-time").text(e);
      })                          
    });


  $(document).ready(function() 
  {

  var CarNumber=getUrlParam('CarNumber');
  $("#car-number").val(CarNumber);
  //alert(CarNumber);

  $("#Submit").click(function() 
  {
    var car_number = $("#car-number").val();
    var start_time = $(".start-time-input").val();
    var end_time = $(".end-time-input").val();

    if ("" == car_number) {
      alert("车牌号不能为空。");
      $(".start-time-input").focus();
      return;
    }
    if ("" == start_time) {
      alert("起始时间不能为空。");
      $(".start-time-input").focus();
      return;
    }
    if (""==end_time)
    {
      alert("终止时间不能为空。");
      $(".end-time-input").focus();
      return;
    }

    $.post("/client/http/monitor.php" + "?item=getLocation&carNumber=" + car_number + "&startTime="+start_time + "&endTime="+end_time, function(data)
      {

        if(data.length==39)
        {
          alert("系统中不存在此两车或无车机日志");
          return;
        }
         
        data=eval('('+data+')');
        if(data.errno===2000)
        {
          var length=data.content.length;
          alert("共描绘了"+length+"个点");
         
          var start = new google.maps.LatLng(data.content[0].latitude,data.content[0].longitude);
          map.setCenter(start);
          map.setZoom(11);
          var marker = new google.maps.Marker(
          {
            position: start,
            title: 'start',
            map: map
          });
          
          var end = new google.maps.LatLng(data.content[length-1].latitude,data.content[length-1].longitude);
          var marker = new google.maps.Marker(
          {
            position: end,
            title: 'end',
            map: map
          });

          for(var i=1;i<length-1;i++)
          {
            var point=new google.maps.LatLng(data.content[i].latitude,data.content[i].longitude);
            addLatLng(point);
          }
        }
      });
  });
});
</script>
</html>