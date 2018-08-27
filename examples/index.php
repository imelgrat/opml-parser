<?php

	use imelgrat\OPML_Parser\OPML_Parser;
	require_once ('../src/opml-parser.php');
?>
<html>
<head>
<title>OPML Parser Example</title>
</head>
<body>
<form action="index.php" method="post" name="form1" target="_self" id="form1">
  <label>URL of OPML file
  <input name="url" type="text" id="url" value="<?php
	echo ($_POST['url'] != '' ? $_POST['url'] : 'http://www.bbc.co.uk/podcasts.opml');
?>" size="60" maxlength="255"/>
  </label>
  <p>
    <label>
    <input type="submit" name="Submit" value="Submit" />
    </label>
  </p>
</form>
<p>&nbsp;</p>
<?php
	
	if ($_POST['url'] != '')
	{
		$parser = new OPML_Parser();

		$parser->ParseLocation($_POST['url'], null);

		foreach ($parser as $key => $item)
		{
			echo "<p> Item: " . $key . '</p><ul>';
			foreach ($item as $attribute => $value)
			{
				echo '<li>' . '<strong>' . $attribute . '</strong>:' . $value . '</li>';
			}
			echo '</ul>';
			echo '<p>&nbsp;</p>';

		}
	}
?>
</body>
</html>