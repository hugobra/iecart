var box = { version: '0.7.0' };

(function() {

var global = this,
    W = global.window,
    D = W.document,
    DE = D.documentElement,
    $ = global.jQuery,
    noop = function() {},
    objToString = Object.prototype.toString,
    html = $(DE).attr('id', 'js');

// flag dom is ready
box.domIsReady = false;
$(D).ready(function() {
    box.domIsReady = true;
});

// flag page is loaded
box.loadIsDone = false;
$(W).load(function() {
    box.loadIsDone = true;
});

// bridge to jQuery
box.dom = function(a1, a2) {
    return $(a1, a2);
};

// global accessors
box.getGlobal = function() {
    return global;
};

box.getWin = function() {
    return W;
};

box.getDoc = function() {
    return D;
};

box.getJWin = function() {
    return $(W);
};

box.getJDoc = function() {
    return $(D);
};

// helpers
box.isArray = function(o) {
    return objToString.call(o) === '[object Array]';
};

box.isObject = function(o) {
    return o !== null && typeof o !== 'undefined' && objToString.call(o) === '[object Object]';
};

// feature testing
box.isHostMethod = function(o, p) {
    var t = typeof o[p];
    return t == 'function' || !!(t == 'object' && o[p]) || t == 'unknown';
};

box.areHostMethods = function(o) {
    var i = arguments.length - 1;
    while(i > 0) {
        if(!box.isHostMethod(o, arguments[i])) {
            return false;
        }
        i--;
    }
    return true;
};

box.isHostCollection = function(o, p) {
    var t = typeof o[p];
    return !!(t == 'object' && o[p]) || t == 'function';
};

box.isHostObject = function(o, p) {
    return !!(typeof(o[p]) == 'object' && o[p]);
};

var inherit, extend, clone;

box.inherit = inherit = (function() {
    var Fn = function() {};
    return function(fnSub, fnSuper) {
        Fn.prototype = fnSuper.prototype;
        fnSub.prototype = new Fn();
        fnSub.prototype.constructor = fnSub;
        fnSub.prototype._super = fnSuper.prototype;
    };
})();

box.extend = extend = function(fn, oFn) {
    var oProto = fn.prototype;
    for(var sPropName in oFn) {
        if(oFn.hasOwnProperty(sPropName)) {
            oProto[sPropName] = oFn[sPropName];
        }
    }
};

box.clone = clone = (function() {
    var Fn = function() {};
    return function(oSrc) {
        Fn.prototype = oSrc;
        return new Fn();
    };
})();

/*!
 * news v0.5.7, a JavaScript notification library
 * Copyright (C) 2011 Manuel Catez
 * 
 * Distributed under an MIT-style license
 * See https://github.com/mcatez/news
 */
var reSubscribe = /^([a-z0-9_-]+)(@[a-z0-9_-]+)?(>(?:\*|[^>\n\r\f\t]+))$/i,
    reUnsubscribe = /^(\*|[a-z0-9_-]+)(@(?:\*|[a-z0-9_-]+))?(>(?:\*|[^>\n\r\f\t]+))$/i,
    oSubscriptions = {},
    Notification,
    subscribe,
    subscribeAll,
    unsubscribe,
    unsubscribeAll,
    publish,
    removeHandlersMain,
    removeHandlers,
    execHandlers;

Notification = function(oDatas) {
    this.type = oDatas.type;
    this.label = oDatas.label;
    this.propagation = (oDatas.propagation !== false);
    this.stopped = false;
    this.prevented = false;
    this.source = oDatas.source || null;
    this.data = oDatas.data || {};
    this.timeStamp = (new Date()).getTime();
};
Notification.prototype = {
    stopPropagation: function() {
        this.propagation = false;
    },
    
    stopImmediatePropagation: function() {
        this.propagation = false;
        this.stopped = true;
    },
    
    preventDefault: function() {
        this.prevented = true;
    }
};

subscribe = function(sNews, fHandler, oContext) {
    var aTokens = sNews.match(reSubscribe);
    if(aTokens) {
        var sType = aTokens[1],
            sNamespace = aTokens[2] || '@!',
            sLabel = aTokens[3],
            oDatas = { handler: fHandler, ns: sNamespace, context: oContext },
            oType = oSubscriptions[sType],
            oActions;
        if(!oType) {
            oType = oSubscriptions[sType] = { count: 0 };
        }
        oActions = oType[sLabel];
        if(oActions) {
            oActions[oActions.length] = oDatas;
        } else {
            oType[sLabel] = [ oDatas ];
            oType.count++;
        }
    }/*<debug>*/ else {
        publish({
            type: 'error',
            label: 'box.subscribe',
            data: { message: 'Incorrect news name: ' + sNews }
        });
    }/*</debug>*/
};

subscribeAll = function() {
    var sType = typeof arguments[0],
        i, l, oDatas, uNames;
    if(sType === 'string') {
        subscribe(arguments[0], arguments[1], arguments[2]);
    } else if(sType === 'object' && arguments[0]) {
        i = -1;
        l = arguments.length;
        while(++i < l) {
            oDatas = arguments[i];
            uNames = oDatas.name;
            if(box.isArray(uNames)) {
                var j = -1,
                    k = uNames.length;
                while(++j < k) {
                    subscribe(uNames[j], oDatas.handler, oDatas.context);
                }
            } else {
                subscribe(uNames, oDatas.handler, oDatas.context);
            }
        }
    }
};

removeHandlersMain = function(oType, sLabel, sNamespace) {
    var aLabel = oType[sLabel];
    if(aLabel) {
        if(sNamespace === '@?') {
            delete oType[sLabel];
            oType.count--;
            return;
        }
        var i = aLabel.length;
        if(sNamespace === '@*') {
            while(i--) {
                if(aLabel[i].ns !== '@!') {
                    aLabel.splice(i, i + 1);
                }
            }
        } else {
            while(i--) {
                if(aLabel[i].ns === sNamespace) {
                    aLabel.splice(i, 1);
                }
            }
        }
        if(aLabel.length === 0) {
            delete oType[sLabel];
            oType.count--;
        }
    }
};

removeHandlers = function(sType, sLabel, sNamespace) {
    var oType = oSubscriptions[sType];
    if(oType) {
        if(sLabel === '>*') {
            var sName;
            for(sName in oType) {
                if(oType.hasOwnProperty(sName) && sName !== 'count') {
                    removeHandlersMain(oType, sName, sNamespace);
                }
            }
        } else {
            removeHandlersMain(oType, sLabel, sNamespace);
        }
        if(oType.count === 0) {
            delete oSubscriptions[sType];
        }
    }
};

unsubscribe = function(sNews) {
    if(sNews === '*' || sNews === '*@?>*') {
        oSubscriptions = {};
    } else {
        var aTokens = sNews.match(reUnsubscribe);
        if(aTokens) {
            var sType = aTokens[1],
                sNamespace = aTokens[2] || '@!',
                sLabel = aTokens[3];
            if(sType === '*') {
                for(sType in oSubscriptions) {
                    if(oSubscriptions.hasOwnProperty(sType)) {
                        removeHandlers(sType, sLabel, sNamespace);
                    }
                }
            } else {
                removeHandlers(sType, sLabel, sNamespace);
            }
        }/*<debug>*/ else {
            publish({
                type: 'error',
                label: 'box.unsubscribe',
                data: { message: 'Incorrect news name: ' + sNews }
            });
        }/*</debug>*/
    }
};

unsubscribeAll = function() {
    var i = arguments.length;
    while(i--) {
        unsubscribe(arguments[i]);
    }
};

execHandlers = function(oNews, aReg) {
    if(aReg) {
        var i = -1,
            l = aReg.length,
            oListener;
        while(++i < l) {
            oListener = aReg[i];
            oListener.handler.call(oListener.context || oNews.source || null, oNews);
            if(oNews.stopped === true) {
                break;
            }
        }
    }
};

publish = function(oDatas) {
    var oType = oSubscriptions[oDatas.type];
    if(oType) {
        var sLabel = '>' + oDatas.label,
            oNews = new Notification(oDatas),
            aParts, i;
        if(oNews.propagation === false) {
            execHandlers(oNews, oType[sLabel]);
        } else {
            aParts = sLabel.split('.');
            i = aParts.length;
            while(i--) {
                sLabel = aParts.slice(0, i + 1).join('.');
                execHandlers(oNews, oType[sLabel]);
                if(oNews.propagation === false || oNews.stopped === true) {
                    break;
                }
            }
            if(oNews.propagation !== false && oType['>*']) {
                execHandlers(oNews, oType['>*']);
            }
        }
        return oNews;
    }
    return null;
};

box.subscribe = subscribeAll;
box.unsubscribe = unsubscribeAll;
box.publish = publish;

// get an ID from an element
$.fn.boxGetAnId = function() {
    return this.length ? (this.getBoxData('id') || this.attr('id')) : null;
};

var Store,
    
    boxStore = {},
    boxUID = 0,
    
    reBoxStore = /^[a-zA-Z_][a-zA-Z0-9_-]*$/,
    reBoxExtractStore = /^([a-zA-Z_][a-zA-Z0-9]*):(.+)/,
    reBoxAdd = /^([a-zA-Z_][a-zA-Z0-9_-]*)$/,
    reBoxNames = /^([A-Z_][A-Z0-9_-]*)(?:\.([A-Z_][A-Z0-9_.:-]+))?$/i;

Store = function(sId) {
    this.id = sId;
};
Store.prototype = {
    boxName: 'store',
    
    boxGetPublishLabel: function() {
        return this.boxName + '.' + this.id + (this.namespace ? '.' + this.namespace : '');
    },
    
    boxPublish: function(sType, oDatas) {
        box.publish({ type: sType, label: this.boxGetPublishLabel(), source: this, data: oDatas });
    },
    
    isValidAddName: function(sName) {
        return typeof sName === 'string' && reBoxAdd.test(sName);
    },
    
    create: function(sName, oDatas) {
        this.namespace = 'create';
        var aName = sName.match(reBoxNames),
            sRoot = this.id + ':',
            sId, sNameObj, oBox, oCfg, sNameCtr, sNameCfg, uRootElm;
        sName = aName[1];
        if(aName[2]) {
            sId = aName[2];
        } else {
            uRootElm = oDatas && oDatas.rootElm;
            if(uRootElm && (typeof uRootElm === 'string' || uRootElm.jquery)) {
                sId = $(uRootElm).boxGetAnId();
            }
            if(!sId) {
                sId = 'box' + (++boxUID);
            }
        }
        sNameObj = sRoot + sName + '.' + sId;
        sNameCtr = sRoot + sName + '#constructor';
        sNameCfg = sRoot + sName + '#config';
        if(boxStore[sNameCtr]) {
            /*<debug>*/if(boxStore[sNameObj]) {
                this.boxPublish(
                    'warning',
                    { message: 'Creating an object that overwrites an existing one ("' + sNameObj + '")' }
                );
            }/*</debug>*/
            oCfg = boxStore[sNameCfg];
            boxStore[sNameObj] = oBox = new boxStore[sNameCtr](sId, oCfg ? clone(oCfg) : {}, oDatas || {});
            return oBox;
        }
        /*<debug>*/this.boxPublish(
            'error',
            { message: 'Trying to create an object from an undefined constructor ("' + sNameCtr + '")' }
        );/*</debug>*/
        return null;
    },
    
    destroy: function(sName) {
        var sId = this.id + ':' + sName;
        if(boxStore[sId]) {
            if(boxStore[sId].boxDestroy) {
                boxStore[sId].boxDestroy();
            }
            delete boxStore[sId];
        }
    },
    
    addConstructor: function(sName, fComponent) {
        this.namespace = 'addConstructor';
        if(this.isValidAddName(sName) && typeof fComponent === 'function') {
            var sRoot = this.id + ':',
                sId = sRoot + sName + '#constructor';
            if(!boxStore[sId]) {
                var oBox = fComponent($, box),
                    oBoxProto = oBox && oBox.prototype;
                if(
                    oBoxProto &&
                    oBoxProto.boxGetName &&
                    oBoxProto.boxPublish &&
                    oBoxProto.boxConfigure &&
                    oBoxProto.boxCreate &&
                    oBoxProto.boxDestroy
                ) {
                    oBoxProto.boxName = sRoot + sName;
                    boxStore[sId] = oBox;
                }/*<debug>*/ else {
                    this.boxPublish(
                        'error',
                        { message: 'No constructor returned ("' + sName + '")' }
                    );
                }
                return;/*</debug>*/
            }
            /*<debug>*/this.boxPublish(
                'error',
                { message: 'Overwriting an existing constructor ("' + sName + '")' }
            );/*</debug>*/
        }/*<debug>*/ else {
            this.boxPublish(
                'error',
                { message: 'Invalid name or function missing ("' + sName + '")' }
            );
        }/*</debug>*/
        return this;
    },
    
    addModule: function(sName, fModule) {
        this.namespace = 'addModule';
        if(this.isValidAddName(sName) && typeof fModule === 'function') {
            var sId = this.id + ':' + sName;
            if(!boxStore[sId]) {
                var oBox = fModule($, box);
                if(oBox) {
                    boxStore[sId] = oBox;
                }/*<debug>*/ else {
                    this.boxPublish(
                        'error',
                        { message: 'No module returned ("' + sName + '")' }
                    );
                }
                return;/*</debug>*/
            }
            /*<debug>*/this.boxPublish(
                'error',
                { message: 'Overwriting an existing module ("' + sName + '")' }
            );/*</debug>*/
        }/*<debug>*/ else {
            this.boxPublish(
                'error',
                { message: 'Invalid name or function missing ("' + sName + '")' }
            );
        }/*</debug>*/
        return this;
    },
    
    addConfig: function(sName, oDatas) {
        this.namespace = 'addConfig';
        var sId = this.id + ':' + sName + '#config';
        /*<debug>*/if(boxStore[sId]) {
            this.boxPublish(
                'warning',
                { message: 'Overwriting an existing configuration ("' + sName + '")' }
            );
        }/*</debug>*/
        boxStore[sId] = oDatas;
        return this;
    },
    
    addDatas: function(sName, uDatas) {
        this.namespace = 'addDatas';
        var sId = this.id + ':' + sName;
        /*<debug>*/if(boxStore[sId]) {
            this.boxPublish(
                'warning',
                { message: 'Overwriting an existing set of datas ("' + sName + '")' }
            );
        }/*</debug>*/
        boxStore[sId] = uDatas;
        return this;
    },
    
    modifyConfig: function(sName, oDatas) {
        var sId = this.id + ':' + sName + '#config',
            oCfg = boxStore[sId],
            sProp;
        if(oCfg) {
            for(sProp in oDatas) {
                if(oDatas.hasOwnProperty(sProp)) {
                    oCfg[sProp] = oDatas[sProp];
                }
            }
        }
        return this;
    },
    
    remove: function(sName) {
        // @todo check if possible to remove?
        if(boxStore[sName]) {
            delete boxStore[sName];
        }
        return this;
    }
};

box.store = function(sId) {
    if(reBoxStore.test(sId)) {
        if(typeof boxStore[sId] === 'undefined') {
            boxStore[sId] = new Store(sId);
        }
        return boxStore[sId];
    }
    /*<debug>*/box.publish({ type: 'error', label: 'box.store', data: { message: 'Invalid store name ("' + sId + '")' } });/*</debug>*/
};

box.get = function(sId) {
    return typeof boxStore[sId] !== 'undefined' ? boxStore[sId] : null;
};

box.store('ui');
box.store('util');
box.store('const').addDatas('NOTIFY_OFF', 0).addDatas('NOTIFY_ON', 1);
/*<debug>*/box.store('internal').addDatas('subscriptions', oSubscriptions);/*</debug>*/

/*jshint eqeqeq:false, strict:false*/
/*global $:false, box:false*/
var reBoxDataInClass = /box\[([^\]]*)\]/,
    reBoxDataPairs = /([^=]+)=([^;]+);?/g,
    reBoxDataLast = /;$/,
    oReBoxDataCache = {};

function writeBoxData(oData) {
    var sData = '',
        sProp;
    for(sProp in oData) {
        if(oData.hasOwnProperty(sProp)) {
            sData += encodeURIComponent(sProp) + '=' + encodeURIComponent(oData[sProp]) + ';';
        }
    }
    return sData.replace(reBoxDataLast, '');
}

$.fn.getBoxData = function(sProp) {
    if(this.length) {
        var sData = this[0].getAttribute('data-box'),
            aParts, oData;
        if(!sData && box.get('const:ELM_DATA_MODE') == 'class') {
            aParts = this[0].className.match(reBoxDataInClass);
            if(aParts) {
                sData = aParts[1];
            }
        }
        if(sData) {
            if(typeof sProp == 'string') {
                if(!oReBoxDataCache[sProp]) {
                    oReBoxDataCache[sProp] = new RegExp(sProp + '=([^;\\]]+)');
                }
                aParts = sData.match(oReBoxDataCache[sProp]);
                return aParts ? decodeURIComponent(aParts[1]) : null;
            } else {
                oData = {};
                while(aParts = reBoxDataPairs.exec(sData)) {
                    oData[decodeURIComponent(aParts[1])] = decodeURIComponent(aParts[2]);
                }
                return oData;
            }
        }
    }
    return null;
};

$.fn.setBoxData = function(oData) {
    if(this.length && box.isObject(oData)) {
        var oExisting = this.getBoxData() || {},
            sData, sProp, sCls;
        for(sProp in oData) {
            if(oData.hasOwnProperty(sProp)) {
                oExisting[sProp] = oData[sProp];
            }
        }
        sData = writeBoxData(oExisting);
        if(box.get('const:ELM_DATA_MODE') == 'class') {
            sCls = this[0].className;
            if(sCls.indexOf('box[') > -1) {
                this[0].className = sCls.replace(reBoxDataInClass, 'box[' + sData + ']');
            } else {
                this.addClass('box[' + sData + ']');
            }
        } else {
            this[0].setAttribute('data-box', sData);
        }
    }
    return this;
};

$.fn.clearBoxData = function(aData) {
    if(this.length) {
        var oExisting = this.getBoxData(),
            i = box.isArray(aData) ? aData.length : 0,
            sData = '',
            sProp;
        if(oExisting) {
            if(i > 0) {
                while(i--) {
                    delete oExisting[aData[i]];
                }
                sData = writeBoxData(oExisting);
            }
            if(box.get('const:ELM_DATA_MODE') == 'class') {
                this[0].className = this[0].className.replace(reBoxDataInClass, 'box[' + sData + ']');
            } else {
                this.attr('data-box', sData);
            }
        }
    }
    return this;
};

// get outer HTML
var divDummyContainer = $('<div></div>');
$.fn.boxOuterHTML = function() {
    divDummyContainer.html('');
    return divDummyContainer.append(this.eq(0).clone()).html();
};

// clear fields onfocus / restore on blur
var reTextFieldTypes = /(text|password)/i,
    reEmpty = /^\s*$/,
    
    clearTextFieldValue = function() {
        if(this.value == this.defaultValue) {
            this.value = '';
        }
    },
    
    restoreTextFieldValue = function() {
        if(reEmpty.test(this.value)) {
            this.value = this.defaultValue;
        }
    };

$.fn.clearTextFields = function() {
    this.each(function(i, elm) {
        if(elm.nodeName.toLowerCase() == 'input' && reTextFieldTypes.test(elm.type)) {
            $(elm).focus(clearTextFieldValue).blur(restoreTextFieldValue);
        } else {
            $('input[type=text], input[type=password]', elm).focus(clearTextFieldValue).blur(restoreTextFieldValue);
        }
    });
};

// get scroll offset
$.fn.getScroll = function() {
    return {
        top: this.scrollTop(),
        left: this.scrollLeft()
    };
};

// get different size types
var getASize = {
    'viewport-width': function() {
        return $(W).width();
    },'viewport-height': function() {
        return $(W).height();
    },'document-width': function() {
        return $(D).width();
    },'document-height': function() {
        return $(D).height();
    },'content-box-width': function(elm) {
        return elm.width();
    },'content-box-height': function(elm) {
        return elm.height();
    },'padding-box-width': function(elm) {
        return elm.innerWidth();
    },'padding-box-height': function(elm) {
        return elm.innerHeight();
    },'border-box-width': function(elm) {
        return elm.outerWidth();
    },'border-box-height': function(elm) {
        return elm.outerHeight();
    },'margin-box-width': function(elm) {
        return elm.outerWidth(true);
    },'margin-box-height': function(elm) {
        return elm.outerHeight(true);
    }
};

// get a size from a keyword
// if no (recognized) keyword, default to content-box size
var getSizeFromKeyword = function(elm, type, keyword) {
    if(elm[0] === W) {
        return getASize['viewport-' + type.toLowerCase()]();
    } else if(elm[0] === D) {
        return getASize['document-' + type.toLowerCase()]();
    } else {
        var method = typeof keyword == 'string' ? keyword.toLowerCase() + '-' + type : 'content-box-' + type;
        if(getASize[method]) {
            return getASize[method](elm);
        } else {
            return getASize['content-box-' + type.toLowerCase()](elm);
        }
    }
};

// get size from a keyword, different keywords possible for width / height
// if no keywords, default to content-box size
$.fn.getSize = function(keyword) {
    return {
        width: getSizeFromKeyword(this, 'width', keyword),
        height: getSizeFromKeyword(this, 'height', keyword)
    };
};
$.fn.getWidth = function(keyword) {
    return getSizeFromKeyword(this, 'width', keyword);
};
$.fn.getHeight = function(keyword) {
    return getSizeFromKeyword(this, 'height', keyword);
};

// set size, only with numbers, or 'auto' keyword
$.fn.setSize = function(datas) {
    if(typeof datas == 'number' || datas == 'auto') {
        this.width(datas).height(datas);
    } else if(typeof datas == 'object') {
        if(typeof datas.width == 'number' || datas.width == 'auto') {
            this.width(datas.width);
        }
        if(typeof datas.height == 'number' || datas.height == 'auto') {
            this.height(datas.height);
        }
    }
    return this;
};
$.fn.setWidth = function(value) {
    if(typeof value == 'number' || value == 'auto') {
        this.width(value);
    }
    return this;
};
$.fn.setHeight = function(value) {
    if(typeof value == 'number' || value == 'auto') {
        this.height(value);
    }
    return this;
};

// get a position from a keyword
// if no (recognized) keyword, default to offset from the document origin
var getXYFromKeyword = function(elm, keyword) {
    if(elm[0] === D) {
        return {top: 0, left: 0};
    } else if(elm[0] === W) {
        return elm.getScroll();
    } else if(keyword == 'positioned-ancestor') {
        return elm.position();
    } else {
        return elm.offset();
    }
};

// get position from a keyword, different keywords possible for top / left
// if no keywords, default to offsets from the document origin
$.fn.getXY = function(keyword) {
    return getXYFromKeyword(this, keyword);
};
$.fn.getX = function(keyword) {
    return getXYFromKeyword(this, keyword).left;
};
$.fn.getY = function(keyword) {
    return getXYFromKeyword(this, keyword).top;
};

// set position, only with numbers
$.fn.setXY = function(datas) {
    if(typeof datas == 'number') {
        this.css({top: datas + 'px', left: datas + 'px'});
    } else if(typeof datas == 'object') {
        var pos = {};
        if(typeof datas.top == 'number') {
            pos.top = datas.top + 'px';
        } else if(datas.top == 'auto') {
            pos.top = 'auto';
        }
        if(typeof datas.left == 'number') {
            pos.left = datas.left + 'px';
        } else if(datas.left == 'auto') {
            pos.left = 'auto';
        }
        this.css(pos);
    }
};

var getAPosition = {
    'root': function(curElm, refElm, type) {
        return refElm.offset()[type];
    },'positioned-ancestor': function(curElm, refElm, type) {
        return refElm.position()[type];
    },'before': function(curElm, refElm, type, relType) {
        var curDim = curElm['get' + relType]('border-box');
        var refPos = refElm.getXY()[type];
        return refPos - curDim;
    },'start': function(curElm, refElm, type, relType) {
        return refElm.getXY()[type];
    },'middle': function(curElm, refElm, type, relType) {
        var curDim = curElm['get' + relType]('border-box');
        var refDim = getSizeFromKeyword(refElm, relType);
        var refPos = refElm.getXY()[type];
        return refPos + (refDim - curDim) / 2;
    },'end': function(curElm, refElm, type, relType) {
        var curDim = curElm['get' + relType]('border-box');
        var refDim = getSizeFromKeyword(refElm, relType);
        var refPos = refElm.getXY()[type];
        return refPos + refDim - curDim;
    },'in-before': function(curElm, refElm, type, relType) {
        return -curElm['get' + relType]('border-box');
    },'in-start': function() {
        return 0;
    },'in-middle': function(curElm, refElm, type, relType) {
        var curDim = curElm['get' + relType]('border-box');
        var refDim = getSizeFromKeyword(refElm, relType);
        return (refDim - curDim) / 2;
    },'in-end': function(curElm, refElm, type, relType) {
        var curDim = curElm['get' + relType]('border-box');
        var refDim = getSizeFromKeyword(refElm, relType);
        return refDim - curDim;
    }
};

var getAlternateSelectorNames = function(name) {
    var ref = {
        'viewport': W,
        'document': D
    };
    return ref[name] || name;
};

var getStyleDim = function(styles, elm) {
    var s;
    if(typeof styles.width == 'string') {
        s = styles.width.split(':');
        if(s.length == 2) {
            styles.width = getASize[s[1] + '-width']($(getAlternateSelectorNames(s[0])), 'width');
        }
        if(!isNaN(styles['min-width']) && !isNaN(styles.width)) {
            styles.width = styles.width < styles['min-width'] ? styles['min-width'] : styles.width;
            delete styles['min-width'];
        }
        if(!isNaN(styles['max-width']) && !isNaN(styles.width)) {
            styles.width = styles.width > styles['max-width'] ? styles['max-width'] : styles.width;
            delete styles['max-width'];
        }
    }
    if(typeof styles.height == 'string') {
        s = styles.height.split(':');
        if(s.length == 2) {
            styles.height = getASize[s[1] + '-height']($(getAlternateSelectorNames(s[0])), 'height');
        }
        if(!isNaN(styles['min-height']) && !isNaN(styles.height)) {
            styles.height = styles.height < styles['min-height'] ? styles['min-height'] : styles.height;
            delete styles['min-height'];
        }
        if(!isNaN(styles['max-height']) && !isNaN(styles.height)) {
            styles.height = styles.height > styles['max-height'] ? styles['max-height'] : styles.height;
            delete styles['max-height'];
        }
    }
    return styles;
};

var getStylePos = function(styles, elm) {
    var s;
    if(typeof styles.top == 'string') {
        s = styles.top.split(':');
        if(s.length == 2) {
            styles.top = getAPosition[s[1]](elm, $(getAlternateSelectorNames(s[0])), 'top', 'Height');
        }
        if(!isNaN(styles['min-top']) && !isNaN(styles.top)) {
            styles.top = styles.top < styles['min-top'] ? styles['min-top'] : styles.top;
            delete styles['min-top'];
        }
        if(!isNaN(styles['max-top']) && !isNaN(styles.top)) {
            styles.top = styles.top > styles['max-top'] ? styles['max-top'] : styles.top;
            delete styles['max-top'];
        }
    }
    if(typeof styles.left == 'string') {
        s = styles.left.split(':');
        if(s.length == 2) {
            styles.left = getAPosition[s[1]](elm, $(getAlternateSelectorNames(s[0])), 'left', 'Width');
        }
        if(!isNaN(styles['min-left']) && !isNaN(styles.left)) {
            styles.left = styles.left < styles['min-left'] ? styles['min-left'] : styles.left;
            delete styles['min-left'];
        }
        if(!isNaN(styles['max-left']) && !isNaN(styles.left)) {
            styles.left = styles.left > styles['max-left'] ? styles['max-left'] : styles.left;
            delete styles['max-left'];
        }
    }
    return styles;
};

// apply styles from a reference element or not
$.fn.applyStyles = function(styles) {
    if(this.length && styles && typeof styles == 'object') {
        var elm = this.eq(0), nStyles = clone(styles);
        
        // size can affect position, so compute first
        nStyles = getStyleDim(nStyles, elm);
        
        if(!isNaN(nStyles.width)) {
            elm.width(nStyles.width);
            delete nStyles.width;
        }
        if(!isNaN(nStyles.height)) {
            elm.height(nStyles.height);
            delete nStyles.height;
        }
        
        nStyles = getStylePos(nStyles, elm);
        
        elm.css(nStyles);
    }
    return this;
};

// get styles from a reference element or not
$.fn.getStyles = function(styles) {
    if(this.length && styles && typeof styles == 'object') {
        var elm = this.eq(0), nStyles = clone(styles);
        
        nStyles = getStyleDim(nStyles, elm);
        nStyles = getStylePos(nStyles, elm);
        
        return nStyles;
    }
    return null;
};

})();

