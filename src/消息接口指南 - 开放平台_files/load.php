var isCompatible=function(){if(navigator.appVersion.indexOf('MSIE')!==-1&&parseFloat(navigator.appVersion.split('MSIE')[1])<6){return false;}return true;};var startUp=function(){mediaWiki.loader.register([["site","1363690916",[],"site"],["startup","20130319110200",[],"startup"],["user","1363690916",[],"user"],["user.options","1363690916",[],"private"],["skins.vector",1363690916,[]],["skins.monobook",1363690916,[]],["skins.simple",1363690916,[]],["skins.chick",1363690916,[]],["skins.modern",1363690916,[]],["skins.cologneblue",1363690916,[]],["skins.nostalgia",1363690916,[]],["skins.standard",1363690916,[]],["jquery",1363690916,[]],["jquery.async",1363690916,[]],["jquery.autoEllipsis",1363690916,["jquery.highlightText"]],["jquery.checkboxShiftClick",1363690916,[]],["jquery.client",1363690916,[]],["jquery.collapsibleTabs",1363690916,[]],["jquery.color",1363690916,[]],["jquery.cookie",1363690916,[]],["jquery.delayedBind",1363690916,[]],["jquery.expandableField",1363690916,[]],[
"jquery.highlightText",1363690916,[]],["jquery.placeholder",1363690916,[]],["jquery.localize",1363690916,[]],["jquery.suggestions",1363690916,["jquery.autoEllipsis"]],["jquery.tabIndex",1363690916,[]],["jquery.textSelection",1363690916,[]],["jquery.tipsy",1363690916,[]],["jquery.ui.core",1363690916,["jquery"]],["jquery.ui.widget",1363690916,[]],["jquery.ui.mouse",1363690916,["jquery.ui.widget"]],["jquery.ui.position",1363690916,[]],["jquery.ui.draggable",1363690916,["jquery.ui.core","jquery.ui.mouse","jquery.ui.widget"]],["jquery.ui.droppable",1363690916,["jquery.ui.core","jquery.ui.mouse","jquery.ui.widget","jquery.ui.draggable"]],["jquery.ui.resizable",1363690916,["jquery.ui.core","jquery.ui.widget","jquery.ui.mouse"]],["jquery.ui.selectable",1363690916,["jquery.ui.core","jquery.ui.widget","jquery.ui.mouse"]],["jquery.ui.sortable",1363690916,["jquery.ui.core","jquery.ui.widget","jquery.ui.mouse"]],["jquery.ui.accordion",1363690916,["jquery.ui.core","jquery.ui.widget"]],[
"jquery.ui.autocomplete",1363690916,["jquery.ui.core","jquery.ui.widget","jquery.ui.position"]],["jquery.ui.button",1363690916,["jquery.ui.core","jquery.ui.widget"]],["jquery.ui.datepicker",1363690916,["jquery.ui.core"]],["jquery.ui.dialog",1363690916,["jquery.ui.core","jquery.ui.widget","jquery.ui.button","jquery.ui.draggable","jquery.ui.mouse","jquery.ui.position","jquery.ui.resizable"]],["jquery.ui.progressbar",1363690916,["jquery.ui.core","jquery.ui.widget"]],["jquery.ui.slider",1363690916,["jquery.ui.core","jquery.ui.widget","jquery.ui.mouse"]],["jquery.ui.tabs",1363690916,["jquery.ui.core","jquery.ui.widget"]],["jquery.effects.core",1363690916,["jquery"]],["jquery.effects.blind",1363690916,["jquery.effects.core"]],["jquery.effects.bounce",1363690916,["jquery.effects.core"]],["jquery.effects.clip",1363690916,["jquery.effects.core"]],["jquery.effects.drop",1363690916,["jquery.effects.core"]],["jquery.effects.explode",1363690916,["jquery.effects.core"]],["jquery.effects.fold",
1363690916,["jquery.effects.core"]],["jquery.effects.highlight",1363690916,["jquery.effects.core"]],["jquery.effects.pulsate",1363690916,["jquery.effects.core"]],["jquery.effects.scale",1363690916,["jquery.effects.core"]],["jquery.effects.shake",1363690916,["jquery.effects.core"]],["jquery.effects.slide",1363690916,["jquery.effects.core"]],["jquery.effects.transfer",1363690916,["jquery.effects.core"]],["mediawiki",1363690916,[]],["mediawiki.util",1363690916,["jquery.checkboxShiftClick","jquery.client","jquery.placeholder"]],["mediawiki.action.history",1363690916,["mediawiki.legacy.history"]],["mediawiki.action.edit",1363690916,[]],["mediawiki.action.view.rightClickEdit",1363690916,[]],["mediawiki.special.preferences",1363690916,[]],["mediawiki.special.search",1363690916,[]],["mediawiki.language",1363690916,[]],["mediawiki.legacy.ajax","20130319110200",["mediawiki.legacy.wikibits"]],["mediawiki.legacy.ajaxwatch",1363690916,["mediawiki.legacy.wikibits"]],["mediawiki.legacy.block",
1363690916,["mediawiki.legacy.wikibits"]],["mediawiki.legacy.commonPrint",1363690916,[]],["mediawiki.legacy.config",1363690916,["mediawiki.legacy.wikibits"]],["mediawiki.legacy.diff",1363690916,["mediawiki.legacy.wikibits"],"mediawiki.action.history"],["mediawiki.legacy.edit",1363690916,["mediawiki.legacy.wikibits"]],["mediawiki.legacy.enhancedchanges",1363690916,["mediawiki.legacy.wikibits"]],["mediawiki.legacy.history",1363690916,["mediawiki.legacy.wikibits"],"mediawiki.action.history"],["mediawiki.legacy.htmlform",1363690916,["mediawiki.legacy.wikibits"]],["mediawiki.legacy.IEFixes",1363690916,["mediawiki.legacy.wikibits"]],["mediawiki.legacy.metadata",1363690916,["mediawiki.legacy.wikibits"]],["mediawiki.legacy.mwsuggest",1363690916,["mediawiki.legacy.wikibits"]],["mediawiki.legacy.prefs",1363690916,["mediawiki.legacy.wikibits","mediawiki.legacy.htmlform"]],["mediawiki.legacy.preview",1363690916,["mediawiki.legacy.wikibits"]],["mediawiki.legacy.protect",1363690916,[
"mediawiki.legacy.wikibits"]],["mediawiki.legacy.search",1363690916,["mediawiki.legacy.wikibits"]],["mediawiki.legacy.shared",1363690916,[]],["mediawiki.legacy.oldshared",1363690916,[]],["mediawiki.legacy.upload",1363690916,["mediawiki.legacy.wikibits"]],["mediawiki.legacy.wikibits","20130319110200",["mediawiki.language"]],["mediawiki.legacy.wikiprintable",1363690916,[]]]);mediaWiki.config.set({"wgLoadScript":"/wiki/load.php","debug":false,"skin":"vector","stylepath":"/wiki/skins","wgUrlProtocols":"http\\:\\/\\/|https\\:\\/\\/|ftp\\:\\/\\/|irc\\:\\/\\/|gopher\\:\\/\\/|telnet\\:\\/\\/|nntp\\:\\/\\/|worldwind\\:\\/\\/|mailto\\:|news\\:|svn\\:\\/\\/|git\\:\\/\\/|mms\\:\\/\\/","wgArticlePath":"/wiki/index.php?title=$1","wgScriptPath":"/wiki","wgScriptExtension":".php","wgScript":"/wiki/index.php","wgVariantArticlePath":false,"wgActionPaths":[],"wgServer":"http://mp.weixin.qq.com","wgUserLanguage":"zh-cn","wgContentLanguage":"zh-cn","wgVersion":"1.17.0","wgEnableAPI":true,"wgEnableWriteAPI"
:true,"wgSeparatorTransformTable":["",""],"wgDigitTransformTable":["",""],"wgMainPageTitle":"首页","wgFormattedNamespaces":{"-2":"媒体文件","-1":"特殊","0":"","1":"讨论","2":"用户","3":"用户讨论","4":"开放平台","5":"开放平台讨论","6":"文件","7":"文件讨论","8":"MediaWiki","9":"MediaWiki讨论","10":"模板","11":"模板讨论","12":"帮助","13":"帮助讨论","14":"分类","15":"分类讨论"},"wgNamespaceIds":{"媒体文件":-2,"特殊":-1,"":0,"讨论":1,"用户":2,"用户讨论":3,"开放平台":4,"开放平台讨论":5,"文件":6,"文件讨论":7,"mediawiki":8,"mediawiki讨论":9,"模板":10,"模板讨论":11,"帮助":12,"帮助讨论":13,"分类":14,"分类讨论":15,"媒体":-2,"对话":1,"用户对话":3,"图像":6,"档案":6,"image":6,"image_talk":7,"图像对话":7,"图像讨论":7,"档案对话":7,"档案讨论":7,"文件对话":7,"模板对话":11,"帮助对话":13,"分类对话":15},"wgSiteName":"开放平台","wgFileExtensions":["png","gif",
"jpg","jpeg","zip"],"wgDBname":"doc_wiki","wgExtensionAssetsPath":"/wiki/extensions","wgResourceLoaderMaxQueryLength":-1});};if(isCompatible()){document.write("\x3cscript src=\"/wiki/load.php?debug=false\x26amp;lang=zh-cn\x26amp;modules=jquery%7Cmediawiki\x26amp;only=scripts\x26amp;skin=vector\x26amp;version=20130319T110140Z\"\x3e\x3c/script\x3e");}delete isCompatible;;