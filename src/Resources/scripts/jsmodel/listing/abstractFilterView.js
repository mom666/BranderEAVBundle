define([
    'backbone.marionette',
    'underscore',
    'brander-eav/view/base'
], function (Marionette, _, BaseView) {
    'use strict';

    return Marionette.ItemView.extend({
        "initialize":         function () {
            BaseView.prototype.initialize.apply(this, arguments);
            Marionette.ItemView.prototype.initialize.apply(this, arguments);
        },
        "getGeoPointFilter":  function (latitude, longitude, distance, unit) {
            latitude = parseFloat(latitude);
            longitude = parseFloat(longitude);
            if (!latitude || !longitude || _.isNaN(latitude) || _.isNaN(longitude)) {
                throw 'wrong point';
            }
            distance = parseFloat(distance);
            if (!distance || _.isNaN(distance)) {
                throw 'wrong distance';
            }
            if (!unit) {
                unit = 'km';
            }
            return '' + latitude + ',' + longitude + 'distance:' + distance + unit;
        },
        "getRangeQuery":      function (lt, gt, lte, gte) {
            var tmp = '';
            if (lt !== undefined) {
                tmp += 'lt:' + lt + ';';
            } else {
                if (lte !== undefined) {
                    tmp += 'lte:' + lte + ';';
                }
            }
            if (gt !== undefined) {
                tmp += 'gt:' + gt + ';';
            } else {
                if (gte !== undefined) {
                    tmp += 'gte:' + gte + ';';
                }
            }
            return tmp;
        },
        "getRangeData":       function (string, formatter) {
            var keywords = [
                    'gt',
                    'gte',
                    'lt',
                    'lte',
                ],
                result   = {},
                res;
            _.each(keywords, function (keyword) {
                var format = keyword + ':\\s*(.+?)\\s*;';
                format = new RegExp(format, 'i');
                res = string.match(format);//, $value, $res
                if (res && res.length > 1) {
                    if (formatter) {
                        result[keyword] = formatter(res[1]);
                    } else {
                        result[keyword] = res[1];
                    }
                }
            });
            return result;
        },
        "getPointFromString": function () {
            var tmp = string.split(',');
            return {
                lat: parseFloat(tmp[0]),
                lon: parseFloat(tmp[1])
            };
        },
        "getGeoFilterData":   function (string) {
            debugger;
            var value = string.trim();
            if (value) {
                if (value.indexOf('distance:') !== -1) {
                    value = value.split('distance:');
                    var distance = value[1];
                    value = value[0];
                    value = this.getPointFromString(value);
                    var units = [
                        'km',
                        'm',
                        'mi',
                        'yd',
                        'ft',
                        'nm',
                    ];
                    var matches = distance.match('/\\s*([\\d\\.,]+)(.*)$/');
                    if (matches[1] && matches[1] !== '') {
                        distance = parseFloat(matches[1]);
                        var unit;
                        if (distance && (unit = matches[2].trim().toLowerCase()) !== '') {
                            if (units.indexOf(unit) === -1) {
                                unit = 'km';
                            }
                        } else {
                            unit = 'km';
                        }
                        value.distance = distance;
                        value.unit = unit;
                        return value;
                    } else {
                        return {};
                    }
                }
            }
            return {};
        }
    });
});