/*jshint eqeqeq:false*/
/*global box:false*/
box.get('util').addModule('component', function($, box) {
    var UtilComponent = function() {};
    UtilComponent.prototype = {
        boxCreate: function() {},
        boxDestroy: function() {},
        boxConfigure: function(oDefaultCfg) {
            this.cfg = oDefaultCfg;
        },
        boxGetName: function() {
            return this.boxName + '.' + this.id;
        },
        boxGetNs: function(sName) {
            return 'box-' + this.boxName + '-' + (sName ? sName + '-' : '') + this.id;
        },
        boxPublish: function(sType, oDatas) {
            return box.publish({
                type: sType,
                label: this.boxBoundEvtLabel || this.boxGetName(),
                source: this,
                data: oDatas,
                propagation: !oDatas || oDatas.propagation !== false
            });
        },
        boxChangeEvtLabel: function(sName) {
            this.boxBoundEvtLabel = sName;
            return this;
        },
        boxRestoreEvtLabel: function() {
            delete this.boxBoundEvtLabel;
        }
    };
    
    return {
        create: function(oComponent) {
            function Com(sId, oDefaultCfg, oDatas) {
                this.id = sId;
                this.boxConfigure(oDefaultCfg, oDatas);
                this.boxCreate(oDatas);
            }
            var ComSuper;
            if(oComponent.inherit) {
                ComSuper = box.get(oComponent.inherit + '#constructor');
                if(!ComSuper) {
                    box.publish(
                        'warning',
                        { message: 'inherit component was not found ("' + oComponent.inherit + '")' }
                    );
                }
            }
            box.inherit(Com, ComSuper || UtilComponent);
            if(oComponent.extend && typeof oComponent.extend == 'object') {
                box.extend(Com, oComponent.extend);
            }
            return Com;
        }
    };
});

