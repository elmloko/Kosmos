"use strict";(globalThis.itsecWebpackJsonP=globalThis.itsecWebpackJsonP||[]).push([[1253],{61581:(e,t,n)=>{n.r(t),n.d(t,{CONTROL:()=>a,createMiddleware:()=>r,getStablePath:()=>o,invalidatePreload:()=>s,invalidatePreloadControl:()=>c}),n.p=window.itsecWebpackPublicPath;const i={};function o(e){const t=e.split("?"),n=t[1],i=t[0];return n?i+"?"+n.split("&").map((function(e){return e.split("=")})).sort((function(e,t){return e[0].localeCompare(t[0])})).map((function(e){return e.join("=")})).join("&"):i}function r(e){return Object.keys(e).reduce(((t,n)=>(t[o(n)]=e[n],t)),i),(e,t)=>{const{parse:n=!0}=e;if("string"==typeof e.path){const t=e.method||"GET",r=o(e.path);if(n&&"GET"===t&&i[r])return Promise.resolve(i[r].body);if("OPTIONS"===t&&i[t]&&i[t][r])return Promise.resolve(i[t][r])}return t(e)}}function s({path:e}){delete i[o(e)]}const a="INVALIDATE_API_FETCH_PRELOAD";function c(e){return{type:a,path:e}}}},e=>{var t=(61581,e(e.s=61581));((window.itsec=window.itsec||{}).packages=window.itsec.packages||{}).preload=t}]);