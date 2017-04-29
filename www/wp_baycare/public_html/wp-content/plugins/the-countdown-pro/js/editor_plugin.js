/*
    The Countdown Pro Shortcode
    Copyright 2014 zourbuth.com (email : zourbuth@gmail.com)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License, version 2, as 
    published by the Free Software Foundation.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
	
	http://wiki.moxiecode.com/index.php/TinyMCE:Create_plugin/3.x#Creating_your_own_plugins
*/

(function() {
	tinymce.create('tinymce.plugins.tcpsc', {		
		init : function(ed, url) {
			ed.addCommand('tcpsc', function() {
				if ( typeof(tcpShortcode) != 'undefined' ) {
					tcpShortcode.open();
					return;
				}				
			});

			ed.addButton('tcpsc', {
				title : 'Countdown',
				cmd : 'tcpsc'
			});
		},
		createControl : function(n, cm) {
			return null;
		},
		getInfo : function() {
			return {
				longname  : 'tcpsc',
				author 	  : 'zourbuth',
				authorurl : 'http://www.zourbuth.com',
				infourl   : 'http://www.zourbuth.com',
				version   : "1.0"
			};
		}
	});
	
	tinymce.PluginManager.add('tcpsc', tinymce.plugins.tcpsc);	// Register plugin
})();