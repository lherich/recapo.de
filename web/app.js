/*global require*/
'use strict';

require.config({
  paths: {
    // require plugins
    'text': 'vendor/require.js-2.1.11/text',
    'domReady': 'vendor/require.js-2.1.11/domReady',
  
    // jquery
    'jquery': 'vendor/jquery-2.1.1/jquery-2.1.1.min',
    'jquery-1.7.1': 'vendor/jquery-1.7.1/jquery-1.7.1.min',
    
    // jquery-ui
    'jquery-ui': 'vendor/jquery-ui-1.10.4/js/jquery-ui-1.10.4.min',
    'jquery-ui-1.8.16': 'vendor/jquery-ui-1.8.16/jquery-ui-1.8.16.min',
    
    // bootstrap
    'bootstrap': 'vendor/bootstrap-3.1.1/js/bootstrap.min',
    
    // bootstrap-editable
    'bootstrap-editable': 'vendor/bootstrap3-editable/js/bootstrap-editable.min',

    // bootstrap-select
    'bootstrap-select': 'vendor/bootstrap-select-1.5.2-22/bootstrap-select.min',

    // bootstrap-switch
    'bootstrap-switch': 'vendor/bootstrap-switch-3.0.2/js/bootstrap-switch.min',

    // bootstrap-datepicker
    'bootstrap-datepicker': 'vendor/bootstrap-datepicker/js/bootstrap-datepicker',
    'bootstrap-datepicker-de': 'vendor/bootstrap-datepicker/js/locales/bootstrap-datepicker.de',

    
    // bootstrap-wysihtml5
    'bootstrap-wysihtml5': 'vendor/bootstrap-wysihtml5/bootstrap-wysihtml5-0.3.0',
    'bootstrap-wysihtml5.parser_rules': 'vendor/bootstrap-wysihtml5/bootstrap-wysihtml5-0.3.0.parser_rules',
    'bootstrap-wysihtml5.de': 'vendor/bootstrap-wysihtml5/locales/bootstrap-wysihtml5-0.3.0.de-DE',
    
    // jQuery dataTable
    'datatables': 'vendor/datatables-1.10.0/media/js/jquery.dataTables.min',
    'datatables-de': 'vendor/dataTables-de',

    // bootstrap-validator
    'bootstrap-validator': 'vendor/bootstrap-validator-0.5.0/validator',

    'bootstrap-lightbox': 'vendor/bootstrap-lightbox-3.1.4/ekko-lightbox.min',
    
    // bootstrap-datetimepicker
    'bootstrap-datetimepicker': 'vendor/bootstrap-datetimepicker-3.0.0/js/locales/bootstrap-datetimepicker.de',
    'bootstrap-datetimepicker-core': 'vendor/bootstrap-datetimepicker-3.0.0/js/bootstrap-datetimepicker.min',

    'jasny-bootstrap': 'vendor/jasny-bootstrap-3.1.3/js/jasny-bootstrap.min',
    
    // moment.js
    'moment': 'vendor/moment.js-2.6.0/moment-with-langs.min',

    // fuelux
    'fuelux': 'vendor/fuelux-3.0.0-wip/js/fuelux',

    'fancytree': 'vendor/jquery.fancytree-2.2.1/jquery.fancytree.min',
    'fancytree-dnd': 'vendor/jquery.fancytree-2.2.1/extension/jquery.fancytree.dnd',
    'fancytree-glyph': 'vendor/jquery.fancytree-2.2.1/extension/jquery.fancytree.glyph',
    'fancytree-persist': 'vendor/jquery.fancytree-2.2.1/extension/jquery.fancytree.persist',
    'jquery.cookie': 'vendor/jquery.cookie-1.4.1/jquery.cookie',

    // jquery-form
    'jquery-form': 'vendor/jquery.form-3.51.0/jquery.form.min',
    
    
    // jquery-dragable
    'jquery-dragable': 'vendor/jquery.dragsort-0.5.1/jquery.dragsort-0.5.1',
    
    'jquery.pagination': 'vendor/jquery.pagination/jquery.pagination',
    'jquery.jrating': 'vendor/jRating-master/jquery/jRating.jquery',
    'jquery.form': 'vendor/form-master/jquery.form',
    
    // Misc
    'async': 'vendor/requirejs/async',


    'helper': 'vendor/helper'
  },
  shim: {
    // jquery
    'jquery-ui': ['jquery'],
    
    // bootstrap
    'bootstrap': ['jquery'],

    // datatables
    'datatables': ['jquery'],

    // bootstrap-editable
    'bootstrap-editable': ['bootstrap'],

    // bootstrap-switch
    'bootstrap-switch': ['bootstrap'],
    
    // bootstrap-datepicker
    'bootstrap-datetimepicker': ['bootstrap', 'moment', 'bootstrap-datetimepicker-core'],
    
    // bootstrap-select
    'bootstrap-select': ['bootstrap'],
    'bootstrap-select-de': ['bootstrap-select'],
    
    // bootstrap-wysihtml5
    'bootstrap-wysihtml5': ['bootstrap'],
    'bootstrap-wysihtml5.parser_rules': ['bootstrap-wysihtml5'],
    'bootstrap-wysihtml5.de': ['bootstrap-wysihtml5'],
    
    // jqBootstrapValidation
    'bootstrap-validator': ['jquery', 'bootstrap'],

    'bootstrap-lightbox': ['jquery', 'bootstrap'],
    
    // jasny-bootstrap
    'jasny-bootstrap': ['bootstrap'],
    
    'fancytree': ['bootstrap', 'jquery', 'jquery-ui'],
    'fancytree-dnd': ['fancytree'],
    'fancytree-glyph': ['fancytree'],
    'fancytree-persist': ['fancytree', 'jquery.cookie'],



    // fuelux
    'fuelux': ['bootstrap'],
    
    // jquery-form
    'jquery-form': ['jquery'],


    // jquery-dragable
    'jquery-dragable': ['jquery'],

    'jquery.pagination': ['jquery'],
    'jquery.jrating': ['jquery'],
    'jquery.form': ['jquery'],

    'helper': ['jquery']
  }
});


require(['domReady'], function (domReady) {
  domReady(function () {
    require(['vendor/bootloader']);

    if (typeof REQUIREJS != 'undefined') {
      if(REQUIREJS instanceof Array) {
        REQUIREJS.forEach(function(item) {
          require([item]);
        });
      } else {
       require([REQUIREJS]); 
      }
    }
  });
});