/*jshint eqeqeq:false, strict:false*/
/*global box:false*/
box.get('util').addModule('delegate-click', function($, box) {
    var nIter = 1,
        bAll = false,
        bListening = false,
        bTouch = false,
        oPosTouch;

    function parseElmData(oElm) {
        var oData = $(oElm).getBoxData();
        return oData && oData.label ? oData : null;
    }

    function tryPublish(oElm, oEvt) {
        var oData = parseElmData(oElm);
        if(oData) {
            oData.originalEvent = oEvt;
            oData.element = oElm;
            oData.url = oElm.getAttribute('href', 2);
            oData.propagate = false;
            box.publish({
                type: 'click',
                label: oData.label,
                data: oData
            });
        } else if(bAll) {
            box.publish({
                type: 'click',
                label: 'util:delegate-click',
                data: { originalEvent: oEvt, element: oElm, url: oElm.getAttribute('href', 2), propagate: false }
            });
        }
    }

    function checkClick(oEvt) {
        if(oEvt.type == 'click' && bTouch) {
            bTouch = false;
            return;
        }
        var oElm = oEvt.target,
            i = nIter,
            sTagName;
        while(i-- && oElm && oElm !== this) {
            sTagName = oElm.tagName;
            if(sTagName == 'A' || (sTagName == 'BUTTON' && oElm.type == 'button')) {
                tryPublish(oElm, oEvt);
                break;
            } else {
                oElm = oElm.parentNode;
            }
        }
    }

    function manageTouchStart(oEvt) {
        var oPos = oEvt.originalEvent.touches[0];
        oPosTouch = { x1: oPos.pageX, y1: oPos.pageY };
        bTouch = false;
    }

    function manageTouchMove(oEvt) {
        var oPos = oEvt.originalEvent.touches[0];
        oPosTouch.x2 = oPos.pageX;
        oPosTouch.y2 = oPos.pageY;
    }

    function manageTouchEnd(oEvt) {
        if(
            typeof oPosTouch.x2 == 'undefined' ||
            (Math.abs(oPosTouch.x2 - oPosTouch.x1) < 30 && Math.abs(oPosTouch.y2 - oPosTouch.y1) < 30)
        ) {
            bTouch = true;
            checkClick(oEvt);
        }
    }

    return {
        start: function() {
            if(!bListening) {
                $(box.getDoc().body)
                    .bind('click.box-delegate-click', checkClick)
                    .bind('touchstart.box-delegate-click', manageTouchStart)
                    .bind('touchmove.box-delegate-click', manageTouchMove)
                    .bind('touchend.box-delegate-click', manageTouchEnd);
                bListening = true;
            }
            return this;
        },

        stop: function() {
            if(bListening) {
                $(box.getDoc().body).unbind('.box-delegate-click');
                bListening = false;
            }
            return this;
        },

        setMaxIter: function(n) {
            if(!isNaN(n) && n > nIter) {
                nIter = n;
            }
            return this;
        },

        startDispatchForAll: function() {
            bAll = true;
            return this;
        },

        endDispatchForAll: function() {
            bAll = false;
            return this;
        }
    };
});

/*jshint eqeqeq:false*/
/*global box:false*/
box.get('ui').addConfig('carousel', {
    htmlBtnPrev: '<a href="#" class="{$clsBtnPrev} {$clsBtnDisabled}">{$txtBtnPrev}</a>',
    htmlBtnNext: '<a href="#" class="{$clsBtnNext} {$clsBtnDisabled}">{$txtBtnNext}</a>',
    htmlPagination: '<div class="{$clsPagination}"><ul>{$content}</ul></div>',
    htmlPage: '<li{$clsActivePage}><a href="#">{$content}</a></li>',
    
    clsRoot: 'carousel',
    clsMask: 'carousel-window',
    clsBtnPrev: 'carousel-btn-prev',
    clsBtnNext: 'carousel-btn-next',
    clsBtnDisabled: 'carousel-btn-disabled',
    clsPagination: 'carousel-pagination',
    clsActivePage: 'carousel-page-on'
}).addConstructor('carousel', function($, box) {
    var getBtnsHTML, getPosition, getIndex, setCurrent, prepareCircularMovePrev, prepareCircularMoveNext,
        prepareMove, positionFirstElements, checkRepositionFirstElements, moveToPosition,
        getPageNumber, getPrevPageIndex, getNextPageIndex, managePagination,
        oProto;
    
    getBtnsHTML = function(oCom, type) {
        var oCfg = oCom.cfg;
        type = type == 'next' ? 'BtnNext' : 'BtnPrev';
        return oCfg['html' + type]
            .replace('{$cls' + type + '}', oCfg['cls' + type])
            .replace('{$clsBtnDisabled}', oCfg.clsBtnDisabled)
            .replace('{$txt' + type + '}', oCfg['txt' + type]);
    };
    
    getPosition = function(carousel) {
        return parseInt(carousel.moveable.css(carousel.property), 10) || 0;
    };
    
    getIndex = function(carousel, index) {
        if(isNaN(index)) {
            return 0;
        } else if(index < 0) {
            return index + carousel.length;
        } else if(index < carousel.length) {
            return index;
        } else {
            return index - carousel.length;
        }
    };
    
    setCurrent = function(carousel, index) {
        carousel.current = getIndex(carousel, index);
        if(carousel.currentPage !== undefined) {
            var page = Math.ceil((carousel.current + carousel.display) / carousel.display);
            $('li', carousel.pagination)
                .eq(carousel.currentPage - 1)
                    .removeClass(carousel.cfg.clsActivePage)
                .end()
                .eq(page - 1)
                    .addClass(carousel.cfg.clsActivePage);
            carousel.currentPage = page;
        }
    };
    
    prepareCircularMovePrev = function(carousel, index) {
        if(carousel.autoplay) {
            carousel.pauseAutoplay();
        }
        
        carousel.moving = true;
        
        var actualPos = getPosition(carousel);
        var futurePos = actualPos + carousel.moveBy * (carousel.current - index);
        var itemPos = parseInt(carousel.items.eq(carousel.current).css(carousel.property), 10);
        
        var min = index;
        var max = carousel.current;
        var c, pos;
        
        for(var i = min; i < max; i++) {
            c = getIndex(carousel, i);
            pos = itemPos - (carousel.current - i) * carousel.moveBy;
            carousel.items.eq(c).css(carousel.property, pos + 'px');
        }
        
        setCurrent(carousel, index);
        moveToPosition(carousel, futurePos);
    };
    
    prepareCircularMoveNext = function(carousel, index) {
        if(carousel.autoplaying) {
            carousel.pauseAutoplay();
        }
        
        carousel.moving = true;
        
        var actualPos = getPosition(carousel);
        var futurePos = actualPos + (-carousel.moveBy * (index - carousel.current));
        var itemPos = parseInt(carousel.items.eq(carousel.current).css(carousel.property), 10) + carousel.display * carousel.moveBy;
        
        var min = carousel.current + carousel.display;
        var max = index + carousel.display;
        var c, pos;
        
        if(carousel.hasOffset && carousel.offset && max >= carousel.length) {
            max++;
        }
        
        for(var i = min; i < max; i++) {
            c = getIndex(carousel, i);
            pos = itemPos + (i - carousel.display - carousel.current) * carousel.moveBy;
            carousel.items.eq(c).css(carousel.property, pos + 'px');
        }
        
        setCurrent(carousel, index);
        moveToPosition(carousel, futurePos);
    };
    
    prepareMove = function(carousel, index) {
        if(carousel.autoplaying) {
            carousel.pauseAutoplay();
        }
        
        carousel.moving = true;
        
        index = Math.min(index, carousel.length - carousel.display);
        if(carousel.buttons) {
            if(!index) {
                carousel.buttonPrev.addClass(carousel.cfg.clsBtnDisabled);
                carousel.buttonNext.removeClass(carousel.cfg.clsBtnDisabled);
            } else if(index == carousel.length - carousel.display) {
                carousel.buttonPrev.removeClass(carousel.cfg.clsBtnDisabled);
                carousel.buttonNext.addClass(carousel.cfg.clsBtnDisabled);
            } else {
                carousel.buttonPrev.removeClass(carousel.cfg.clsBtnDisabled);
                carousel.buttonNext.removeClass(carousel.cfg.clsBtnDisabled);
            }
        }
        
        setCurrent(carousel, index);
        moveToPosition(carousel, -carousel.moveBy * index);
    };
    
    positionFirstElements = function(carousel, fromReposition) {
        var min = carousel.startAt;
        var max = min + carousel.length;
        var c, pos;
        for(var i = min; i < max; i++) {
            c = getIndex(carousel, i);
            carousel.items.eq(c).css(carousel.property, i * carousel.moveBy + 'px');
        }
    };
    
    checkRepositionFirstElements = function(carousel, to) {
        if(carousel.circular && to == (-(carousel.length * carousel.moveBy) + carousel.offset)) {
            carousel.moveable.css(carousel.property, carousel.offset + 'px');
            positionFirstElements(carousel, true);
        }
    };
    
    moveToPosition = function(carousel, to) {
        carousel.boxPublish('startmove');
        if(carousel.duration) {
            var p = {};
            p[carousel.property] = to;
            carousel.moveable.animate(p, carousel.duration, function() {
                checkRepositionFirstElements(carousel, to);
                if(carousel.autoplaying) {
                    carousel.startAutoplay(carousel.autoplay);
                }
                carousel.moving = false;
                carousel.boxPublish('endmove');
            });
        } else {
            carousel.moveable.css(carousel.property, to + 'px');
            checkRepositionFirstElements(carousel, to);
            if(carousel.autoplaying) {
                carousel.startAutoplay(carousel.autoplay);
            }
            carousel.moving = false;
            carousel.boxPublish('endmove');
        }
    };
    
    getPageNumber = function(carousel) {
        return Math.ceil(carousel.length / carousel.display);
    };
    
    getPrevPageIndex = function(carousel) {
        var page = carousel.currentPage - 1;
        if(page < 1) {
            page = carousel.circular ? getPageNumber(carousel) : 1;
        }
        return page * carousel.display - carousel.display;
    };
    
    getNextPageIndex = function(carousel) {
        var page = carousel.currentPage + 1;
        if(page > getPageNumber(carousel)) {
            page = carousel.circular ? 1 : getPageNumber(carousel);
        }
        return page * carousel.display - carousel.display;
    };
    
    managePagination = function(e, carousel) {
        if(e.target.nodeName.toLowerCase() == 'a') {
            e.preventDefault();
            if(!carousel.moving) {
                carousel.moveToItem((Number($(e.target).text()) - 1) * carousel.display + 1);
            }
        }
    };
    
    oProto = {
        boxConfigure: function(oDefaultCfg, oDatas) {
            var aCfg = [
                    'htmlBtnPrev', 'htmlBtnNext', 'htmlPagination', 'htmlPage', 'clsBtnPrev', 'clsBtnNext', 'clsBtnDisabled', 'clsPagination', 'clsActivePage', 'txtBtnPrev', 'txtBtnNext'
                ],
                i = aCfg.length,
                sName;
            while(i--) {
                sName = aCfg[i];
                if(oDatas[sName]) {
                    oDefaultCfg[sName] = oDatas[sName];
                }
            }
            this.cfg = oDefaultCfg;
        },
        
        boxCreate: function(datas) {
            var that = this,
                oCfg = that.cfg;
            
            that.property = datas.horizontal ? 'left' : 'top';
            that.buttons = datas.buttons === false ? false : true;
            that.circular = !!datas.circular || false;
            that.duration = !isNaN(datas.duration) ? datas.duration : null;
            that.autoplay = !isNaN(datas.autoplay) && datas.autoplay > 10 && that.circular ? datas.autoplay : null;
            that.hasOffset = !!datas.hasOffset;
            
            that.element = $(datas.rootElm).addClass(oCfg.clsRoot);
            that.mask = that.element.find('.' + oCfg.clsMask);
            that.moveable = datas.moveable ? that.mask.find(datas.moveable) : that.mask.children();
            that.items = datas.items ? that.moveable.find(datas.items) : that.moveable.children();
            
            that.length = that.items.length;
            that.display = datas.display;
            that.startAt = !isNaN(datas.startAt) ? datas.startAt - 1 : 0;
            // startAt must be >= 0 && < length
            if(that.startAt < 0 || that.startAt >= that.length) {
                that.startAt = 0;
            }
            
            that.offset = parseInt(that.moveable.css(that.property), 10) || 0;
            // negative offset only, for circular carousel
            if(that.hasOffset && that.offset) {
                if(!that.circular) {
                    that.boxPublish('error', { message: 'carousel must be circular when an offset is present' });
                    return;
                }
                if(that.offset > 0) {
                    that.boxPublish('error', { message: 'offset must be negative' });
                    return;
                }
                if(-that.offset > that.moveBy) {
                    that.boxPublish('error', { message: 'offset must be less than moveBy' });
                    return;
                }
                if(that.length < that.display + 2) {
                    that.boxPublish('error', { message: 'when an offset is present, there must be at least 2 more items than what is displayed' });
                    return;
                }
            }
            if(that.hasOffset && that.offset < 0 && that.length > that.display + 2) {
                ++that.display;
            }
            that.moveBy = that.items.eq(0)[that.property == 'top' ? 'outerHeight' : 'outerWidth'](true);
            
            setCurrent(that, that.startAt);
            
            if(that.property == 'left') {
                that.moveable.width(that.moveBy * that.length);
            }
            if(that.circular) {
                positionFirstElements(that);
            }
            
            if(that.length > that.display) {
                that.disabled = false;
                
                if(!that.circular && that.current > that.length - that.display) {
                    that.current = that.length - that.display;
                }
                
                if(that.current) {
                    that.moveable.css(that.property, -that.moveBy * that.current + that.offset);
                    that.offset = -that.moveBy * that.current + that.offset;
                }
                
                if(that.buttons) {
                    that.buttonNext = $(getBtnsHTML(that, 'next')).appendTo(that.element).click(function(e) {
                        that.moveNext(e);
                        e.preventDefault();
                    });
                    that.buttonPrev = $(getBtnsHTML(that, 'prev')).prependTo(that.element).click(function(e) {
                        that.movePrev(e);
                        e.preventDefault();
                    });
                    
                    if(that.circular || that.current) {
                        that.buttonPrev.removeClass(oCfg.clsBtnDisabled);
                    }
                    if(that.circular || that.current + that.display < that.length) {
                        that.buttonNext.removeClass(oCfg.clsBtnDisabled);
                    }
                }
                
                if(datas.paginate) {
                    that.addPagination();
                }
                
                if(that.autoplay) {
                    that.startAutoplay(that.autoplay);
                }
            } else {
                that.disabled = true;
            }
        },
        
        movePrev: function() {
            if(!this.moving) {
                var index = !isNaN(this.currentPage) ? getPrevPageIndex(this) : this.current - 1;
                if(this.circular) {
                    prepareCircularMovePrev(this, index);
                } else if(index > -1) {
                    prepareMove(this, index);
                }
            }
        },
        
        moveNext: function() {
            if(!this.moving) {
                var index = !isNaN(this.currentPage) ? getNextPageIndex(this) : this.current + 1;
                if(this.circular) {
                    prepareCircularMoveNext(this, index);
                } else if(index < this.length) {
                    prepareMove(this, index);
                }
            }
        },
        
        moveToItem: function(i) {
            if(!this.moving && typeof i == 'number') {
                --i;
                if(this.items[i]) {
                    if(this.currentPage) {
                        var page = Math.floor(i / this.display) + 1;
                        i = (page - 1) * this.display;
                    }
                    if(this.circular) {
                        if(i > this.current && i - this.current > this.length - i) {
                            i = i - this.length;
                        } else if(i < this.current && this.current - i > i + this.length - this.current) {
                            i = this.length + i;
                        }
                        if(i < this.current) {
                            prepareCircularMovePrev(this, i);
                        } else if(i > this.current) {
                            prepareCircularMoveNext(this, i);
                        }
                    } else {
                        prepareMove(this, i);
                    }
                }
            }
        },
        
        startAutoplay: function(delay) {
            var that = this;
            if(that.circular && (!isNaN(delay) || that.autoplay)) {
                if(isNaN(delay)) {
                    delay = that.autoplay;
                } else {
                    that.autoplay = delay;
                }
                that.autoplaying = true;
                that.timer = box.getWin().setInterval(function() {
                    that.moveNext();
                }, delay);
            }
        },
        
        pauseAutoplay: function() {
            box.getWin().clearInterval(this.timer);
            this.timer = null;
        },
        
        endAutoplay: function() {
            this.pauseAutoplay();
            this.autoplaying = false;
        },
        
        addPagination: function() {
            var that = this,
                html = that.cfg.htmlPagination.replace('{$clsPagination}', that.cfg.clsPagination),
                pages = getPageNumber(that),
                items = '',
                startItem,
                endItem,
                oCfg = that.cfg;
            for(var i = 1; i <= pages; i++) {
                startItem = (i - 1) * that.display;
                endItem = startItem + that.display - 1;
                if(that.startAt >= startItem && that.startAt <= endItem) {
                    that.currentPage = i;
                    items += oCfg.htmlPage.replace('{$clsActivePage}', ' class="' + oCfg.clsActivePage + '"');
                } else {
                    items += oCfg.htmlPage.replace('{$clsActivePage}', '');
                }
                items = items.replace(/{\$content}/g, i);
            }
            html = html.replace('{$content}', items);
            this.pagination = $(html).appendTo(that.element).click(function(e) {
                managePagination(e, that);
            });
        },
        
        removePagination: function() {
            this.pagination.unbind('click').remove();
        }
    };
    
    return box.get('util:component').create({
        extend: oProto
    });
});

