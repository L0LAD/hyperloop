function searchTable() {
  var input, filter, table, tr, td1, td0, i, txtValue0, txtValue1;
  input = document.getElementById("searchBar");
  filter = input.value.toLowerCase();
  table = document.getElementById("table");
  tr = table.getElementsByTagName("tr");
  //Loop through all table rows, and hide those who don't match the search query
  for (i = 0; i < tr.length; i++) {
    td0 = tr[i].getElementsByTagName("td")[0];
    td1 = tr[i].getElementsByTagName("td")[1];
    if (td1 || td0) {
      txtValue0 = td0.textContent || td0.innerText;
      txtValue1 = td1.textContent || td1.innerText;
      if (txtValue0.toLowerCase().indexOf(filter) > -1) {	//Check if the value equals any module's code
        tr[i].style.display = "";
      } else if (txtValue1.toLowerCase().indexOf(filter) > -1) {	//Check if the value equals any module's place
      	tr[i].style.display = "";
      } else {							//Otherwise the module shouldn't be displayed
        tr[i].style.display = "none";
      }
    }
  }
}

function changeOrder(col) {
	var column = document.getElementById("table").getElementsByTagName("th")[col];
	var order = column.getAttribute("value");
	if (order == 'a') {
		order = 'z';
	} else if (order == 'z') {
		order = 'a';
	}
	column.setAttribute("value", order);
	return order;
}

function sort(col,type) {
	var table, rows, switching, i, x, y, order;
	order = changeOrder(col);
	table = document.getElementById("table");
	switching = true;
	/* Make a loop that will continue until
	no switching has been done : */
	while (switching) {
		// Start by saying: no switching is done : 
		switching = false;
		rows = table.rows;
		/* Loop through all table rows except the headers : */
		for (i = 1; i < (rows.length - 1); i++) {
			/* Get the two elements to compare,
			one from current row and one from the next : */
			x = rows[i].getElementsByTagName("td")[col];
			console.log(x);
			y = rows[i + 1].getElementsByTagName("td")[col];
			// Check if the two rows should switch place according to the desired order :
			shouldSwitch = isSortable(x,y,order,type);
			if (shouldSwitch) {
			  /* If a switch has been marked, make the switch
			  and mark that a switch has been done : */
			  rows[i].parentNode.insertBefore(rows[i + 1], rows[i]);
			  switching = true;
			}
		}
	}
}

function isSortable(x,y,order,type) {
	console.log(y);
	var shouldSwitch = false;
	//Comparaisons aren't the same for all types of data
	if (type == 'letter') {
		if (x.innerHTML.toLowerCase() > y.innerHTML.toLowerCase() && order == 'a'
		  	|| x.innerHTML.toLowerCase() < y.innerHTML.toLowerCase() && order == 'z') {
		    shouldSwitch = true;
		}
	} else if (type == 'number') {
		if (Number(x.innerHTML) > Number(y.innerHTML) && order == 'a'
		  	|| Number(x.innerHTML) < Number(y.innerHTML) && order == 'z') {
			shouldSwitch = true;
		}
	} else if (type == 'date') {
		xDate = convertFromDate(x.innerHTML);
		yDate = convertFromDate(y.innerHTML);
		if (Number(xDate) > Number(yDate) && order == 'a'
		  	|| Number(xDate) < Number(yDate) && order == 'z') {
			shouldSwitch = true;
		}
	}  else if (type == 'datetime') {
		xDate = convertFromDateTime(x.innerHTML);
		yDate = convertFromDateTime(y.innerHTML);
		if (Number(xDate) > Number(yDate) && order == 'a'
		  	|| Number(xDate) < Number(yDate) && order == 'z') {
			shouldSwitch = true;
		}
	}
	return shouldSwitch;
}

function convertFromDateTime(datetime) {
	var date = datetime.split(" ")[0];
	var time = datetime.split(" ")[1];
	var d = convertFromDate(date);
	var t = convertFromTime(time);
	return +(d+t);
}

function convertFromDate(date) {
	var d = date.split("-");
	return +(d[0]+d[1]+d[2]);
}

function convertFromTime(time) {
	var t = time.split(":");
	return +(t[0]+t[1]+t[2]);
}