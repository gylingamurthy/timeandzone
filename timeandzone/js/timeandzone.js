/**
 * @file
 */

(function ($) {

  'use strict';
  var strArray = drupalSettings.timezone_clock.country_name;
  strArray = strArray.split(",");
  var time_format = 'hh:mm A';

  $('#clock_' + strArray[3]).jClocksGMT(
        {
            title: strArray[0] + ' -- ' + strArray[1],
            offset: strArray[2],
            //skin: comma_array[1],
            //skin: 0,
            digital: true,
            analog: false,
            date: true,
            timeformat: time_format,
            dateformat: 'DDth  MMM YYYY',
        });

})(jQuery);
