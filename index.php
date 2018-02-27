<?php
// function to geocode address, it will return false if unable to geocode address
function geocode($address){

  // url encode the address
  $address = urlencode($address);

  // google map geocode api url
  $url = "https://maps.googleapis.com/maps/api/geocode/json?address={$address}&key=AIzaSyCHfBAxW62Gpotc4vwI_JGaLHjluAnMTlw";

  // get the json response
  $resp_json = file_get_contents($url);

  // decode the json
  $resp = json_decode($resp_json, true);

  // response status will be 'OK', if able to geocode given address
  if($resp['status']=='OK'){

    // get the important data
    $lati = isset($resp['results'][0]['geometry']['location']['lat']) ? $resp['results'][0]['geometry']['location']['lat'] : "";
    $longi = isset($resp['results'][0]['geometry']['location']['lng']) ? $resp['results'][0]['geometry']['location']['lng'] : "";
    $formatted_address = isset($resp['results'][0]['formatted_address']) ? $resp['results'][0]['formatted_address'] : "";

    // verify if data is complete
    if($lati && $longi && $formatted_address){
      // put the data in the array
      $data_arr = array();
      array_push(
        $data_arr,
        $lati,
        $longi,
        $formatted_address
      );

      return $data_arr;

    }else{
      return false;
    }

  }

  else{
    echo "<strong>ERROR: {$resp['status']}</strong>";
    return false;
  }
}

function getdestination($address){
  // url encode the address
  $address = urlencode($address);

  // google map geocode api url
  $url = "https://maps.googleapis.com/maps/api/geocode/json?address={$address}&key=AIzaSyCxs0NIji6J3bS0XGRBu2i-538CqKSvwRU";

  // get the json response
  $resp_json = file_get_contents($url);

  // decode the json
  $resp = json_decode($resp_json, true);

  // response status will be 'OK', if able to geocode given address
  if($resp['status']=='OK'){

    // get the important data
    $lati = isset($resp['results'][0]['geometry']['location']['lat']) ? $resp['results'][0]['geometry']['location']['lat'] : "";
    $longi = isset($resp['results'][0]['geometry']['location']['lng']) ? $resp['results'][0]['geometry']['location']['lng'] : "";
    if($lati && $longi && $formatted_address){
      // put the data in the array
      $desti_arr = array();
      array_push(
        $desti_arr,
        $lati,
        $longi
      );

      return $desti_arr;

    }else{
      return false;
    }

  }

  else{
    echo "<strong>ERROR: {$resp['status']}</strong>";
    return false;
  }
}
?>
<?php
if(isset($_POST['address'])){

  // get latitude, longitude and formatted address
  $data_arr = geocode($_POST['address']);

  // if able to geocode the address
  if($data_arr){

    $latitude = $data_arr[0];
    $longitude = $data_arr[1];
    $formatted_address = $data_arr[2];


    // if unable to geocode the address
  }else{
    echo "No map found.";
  }
  $desti_arr = geocode($_POST['address2']);

  if($desti_arr){

    $latitude2 = $desti_arr[0];
    $longitude2 = $desti_arr[1];
    $formatted_address2 = $desti_arr[2];
    // if unable to geocode the address
  }else{
    echo "No map found.";
  }



}
?>
<!DOCTYPE html>
<html lang="en">
<head>

  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1">

  <title>Google Maps Geocoding with PHP</title>

  <style>
  body{
    font-family:arial;
    font-size:.8em;
  }

  input[type=text]{
    padding:0.5em;
    width:20em;
  }

  input[type=submit]{
    padding:0.4em;
  }

  #gmap{
    width:100%;
    height:45em;
  }

  #map-label,
  #address-examples{
    margin:1em 0;
  }
  #body{
    background-color: lightgrey;
    font-size: 15px;
  }

  </style>

</head>
<body>



  <div id="gmap">Loading map...</div>



  <script type="text/javascript" src="https://maps.googleapis.com/maps/api/js?key=AIzaSyCxs0NIji6J3bS0XGRBu2i-538CqKSvwRU&callback=initMap"></script>
  <script type="text/javascript">
  function initMap() {
    var map = new google.maps.Map(document.getElementById('gmap'), {
      zoom: 13,
      center: {lat: 40.771, lng: -73.974}
    });
    var markerArray = [];
    // Instantiate a directions service.
    var directionsService = new google.maps.DirectionsService;

    // Create a renderer for directions and bind it to the map.
    var directionsDisplay = new google.maps.DirectionsRenderer({map: map});

    // Instantiate an info window to hold step text.
    var stepDisplay = new google.maps.InfoWindow;


    for (var i = 0; i < markerArray.length; i++) {
      markerArray[i].setMap(null);
    }

    // Retrieve the start and end locations and create a DirectionsRequest using
    // WALKING directions.
    directionsService.route({
      origin: {lat:<?php echo $latitude; ?>,lng:<?php echo $longitude; ?>},
      destination: {lat:<?php echo $latitude2; ?>,lng:<?php echo $longitude2; ?>},
      travelMode: 'WALKING'
    }, function(response, status) {
      // Route the directions and pass the response to a function to create
      // markers for each step.
      if (status === 'OK') {
        directionsDisplay.setDirections(response);
        //DISTANCE

      } else {
        window.alert('Directions request failed due to ' + status);
      }
    });

    var service = new google.maps.DistanceMatrixService;
    service.getDistanceMatrix({
      origins: [{lat:<?php echo $latitude; ?>,lng:<?php echo $longitude; ?>}],
      destinations: [{lat:<?php echo $latitude2; ?>,lng:<?php echo $longitude2; ?>}],
      travelMode: 'DRIVING',
    }, function(response, status) {
      if (status !== 'OK') {
        alert('Error was: ' + status);
      } else {
        var originList = response.originAddresses;
        var destinationList = response.destinationAddresses;
        for(var i =0; i<originList.length; i++){
          var results = response.rows[i].elements;
          for(var j=0; j<results.length;j++){
            var element = results[j];
            var dt = element.distance.text;
            document.getElementById('range').innerHTML = dt;
          };
        };
      }
    });
  }

  google.maps.event.addDomListener(window, 'load', initMap);

  </script>



  <div id="body">
    <div id="input">

      <form action="" method="post">
        <input type='text' name='address' placeholder='Enter origin address here' />
        <input type='submit' value='Search for direction' /><br>
        <input type='text' name='address2' placeholder='Enter destination here' />

      </form>
    </div>
    <div id="info">
      <p>Coordinate's Latitude and Longitude</p>
      <p>Origin's Latitude and Longitude:</p><?php echo $latitude; ?> | <?php echo $longitude; ?><br>
      <p>Destination's Latitude and Longitude:</p><?php echo $latitude2; ?> | <?php echo $longitude2; ?>
    </div>
    <p>The Distance between two points:</p><div id="range"></div>

  </div>


</body>
</html>