/*jshint eqeqeq:false*/
/*global box:false*/
box.get('ui').addConfig('element', {
    clsDisabled: 'disabled'
}).addConstructor('element', function($, box) {
    var rePosition = /^(relative|absolute|fixed)$/,
        oInsertMethods = {
            beforebegin: 'insertBefore',
            afterbegin: 'prependTo',
            beforeend: 'appendTo',
            afterend: 'insertAfter'
        },
        containsElm, getElmRegion, getRegionForUi, getSelfXY, getToXY, endAnim,
        oProto;
    
    if(box.isHostMethod(box.getDoc().documentElement, 'contains')) {
        containsElm = function($ancestor, $descendant) {
            return $ancestor[0].contains($descendant[0]);
        };
    } else if(box.isHostMethod(box.getDoc().documentElement, 'compareDocumentPosition')) {
        containsElm = function($ancestor, $descendant) {
            return !!($ancestor[0].compareDocumentPosition($descendant[0]) & 16);
        };
    }
    
    getElmRegion = function($elm, oUi) {
        var bRelative = oUi ? oUi.hasAncestor($elm) && rePosition.test($elm.css('position')) : false,
            oOffsets = bRelative ? { top: 0, left: 0 } : $elm.offset(),
            nWidth = $elm.outerWidth(),
            nHeight = $elm.outerHeight(),
            oBorders;
        if(bRelative) {
            oBorders = {
                x: (parseInt($elm.css('borderLeftWidth'), 10) + parseInt($elm.css('borderRightWidth'), 10)) || 0,
                y: (parseInt($elm.css('borderTopWidth'), 10) + parseInt($elm.css('borderBottomWidth'), 10)) || 0
            };
        } else {
            oBorders = { x: 0, y: 0 }
        }
        return {
            top: oOffsets.top,
            right: oOffsets.left + nWidth - oBorders.x,
            bottom: oOffsets.top + nHeight - oBorders.y,
            left: oOffsets.left,
            width: nWidth,
            height: nHeight
        };
    };
    
    getRegionForUi = function(uElm, oUi) {
        var sType = uElm == 'viewport' || !uElm ?
            'getViewRegion' :
            uElm == 'document' ?
                'getDocRegion' :
                null;
        return sType ? oUi[sType]() : getElmRegion($(uElm), oUi);
    };
    
    getSelfXY = function(sPos, oSize) {
        var oXY;
        switch(sPos) {
            case 'tl':
                oXY = { x: 0, y: 0 };
                break;
            case 'tc':
                oXY = { x: -oSize.width / 2, y: 0 };
                break;
            case 'tr':
                oXY = { x: -oSize.width, y: 0 };
                break;
            case 'cl':
                oXY = { x: 0, y: -oSize.height / 2 };
                break;
            case 'cc':
                oXY = { x: -oSize.width / 2, y: -oSize.height / 2 };
                break;
            case 'cr':
                oXY = { x: -oSize.width, y: -oSize.height / 2 };
                break;
            case 'bl':
                oXY = { x: 0, y: -oSize.height };
                break;
            case 'bc':
                oXY = { x: -oSize.width / 2, y: -oSize.height };
                break;
            case 'br':
                oXY = { x: -oSize.width, y: -oSize.height };
                break;
        }
        return oXY;
    };
    
    getToXY = function(sPos, oRegion) {
        var oXY;
        switch(sPos) {
            case 'tl':
                oXY = { x: oRegion.left, y: oRegion.top };
                break;
            case 'tc':
                oXY = { x: oRegion.left + oRegion.width / 2, y: oRegion.top };
                break;
            case 'tr':
                oXY = { x: oRegion.right, y: oRegion.top };
                break;
            case 'cl':
                oXY = { x: oRegion.left, y: oRegion.top + oRegion.height / 2 };
                break;
            case 'cc':
                oXY = { x: oRegion.left + oRegion.width / 2, y: oRegion.top + oRegion.height / 2 };
                break;
            case 'cr':
                oXY = { x: oRegion.right, y: oRegion.top + oRegion.height / 2 };
                break;
            case 'bl':
                oXY = { x: oRegion.left, y: oRegion.top + oRegion.height };
                break;
            case 'bc':
                oXY = { x: oRegion.left + oRegion.width / 2, y: oRegion.top + oRegion.height };
                break;
            case 'br':
                oXY = { x: oRegion.right, y: oRegion.top + oRegion.height };
                break;
        }
        return oXY;
    };
    
    endAnim = function(oCom, sEvtType) {
        return function() {
            oCom.animating = false;
            delete oCom.phase;
            oCom.boxPublish(sEvtType);
        };
    };
    
    oProto = {
        boxConfigure: function(oDefaultCfg, oDatas) {
            this.cfg = oDefaultCfg;
            if(oDatas.clsDisabled) {
                this.cfg.clsDisabled = oDatas.clsDisabled;
            }
        },
        
        boxCreate: function(oDatas) {
            this.rootElm = $(oDatas.rootElm || oDatas.rootHtml);
            this.inDOM = this.hasAncestor(box.getDoc().body);
            this.disabled = false;
            this.animating = false;
        },
        
        disable: function() {
            if(!this.disabled) {
                this.rootElm.attr('aria-disabled', 'true').addClass(this.cfg.clsDisabled);
                this.disabled = true;
            }
            return this;
        },
        
        enable: function() {
            if(this.disabled) {
                this.rootElm.removeAttr('aria-disabled').removeClass(this.cfg.clsDisabled);
                this.disabled = false;
            }
            return this;
        },
        
        hasAncestor: function(uElm) {
            return this.rootElm.length ? containsElm($(uElm), this.rootElm) : false;
        },
        
        hasDescendant: function(uElm) {
            return this.rootElm.length ? containsElm(this.rootElm, $(uElm)) : false;
        },
        
        getViewRegion: function() {
            var $win = box.getJWin(),
                nTop = $win.scrollTop(),
                nLeft = $win.scrollLeft(),
                nWidth = $win.width(),
                nHeight = $win.height();
            return {
                top: nTop,
                right: nLeft + nWidth,
                bottom: nTop + nHeight,
                left: nLeft,
                width: nWidth,
                height: nHeight
            };
        },
        
        getDocRegion: function() {
            var $doc = box.getJDoc(),
                nWidth = $doc.width(),
                nHeight = $doc.height();
            return {
                top: 0,
                right: nWidth,
                bottom: nHeight,
                left: 0,
                width: nWidth,
                height: nHeight
            };
        },
        
        getPageXY: function() {
            if(this.inDOM) {
                var oXY = this.rootElm.offset();
                return { pageX: oXY.left, pageY: oXY.top };
            }
            return null;
        },
        
        getRegion: function() {
            if(this.inDOM) {
                return getElmRegion(this.rootElm);
            }
            return null;
        },
        
        getLimits: function(uElm) {
            if(this.inDOM) {
                var oRegion = getRegionForUi(uElm, this),
                    nWidth = this.rootElm.outerWidth(),
                    nHeight = this.rootElm.outerHeight();
                return {
                    minTop: oRegion.top,
                    maxTop: oRegion.bottom - nHeight,
                    minLeft: oRegion.left,
                    maxLeft: oRegion.right - nWidth,
                    width: nWidth,
                    height: nHeight
                };
            }
            return null;
        },
        
        setId: function(sId) {
            this.rootElm.attr('id', sId);
            return this;
        },
        
        setVisibility: function(bVisible) {
            this.rootElm.css('visibility', bVisible ? 'visible' : 'hidden');
            return this;
        },
        
        setStyles: function(oDatas) {
            if(!this.animating) {
                this.rootElm.css(oDatas);
            }
            return this;
        },
        
        setRoot: function(uElm) {
            if(!this.inDOM) {
                this.rootElm = $(uElm);
            }
            return this;
        },
        
        setContent: function(sHtml) {
            if(!this.animating) {
                this.rootElm.html(sHtml);
                this.boxPublish('changecontent', { content: sHtml });
            }
            return this;
        },
        
        insert: function(sWhere, uElm) {
            if(!this.inDOM) {
                var sMethod = oInsertMethods[sWhere.toLowerCase()];
                if(sMethod) {
                    this.rootElm[sMethod](uElm);
                    this.inDOM = true;
                    this.boxPublish('insert');
                }
            }
            return this;
        },
        
        remove: function() {
            if(this.inDOM) {
                this.stop();
                this.rootElm.remove();
                this.inDOM = false;
                this.boxPublish('remove');
            }
            return this;
        },
        
        getPosition: function(oPosition, uElm) {
            if(this.inDOM) {
                var sSelf = oPosition.self || 'tl',
                    sTo = oPosition.to || 'tl',
                    oSelfSize = { width: this.rootElm.outerWidth(), height: this.rootElm.outerHeight() },
                    oToRegion = getRegionForUi(uElm, this),
                    oSelfXY = getSelfXY(sSelf, oSelfSize),
                    oToXY,
                    nOffsetTop = oPosition.offsetTop || 0,
                    nOffsetRight = oPosition.offsetRight || 0,
                    nOffsetBottom = oPosition.offsetBottom || 0,
                    nOffsetLeft = oPosition.offsetLeft || 0,
                    nTop,
                    nLeft;
                oToRegion.left -= nOffsetLeft;
                oToRegion.right += nOffsetRight;
                oToRegion.width += nOffsetLeft + nOffsetRight;
                oToRegion.top -= nOffsetTop;
                oToRegion.bottom += nOffsetBottom;
                oToRegion.height += nOffsetTop + nOffsetBottom;
                oToXY = getToXY(sTo, oToRegion);
                if(oSelfXY && oToXY) {
                    nTop = oSelfXY.y + oToXY.y;
                    nLeft = oSelfXY.x + oToXY.x;
                    return {
                        top: nTop,
                        right: nLeft + oSelfSize.width,
                        bottom: nTop + oSelfSize.height,
                        left: nLeft,
                        width: oSelfSize.width,
                        height: oSelfSize.height,
                        target: oToRegion
                    };
                }
            }
            return null;
        },
        
        setPosition: function(oPosition, uElm) {
            var oRegion = this.getPosition(oPosition, uElm);
            if(oRegion) {
                this.rootElm.css({ top: oRegion.top, left: oRegion.left });
            }
            return this;
        },
        
        getPositionConstrained: function(oPosition, uElm) {
            var oRegion = this.getPosition(oPosition, uElm);
            if(oRegion) {
                if(oRegion.bottom > oRegion.target.bottom) { oRegion.top = oRegion.target.bottom - oRegion.height; }
                if(oRegion.top < oRegion.target.top) { oRegion.top = oRegion.target.top; }
                if(oRegion.right > oRegion.target.right) { oRegion.left = oRegion.target.right - oRegion.width; }
                if(oRegion.left < oRegion.target.left) { oRegion.left = oRegion.target.left; }
                return oRegion;
            }
            return null;
        },
        
        setPositionConstrained: function(oPosition, uElm) {
            var oRegion = this.getPositionConstrained(oPosition, uElm);
            if(oRegion) {
                this.rootElm.css({ top: oRegion.top, left: oRegion.left });
            }
            return this;
        },
        
        center: function(uElm) {
            this.setPosition({ self: 'cc', to: 'cc' }, uElm);
            return this;
        },
        
        cover: function(uElm) {
            if(this.inDOM && !this.animating) {
                var nTotalWidth = this.rootElm.outerWidth(),
                    nTotalHeight = this.rootElm.outerHeight(),
                    nWidth = this.rootElm.width(),
                    nHeight = this.rootElm.height(),
                    oRegion = getRegionForUi(uElm, this);
                if(uElm == 'document') {
                    // For IE 6, 7, 8
                    oRegion.width = box.getJWin().width();
                }
                this.rootElm.css({
                    top: oRegion.top,
                    left: oRegion.left,
                    width: oRegion.width - (nTotalWidth - nWidth),
                    height: oRegion.height - (nTotalHeight - nHeight)
                });
            }
            return this;
        },
        
        animate: function(oProperties, nDuration, sEvtType) {
            if(this.inDOM && !this.animating) {
                this.animating = true;
                this.rootElm.animate(oProperties, nDuration, endAnim(this, sEvtType || this.phase || 'endanim'));
            }
        },
        
        stop: function() {
            if(this.animating) {
                this.rootElm.stop();
                this.animating = false;
            }
            return this;
        }
    };
    
    return box.get('util:component').create({
        extend: oProto
    });
});

