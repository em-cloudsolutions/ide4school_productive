(this["webpackJsonp@raspberrypifoundation/editor-ui"]=this["webpackJsonp@raspberrypifoundation/editor-ui"]||[]).push([[2],{530:function(e,t,n){"use strict";n.d(t,"a",(function(){return m})),n.d(t,"b",(function(){return s})),n.d(t,"c",(function(){return p})),n.d(t,"d",(function(){return h})),n.d(t,"e",(function(){return u})),n.d(t,"f",(function(){return v})),n.d(t,"g",(function(){return l})),n.d(t,"h",(function(){return f})),n.d(t,"i",(function(){return d})),n.d(t,"j",(function(){return b})),n.d(t,"k",(function(){return w})),n.d(t,"l",(function(){return c})),n.d(t,"m",(function(){return i})),n.d(t,"n",(function(){return o})),n.d(t,"o",(function(){return a})),n.d(t,"p",(function(){return O})),n.d(t,"q",(function(){return g})),n.d(t,"r",(function(){return j}));var r=n(1),o="szh-menu-container",i="szh-menu",c="arrow",a="item",u=Object(r.createContext)(),f=Object(r.createContext)({}),l=Object(r.createContext)({}),s=Object(r.createContext)({}),d=Object(r.createContext)({}),b=Object(r.createContext)({}),v=Object.freeze({ENTER:"Enter",ESC:"Escape",SPACE:" ",HOME:"Home",END:"End",LEFT:"ArrowLeft",RIGHT:"ArrowRight",UP:"ArrowUp",DOWN:"ArrowDown"}),h=Object.freeze({RESET:0,SET:1,UNSET:2,INCREASE:3,DECREASE:4,FIRST:5,LAST:6,SET_INDEX:7}),m=Object.freeze({CLICK:"click",CANCEL:"cancel",BLUR:"blur",SCROLL:"scroll"}),p=Object.freeze({FIRST:"first",LAST:"last"}),O="absolute",j="presentation",g="menuitem",w={"aria-hidden":!0,role:g}},532:function(e,t,n){"use strict";function r(){return r=Object.assign?Object.assign.bind():function(e){for(var t=1;t<arguments.length;t++){var n=arguments[t];for(var r in n)Object.prototype.hasOwnProperty.call(n,r)&&(e[r]=n[r])}return e},r.apply(this,arguments)}function o(e,t){if(null==e)return{};var n,r,o={},i=Object.keys(e);for(r=0;r<i.length;r++)n=i[r],t.indexOf(n)>=0||(o[n]=e[n]);return o}n.d(t,"a",(function(){return r})),n.d(t,"b",(function(){return o}))},533:function(e,t,n){"use strict";n.d(t,"a",(function(){return i})),n.d(t,"b",(function(){return d})),n.d(t,"c",(function(){return c})),n.d(t,"d",(function(){return s})),n.d(t,"e",(function(){return a})),n.d(t,"f",(function(){return b})),n.d(t,"g",(function(){return o})),n.d(t,"h",(function(){return f})),n.d(t,"i",(function(){return l})),n.d(t,"j",(function(){return u}));var r=n(131),o=function(e){return!!e&&"o"===e[0]},i=r.unstable_batchedUpdates||function(e){return e()},c=(Object.values,function(e,t,n){return void 0===n&&(n=1e-4),Math.abs(e-t)<n}),a=function(e,t){return!0===e||!(!e||!e[t])},u=function(e,t){return"function"===typeof e?e(t):e},f=function(e,t){return t&&Object.keys(t).forEach((function(n){var r=e[n],o=t[n];e[n]="function"===typeof o&&r?function(){o.apply(void 0,arguments),r.apply(void 0,arguments)}:o})),e},l=function(e){if("string"!==typeof e)return{top:0,right:0,bottom:0,left:0};var t=e.trim().split(/\s+/,4).map(parseFloat),n=isNaN(t[0])?0:t[0],r=isNaN(t[1])?n:t[1];return{top:n,right:r,bottom:isNaN(t[2])?n:t[2],left:isNaN(t[3])?r:t[3]}},s=function(e){for(;e;){if(!(e=e.parentNode)||e===document.body||!e.parentNode)return;var t=getComputedStyle(e),n=t.overflow,r=t.overflowX,o=t.overflowY;if(/auto|scroll|overlay|hidden/.test(n+o+r))return e}};function d(e,t){return{"aria-disabled":e||void 0,tabIndex:t?0:-1}}function b(e,t){for(var n=0;n<e.length;n++)if(e[n]===t)return n;return-1}},565:function(e,t,n){"use strict";n.d(t,"a",(function(){return o}));var r=n(1),o=function(e){var t=e.block,n=e.element,o=e.modifiers,i=e.className;return Object(r.useMemo)((function(){var e=n?t+"__"+n:t,r=e;o&&Object.keys(o).forEach((function(t){var n=o[t];n&&(r+=" "+e+"--"+(!0===n?t:t+"-"+n))}));var c="function"===typeof i?i(o):i;return"string"===typeof c&&(c=c.trim())&&(r+=" "+c),r}),[t,n,o,i])}},583:function(e,t,n){"use strict";n.d(t,"a",(function(){return o}));var r=n(1),o="undefined"!==typeof window&&"undefined"!==typeof window.document&&"undefined"!==typeof window.document.createElement?r.useLayoutEffect:r.useEffect},584:function(e,t,n){"use strict";n.d(t,"a",(function(){return i}));var r=n(1);function o(e,t){"function"===typeof e?e(t):e.current=t}var i=function(e,t){return Object(r.useMemo)((function(){return e?t?function(n){o(e,n),o(t,n)}:e:t}),[e,t])}},721:function(e,t,n){"use strict";n.d(t,"a",(function(){return L}));Math.pow(10,8);var r=6e4,o=36e5,i=3600,c=60,a=24*i,u=7*a,f=365.2425*a,l=f/12,s=3*l;function d(e){var t=new Date(Date.UTC(e.getFullYear(),e.getMonth(),e.getDate(),e.getHours(),e.getMinutes(),e.getSeconds(),e.getMilliseconds()));return t.setUTCFullYear(e.getFullYear()),e.getTime()-t.getTime()}var b=n(64);function v(e,t){if(t.length<e)throw new TypeError(e+" argument"+(e>1?"s":"")+" required, but only "+t.length+" present")}function h(e){v(1,arguments);var t=Object.prototype.toString.call(e);return e instanceof Date||"object"===Object(b.a)(e)&&"[object Date]"===t?new Date(e.getTime()):"number"===typeof e||"[object Number]"===t?new Date(e):("string"!==typeof e&&"[object String]"!==t||"undefined"===typeof console||(console.warn("Starting with v2.0.0-beta.1 date-fns doesn't accept strings as date arguments. Please use `parseISO` to parse strings. See: https://github.com/date-fns/date-fns/blob/master/docs/upgradeGuide.md#string-arguments"),console.warn((new Error).stack)),new Date(NaN))}function m(e){v(1,arguments);var t=h(e);return t.setHours(0,0,0,0),t}var p=864e5;function O(e,t){v(2,arguments);var n=m(e),r=m(t),o=n.getTime()-d(n),i=r.getTime()-d(r);return Math.round((o-i)/p)}function j(e,t){v(2,arguments);var n=h(e),r=h(t);return 12*(n.getFullYear()-r.getFullYear())+(n.getMonth()-r.getMonth())}function g(e){v(1,arguments);var t=h(e);return Math.floor(t.getMonth()/3)+1}function w(e,t){v(2,arguments);var n=h(e),r=h(t);return 4*(n.getFullYear()-r.getFullYear())+(g(n)-g(r))}var R={};function y(e,t){var n,r,o,i,c,a,u,f;v(1,arguments);var l=R,s=function(e){if(null===e||!0===e||!1===e)return NaN;var t=Number(e);return isNaN(t)?t:t<0?Math.ceil(t):Math.floor(t)}(null!==(n=null!==(r=null!==(o=null!==(i=null===t||void 0===t?void 0:t.weekStartsOn)&&void 0!==i?i:null===t||void 0===t||null===(c=t.locale)||void 0===c||null===(a=c.options)||void 0===a?void 0:a.weekStartsOn)&&void 0!==o?o:l.weekStartsOn)&&void 0!==r?r:null===(u=l.locale)||void 0===u||null===(f=u.options)||void 0===f?void 0:f.weekStartsOn)&&void 0!==n?n:0);if(!(s>=0&&s<=6))throw new RangeError("weekStartsOn must be between 0 and 6 inclusively");var d=h(e),b=d.getDay(),m=(b<s?7:0)+b-s;return d.setDate(d.getDate()-m),d.setHours(0,0,0,0),d}var E=6048e5;function C(e,t,n){v(2,arguments);var r=y(e,n),o=y(t,n),i=r.getTime()-d(r),c=o.getTime()-d(o);return Math.round((i-c)/E)}function x(e,t){v(2,arguments);var n=h(e),r=h(t);return n.getFullYear()-r.getFullYear()}function S(e,t){return v(2,arguments),h(e).getTime()-h(t).getTime()}var k={ceil:Math.ceil,round:Math.round,floor:Math.floor,trunc:function(e){return e<0?Math.ceil(e):Math.floor(e)}},T="trunc";function M(e){return e?k[e]:k[T]}function N(e,t,n){v(2,arguments);var r=S(e,t)/o;return M(null===n||void 0===n?void 0:n.roundingMethod)(r)}function D(e,t,n){v(2,arguments);var o=S(e,t)/r;return M(null===n||void 0===n?void 0:n.roundingMethod)(o)}function P(e,t,n){v(2,arguments);var r=S(e,t)/1e3;return M(null===n||void 0===n?void 0:n.roundingMethod)(r)}function L(e,t,n){v(2,arguments);var r,o=0,d=h(e),b=h(t);if(null!==n&&void 0!==n&&n.unit)"second"===(r=null===n||void 0===n?void 0:n.unit)?o=P(d,b):"minute"===r?o=D(d,b):"hour"===r?o=N(d,b):"day"===r?o=O(d,b):"week"===r?o=C(d,b):"month"===r?o=j(d,b):"quarter"===r?o=w(d,b):"year"===r&&(o=x(d,b));else{var m=P(d,b);Math.abs(m)<c?(o=P(d,b),r="second"):Math.abs(m)<i?(o=D(d,b),r="minute"):Math.abs(m)<a&&Math.abs(O(d,b))<1?(o=N(d,b),r="hour"):Math.abs(m)<u&&(o=O(d,b))&&Math.abs(o)<7?r="day":Math.abs(m)<l?(o=C(d,b),r="week"):Math.abs(m)<s?(o=j(d,b),r="month"):Math.abs(m)<f&&w(d,b)<4?(o=w(d,b),r="quarter"):(o=x(d,b),r="year")}return new Intl.RelativeTimeFormat(null===n||void 0===n?void 0:n.locale,{localeMatcher:null===n||void 0===n?void 0:n.localeMatcher,numeric:(null===n||void 0===n?void 0:n.numeric)||"auto",style:null===n||void 0===n?void 0:n.style}).format(o,r)}},723:function(e,t,n){"use strict";n.d(t,"a",(function(){return j}));var r=n(532),o=n(1),i=n(131),c=n(14),a=n(533),u=n(565),f=n(530),l=function(e){var t=e.className,n=e.containerRef,i=e.containerProps,l=e.children,s=e.isOpen,d=e.skipOpen,b=e.theming,v=e.transition,h=e.onClose,m=Object(a.e)(v,"item");return Object(c.jsx)("div",Object(r.a)({},Object(a.h)({onKeyDown:function(e){var t=e.key;if(t===f.f.ESC)Object(a.j)(h,{key:t,reason:f.a.CANCEL})},onBlur:function(e){s&&!e.currentTarget.contains(e.relatedTarget||document.activeElement)&&(Object(a.j)(h,{reason:f.a.BLUR}),d&&(d.current=!0,setTimeout((function(){return d.current=!1}),300)))}},i),{className:Object(u.a)({block:f.n,modifiers:Object(o.useMemo)((function(){return{theme:b,itemTransition:m}}),[b,m]),className:t}),style:Object(r.a)({position:"absolute"},null==i?void 0:i.style),ref:n,children:l}))},s=function(){var e,t=0;return{toggle:function(e){e?t++:t--,t=Math.max(t,0)},on:function(n,r,o){t?e||(e=setTimeout((function(){e=0,r()}),n)):null==o||o()},off:function(){e&&(clearTimeout(e),e=0)}}},d=function(e){var t,n,r,o=e.anchorRect,i=e.containerRect,c=e.menuRect,a=e.placeLeftorRightY,u=e.placeLeftX,f=e.placeRightX,l=e.getLeftOverflow,s=e.getRightOverflow,d=e.confineHorizontally,b=e.confineVertically,v=e.arrowRef,h=e.arrow,m=e.direction,p=e.position,O=m,j=a;"initial"!==p&&(j=b(j),"anchor"===p&&(j=Math.min(j,o.bottom-i.top),j=Math.max(j,o.top-i.top-c.height))),"left"===O?(t=u,"initial"!==p&&(n=l(t))<0&&((r=s(f))<=0||-n>r)&&(t=f,O="right")):(t=f,"initial"!==p&&(r=s(t))>0&&((n=l(u))>=0||-n<r)&&(t=u,O="left")),"auto"===p&&(t=d(t));var g=h?function(e){var t=e.arrowRef,n=e.menuY,r=e.anchorRect,o=e.containerRect,i=e.menuRect,c=r.top-o.top-n+r.height/2,a=1.25*t.current.offsetHeight;return c=Math.max(a,c),Math.min(c,i.height-a)}({menuY:j,arrowRef:v,anchorRect:o,containerRect:i,menuRect:c}):void 0;return{arrowY:g,x:t,y:j,computedDirection:O}},b=function(e){var t,n,r,o=e.anchorRect,i=e.containerRect,c=e.menuRect,a=e.placeToporBottomX,u=e.placeTopY,f=e.placeBottomY,l=e.getTopOverflow,s=e.getBottomOverflow,d=e.confineHorizontally,b=e.confineVertically,v=e.arrowRef,h=e.arrow,m=e.direction,p=e.position,O="top"===m?"top":"bottom",j=a;"initial"!==p&&(j=d(j),"anchor"===p&&(j=Math.min(j,o.right-i.left),j=Math.max(j,o.left-i.left-c.width))),"top"===O?(t=u,"initial"!==p&&(n=l(t))<0&&((r=s(f))<=0||-n>r)&&(t=f,O="bottom")):(t=f,"initial"!==p&&(r=s(t))>0&&((n=l(u))>=0||-n<r)&&(t=u,O="top")),"auto"===p&&(t=b(t));var g=h?function(e){var t=e.arrowRef,n=e.menuX,r=e.anchorRect,o=e.containerRect,i=e.menuRect,c=r.left-o.left-n+r.width/2,a=1.25*t.current.offsetWidth;return c=Math.max(a,c),Math.min(c,i.width-a)}({menuX:j,arrowRef:v,anchorRect:o,containerRect:i,menuRect:c}):void 0;return{arrowX:g,x:j,y:t,computedDirection:O}},v=n(583),h=n(584),m=["ariaLabel","menuClassName","menuStyle","arrowClassName","arrowStyle","anchorPoint","anchorRef","containerRef","containerProps","focusProps","externalRef","parentScrollingRef","arrow","align","direction","position","overflow","setDownOverflow","repositionFlag","captureFocus","state","endTransition","isDisabled","menuItemFocus","offsetX","offsetY","children","onClose"],p=function(e){var t=e.ariaLabel,n=e.menuClassName,p=e.menuStyle,O=e.arrowClassName,j=e.arrowStyle,g=e.anchorPoint,w=e.anchorRef,R=e.containerRef,y=e.containerProps,E=e.focusProps,C=e.externalRef,x=e.parentScrollingRef,S=e.arrow,k=e.align,T=void 0===k?"start":k,M=e.direction,N=void 0===M?"bottom":M,D=e.position,P=void 0===D?"auto":D,L=e.overflow,I=void 0===L?"visible":L,A=e.setDownOverflow,B=e.repositionFlag,F=e.captureFocus,Y=void 0===F||F,H=e.state,X=e.endTransition,z=e.isDisabled,U=e.menuItemFocus,_=e.offsetX,q=void 0===_?0:_,K=e.offsetY,W=void 0===K?0:K,V=e.children,G=e.onClose,J=Object(r.b)(e,m),Q=Object(o.useState)({x:0,y:0}),Z=Q[0],$=Q[1],ee=Object(o.useState)({}),te=ee[0],ne=ee[1],re=Object(o.useState)(),oe=re[0],ie=re[1],ce=Object(o.useState)(N),ae=ce[0],ue=ce[1],fe=Object(o.useState)(s)[0],le=Object(o.useReducer)((function(e){return e+1}),1),se=le[0],de=le[1],be=Object(o.useContext)(f.j),ve=be.transition,he=be.boundingBoxRef,me=be.boundingBoxPadding,pe=be.rootMenuRef,Oe=be.rootAnchorRef,je=be.scrollNodesRef,ge=be.reposition,we=be.viewScroll,Re=be.submenuCloseDelay,ye=Object(o.useContext)(f.g),Ee=ye.submenuCtx,Ce=ye.reposSubmenu,xe=void 0===Ce?B:Ce,Se=Object(o.useRef)(null),ke=Object(o.useRef)(),Te=Object(o.useRef)(),Me=Object(o.useRef)(!1),Ne=Object(o.useRef)({width:0,height:0}),De=Object(o.useRef)((function(){})),Pe=function(e,t){var n=Object(o.useState)(),r=n[0],i=n[1],c=Object(o.useRef)({items:[],hoverIndex:-1,sorted:!1}).current,u=Object(o.useCallback)((function(e,n){var r=c.items;if(e)if(n)r.push(e);else{var o=r.indexOf(e);o>-1&&(r.splice(o,1),e.contains(document.activeElement)&&(t.current.focus(),i()))}else c.items=[];c.hoverIndex=-1,c.sorted=!1}),[c,t]);return{hoverItem:r,dispatch:Object(o.useCallback)((function(t,n,r){var o=c.items,u=c.hoverIndex,l=function(){if(!c.sorted){var t=e.current.querySelectorAll(".szh-menu__item");o.sort((function(e,n){return Object(a.f)(t,e)-Object(a.f)(t,n)})),c.sorted=!0}},s=-1,d=void 0;switch(t){case f.d.RESET:break;case f.d.SET:d=n;break;case f.d.UNSET:d=function(e){return e===n?void 0:e};break;case f.d.FIRST:l(),d=o[s=0];break;case f.d.LAST:l(),s=o.length-1,d=o[s];break;case f.d.SET_INDEX:l(),d=o[s=r];break;case f.d.INCREASE:l(),(s=u)<0&&(s=o.indexOf(n)),++s>=o.length&&(s=0),d=o[s];break;case f.d.DECREASE:l(),(s=u)<0&&(s=o.indexOf(n)),--s<0&&(s=o.length-1),d=o[s]}d||(s=-1),i(d),c.hoverIndex=s}),[e,c]),updateItems:u}}(Se,ke),Le=Pe.hoverItem,Ie=Pe.dispatch,Ae=Pe.updateItems,Be=Object(a.g)(H),Fe=Object(a.e)(ve,"open"),Ye=Object(a.e)(ve,"close"),He=je.current,Xe=Object(o.useCallback)((function(e){var t,n=w?null==(t=w.current)?void 0:t.getBoundingClientRect():g?{left:g.x,right:g.x,top:g.y,bottom:g.y,width:0,height:0}:null;if(n){He.menu||(He.menu=(he?he.current:Object(a.d)(pe.current))||window);var o=function(e,t,n,r){var o=t.current.getBoundingClientRect(),i=e.current.getBoundingClientRect(),c=n===window?{left:0,top:0,right:document.documentElement.clientWidth,bottom:window.innerHeight}:n.getBoundingClientRect(),u=Object(a.i)(r),f=function(e){return e+i.left-c.left-u.left},l=function(e){return e+i.left+o.width-c.right+u.right},s=function(e){return e+i.top-c.top-u.top},d=function(e){return e+i.top+o.height-c.bottom+u.bottom};return{menuRect:o,containerRect:i,getLeftOverflow:f,getRightOverflow:l,getTopOverflow:s,getBottomOverflow:d,confineHorizontally:function(e){var t=f(e);if(t<0)e-=t;else{var n=l(e);n>0&&(t=f(e-=n))<0&&(e-=t)}return e},confineVertically:function(e){var t=s(e);if(t<0)e-=t;else{var n=d(e);n>0&&(t=s(e-=n))<0&&(e-=t)}return e}}}(R,Se,He.menu,me),i=function(e){var t=e.arrow,n=e.align,o=e.direction,i=e.offsetX,c=e.offsetY,a=e.position,u=e.anchorRect,f=e.arrowRef,l=e.positionHelpers,s=l.menuRect,v=l.containerRect,h=i,m=c;t&&("left"===o||"right"===o?h+=f.current.offsetWidth:m+=f.current.offsetHeight);var p,O,j=u.left-v.left-s.width-h,g=u.right-v.left+h,w=u.top-v.top-s.height-m,R=u.bottom-v.top+m;"end"===n?(p=u.right-v.left-s.width,O=u.bottom-v.top-s.height):"center"===n?(p=u.left-v.left-(s.width-u.width)/2,O=u.top-v.top-(s.height-u.height)/2):(p=u.left-v.left,O=u.top-v.top),p+=h,O+=m;var y=Object(r.a)({},l,{anchorRect:u,placeLeftX:j,placeRightX:g,placeLeftorRightY:O,placeTopY:w,placeBottomY:R,placeToporBottomX:p,arrowRef:f,arrow:t,direction:o,position:a});switch(o){case"left":case"right":return d(y);default:return b(y)}}({arrow:S,align:T,direction:N,offsetX:q,offsetY:W,position:P,anchorRect:n,arrowRef:Te,positionHelpers:o}),c=i.arrowX,u=i.arrowY,f=i.x,l=i.y,s=i.computedDirection,v=o.menuRect,h=v.height;if(!e&&"visible"!==I){var m,p,O=o.getTopOverflow,j=o.getBottomOverflow,y=Ne.current.height,E=j(l);if(E>0||Object(a.c)(E,0)&&Object(a.c)(h,y))m=h-E,p=E;else{var C=O(l);(C<0||Object(a.c)(C,0)&&Object(a.c)(h,y))&&(p=0-C,(m=h+C)>=0&&(l-=C))}m>=0?(h=m,ie({height:m,overflowAmt:p})):ie()}S&&ne({x:c,y:u}),$({x:f,y:l}),ue(s),Ne.current={width:v.width,height:h}}}),[S,T,me,N,q,W,P,I,g,w,R,he,pe,He]);Object(v.a)((function(){Be&&(Xe(),Me.current&&de()),Me.current=Be,De.current=Xe}),[Be,Xe,xe]),Object(v.a)((function(){oe&&!A&&(Se.current.scrollTop=0)}),[oe,A]),Object(v.a)((function(){return Ae}),[Ae]),Object(o.useEffect)((function(){var e=He.menu;if(Be&&e){if(e=e.addEventListener?e:window,!He.anchors){He.anchors=[];for(var t=Object(a.d)(Oe&&Oe.current);t&&t!==e;)He.anchors.push(t),t=Object(a.d)(t)}var n=we;if(He.anchors.length&&"initial"===n&&(n="auto"),"initial"!==n){var r=function(){"auto"===n?Object(a.a)((function(){return Xe(!0)})):Object(a.j)(G,{reason:f.a.SCROLL})},o=He.anchors.concat("initial"!==we?e:[]);return o.forEach((function(e){return e.addEventListener("scroll",r)})),function(){return o.forEach((function(e){return e.removeEventListener("scroll",r)}))}}}}),[Oe,He,Be,G,we,Xe]);var ze=!!oe&&oe.overflowAmt>0;Object(o.useEffect)((function(){if(!ze&&Be&&x){var e=function(){return Object(a.a)(Xe)},t=x.current;return t.addEventListener("scroll",e),function(){return t.removeEventListener("scroll",e)}}}),[Be,ze,x,Xe]),Object(o.useEffect)((function(){if("function"===typeof ResizeObserver&&"initial"!==ge){var e=new ResizeObserver((function(e){var t,n,r=e[0],o=r.borderBoxSize,c=r.target;if(o){var u=o[0]||o;t=u.inlineSize,n=u.blockSize}else{var f=c.getBoundingClientRect();t=f.width,n=f.height}0!==t&&0!==n&&(Object(a.c)(t,Ne.current.width,1)&&Object(a.c)(n,Ne.current.height,1)||Object(i.flushSync)((function(){De.current(),de()})))})),t=Se.current;return e.observe(t,{box:"border-box"}),function(){return e.unobserve(t)}}}),[ge]),Object(o.useEffect)((function(){if(!Be)return Ie(f.d.RESET),void(Ye||ie());var e=U||{},t=e.position,n=e.alwaysUpdate,r=function(){t===f.c.FIRST?Ie(f.d.FIRST):t===f.c.LAST?Ie(f.d.LAST):t>=-1&&Ie(f.d.SET_INDEX,void 0,t)};if(n)r();else if(Y){var o=setTimeout((function(){Se.current.contains(document.activeElement)||(ke.current.focus(),r())}),Fe?170:100);return function(){return clearTimeout(o)}}}),[Be,Fe,Ye,Y,U,Ie]);var Ue,_e,qe=Object(o.useMemo)((function(){return{isParentOpen:Be,submenuCtx:fe,dispatch:Ie,updateItems:Ae}}),[Be,fe,Ie,Ae]);oe&&(A?_e=oe.overflowAmt:Ue=oe.height);var Ke=Object(o.useMemo)((function(){return{reposSubmenu:se,submenuCtx:fe,overflow:I,overflowAmt:_e,parentMenuRef:Se,parentDir:ae}}),[se,fe,I,_e,ae]),We=Ue>=0?{maxHeight:Ue,overflow:I}:void 0,Ve=Object(o.useMemo)((function(){return{state:H,dir:ae}}),[H,ae]),Ge=Object(o.useMemo)((function(){return{dir:ae}}),[ae]),Je=Object(u.a)({block:f.m,element:f.l,modifiers:Ge,className:O}),Qe=Object(c.jsxs)("ul",Object(r.a)({role:"menu","aria-label":t},Object(a.b)(z),Object(a.h)({onPointerEnter:null==Ee?void 0:Ee.off,onPointerMove:function(e){e.stopPropagation(),fe.on(Re,(function(){Ie(f.d.RESET),ke.current.focus()}))},onPointerLeave:function(e){e.target===e.currentTarget&&fe.off()},onKeyDown:function(e){switch(e.key){case f.f.HOME:Ie(f.d.FIRST);break;case f.f.END:Ie(f.d.LAST);break;case f.f.UP:Ie(f.d.DECREASE,Le);break;case f.f.DOWN:Ie(f.d.INCREASE,Le);break;case f.f.SPACE:return void(e.target&&-1!==e.target.className.indexOf(f.m)&&e.preventDefault());default:return}e.preventDefault(),e.stopPropagation()},onAnimationEnd:function(){"closing"===H&&ie(),Object(a.j)(X)}},J),{ref:Object(h.a)(C,Se),className:Object(u.a)({block:f.m,modifiers:Ve,className:n}),style:Object(r.a)({},p,We,{margin:0,display:"closed"===H?"none":void 0,position:f.p,left:Z.x,top:Z.y}),children:[Object(c.jsx)("li",Object(r.a)({tabIndex:-1,style:{position:f.p,left:0,top:0,display:"block",outline:"none"},ref:ke},f.k,E)),S&&Object(c.jsx)("li",Object(r.a)({className:Je,style:Object(r.a)({display:"block",position:f.p,left:te.x,top:te.y},j),ref:Te},f.k)),Object(c.jsx)(f.g.Provider,{value:Ke,children:Object(c.jsx)(f.h.Provider,{value:qe,children:Object(c.jsx)(f.e.Provider,{value:Le,children:Object(a.j)(V,Ve)})})})]}));return y?Object(c.jsx)(l,Object(r.a)({},y,{isOpen:Be,children:Qe})):Qe},O=["aria-label","className","containerProps","initialMounted","unmountOnClose","transition","transitionTimeout","boundingBoxRef","boundingBoxPadding","reposition","submenuOpenDelay","submenuCloseDelay","skipOpen","viewScroll","portal","theming","onItemClick"],j=Object(o.forwardRef)((function(e,t){var n=e["aria-label"],u=e.className,l=e.containerProps,s=e.initialMounted,d=e.unmountOnClose,b=e.transition,v=e.transitionTimeout,h=e.boundingBoxRef,m=e.boundingBoxPadding,j=e.reposition,g=void 0===j?"auto":j,w=e.submenuOpenDelay,R=void 0===w?300:w,y=e.submenuCloseDelay,E=void 0===y?150:y,C=e.skipOpen,x=e.viewScroll,S=void 0===x?"initial":x,k=e.portal,T=e.theming,M=e.onItemClick,N=Object(r.b)(e,O),D=Object(o.useRef)(null),P=Object(o.useRef)({}),L=N.anchorRef,I=N.state,A=N.onClose,B=Object(o.useMemo)((function(){return{initialMounted:s,unmountOnClose:d,transition:b,transitionTimeout:v,boundingBoxRef:h,boundingBoxPadding:m,rootMenuRef:D,rootAnchorRef:L,scrollNodesRef:P,reposition:g,viewScroll:S,submenuOpenDelay:R,submenuCloseDelay:E}}),[s,d,b,v,L,h,m,g,S,R,E]),F=Object(o.useMemo)((function(){return{handleClick:function(e,t){e.stopPropagation||Object(a.j)(M,e);var n=e.keepOpen;void 0===n&&(n=t&&e.key===f.f.SPACE),n||Object(a.j)(A,{value:e.value,key:e.key,reason:f.a.CLICK})},handleClose:function(e){Object(a.j)(A,{key:e,reason:f.a.CLICK})}}}),[M,A]);if(!I)return null;var Y=Object(c.jsx)(f.j.Provider,{value:B,children:Object(c.jsx)(f.b.Provider,{value:F,children:Object(c.jsx)(p,Object(r.a)({},N,{ariaLabel:n||"Menu",externalRef:t,containerRef:D,containerProps:{className:u,containerRef:D,containerProps:l,skipOpen:C,theming:T,transition:b,onClose:A}}))})});return!0===k&&"undefined"!==typeof document?Object(i.createPortal)(Y,document.body):k?k.target?Object(i.createPortal)(Y,k.target):k.stablePosition?null:Y:Y}))},725:function(e,t,n){"use strict";n.d(t,"a",(function(){return h}));var r=n(532),o=n(1),i=n(14),c=n(583),a=n(530),u=function(e,t,n,r){var i=Object(o.useContext)(a.j).submenuCloseDelay,u=Object(o.useContext)(a.h),f=u.isParentOpen,l=u.submenuCtx,s=u.dispatch,d=u.updateItems,b=function(){!n&&!r&&s(a.d.SET,e.current)},v=function(){!r&&s(a.d.UNSET,e.current)};return function(e,t,n){Object(c.a)((function(){if(!e){var r=t.current;return n(r,!0),function(){n(r)}}}),[e,t,n])}(r,e,d),Object(o.useEffect)((function(){n&&f&&t.current&&t.current.focus()}),[t,n,f]),{setHover:b,onBlur:function(e){n&&!e.currentTarget.contains(e.relatedTarget)&&v()},onPointerMove:function(e){r||(e.stopPropagation(),l.on(i,b,b))},onPointerLeave:function(e,t){l.off(),!t&&v()}}},f=n(584),l=n(565),s=function(e,t){var n=Object(o.memo)(t),c=Object(o.forwardRef)((function(e,t){var c=Object(o.useRef)(null);return Object(i.jsx)(n,Object(r.a)({},e,{itemRef:c,externalRef:t,isHovering:Object(o.useContext)(a.e)===c.current}))}));return c.displayName="WithHovering("+e+")",c},d=n(533),b=["className","value","href","type","checked","disabled","children","onClick","isHovering","itemRef","externalRef"],v=["setHover"],h=s("MenuItem",(function(e){var t=e.className,n=e.value,c=e.href,s=e.type,h=e.checked,m=e.disabled,p=e.children,O=e.onClick,j=e.isHovering,g=e.itemRef,w=e.externalRef,R=Object(r.b)(e,b),y=!!m,E=u(g,g,j,y),C=E.setHover,x=Object(r.b)(E,v),S=Object(o.useContext)(a.b),k=Object(o.useContext)(a.i),T="radio"===s,M="checkbox"===s,N=!!c&&!y&&!T&&!M,D=T?k.value===n:!!M&&!!h,P=function(e){if(y)return e.stopPropagation(),void e.preventDefault();var t={value:n,syntheticEvent:e};void 0!==e.key&&(t.key=e.key),M&&(t.checked=!D),T&&(t.name=k.name),Object(d.j)(O,t),T&&Object(d.j)(k.onRadioChange,t),S.handleClick(t,M||T)},L=Object(o.useMemo)((function(){return{type:s,disabled:y,hover:j,checked:D,anchor:N}}),[s,y,j,D,N]),I=Object(d.h)(Object(r.a)({},x,{onPointerDown:C,onKeyDown:function(e){if(j)switch(e.key){case a.f.ENTER:case a.f.SPACE:N?e.key===a.f.SPACE&&g.current.click():P(e)}},onClick:P}),R),A=Object(r.a)({role:T?"menuitemradio":M?"menuitemcheckbox":a.q,"aria-checked":T||M?D:void 0},Object(d.b)(y,j),I,{ref:Object(f.a)(w,g),className:Object(l.a)({block:a.m,element:a.o,modifiers:L,className:t}),children:Object(o.useMemo)((function(){return Object(d.j)(p,L)}),[p,L])});return N?Object(i.jsx)("li",{role:a.r,children:Object(i.jsx)("a",Object(r.a)({href:c},A))}):Object(i.jsx)("li",Object(r.a)({},A))}))}}]);
//# sourceMappingURL=2.751024b5.chunk.js.map