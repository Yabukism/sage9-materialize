
/*
 * Main SyntaxHiglight object
 * Create it (ie. new SyntaxHighlight()) to replace textarea with ACE editor.
 */
function SyntaxHighlight(id, settings) {
	var textarea, editor, form, session, editDiv;

	this.textarea = textarea = jQuery(id);
	this.settings = settings || {};
	
	if (textarea.length === 0 ) { // Element does not exist
		this.valid = false;
		return;
	}

	this.valid = true;

	editDiv = jQuery('<div>', {
		position: 'absolute',
		width: textarea.width(),
		height: textarea.height(),
		'class': textarea.attr('class')
	}).insertBefore(textarea);

	textarea.css('display', 'none');

	this.editor = editor = ace.edit(editDiv[0]);
	this.form = form = textarea.closest('form');
	this.session = session = editor.getSession();
	session.setValue(textarea.val());
	editor.setStyle("ace-fix");

	// copy back to textarea on form submit...
	form.submit(function () {
		textarea.val(session.getValue());
	});

	this.setFullscreen();
	this.setMode();
	this.applySettings();
}

SyntaxHighlight.prototype.setFullscreen = function() {
	var	dom = ace.require("ace/lib/dom"),
		editor = this.editor;

	editor.commands.addCommand({
		name: "Toggle Fullscreen",
		bindKey: {win: "Ctrl-Enter", mac: "Command-Enter"},
		exec: function (editor) {
			dom.toggleCssClass(document.body, "fullScreen");
			dom.toggleCssClass(editor.container, "fullScreen-editor");
			editor.resize();
		}
	});
};

/*
 *	Sets mode (language) 
 */
SyntaxHighlight.prototype.setMode = function () {
	var	modelist = ace.require('ace/ext/modelist'),
		filePath = jQuery("input[name='ext']").attr("value"),
		mode = modelist.getModeForPath(filePath).mode;
	this.session.setMode(mode);
};

SyntaxHighlight.prototype.applySettings = function () {
	var	editor = this.editor,
		session = this.session,
		settings = this.settings;

	//editor.renderer.setShowGutter(settings['show_line_numbers'] == 1);
	//editor.setHighlightActiveLine(settings['highlight_curr_line'] == 1);
	//editor.setSelectionStyle(settings['full_line_selection'] == 1 ? "line" : "text");
	editor.setTheme("ace/theme/dawn");
	//session.setUseWrapMode(settings['word_wrap'] == 1);
	//session.setTabSize(settings['tab_size']);
	//session.setUseSoftTabs(settings['use_soft_tabs'] == 1);

	//this.setKeybinding(settings['key_bindings']);
	
	//if (settings['unsaved_changes'] == 1) {
		this.setUnsavedChangesAlert(1);
	//}

	//if (settings['ctrls_save'] == 1) {
		//this.setAjaxSave();
	//}
};

SyntaxHighlight.prototype.setKeybinding = function (keybinding) {
	switch(keybinding) {
	case 'vim':
		this.editor.setKeyboardHandler("ace/keyboard/vim");
		break;
	case 'emacs':
		this.editor.setKeyboardHandler("ace/keyboard/emacs");
		break;
	}
};

SyntaxHighlight.prototype.setUnsavedChangesAlert = function (message) {
	var that = this;

	this.changed = false;
	this.editor.on("change", function (e) {
		that.changed = true;
	});

	jQuery(window).bind('beforeunload', function (e) {
		if (that.changed) {
			return message;
		}
	});
};


/*
 * Create SyntaxHighlight objet when docuement is ready
 */
jQuery(function () {

	new SyntaxHighlight('#newcontent', null);
});