/*jshint eqeqeq:false*/
/*global box:false*/
box.get('util').addModule('draggable', function($, box) {
    var oDraggable = {
        getEvtPageXY: function(jEvt) {
            var bTouch = jEvt.originalEvent.touches && jEvt.originalEvent.touches.length,
                oSrc = bTouch ? jEvt.originalEvent.touches[0] : jEvt;
            return {
                pageX: oSrc.pageX,
                pageY: oSrc.pageY
            };
        },
        
        startMove: function(oCom) {
            return function(oEvt) {
                if(oEvt.type == 'mousedown') {
                    oEvt.preventDefault();
                    oEvt.stopPropagation();
                }
                oCom.computeLimits();
                var oXY = oDraggable.getEvtPageXY(oEvt),
                    oDrag = oCom.drag = {},
                    sName = oCom.cfg.evtNs + oCom.id;
                oDrag.startTop = oCom.region.top = parseInt(oCom.rootElm.css('top'), 10) || 0;
                oDrag.startLeft = oCom.region.left = parseInt(oCom.rootElm.css('left'), 10) || 0;
                oDrag.startPageX = oXY.pageX;
                oDrag.startPageY = oXY.pageY;
                oCom.currentHandleElm = this;
                oCom.boxPublish('start' + oCom.cfg.evtRoot);
                oCom.moving = true;
                box.getJDoc()
                    .bind('mouseup' + sName, oDraggable.endMove(oCom))
                    .bind('mousemove' + sName, oDraggable.move(oCom))
                    .bind('touchend' + sName, oDraggable.endMove(oCom))
                    .bind('touchmove' + sName, oDraggable.move(oCom));
            };
        },
        
        endMove: function(oCom) {
            return function(oEvt) {
                if(oEvt.type == 'mouseup') {
                    oEvt.stopPropagation();
                }
                box.getJDoc().unbind(oCom.cfg.evtNs + oCom.id);
                oCom.currentHandleElm = null;
                oCom.moving = false;
                oCom.boxPublish('end' + oCom.cfg.evtRoot);
            };
        },
        
        move: function(oCom) {
            return function(oEvt) {
                oEvt.preventDefault();
                if(oEvt.type == 'mousemove') {
                    oEvt.stopPropagation();
                }
                var oXY = oDraggable.getEvtPageXY(oEvt),
                    oDrag = oCom.drag;
                oDrag.lastPageX = oXY.pageX;
                oDrag.lastPageY = oXY.pageY;
                oDrag.x = oXY.pageX - oDrag.startPageX;
                oDrag.y = oXY.pageY - oDrag.startPageY;
                oDrag.top = oDrag.startTop + oDrag.y;
                oDrag.left = oDrag.startLeft + oDrag.x;
                oCom.dragBy(oDrag);
            };
        }
    };
    
    return oDraggable;
});

box.get('ui').addConfig('draggable', {
    clsRoot: 'draggable',
    clsHandle: 'draggable-handle',
    clsDisabled: 'draggable-disabled',
    dataHandle: 'data-box-draggable',
    tolerance: 0.5,
    evtRoot: 'move',
    evtNs: '.box-draggable-'
}).addConstructor('draggable', function($, box) {
    var oDraggable = box.get('util:draggable'),
        oProto;
    
    oProto = {
        boxConfigure: function(oDefaultCfg, oDatas) {
            if(typeof oDatas.clsRoot === 'string') {
                oDefaultCfg.clsRoot = oDatas.clsRoot;
            }
            if(typeof oDatas.clsHandle === 'string') {
                oDefaultCfg.clsHandle = oDatas.clsHandle;
            }
            if(!isNaN(oDatas.tolerance) && oDatas.tolerance >= 0 && oDatas.tolerance <= 1) {
                oDefaultCfg.tolerance = oDatas.tolerance;
            }
            if(typeof oDatas.grid === 'object' && oDatas.grid) {
                if(!isNaN(oDatas.grid.x)) {
                    oDatas.grid.xDelta = oDatas.grid.x * (1 - oDefaultCfg.tolerance);
                }
                if(!isNaN(oDatas.grid.y)) {
                    oDatas.grid.yDelta = oDatas.grid.y * (1 - oDefaultCfg.tolerance);
                }
                oDefaultCfg.grid = oDatas.grid;
            }
            this.cfg = oDefaultCfg;
        },
        
        boxCreate: function(oDatas) {
            var oCfg = this.cfg;
            this.rootElm = $(oDatas.rootElm || oDatas.rootHtml).addClass(oCfg.clsRoot);
            if(oDatas.handlesHtml) {
                this.rootElm.append(oDatas.handlesHtml);
            }
            this.handleElm = this.rootElm.find(oDatas.handleElm || '.' + oCfg.clsHandle);
            if(!this.handleElm.length) {
                this.handleElm = this.rootElm;
            }
            this.handleElm.addClass(oCfg.clsHandle);
            this.drag = {
                startTop: parseInt(this.rootElm.css('top'), 10) || 0,
                startLeft: parseInt(this.rootElm.css('left'), 10) || 0
            };
            this.disabled = true;
            this.inDOM = this.hasAncestor(box.getDoc().body);
            this.moving = this.animating = false;
            this.setLimits(oDatas.limits);
            this.enable();
        },
        
        boxDestroy: function() {
            this.disable();
            this.rootElm.removeClass(this.cfg.clsRoot);
        },

        disable: function() {
            if(this.disabled === false) {
                this.handleElm.unbind(this.cfg.evtNs + this.id);
                this.rootElm.addClass(this.cfg.clsDisabled);
                this.disabled = true;
            }
        },

        enable: function() {
            if(this.disabled === true) {
                var sName = this.cfg.evtNs + this.id;
                this.rootElm.removeClass(this.cfg.clsDisabled);
                this.handleElm.bind('mousedown' + sName, oDraggable.startMove(this)).bind('touchstart' + sName, oDraggable.startMove(this));
                this.disabled = false;
            }
        },
        
        setLimits: function(oDatas) {
            this.limits = (!oDatas || typeof oDatas != 'object') ? {} : oDatas;
            this.computeLimits();
        },
        
        clearLimits: function() {
            this.setLimits();
        },
        
        computeLimits: function() {
            if(!this.moving && typeof this.limits == 'object' && this.limits) {
                var oDatas = this.limits,
                    oRegion = this.region = {};
                if(typeof oDatas.targetElm == 'string' || (oDatas.targetElm && oDatas.targetElm.jquery)) {
                    oDatas = this.getLimits(oDatas.targetElm);
                }
                if(!isNaN(oDatas.minTop)) { oRegion.minTop = oDatas.minTop; }
                if(!isNaN(oDatas.maxTop)) { oRegion.maxTop = oDatas.maxTop; }
                if(!isNaN(oDatas.minLeft)) { oRegion.minLeft = oDatas.minLeft; }
                if(!isNaN(oDatas.maxLeft)) { oRegion.maxLeft = oDatas.maxLeft; }
                oRegion.width = oDatas.width;
                oRegion.height = oDatas.height;
            }
        },
        
        _computeWithGrid: function(nx, ny) {
            var oGrid = this.cfg.grid,
                oDrag = this.drag,
                nr;
            if(oGrid) {
                if(oGrid.xDelta) {
                    nr = Math.floor(nx / oGrid.x) * oGrid.x;
                    nx = nr + Math.floor((nx - nr) / oGrid.xDelta, 10) * oGrid.x;
                }
                if(oGrid.yDelta) {
                    nr = Math.floor(ny / oGrid.y) * oGrid.y;
                    ny = nr + Math.floor((ny - nr) / oGrid.yDelta, 10) * oGrid.y;
                }
                oDrag.x = nx;
                oDrag.y = ny;
                oDrag.top = oDrag.startTop + oDrag.y;
                oDrag.left = oDrag.startLeft + oDrag.x;
            }
        },
        
        _execDrag: function() {
            var nLeft = this.drag.left,
                nTop = this.drag.top;
            if(!isNaN(nLeft) && !isNaN(nTop)) {
                var sOrientation = this.currentHandleElm && this.currentHandleElm.getAttribute(this.cfg.dataHandle),
                    oRegion = this.region;
                if(sOrientation) {
                    if(sOrientation == 'n' || sOrientation == 's') {
                        nLeft = oRegion.left;
                    } else if(sOrientation == 'w' || sOrientation == 'e') {
                        nTop = oRegion.top;
                    }
                }
                if(!isNaN(oRegion.minTop)) { nTop = Math.max(nTop, oRegion.minTop); }
                if(!isNaN(oRegion.maxTop)) { nTop = Math.min(nTop, oRegion.maxTop); }
                if(!isNaN(oRegion.minLeft)) { nLeft = Math.max(nLeft, oRegion.minLeft); }
                if(!isNaN(oRegion.maxLeft)) { nLeft = Math.min(nLeft, oRegion.maxLeft); }
                if(nTop != oRegion.top || nLeft != oRegion.left) {
                    oRegion.top = nTop;
                    oRegion.left = nLeft;
                    this.rootElm.css({ top: nTop, left: nLeft });
                    this.boxPublish(this.cfg.evtRoot, { top: nTop, left: nLeft, propagation: false });
                }
            }
        },
        
        dragTo: function(oDatas) {
            if(!isNaN(oDatas.left) && !isNaN(oDatas.top)) {
                this._computeWithGrid(oDatas.left - this.drag.startLeft, oDatas.top - this.drag.startTop);
                this._execDrag();
            }
        },
        
        dragBy: function(oDatas) {
            if(!isNaN(oDatas.x) && !isNaN(oDatas.y)) {
                this._computeWithGrid(oDatas.x, oDatas.y);
                this._execDrag();
            }
        }
    };
    
    return box.get('util:component').create({
        inherit: 'ui:element',
        extend: oProto
    });
});

