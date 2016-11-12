//STATIC
//CKEditor OW functions
//window.htmlAreaDataDefaults = {"editorCss":"http:\/\/whuw-one\/dev\/social\/ow\/ow_static\/plugins\/base\/css\/htmlarea_editor.css","themeImagesUrl":"http:\/\/whuw-one\/dev\/social\/ow\/ow_static\/themes\/macabre\/images\/","imagesUrl":"http:\/\/whuw-one\/dev\/social\/ow\/base\/media-panel\/index\/pluginKey\/blog\/id\/__id__\/","labels":{"buttons":{"bold":"Bold","italic":"Italic","underline":"Underline","orderedlist":"Insert Ordered List","unorderedlist":"Insert Unordered List","link":"Insert Link","image":"Insert Image","video":"Insert Video","html":"Insert HTML","more":"More","switchHtml":"Show\/Hide HTML Source View"},"common":{"buttonAdd":"Add","buttonInsert":"Insert","videoHeadLabel":"Insert video","htmlHeadLabel":"Insert HTML","htmlTextareaLabel":"Your html code:","videoTextareaLabel":"Embed your video code here:","linkTextLabel":"Text to display:","linkUrlLabel":"To what URL should this link go:","linkNewWindowLabel":"Open in new window"},"messages":{"imagesEmptyFields":"base+ws_image_empty_fields","linkEmptyFields":"Please fill `label` and `url` fields to insert the link","videoEmptyField":"Enter video code please"}},"buttonCode":"<span class=\"ow_button ow_ic_add mn_submit\"><span><input type=\"button\"  value=\"#label#\" class=\"ow_ic_add mn_submit\"  \/><\/span><\/span>","rtl":false};
//if (!window.htmlAreaData) {
//	window.htmlAreaData=window.htmlAreaDataDefaults;
//}
function iisadvanceeditor_get_ow_button_label(key,defaultLabel){
if (window.htmlAreaData && window.htmlAreaData.labels && window.htmlAreaData.labels.buttons && window.htmlAreaData.labels.buttons[key]) {
	return window.htmlAreaData.labels.buttons[key];
}
return defaultLabel;
}
CKEDITOR.plugins.add('ow_image',{
	init:function(editor){
		editor.addCommand('ow_image',{
			exec:function(editor){
				editor.element.$.jhtmlareaObject.image();
			}
		});
		editor.ui.addButton('ow_image',{
		label:iisadvanceeditor_get_ow_button_label('image', 'Insert Image'),
		command:'ow_image'
		});
	}
});
CKEDITOR.plugins.add('ow_video',{
	init:function(editor){
		editor.addCommand('ow_video',{
			exec:function(editor){
				editor.element.$.jhtmlareaObject.video();
			}
		});
		editor.ui.addButton('ow_video',{
		label:iisadvanceeditor_get_ow_button_label('video', 'Insert Video'),
		command:'ow_video'
		});
	}
});
CKEDITOR.plugins.add('ow_more',{
	init:function(editor){
		editor.addCommand('ow_more',{
			exec:function(editor){
				editor.element.$.jhtmlareaObject.more();
			}
		});
		editor.ui.addButton('ow_more',{
		label:iisadvanceeditor_get_ow_button_label('more', 'More'),
		command:'ow_more'
		});
	}
});

