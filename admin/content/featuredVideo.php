<?php
$query = "SELECT * FROM featuredVideo LIMIT 1";
$result = mysql_query($query);
$record = mysql_fetch_assoc($result);
?>
<h1>Manage Your Featured Video:</h1>
<?php
if($error != ''){
    echo '<p class="error" >'.$error.'</p>';
}
if($msg != ''){
    echo '<p class="success" >'.$msg.'</p>';
}
echo $body; ?>
<form action="<?php echo $pageName; ?>" enctype="multipart/form-data" method="post">
    <label>YouTube Code:<span class="small">(i.e.http://www.youtube.com/watch?v=<strong>*this*</strong> )</span><br />
    <input class="text" type="text" value="<?php echo $record['url']; ?>" name="url" /></label>
    <label>Title:<br />
    <input class="text" type="text" value="<?php echo $record['title']; ?>" name="title" /></label>
    <label>Description:<br />
    <textarea name="description" class="textarea"><?php echo stripslashes($record['description']); ?></textarea></label>
    <label>Runtime:<span class="small">(MM:SS)</span><br />
    <input type="text" class="short-text" value="<?php echo $record['runtime']; ?>" name="runtime" /></label>
    <input type="hidden" value="process" name="cmd" />
	<input type="submit" class="submit" value="Submit" />
</form>
<div class="clear"></div>