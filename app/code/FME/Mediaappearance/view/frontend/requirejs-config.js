/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
var config = {
    map: {
        "*": {
            fancybox: 'FME_Mediaappearance/js/fancybox',
            helpers: 'FME_Mediaappearance/js/fancyboxmedia',
            CBPFWTabs: 'FME_Mediaappearance/js/tabs/cbpFWTabs',
            ias: 'FME_Mediaappearance/js/ias',
            owlcarousel: 'FME_Mediaappearance/js/owlcarousel/owlcarousel',
            jwplayer: 'FME_Mediaappearance/js/jwplayer',
            ugallery : 'FME_Mediaappearance/js/upgrade/unitegallery',
            utheme: 'FME_Mediaappearance/js/upgrade/ug-theme-compact',
            uthemegrid: 'FME_Mediaappearance/js/upgrade/ug-theme-grid',
            uthemeslider: 'FME_Mediaappearance/js/upgrade/ug-theme-slider',
            uthemevideo: 'FME_Mediaappearance/js/upgrade/ug-theme-video',
            finaltilesgallery: 'FME_Mediaappearance/js/upgrade/jquery.finaltilesgallery',
            finalmagpop: 'FME_Mediaappearance/js/upgrade/jquery.magnific-popup.min',
            photolighbox: 'FME_Mediaappearance/js/upgrade/lightbox2',
            uthemecarousel: 'FME_Mediaappearance/js/upgrade/ug-theme-carousel',
            uthemetiles: 'FME_Mediaappearance/js/upgrade/ug-theme-tiles',
            uthemegridtiles: 'FME_Mediaappearance/js/upgrade/ug-theme-tilesgrid',
            nanogallery3: 'FME_Mediaappearance/js/nano/jquery.nanogallery2',
            tdgallery :'FME_Mediaappearance/js/upgrade1/jR3DCarousel.min'
           // airplay:'FME_Mediaappearance/js/airplay.js',
        }
    },
    paths: {
        "fancybox": 'js/fancybox',
        "helpers": 'js/fancyboxmedia',
        "CBPFWTabs": 'js/tabs/cbpFWTabs',
        "ias": 'js/ias',
        "owlcarousel": 'js/owlcarousel/owlcarousel',
        "jwplayer": 'js/jwplayer'
    },
    shim: {
        "fancybox": {
            deps: ["jquery"]
        },
        "helpers": {
            deps: ["fancybox"]
        },
        "CBPFWTabs": {
            deps: ["prototype"]
        },
    },
};

