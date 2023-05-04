(self["webpackChunk"] = self["webpackChunk"] || []).push([["search"],{

/***/ "./assets/search.js":
/*!**************************!*\
  !*** ./assets/search.js ***!
  \**************************/
/***/ ((__unused_webpack_module, __unused_webpack_exports, __webpack_require__) => {

__webpack_require__(/*! core-js/modules/es.object.keys.js */ "./node_modules/core-js/modules/es.object.keys.js");
__webpack_require__(/*! core-js/modules/es.symbol.js */ "./node_modules/core-js/modules/es.symbol.js");
__webpack_require__(/*! core-js/modules/es.symbol.description.js */ "./node_modules/core-js/modules/es.symbol.description.js");
__webpack_require__(/*! core-js/modules/es.array.concat.js */ "./node_modules/core-js/modules/es.array.concat.js");
var searchField = document.getElementById('search-dropdown');
$("#price-slider").on('slidestop', callback);
$("#duree-slider").on('slidestop', callback);
$("#search-dropdown").keyup(callback);
$("#reset-btn").on('click', function () {
  reset();
  $('#res').text('');
  $('#search-dropdown').val('');
});
function callback() {
  var value = $('#search-dropdown').val();
  var prix = [1, 5000];
  var duree = [1, 30];
  if ($('#price-slider').length) prix = $('#price-slider').slider("values");
  if ($('#duree-slider').length) duree = $('#duree-slider').slider("values");
  $.ajax({
    url: "/formation/search",
    type: 'GET',
    data: {
      'searchValue': value,
      'minPrix': prix[0],
      'maxPrix': prix[1],
      'minDuree': duree[0],
      'maxDuree': duree[1]
    },
    success: function success(retour) {
      var data = JSON.parse(retour);
      if (Object.keys(data).length === 0) {
        searchField.style = 'color:red';
        if ($('#res').length) {
          $('#res').css({
            'color': 'red'
          });
          $('#res').text('Aucune résultat trouvée');
        }
        reset();
      } else {
        searchField.style = 'color:green';
        if ($('#res').length) {
          $('#res').css({
            'color': 'green'
          });
          $('#res').text(data.length + ' formations trouvées');
        }
        $('#prev-tab').hide().fadeOut('fast');
        $('#new-tab').empty();
        $('#new-tab').show().fadeIn('fast');
        FillUserArray(data);
      }
    },
    error: function error(xhr, status, _error) {
      console.log("Error: " + _error + ' ' + status);
      $('#prev-tab').show();
      $('#new-tab').empty();
    }
  });
}
function reset() {
  $('#new-tab').empty();
  $('#new-tab').hide().fadeOut('fast');
  $('#prev-tab').show().fadeIn('fast');
}
function FillUserArray(data) {
  var idForma = 0;
  $.each(data, function (i, obj) {
    idForma = obj.idFormation;
    var uniqueClassName = 'actu-' + idForma;
    $('#new-tab').append('<tr class="ligne">' + '<th scope="row">' + obj.nomFormation.toUpperCase() + '</th>' + '<td style="min-width: 250px;">' + obj.description + '</td>' + '<td>' + obj.duree + ' semaines</td>' + '<td>' + obj.prix + ' TND</td>' + '<td class="' + uniqueClassName + '"></td>' + '</tr>');
    var token = document.getElementById("t_".concat(obj.idFormation));
    $('.' + uniqueClassName).html();
    $('.' + uniqueClassName).append("\n    <button id=\"d-btn".concat(obj.idFormation, "\" data-dropdown-toggle=\"dropmenu").concat(obj.idFormation, "\" class=\"text-white bg-blue-700 hover:bg-blue-800 mb-0 focus:outline-none font-medium rounded-lg text-sm px-4 py-2.5 text-center inline-flex items-center\" type=\"button\">\n        Actions\n        <svg class=\"w-4 h-4 ml-2\" aria-hidden=\"true\" fill=\"none\" stroke=\"currentColor\" viewBox=\"0 0 24 24\" xmlns=\"http://www.w3.org/2000/svg\">\n            <path stroke-linecap=\"round\" stroke-linejoin=\"round\" stroke-width=\"2\" d=\"M19 9l-7 7-7-7\"></path>\n        </svg>\n    </button>\n    <!-- Dropdown menu -->\n    <div id=\"dropmenu").concat(obj.idFormation, "\" class=\"z-10 hidden bg-white divide-y divide-gray-100 rounded-lg shadow\">\n        <ul class=\"py-2 text-sm text-gray-700\" aria-labelledby=\"d-btn").concat(obj.idFormation, "\">\n            <li>\n                <a href=\"/formation/show/").concat(obj.idFormation, "\" class=\"block text-center px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-600 dark:hover:text-white\">Consulter</a>\n            </li>\n            <li>\n                <a href=\"/formation/").concat(obj.idFormation, "/edit\" class=\"block text-center px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-600 dark:hover:text-white\">Modifier</a>\n            </li>\n            <li>\n                <form method=\"post\" action=\"/formation/").concat(obj.idFormation, "\" onsubmit=\"return confirm('\xCAtes-vous s\xFBr de vouloir supprimer cet \xE9l\xE9ment ??');\">\n                            <input type=\"hidden\" name=\"_token\" value=\"").concat(token.value, "\">\n                    <button class=\"block w-full px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-600 dark:hover:text-white\">Supprimer</button>\n                </form>\n            </li>\n        </ul>\n    </div>\n"));
    var button = document.getElementById("d-btn".concat(obj.idFormation));
    var dropdown = document.getElementById("dropmenu".concat(obj.idFormation));
    var options = {
      placement: 'bottom',
      triggerType: 'click',
      offsetSkidding: 0,
      offsetDistance: 10,
      delay: 300
    };
    new Dropdown(dropdown, button, options);
  });
}

/***/ }),

/***/ "./node_modules/core-js/internals/array-method-has-species-support.js":
/*!****************************************************************************!*\
  !*** ./node_modules/core-js/internals/array-method-has-species-support.js ***!
  \****************************************************************************/
/***/ ((module, __unused_webpack_exports, __webpack_require__) => {

var fails = __webpack_require__(/*! ../internals/fails */ "./node_modules/core-js/internals/fails.js");
var wellKnownSymbol = __webpack_require__(/*! ../internals/well-known-symbol */ "./node_modules/core-js/internals/well-known-symbol.js");
var V8_VERSION = __webpack_require__(/*! ../internals/engine-v8-version */ "./node_modules/core-js/internals/engine-v8-version.js");

var SPECIES = wellKnownSymbol('species');

module.exports = function (METHOD_NAME) {
  // We can't use this feature detection in V8 since it causes
  // deoptimization and serious performance degradation
  // https://github.com/zloirock/core-js/issues/677
  return V8_VERSION >= 51 || !fails(function () {
    var array = [];
    var constructor = array.constructor = {};
    constructor[SPECIES] = function () {
      return { foo: 1 };
    };
    return array[METHOD_NAME](Boolean).foo !== 1;
  });
};


/***/ }),

/***/ "./node_modules/core-js/internals/does-not-exceed-safe-integer.js":
/*!************************************************************************!*\
  !*** ./node_modules/core-js/internals/does-not-exceed-safe-integer.js ***!
  \************************************************************************/
/***/ ((module) => {

var $TypeError = TypeError;
var MAX_SAFE_INTEGER = 0x1FFFFFFFFFFFFF; // 2 ** 53 - 1 == 9007199254740991

module.exports = function (it) {
  if (it > MAX_SAFE_INTEGER) throw $TypeError('Maximum allowed index exceeded');
  return it;
};


/***/ }),

/***/ "./node_modules/core-js/modules/es.array.concat.js":
/*!*********************************************************!*\
  !*** ./node_modules/core-js/modules/es.array.concat.js ***!
  \*********************************************************/
/***/ ((__unused_webpack_module, __unused_webpack_exports, __webpack_require__) => {

"use strict";

var $ = __webpack_require__(/*! ../internals/export */ "./node_modules/core-js/internals/export.js");
var fails = __webpack_require__(/*! ../internals/fails */ "./node_modules/core-js/internals/fails.js");
var isArray = __webpack_require__(/*! ../internals/is-array */ "./node_modules/core-js/internals/is-array.js");
var isObject = __webpack_require__(/*! ../internals/is-object */ "./node_modules/core-js/internals/is-object.js");
var toObject = __webpack_require__(/*! ../internals/to-object */ "./node_modules/core-js/internals/to-object.js");
var lengthOfArrayLike = __webpack_require__(/*! ../internals/length-of-array-like */ "./node_modules/core-js/internals/length-of-array-like.js");
var doesNotExceedSafeInteger = __webpack_require__(/*! ../internals/does-not-exceed-safe-integer */ "./node_modules/core-js/internals/does-not-exceed-safe-integer.js");
var createProperty = __webpack_require__(/*! ../internals/create-property */ "./node_modules/core-js/internals/create-property.js");
var arraySpeciesCreate = __webpack_require__(/*! ../internals/array-species-create */ "./node_modules/core-js/internals/array-species-create.js");
var arrayMethodHasSpeciesSupport = __webpack_require__(/*! ../internals/array-method-has-species-support */ "./node_modules/core-js/internals/array-method-has-species-support.js");
var wellKnownSymbol = __webpack_require__(/*! ../internals/well-known-symbol */ "./node_modules/core-js/internals/well-known-symbol.js");
var V8_VERSION = __webpack_require__(/*! ../internals/engine-v8-version */ "./node_modules/core-js/internals/engine-v8-version.js");

var IS_CONCAT_SPREADABLE = wellKnownSymbol('isConcatSpreadable');

// We can't use this feature detection in V8 since it causes
// deoptimization and serious performance degradation
// https://github.com/zloirock/core-js/issues/679
var IS_CONCAT_SPREADABLE_SUPPORT = V8_VERSION >= 51 || !fails(function () {
  var array = [];
  array[IS_CONCAT_SPREADABLE] = false;
  return array.concat()[0] !== array;
});

var isConcatSpreadable = function (O) {
  if (!isObject(O)) return false;
  var spreadable = O[IS_CONCAT_SPREADABLE];
  return spreadable !== undefined ? !!spreadable : isArray(O);
};

var FORCED = !IS_CONCAT_SPREADABLE_SUPPORT || !arrayMethodHasSpeciesSupport('concat');

// `Array.prototype.concat` method
// https://tc39.es/ecma262/#sec-array.prototype.concat
// with adding support of @@isConcatSpreadable and @@species
$({ target: 'Array', proto: true, arity: 1, forced: FORCED }, {
  // eslint-disable-next-line no-unused-vars -- required for `.length`
  concat: function concat(arg) {
    var O = toObject(this);
    var A = arraySpeciesCreate(O, 0);
    var n = 0;
    var i, k, length, len, E;
    for (i = -1, length = arguments.length; i < length; i++) {
      E = i === -1 ? O : arguments[i];
      if (isConcatSpreadable(E)) {
        len = lengthOfArrayLike(E);
        doesNotExceedSafeInteger(n + len);
        for (k = 0; k < len; k++, n++) if (k in E) createProperty(A, n, E[k]);
      } else {
        doesNotExceedSafeInteger(n + 1);
        createProperty(A, n++, E);
      }
    }
    A.length = n;
    return A;
  }
});


/***/ }),

/***/ "./node_modules/core-js/modules/es.object.keys.js":
/*!********************************************************!*\
  !*** ./node_modules/core-js/modules/es.object.keys.js ***!
  \********************************************************/
/***/ ((__unused_webpack_module, __unused_webpack_exports, __webpack_require__) => {

var $ = __webpack_require__(/*! ../internals/export */ "./node_modules/core-js/internals/export.js");
var toObject = __webpack_require__(/*! ../internals/to-object */ "./node_modules/core-js/internals/to-object.js");
var nativeKeys = __webpack_require__(/*! ../internals/object-keys */ "./node_modules/core-js/internals/object-keys.js");
var fails = __webpack_require__(/*! ../internals/fails */ "./node_modules/core-js/internals/fails.js");

var FAILS_ON_PRIMITIVES = fails(function () { nativeKeys(1); });

// `Object.keys` method
// https://tc39.es/ecma262/#sec-object.keys
$({ target: 'Object', stat: true, forced: FAILS_ON_PRIMITIVES }, {
  keys: function keys(it) {
    return nativeKeys(toObject(it));
  }
});


/***/ })

},
/******/ __webpack_require__ => { // webpackRuntimeModules
/******/ var __webpack_exec__ = (moduleId) => (__webpack_require__(__webpack_require__.s = moduleId))
/******/ __webpack_require__.O(0, ["vendors-node_modules_core-js_modules_es_symbol_description_js-node_modules_core-js_modules_es-df0961"], () => (__webpack_exec__("./assets/search.js")));
/******/ var __webpack_exports__ = __webpack_require__.O();
/******/ }
]);
//# sourceMappingURL=data:application/json;charset=utf-8;base64,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QUM7O0FBRUQ7QUFDQTtBQUNBO0FBQ0E7QUFDQTs7QUFFQTs7QUFFQTtBQUNBO0FBQ0E7QUFDQSxJQUFJLHdEQUF3RDtBQUM1RDtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQSw0Q0FBNEMsWUFBWTtBQUN4RDtBQUNBO0FBQ0E7QUFDQTtBQUNBLG9CQUFvQixTQUFTO0FBQzdCLFFBQVE7QUFDUjtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBLENBQUM7Ozs7Ozs7Ozs7O0FDekRELFFBQVEsbUJBQU8sQ0FBQyx1RUFBcUI7QUFDckMsZUFBZSxtQkFBTyxDQUFDLDZFQUF3QjtBQUMvQyxpQkFBaUIsbUJBQU8sQ0FBQyxpRkFBMEI7QUFDbkQsWUFBWSxtQkFBTyxDQUFDLHFFQUFvQjs7QUFFeEMsOENBQThDLGdCQUFnQjs7QUFFOUQ7QUFDQTtBQUNBLElBQUksMkRBQTJEO0FBQy9EO0FBQ0E7QUFDQTtBQUNBLENBQUMiLCJzb3VyY2VzIjpbIndlYnBhY2s6Ly8vLi9hc3NldHMvc2VhcmNoLmpzIiwid2VicGFjazovLy8uL25vZGVfbW9kdWxlcy9jb3JlLWpzL2ludGVybmFscy9hcnJheS1tZXRob2QtaGFzLXNwZWNpZXMtc3VwcG9ydC5qcyIsIndlYnBhY2s6Ly8vLi9ub2RlX21vZHVsZXMvY29yZS1qcy9pbnRlcm5hbHMvZG9lcy1ub3QtZXhjZWVkLXNhZmUtaW50ZWdlci5qcyIsIndlYnBhY2s6Ly8vLi9ub2RlX21vZHVsZXMvY29yZS1qcy9tb2R1bGVzL2VzLmFycmF5LmNvbmNhdC5qcyIsIndlYnBhY2s6Ly8vLi9ub2RlX21vZHVsZXMvY29yZS1qcy9tb2R1bGVzL2VzLm9iamVjdC5rZXlzLmpzIl0sInNvdXJjZXNDb250ZW50IjpbIlxyXG5cclxuXHJcbmNvbnN0IHNlYXJjaEZpZWxkID0gZG9jdW1lbnQuZ2V0RWxlbWVudEJ5SWQoJ3NlYXJjaC1kcm9wZG93bicpO1xyXG5cclxuXHJcblxyXG4kKFwiI3ByaWNlLXNsaWRlclwiKS5vbignc2xpZGVzdG9wJywgY2FsbGJhY2spXHJcbiQoXCIjZHVyZWUtc2xpZGVyXCIpLm9uKCdzbGlkZXN0b3AnLCBjYWxsYmFjaylcclxuJChcIiNzZWFyY2gtZHJvcGRvd25cIikua2V5dXAoIGNhbGxiYWNrICk7XHJcbiQoXCIjcmVzZXQtYnRuXCIpLm9uKCdjbGljaycsZnVuY3Rpb24gKCkge1xyXG4gICAgcmVzZXQoKTtcclxuICAgICQoJyNyZXMnKS50ZXh0KCcnKTtcclxuICAgICQoJyNzZWFyY2gtZHJvcGRvd24nKS52YWwoJycpO1xyXG59KVxyXG5cclxuZnVuY3Rpb24gY2FsbGJhY2soKSB7XHJcbiAgICBsZXQgdmFsdWUgPSAkKCcjc2VhcmNoLWRyb3Bkb3duJykudmFsKCk7XHJcbiAgICBsZXQgcHJpeD0gWzEsNTAwMF1cclxuICAgIGxldCBkdXJlZSA9IFsxLDMwXVxyXG4gICAgaWYgKCQoJyNwcmljZS1zbGlkZXInKS5sZW5ndGgpXHJcbiAgICAgICAgcHJpeCA9ICQoJyNwcmljZS1zbGlkZXInKS5zbGlkZXIoXCJ2YWx1ZXNcIik7XHJcbiAgICBpZiAoJCgnI2R1cmVlLXNsaWRlcicpLmxlbmd0aClcclxuICAgICAgICBkdXJlZSA9ICAkKCcjZHVyZWUtc2xpZGVyJykuc2xpZGVyKFwidmFsdWVzXCIpO1xyXG5cclxuICAgICQuYWpheCh7XHJcbiAgICAgICAgdXJsOiBcIi9mb3JtYXRpb24vc2VhcmNoXCIsXHJcbiAgICAgICAgdHlwZTogJ0dFVCcsXHJcbiAgICAgICAgZGF0YToge1xyXG4gICAgICAgICAgICAnc2VhcmNoVmFsdWUnOiB2YWx1ZSxcclxuICAgICAgICAgICAgJ21pblByaXgnIDogcHJpeFswXSxcclxuICAgICAgICAgICAgJ21heFByaXgnIDogcHJpeFsxXSxcclxuICAgICAgICAgICAgJ21pbkR1cmVlJyA6IGR1cmVlWzBdLFxyXG4gICAgICAgICAgICAnbWF4RHVyZWUnIDogZHVyZWVbMV1cclxuICAgICAgICB9LFxyXG4gICAgICAgIHN1Y2Nlc3M6IGZ1bmN0aW9uIChyZXRvdXIpIHtcclxuICAgICAgICAgICAgbGV0IGRhdGEgPSBKU09OLnBhcnNlKHJldG91cik7XHJcbiAgICAgICAgICAgIGlmIChPYmplY3Qua2V5cyhkYXRhKS5sZW5ndGggPT09IDApIHtcclxuICAgICAgICAgICAgICAgIHNlYXJjaEZpZWxkLnN0eWxlID0gJ2NvbG9yOnJlZCc7XHJcbiAgICAgICAgICAgICAgICBpZiAoJCgnI3JlcycpLmxlbmd0aCkge1xyXG4gICAgICAgICAgICAgICAgICAgICQoJyNyZXMnKS5jc3Moe1xyXG4gICAgICAgICAgICAgICAgICAgICAgICAnY29sb3InOiAncmVkJyxcclxuICAgICAgICAgICAgICAgICAgICB9KVxyXG4gICAgICAgICAgICAgICAgICAgICQoJyNyZXMnKS50ZXh0KCdBdWN1bmUgcsOpc3VsdGF0IHRyb3V2w6llJyk7XHJcbiAgICAgICAgICAgICAgICB9XHJcblxyXG4gICAgICAgICAgICAgICAgcmVzZXQoKTtcclxuICAgICAgICAgICAgfSBlbHNlIHtcclxuICAgICAgICAgICAgICAgIHNlYXJjaEZpZWxkLnN0eWxlID0gJ2NvbG9yOmdyZWVuJztcclxuICAgICAgICAgICAgICAgIGlmICgkKCcjcmVzJykubGVuZ3RoKSB7XHJcbiAgICAgICAgICAgICAgICAgICAgJCgnI3JlcycpLmNzcyh7XHJcbiAgICAgICAgICAgICAgICAgICAgICAgICdjb2xvcic6ICdncmVlbicsXHJcbiAgICAgICAgICAgICAgICAgICAgfSlcclxuICAgICAgICAgICAgICAgICAgICAkKCcjcmVzJykudGV4dChkYXRhLmxlbmd0aCArICcgZm9ybWF0aW9ucyB0cm91dsOpZXMnKTtcclxuICAgICAgICAgICAgICAgIH1cclxuXHJcbiAgICAgICAgICAgICAgICAkKCcjcHJldi10YWInKS5oaWRlKCkuZmFkZU91dCgnZmFzdCcpO1xyXG4gICAgICAgICAgICAgICAgJCgnI25ldy10YWInKS5lbXB0eSgpO1xyXG4gICAgICAgICAgICAgICAgJCgnI25ldy10YWInKS5zaG93KCkuZmFkZUluKCdmYXN0Jyk7XHJcblxyXG4gICAgICAgICAgICAgICAgRmlsbFVzZXJBcnJheShkYXRhKTtcclxuICAgICAgICAgICAgfVxyXG5cclxuICAgICAgICB9LFxyXG4gICAgICAgIGVycm9yOiBmdW5jdGlvbiAoeGhyLCBzdGF0dXMsIGVycm9yKSB7XHJcbiAgICAgICAgICAgIGNvbnNvbGUubG9nKFwiRXJyb3I6IFwiICsgZXJyb3IgKyAnICcgKyBzdGF0dXMpO1xyXG4gICAgICAgICAgICAkKCcjcHJldi10YWInKS5zaG93KCk7XHJcbiAgICAgICAgICAgICQoJyNuZXctdGFiJykuZW1wdHkoKTtcclxuICAgICAgICB9XHJcblxyXG4gICAgfSk7XHJcblxyXG59XHJcblxyXG5mdW5jdGlvbiByZXNldCgpIHtcclxuICAgICQoJyNuZXctdGFiJykuZW1wdHkoKTtcclxuICAgICQoJyNuZXctdGFiJykuaGlkZSgpLmZhZGVPdXQoJ2Zhc3QnKTtcclxuICAgICQoJyNwcmV2LXRhYicpLnNob3coKS5mYWRlSW4oJ2Zhc3QnKTtcclxufVxyXG5mdW5jdGlvbiBGaWxsVXNlckFycmF5KGRhdGEpIHtcclxuICAgIGxldCBpZEZvcm1hID0gMFxyXG4gICAgJC5lYWNoKGRhdGEsIGZ1bmN0aW9uIChpLCBvYmopIHtcclxuICAgICAgICBpZEZvcm1hID0gb2JqLmlkRm9ybWF0aW9uXHJcbiAgICAgICAgbGV0IHVuaXF1ZUNsYXNzTmFtZSA9ICdhY3R1LScgKyBpZEZvcm1hO1xyXG4gICAgICAgICQoJyNuZXctdGFiJykuYXBwZW5kKFxyXG4gICAgICAgICAgICAnPHRyIGNsYXNzPVwibGlnbmVcIj4nICtcclxuICAgICAgICAgICAgJzx0aCBzY29wZT1cInJvd1wiPicgKyBvYmoubm9tRm9ybWF0aW9uLnRvVXBwZXJDYXNlKCkgKyAnPC90aD4nICtcclxuICAgICAgICAgICAgJzx0ZCBzdHlsZT1cIm1pbi13aWR0aDogMjUwcHg7XCI+JyArIG9iai5kZXNjcmlwdGlvbiArICc8L3RkPicgK1xyXG4gICAgICAgICAgICAnPHRkPicgKyBvYmouZHVyZWUgKyAnIHNlbWFpbmVzPC90ZD4nICtcclxuICAgICAgICAgICAgJzx0ZD4nICsgb2JqLnByaXggKyAnIFRORDwvdGQ+JyArXHJcbiAgICAgICAgICAgICc8dGQgY2xhc3M9XCInICsgdW5pcXVlQ2xhc3NOYW1lICsgJ1wiPjwvdGQ+JyArXHJcbiAgICAgICAgICAgICc8L3RyPicpO1xyXG5cclxuICAgICAgICBsZXQgdG9rZW4gPSBkb2N1bWVudC5nZXRFbGVtZW50QnlJZChgdF8ke29iai5pZEZvcm1hdGlvbn1gKTtcclxuICAgICAgICAkKCcuJyArIHVuaXF1ZUNsYXNzTmFtZSkuaHRtbCgpXHJcblxyXG4gICAgICAgICQoJy4nICsgdW5pcXVlQ2xhc3NOYW1lKS5hcHBlbmQoYFxyXG4gICAgPGJ1dHRvbiBpZD1cImQtYnRuJHtvYmouaWRGb3JtYXRpb259XCIgZGF0YS1kcm9wZG93bi10b2dnbGU9XCJkcm9wbWVudSR7b2JqLmlkRm9ybWF0aW9ufVwiIGNsYXNzPVwidGV4dC13aGl0ZSBiZy1ibHVlLTcwMCBob3ZlcjpiZy1ibHVlLTgwMCBtYi0wIGZvY3VzOm91dGxpbmUtbm9uZSBmb250LW1lZGl1bSByb3VuZGVkLWxnIHRleHQtc20gcHgtNCBweS0yLjUgdGV4dC1jZW50ZXIgaW5saW5lLWZsZXggaXRlbXMtY2VudGVyXCIgdHlwZT1cImJ1dHRvblwiPlxyXG4gICAgICAgIEFjdGlvbnNcclxuICAgICAgICA8c3ZnIGNsYXNzPVwidy00IGgtNCBtbC0yXCIgYXJpYS1oaWRkZW49XCJ0cnVlXCIgZmlsbD1cIm5vbmVcIiBzdHJva2U9XCJjdXJyZW50Q29sb3JcIiB2aWV3Qm94PVwiMCAwIDI0IDI0XCIgeG1sbnM9XCJodHRwOi8vd3d3LnczLm9yZy8yMDAwL3N2Z1wiPlxyXG4gICAgICAgICAgICA8cGF0aCBzdHJva2UtbGluZWNhcD1cInJvdW5kXCIgc3Ryb2tlLWxpbmVqb2luPVwicm91bmRcIiBzdHJva2Utd2lkdGg9XCIyXCIgZD1cIk0xOSA5bC03IDctNy03XCI+PC9wYXRoPlxyXG4gICAgICAgIDwvc3ZnPlxyXG4gICAgPC9idXR0b24+XHJcbiAgICA8IS0tIERyb3Bkb3duIG1lbnUgLS0+XHJcbiAgICA8ZGl2IGlkPVwiZHJvcG1lbnUke29iai5pZEZvcm1hdGlvbn1cIiBjbGFzcz1cInotMTAgaGlkZGVuIGJnLXdoaXRlIGRpdmlkZS15IGRpdmlkZS1ncmF5LTEwMCByb3VuZGVkLWxnIHNoYWRvd1wiPlxyXG4gICAgICAgIDx1bCBjbGFzcz1cInB5LTIgdGV4dC1zbSB0ZXh0LWdyYXktNzAwXCIgYXJpYS1sYWJlbGxlZGJ5PVwiZC1idG4ke29iai5pZEZvcm1hdGlvbn1cIj5cclxuICAgICAgICAgICAgPGxpPlxyXG4gICAgICAgICAgICAgICAgPGEgaHJlZj1cIi9mb3JtYXRpb24vc2hvdy8ke29iai5pZEZvcm1hdGlvbn1cIiBjbGFzcz1cImJsb2NrIHRleHQtY2VudGVyIHB4LTQgcHktMiBob3ZlcjpiZy1ncmF5LTEwMCBkYXJrOmhvdmVyOmJnLWdyYXktNjAwIGRhcms6aG92ZXI6dGV4dC13aGl0ZVwiPkNvbnN1bHRlcjwvYT5cclxuICAgICAgICAgICAgPC9saT5cclxuICAgICAgICAgICAgPGxpPlxyXG4gICAgICAgICAgICAgICAgPGEgaHJlZj1cIi9mb3JtYXRpb24vJHtvYmouaWRGb3JtYXRpb259L2VkaXRcIiBjbGFzcz1cImJsb2NrIHRleHQtY2VudGVyIHB4LTQgcHktMiBob3ZlcjpiZy1ncmF5LTEwMCBkYXJrOmhvdmVyOmJnLWdyYXktNjAwIGRhcms6aG92ZXI6dGV4dC13aGl0ZVwiPk1vZGlmaWVyPC9hPlxyXG4gICAgICAgICAgICA8L2xpPlxyXG4gICAgICAgICAgICA8bGk+XHJcbiAgICAgICAgICAgICAgICA8Zm9ybSBtZXRob2Q9XCJwb3N0XCIgYWN0aW9uPVwiL2Zvcm1hdGlvbi8ke29iai5pZEZvcm1hdGlvbn1cIiBvbnN1Ym1pdD1cInJldHVybiBjb25maXJtKCfDinRlcy12b3VzIHPDu3IgZGUgdm91bG9pciBzdXBwcmltZXIgY2V0IMOpbMOpbWVudCA/PycpO1wiPlxyXG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgPGlucHV0IHR5cGU9XCJoaWRkZW5cIiBuYW1lPVwiX3Rva2VuXCIgdmFsdWU9XCIke3Rva2VuLnZhbHVlfVwiPlxyXG4gICAgICAgICAgICAgICAgICAgIDxidXR0b24gY2xhc3M9XCJibG9jayB3LWZ1bGwgcHgtNCBweS0yIGhvdmVyOmJnLWdyYXktMTAwIGRhcms6aG92ZXI6YmctZ3JheS02MDAgZGFyazpob3Zlcjp0ZXh0LXdoaXRlXCI+U3VwcHJpbWVyPC9idXR0b24+XHJcbiAgICAgICAgICAgICAgICA8L2Zvcm0+XHJcbiAgICAgICAgICAgIDwvbGk+XHJcbiAgICAgICAgPC91bD5cclxuICAgIDwvZGl2PlxyXG5gKTtcclxuXHJcbiAgICAgICAgbGV0IGJ1dHRvbiA9IGRvY3VtZW50LmdldEVsZW1lbnRCeUlkKGBkLWJ0biR7b2JqLmlkRm9ybWF0aW9ufWApO1xyXG5cclxuICAgICAgICBsZXQgZHJvcGRvd24gPSBkb2N1bWVudC5nZXRFbGVtZW50QnlJZChgZHJvcG1lbnUke29iai5pZEZvcm1hdGlvbn1gKTtcclxuXHJcbiAgICAgICAgY29uc3Qgb3B0aW9ucyA9IHtcclxuICAgICAgICAgICAgcGxhY2VtZW50OiAnYm90dG9tJyxcclxuICAgICAgICAgICAgdHJpZ2dlclR5cGU6ICdjbGljaycsXHJcbiAgICAgICAgICAgIG9mZnNldFNraWRkaW5nOiAwLFxyXG4gICAgICAgICAgICBvZmZzZXREaXN0YW5jZTogMTAsXHJcbiAgICAgICAgICAgIGRlbGF5OiAzMDAsXHJcbiAgICAgICAgfTtcclxuXHJcbm5ldyBEcm9wZG93bihkcm9wZG93biwgYnV0dG9uLCBvcHRpb25zKTtcclxuXHJcblxyXG5cclxuXHJcblxyXG5cclxuXHJcblxyXG5cclxuXHJcblxyXG5cclxuICAgIH0pO1xyXG5cclxufVxyXG5cclxuXHJcblxyXG5cclxuIiwidmFyIGZhaWxzID0gcmVxdWlyZSgnLi4vaW50ZXJuYWxzL2ZhaWxzJyk7XG52YXIgd2VsbEtub3duU3ltYm9sID0gcmVxdWlyZSgnLi4vaW50ZXJuYWxzL3dlbGwta25vd24tc3ltYm9sJyk7XG52YXIgVjhfVkVSU0lPTiA9IHJlcXVpcmUoJy4uL2ludGVybmFscy9lbmdpbmUtdjgtdmVyc2lvbicpO1xuXG52YXIgU1BFQ0lFUyA9IHdlbGxLbm93blN5bWJvbCgnc3BlY2llcycpO1xuXG5tb2R1bGUuZXhwb3J0cyA9IGZ1bmN0aW9uIChNRVRIT0RfTkFNRSkge1xuICAvLyBXZSBjYW4ndCB1c2UgdGhpcyBmZWF0dXJlIGRldGVjdGlvbiBpbiBWOCBzaW5jZSBpdCBjYXVzZXNcbiAgLy8gZGVvcHRpbWl6YXRpb24gYW5kIHNlcmlvdXMgcGVyZm9ybWFuY2UgZGVncmFkYXRpb25cbiAgLy8gaHR0cHM6Ly9naXRodWIuY29tL3psb2lyb2NrL2NvcmUtanMvaXNzdWVzLzY3N1xuICByZXR1cm4gVjhfVkVSU0lPTiA+PSA1MSB8fCAhZmFpbHMoZnVuY3Rpb24gKCkge1xuICAgIHZhciBhcnJheSA9IFtdO1xuICAgIHZhciBjb25zdHJ1Y3RvciA9IGFycmF5LmNvbnN0cnVjdG9yID0ge307XG4gICAgY29uc3RydWN0b3JbU1BFQ0lFU10gPSBmdW5jdGlvbiAoKSB7XG4gICAgICByZXR1cm4geyBmb286IDEgfTtcbiAgICB9O1xuICAgIHJldHVybiBhcnJheVtNRVRIT0RfTkFNRV0oQm9vbGVhbikuZm9vICE9PSAxO1xuICB9KTtcbn07XG4iLCJ2YXIgJFR5cGVFcnJvciA9IFR5cGVFcnJvcjtcbnZhciBNQVhfU0FGRV9JTlRFR0VSID0gMHgxRkZGRkZGRkZGRkZGRjsgLy8gMiAqKiA1MyAtIDEgPT0gOTAwNzE5OTI1NDc0MDk5MVxuXG5tb2R1bGUuZXhwb3J0cyA9IGZ1bmN0aW9uIChpdCkge1xuICBpZiAoaXQgPiBNQVhfU0FGRV9JTlRFR0VSKSB0aHJvdyAkVHlwZUVycm9yKCdNYXhpbXVtIGFsbG93ZWQgaW5kZXggZXhjZWVkZWQnKTtcbiAgcmV0dXJuIGl0O1xufTtcbiIsIid1c2Ugc3RyaWN0JztcbnZhciAkID0gcmVxdWlyZSgnLi4vaW50ZXJuYWxzL2V4cG9ydCcpO1xudmFyIGZhaWxzID0gcmVxdWlyZSgnLi4vaW50ZXJuYWxzL2ZhaWxzJyk7XG52YXIgaXNBcnJheSA9IHJlcXVpcmUoJy4uL2ludGVybmFscy9pcy1hcnJheScpO1xudmFyIGlzT2JqZWN0ID0gcmVxdWlyZSgnLi4vaW50ZXJuYWxzL2lzLW9iamVjdCcpO1xudmFyIHRvT2JqZWN0ID0gcmVxdWlyZSgnLi4vaW50ZXJuYWxzL3RvLW9iamVjdCcpO1xudmFyIGxlbmd0aE9mQXJyYXlMaWtlID0gcmVxdWlyZSgnLi4vaW50ZXJuYWxzL2xlbmd0aC1vZi1hcnJheS1saWtlJyk7XG52YXIgZG9lc05vdEV4Y2VlZFNhZmVJbnRlZ2VyID0gcmVxdWlyZSgnLi4vaW50ZXJuYWxzL2RvZXMtbm90LWV4Y2VlZC1zYWZlLWludGVnZXInKTtcbnZhciBjcmVhdGVQcm9wZXJ0eSA9IHJlcXVpcmUoJy4uL2ludGVybmFscy9jcmVhdGUtcHJvcGVydHknKTtcbnZhciBhcnJheVNwZWNpZXNDcmVhdGUgPSByZXF1aXJlKCcuLi9pbnRlcm5hbHMvYXJyYXktc3BlY2llcy1jcmVhdGUnKTtcbnZhciBhcnJheU1ldGhvZEhhc1NwZWNpZXNTdXBwb3J0ID0gcmVxdWlyZSgnLi4vaW50ZXJuYWxzL2FycmF5LW1ldGhvZC1oYXMtc3BlY2llcy1zdXBwb3J0Jyk7XG52YXIgd2VsbEtub3duU3ltYm9sID0gcmVxdWlyZSgnLi4vaW50ZXJuYWxzL3dlbGwta25vd24tc3ltYm9sJyk7XG52YXIgVjhfVkVSU0lPTiA9IHJlcXVpcmUoJy4uL2ludGVybmFscy9lbmdpbmUtdjgtdmVyc2lvbicpO1xuXG52YXIgSVNfQ09OQ0FUX1NQUkVBREFCTEUgPSB3ZWxsS25vd25TeW1ib2woJ2lzQ29uY2F0U3ByZWFkYWJsZScpO1xuXG4vLyBXZSBjYW4ndCB1c2UgdGhpcyBmZWF0dXJlIGRldGVjdGlvbiBpbiBWOCBzaW5jZSBpdCBjYXVzZXNcbi8vIGRlb3B0aW1pemF0aW9uIGFuZCBzZXJpb3VzIHBlcmZvcm1hbmNlIGRlZ3JhZGF0aW9uXG4vLyBodHRwczovL2dpdGh1Yi5jb20vemxvaXJvY2svY29yZS1qcy9pc3N1ZXMvNjc5XG52YXIgSVNfQ09OQ0FUX1NQUkVBREFCTEVfU1VQUE9SVCA9IFY4X1ZFUlNJT04gPj0gNTEgfHwgIWZhaWxzKGZ1bmN0aW9uICgpIHtcbiAgdmFyIGFycmF5ID0gW107XG4gIGFycmF5W0lTX0NPTkNBVF9TUFJFQURBQkxFXSA9IGZhbHNlO1xuICByZXR1cm4gYXJyYXkuY29uY2F0KClbMF0gIT09IGFycmF5O1xufSk7XG5cbnZhciBpc0NvbmNhdFNwcmVhZGFibGUgPSBmdW5jdGlvbiAoTykge1xuICBpZiAoIWlzT2JqZWN0KE8pKSByZXR1cm4gZmFsc2U7XG4gIHZhciBzcHJlYWRhYmxlID0gT1tJU19DT05DQVRfU1BSRUFEQUJMRV07XG4gIHJldHVybiBzcHJlYWRhYmxlICE9PSB1bmRlZmluZWQgPyAhIXNwcmVhZGFibGUgOiBpc0FycmF5KE8pO1xufTtcblxudmFyIEZPUkNFRCA9ICFJU19DT05DQVRfU1BSRUFEQUJMRV9TVVBQT1JUIHx8ICFhcnJheU1ldGhvZEhhc1NwZWNpZXNTdXBwb3J0KCdjb25jYXQnKTtcblxuLy8gYEFycmF5LnByb3RvdHlwZS5jb25jYXRgIG1ldGhvZFxuLy8gaHR0cHM6Ly90YzM5LmVzL2VjbWEyNjIvI3NlYy1hcnJheS5wcm90b3R5cGUuY29uY2F0XG4vLyB3aXRoIGFkZGluZyBzdXBwb3J0IG9mIEBAaXNDb25jYXRTcHJlYWRhYmxlIGFuZCBAQHNwZWNpZXNcbiQoeyB0YXJnZXQ6ICdBcnJheScsIHByb3RvOiB0cnVlLCBhcml0eTogMSwgZm9yY2VkOiBGT1JDRUQgfSwge1xuICAvLyBlc2xpbnQtZGlzYWJsZS1uZXh0LWxpbmUgbm8tdW51c2VkLXZhcnMgLS0gcmVxdWlyZWQgZm9yIGAubGVuZ3RoYFxuICBjb25jYXQ6IGZ1bmN0aW9uIGNvbmNhdChhcmcpIHtcbiAgICB2YXIgTyA9IHRvT2JqZWN0KHRoaXMpO1xuICAgIHZhciBBID0gYXJyYXlTcGVjaWVzQ3JlYXRlKE8sIDApO1xuICAgIHZhciBuID0gMDtcbiAgICB2YXIgaSwgaywgbGVuZ3RoLCBsZW4sIEU7XG4gICAgZm9yIChpID0gLTEsIGxlbmd0aCA9IGFyZ3VtZW50cy5sZW5ndGg7IGkgPCBsZW5ndGg7IGkrKykge1xuICAgICAgRSA9IGkgPT09IC0xID8gTyA6IGFyZ3VtZW50c1tpXTtcbiAgICAgIGlmIChpc0NvbmNhdFNwcmVhZGFibGUoRSkpIHtcbiAgICAgICAgbGVuID0gbGVuZ3RoT2ZBcnJheUxpa2UoRSk7XG4gICAgICAgIGRvZXNOb3RFeGNlZWRTYWZlSW50ZWdlcihuICsgbGVuKTtcbiAgICAgICAgZm9yIChrID0gMDsgayA8IGxlbjsgaysrLCBuKyspIGlmIChrIGluIEUpIGNyZWF0ZVByb3BlcnR5KEEsIG4sIEVba10pO1xuICAgICAgfSBlbHNlIHtcbiAgICAgICAgZG9lc05vdEV4Y2VlZFNhZmVJbnRlZ2VyKG4gKyAxKTtcbiAgICAgICAgY3JlYXRlUHJvcGVydHkoQSwgbisrLCBFKTtcbiAgICAgIH1cbiAgICB9XG4gICAgQS5sZW5ndGggPSBuO1xuICAgIHJldHVybiBBO1xuICB9XG59KTtcbiIsInZhciAkID0gcmVxdWlyZSgnLi4vaW50ZXJuYWxzL2V4cG9ydCcpO1xudmFyIHRvT2JqZWN0ID0gcmVxdWlyZSgnLi4vaW50ZXJuYWxzL3RvLW9iamVjdCcpO1xudmFyIG5hdGl2ZUtleXMgPSByZXF1aXJlKCcuLi9pbnRlcm5hbHMvb2JqZWN0LWtleXMnKTtcbnZhciBmYWlscyA9IHJlcXVpcmUoJy4uL2ludGVybmFscy9mYWlscycpO1xuXG52YXIgRkFJTFNfT05fUFJJTUlUSVZFUyA9IGZhaWxzKGZ1bmN0aW9uICgpIHsgbmF0aXZlS2V5cygxKTsgfSk7XG5cbi8vIGBPYmplY3Qua2V5c2AgbWV0aG9kXG4vLyBodHRwczovL3RjMzkuZXMvZWNtYTI2Mi8jc2VjLW9iamVjdC5rZXlzXG4kKHsgdGFyZ2V0OiAnT2JqZWN0Jywgc3RhdDogdHJ1ZSwgZm9yY2VkOiBGQUlMU19PTl9QUklNSVRJVkVTIH0sIHtcbiAga2V5czogZnVuY3Rpb24ga2V5cyhpdCkge1xuICAgIHJldHVybiBuYXRpdmVLZXlzKHRvT2JqZWN0KGl0KSk7XG4gIH1cbn0pO1xuIl0sIm5hbWVzIjpbInNlYXJjaEZpZWxkIiwiZG9jdW1lbnQiLCJnZXRFbGVtZW50QnlJZCIsIiQiLCJvbiIsImNhbGxiYWNrIiwia2V5dXAiLCJyZXNldCIsInRleHQiLCJ2YWwiLCJ2YWx1ZSIsInByaXgiLCJkdXJlZSIsImxlbmd0aCIsInNsaWRlciIsImFqYXgiLCJ1cmwiLCJ0eXBlIiwiZGF0YSIsInN1Y2Nlc3MiLCJyZXRvdXIiLCJKU09OIiwicGFyc2UiLCJPYmplY3QiLCJrZXlzIiwic3R5bGUiLCJjc3MiLCJoaWRlIiwiZmFkZU91dCIsImVtcHR5Iiwic2hvdyIsImZhZGVJbiIsIkZpbGxVc2VyQXJyYXkiLCJlcnJvciIsInhociIsInN0YXR1cyIsImNvbnNvbGUiLCJsb2ciLCJpZEZvcm1hIiwiZWFjaCIsImkiLCJvYmoiLCJpZEZvcm1hdGlvbiIsInVuaXF1ZUNsYXNzTmFtZSIsImFwcGVuZCIsIm5vbUZvcm1hdGlvbiIsInRvVXBwZXJDYXNlIiwiZGVzY3JpcHRpb24iLCJ0b2tlbiIsImNvbmNhdCIsImh0bWwiLCJidXR0b24iLCJkcm9wZG93biIsIm9wdGlvbnMiLCJwbGFjZW1lbnQiLCJ0cmlnZ2VyVHlwZSIsIm9mZnNldFNraWRkaW5nIiwib2Zmc2V0RGlzdGFuY2UiLCJkZWxheSIsIkRyb3Bkb3duIl0sInNvdXJjZVJvb3QiOiIifQ==