/*jshint browser:true, eqeqeq:false*/
/*global box:false*/
box.rtl = document.documentElement.dir == 'rtl';
box.get('ui').addConfig('form', {
    webbox: false,

    htmlFauxOptions: (
        '<div class="{$clsFauxOptions}" style="position:absolute; top:-10000px; ' + (box.rtl ? 'right' : 'left') + ':-10000px">' +
            '<div class="{$clsFauxOptionsInner}">' +
                '<div class="{$clsFauxOptionsScroll}"></div>' +
            '</div>' +
        '</div>'
    ),
    
    clsFocus: 'focus',
    clsChecked: 'checked',
    clsSelected: 'selected',
    clsHover: 'hover',
    clsLegend: 'legend',
    clsFauxCheckbox: 'faux-checkbox',
    clsFauxRadio: 'faux-radio',
    clsFauxSelect: 'faux-select',
    clsFauxOptions: 'faux-options',
    clsFauxOptionsInner: 'faux-options-inner',
    clsFauxOptionsReversed: 'faux-options-reversed',
    clsFauxOptionsScroll: 'faux-options-scrollable',
    
    maxHeightFauxOptions: 200,
    scrollbarOffsetFauxOptions: 0
}).addConstructor('form', function($, box) {
    var FORM_INIT_PHASE = 1,
        W = box.getWin(),
        D = box.getDoc(),
        makeField,
        fields = {},
        
        types = {
            'checkbox': 'checkbox',
            'hidden': 'text',
            'password': 'text',
            'radio': 'radio',
            'select-one': 'select',
            'text': 'text',
            'textarea': 'text'
        },
        
        patterns = {
            empty: /^\s*$/,
            email: /^\s*[\w-]+(\.[\w-]+)*@([\w-]+\.)+[A-Za-z]{2,7}\s*$/
        },
        
        rules = {
            empty: function(value) {
                return patterns.empty.test(value);
            },
            email: function(value) {
                return patterns.email.test(value);
            }
        };
    
    box.addFormPatterns = function(datas) {
        for(var p in datas) {
            if(datas.hasOwnProperty(p)) {
                (function(pattern, key) {
                    patterns[key] = pattern;
                    rules[key] = function(value) {
                        return pattern.test(value);
                    };
                })(datas[p], p);
            }
        }
    };
    
    /**
     * Common form management methods
     */
    var reExtractFieldName, extractFieldName, getFieldCacheId, counterForCommonRoot,
        validateForm, bindFormSubmit, unbindFormSubmit;
    
    // only for webbox platform
    reExtractFieldName = /(ctl|brandlayout|mainbody)[0-9]+[$_]/g;
    extractFieldName = function(name) {
        return name.replace(reExtractFieldName, '');
    };
    
    getFieldCacheId = function(fieldName, formName) {
        return formName + '.' + fieldName;
    };
    counterForCommonRoot = 0;
    
    // form submission
    validateForm = function(e) {
        var id = $(this).getBoxData('form'),
            form = box.get('ui:form.' + id);
        if(form && form.disabled !== true) {
            if(form.mustValidateRules) {
                if(!form.isValid()) {
                    e.preventDefault();
                    form.boxPublish('submit', { valid: false, domEvt: e });
                } else {
                    form.disable();
                    form.boxPublish('submit', { valid: true, domEvt: e });
                }
            } else {
                form.disable();
                form.boxPublish('submit', { domEvt: e });
            }
        } else {
            e.preventDefault();
        }
    };
    
    bindFormSubmit = function(form) {
        if(form.submitBtn) {
            form.submitBtn.bind('click.boxValidation', validateForm);
        } else {
            form.dom.bind('submit.boxValidation', validateForm);
        }
    };
    
    unbindFormSubmit = function(form) {
        if(form.submitBtn) {
            form.submitBtn.unbind('click.boxValidation');
        } else {
            form.dom.unbind('submit.boxValidation');
        }
    };
    
    
    /**
     * UiForm
     */
    var oProto = {
        boxConfigure: function(oDefaultCfg, oDatas) {
            var aCfg = [
                    'htmlFauxOptions', 'clsFocus', 'clsChecked', 'clsSelected', 'clsHover', 'clsFauxSelect', 'clsFauxOptions', 'clsFauxOptionsInner', 'clsFauxOptionsReversed'
                ],
                i = aCfg.length,
                sName;
            while(i--) {
                sName = aCfg[i];
                if(typeof oDatas[sName] == 'string' && oDatas[sName]) {
                    oDefaultCfg[sName] = oDatas[sName];
                }
            }
            oDefaultCfg.webbox = oDatas.webbox === true;
            if(!isNaN(oDatas.maxHeightFauxOptions) && oDatas.maxHeightFauxOptions > 0) {
                oDefaultCfg.maxHeightFauxOptions = oDatas.maxHeightFauxOptions;
            }
            if(!isNaN(oDatas.scrollbarOffsetFauxOptions) && oDatas.scrollbarOffsetFauxOptions >= 0) {
                oDefaultCfg.scrollbarOffsetFauxOptions = oDatas.scrollbarOffsetFauxOptions;
            }
            this.cfg = oDefaultCfg;
        },
        
        boxCreate: function(datas) {
            var that = this;
            
            that.dom = $(datas.rootElm).setBoxData({ form: that.id });
            that.fields = [];
            that.submitBtn = datas.submit !== undefined ? that.dom.find(datas.submit) : null;
            if(that.submitBtn && 1 == that.submitBtn.length) { // do not check for webbox here (diags)
                that.submitBtn.setBoxData({ form: that.id });
                if(that.submitBtn.boxOuterHTML().indexOf('doPostBack') > -1) {
                    var n = that.submitBtn[0].href.match(/'([^']+)'/);
                    that.submitName = n && n[1];
                    that.submitHref = that.submitBtn.attr('href');
                    that.submitBtn.attr('href', '#');
                }
            }
            bindFormSubmit(that);
            
            $('input, select, textarea', that.dom).each(function(i, elm) {
                if(elm.id && elm.name && elm.type && types[elm.type]) {
                    var bWebbox = that.cfg.webbox,
                        type = types[elm.type],
                        name;
                    if('radio' == type) {
                        name = bWebbox ? extractFieldName(elm.name) : elm.name;
                    } else {
                        name = bWebbox ? extractFieldName(elm.id) : elm.id;
                    }
                    var id = getFieldCacheId(name, that.id);
                    
                    // check for common root ids (Alsy diags)
                    if(bWebbox && 'radio' != type && fields[id]) {
                        ++counterForCommonRoot;
                        name = name + counterForCommonRoot;
                        id = id + counterForCommonRoot;
                        elm.id = name;
                    }
                    
                    if(!fields[id]) {
                        if('radio' == type) {
                            if(that.dom[0].nodeName.toLowerCase() == 'form') {
                                elm = that.dom[0].elements[elm.name];
                            } else {
                                elm = D.forms[0].elements[elm.name];
                            }
                        }
                        fields[id] = new makeField[type]($(elm), type, name, that.id);
                        that.fields.push(id);
                    }
                }
            });
            
            that.enable();
        },
        
        boxDestroy: function() {
            if(this.submitHref) {
                this.submitBtn.attr('href', this.submitHref);
            }
            unbindFormSubmit(this);
            this.clearErrors().removeValidation().removeReplacement();
            this.eachField(function(field) {
                delete fields[field.form + '.' + field.name];
            });
        },
        
        disable: function() {
            this.disabled = true;
        },
        
        enable: function() {
            this.disabled = false;
        },
        
        getElement: function() {
            return this.dom;
        },
        
        field: function(name) {
            return fields[getFieldCacheId(name, this.id)] || null;
        },
        
        eachField: function(fn) {
            var i = this.fields.length, l = i - 1;
            while(i--) {
                if(false === fn(fields[this.fields[l - i]])) {
                    break;
                }
            }
            return this;
        },
        
        submit: function() {
            if(this.dom[0].tagName === 'FORM') {
                this.dom[0].submit();
            } else if(this.submitName && box.getGlobal().__doPostBack) {
                box.getGlobal().__doPostBack(this.submitName, '');
            }
        },
        
        mustValidate: function(rules) {
            if(!this.mustValidateRules) {
                var msg = rules(this);
                if('string' == typeof msg) {
                    this.msg = msg;
                }
                this.mustValidateRules = true;
            }
            return this;
        },
        
        removeValidation: function() {
            this.eachField(function(field) {
                if(field.rule) {
                    field.removeValidation();
                }
            });
            return this;
        },
        
        getErrors: function() {
            var i = 0, errors = {};
            this.eachField(function(field) {
                if(field.error) {
                    errors[field.name] = field.error;
                    ++i;
                }
            });
            return (i ? errors : null);
        },
        
        setErrors: function(errors) {
            if('object' == typeof errors) {
                var id;
                for(var name in errors) {
                    id = getFieldCacheId(name, this.id);
                    if(errors.hasOwnProperty(name) && fields[id]) {
                        fields[id].setError(errors[name]);
                    }
                }
            }
            return this;
        },
        
        clearErrors: function() {
            this.eachField(function(field) {
                field.clearError();
            });
            this.boxPublish('submit', { valid: true });
            return this;
        },
        
        isValid: function(noBroadcast) {
            this.validate(noBroadcast === box.get('const:NOTIFY_OFF') ? box.get('const:NOTIFY_OFF') : undefined);
            var valid = true;
            this.eachField(function(field) {
                if(typeof field.error == 'string') {
                    return (valid = false);
                }
            });
            return valid;
        },
        
        validate: function(noBroadcast) {
            this.eachField(function(field) {
                if(undefined !== field.rule) {
                    field.validate(noBroadcast);
                }
            });
            return this;
        },
        
        addReplacement: function(options) {
            this.eachField(function(field) {
                if(undefined !== field.addReplacement) {
                    field.addReplacement(options);
                }
            });
            return this;
        },
        
        removeReplacement: function() {
            this.eachField(function(field) {
                if(undefined !== field.removeReplacement) {
                    field.removeReplacement();
                }
            });
            return this;
        }
    };
    
    
    /**
     * Common field management methods
     */
    var getFieldLabel, getFieldValidationEventName, validateField, bindFieldRule, unbindFieldRule,
        getFieldChangeEventName, changeField, bindFieldChange, unbindFieldChange,
        focusBlurField, bindFieldFocusBlur, unbindFieldFocusBlur, disableField, enableField;
    
    getFieldLabel = function(field) {
        if(field.jquery) {
            field = $(field);
        }
        var label = field.next('label');
        if(!label.length) {
            label = field.prev('label');
            if(!label.length && field.parent().length) {
                label = field.parent('label');
                if(!label.length) {
                    label = getFieldLabel(field.parent());
                }
            }
        }
        return label;
    };
    
    getFieldValidationEventName = function(type) {
        var evt;
        switch(type) {
            case 'checkbox':
            case 'radio':
                evt = 'click.boxValidation';
                break;
            case 'select':
            case 'text':
                evt = 'change.boxValidation';
        }
        return evt;
    };
    
    validateField = function(e) {
        var id = $(this).getBoxData('id');
        if(id && fields[id]) {
            fields[id].validate();
        }
    };
    
    bindFieldRule = function(field) {
        field.dom.bind(getFieldValidationEventName(field.type), validateField);
    };
    
    unbindFieldRule = function(field) {
        field.dom.unbind(getFieldValidationEventName(field.type));
    };
    
    getFieldChangeEventName = function(type) {
        var evt;
        switch(type) {
            case 'checkbox':
            case 'radio':
            case 'select':
                evt = 'click.boxChange';
                break;
            case 'text':
                evt = 'change.boxChange';
        }
        return evt;
    };
    
    changeField = function(e) {
        var id = $(this).getBoxData('id');
        var field = id && fields[id];
        if(field) {
            var type = field.type;
            if('checkbox' == type || 'radio' == type) {
                field[this.checked ? 'check' : 'uncheck'](extractFieldName(this.id));
            } else if('select' == field.type) {
                if(field.getIndex() != field.current) {
                    field.setIndex(field.getIndex());
                }
            } else {
                field.boxPublish('change');
            }
        }
    };
    
    bindFieldChange = function(field) {
        field.dom.bind(getFieldChangeEventName(field.type), changeField);
    };
    
    unbindFieldChange = function(field) {
        field.dom.unbind(getFieldChangeEventName(field.type));
    };
    
    disableField = function(field) {
        unbindFieldChange(field);
        if('select' == typeof field.type) {
            unbindSelectKeyNav(field);
        }
        field.dom.each(function(i, elm) {
            elm.disabled = true;
        });
        field.boxPublish('disable');
    };
    
    enableField = function(field, init) {
        bindFieldChange(field);
        if('select' == field.type) {
            bindSelectKeyNav(field);
        }
        if(init != FORM_INIT_PHASE) {
            field.dom.each(function(i, elm) {
                elm.disabled = false;
            });
        }
        field.boxPublish(init == FORM_INIT_PHASE ? 'init' : 'enable');
    };
    
    focusBlurField = function(e) {
        var id = $(this).getBoxData('id'),
            field = id && fields[id],
            cfg;
        if(field) {
            cfg = field.getCfg();
            if('focus' == e.type) {
                if('radio' == field.type || 'checkbox' == field.type) {
                    field.getLabel(extractFieldName(this.id)).addClass(cfg.clsFocus);
                } else if('select' == field.type) {
                    field.getReplaced().addClass(cfg.clsFocus);
                    // bug IE6, when clicking on a label, select the first option
                    if(box.ie6 && field.current != field.getIndex()) {
                        field.dom[0].selectedIndex = field.current;
                    }
                }
            } else {
                if('radio' == field.type || 'checkbox' == field.type) {
                    field.getLabel(extractFieldName(this.id)).removeClass(cfg.clsFocus);
                } else if('select' == field.type) {
                    field.getReplaced().removeClass(cfg.clsFocus);
                }
            }
        }
    };
    
    bindFieldFocusBlur = function(field) {
        field.dom.bind('focus.boxReplacement', focusBlurField).bind('blur.boxReplacement', focusBlurField);
    };
    
    unbindFieldFocusBlur = function(field) {
        field.dom.unbind('.boxReplacement');
    };
    
    
    /**
     * Field (base constructor)
     */
    var Field = function(elm, type, name, form) {
        this.initialize(elm, type, name, form);
    };
    Field.prototype = {
        boxName: 'ui:field',
        
        boxGetName: function() {
            return this.boxName + '.' + this.name;
        },
        
        boxPublish: function(sType, oDatas) {
            box.publish({ type: sType, label: this.boxGetName(), source: this, data: oDatas });
        },
        
        initialize: function(elm, type, name, form) {
            this.dom = elm;
            this.dom.setBoxData({ id: getFieldCacheId(name, form) });
            this.type = type;
            this.name = name;
            this.form = form;
            this.error = null;
            this.enable(FORM_INIT_PHASE);
        },
        
        getForm: function() {
            return box.get('ui:form.' + this.form);
        },
        
        getCfg: function() {
            return this.getForm().cfg;
        },
        
        getElement: function() {
            return this.dom;
        },
        
        getLabel: function() {
            return getFieldLabel(this.dom);
        },
        
        getLegend: function() {
            var parent = this.dom.eq(0).parent(),
                clsLegend = '.' + this.getCfg().clsLegend,
                form = this.getForm().dom[0],
                legend;
            while(parent[0] !== form) {
                legend = parent.find(clsLegend);
                if(legend.length) {
                    break;
                }
                parent = parent.parent();
            }
            return legend;
        },
        
        getValue: function() {
            return (this.dom[0].value || null);
        },
        
        setValue: function(value) {
            this.dom[0].value = value;
            return this;
        },
        
        isDisabled: function() {
            return this.dom[0].disabled;
        },
        
        disable: function() {
            disableField(this);
            return this;
        },
        
        enable: function(init) {
            enableField(this, init);
            return this;
        },
        
        mustValidate: function(rule) {
            this.rule = rule;
            bindFieldRule(this);
            return this;
        },
        
        removeValidation: function() {
            this.rule = null;
            unbindFieldRule(this);
            return this;
        },
        
        isValid: function(noBroadcast) {
            this.validate(noBroadcast === box.get('const:NOTIFY_OFF') ? box.get('const:NOTIFY_OFF') : undefined);
            return typeof this.error != 'string';
        },
        
        validate: function(noBroadcast) {
            if(this.rule) {
                var r = this.rule(this);
                if('string' == typeof r) {
                    this.setError(r, noBroadcast);
                } else {
                    this.clearError();
                }
            }
            return this;
        },
        
        getError: function() {
            return this.error;
        },
        
        setError: function(error, noBroadcast) {
            if('string' == typeof error) {
                this.error = error;
                if(noBroadcast !== box.get('const:NOTIFY_OFF')) {
                    this.boxPublish('error', { message: error });
                }
            }
            return this;
        },
        
        clearError: function(noBroadcast) {
            this.error = null;
            if(noBroadcast !== box.get('const:NOTIFY_OFF')) {
                this.boxPublish('valid');
            }
            return this;
        },
        
        isReplaced: function() {
            return this.dom.eq(0).getBoxData('mode') === 'replaced';
        }
    };
    
    
    /**
     * TextField
     */
    var TextField = function(elm, type, name, form) {
        this.initialize(elm, type, name, form);
    };
    box.inherit(TextField, Field);
    box.extend(TextField, {
        boxName: 'ui:field.text',
        
        isDefault: function() {
            return (this.dom[0].value == this.dom[0].defaultValue);
        },
        
        clearValue: function() {
            this.dom[0].value = '';
            return this;
        },
        
        isEmpty: function() {
            return rules.empty(this.dom[0].value);
        },
        
        isMatching: function(pattern) {
            return (rules[pattern] ? rules[pattern](this.dom[0].value) : null);
        },
        
        isEqualTo: function(value) {
            return (this.dom[0].value == value);
        }
    });
    
    
    /**
     * CheckboxField
     */
    var CheckboxField = function(elm, type, name, form) {
        this.initialize(elm, type, name, form);
    };
    box.inherit(CheckboxField, Field);
    box.extend(CheckboxField, {
        boxName: 'ui:field.checkbox',
        
        isChecked: function() {
            return this.dom[0].checked;
        },
        
        check: function() {
            this.dom[0].checked = true;
            if(this.isReplaced()) {
                this.getLabel().addClass(this.getCfg().clsChecked);
            }
            this.boxPublish('change');
            return this;
        },
        
        uncheck: function() {
            this.dom[0].checked = false;
            if(this.isReplaced()) {
                this.getLabel().removeClass(this.getCfg().clsChecked);
            }
            this.boxPublish('change');
            return this;
        },
        
        addReplacement: function() {
            this.dom.setBoxData({ mode: 'replaced' });
            var oCfg = this.getCfg(),
                $label = this.getLabel();
            $label.prepend('<span class="' + oCfg.clsFauxCheckbox + '"></span>');
            if(this.isChecked()) {
                this.getLabel().addClass(oCfg.clsChecked);
            }
            bindFieldFocusBlur(this);
            this.boxPublish('replace');
            return this;
        },
        
        removeReplacement: function() {
            var cfg = this.getCfg();
            this.dom.clearBoxData([ 'mode' ]);
            this.getLabel().removeClass(cfg.clsChecked).removeClass(cfg.clsFocus);
            unbindFieldFocusBlur(this);
            return this;
        }
    });
    
    
    /**
     * RadiosGroup
     */
    var RadiosGroup = function(elm, type, name, form) {
        this.initialize(elm, type, name, form);
    };
    box.inherit(RadiosGroup, Field);
    box.extend(RadiosGroup, {
        boxName: 'ui:field.radio',
        
        initialize: function(elm, type, name, form) {
            var that = this;
            
            that.dom = elm;
            that.type = type;
            that.name = name;
            that.form = form;
            that.error = null;
            that.length = that.dom.length;
            that.map = {};
            that.current = null;
            that.each(function(field, i) {
                that.map[extractFieldName(field.id)] = i;
                if(field.checked) {
                    that.current = extractFieldName(field.id);
                }
                $(field).setBoxData({ id: getFieldCacheId(name, form) });
            });
            that.enable(FORM_INIT_PHASE);
        },
        
        each: function(fn) {
            var i = this.length, l = i - 1;
            while(i--) {
                if(fn(this.dom[l - i], l - i)) {
                    break;
                }
            }
            return this;
        },
        
        getChecked: function() {
            return this.current ? this.dom[this.map[this.current]] : null;
        },
        
        getElement: function(id) {
            var f = null;
            if('string' == typeof id) {
                if(undefined !== this.map[id]) {
                    return this.dom[this.map[id]];
                }
            } else if(typeof id == 'number') {
                if(id >= 0 && id < this.length) {
                    return this.dom[id];
                }
            } else {
                f = this.getChecked();
            }
            return f;
        },
        
        getElements: function() {
            return this.dom;
        },
        
        getLabel: function(id) {
            var field = this.getElement(id);
            return (field && getFieldLabel($(field)));
        },
        
        getLabels: function() {
            return getFieldLabel(this.dom);
        },
        
        getValue: function(id) {
            if(undefined !== id) {
                var field = this.getElement(id);
                return ((field && field.value) ? field.value : null);
            } else {
                var current = this.getChecked();
                return (current && current.value);
            }
        },
        
        setValue: function(value, id) {
            if(undefined !== id) {
                var field = this.getElement(id);
                if(field) {
                    field.value = value;
                }
            } else {
                var current = this.getChecked();
                if(current) {
                    current.value = value;
                }
            }
            return this;
        },
        
        isChecked: function(id) {
            var ok = false;
            if(undefined !== id) {
                var field = this.getElement(id);
                ok = (!!field && field.checked);
            } else {
                ok = !!this.current;
            }
            return ok;
        },
        
        check: function(id) {
            if(undefined !== id) {
                var field = this.getElement(id),
                    cfg;
                if(field && id != this.current) {
                    cfg = this.getCfg();
                    field.checked = true;
                    if(this.isReplaced()) {
                        if(this.current) {
                            this.getLabel(this.current).removeClass(cfg.clsChecked);
                        }
                        this.getLabel(id).addClass(cfg.clsChecked);
                    }
                    this.current = id;
                    this.boxPublish('change');
                }
            }
            return this;
        },
        
        uncheck: function(id) {
            if(this.current) {
                var field = this.getElement(undefined !== id ? id : this.current);
                if(field && field.checked) {
                    field.checked = false;
                    if(this.isReplaced()) {
                        this.getLabel(this.current).removeClass(this.getCfg().clsChecked);
                    }
                    this.current = null;
                    this.boxPublish('disable');
                }
            }
            return this;
        },
        
        addReplacement: function() {
            this.dom.setBoxData({ mode: 'replaced' });
            var oCfg = this.getCfg();
            this.getLabels().prepend('<span class="' + oCfg.clsFauxRadio + '"></span>');
            if(this.isChecked()) {
                this.getLabel(this.current).addClass(oCfg.clsChecked);
            }
            bindFieldFocusBlur(this);
            this.boxPublish('replace');
            return this;
        },
        
        removeReplacement: function() {
            var cfg = this.getCfg();
            this.dom.clearBoxData([ 'mode' ]);
            this.getLabels().removeClass(cfg.clsChecked).removeClass(cfg.clsFocus);
            unbindFieldFocusBlur(this);
            return this;
        }
    });
    
    
    /**
     * SelectField
     */
    var fauxOptions, openedFauxSelect,
        getFauxOptions, getFauxOptionIndex, manageFauxSelectState, openFauxOptions, closeFauxOptions,
        clickOnFauxSelect, bindFauxSelectClick, unbindFauxSelectClick,
        clickOnFauxOptions, bindFauxOptionsClick, unbindFauxOptionsClick,
        keyUpOnFauxSelect, keyDownOnFauxSelect, bindSelectKeyNav, unbindSelectKeyNav,
        mouseOverOptionsIE6, mouseOutOptionsIE6,
        boundFauxOptionsClick = false;
    
    getFauxOptions = function(select) {
        var options = select.getOptions();
        var selected = select.getIndex();
        var i = options.length, l = i - 1, cls, html = '';
        while(i--) {
            cls = (l - i) == selected ? ' ' + select.getCfg().clsSelected : '';
            html += '<li class="box[i=' + (l - i) + ']' + cls + '">' + (options[l - i].text || '&nbsp;') + '</li>';
        }
        return html;
    };
    
    getFauxOptionIndex = function(option) {
        return parseInt(option.className.match(/i=(\d+)/)[1], 10);
    };
    
    mouseOverOptionsIE6 = function(cls) {
        return function() {
            $(this).addClass(cls);
        };
    };
    
    mouseOutOptionsIE6 = function(cls) {
        return function() {
            $(this).removeClass(cls);
        };
    };
    
    openFauxOptions = function(select) {
        select.opened = true;
        openedFauxSelect = getFieldCacheId(select.name, select.form);
        
        select.boxPublish('beforeopen');
        
        box
            .get('ui:element.faux-options')
            .setContent(getFauxOptions(select))
            .insert('beforeEnd', box.get('ui:scrollable.faux-options').wrapper);
        
        var cfg = select.getCfg(),
            oMask = box.get('ui:element.mask-faux-options'),
            oScroll = box.get('ui:scrollable.faux-options'),
            fauxSelect = select.getReplaced(),
            fauxSelectPos = fauxSelect.getXY(),
            fauxSelectWidth = fauxSelect[0].offsetWidth,
            fauxSelectHeight = fauxSelect[0].offsetHeight,
            scrollbarBorders = oScroll.scrollbar.outerWidth() - oScroll.scrollbar.innerWidth();
        
        // set width before computing height
        fauxOptions.width(fauxSelectWidth);
        
        var scrollableContentHeight = oScroll.wrapper[0].offsetHeight;
        if(scrollableContentHeight > cfg.maxHeightFauxOptions) {
            scrollableContentHeight = cfg.maxHeightFauxOptions;
        }
        
        // set height before computing position
        oScroll.element.height(scrollableContentHeight);
        
        var fauxOptionsHeight = fauxOptions[0].offsetHeight,
            wSize = $(W).getSize(),
            wOffset = $(W).getScroll(),
            top = fauxSelectPos.top + fauxSelectHeight,
            reverse = false;
        
        if(top + fauxOptionsHeight > wOffset.top + wSize.height) {
            var tmp = fauxSelectPos.top - fauxOptionsHeight;
            if(tmp >= wOffset.top) {
                top = tmp;
                reverse = true;
                fauxOptions.addClass(cfg.clsFauxOptionsReversed);
            }
        }
        
        oMask.insert('beforeBegin', fauxOptions).cover('document');
        oMask.rootElm.click(function() {
            closeFauxOptions(fields[openedFauxSelect]);
        });

        fauxOptions.css({
            top: top,
            right: 'auto',
            // substract 10000px in IE7-, only in right-to-left mode
            left: fauxSelectPos.left > 10000 ? fauxSelectPos.left - 10000 : fauxSelectPos.left
        });

        oScroll.gutter.css('height', scrollableContentHeight - scrollbarBorders - (2 * cfg.scrollbarOffsetFauxOptions));
        oScroll.compute().moveToElement('#' + select.form + select.name + select.getIndex());
        if(box.ie6) {
            fauxOptions.find('li').mouseover(mouseOverOptionsIE6(cfg.clsHover)).mouseout(mouseOutOptionsIE6(cfg.clsHover));
        }
        select.boxPublish('open', { reverse: reverse });
    };
    
    closeFauxOptions = function(select) {
        select.opened = false;
        var styles = { height: 'auto' };
        if(box.rtl) {
            styles.right = '-10000px';
            styles.left = 'auto';
        } else {
            styles.left = '-10000px';
            styles.right = 'auto';
        }
        fauxOptions.css(styles).removeClass(select.getCfg().clsFauxOptionsReversed);
        box.get('ui:element.mask-faux-options').rootElm.unbind('click');
        box.get('ui:element.mask-faux-options').remove();
        if(box.ie6) {
            fauxOptions.find('li').unbind('mouseover mouseout');
        }
        var oScroll = box.get('ui:scrollable.faux-options');
        oScroll.wrapper.css('width', 'auto');
        oScroll.element.css('height', 'auto');
        oScroll.gutter.css('height', 'auto');
        oScroll.disable().reposition();
        box.get('ui:element.faux-options').remove();
    };
    
    manageFauxSelectState = function(select) {
        if(select.isReplaced()) {
            if(select.opened) {
                closeFauxOptions(select);
            } else {
                openFauxOptions(select);
            }
        }
    };
    
    clickOnFauxSelect = function(e) {
        var id = $(this).prev().getBoxData('id');
        var select = id && fields[id];
        if(select) {
            select.dom[0].focus();
            manageFauxSelectState(select);
        }
    };
    
    bindFauxSelectClick = function(select) {
        select.getReplaced().click(clickOnFauxSelect);
    };
    
    unbindFauxSelectClick = function(select) {
        select.getReplaced().unbind('click');
    };
    
    clickOnFauxOptions = function(e) {
        var select = fields[openedFauxSelect];
        if(select && e.target.nodeName.toLowerCase() == 'li') {
            select.setIndex(getFauxOptionIndex(e.target));
            closeFauxOptions(select);
            select.dom[0].focus();
        }
    };
    
    bindFauxOptionsClick = function(select) {
        if(boundFauxOptionsClick) { return; }
        boundFauxOptionsClick = true;
        fauxOptions.click(clickOnFauxOptions);
    };
    
    unbindFauxOptionsClick = function(select) {
        fauxOptions.unbind('click');
    };
    
    keyUpOnFauxSelect = function(e) {
        var id = $(this).getBoxData('id'),
            select = id && fields[id],
            current = select.dom[0].selectedIndex,
            length = select.dom[0].options.length;
        if(select) {
            var k = e.which;
            if(e.altKey && (k == 38 || k == 40)) {
                manageFauxSelectState(select);
                return;
            }
            if(select.current != current) {
                switch(k) {
                    case 13:
                    case 27:
                        select.setIndex(current);
                        if(select.isReplaced()) {
                            closeFauxOptions(select);
                        }
                        break;
                    case 34:
                    case 35:
                        select.setIndex(length - 1);
                        break;
                    case 33:
                    case 36:
                        select.setIndex(0);
                        break;
                    default:
                        select.setIndex(current);
                }
            }
        }
    };
    
    keyDownOnFauxSelect = function(e) {
        var id = $(this).getBoxData('id');
        var select = id && fields[id];
        if(select && select.isReplaced() && 9 == e.which) {
            closeFauxOptions(select);
        }
    };
    
    bindSelectKeyNav = function(select) {
        select.dom.bind('keyup.boxKeyNav', keyUpOnFauxSelect).bind('keydown.boxKeyNav', keyDownOnFauxSelect);
    };
    
    unbindSelectKeyNav = function(select) {
        select.dom.unbind('.boxKeyNav');
    };
    
    box.subscribe('endmove>ui:draggable.scrollable.faux-options', function(e) {
        fields[openedFauxSelect].getElement()[0].focus();
    });
    
    var SelectField = function(elm, type, name, form) {
        this.initialize(elm, type, name, form);
    };
    box.inherit(SelectField, Field);
    box.extend(SelectField, {
        boxName: 'ui:field.select',
        
        initialize: function(elm, type, name, form) {
            this.dom = elm;
            this.dom.setBoxData({ id: getFieldCacheId(name, form) });
            this.type = type;
            this.name = name;
            this.form = form;
            this.error = null;
            this.length = this.dom[0].options ? this.dom[0].options.length : 0;
            this.current = this.dom[0].selectedIndex;
            this.enable(FORM_INIT_PHASE);
        },
        
        hasIndex: function(i) {
            return (!isNaN(i) && i >= 0 && i < this.length);
        },
        
        getIndex: function() {
            return this.dom[0].selectedIndex;
        },
        
        setIndex: function(i, bValidate) {
            if(this.hasIndex(i)) {
                var changed = i != this.dom[0].selectedIndex;
                this.dom[0].selectedIndex = i;
                if(this.isReplaced()) {
                    this.getReplaced('span').html(this.getText() || '&nbsp;');
                    if(this.opened) {
                        var opts = fauxOptions.find('li'),
                            cfg = this.getCfg();
                        opts.eq(this.current).removeClass(cfg.clsSelected);
                        opts.eq(i).addClass(cfg.clsSelected);
                        if(!box.get('ui:scrollable.faux-options').disabled) {
                            box.get('ui:scrollable.faux-options').moveToElement(opts.eq(i));
                        }
                        opts = null;
                    }
                    if(this.rule && bValidate !== false) {
                        this.validate();
                    }
                }
                this.current = i;
                if(changed) {
                    this.boxPublish('change');
                }
            }
            return this;
        },
        
        getValue: function(i) {
            i = undefined !== i ? i : this.getIndex();
            if(this.hasIndex(i)) {
                return this.dom[0].options[i].value || null;
            }
            return null;
        },
        
        setValue: function(value, bValidate) {
            var options = this.dom[0].options,
                i = options.length;
            while(i--) {
                if(options[i].value == value) {
                    this.setIndex(i, bValidate);
                    break;
                }
            }
            return this;
        },
        
        getText: function(i) {
            i = undefined !== i ? i : this.getIndex();
            if(this.hasIndex(i)) {
                return this.dom[0].options[i].text || null;
            }
            return null;
        },
        
        setText: function(text, i) {
            i = undefined !== i ? i : this.getIndex();
            if(this.hasIndex(i)) {
                this.dom[0].options[i].text = text;
                if(i == this.current && this.isReplaced()) {
                    this.getReplaced('span').html(text || '&nbsp;');
                }
            }
            return this;
        },
        
        getOption: function(i) {
            i = undefined !== i ? i : this.getIndex();
            if(this.hasIndex(i)) {
                return {'text': this.getText(i), 'value': this.getValue(i), 'selected': i == this.getIndex()};
            }
            return null;
        },
        
        setOption: function(option, i) {
            if('object' == typeof option) {
                i = undefined !== i ? i : this.getIndex();
                if(this.hasIndex(i)) {
                    this.dom[0].options[i].value = option.value;
                    this.dom[0].options[i].text = option.text;
                }
            }
            return this;
        },
        
        getOptions: function() {
            var options = [], i = this.length, l = i - 1;
            while(i--) {
                options[l - i] = this.getOption(l - i);
            }
            return options;
        },
        
        setOptions: function(options, clear) {
            if('object' == typeof options && options.length) {
                if(clear) {
                    this.dom[0].options.length = 0;
                }
                var i = options.length, l = i - 1, opt;
                while(i--) {
                    opt = options[l - i];
                    if(opt.selected) {
                        this.current = i;
                    }
                    this.dom[0].options[this.dom[0].options.length] = new Option(opt.text, opt.value, opt.selected);
                }
                this.length = this.dom[0].options.length;
                // @todo broadcast an 'update' event?
            }
            return this;
        },
        
        addReplacement: function() {
            this.dom.setBoxData({ mode: 'replaced' });
            var cfg = this.getCfg(),
                id = this.form + this.name + 'REP',
                html = '<div id="' + id + '" class="' + cfg.clsFauxSelect + '"><div><span id="' + id + 'Inner">' + (this.getText() || '&nbsp;') + '</span></div></div>';
            $(html).insertAfter(this.dom);
            bindFauxSelectClick(this);
            bindFieldFocusBlur(this);
            if(!fauxOptions && !box.get('ui:element.faux-options') && !box.get('ui:element.mask-faux-options')) {
                fauxOptions = $(
                    cfg.htmlFauxOptions
                        .replace('{$clsFauxOptions}', cfg.clsFauxOptions)
                        .replace('{$clsFauxOptionsInner}', cfg.clsFauxOptionsInner)
                        .replace('{$clsFauxOptionsScroll}', cfg.clsFauxOptionsScroll)
                ).appendTo(D.body).mousedown(bindFauxOptionsClick);
                box.get('ui').create('scrollable.faux-options', {
                    rootElm: fauxOptions.find('.' + cfg.clsFauxOptionsScroll)
                });
                box.get('ui').create('element.mask-faux-options', {
                    rootHtml: '<div id="box-faux-options-mask" style="position:absolute; top:0; left:0;"></div>'
                });
                box.get('ui').create('element.faux-options', {
                    rootHtml: '<ul></ul>'
                });
            }
            if(box.ie6) {
                this.dom.bind('mousewheel', function(e) {
                    box.wheelEventForScroll(e, box.get('ui:scrollable.faux-options'));
                });
            }
            this.boxPublish('replace');
            return this;
        },
        
        removeReplacement: function() {
            this.dom.clearBoxData([ 'mode' ]);
            unbindFauxSelectClick(this);
            unbindFieldFocusBlur(this);
            this.getReplaced().remove();
            return this;
        },
        
        getReplaced: function(selector) {
            return $('#' + this.form + this.name + 'REP ' + (selector || ''));
        }
    });
    
    makeField = {
        checkbox: CheckboxField,
        radio: RadiosGroup,
        select: SelectField,
        text: TextField
    };
    
    return box.get('util:component').create({
        extend: oProto
    });
});

