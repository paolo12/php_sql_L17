<?php
require '/vendor/autoload.php';
$api = new \Yandex\Geo\Api();
?>

<html>
<head>
	<title>Найти адрес</title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <!-- Если вы используете API локально, то в URL ресурса необходимо указывать протокол в стандартном виде (http://...)-->
    <script src="//api-maps.yandex.ru/2.1/?lang=ru_RU" type="text/javascript"></script>
    <script src="icon_customImage.js" type="text/javascript"></script>
	<style>
        #map {
            width: 300px; 
			height: 300px;
			padding: 0; 
			margin: 0;
        }
		table { 
			border-spacing: 0;
			border-collapse: collapse;
		}

		table td, table th {
			border: 1px solid #ccc;
			padding: 5px;
		}
		
		table th {
			background: #eee;
		}

		#center {
			text-align: center;
		}	
    </style>
<html> 
<head>


</head> 
<body>
<h2>Найти адрес</h2>
<div>
    <form method="POST">
        <input type="text" name="adress" placeholder="Введите адрес" value="" />
        <input type="submit" name="search" value="Искать!" />
    </form>
</div>

<div>
<?php
if(!empty($_POST['adress'])){
	// Или можно икать по адресу
	$api->setQuery(strval($_POST['adress']));

	// Настройка фильтров
	$api
		->setLang(\Yandex\Geo\Api::LANG_RU) // локаль ответа
		->load();

	$response = $api->getResponse();
	$response->getFoundCount(); // кол-во найденных адресов
	$response->getQuery(); // исходный запрос
	$response->getLatitude(); // широта для исходного запроса
	$response->getLongitude(); // долгота для исходного запроса

	echo '<br><div>';
	echo 'Список найденных адресов:';
	echo '</div>';
	echo '<table>
		<col width="350px" align="justify" valign="middle">
		<col width="350px" valign="top">
		<tr>
			<th>Адрес</th>
			<th>Карта</th>
		</tr>';
	// Список найденных точек
	$collection = $response->getList();
	foreach ($collection as $item) {
		echo '<tr height = "300px";><td>';
		echo 'Адрес: '.$item->getAddress(); // вернет адрес
		echo '<br>';	
		echo 'Широта: '.$item->getLatitude(); // широта
		echo '<br>';	
		echo 'Долгота: '.$item->getLongitude(); // долгота
		echo '</td><td>';
		echo '<div id="map">
			<script>
			ymaps.ready(init);

			function init () {
				var myMap = new ymaps.Map("map", {
						center: ['.$item->getLatitude().','.$item->getLongitude().'],
						zoom: 5
					}, {
						searchControlProvider: "yandex#search"
					}),
					myPlacemark = new ymaps.Placemark(['.$item->getLatitude().','.$item->getLongitude().'], {
						balloonContentBody: "'.$item->getAddress().'"
					});

				myMap.geoObjects.add(myPlacemark);

				// Открываем балун на карте (без привязки к геообъекту).
				myMap.balloon.open(['.$item->getLatitude().','.$item->getLongitude().'], "'.$item->getAddress().'", {
					closeButton: false
				});
			}
			</script>';
		echo '</div>';
		echo '</td></tr>';
	}
	echo '</table>';
}
?>
</div>
</table>
</body> 
</html>