//override htmlarea
$.fn.htmlarea = function(opts) {
return this.each(function() {
    new jHtmlAreaCKWrapper(this, opts);
});
};
var jHtmlAreaCKWrapper = window.jHtmlAreaCKWrapper = function(elem, options) {
if (elem.jquery) {
    return jHtmlAreaCKWrapper(elem[0], options);
}
if (elem.jhtmlareaObject) {
    return elem.jhtmlareaObject;
} else {
    return new jHtmlAreaCKWrapper.fn.init(elem, options);
}
};
jHtmlAreaCKWrapper.fn=jHtmlAreaCKWrapper.prototype={
	index:0,
	init: function(elem, options) {
		elem.jhtmlareaObject = this;
		elem.htmlareaFocus = function () {console.log('focus');};
		elem.htmlareaRefresh = function () {console.log('refresh');};
		this.elem=elem;
		this.initCK(elem, options);
	},
	initCK: function(elem, options) {
		var element=$(elem);
		var element_id=element.prop('id');
		if (element_id==undefined || element_id.length==0) {
			element_id='SITE-CKEditor-'+this.index;
			this.index++;
			element.attr('id',element_id);
		}

		//adjust toolbar to match desired options
		var config=window.CKCONFIG;
		/*if (options.toolbar) {
			config.toolbar+='Dynamic';
			var toolbarName='toolbar_'+config.toolbar;
			if (config[toolbarName]===undefined) {
				config[toolbarName]=[[]];
			}
			var configToolbar=config[toolbarName][0];
			//config[toolbarName].push([]);
			//var configToolbar=config[toolbarName][1];

			var index=$.inArray('orderedlist',options.toolbar);
			if (index>-1) {
				configToolbar.push('NumberedList');
			}
			index=$.inArray('unorderedlist',options.toolbar);
			if (index>-1) {
				configToolbar.push('BulletedList');
			}
			index=$.inArray('more',options.toolbar);
			if (index>-1) {
				configToolbar.push('ow_more');
			}
			index=$.inArray('link',options.toolbar);
			if (index>-1) {
				configToolbar.push('Link');
			}
			index=$.inArray('image',options.toolbar);
			if (index>-1) {
				configToolbar.push('ow_image');
			}
			index=$.inArray('video',options.toolbar);
			if (index>-1) {
				configToolbar.push('ow_video');
			}
			index=$.inArray('switchHtml',options.toolbar);
			if (index>-1) {
				configToolbar.push('Source');
			}
			configToolbar.push('Undo');
		}*/
		config.toolbarGroups = [
			{ name: 'document', groups: [ 'mode', 'document', 'doctools' ] },
			{ name: 'clipboard', groups: [ 'clipboard', 'undo' ] },
			{ name: 'editing', groups: [ 'find', 'selection', 'spellchecker', 'editing' ] },
			{ name: 'forms', groups: [ 'forms' ] },
			'/',
			{ name: 'basicstyles', groups: [ 'basicstyles', 'cleanup' ] },
			{ name: 'paragraph', groups: [ 'list', 'indent', 'blocks', 'align', 'bidi', 'paragraph' ] },
			{ name: 'links', groups: [ 'links' ] },
			{ name: 'insert', groups: [ 'insert' ] },
			'/',
			{ name: 'styles', groups: [ 'styles' ] },
			{ name: 'colors', groups: [ 'colors' ] },
			{ name: 'tools', groups: [ 'tools' ] },
			{ name: 'others', groups: [ 'others' ] },
			{ name: 'about', groups: [ 'about' ] }
		];

		config.removeButtons = 'Styles,Format,Table,About,Language,PageBreak,Iframe,HorizontalRule,Anchor,Flash,CreateDiv,HiddenField,ImageButton,Textarea,Form,Checkbox,Radio,TextField,Select,Button,Scayt,SelectAll,Save,NewPage,Preview,Print,Templates,Font';

		//init CK
		this.editor = CKEDITOR.replace( element_id, config);
		var editor=this.editor;

		//too late 
		//this.editor.on('blur',function(e){ editor.updateElement(); /*element.val(editor.getData());*/console.log(element.val())},null,null,1);
		iisadvanceeditor_form_refresh_before_submit(element.parents('form').get(0),editor);
	},
        insertImage: function(params){
            //this.restoreCaret();
            if( params.preview ){
                $html = $('<div><a href="'+params.src+'" target="_blank"><img style="padding:5px;" src="'+params.src+'" /></a></div>');
            }else{
                $html = $('<div><img style="padding:5px;" src="'+params.src+'" /></div>');
            }

            $img = $('img', $html);
            if( params.align ){
                $img.css({
                    'float':params.align
                });
            }
            if( params.resize ){
                $img.css({
                    'width':params.resize
                });
            }
            this.pasteHTML($html.html());
            this.tempFB.close();
        },
        pasteHTML: function(html) {
            this.editor.insertHtml( html, 'unfiltered_html');
	},
        image: function(){
            this.tempFB = new OW_FloatBox({
                $title: iisadvanceeditor_get_ow_button_label('image', 'Insert Image'),
                width: '600px',
                height: '100%',
                $contents: '<center><iframe style="min-width: 550px; min-height: 500px;" src="'+this.editor.config.ow_imagesUrl.replace('__id__', this.editor.element.getAttribute('id'))+'"></iframe></center>'
            });
        },
        video: function(){
            //this.saveCaret();
            var self = this;
            var $contents = $('<div>'+(window.htmlAreaData.labels.common.videoTextareaLabel || '') +'<br /><textarea name="code" style="height:200px;"></textarea><br /><br /></div>');
            var buttonCode = window.htmlAreaData.buttonCode;
            $contents.append('<div style="text-align:center;">'+buttonCode.replace('#label#', window.htmlAreaData.labels.common.buttonInsert)+'</div>');
            $('input[type=button].mn_submit', $contents).click(function(){
                self.insertVideo({
                    code:$('textarea[name=code]', $contents).val()
                })
            });

            this.tempFB = new OW_FloatBox({
                $title: window.htmlAreaData.labels.common.videoHeadLabel || '',
                width: '600px',
                height: '400px',
                $contents: $contents
            });
        },

        insertVideo: function(params){
            //this.restoreCaret();
            if( !params || !params.code ){
                OW.error(window.htmlAreaData.labels.messages.videoEmptyField);
                return;
            }
            $html = $('<div><span class="ow_ws_video"></span></div>');
            $('span', $html).append(params.code);
            this.pasteHTML($html.html());
            this.tempFB.close();
        },
        more: function(){
            $html = $('<div></div>');
            $html.append(document.createTextNode('<!--more-->'));
            this.pasteHTML($html.html());
        }
};
jHtmlAreaCKWrapper.fn.init.prototype = jHtmlAreaCKWrapper.fn;