/*jshint eqeqeq:false*/
/*global box:false*/
box.get('ui').addConfig('scrollable', {
    htmlContent: '<div class="{$clsContent}"></div>',
    htmlScrollbar: '<div class="{$clsScrollbar}">{$content}</div>',
    htmlGutter: '<div class="{$clsGutter}">{$htmlFace}</div>',
    htmlPrev: '<span class="{$clsPrev}"></span>',
    htmlNext: '<span class="{$clsNext}"></span>',
    htmlFace: '<span class="{$clsFace}"></span>',
    
    clsRoot: 'scrollable',
    clsContent: 'scrollable-content',
    clsContentDisabled: 'scrollable-content-disabled',
    clsScrollbar: 'scrollbar',
    clsGutter: 'scrollbar-gutter',
    clsPrev: 'scrollbar-prev',
    clsNext: 'scrollbar-next',
    clsFace: 'scrollbar-face'
}).addConstructor('scrollable', function($, box) {
    var dimTotal, dimPartial, bIE6,
        getScrollbarHTML, addBarDraggable, clickToPosition, wheelEvent,
        oProto;
    
    dimTotal = { top: 'offsetHeight', left: 'offsetWidth' };
    dimPartial = { top: 'height', left: 'width' };
    bIE6 = box.ie6;
    
    getScrollbarHTML = function(oUi, bar, buttons) {
        var tmp = bar ? oUi.cfg.htmlGutter.replace('{$htmlFace}', oUi.cfg.htmlFace) : '';
        if(buttons) {
            tmp = oUi.cfg.htmlPrev + tmp + oUi.cfg.htmlNext;
        }
        var html = oUi.cfg.htmlScrollbar.replace('{$content}', tmp);
        $.each(['htmlScrollbar', 'htmlPrev', 'htmlNext', 'htmlGutter', 'htmlFace'], function(i, name) {
            var cls = 'cls' + name.substring(4);
            html = html.replace('{$' + cls + '}', oUi.cfg[cls]);
        });
        return html;
    };
    
    addBarDraggable = function(oUi) {
        box.get('ui').create('draggable.' + oUi.dragId, {
            rootElm: oUi.bar
        });
        
        box.subscribe('move>ui:draggable.' + oUi.dragId, function(oEvt) {
            var coord = oEvt.source.region[oUi.position];
            if(coord == Math.round(oUi.size.scrollDiff)) {
                coord = oUi.size.scrollDiff;
            }
            var pos = Math.round(coord / oUi.size.scrollDiff * oUi.size.elementDiff);
            oUi.wrapper.css(oUi.position, - pos + 'px');
        });
    };
    
    clickToPosition = function(oEvt, oUi) {
        oEvt.preventDefault();
        var t = $(oEvt.target), pos;
        if(t.hasClass(oUi.cfg.clsPrev)) {
            pos = Math.round(oUi.getWrapperOffset() + oUi.moveBy);
            oUi.moveContentTo(pos);
        } else if(t.hasClass(oUi.cfg.clsNext)) {
            pos = Math.round(oUi.getWrapperOffset() - oUi.moveBy);
            oUi.moveContentTo(pos);
        } else if(t.hasClass(oUi.cfg.clsGutter)) {
            var coord = oUi.position == 'top' ? oEvt.pageY : oEvt.pageX;
            pos = coord - oUi.gutter.offset()[oUi.position] - Math.round(oUi.size.bar / 2);
            oUi.moveBarTo(pos);
        }
        t = null;
    };
    
    wheelEvent = function(oEvt, oUi) {
        if(!oUi.disabled) {
            oEvt.preventDefault();
            var n = oEvt.detail ? - oEvt.detail / 3 : oEvt.wheelDelta / 120;
            var pos = Math.round(oUi.getWrapperOffset() + (n * oUi.moveBy));
            oUi.moveContentTo(pos);
        }
    };
    box.wheelEventForScroll = wheelEvent; // for IE6
    
    oProto = {
        boxConfigure: function(oDefaultCfg, oDatas) {
            var aCfg = [
                    'htmlContent', 'htmlScrollbar', 'htmlGutter', 'htmlPrev', 'htmlNext', 'htmlFace', 'clsRoot', 'clsContent', 'clsContentDisabled', 'clsScrollbar', 'clsGutter', 'clsPrev', 'clsNext', 'clsFace'
                ],
                i = aCfg.length,
                sName;
            while(i--) {
                sName = aCfg[i];
                if(oDatas[sName]) {
                    oDefaultCfg[sName] = oDatas[sName];
                }
            }
            this.cfg = oDefaultCfg;
        },
        
        boxCreate: function(datas) {
            var oThis = this,
                oCfg = oThis.cfg;
            
            oThis.direction = datas.horizontal ? 'horizontal' : 'vertical';
            oThis.position = oThis.direction == 'vertical' ? 'top' : 'left';
            oThis.moveBy = (!isNaN(datas.moveBy) && datas.moveBy > 0) ? datas.moveBy : null;
            oThis.barMinSize = (!isNaN(datas.barMinSize) && datas.barMinSize > 10) ? datas.barMinSize : 10;
            
            oThis.element = $(datas.rootElm).addClass(oCfg.clsRoot);
            var wrapHTML = oCfg.htmlContent.replace('{$clsContent}', oCfg.clsContentDisabled);
            if(!oThis.element.html()) {
                oThis.element.html(wrapHTML);
            } else {
                oThis.element.wrapInner(wrapHTML);
            }
            oThis.wrapper = oThis.element.children();
            
            var insertMethod = datas.insertMethod || 'prependTo',
                insertTarget = datas.insertTarget || oThis.element;
            
            // @todo add support for scroll without bar
            if(!datas.bar && !datas.buttons) {
                datas.bar = true;
            }
            oThis.scrollbar = $(getScrollbarHTML(oThis, datas.bar, datas.buttons))[insertMethod](insertTarget);
            if(datas.bar) {
                oThis.gutter = oThis.scrollbar.find('.' + oCfg.clsGutter);
                oThis.bar = oThis.scrollbar.find('.' + oCfg.clsFace);
            }
            
            oThis.dragId = 'scroll.' + oThis.id;
            addBarDraggable(this);
            
            oThis.boxPublish('beforefirstcompute');
            
            oThis.compute();
            
            if(oThis.wrapper.find('img').length && !box.loadIsDone) {
                box.getJWin().load(function() {
                    oThis.compute();
                });
            }
        },
        
        boxDestroy: function() {
            this.disable();
            box.get('ui').destroy('draggable.' + this.dragId);
            box.unsubscribe('move>ui:draggable.' + this.dragId);
            this.element.html(this.wrapper.html());
        },
        
        disable: function() {
            if(this.disabled !== true) {
                this.scrollbar.css('visibility', 'hidden');
                this.wrapper.removeClass(this.cfg.clsContent).addClass(this.cfg.clsContentDisabled);
                this.element.unbind('DOMMouseScroll').unbind('mousewheel');
                this.scrollbar.unbind('click');
                this.disabled = true;
            }
            return this;
        },
        
        enable: function() {
            var that = this;
            if(that.disabled !== false) {
                that.element.bind('DOMMouseScroll', function(e) {
                    wheelEvent(e, that);
                }).bind('mousewheel', function(e) {
                    wheelEvent(e, that);
                });
                
                that.scrollbar.click(function(e) {
                    clickToPosition(e, that);
                });
                
                that.wrapper.removeClass(that.cfg.clsContentDisabled).addClass(that.cfg.clsContent);
                
                // scrollbar should always be above the wrapper to be accessible
                var zIndex = parseInt(that.wrapper.css('zIndex'), 10);
                that.scrollbar.css({zIndex: isNaN(zIndex) ? 1 : ++zIndex, visibility: 'visible'});
                
                that.disabled = false;
            }
            return that;
        },
        
        reposition: function() {
            this.wrapper.css(this.position, 0);
            this.bar.css(this.position, 0);
            return this;
        },
        
        compute: function() {
            this.size = {};
            
            this.size.element = this.element[dimPartial[this.position]]();
            this.size.wrapper = this.wrapper[0][dimTotal[this.position]];
            
            if(this.size.wrapper > this.size.element) {
                this.size.gutter = this.gutter[0][dimTotal[this.position]];
                this.size.bar = this.size.element / this.size.wrapper * this.size.gutter;
                
                if(this.size.bar < this.barMinSize) {
                    this.size.bar = this.barMinSize;
                }
                
                // debug IE6 with bottom/right positioning inside bar
                if(bIE6 && Math.round(this.size.bar) % 2 !== 0) {
                    this.size.bar = Math.round(this.size.bar) - 1;
                }
                
                this.size.scrollDiff = this.size.gutter - this.size.bar;
                this.size.elementDiff = this.size.wrapper - this.size.element;
                
                this.bar.css(dimPartial[this.position], Math.round(this.size.bar) + 'px');
                
                if(!this.moveBy) {
                    var amount = Math.ceil((this.size.gutter - this.size.bar) / this.size.gutter * this.size.bar);
                    this.moveBy = (amount > 10) ? amount : 10;
                }
                
                var oDragLimits;
                if(this.direction == 'vertical') {
                    oDragLimits = {
                        minLeft: 0,
                        maxLeft: 0,
                        minTop: 0,
                        maxTop: Math.round(this.size.scrollDiff)
                    };
                } else {
                    oDragLimits = {
                        minLeft: 0,
                        maxLeft: Math.round(this.size.scrollDiff),
                        minTop: 0,
                        maxTop: 0
                    };
                }
                box.get('ui:draggable.' + this.dragId).setLimits(oDragLimits);
                
                this.boxPublish('computesuccess');
                this.enable();
            } else {
                this.disable();
            }
            return this;
        },
        
        getWrapperOffset: function() {
            return parseInt(this.wrapper.css(this.position), 10) || 0;
        },
        
        moveBarTo: function(scrollPos) {
            if(!this.disabled && !isNaN(scrollPos)) {
                if(scrollPos < 0) {
                    scrollPos = 0;
                } else if(scrollPos > this.size.scrollDiff) {
                    scrollPos = this.size.scrollDiff;
                }
                var wrapperPos = - Math.round(Math.abs(scrollPos) / this.size.scrollDiff * this.size.elementDiff);
                this.wrapper.css(this.position, wrapperPos + 'px');
                this.bar.css(this.position, Math.round(scrollPos) + 'px');
            }
            return this;
        },
        
        moveContentTo: function(wrapperPos) {
            if(!this.disabled && !isNaN(wrapperPos)) {
                if(wrapperPos > 0) {
                    wrapperPos = 0;
                } else if(wrapperPos < -this.size.elementDiff) {
                    wrapperPos = -this.size.elementDiff;
                }
                var scrollPos = Math.round(Math.abs(wrapperPos) / this.size.elementDiff * this.size.scrollDiff);
                this.wrapper.css(this.position, Math.round(wrapperPos) + 'px');
                this.bar.css(this.position, scrollPos + 'px');
            }
            return this;
        },
        
        moveToElement: function(elm) {
            if(!this.disabled) {
                if(typeof elm == 'string') {
                    elm = this.wrapper.find(elm);
                }
                if(elm && elm.jquery && elm.length) {
                    var targetStart = elm.getXY('positioned-ancestor')[this.position],
                        targetDim = elm['get' + (this.position == 'top' ? 'Height' : 'Width')]('margin-box'),
                        targetEnd = targetStart + targetDim,
                        offset = -this.getWrapperOffset(),
                        visibleEnd = offset + this.size.element;
                    
                    if(targetStart < offset) {
                        this.moveContentTo(-targetStart);
                    } else if(targetEnd > visibleEnd) {
                        if(targetDim < this.size.element) {
                            this.moveContentTo(-(targetEnd - this.size.element));
                        } else {
                            this.moveContentTo(-targetStart);
                        }
                    }
                }
            }
            return this;
        }
    };
    
    return box.get('util:component').create({
        extend: oProto
    });
});

