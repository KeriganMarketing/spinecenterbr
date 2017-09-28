<?php
	//GET THE WYSISYG 
	// Include the CKEditor class.
	include_once "../editor/ckeditor.php";
	
	// Create a class instance.
	$CKEditor = new CKEditor();
	
	// Path to the CKEditor directory.
	$CKEditor->basePath = '/editor/';
	
	$CKEditor->config['filebrowserBrowseUrl'] = '/editor/filemanager/browser/default/browser.html?Connector=/editor/filemanager/connectors/php/connector.php';
	$CKEditor->config['filebrowserImageBrowseUrl'] = '/editor/filemanager/browser/default/browser.html?Type=Image&Connector=/editor/filemanager/connectors/php/connector.php';
	$CKEditor->config['filebrowserWindowWidth'] = '800';
	$CKEditor->config['filebrowserWindowHeight'] = '600';
	
	//settings
	$CKEditor->config['toolbar'] = array(
		array( 'Source' ),
		array( 'Cut','Copy','Paste','PasteText','PasteFromWord','-','Print', 'SpellChecker', 'Scayt' ),
		array( 'Undo','Redo','-','Find','Replace','-','SelectAll','RemoveFormat' ),
    	'/',
		array( 'Bold','Italic','Underline','Strike','-','Subscript','Superscript' ),
		array( 'NumberedList','BulletedList','-','Outdent','Indent' ),
		array( 'JustifyLeft','JustifyCenter','JustifyRight','JustifyBlock' ),
		array( 'Link','Unlink','Anchor' ),
    	'/',
		array( 'Image','Table','HorizontalRule','SpecialChar' ),
		array( 'Format','Font','FontSize' ),
		array( 'TextColor' )
	);
	
	$CKEditor->config['skin'] = 'office2003';

	$CKEditor->config['height'] = 400;
	
	// Replace a textarea element with an id (or name) of "textarea_id".
	$CKEditor->replace("pageContent");
?>