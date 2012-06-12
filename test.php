</script>
<script type="text/javascript" src="http://maps.google.com/maps/api/js?sensor=false"></script>

<script type="text/javascript">
	var data = [
		{
			"t":"18 June 07:00",
			"c":"-29.607335, 30.411336"
		}

	];

	function initialize() {
		var def = data[0].c;
		def = def.split(",");
		var myLatlng = new google.maps.LatLng(def[0], def[1]);
		var mapOptions = {
			zoom     :8,
			center   :myLatlng,
			mapTypeId:google.maps.MapTypeId.HYBRID
			//disableDefaultUI:true
		};

		var map = new google.maps.Map(document.getElementById("map_canvas"), mapOptions);

		var flightPlanCoordinates = [];
		for (var i in data) {
			var item = data[i].c;
			item = item.split(",");
			myLatlng = new google.maps.LatLng(item[0], item[1]);
			flightPlanCoordinates.push(myLatlng);

			var marker = new google.maps.Marker({
				position:myLatlng,
				map     :map,
				title   :data[i].t
			});

		}
		var flightPath = new google.maps.Polyline({
			path         :flightPlanCoordinates,
			strokeColor  :"#FF0000",
			strokeOpacity:1.0,
			strokeWeight :2
		});

		flightPath.setMap(map);

		marker = new google.maps.Marker({
			position:new google.maps.LatLng(-33.714773, 18.956738),
			map     :map,
			title   :"End Destination"
		});

	}

	initialize();
	$(document).ready(function () {
		$(".tweet").tweet({
			join_text             :"auto",
			username              :"freedom_trail",
			avatar_size           :48,
			count                 :6,
			auto_join_text_default:"we said,",
			auto_join_text_ed     :"we",
			auto_join_text_ing    :"we were",
			auto_join_text_reply  :"we replied",
			auto_join_text_url    :"we were checking out",
			loading_text          :"loading tweets..."
		});
	});
