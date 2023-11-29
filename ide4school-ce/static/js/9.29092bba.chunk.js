(this["webpackJsonp@raspberrypifoundation/editor-ui"]=this["webpackJsonp@raspberrypifoundation/editor-ui"]||[]).push([[9],{519:function(t,e,n){},523:function(t,e,n){"use strict";n(1);var r=n(538),o=n.n(r),a=n(103),i=(n(519),n(102)),c=n(235),s=n(14);e.a=function(t){var e=t.buttons,n=void 0===e?[]:e,r=t.children,l=t.defaultCallback,u=t.heading,d=t.isOpen,p=t.text,b=void 0===p?[]:p,h=t.withCloseButton,j=void 0!==h&&h,f=t.closeModal,v=Object(i.b)().t;return Object(s.jsx)("div",{onKeyDown:function(t){"Enter"===t.key&&l&&(t.preventDefault(),l())},children:Object(s.jsxs)(o.a,{isOpen:d,onRequestClose:f,className:"modal-content",overlayClassName:"modal-overlay",contentLabel:u,parentSelector:function(){return document.querySelector("#app")},appElement:document.getElementById("app")||void 0,children:[Object(s.jsxs)("div",{className:"modal-content__header",children:[Object(s.jsx)("h2",{className:"modal-content__heading",children:u}),j?Object(s.jsx)(a.a,{className:"btn--tertiary",onClickHandler:f,ButtonIcon:c.a,label:v("modals.close"),title:v("modals.close")}):null]}),Object(s.jsxs)("div",{className:"modal-content__body",children:[b.map((function(t,e){return"subheading"===t.type?Object(s.jsx)("h3",{className:"modal-content__subheading",children:t.content},e):Object(s.jsx)("p",{className:"modal-content__text",children:t.content},e)})),r]}),Object(s.jsx)("div",{className:"modal-content__buttons",children:n})]})})}},525:function(t,e,n){"use strict";n.d(e,"b",(function(){return o})),n.d(e,"a",(function(){return i}));var r=n(322),o={identifier:"new",project_type:"python",name:r.a.t("project.untitled"),locale:null,components:[{extension:"py",name:"main",content:"",default:!0}],image_list:[],to_review:!1},a={project_type:"html",name:r.a.t("project.untitled"),components:[{extension:"html",name:"index",content:""},{extension:"css",name:"style",content:""}],to_review:!1},i={python:o,html:a}},534:function(t,e,n){},537:function(t,e,n){"use strict";var r,o=n(1);function a(){return a=Object.assign?Object.assign.bind():function(t){for(var e=1;e<arguments.length;e++){var n=arguments[e];for(var r in n)Object.prototype.hasOwnProperty.call(n,r)&&(t[r]=n[r])}return t},a.apply(this,arguments)}e.a=function(t){return o.createElement("svg",a({width:24,height:24,viewBox:"0 0 24 24",fill:"none",xmlns:"http://www.w3.org/2000/svg"},t),r||(r=o.createElement("path",{d:"M5 19h1.4l8.625-8.625-1.4-1.4L5 17.6V19zM19.3 8.925l-4.25-4.2 1.4-1.4a1.92 1.92 0 011.413-.575 1.92 1.92 0 011.412.575l1.4 1.4c.383.383.583.846.6 1.388a1.806 1.806 0 01-.55 1.387L19.3 8.925zM17.85 10.4L7.25 21H3v-4.25l10.6-10.6 4.25 4.25zm-3.525-.725l-.7-.7 1.4 1.4-.7-.7z"})))}},540:function(t,e,n){"use strict";var r,o,a=n(0),i=n(130),c=n(1),s=n(523),l=(n(534),n(78)),u=n(14),d=function(){var t=Object(l.e)((function(t){return t.editor.nameError}));return t?Object(u.jsx)("div",{className:"error-message",children:Object(u.jsx)("p",{className:"error-message__content",children:t})}):null};n(568);function p(){return p=Object.assign?Object.assign.bind():function(t){for(var e=1;e<arguments.length;e++){var n=arguments[e];for(var r in n)Object.prototype.hasOwnProperty.call(n,r)&&(t[r]=n[r])}return t},p.apply(this,arguments)}var b=function(t){return c.createElement("svg",p({width:24,height:24,viewBox:"0 0 24 24",fill:"none",xmlns:"http://www.w3.org/2000/svg"},t),r||(r=c.createElement("rect",{width:24,height:24,rx:12,fill:"#14BDAC"})),o||(o=c.createElement("path",{d:"M9.958 17l-4.75-4.75 1.188-1.187 3.562 3.562 7.646-7.646 1.188 1.188L9.958 17z",fill:"#000"})))},h=function(t){var e=t.label,n=t.options,r=t.value,o=t.setValue;return Object(u.jsxs)("fieldset",{className:"select-buttons",children:[Object(u.jsx)("legend",{className:"select-buttons__legend",children:e}),Object(u.jsx)("div",{className:"select-buttons__options",children:n.map((function(t,e){return Object(u.jsxs)("div",{className:"select-buttons__option",children:[Object(u.jsx)("input",{className:"select-buttons__button",type:"radio",id:"option".concat(e),value:t.value,onChange:function(t){return o(t.target.value)},checked:t.value===r}),Object(u.jsxs)("label",{className:"select-buttons__label".concat(t.value===r?" select-buttons__label--selected":""),htmlFor:"option".concat(e),children:[t.Icon?Object(u.jsx)(t.Icon,{}):null,t.label,Object(u.jsx)("div",{className:"select-buttons__tick".concat(t.value===r?" select-buttons__tick--selected":""),children:Object(u.jsx)(b,{})})]})]},e)}))})]})},j=["inputs"];e.a=function(t){var e=t.inputs,n=Object(i.a)(t,j),r=Object(c.useCallback)((function(t){t&&t.select()}),[]);return Object(u.jsx)(s.a,Object(a.a)(Object(a.a)({},n),{},{children:Object(u.jsx)("div",{className:"modal-content__inputs",children:e.map((function(t,e){return Object(u.jsx)("div",{children:"radio"===t.type?Object(u.jsx)(h,{label:t.label,options:t.options,value:t.value,setValue:t.setValue}):Object(u.jsxs)("div",{className:"modal-content__input-section",children:[Object(u.jsxs)("label",{htmlFor:e,children:[t.label,Object(u.jsx)("p",{className:"modal-content__help-text",children:t.helpText})]}),Object(u.jsxs)("div",{className:"modal-content__input",children:[t.validateName?Object(u.jsx)(d,{}):null,Object(u.jsx)("input",{ref:0===e?r:null,type:"text",id:e,onChange:function(e){return t.setValue(e.target.value)},value:t.value})]})]})},e)}))})}))}},541:function(t,e,n){"use strict";n.d(e,"b",(function(){return d})),n.d(e,"a",(function(){return p}));var r=n(0),o=n(206),a=(n(542),n(543)),i=n(322),c=n(103),s=n(235),l=n(14),u={position:o.b.POSITION.BOTTOM_CENTER,autoClose:3e3,className:"toast--bottom-center__message",closeButton:!1,containerId:"bottom-center",hideProgressBar:!0},d=(o.b.POSITION.TOP_CENTER,function(){Object(o.b)(i.a.t("notifications.projectSaved"),Object(r.a)(Object(r.a)({},u),{},{icon:a.a}))}),p=function(){Object(o.b)(i.a.t("notifications.projectRenamed"),Object(r.a)(Object(r.a)({},u),{},{icon:a.a}))}},542:function(t,e,n){"use strict";var r,o=n(1);function a(){return a=Object.assign?Object.assign.bind():function(t){for(var e=1;e<arguments.length;e++){var n=arguments[e];for(var r in n)Object.prototype.hasOwnProperty.call(n,r)&&(t[r]=n[r])}return t},a.apply(this,arguments)}e.a=function(t){return o.createElement("svg",a({width:24,height:24,viewBox:"0 0 24 24",fill:"none",xmlns:"http://www.w3.org/2000/svg"},t),r||(r=o.createElement("path",{d:"M11 17h2v-6h-2v6zm1-8a.968.968 0 00.713-.288A.967.967 0 0013 8a.97.97 0 00-.287-.713A.97.97 0 0012 7a.967.967 0 00-.712.287A.968.968 0 0011 8c0 .283.096.52.288.712A.965.965 0 0012 9zm0 13a9.733 9.733 0 01-3.9-.788 10.092 10.092 0 01-3.175-2.137c-.9-.9-1.612-1.958-2.137-3.175A9.733 9.733 0 012 12c0-1.383.263-2.683.788-3.9a10.092 10.092 0 012.137-3.175c.9-.9 1.958-1.613 3.175-2.138A9.743 9.743 0 0112 2c1.383 0 2.683.262 3.9.787a10.105 10.105 0 013.175 2.138c.9.9 1.612 1.958 2.137 3.175A9.733 9.733 0 0122 12a9.733 9.733 0 01-.788 3.9 10.092 10.092 0 01-2.137 3.175c-.9.9-1.958 1.612-3.175 2.137A9.733 9.733 0 0112 22zm0-2c2.217 0 4.104-.779 5.663-2.337C19.221 16.104 20 14.217 20 12s-.779-4.104-2.337-5.663C16.104 4.779 14.217 4 12 4s-4.104.779-5.662 2.337C4.779 7.896 4 9.783 4 12s.78 4.104 2.338 5.663C7.896 19.221 9.783 20 12 20z"})))}},543:function(t,e,n){"use strict";var r,o=n(1);function a(){return a=Object.assign?Object.assign.bind():function(t){for(var e=1;e<arguments.length;e++){var n=arguments[e];for(var r in n)Object.prototype.hasOwnProperty.call(n,r)&&(t[r]=n[r])}return t},a.apply(this,arguments)}e.a=function(t){return o.createElement("svg",a({width:18,height:13,viewBox:"0 0 18 13",fill:"none",xmlns:"http://www.w3.org/2000/svg"},t),r||(r=o.createElement("path",{d:"M6.55 13L.85 7.3l1.425-1.425L6.55 10.15 15.725.975 17.15 2.4 6.55 13z"})))}},560:function(t,e,n){"use strict";e.a=n.p+"static/media/python_icon.51f1d7d2.svg"},561:function(t,e,n){"use strict";e.a=n.p+"static/media/html_icon.5aff5d91.svg"},562:function(t,e,n){"use strict";var r,o=n(1);function a(){return a=Object.assign?Object.assign.bind():function(t){for(var e=1;e<arguments.length;e++){var n=arguments[e];for(var r in n)Object.prototype.hasOwnProperty.call(n,r)&&(t[r]=n[r])}return t},a.apply(this,arguments)}e.a=function(t){return o.createElement("svg",a({width:4,height:16,viewBox:"0 0 4 16",fill:"none",xmlns:"http://www.w3.org/2000/svg"},t),r||(r=o.createElement("path",{d:"M2 16c-.55 0-1.02-.196-1.412-.587A1.927 1.927 0 010 14c0-.55.196-1.021.588-1.413A1.925 1.925 0 012 12c.55 0 1.021.196 1.413.587.391.392.587.863.587 1.413s-.196 1.021-.587 1.413A1.928 1.928 0 012 16zm0-6c-.55 0-1.02-.196-1.412-.588A1.923 1.923 0 010 8c0-.55.196-1.021.588-1.413A1.925 1.925 0 012 6c.55 0 1.021.196 1.413.587C3.804 6.979 4 7.45 4 8s-.196 1.02-.587 1.412A1.927 1.927 0 012 10zm0-6C1.45 4 .98 3.804.588 3.412A1.923 1.923 0 010 2C0 1.45.196.98.588.588A1.923 1.923 0 012 0c.55 0 1.021.196 1.413.588C3.804.979 4 1.45 4 2c0 .55-.196 1.02-.587 1.412A1.927 1.927 0 012 4z"})))}},563:function(t,e,n){"use strict";var r=n(7),o=n(1),a=n(723),i=n(725),c=n(234),s=(n(564),n(14));e.a=function(t){var e=t.align,n=t.direction,l=t.menuButtonLabel,u=t.menuButtonClassName,d=t.MenuButtonIcon,p=t.menuOptions,b=t.offsetX,h=t.offsetY,j=Object(o.useContext)(c.a),f=Object(o.useRef)(null),v=Object(o.useRef)(),O=Object(o.useState)(!1),m=Object(r.a)(O,2),g=m[0],y=m[1],x=function(t){if(y(t),t){var e=v.current.firstChild;e.setAttribute("role","menuitem"),e.setAttribute("aria-hidden","true")}else f.current.focus()};return Object(s.jsxs)(s.Fragment,{children:[Object(s.jsx)("button",{"aria-haspopup":"menu","aria-label":l,className:"btn btn--tertiary context-menu__drop".concat(u?" ".concat(u):""),title:l,type:"button",ref:f,onClick:function(){return x(!0)},children:Object(s.jsx)(d,{})}),Object(s.jsx)(a.a,{transition:!0,align:e,direction:n,menuStyle:{padding:"5px"},offsetX:b,offsetY:h,position:"anchor",viewScroll:"initial",portal:!0,menuClassName:"context-menu context-menu--".concat(j.theme),menuItemFocus:{position:"first"},state:g?"open":"closed",anchorRef:f,ref:v,onClose:function(){return x(!1)},children:p.map((function(t,e){return Object(s.jsxs)(i.a,{className:"btn context-menu__item",onClick:t.action,children:[Object(s.jsx)(t.icon,{}),"\xa0",t.text]},e)}))})]})}},564:function(t,e,n){},566:function(t,e,n){"use strict";var r,o=n(1);function a(){return a=Object.assign?Object.assign.bind():function(t){for(var e=1;e<arguments.length;e++){var n=arguments[e];for(var r in n)Object.prototype.hasOwnProperty.call(n,r)&&(t[r]=n[r])}return t},a.apply(this,arguments)}e.a=function(t){return o.createElement("svg",a({width:14,height:14,viewBox:"0 0 14 14",fill:"none",xmlns:"http://www.w3.org/2000/svg"},t),r||(r=o.createElement("path",{d:"M6 14V8H0V6h6V0h2v6h6v2H8v6H6z"})))}},568:function(t,e,n){},623:function(t,e,n){},624:function(t,e,n){},625:function(t,e,n){},635:function(t,e,n){},722:function(t,e,n){"use strict";n.r(e),n.d(e,"PROJECT_INDEX_QUERY",(function(){return Ct}));var r=n(11),o=n(16);function a(t,e){return e||(e=t.slice(0)),Object.freeze(Object.defineProperties(t,{raw:{value:Object.freeze(e)}}))}var i=n(78),c=n(102),s=n(132),l=n(5),u=n(12),d=n(1),p=n(114),b=!1,h=d.useSyncExternalStore||function(t,e,n){var r=e();__DEV__&&!b&&r!==e()&&(b=!0,__DEV__&&u.c.error("The result of getSnapshot should be cached to avoid an infinite loop"));var o=d.useState({inst:{value:r,getSnapshot:e}}),a=o[0].inst,i=o[1];return p.b?d.useLayoutEffect((function(){Object.assign(a,{value:r,getSnapshot:e}),j(a)&&i({inst:a})}),[t,r,e]):Object.assign(a,{value:r,getSnapshot:e}),d.useEffect((function(){return j(a)&&i({inst:a}),t((function(){j(a)&&i({inst:a})}))}),[t]),r};function j(t){var e=t.value,n=t.getSnapshot;try{return e!==n()}catch(r){return!0}}var f,v=n(39),O=n(507),m=n(313),g=n(63),y=n(32);!function(t){t[t.Query=0]="Query",t[t.Mutation=1]="Mutation",t[t.Subscription=2]="Subscription"}(f||(f={}));var x=new Map;function _(t){var e;switch(t){case f.Query:e="Query";break;case f.Mutation:e="Mutation";break;case f.Subscription:e="Subscription"}return e}function w(t,e){var n=function(t){var e,n,r=x.get(t);if(r)return r;__DEV__?Object(u.c)(!!t&&!!t.kind,"Argument of ".concat(t," passed to parser was not a valid GraphQL ")+"DocumentNode. You may need to use 'graphql-tag' or another method to convert your operation into a document"):Object(u.c)(!!t&&!!t.kind,33);for(var o=[],a=[],i=[],c=[],s=0,l=t.definitions;s<l.length;s++){var d=l[s];if("FragmentDefinition"!==d.kind){if("OperationDefinition"===d.kind)switch(d.operation){case"query":a.push(d);break;case"mutation":i.push(d);break;case"subscription":c.push(d)}}else o.push(d)}__DEV__?Object(u.c)(!o.length||a.length||i.length||c.length,"Passing only a fragment to 'graphql' is not yet supported. You must include a query, subscription or mutation as well"):Object(u.c)(!o.length||a.length||i.length||c.length,34),__DEV__?Object(u.c)(a.length+i.length+c.length<=1,"react-apollo only supports a query, subscription, or a mutation per HOC. "+"".concat(t," had ").concat(a.length," queries, ").concat(c.length," ")+"subscriptions and ".concat(i.length," mutations. ")+"You can use 'compose' to join multiple operation types to a component"):Object(u.c)(a.length+i.length+c.length<=1,35),n=a.length?f.Query:f.Mutation,a.length||i.length||(n=f.Subscription);var p=a.length?a:i.length?i:c;__DEV__?Object(u.c)(1===p.length,"react-apollo only supports one definition per HOC. ".concat(t," had ")+"".concat(p.length," definitions. ")+"You can use 'compose' to join multiple operation types to a component"):Object(u.c)(1===p.length,36);var b=p[0];e=b.variableDefinitions||[];var h={name:b.name&&"Name"===b.name.kind?b.name.value:"data",type:n,variables:e};return x.set(t,h),h}(t),r=_(e),o=_(n.type);__DEV__?Object(u.c)(n.type===e,"Running a ".concat(r," requires a graphql ")+"".concat(r,", but a ").concat(o," was used instead.")):Object(u.c)(n.type===e,37)}function P(t){var e=Object(d.useContext)(Object(m.a)()),n=t||e.client;return __DEV__?Object(u.c)(!!n,'Could not find "client" in the context or passed in as an option. Wrap the root component in an <ApolloProvider>, or pass an ApolloClient instance in via options.'):Object(u.c)(!!n,32),n}var C=n(508),k=n(199),N=n(75),M=Object.prototype.hasOwnProperty;function E(t,e){return void 0===e&&(e=Object.create(null)),function(t,e){var n=Object(d.useRef)();n.current&&t===n.current.client&&e===n.current.query||(n.current=new S(t,e,n.current));var r=n.current,o=Object(d.useState)(0),a=(o[0],o[1]);return r.forceUpdate=function(){a((function(t){return t+1}))},r}(P(e.client),t).useQuery(e)}var L,S=function(){function t(t,e,n){this.client=t,this.query=e,this.ssrDisabledResult=Object(C.a)({loading:!0,data:void 0,error:void 0,networkStatus:y.a.loading}),this.skipStandbyResult=Object(C.a)({loading:!1,data:void 0,error:void 0,networkStatus:y.a.ready}),this.toQueryResultCache=new(p.d?WeakMap:Map),w(e,f.Query);var r=n&&n.result,o=r&&r.data;o&&(this.previousData=o)}return t.prototype.forceUpdate=function(){__DEV__&&u.c.warn("Calling default no-op implementation of InternalState#forceUpdate")},t.prototype.executeQuery=function(t){var e,n=this;t.query&&Object.assign(this,{query:t.query}),this.watchQueryOptions=this.createWatchQueryOptions(this.queryHookOptions=t);var r=this.observable.reobserveAsConcast(this.getObsQueryOptions());return this.previousData=(null===(e=this.result)||void 0===e?void 0:e.data)||this.previousData,this.result=void 0,this.forceUpdate(),new Promise((function(t){var e;r.subscribe({next:function(t){e=t},error:function(){t(n.toQueryResult(n.observable.getCurrentResult()))},complete:function(){t(n.toQueryResult(e))}})}))},t.prototype.useQuery=function(t){var e=this;this.renderPromises=Object(d.useContext)(Object(m.a)()).renderPromises,this.useOptions(t);var n=this.useObservableQuery(),r=h(Object(d.useCallback)((function(){if(e.renderPromises)return function(){};var t=function(){var t=e.result,r=n.getCurrentResult();t&&t.loading===r.loading&&t.networkStatus===r.networkStatus&&Object(v.a)(t.data,r.data)||e.setResult(r)},r=n.subscribe(t,(function o(a){var i=n.last;r.unsubscribe();try{n.resetLastResults(),r=n.subscribe(t,o)}finally{n.last=i}if(!M.call(a,"graphQLErrors"))throw a;var c=e.result;(!c||c&&c.loading||!Object(v.a)(a,c.error))&&e.setResult({data:c&&c.data,error:a,loading:!1,networkStatus:y.a.error})}));return function(){return setTimeout((function(){return r.unsubscribe()}))}}),[n,this.renderPromises,this.client.disableNetworkFetches]),(function(){return e.getCurrentResult()}),(function(){return e.getCurrentResult()}));return this.unsafeHandlePartialRefetch(r),this.toQueryResult(r)},t.prototype.useOptions=function(e){var n,r=this.createWatchQueryOptions(this.queryHookOptions=e),o=this.watchQueryOptions;Object(v.a)(r,o)||(this.watchQueryOptions=r,o&&this.observable&&(this.observable.reobserve(this.getObsQueryOptions()),this.previousData=(null===(n=this.result)||void 0===n?void 0:n.data)||this.previousData,this.result=void 0)),this.onCompleted=e.onCompleted||t.prototype.onCompleted,this.onError=e.onError||t.prototype.onError,!this.renderPromises&&!this.client.disableNetworkFetches||!1!==this.queryHookOptions.ssr||this.queryHookOptions.skip?this.queryHookOptions.skip||"standby"===this.watchQueryOptions.fetchPolicy?this.result=this.skipStandbyResult:this.result!==this.ssrDisabledResult&&this.result!==this.skipStandbyResult||(this.result=void 0):this.result=this.ssrDisabledResult},t.prototype.getObsQueryOptions=function(){var t=[],e=this.client.defaultOptions.watchQuery;return e&&t.push(e),this.queryHookOptions.defaultOptions&&t.push(this.queryHookOptions.defaultOptions),t.push(Object(k.a)(this.observable&&this.observable.options,this.watchQueryOptions)),t.reduce(O.a)},t.prototype.createWatchQueryOptions=function(t){var e;void 0===t&&(t={});var n=t.skip,r=(t.ssr,t.onCompleted,t.onError,t.defaultOptions,Object(l.__rest)(t,["skip","ssr","onCompleted","onError","defaultOptions"])),o=Object.assign(r,{query:this.query});if(!this.renderPromises||"network-only"!==o.fetchPolicy&&"cache-and-network"!==o.fetchPolicy||(o.fetchPolicy="cache-first"),o.variables||(o.variables={}),n){var a=o.fetchPolicy,i=void 0===a?this.getDefaultFetchPolicy():a,c=o.initialFetchPolicy,s=void 0===c?i:c;Object.assign(o,{initialFetchPolicy:s,fetchPolicy:"standby"})}else o.fetchPolicy||(o.fetchPolicy=(null===(e=this.observable)||void 0===e?void 0:e.options.initialFetchPolicy)||this.getDefaultFetchPolicy());return o},t.prototype.getDefaultFetchPolicy=function(){var t,e;return(null===(t=this.queryHookOptions.defaultOptions)||void 0===t?void 0:t.fetchPolicy)||(null===(e=this.client.defaultOptions.watchQuery)||void 0===e?void 0:e.fetchPolicy)||"cache-first"},t.prototype.onCompleted=function(t){},t.prototype.onError=function(t){},t.prototype.useObservableQuery=function(){var t=this.observable=this.renderPromises&&this.renderPromises.getSSRObservable(this.watchQueryOptions)||this.observable||this.client.watchQuery(this.getObsQueryOptions());this.obsQueryFields=Object(d.useMemo)((function(){return{refetch:t.refetch.bind(t),reobserve:t.reobserve.bind(t),fetchMore:t.fetchMore.bind(t),updateQuery:t.updateQuery.bind(t),startPolling:t.startPolling.bind(t),stopPolling:t.stopPolling.bind(t),subscribeToMore:t.subscribeToMore.bind(t)}}),[t]);var e=!(!1===this.queryHookOptions.ssr||this.queryHookOptions.skip);return this.renderPromises&&e&&(this.renderPromises.registerSSRObservable(t),t.getCurrentResult().loading&&this.renderPromises.addObservableQueryPromise(t)),t},t.prototype.setResult=function(t){var e=this.result;e&&e.data&&(this.previousData=e.data),this.result=t,this.forceUpdate(),this.handleErrorOrCompleted(t)},t.prototype.handleErrorOrCompleted=function(t){var e=this;if(!t.loading){var n=this.toApolloError(t);Promise.resolve().then((function(){n?e.onError(n):t.data&&e.onCompleted(t.data)})).catch((function(t){__DEV__&&u.c.warn(t)}))}},t.prototype.toApolloError=function(t){return Object(N.b)(t.errors)?new g.a({graphQLErrors:t.errors}):t.error},t.prototype.getCurrentResult=function(){return this.result||this.handleErrorOrCompleted(this.result=this.observable.getCurrentResult()),this.result},t.prototype.toQueryResult=function(t){var e=this.toQueryResultCache.get(t);if(e)return e;var n=t.data,r=(t.partial,Object(l.__rest)(t,["data","partial"]));return this.toQueryResultCache.set(t,e=Object(l.__assign)(Object(l.__assign)(Object(l.__assign)({data:n},r),this.obsQueryFields),{client:this.client,observable:this.observable,variables:this.observable.variables,called:!this.queryHookOptions.skip,previousData:this.previousData})),!e.error&&Object(N.b)(t.errors)&&(e.error=new g.a({graphQLErrors:t.errors})),e},t.prototype.unsafeHandlePartialRefetch=function(t){!t.partial||!this.queryHookOptions.partialRefetch||t.loading||t.data&&0!==Object.keys(t.data).length||"cache-only"===this.observable.options.fetchPolicy||(Object.assign(t,{loading:!0,networkStatus:y.a.refetch}),this.observable.refetch())},t}(),Q=n(17),H=(n(623),n(14)),R=function(t){var e=Object(c.b)().t;return Object(H.jsx)("header",{className:"editor-project-header",children:Object(H.jsxs)("div",{className:"editor-project-header__inner",children:[Object(H.jsxs)("div",{className:"editor-project-header__content",children:[Object(H.jsx)("h2",{children:e("projectHeader.subTitle")}),Object(H.jsx)("h1",{className:"editor-project-header__title",children:e("projectHeader.title")}),Object(H.jsx)("h3",{children:e("projectHeader.text")})]}),Object(H.jsx)("div",{className:"editor-project-header__action",children:t.children})]})})},D=n(721),I=n(233),T=n(103),q=n(560),z=n(561);n(624);function A(){return A=Object.assign?Object.assign.bind():function(t){for(var e=1;e<arguments.length;e++){var n=arguments[e];for(var r in n)Object.prototype.hasOwnProperty.call(n,r)&&(t[r]=n[r])}return t},A.apply(this,arguments)}var V,B,F=function(t){return d.createElement("svg",A({width:14,height:16,viewBox:"0 0 14 16",fill:"none",xmlns:"http://www.w3.org/2000/svg"},t),L||(L=d.createElement("path",{d:"M1.167 15.5V3H.333V1.333H4.5V.5h5v.833h4.167V3h-.834v12.5H1.167zm1.666-1.667h8.334V3H2.833v10.833zM4.5 12.167h1.667v-7.5H4.5v7.5zm3.333 0H9.5v-7.5H7.833v7.5zM2.833 3v10.833V3z"})))},$=n(537),U=n(562),Y=n(563),W=function(t){var e=t.project,n=Object(c.b)().t,r=Object(i.d)();return Object(H.jsx)(Y.a,{align:"end",direction:"bottom",menuButtonLabel:n("projectList.label"),menuButtonClassName:"editor-project-list__menu",MenuButtonIcon:U.a,menuOptions:[{icon:$.a,text:n("projectList.rename"),action:function(){r(Object(I.E)(e))}},{icon:F,text:n("projectList.delete"),action:function(){r(Object(I.z)(e))}}],offsetX:-10})},X=n(92),J=Object(s.d)(V||(V=a(["\n  fragment ProjectListItemFragment on Project {\n    name\n    identifier\n    locale\n    updatedAt\n    projectType\n  }\n"]))),G=function(t){var e=t.project,n=Object(c.b)(),r=n.t,o=n.i18n.language,a=Object(i.d)(),s=Object(D.a)(new Date(e.updatedAt),Date.now(),{style:"short"}),l=t.project.projectType;return Object(H.jsxs)("div",{className:"editor-project-list__item",children:[Object(H.jsx)("div",{className:"editor-project-list__info",children:Object(H.jsxs)(X.b,{className:"editor-project-list__title",to:"/".concat(o,"/projects/").concat(e.identifier),children:[Object(H.jsx)("img",{className:"editor-project-list__type",src:"html"===l?z.a:q.a,alt:r("header.editorLogoAltText")}),Object(H.jsxs)("div",{className:"editor-project-list__copy",children:[Object(H.jsx)("div",{className:"editor-project-list__name",children:e.name}),Object(H.jsx)("span",{className:"editor-project-list__tag",children:r("html"===l?"projectList.html_type":"projectList.python_type")}),Object(H.jsxs)("span",{className:"editor-project-list__heading",children:[r("projectList.updated")," ",s]})]})]})}),Object(H.jsxs)("div",{className:"editor-project-list__actions",children:[Object(H.jsx)(T.a,{className:"btn--tertiary editor-project-list__rename",buttonText:r("projectList.rename"),ButtonIcon:$.a,onClickHandler:function(){a(Object(I.E)(e))},label:r("projectList.renameLabel"),title:r("projectList.renameLabel")}),Object(H.jsx)(T.a,{className:"btn--tertiary editor-project-list__delete",buttonText:r("projectList.delete"),ButtonIcon:F,onClickHandler:function(){a(Object(I.z)(e))},label:r("projectList.deleteLabel"),title:r("projectList.deleteLabel")})]}),Object(H.jsx)(W,{project:e})]})},K=(n(625),Object(s.d)(B||(B=a(["\n  fragment ProjectListTableFragment on ProjectConnection {\n    edges {\n      cursor\n      node {\n        id\n        ...ProjectListItemFragment\n      }\n    }\n  }\n  ","\n"])),J)),Z=function(t){var e,n=Object(c.b)().t,r=t.projectData,o=null===r||void 0===r||null===(e=r.edges)||void 0===e?void 0:e.map((function(t){return t.node}));return Object(H.jsx)("div",{className:"editor-project-list",children:Object(H.jsx)("div",{className:"editor-project-list__container",children:o&&o.length>0?Object(H.jsx)(H.Fragment,{children:o.map((function(t,e){return Object(H.jsx)(G,{project:t},e)}))}):Object(H.jsx)("div",{className:"editor-project-list__empty",children:Object(H.jsx)("p",{children:n("projectList.empty")})})})})},tt=n(566),et=n(7);function nt(t,e){var n=P(null===e||void 0===e?void 0:e.client);w(t,f.Mutation);var r=Object(d.useState)({called:!1,loading:!1,client:n}),o=r[0],a=r[1],i=Object(d.useRef)({result:o,mutationId:0,isMounted:!0,client:n,mutation:t,options:e});Object.assign(i.current,{client:n,options:e,mutation:t});var c=Object(d.useCallback)((function(t){void 0===t&&(t={});var e=i.current,n=e.options,r=e.mutation,o=Object(l.__assign)(Object(l.__assign)({},n),{mutation:r}),c=t.client||i.current.client;i.current.result.loading||o.ignoreResults||!i.current.isMounted||a(i.current.result={loading:!0,error:void 0,data:void 0,called:!0,client:c});var s=++i.current.mutationId,u=Object(O.a)(o,t);return c.mutate(u).then((function(e){var n,r=e.data,o=e.errors,l=o&&o.length>0?new g.a({graphQLErrors:o}):void 0;if(s===i.current.mutationId&&!u.ignoreResults){var d={called:!0,loading:!1,data:r,error:l,client:c};i.current.isMounted&&!Object(v.a)(i.current.result,d)&&a(i.current.result=d)}var p=t.onCompleted||(null===(n=i.current.options)||void 0===n?void 0:n.onCompleted);return null===p||void 0===p||p(e.data,u),e})).catch((function(e){var n;if(s===i.current.mutationId&&i.current.isMounted){var r={loading:!1,error:e,data:void 0,called:!0,client:c};Object(v.a)(i.current.result,r)||a(i.current.result=r)}var o=t.onError||(null===(n=i.current.options)||void 0===n?void 0:n.onError);if(o)return o(e,u),{data:void 0,errors:e};throw e}))}),[]),s=Object(d.useCallback)((function(){i.current.isMounted&&a({called:!1,loading:!1,client:n})}),[]);return Object(d.useEffect)((function(){return i.current.isMounted=!0,function(){i.current.isMounted=!1}}),[]),[c,Object(l.__assign)({reset:s},o)]}var rt,ot,at,it,ct=n(541),st=n(540),lt=Object(s.d)(rt||(rt=a(["\n  mutation RenameProject($id: String!, $name: String!) {\n    updateProject(input: { id: $id, name: $name }) {\n      project {\n        id\n        name\n        updatedAt\n      }\n    }\n  }\n"]))),ut=function(){var t=Object(i.d)(),e=Object(c.b)().t,n=Object(i.e)((function(t){return t.editor.renameProjectModalShowing})),r=Object(i.e)((function(t){return t.editor.modals.renameProject})),o=function(){return t(Object(I.l)())},a=Object(d.useState)(r.name),s=Object(et.a)(a,2),l=s[0],u=s[1],p=function(){o(),Object(ct.a)()},b=nt(lt),h=Object(et.a)(b,1)[0],j=function(){h({variables:{id:r.id,name:l},onCompleted:p})};return Object(H.jsx)(st.a,{isOpen:n,closeModal:o,withCloseButton:!0,heading:e("projectList.renameProjectModal.heading"),inputs:[{label:e("projectList.renameProjectModal.inputLabel"),value:l,setValue:u}],defaultCallback:j,buttons:[Object(H.jsx)(T.a,{className:"btn--primary",buttonText:e("projectList.renameProjectModal.save"),onClickHandler:j},"rename"),Object(H.jsx)(T.a,{className:"btn--secondary",buttonText:e("projectList.renameProjectModal.cancel"),onClickHandler:o},"close")]})},dt=n(523),pt=Object(s.d)(ot||(ot=a(["\n  mutation DeleteProject($id: String!) {\n    deleteProject(input: { id: $id }) {\n      id\n    }\n  }\n"]))),bt=function(){var t=Object(i.d)(),e=Object(c.b)().t,n=Object(i.e)((function(t){return t.editor.deleteProjectModalShowing})),a=Object(i.e)((function(t){return t.editor.modals.deleteProject})),s=function(){return t(Object(I.d)())},l=nt(pt,{refetchQueries:["ProjectIndexQuery"]}),u=Object(et.a)(l,1)[0],d=function(){var t=Object(o.a)(Object(r.a)().mark((function t(){return Object(r.a)().wrap((function(t){for(;;)switch(t.prev=t.next){case 0:u({variables:{id:a.id},onCompleted:s});case 1:case"end":return t.stop()}}),t)})));return function(){return t.apply(this,arguments)}}();return Object(H.jsx)(dt.a,{isOpen:n,closeModal:s,withCloseButton:!0,heading:e("projectList.deleteProjectModal.heading"),text:[{type:"paragraph",content:e("projectList.deleteProjectModal.text")}],buttons:[Object(H.jsx)(T.a,{className:"btn--danger",buttonText:e("projectList.deleteProjectModal.delete"),onClickHandler:d},"delete"),Object(H.jsx)(T.a,{className:"btn--secondary",buttonText:e("projectList.deleteProjectModal.cancel"),onClickHandler:s},"close")],defaultCallback:d})},ht=(n(635),Object(s.d)(at||(at=a(["\n  fragment ProjectIndexPaginationFragment on ProjectConnection {\n    totalCount\n    pageInfo {\n      hasPreviousPage\n      startCursor\n      endCursor\n      hasNextPage\n    }\n  }\n"])))),jt=function(t){var e=Object(c.b)().t,n=t.paginationData,r=t.pageSize,o=t.fetchMore;if(0===(n.totalCount||0))return null;var a=n.pageInfo||{};return 0===Object.keys(a).length?null:Object(H.jsx)("div",{"data-testid":"projectIndexPagination",className:"editor-project-list-pagination",children:Object(H.jsx)("div",{className:"editor-project-pagination__buttons",children:a.hasNextPage?Object(H.jsx)(H.Fragment,{children:Object(H.jsx)(T.a,{className:"btn--primary",onClickHandler:function(){o({variables:{first:r,after:a.endCursor}})},title:e("projectList.pagination.more"),buttonText:e("projectList.pagination.more")})}):null})})},ft=n(0),vt=n(137),Ot=n(525);function mt(){return mt=Object.assign?Object.assign.bind():function(t){for(var e=1;e<arguments.length;e++){var n=arguments[e];for(var r in n)Object.prototype.hasOwnProperty.call(n,r)&&(t[r]=n[r])}return t},mt.apply(this,arguments)}var gt,yt=function(t){return d.createElement("svg",mt({width:20,height:17,viewBox:"0 0 20 17",fill:"none",xmlns:"http://www.w3.org/2000/svg"},t),it||(it=d.createElement("path",{d:"M0 7.125l7.5-7v3.25l-5.625 5 5.625 5v3.25l-7.5-7v-2.5zM20 9.75l-7.5 7v-3.375l5.75-5-5.75-5V0L20 7v2.75z"})))};function xt(){return xt=Object.assign?Object.assign.bind():function(t){for(var e=1;e<arguments.length;e++){var n=arguments[e];for(var r in n)Object.prototype.hasOwnProperty.call(n,r)&&(t[r]=n[r])}return t},xt.apply(this,arguments)}var _t,wt=function(t){return d.createElement("svg",xt({width:20,height:21,viewBox:"0 0 20 21",fill:"none",xmlns:"http://www.w3.org/2000/svg"},t),gt||(gt=d.createElement("path",{d:"M9.527 9.626H7.162c-1.656 0-2.72 1.065-2.72 2.72v2.129c0 .236-.119.355-.355.355H3.022c-1.064 0-1.892-.473-2.365-1.42-.355-.709-.591-1.419-.591-2.128C-.053 9.98-.053 8.68.42 7.379.775 6.315 1.486 5.487 2.669 5.25h6.859c.118 0 .355 0 .355-.118v-.591s-.237-.119-.355-.119H5.506c-.355 0-.473-.118-.473-.473V2.412c0-.828.355-1.42 1.064-1.656C6.69.52 7.28.283 7.871.165c1.42-.237 2.839-.237 4.258.118.591.118 1.182.355 1.655.71.474.473.828.946.71 1.655v4.258c0 1.656-.946 2.602-2.602 2.602-.828.118-1.655.118-2.365.118zM6.216 2.53c0 .473.354.946.946.946.473 0 .946-.473.946-.946s-.473-.828-.946-.946c-.592 0-.946.473-.946.946zm4.257 8.279h2.365c1.656 0 2.72-1.065 2.72-2.72v-2.13c0-.236.119-.354.355-.354h1.065c1.064 0 1.892.473 2.365 1.42.355.709.591 1.418.591 2.128.119 1.3.119 2.602-.355 3.903-.354 1.064-1.064 1.892-2.247 2.129h-6.859c-.118 0-.355 0-.355.118v.591s.237.118.355.118h4.021c.355 0 .473.119.473.473v1.538c0 .828-.355 1.42-1.064 1.656-.592.236-1.183.473-1.774.591-1.42.236-2.839.236-4.258-.118-.591-.119-1.182-.355-1.655-.71-.474-.473-.828-.946-.71-1.656V13.53c0-1.656.946-2.602 2.602-2.602.828-.118 1.655-.118 2.365-.118zm3.311 7.096c0-.473-.354-.947-.946-.947-.473 0-.946.474-.946.947s.473.828.946.946c.592 0 .947-.473.947-.946z"})))},Pt=function(){var t=Object(c.b)(),e=t.t,n=t.i18n,a=Object(i.d)(),s=Object(i.e)((function(t){return t.auth.user})),l=Object(i.e)((function(t){return t.editor.newProjectModalShowing})),u=function(){return a(Object(I.i)())},p=Object(d.useState)(e("newProjectModal.projectName.default")),b=Object(et.a)(p,2),h=b[0],j=b[1],f=Object(d.useState)(),v=Object(et.a)(f,2),O=v[0],m=v[1],g=Object(Q.q)(),y=function(){var t=Object(o.a)(Object(r.a)().mark((function t(){var e,o,a;return Object(r.a)().wrap((function(t){for(;;)switch(t.prev=t.next){case 0:return t.next=2,Object(vt.b)(Object(ft.a)(Object(ft.a)({},Ot.a[O]),{},{name:h}),s.access_token);case 2:e=t.sent,o=e.data.identifier,a=n.language,u(),g("/".concat(a,"/projects/").concat(o));case 7:case"end":return t.stop()}}),t)})));return function(){return t.apply(this,arguments)}}();return Object(H.jsx)(st.a,{isOpen:l,closeModal:u,withCloseButton:!0,heading:e("newProjectModal.heading"),inputs:[{type:"text",label:e("newProjectModal.projectName.inputLabel"),helpText:e("newProjectModal.projectName.helpText"),value:h,setValue:j},{type:"radio",label:e("newProjectModal.projectType.inputLabel"),value:O,setValue:m,options:[{value:"python",label:e("projectTypes.python"),Icon:wt},{value:"html",label:e("projectTypes.html"),Icon:yt}]}],defaultCallback:y,buttons:[Object(H.jsx)(T.a,{className:"btn--primary",buttonText:e("newProjectModal.createProject"),onClickHandler:y},"create"),Object(H.jsx)(T.a,{className:"btn--secondary",buttonText:e("newProjectModal.cancel"),onClickHandler:u},"close")]})},Ct=Object(s.d)(_t||(_t=a(["\n  query ProjectIndexQuery(\n    $userId: String\n    $first: Int\n    $last: Int\n    $before: String\n    $after: String\n  ) {\n    projects(\n      userId: $userId\n      first: $first\n      last: $last\n      before: $before\n      after: $after\n    ) {\n      ...ProjectListTableFragment\n      ...ProjectIndexPaginationFragment\n    }\n  }\n  ","\n  ","\n"])),K,ht);e.default=Object(i.b)((function(t){return{isLoading:t.auth.isLoadingUser,user:t.auth.user}}))((function(t){var e,n=t.isLoading,a=t.user,s=Object(c.b)().t;!function(t,e){var n=Object(Q.q)();Object(d.useEffect)((function(){t||t||e||n("/")}),[t,e])}(n,a);var l=Object(i.e)((function(t){return t.editor.newProjectModalShowing})),u=Object(i.e)((function(t){return t.editor.renameProjectModalShowing})),p=Object(i.e)((function(t){return t.editor.deleteProjectModalShowing})),b=Object(i.d)(),h=function(){var t=Object(o.a)(Object(r.a)().mark((function t(){return Object(r.a)().wrap((function(t){for(;;)switch(t.prev=t.next){case 0:b(Object(I.C)());case 1:case"end":return t.stop()}}),t)})));return function(){return t.apply(this,arguments)}}(),j=E(Ct,{fetchPolicy:"network-only",nextFetchPolicy:"cache-first",variables:{userId:null===a||void 0===a||null===(e=a.profile)||void 0===e?void 0:e.user,first:8},skip:void 0===a}),f=j.loading,v=j.error,O=j.data,m=j.fetchMore;return Object(H.jsxs)(H.Fragment,{children:[Object(H.jsx)(R,{children:Object(H.jsx)(T.a,{className:"btn--primary",onClickHandler:h,buttonText:s("projectList.newProject"),ButtonIcon:tt.a,buttonIconPosition:"right"})}),!f&&O?Object(H.jsxs)(H.Fragment,{children:[Object(H.jsx)(Z,{projectData:O.projects}),Object(H.jsx)(jt,{paginationData:O.projects,fetchMore:m,pageSize:8})]}):null,f?Object(H.jsx)("p",{children:s("projectList.loading")}):null,v?Object(H.jsx)("p",{children:s("projectList.loadingFailed")}):null,l?Object(H.jsx)(Pt,{}):null,u?Object(H.jsx)(ut,{}):null,p?Object(H.jsx)(bt,{}):null]})}))}}]);
//# sourceMappingURL=9.29092bba.chunk.js.map