define("zepto", (function (global) {
    return function () {
        var ret, fn;
        return ret || global.$;
    };
}(this)));

define("underscore", (function (global) {
    return function () {
        var ret, fn;
        return ret || global._;
    };
}(this)));

define("backbone", (function (global) {
    return function () {
        var ret, fn;
        return ret || global.Backbone;
    };
}(this)));

