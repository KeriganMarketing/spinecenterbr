/**
 * @license Copyright (c) 2003-2013, CKSource - Frederico Knabben. All rights reserved.
 * For licensing, see LICENSE.html or http://ckeditor.com/license
 */

CKEDITOR.editorConfig = function( config ) {
	skin = 'office2013',
	config.plugins = 'dialogui,dialog,about,a11yhelp,dialogadvtab,basicstyles,bidi,blockquote,clipboard,button,panelbutton,panel,floatpanel,colorbutton,colordialog,templates,menu,contextmenu,div,resize,toolbar,elementspath,enterkey,entities,popup,filebrowser,find,fakeobjects,flash,floatingspace,listblock,richcombo,font,forms,format,horizontalrule,htmlwriter,iframe,wysiwygarea,image,indent,indentblock,indentlist,smiley,justify,menubutton,language,link,list,liststyle,magicline,maximize,newpage,pagebreak,pastetext,pastefromword,preview,print,removeformat,save,selectall,showblocks,showborders,sourcearea,specialchar,scayt,stylescombo,tab,table,tabletools,undo,wsc,autogrow,lineutils,widget,codesnippet,iframedialog,oembed,wordcount,pbckcode,gg,imageresize,tableresize,backgrounds';
	config.allowedContent = true;
	config.scayt_autoStartup = true;
    config.entities = false;
	config.toolbar = [
			[ 'Source', '-', 'Cut','Copy','Paste','PasteText','PasteFromWord','-','Print', 'SpellChecker', 'Scayt' ],
			[ 'Undo','Redo','-','Find','Replace','-','SelectAll','RemoveFormat' ],
			'/',
			[ 'Bold','Italic','Underline','Strike','-','Subscript','Superscript' ],
			[ 'NumberedList','BulletedList','-','Outdent','Indent' ],
			[ 'JustifyLeft','JustifyCenter','JustifyRight','JustifyBlock' ],
			[ 'Link','Unlink','Anchor' ],
			'/',
			[ 'Image','oembed','Table','HorizontalRule','SpecialChar' ],
			[ 'Format','Font','FontSize','TextColor' ]
		];
};
