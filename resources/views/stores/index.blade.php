<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
    <!-- CSS files -->

    <link href="{{ asset('/public/libs/tabler/dist/css/tabler.min.css') }}" rel="stylesheet" />
    <link href="{{ asset('/public/libs/tabler/dist/css/tabler-vendors.min.css') }}" rel="stylesheet" />
    <style>
        #map {
            height: 400px;
            width: 100%;
        }
    </style>
</head>

<body>
    <main>
        <div class="container">
            <div class="row">
                <div class="col-6">
                    <label for="">{{ __('address') }}</label>
                    <x-input name="place" id="place" />
                </div>
            </div>
        </div>
    </main>
    <div id="map"></div>
    <button type="button" id="currentLocation" class="btn btn-default-cms">Lay vi tri</button>

    <script src="{{ asset('public/libs/tabler/dist/js/tabler.min.js') }}" defer></script>
    <script src="{{ asset('public/libs/jquery/jquery.min.js') }}"></script>
    <script type="text/javascript"
        src="https://maps.googleapis.com/maps/api/js?key=AIzaSyAzbVo-3B81-_eIIQHZKPDEm4DLjo0LCFU&libraries=places&language=vi&callback=initMap"
        async defer></script>

    <script>
        var map;
        var marker;
        var autocomplete;


        function initMap() {
            map = new google.maps.Map(document.getElementById('map'), {
                center: {
                    lat: 10.762622,
                    lng: 106.660172
                },
                zoom: 12,
                gestureHandling: "cooperative"
            });

            marker = new google.maps.Marker({
                map: map,
                draggable: true
            });

            // marker = new google.maps.marker.AdvancedMarkerElement({
            //     map: map,
            //     draggable: true
            // });

            autocomplete = new google.maps.places.Autocomplete(document.getElementById('place'));

            autocomplete.addListener('place_changed', function() {
                var place = autocomplete.getPlace();
                if (!place.geometry) {
                    console.log("Không tìm thấy địa điểm: '" + place.name + "'");
                    return;
                }

                if (place.geometry.viewport) {
                    map.fitBounds(place.geometry.viewport);
                } else {
                    map.setCenter(place.geometry.location);
                    map.setZoom(17); // Thu phóng để hiển thị đủ dữ liệu cho điểm được chọn
                }

                marker.setPosition(place.geometry.location);
                map.setCenter(place.geometry.location);

                console.log('Vị trí mới: ' + place.geometry.location.lat() + ', ' + place.geometry.location.lng());
            });

            // Sự kiện khi kéo thả marker
            // marker.addListener('dragend', function() {
            //     // Lấy vị trí mới của marker
            //     var newPosition = marker.getPosition();
            //     console.log(newPosition);
            //     // Tạo một đối tượng LatLng từ vị trí mới
            //     var latLng = new google.maps.LatLng(newPosition.lat(), newPosition.lng());
            //     // Sử dụng đối tượng LatLng để lấy thông tin vị trí từ Geocoder
            //     var geocoder = new google.maps.Geocoder();
            //     geocoder.geocode({ 'location': latLng }, function(results, status) {
            //         if (status === google.maps.GeocoderStatus.OK) {
            //             if (results[0]) {
            //                 // Lấy địa chỉ từ kết quả đầu tiên của Geocoder
            //                 var placeText = results[0].formatted_address;

            //                 // Hiển thị văn bản của địa điểm trong phần tử selectedPlace
            //                 console.log(placeText);

            //                 // Hiển thị vị trí mới của marker trong console
            //                 console.log('Vị trí mới: ' + newPosition.lat() + ', ' + newPosition.lng());
            //             } else {
            //                 window.alert('Không có kết quả nào được tìm thấy');
            //             }
            //         } else {
            //             window.alert('Geocoder failed due to: ' + status);
            //         }
            //     });
            // });








        }


        // Hàm lấy vị trí hiện tại của người dùng
        function getLocation() {
            if (navigator.geolocation) {
                navigator.geolocation.getCurrentPosition(function(position) {
                    var userLocation = {
                        lat: position.coords.latitude,
                        lng: position.coords.longitude
                    };
                    marker.setPosition(userLocation);
                    map.setCenter(userLocation); // Đặt trung tâm của bản đồ là vị trí hiện tại
                    map.setZoom(17);

                    var newPosition = marker.getPosition();

                    // Tạo một request để lấy chi tiết của địa điểm từ API Places
                    var request = {
                        location: newPosition,
                        radius: '20', // Bán kính tìm kiếm (tùy chỉnh theo nhu cầu của bạn)
                    };
                    var service = new google.maps.places.PlacesService(map);

                    service.nearbySearch(request, function(results, status) {
                        console.log(results)
                        if (status == google.maps.places.PlacesServiceStatus.OK) {
                            if (results[0]) {
                                var placeDetailsRequest = {
                                    placeId: results[0]
                                        .place_id // Lấy place_id của địa điểm đầu tiên trong kết quả
                                };

                                service.getDetails(placeDetailsRequest, function(place, status) {
                                    if (status === google.maps.places.PlacesServiceStatus.OK) {
                                        // Lấy địa chỉ của địa điểm
                                        var placeAddress = place.formatted_address;

                                        // Hiển thị địa chỉ trong phần tử selectedPlace
                                        console.log(placeAddress);

                                        // Hiển thị vị trí mới của marker trong console
                                        console.log('Vị trí mới: ' + newPosition.lat() + ', ' +
                                            newPosition.lng());
                                    }
                                });
                            }
                        }
                    });
                }, function() {
                    handleLocationError(true, infoWindow, map.getCenter());
                }, {
                    enableHighAccuracy: true,
                    maximumAge: 0
                });
            } else {
                alert('Trình duyệt không hỗ trợ lấy vị trí.');
            }

        }

        // Sự kiện click cho nút lấy vị trí
        document.getElementById('currentLocation').addEventListener('click', getLocation);

        function handleLocationError(browserHasGeolocation, infoWindow, pos) {
            infoWindow.setPosition(pos);
            infoWindow.setContent(browserHasGeolocation ?
                'Error: The Geolocation service failed.' :
                'Error: Your browser doesn\'t support geolocation.');
        }
    </script>

</body>

</html>
