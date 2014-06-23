<!DOCTYPE html>
<html>
	<head>
		<meta charset="UTF-8">
		<title>Easy - API Generator</title>
	</head>
	<body>
		<form method="post" action="Download.php">
			<table>
				<tbody>
					<tr><td>Database Host:</td><td><input type="text" name="db_host" /></td></tr>
					<tr><td>Database Name:</td><td><input type="text" name="db_name" /></td></tr>
					<tr><td>Database User:</td><td><input type="text" name="db_user" /></td></tr>
					<tr><td>Database Pass:</td><td><input type="password" name="db_pass" /></td></tr>
					<tr><td>Prefix:</td><td><input type="text" name="db_prefix" /></td></tr>
					<tr><td>Compress data:</td><td><input type="checkbox" name="compress" /></td></tr>
					<tr><td colspan="2"><input type="submit" value="Download API" /></td></tr>
				</tbody>
			</table>
		</form>
	</body>
</html>