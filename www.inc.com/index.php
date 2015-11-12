<html>
	<head>
		<script src="http://ajax.googleapis.com/ajax/libs/jquery/1/jquery.min.js"></script>
		<script src="js/knockout-3.3.0.js"></script>
		<script src="js/knockout.mapping.js"></script>
		<script src="js/knockout.simpleGrid.js"></script>
		<script src="application.js"></script>
		<title></title>
	</head>
	<body>
		<script type="text/html" id="TableRow">
			<tr>
				<td data-bind="text: company"></td>
				<td data-bind="text: city"></td>
				<td data-bind="text: growth"></td>
				<td data-bind="text: revenue"></td>
				<td data-bind="text: workers"></td>
			</tr>
		</script>
		
		<table>
			<thead>
				<tr>
					<th>	Company name</th>
					<th>	city</th>
					<th>	growth</th>
					<th>	revenue</th>
					<th>	Workers</th>
				</tr>
			</thead>
			<tbody data-bind="template: {
				name: 'TableRow',
				foreach: firms
			}">
			</tbody>
		</table>
	</body>
</html>