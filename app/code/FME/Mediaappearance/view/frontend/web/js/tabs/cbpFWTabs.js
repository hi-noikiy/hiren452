

/**
 * cbpFWTabs.js v1.0.0
 * http://www.codrops.com
 *
 * Licensed under the MIT license.
 * http://www.opensource.org/licenses/mit-license.php
 *
 * Copyright 2014, Codrops
 * http://www.codrops.com
 */
define(function ( window ) {
    
    'use strict';


    function extend( a, b )
    {
        for (var key in b) {
            if (b.hasOwnProperty(key) ) {
                a[key] = b[key];
            }
        }
        return a;
    }

    function CBPFWTabs( el, options )
    {
        this.el = el;
        this.options = extend({}, this.options);
        extend(this.options, options);
        this._init();
    }

    CBPFWTabs.create = function (el, options) {
        var result = new CBPFWTabs(el, options);
        return result;
    };
    CBPFWTabs.prototype = {


        constructor: CBPFWTabs,

        options : function () {
            start : 0
        },

        _init : function () {
            // tabs elemes
            this.tabs = [].slice.call(this.el.querySelectorAll('nav > ul > li'));
            // content items

            this.items = [].slice.call(this.el.querySelectorAll('.content > section'));
            // current index
            this.current = -1;
            // show current content item
            this._show();
            // init events
            this._initEvents();
        },

        _initEvents : function () {
            var self = this;
            this.tabs.forEach(function ( tab, idx ) {
                tab.addEventListener('click', function ( ev ) {
                    /*alert('Click');*/
                    ev.preventDefault();
                    var url = document.getElementById('tab'+idx).value;
                    document.location = url;
                    /*self._show( idx );*/
                });
            });
        },

        _show : function ( idx ) {
            /*if( this.current >= 0 ) {
			this.tabs[ this.current ].className = '';
			this.items[ this.current ].className = '';
            }*/
            if (GetUrlValue('tab')!=undefined) {
                idx = GetUrlValue('tab');
                if (idx!=undefined) {
                    idx = idx - 1;
                }
                this.current = idx;
            } else {
                this.current = idx != undefined ? idx : this.options.start >= 0 && this.options.start < this.items.length ? this.options.start : 0;
            }
                this.tabs[ this.current ].className = 'tab-current';
                this.items[ this.current ].className = 'content-current';
        },

    };

    function GetUrlValue(VarSearch)
    {
        var SearchString = document.location.search.substring(1);
        var VariableArray = SearchString.split('&');
        for (var i = 0; i < VariableArray.length; i++) {
            var KeyValuePair = VariableArray[i].split('=');
            if (KeyValuePair[0] == VarSearch) {
                return KeyValuePair[1];
            }
        }
    }

    // add to global namespace
    //window.CBPFWTabs = CBPFWTabs;
    
    

    return CBPFWTabs;

});