//element already on the page
function iisadvanceeditor_textarea_attach(elem){
	var element=$(elem);
	if (elem.htmlarea==undefined) {
		elem.htmlarea = function(){ element.htmlarea( {'size':300} );};
		elem.htmlarea();
	}
}
function iisadvanceeditor_textarea_check(){
	$('textarea#post_body,textarea[name=body],textarea[name=intro],textarea[name=maintenance_text]').each(function(index){
		iisadvanceeditor_textarea_attach(this);
	/*
		//if (element.htmlarea) {
			var element_id=element.prop('id');
			if (element_id==undefined) {
				element_id='SITE-CKEditor-'+index;
				element.attr('id',element_id);
			}
			var editor = CKEDITOR.replace( element_id, CKCONFIG );
			new jHtmlAreaCKWrapper(\$('#'+element_id).get(0));
			element.get(0).jhtmlareaObject.editor = editor;
		//}
	*/
	});
}

//fix for CK for ajax submitting before CK update happens
function iisadvanceeditor_form_refresh_before_submit(form,editor){
	if (form)
	for (var name in window.owForms){
		var thisForm=window.owForms[name]
		if (thisForm.form && thisForm.form===form) {
			$(thisForm.form).unbind('submit').bind('submit',{form:thisForm},function(e){
				editor.updateElement();
				return e.data.form.submitForm();
			});
			break;
		}
	}
}

//Drag and drop:customizing pages popup
if (window.OW_Components_DragAndDropAjaxHandler) {
	OW_Components_DragAndDropAjaxHandler.prototype.original_loadSettings=OW_Components_DragAndDropAjaxHandler.prototype.loadSettings;
	OW_Components_DragAndDropAjaxHandler.prototype.loadSettings=function(id, successFunction) {
		this.original_loadSettings(id, function(settingMarkup){successFunction(settingMarkup);iisadvanceeditor_load_after_loadSettings(id); });
	};
	function iisadvanceeditor_load_after_loadSettings(id){
		$('.floatbox_container textarea').each(function(index){
			var element=$(this);
			iisadvanceeditor_textarea_attach(this);
			var editor=this.jhtmlareaObject.editor;
			
		//	var element_id=id+index;
//			element.attr('id',element_id);
//			var editor = CKEDITOR.replace( element_id, CKCONFIG );
			//editor.on('blur',function(e){ editor.updateElement(); element.val(editor.getData());console.log(element.val())},null,null,1);
			//$(this).parents('.settings_form').bind('submit',function(){ editor.updateElement(); });

			$('.floatbox_container input.dd_save').off('click').on('click',function(){ editor.updateElement();element.parents('.settings_form').submit(); });

			//iisadvanceeditor_form_refresh_before_submit(element.parents('form').get(0),editor);
		});
	};
}

/*
if (window.massMailing) {
	massMailing.addVarOriginal=massMailing.addVar;
	massMailing.addVar=function($varname){
		editor.element.$.jhtmlareaObject.pasteHTML($varname);
	};
}
*/
