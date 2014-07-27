/**
 * This is a siff standard to be used with the JsCodesniffer originally
 * developed by Dmitry Sheiko (http://www.dsheiko.com).
 * @see https://github.com/dsheiko/jscodesniffer
 * 
 * 
 * 
 * @package jscodesniffer
 * @author wick-ed
 * @license MIT
 * @copyright (c) Bernhard Wick
 * @jscs standard:YuiDoc
 * 
 * @see https://yui.github.io/yuidoc/syntax/index.html
 */

// UMD boilerplate according to https://github.com/umdjs/umd
if ( typeof module === "object" && typeof define !== "function" ) {
	var define = function ( factory ) {
		module.exports = factory( require, exports, module );
	};
}
/**
 * A module representing a ruleset.
 * @module standard/YuiDoc
 */
define(function() {
	/**
	* @type {object}
	* @alias module:standard/YuiDoc
	*/
	return {
		// intentation with spaces
		"Indentation": {
			"allowOnlyTabs": false,
			"allowOnlySpaces": true
		},
		// no whitespaces at the end of lines
		"LineSpacing": {
			"allowLineTrailingSpaces": false
		}
	};
});