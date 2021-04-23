function editModule() {
	var id = <?php echo $id; ?>;

	var placeSelect = document.getElementById('placeSelect');
	var places = placeSelect.options;
	for (var opt, j = 0; opt = places[j]; j++) {
		if (opt.value == "<?php echo $place ?>") {
		  placeSelect.selectedIndex = j;
		  break;
		}			
	}	

	var typeSelect = document.getElementById('typeSelect');
	var type = typeSelect.options;
	for (var opt, j = 0; opt = type[j]; j++) {
		if (opt.value == "<?php echo $type ?>") {
		  typeSelect.selectedIndex = j;
		  break;
		}
	}

	var statusSelect = document.getElementById('statusSelect');
	var status = statusSelect.options;
	for (var opt, j = 0; opt = status[j]; j++) {
		if (opt.value == "<?php echo $status ?>") {
		  statusSelect.selectedIndex = j;
		  break;
		}
	}

	var blank = '';
	var description = '<?php echo $description; ?>'.concat('', blank);
	if (description) {
		document.getElementById('descriptionField').value = description;	
	}

	var temperature = 0<?php echo $temperature; ?>;
	if (temperature) {
		document.getElementById('temperatureCheck').checked = true;
		document.getElementById('temperatureRow').style.display = "";
		document.getElementById('temperatureInput').value = temperature;	
	}

	var humidity = 0<?php echo $humidity; ?>;
	if (temperature) {
		document.getElementById('humidityCheck').checked = true;
		document.getElementById('humidityRow').style.display = "";
		document.getElementById('humidityInput').value = humidity;			
	}

	var wind = 0<?php echo $wind; ?>;
	if (wind) {
		document.getElementById('windCheck').checked = true;
		document.getElementById('windRow').style.display = "";
		document.getElementById('windInput').value = wind;	
	}
		
}