/*jshint eqeqeq:false*/
/*global box:false*/
box.get('ui').addConfig('tabs', {
    clsTabList: 'tab-list',
    clsTabActive: 'tab-on',
    clsTabPanel: 'tab-panel',
    clsTabActivePanel: 'tab-panel-on',
    evtNs: '.box-tabs-'
}).addConstructor('tabs', function($, box) {
    var reHref = /^[^#]*/,
        changeTab, afterCloseAnimTab, afterOpenAnimTab,
        oProto;
    
    changeTab = function(oEvt) {
        var oData = oEvt.data;
        oData.originalEvent.preventDefault();
        this.change(oData.element);
    };
    
    afterCloseAnimTab = function() {
        this.getActivePanel().removeClass(this.cfg.clsTabActivePanel);
        this.tabActiveElm.removeClass(this.cfg.clsTabActive);
        this.phase = 'open';
        var oBoxEvt = this.boxPublish('beforeopen');
        if(!oBoxEvt || oBoxEvt.prevented === false) {
            afterOpenAnimTab.call(this);
        }
    };
    
    afterOpenAnimTab = function() {
        var sId = this.tabNextActiveElm.attr('href').replace(reHref, '');
        $(sId).addClass(this.cfg.clsTabActivePanel);
        this.tabActiveElm = this.tabNextActiveElm.addClass(this.cfg.clsTabActive);
        this.tabNextActiveElm = null;
    };
    
    oProto = {
        boxConfigure: function(oDefaultCfg, oDatas) {
            if(oDatas.clsTabList) {
                oDefaultCfg.clsTabList = oDatas.clsTabList;
            }
            if(oDatas.clsTabActive) {
                oDefaultCfg.clsTabActive = oDatas.clsTabActive;
            }
            if(oDatas.clsTabPanel) {
                oDefaultCfg.clsTabPanel = oDatas.clsTabPanel;
            }
            if(oDatas.clsTabActivePanel) {
                oDefaultCfg.clsTabActivePanel = oDatas.clsTabActivePanel;
            }
            this.cfg = oDefaultCfg;
        },
        
        boxCreate: function(oDatas) {
            var oCom = this,
                oCfg = oCom.cfg,
                oBoxData = { label: oCom.boxGetName() },
                sName = '@box>' + oBoxData.label;
            oCom.tabListElm = $(oDatas.tabListElm);
            oCom.tabListElm.find('a').each(function(i, oElm) {
                var sUrl = oElm.getAttribute('href', 2),
                    $elm;
                if(sUrl && sUrl.charAt(0) == '#') {
                    $elm = $(oElm).setBoxData(oBoxData);
                    if($elm.hasClass(oCfg.clsTabActive)) {
                        oCom.tabActiveElm = $elm;
                    }
                }
            });
            oCom.getActivePanel().addClass(oCfg.clsTabActivePanel);
            box.get('util:delegate-click').start();
            box.subscribe(
                {
                    name: 'click' + sName,
                    context: oCom,
                    handler: changeTab
                }, {
                    name: 'close' + sName,
                    handler: afterCloseAnimTab
                }, {
                    name: 'open' + sName,
                    handler: afterOpenAnimTab
                }
            );
            oCom.animating = false;
        },
        
        boxDestroy: function() {
            var sName = '@box>' + this.boxGetName();
            box.unsubscribe('click' + sName, 'close' + sName, 'open' + sName);
        },
        
        getActivePanel: function() {
            var sId = this[this.phase == 'open' ? 'tabNextActiveElm' : 'tabActiveElm'].attr('href').replace(reHref, '');
            return $(sId);
        },
        
        change: function(uElm) {
            if(!this.animating && uElm && (uElm.href || uElm.attr)) {
                this.tabNextActiveElm = $(uElm);
                this.phase = 'close';
                var oBoxEvt = this.boxPublish('beforeclose');
                if(!oBoxEvt || oBoxEvt.prevented === false) {
                    afterCloseAnimTab.call(this);
                }
            }
        },
        
        animate: function(oProperties, nDuration) {
            var oThis = this;
            if(!oThis.animating) {
                oThis.animating = true;
                oThis.getActivePanel().animate(oProperties, nDuration, function() {
                    oThis.animating = false;
                    oThis.boxPublish(oThis.phase);
                });
            }
        }
    };
    
    return box.get('util:component').create({
        extend: oProto
    });
});

