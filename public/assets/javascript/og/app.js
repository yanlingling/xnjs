var App = window.App || {};
App.engine  = {}; // engine namspace
App.modules = {}; // modules (such as AddTaskForm, AddMessageForm etc)
App.widgets = {}; // widgets (such as GroupedBlock, UserBoxMenu, PageAction)
App.engine = {
	showStatus: function(message) {
	},
	hideStatus: function() {
	}
};

// lang
if (typeof _lang != 'object') _lang = {};

function lang(name) {
	var value = _lang[name];
	if (!value) {
		return "Missing lang.js: " + name;
	}
	for (var i=1; i < arguments.length; i++) {
		value = value.replace("{" + (i-1) + "}", arguments[i]);
	}
	return value;
}

function langhtml(name) {
	return '<span name="og-lang" id="og-lang-' + name + '">' + lang(name) + '</span>';
}

function addLangs(langs) {
	for (var k in langs) {
		_lang[k] = langs[k];
	}
}
function getStrLength(str) {
    var len = str.length;
    var result = 0;
    for (var i = 0; i < len; i++) {
        if (str.charCodeAt(i) < 27 || str.charCodeAt(i) > 126) {
            result += 2;
        } else {
            result++;
        }
    }
    return result;
}