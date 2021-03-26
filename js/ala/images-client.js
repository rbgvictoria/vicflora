//# sourceMappingURL=images-client.js.map
!(function (f, g, c) {
    var e = f.L,
        b = { version: "0.7.3" };
    "object" == typeof module && "object" == typeof module.exports ? (module.exports = b) : "function" == typeof define && define.amd && define(b);
    b.noConflict = function () {
        return (f.L = e), this;
    };
    f.L = b;
    b.Util = {
        extend: function (a) {
            var d,
                b,
                c,
                e,
                k = Array.prototype.slice.call(arguments, 1);
            b = 0;
            for (c = k.length; c > b; b++) for (d in ((e = k[b] || {}), e)) e.hasOwnProperty(d) && (a[d] = e[d]);
            return a;
        },
        bind: function (a, d) {
            var b = 2 < arguments.length ? Array.prototype.slice.call(arguments, 2) : null;
            return function () {
                return a.apply(d, b || arguments);
            };
        },
        stamp: (function () {
            var a = 0;
            return function (d) {
                return (d._leaflet_id = d._leaflet_id || ++a), d._leaflet_id;
            };
        })(),
        invokeEach: function (a, d, b) {
            var c, e;
            if ("object" == typeof a) {
                e = Array.prototype.slice.call(arguments, 3);
                for (c in a) d.apply(b, [c, a[c]].concat(e));
                return !0;
            }
            return !1;
        },
        limitExecByInterval: function (a, d, b) {
            var c, e;
            return function l() {
                var f = arguments;
                return c
                    ? void (e = !0)
                    : ((c = !0),
                      setTimeout(function () {
                          c = !1;
                          e && (l.apply(b, f), (e = !1));
                      }, d),
                      void a.apply(b, f));
            };
        },
        falseFn: function () {
            return !1;
        },
        formatNum: function (a, d) {
            d = Math.pow(10, d || 5);
            return Math.round(a * d) / d;
        },
        trim: function (a) {
            return a.trim ? a.trim() : a.replace(/^\s+|\s+$/g, "");
        },
        splitWords: function (a) {
            return b.Util.trim(a).split(/\s+/);
        },
        setOptions: function (a, d) {
            return (a.options = b.extend({}, a.options, d)), a.options;
        },
        getParamString: function (a, d, b) {
            var c = [],
                e;
            for (e in a) c.push(encodeURIComponent(b ? e.toUpperCase() : e) + "=" + encodeURIComponent(a[e]));
            return (d && -1 !== d.indexOf("?") ? "&" : "?") + c.join("&");
        },
        template: function (a, d) {
            return a.replace(/\{ *([\w_]+) *\}/g, function (a, b) {
                b = d[b];
                if (b === c) throw Error("No value provided for variable " + a);
                return "function" == typeof b && (b = b(d)), b;
            });
        },
        isArray:
            Array.isArray ||
            function (a) {
                return "[object Array]" === Object.prototype.toString.call(a);
            },
        emptyImageUrl: "data:image/gif;base64,R0lGODlhAQABAAD/ACwAAAAAAQABAAACADs=",
    };
    (function () {
        function a(a) {
            var d,
                b,
                h = ["webkit", "moz", "o", "ms"];
            for (d = 0; d < h.length && !b; d++) b = f[h[d] + a];
            return b;
        }
        function d(a) {
            var d = +new Date(),
                b = Math.max(0, 16 - (d - h));
            return (h = d + b), f.setTimeout(a, b);
        }
        var h = 0,
            c = f.requestAnimationFrame || a("RequestAnimationFrame") || d,
            e =
                f.cancelAnimationFrame ||
                a("CancelAnimationFrame") ||
                a("CancelRequestAnimationFrame") ||
                function (a) {
                    f.clearTimeout(a);
                };
        b.Util.requestAnimFrame = function (a, h, e, n) {
            return (a = b.bind(a, h)), e && c === d ? void a() : c.call(f, a, n);
        };
        b.Util.cancelAnimFrame = function (a) {
            a && e.call(f, a);
        };
    })();
    b.extend = b.Util.extend;
    b.bind = b.Util.bind;
    b.stamp = b.Util.stamp;
    b.setOptions = b.Util.setOptions;
    b.Class = function () {};
    b.Class.extend = function (a) {
        var d = function () {
                this.initialize && this.initialize.apply(this, arguments);
                this._initHooks && this.callInitHooks();
            },
            h = function () {};
        h.prototype = this.prototype;
        var c = new h();
        c.constructor = d;
        d.prototype = c;
        for (var e in this) this.hasOwnProperty(e) && "prototype" !== e && (d[e] = this[e]);
        a.statics && (b.extend(d, a.statics), delete a.statics);
        a.includes && (b.Util.extend.apply(null, [c].concat(a.includes)), delete a.includes);
        a.options && c.options && (a.options = b.extend({}, c.options, a.options));
        b.extend(c, a);
        c._initHooks = [];
        var k = this;
        return (
            (d.__super__ = k.prototype),
            (c.callInitHooks = function () {
                if (!this._initHooksCalled) {
                    k.prototype.callInitHooks && k.prototype.callInitHooks.call(this);
                    this._initHooksCalled = !0;
                    for (var a = 0, d = c._initHooks.length; d > a; a++) c._initHooks[a].call(this);
                }
            }),
            d
        );
    };
    b.Class.include = function (a) {
        b.extend(this.prototype, a);
    };
    b.Class.mergeOptions = function (a) {
        b.extend(this.prototype.options, a);
    };
    b.Class.addInitHook = function (a) {
        var d = Array.prototype.slice.call(arguments, 1);
        this.prototype._initHooks = this.prototype._initHooks || [];
        this.prototype._initHooks.push(
            "function" == typeof a
                ? a
                : function () {
                      this[a].apply(this, d);
                  }
        );
    };
    b.Mixin = {};
    b.Mixin.Events = {
        addEventListener: function (a, d, h) {
            if (b.Util.invokeEach(a, this.addEventListener, this, d, h)) return this;
            var c,
                e,
                k,
                f,
                m,
                p,
                g,
                r = (this._leaflet_events = this._leaflet_events || {}),
                t = h && h !== this && b.stamp(h);
            a = b.Util.splitWords(a);
            c = 0;
            for (e = a.length; e > c; c++)
                (k = { action: d, context: h || this }), (f = a[c]), t ? ((m = f + "_idx"), (p = m + "_len"), (g = r[m] = r[m] || {}), g[t] || ((g[t] = []), (r[p] = (r[p] || 0) + 1)), g[t].push(k)) : ((r[f] = r[f] || []), r[f].push(k));
            return this;
        },
        hasEventListeners: function (a) {
            var d = this._leaflet_events;
            return !!d && ((a in d && 0 < d[a].length) || (a + "_idx" in d && 0 < d[a + "_idx_len"]));
        },
        removeEventListener: function (a, d, h) {
            if (!this._leaflet_events) return this;
            if (!a) return this.clearAllEventListeners();
            if (b.Util.invokeEach(a, this.removeEventListener, this, d, h)) return this;
            var c,
                e,
                k,
                f,
                m,
                p,
                g,
                r,
                t,
                q = this._leaflet_events,
                z = h && h !== this && b.stamp(h);
            a = b.Util.splitWords(a);
            c = 0;
            for (e = a.length; e > c; c++)
                if (((k = a[c]), (p = k + "_idx"), (g = p + "_len"), (r = q[p]), d)) {
                    if ((f = z && r ? r[z] : q[k])) {
                        for (m = f.length - 1; 0 <= m; m--) f[m].action !== d || (h && f[m].context !== h) || ((t = f.splice(m, 1)), (t[0].action = b.Util.falseFn));
                        h && r && 0 === f.length && (delete r[z], q[g]--);
                    }
                } else delete q[k], delete q[p], delete q[g];
            return this;
        },
        clearAllEventListeners: function () {
            return delete this._leaflet_events, this;
        },
        fireEvent: function (a, d) {
            if (!this.hasEventListeners(a)) return this;
            var h,
                c,
                e,
                f = b.Util.extend({}, d, { type: a, target: this }),
                l = this._leaflet_events;
            if (l[a]) for (d = l[a].slice(), h = 0, c = d.length; c > h; h++) d[h].action.call(d[h].context, f);
            a = l[a + "_idx"];
            for (e in a) if ((d = a[e].slice())) for (h = 0, c = d.length; c > h; h++) d[h].action.call(d[h].context, f);
            return this;
        },
        addOneTimeEventListener: function (a, d, h) {
            if (b.Util.invokeEach(a, this.addOneTimeEventListener, this, d, h)) return this;
            var c = b.bind(function () {
                this.removeEventListener(a, d, h).removeEventListener(a, c, h);
            }, this);
            return this.addEventListener(a, d, h).addEventListener(a, c, h);
        },
    };
    b.Mixin.Events.on = b.Mixin.Events.addEventListener;
    b.Mixin.Events.off = b.Mixin.Events.removeEventListener;
    b.Mixin.Events.once = b.Mixin.Events.addOneTimeEventListener;
    b.Mixin.Events.fire = b.Mixin.Events.fireEvent;
    (function () {
        var a = "ActiveXObject" in f,
            d = a && !g.addEventListener,
            h = navigator.userAgent.toLowerCase(),
            u = -1 !== h.indexOf("webkit"),
            e = -1 !== h.indexOf("chrome"),
            k = -1 !== h.indexOf("phantom"),
            l = -1 !== h.indexOf("android"),
            m = -1 !== h.search("android [23]"),
            h = -1 !== h.indexOf("gecko"),
            p = typeof orientation != c + "",
            r = f.navigator && f.navigator.msPointerEnabled && f.navigator.msMaxTouchPoints && !f.PointerEvent,
            x = (f.PointerEvent && f.navigator.pointerEnabled && f.navigator.maxTouchPoints) || r,
            t = ("devicePixelRatio" in f && 1 < f.devicePixelRatio) || ("matchMedia" in f && f.matchMedia("(min-resolution:144dpi)") && f.matchMedia("(min-resolution:144dpi)").matches),
            q = g.documentElement,
            z = a && "transition" in q.style,
            A = "WebKitCSSMatrix" in f && "m11" in new f.WebKitCSSMatrix() && !m,
            y = "MozPerspective" in q.style,
            F = "OTransition" in q.style,
            H = !f.L_DISABLE_3D && (z || A || y || F) && !k;
        if ((k = !f.L_NO_TOUCH && !k))
            x || "ontouchstart" in q
                ? (k = !0)
                : ((k = g.createElement("div")), (q = !1), (k = k.setAttribute ? (k.setAttribute("ontouchstart", "return;"), "function" == typeof k.ontouchstart && (q = !0), k.removeAttribute("ontouchstart"), q) : !1));
        b.Browser = {
            ie: a,
            ielt9: d,
            webkit: u,
            gecko: h && !u && !f.opera && !a,
            android: l,
            android23: m,
            chrome: e,
            ie3d: z,
            webkit3d: A,
            gecko3d: y,
            opera3d: F,
            any3d: H,
            mobile: p,
            mobileWebkit: p && u,
            mobileWebkit3d: p && A,
            mobileOpera: p && f.opera,
            touch: k,
            msPointer: r,
            pointer: x,
            retina: t,
        };
    })();
    b.Point = function (a, d, b) {
        this.x = b ? Math.round(a) : a;
        this.y = b ? Math.round(d) : d;
    };
    b.Point.prototype = {
        clone: function () {
            return new b.Point(this.x, this.y);
        },
        add: function (a) {
            return this.clone()._add(b.point(a));
        },
        _add: function (a) {
            return (this.x += a.x), (this.y += a.y), this;
        },
        subtract: function (a) {
            return this.clone()._subtract(b.point(a));
        },
        _subtract: function (a) {
            return (this.x -= a.x), (this.y -= a.y), this;
        },
        divideBy: function (a) {
            return this.clone()._divideBy(a);
        },
        _divideBy: function (a) {
            return (this.x /= a), (this.y /= a), this;
        },
        multiplyBy: function (a) {
            return this.clone()._multiplyBy(a);
        },
        _multiplyBy: function (a) {
            return (this.x *= a), (this.y *= a), this;
        },
        round: function () {
            return this.clone()._round();
        },
        _round: function () {
            return (this.x = Math.round(this.x)), (this.y = Math.round(this.y)), this;
        },
        floor: function () {
            return this.clone()._floor();
        },
        _floor: function () {
            return (this.x = Math.floor(this.x)), (this.y = Math.floor(this.y)), this;
        },
        distanceTo: function (a) {
            a = b.point(a);
            var d = a.x - this.x;
            a = a.y - this.y;
            return Math.sqrt(d * d + a * a);
        },
        equals: function (a) {
            return (a = b.point(a)), a.x === this.x && a.y === this.y;
        },
        contains: function (a) {
            return (a = b.point(a)), Math.abs(a.x) <= Math.abs(this.x) && Math.abs(a.y) <= Math.abs(this.y);
        },
        toString: function () {
            return "Point(" + b.Util.formatNum(this.x) + ", " + b.Util.formatNum(this.y) + ")";
        },
    };
    b.point = function (a, d, h) {
        return a instanceof b.Point ? a : b.Util.isArray(a) ? new b.Point(a[0], a[1]) : a === c || null === a ? a : new b.Point(a, d, h);
    };
    b.Bounds = function (a, d) {
        if (a) {
            a = d ? [a, d] : a;
            d = 0;
            for (var b = a.length; b > d; d++) this.extend(a[d]);
        }
    };
    b.Bounds.prototype = {
        extend: function (a) {
            return (
                (a = b.point(a)),
                this.min || this.max
                    ? ((this.min.x = Math.min(a.x, this.min.x)), (this.max.x = Math.max(a.x, this.max.x)), (this.min.y = Math.min(a.y, this.min.y)), (this.max.y = Math.max(a.y, this.max.y)))
                    : ((this.min = a.clone()), (this.max = a.clone())),
                this
            );
        },
        getCenter: function (a) {
            return new b.Point((this.min.x + this.max.x) / 2, (this.min.y + this.max.y) / 2, a);
        },
        getBottomLeft: function () {
            return new b.Point(this.min.x, this.max.y);
        },
        getTopRight: function () {
            return new b.Point(this.max.x, this.min.y);
        },
        getSize: function () {
            return this.max.subtract(this.min);
        },
        contains: function (a) {
            var d, h;
            return (
                (a = "number" == typeof a[0] || a instanceof b.Point ? b.point(a) : b.bounds(a)),
                a instanceof b.Bounds ? ((d = a.min), (h = a.max)) : (d = h = a),
                d.x >= this.min.x && h.x <= this.max.x && d.y >= this.min.y && h.y <= this.max.y
            );
        },
        intersects: function (a) {
            a = b.bounds(a);
            var d = this.min,
                h = this.max,
                c = a.min;
            a = a.max;
            var e = a.y >= d.y && c.y <= h.y;
            return a.x >= d.x && c.x <= h.x && e;
        },
        isValid: function () {
            return !(!this.min || !this.max);
        },
    };
    b.bounds = function (a, d) {
        return !a || a instanceof b.Bounds ? a : new b.Bounds(a, d);
    };
    b.Transformation = function (a, d, b, c) {
        this._a = a;
        this._b = d;
        this._c = b;
        this._d = c;
    };
    b.Transformation.prototype = {
        transform: function (a, d) {
            return this._transform(a.clone(), d);
        },
        _transform: function (a, d) {
            return (d = d || 1), (a.x = d * (this._a * a.x + this._b)), (a.y = d * (this._c * a.y + this._d)), a;
        },
        untransform: function (a, d) {
            return (d = d || 1), new b.Point((a.x / d - this._b) / this._a, (a.y / d - this._d) / this._c);
        },
    };
    b.DomUtil = {
        get: function (a) {
            return "string" == typeof a ? g.getElementById(a) : a;
        },
        getStyle: function (a, d) {
            var b = a.style[d];
            (!b && a.currentStyle && (b = a.currentStyle[d]), (b && "auto" !== b) || !g.defaultView) || (b = (a = g.defaultView.getComputedStyle(a, null)) ? a[d] : null);
            return "auto" === b ? null : b;
        },
        getViewportOffset: function (a) {
            var d,
                h = 0,
                c = 0,
                e = a,
                f = g.body,
                l = g.documentElement;
            do {
                if (
                    ((h += e.offsetTop || 0),
                    (c += e.offsetLeft || 0),
                    (h += parseInt(b.DomUtil.getStyle(e, "borderTopWidth"), 10) || 0),
                    (c += parseInt(b.DomUtil.getStyle(e, "borderLeftWidth"), 10) || 0),
                    (d = b.DomUtil.getStyle(e, "position")),
                    e.offsetParent === f && "absolute" === d)
                )
                    break;
                if ("fixed" === d) {
                    h += f.scrollTop || l.scrollTop || 0;
                    c += f.scrollLeft || l.scrollLeft || 0;
                    break;
                }
                if ("relative" === d && !e.offsetLeft) {
                    d = b.DomUtil.getStyle(e, "width");
                    var m = b.DomUtil.getStyle(e, "max-width"),
                        p = e.getBoundingClientRect();
                    ("none" === d && "none" === m) || (c += p.left + e.clientLeft);
                    h += p.top + (f.scrollTop || l.scrollTop || 0);
                    break;
                }
                e = e.offsetParent;
            } while (e);
            e = a;
            do {
                if (e === f) break;
                h -= e.scrollTop || 0;
                c -= e.scrollLeft || 0;
                e = e.parentNode;
            } while (e);
            return new b.Point(c, h);
        },
        documentIsLtr: function () {
            return b.DomUtil._docIsLtrCached || ((b.DomUtil._docIsLtrCached = !0), (b.DomUtil._docIsLtr = "ltr" === b.DomUtil.getStyle(g.body, "direction"))), b.DomUtil._docIsLtr;
        },
        create: function (a, d, b) {
            a = g.createElement(a);
            return (a.className = d), b && b.appendChild(a), a;
        },
        hasClass: function (a, d) {
            if (a.classList !== c) return a.classList.contains(d);
            a = b.DomUtil._getClass(a);
            return 0 < a.length && new RegExp("(^|\\s)" + d + "(\\s|$)").test(a);
        },
        addClass: function (a, d) {
            if (a.classList !== c) {
                d = b.Util.splitWords(d);
                for (var h = 0, u = d.length; u > h; h++) a.classList.add(d[h]);
            } else b.DomUtil.hasClass(a, d) || ((h = b.DomUtil._getClass(a)), b.DomUtil._setClass(a, (h ? h + " " : "") + d));
        },
        removeClass: function (a, d) {
            a.classList !== c ? a.classList.remove(d) : b.DomUtil._setClass(a, b.Util.trim((" " + b.DomUtil._getClass(a) + " ").replace(" " + d + " ", " ")));
        },
        _setClass: function (a, d) {
            a.className.baseVal === c ? (a.className = d) : (a.className.baseVal = d);
        },
        _getClass: function (a) {
            return a.className.baseVal === c ? a.className : a.className.baseVal;
        },
        setOpacity: function (a, d) {
            if ("opacity" in a.style) a.style.opacity = d;
            else if ("filter" in a.style) {
                var b = !1;
                try {
                    b = a.filters.item("DXImageTransform.Microsoft.Alpha");
                } catch (c) {
                    if (1 === d) return;
                }
                d = Math.round(100 * d);
                b ? ((b.Enabled = 100 !== d), (b.Opacity = d)) : (a.style.filter += " progid:DXImageTransform.Microsoft.Alpha(opacity=" + d + ")");
            }
        },
        testProp: function (a) {
            for (var d = g.documentElement.style, b = 0; b < a.length; b++) if (a[b] in d) return a[b];
            return !1;
        },
        getTranslateString: function (a) {
            var d = b.Browser.webkit3d;
            return "translate" + (d ? "3d" : "") + "(" + a.x + "px," + a.y + "px" + ((d ? ",0" : "") + ")");
        },
        getScaleString: function (a, d) {
            return b.DomUtil.getTranslateString(d.add(d.multiplyBy(-1 * a))) + (" scale(" + a + ") ");
        },
        setPosition: function (a, d, h) {
            a._leaflet_pos = d;
            !h && b.Browser.any3d ? (a.style[b.DomUtil.TRANSFORM] = b.DomUtil.getTranslateString(d)) : ((a.style.left = d.x + "px"), (a.style.top = d.y + "px"));
        },
        getPosition: function (a) {
            return a._leaflet_pos;
        },
    };
    b.DomUtil.TRANSFORM = b.DomUtil.testProp(["transform", "WebkitTransform", "OTransform", "MozTransform", "msTransform"]);
    b.DomUtil.TRANSITION = b.DomUtil.testProp(["webkitTransition", "transition", "OTransition", "MozTransition", "msTransition"]);
    b.DomUtil.TRANSITION_END = "webkitTransition" === b.DomUtil.TRANSITION || "OTransition" === b.DomUtil.TRANSITION ? b.DomUtil.TRANSITION + "End" : "transitionend";
    (function () {
        if ("onselectstart" in g)
            b.extend(b.DomUtil, {
                disableTextSelection: function () {
                    b.DomEvent.on(f, "selectstart", b.DomEvent.preventDefault);
                },
                enableTextSelection: function () {
                    b.DomEvent.off(f, "selectstart", b.DomEvent.preventDefault);
                },
            });
        else {
            var a = b.DomUtil.testProp(["userSelect", "WebkitUserSelect", "OUserSelect", "MozUserSelect", "msUserSelect"]);
            b.extend(b.DomUtil, {
                disableTextSelection: function () {
                    if (a) {
                        var d = g.documentElement.style;
                        this._userSelect = d[a];
                        d[a] = "none";
                    }
                },
                enableTextSelection: function () {
                    a && ((g.documentElement.style[a] = this._userSelect), delete this._userSelect);
                },
            });
        }
        b.extend(b.DomUtil, {
            disableImageDrag: function () {
                b.DomEvent.on(f, "dragstart", b.DomEvent.preventDefault);
            },
            enableImageDrag: function () {
                b.DomEvent.off(f, "dragstart", b.DomEvent.preventDefault);
            },
        });
    })();
    b.LatLng = function (a, d, b) {
        if (((a = parseFloat(a)), (d = parseFloat(d)), isNaN(a) || isNaN(d))) throw Error("Invalid LatLng object: (" + a + ", " + d + ")");
        this.lat = a;
        this.lng = d;
        b !== c && (this.alt = parseFloat(b));
    };
    b.extend(b.LatLng, { DEG_TO_RAD: Math.PI / 180, RAD_TO_DEG: 180 / Math.PI, MAX_MARGIN: 1e-9 });
    b.LatLng.prototype = {
        equals: function (a) {
            if (!a) return !1;
            a = b.latLng(a);
            return Math.max(Math.abs(this.lat - a.lat), Math.abs(this.lng - a.lng)) <= b.LatLng.MAX_MARGIN;
        },
        toString: function (a) {
            return "LatLng(" + b.Util.formatNum(this.lat, a) + ", " + b.Util.formatNum(this.lng, a) + ")";
        },
        distanceTo: function (a) {
            a = b.latLng(a);
            var d = b.LatLng.DEG_TO_RAD,
                h = Math.sin(((a.lat - this.lat) * d) / 2),
                c = Math.sin(((a.lng - this.lng) * d) / 2);
            a = h * h + c * c * Math.cos(this.lat * d) * Math.cos(a.lat * d);
            return 12756274 * Math.atan2(Math.sqrt(a), Math.sqrt(1 - a));
        },
        wrap: function (a, d) {
            var h = this.lng;
            return (a = a || -180), (d = d || 180), (h = ((h + d) % (d - a)) + (a > h || h === d ? d : a)), new b.LatLng(this.lat, h);
        },
    };
    b.latLng = function (a, d) {
        return a instanceof b.LatLng
            ? a
            : b.Util.isArray(a)
            ? "number" == typeof a[0] || "string" == typeof a[0]
                ? new b.LatLng(a[0], a[1], a[2])
                : null
            : a === c || null === a
            ? a
            : "object" == typeof a && "lat" in a
            ? new b.LatLng(a.lat, "lng" in a ? a.lng : a.lon)
            : d === c
            ? null
            : new b.LatLng(a, d);
    };
    b.LatLngBounds = function (a, d) {
        if (a) {
            a = d ? [a, d] : a;
            d = 0;
            for (var b = a.length; b > d; d++) this.extend(a[d]);
        }
    };
    b.LatLngBounds.prototype = {
        extend: function (a) {
            if (!a) return this;
            var d = b.latLng(a);
            return (
                (a = null !== d ? d : b.latLngBounds(a)),
                a instanceof b.LatLng
                    ? this._southWest || this._northEast
                        ? ((this._southWest.lat = Math.min(a.lat, this._southWest.lat)),
                          (this._southWest.lng = Math.min(a.lng, this._southWest.lng)),
                          (this._northEast.lat = Math.max(a.lat, this._northEast.lat)),
                          (this._northEast.lng = Math.max(a.lng, this._northEast.lng)))
                        : ((this._southWest = new b.LatLng(a.lat, a.lng)), (this._northEast = new b.LatLng(a.lat, a.lng)))
                    : a instanceof b.LatLngBounds && (this.extend(a._southWest), this.extend(a._northEast)),
                this
            );
        },
        pad: function (a) {
            var d = this._southWest,
                h = this._northEast,
                c = Math.abs(d.lat - h.lat) * a;
            a *= Math.abs(d.lng - h.lng);
            return new b.LatLngBounds(new b.LatLng(d.lat - c, d.lng - a), new b.LatLng(h.lat + c, h.lng + a));
        },
        getCenter: function () {
            return new b.LatLng((this._southWest.lat + this._northEast.lat) / 2, (this._southWest.lng + this._northEast.lng) / 2);
        },
        getSouthWest: function () {
            return this._southWest;
        },
        getNorthEast: function () {
            return this._northEast;
        },
        getNorthWest: function () {
            return new b.LatLng(this.getNorth(), this.getWest());
        },
        getSouthEast: function () {
            return new b.LatLng(this.getSouth(), this.getEast());
        },
        getWest: function () {
            return this._southWest.lng;
        },
        getSouth: function () {
            return this._southWest.lat;
        },
        getEast: function () {
            return this._northEast.lng;
        },
        getNorth: function () {
            return this._northEast.lat;
        },
        contains: function (a) {
            a = "number" == typeof a[0] || a instanceof b.LatLng ? b.latLng(a) : b.latLngBounds(a);
            var d,
                h,
                c = this._southWest,
                e = this._northEast;
            return a instanceof b.LatLngBounds ? ((d = a.getSouthWest()), (h = a.getNorthEast())) : (d = h = a), d.lat >= c.lat && h.lat <= e.lat && d.lng >= c.lng && h.lng <= e.lng;
        },
        intersects: function (a) {
            a = b.latLngBounds(a);
            var d = this._southWest,
                h = this._northEast,
                c = a.getSouthWest();
            a = a.getNorthEast();
            var e = a.lng >= d.lng && c.lng <= h.lng;
            return a.lat >= d.lat && c.lat <= h.lat && e;
        },
        toBBoxString: function () {
            return [this.getWest(), this.getSouth(), this.getEast(), this.getNorth()].join();
        },
        equals: function (a) {
            return a ? ((a = b.latLngBounds(a)), this._southWest.equals(a.getSouthWest()) && this._northEast.equals(a.getNorthEast())) : !1;
        },
        isValid: function () {
            return !(!this._southWest || !this._northEast);
        },
    };
    b.latLngBounds = function (a, d) {
        return !a || a instanceof b.LatLngBounds ? a : new b.LatLngBounds(a, d);
    };
    b.Projection = {};
    b.Projection.SphericalMercator = {
        MAX_LATITUDE: 85.0511287798,
        project: function (a) {
            var d = b.LatLng.DEG_TO_RAD,
                h = this.MAX_LATITUDE,
                c = a.lng * d;
            a = Math.max(Math.min(h, a.lat), -h) * d;
            return (a = Math.log(Math.tan(Math.PI / 4 + a / 2))), new b.Point(c, a);
        },
        unproject: function (a) {
            var d = b.LatLng.RAD_TO_DEG;
            return new b.LatLng((2 * Math.atan(Math.exp(a.y)) - Math.PI / 2) * d, a.x * d);
        },
    };
    b.Projection.LonLat = {
        project: function (a) {
            return new b.Point(a.lng, a.lat);
        },
        unproject: function (a) {
            return new b.LatLng(a.y, a.x);
        },
    };
    b.CRS = {
        latLngToPoint: function (a, d) {
            a = this.projection.project(a);
            d = this.scale(d);
            return this.transformation._transform(a, d);
        },
        pointToLatLng: function (a, d) {
            d = this.scale(d);
            a = this.transformation.untransform(a, d);
            return this.projection.unproject(a);
        },
        project: function (a) {
            return this.projection.project(a);
        },
        scale: function (a) {
            return 256 * Math.pow(2, a);
        },
        getSize: function (a) {
            a = this.scale(a);
            return b.point(a, a);
        },
    };
    b.CRS.Simple = b.extend({}, b.CRS, {
        projection: b.Projection.LonLat,
        transformation: new b.Transformation(1, 0, -1, 0),
        scale: function (a) {
            return Math.pow(2, a);
        },
    });
    b.CRS.EPSG3857 = b.extend({}, b.CRS, {
        code: "EPSG:3857",
        projection: b.Projection.SphericalMercator,
        transformation: new b.Transformation(0.5 / Math.PI, 0.5, -0.5 / Math.PI, 0.5),
        project: function (a) {
            return this.projection.project(a).multiplyBy(6378137);
        },
    });
    b.CRS.EPSG900913 = b.extend({}, b.CRS.EPSG3857, { code: "EPSG:900913" });
    b.CRS.EPSG4326 = b.extend({}, b.CRS, { code: "EPSG:4326", projection: b.Projection.LonLat, transformation: new b.Transformation(1 / 360, 0.5, -1 / 360, 0.5) });
    b.Map = b.Class.extend({
        includes: b.Mixin.Events,
        options: { crs: b.CRS.EPSG3857, fadeAnimation: b.DomUtil.TRANSITION && !b.Browser.android23, trackResize: !0, markerZoomAnimation: b.DomUtil.TRANSITION && b.Browser.any3d },
        initialize: function (a, d) {
            d = b.setOptions(this, d);
            this._initContainer(a);
            this._initLayout();
            this._onResize = b.bind(this._onResize, this);
            this._initEvents();
            d.maxBounds && this.setMaxBounds(d.maxBounds);
            d.center && d.zoom !== c && this.setView(b.latLng(d.center), d.zoom, { reset: !0 });
            this._handlers = [];
            this._layers = {};
            this._zoomBoundLayers = {};
            this._tileLayersNum = 0;
            this.callInitHooks();
            this._addLayers(d.layers);
        },
        setView: function (a, d) {
            return (d = d === c ? this.getZoom() : d), this._resetView(b.latLng(a), this._limitZoom(d)), this;
        },
        setZoom: function (a, d) {
            return this._loaded ? this.setView(this.getCenter(), a, { zoom: d }) : ((this._zoom = this._limitZoom(a)), this);
        },
        zoomIn: function (a, d) {
            return this.setZoom(this._zoom + (a || 1), d);
        },
        zoomOut: function (a, d) {
            return this.setZoom(this._zoom - (a || 1), d);
        },
        setZoomAround: function (a, d, h) {
            var c = this.getZoomScale(d),
                e = this.getSize().divideBy(2);
            a = (a instanceof b.Point ? a : this.latLngToContainerPoint(a)).subtract(e).multiplyBy(1 - 1 / c);
            e = this.containerPointToLatLng(e.add(a));
            return this.setView(e, d, { zoom: h });
        },
        fitBounds: function (a, d) {
            d = d || {};
            a = a.getBounds ? a.getBounds() : b.latLngBounds(a);
            var h = b.point(d.paddingTopLeft || d.padding || [0, 0]),
                c = b.point(d.paddingBottomRight || d.padding || [0, 0]),
                e = this.getBoundsZoom(a, !1, h.add(c)),
                h = c.subtract(h).divideBy(2),
                c = this.project(a.getSouthWest(), e);
            a = this.project(a.getNorthEast(), e);
            a = this.unproject(c.add(a).divideBy(2).add(h), e);
            return (e = d && d.maxZoom ? Math.min(d.maxZoom, e) : e), this.setView(a, e, d);
        },
        fitWorld: function (a) {
            return this.fitBounds(
                [
                    [-90, -180],
                    [90, 180],
                ],
                a
            );
        },
        panTo: function (a, d) {
            return this.setView(a, this._zoom, { pan: d });
        },
        panBy: function (a) {
            return this.fire("movestart"), this._rawPanBy(b.point(a)), this.fire("move"), this.fire("moveend");
        },
        setMaxBounds: function (a) {
            return (a = b.latLngBounds(a)), (this.options.maxBounds = a), a ? (this._loaded && this._panInsideMaxBounds(), this.on("moveend", this._panInsideMaxBounds, this)) : this.off("moveend", this._panInsideMaxBounds, this);
        },
        panInsideBounds: function (a, d) {
            var b = this.getCenter();
            a = this._limitCenter(b, this._zoom, a);
            return b.equals(a) ? this : this.panTo(a, d);
        },
        addLayer: function (a) {
            var d = b.stamp(a);
            return this._layers[d]
                ? this
                : ((this._layers[d] = a),
                  !a.options || (isNaN(a.options.maxZoom) && isNaN(a.options.minZoom)) || ((this._zoomBoundLayers[d] = a), this._updateZoomLevels()),
                  this.options.zoomAnimation && b.TileLayer && a instanceof b.TileLayer && (this._tileLayersNum++, this._tileLayersToLoad++, a.on("load", this._onTileLayerLoad, this)),
                  this._loaded && this._layerAdd(a),
                  this);
        },
        removeLayer: function (a) {
            var d = b.stamp(a);
            return this._layers[d]
                ? (this._loaded && a.onRemove(this),
                  delete this._layers[d],
                  this._loaded && this.fire("layerremove", { layer: a }),
                  this._zoomBoundLayers[d] && (delete this._zoomBoundLayers[d], this._updateZoomLevels()),
                  this.options.zoomAnimation && b.TileLayer && a instanceof b.TileLayer && (this._tileLayersNum--, this._tileLayersToLoad--, a.off("load", this._onTileLayerLoad, this)),
                  this)
                : this;
        },
        hasLayer: function (a) {
            return a ? b.stamp(a) in this._layers : !1;
        },
        eachLayer: function (a, d) {
            for (var b in this._layers) a.call(d, this._layers[b]);
            return this;
        },
        invalidateSize: function (a) {
            if (!this._loaded) return this;
            a = b.extend({ animate: !1, pan: !0 }, !0 === a ? { animate: !0 } : a);
            var d = this.getSize();
            this._sizeChanged = !0;
            this._initialCenter = null;
            var c = this.getSize(),
                e = d.divideBy(2).round(),
                n = c.divideBy(2).round(),
                e = e.subtract(n);
            return e.x || e.y
                ? (a.animate && a.pan
                      ? this.panBy(e)
                      : (a.pan && this._rawPanBy(e), this.fire("move"), a.debounceMoveend ? (clearTimeout(this._sizeTimer), (this._sizeTimer = setTimeout(b.bind(this.fire, this, "moveend"), 200))) : this.fire("moveend")),
                  this.fire("resize", { oldSize: d, newSize: c }))
                : this;
        },
        addHandler: function (a, d) {
            if (!d) return this;
            d = this[a] = new d(this);
            return this._handlers.push(d), this.options[a] && d.enable(), this;
        },
        remove: function () {
            this._loaded && this.fire("unload");
            this._initEvents("off");
            try {
                delete this._container._leaflet;
            } catch (a) {
                this._container._leaflet = c;
            }
            return this._clearPanes(), this._clearControlPos && this._clearControlPos(), this._clearHandlers(), this;
        },
        getCenter: function () {
            return this._checkIfLoaded(), this._initialCenter && !this._moved() ? this._initialCenter : this.layerPointToLatLng(this._getCenterLayerPoint());
        },
        getZoom: function () {
            return this._zoom;
        },
        getBounds: function () {
            var a = this.getPixelBounds(),
                d = this.unproject(a.getBottomLeft()),
                a = this.unproject(a.getTopRight());
            return new b.LatLngBounds(d, a);
        },
        getMinZoom: function () {
            return this.options.minZoom === c ? (this._layersMinZoom === c ? 0 : this._layersMinZoom) : this.options.minZoom;
        },
        getMaxZoom: function () {
            return this.options.maxZoom === c ? (this._layersMaxZoom === c ? 1 / 0 : this._layersMaxZoom) : this.options.maxZoom;
        },
        getBoundsZoom: function (a, d, c) {
            a = b.latLngBounds(a);
            var e,
                n = this.getMinZoom() - (d ? 1 : 0),
                f = this.getMaxZoom(),
                l = this.getSize(),
                m = a.getNorthWest();
            a = a.getSouthEast();
            c = b.point(c || [0, 0]);
            do n++, (e = this.project(a, n).subtract(this.project(m, n)).add(c)), (e = d ? e.x < l.x || e.y < l.y : l.contains(e));
            while (e && f >= n);
            return e && d ? null : d ? n : n - 1;
        },
        getSize: function () {
            return (!this._size || this._sizeChanged) && ((this._size = new b.Point(this._container.clientWidth, this._container.clientHeight)), (this._sizeChanged = !1)), this._size.clone();
        },
        getPixelBounds: function () {
            var a = this._getTopLeftPoint();
            return new b.Bounds(a, a.add(this.getSize()));
        },
        getPixelOrigin: function () {
            return this._checkIfLoaded(), this._initialTopLeftPoint;
        },
        getPanes: function () {
            return this._panes;
        },
        getContainer: function () {
            return this._container;
        },
        getZoomScale: function (a) {
            var d = this.options.crs;
            return d.scale(a) / d.scale(this._zoom);
        },
        getScaleZoom: function (a) {
            return this._zoom + Math.log(a) / Math.LN2;
        },
        project: function (a, d) {
            return (d = d === c ? this._zoom : d), this.options.crs.latLngToPoint(b.latLng(a), d);
        },
        unproject: function (a, d) {
            return (d = d === c ? this._zoom : d), this.options.crs.pointToLatLng(b.point(a), d);
        },
        layerPointToLatLng: function (a) {
            a = b.point(a).add(this.getPixelOrigin());
            return this.unproject(a);
        },
        latLngToLayerPoint: function (a) {
            return this.project(b.latLng(a))._round()._subtract(this.getPixelOrigin());
        },
        containerPointToLayerPoint: function (a) {
            return b.point(a).subtract(this._getMapPanePos());
        },
        layerPointToContainerPoint: function (a) {
            return b.point(a).add(this._getMapPanePos());
        },
        containerPointToLatLng: function (a) {
            a = this.containerPointToLayerPoint(b.point(a));
            return this.layerPointToLatLng(a);
        },
        latLngToContainerPoint: function (a) {
            return this.layerPointToContainerPoint(this.latLngToLayerPoint(b.latLng(a)));
        },
        mouseEventToContainerPoint: function (a) {
            return b.DomEvent.getMousePosition(a, this._container);
        },
        mouseEventToLayerPoint: function (a) {
            return this.containerPointToLayerPoint(this.mouseEventToContainerPoint(a));
        },
        mouseEventToLatLng: function (a) {
            return this.layerPointToLatLng(this.mouseEventToLayerPoint(a));
        },
        _initContainer: function (a) {
            a = this._container = b.DomUtil.get(a);
            if (!a) throw Error("Map container not found.");
            if (a._leaflet) throw Error("Map container is already initialized.");
            a._leaflet = !0;
        },
        _initLayout: function () {
            var a = this._container;
            b.DomUtil.addClass(
                a,
                "leaflet-container" + (b.Browser.touch ? " leaflet-touch" : "") + (b.Browser.retina ? " leaflet-retina" : "") + (b.Browser.ielt9 ? " leaflet-oldie" : "") + (this.options.fadeAnimation ? " leaflet-fade-anim" : "")
            );
            var d = b.DomUtil.getStyle(a, "position");
            "absolute" !== d && "relative" !== d && "fixed" !== d && (a.style.position = "relative");
            this._initPanes();
            this._initControlPos && this._initControlPos();
        },
        _initPanes: function () {
            var a = (this._panes = {});
            this._mapPane = a.mapPane = this._createPane("leaflet-map-pane", this._container);
            this._tilePane = a.tilePane = this._createPane("leaflet-tile-pane", this._mapPane);
            a.objectsPane = this._createPane("leaflet-objects-pane", this._mapPane);
            a.shadowPane = this._createPane("leaflet-shadow-pane");
            a.overlayPane = this._createPane("leaflet-overlay-pane");
            a.markerPane = this._createPane("leaflet-marker-pane");
            a.popupPane = this._createPane("leaflet-popup-pane");
            this.options.markerZoomAnimation || (b.DomUtil.addClass(a.markerPane, " leaflet-zoom-hide"), b.DomUtil.addClass(a.shadowPane, " leaflet-zoom-hide"), b.DomUtil.addClass(a.popupPane, " leaflet-zoom-hide"));
        },
        _createPane: function (a, d) {
            return b.DomUtil.create("div", a, d || this._panes.objectsPane);
        },
        _clearPanes: function () {
            this._container.removeChild(this._mapPane);
        },
        _addLayers: function (a) {
            a = a ? (b.Util.isArray(a) ? a : [a]) : [];
            for (var d = 0, c = a.length; c > d; d++) this.addLayer(a[d]);
        },
        _resetView: function (a, d, c, e) {
            var n = this._zoom !== d;
            e || (this.fire("movestart"), n && this.fire("zoomstart"));
            this._zoom = d;
            this._initialCenter = a;
            this._initialTopLeftPoint = this._getNewTopLeftPoint(a);
            c ? this._initialTopLeftPoint._add(this._getMapPanePos()) : b.DomUtil.setPosition(this._mapPane, new b.Point(0, 0));
            this._tileLayersToLoad = this._tileLayersNum;
            a = !this._loaded;
            this._loaded = !0;
            this.fire("viewreset", { hard: !c });
            a && (this.fire("load"), this.eachLayer(this._layerAdd, this));
            this.fire("move");
            (n || e) && this.fire("zoomend");
            this.fire("moveend", { hard: !c });
        },
        _rawPanBy: function (a) {
            b.DomUtil.setPosition(this._mapPane, this._getMapPanePos().subtract(a));
        },
        _getZoomSpan: function () {
            return this.getMaxZoom() - this.getMinZoom();
        },
        _updateZoomLevels: function () {
            var a,
                d = 1 / 0,
                b = -1 / 0,
                e = this._getZoomSpan();
            for (a in this._zoomBoundLayers) {
                var n = this._zoomBoundLayers[a];
                isNaN(n.options.minZoom) || (d = Math.min(d, n.options.minZoom));
                isNaN(n.options.maxZoom) || (b = Math.max(b, n.options.maxZoom));
            }
            a === c ? (this._layersMaxZoom = this._layersMinZoom = c) : ((this._layersMaxZoom = b), (this._layersMinZoom = d));
            e !== this._getZoomSpan() && this.fire("zoomlevelschange");
        },
        _panInsideMaxBounds: function () {
            this.panInsideBounds(this.options.maxBounds);
        },
        _checkIfLoaded: function () {
            if (!this._loaded) throw Error("Set map center and zoom first.");
        },
        _initEvents: function (a) {
            if (b.DomEvent) {
                a = a || "on";
                b.DomEvent[a](this._container, "click", this._onMouseClick, this);
                var d,
                    c,
                    e = "dblclick mousedown mouseup mouseenter mouseleave mousemove contextmenu".split(" ");
                d = 0;
                for (c = e.length; c > d; d++) b.DomEvent[a](this._container, e[d], this._fireMouseEvent, this);
                this.options.trackResize && b.DomEvent[a](f, "resize", this._onResize, this);
            }
        },
        _onResize: function () {
            b.Util.cancelAnimFrame(this._resizeRequest);
            this._resizeRequest = b.Util.requestAnimFrame(
                function () {
                    this.invalidateSize({ debounceMoveend: !0 });
                },
                this,
                !1,
                this._container
            );
        },
        _onMouseClick: function (a) {
            !this._loaded || (!a._simulated && ((this.dragging && this.dragging.moved()) || (this.boxZoom && this.boxZoom.moved()))) || b.DomEvent._skipped(a) || (this.fire("preclick"), this._fireMouseEvent(a));
        },
        _fireMouseEvent: function (a) {
            if (this._loaded && !b.DomEvent._skipped(a)) {
                var d = a.type;
                if (((d = "mouseenter" === d ? "mouseover" : "mouseleave" === d ? "mouseout" : d), this.hasEventListeners(d))) {
                    "contextmenu" === d && b.DomEvent.preventDefault(a);
                    var c = this.mouseEventToContainerPoint(a),
                        e = this.containerPointToLayerPoint(c),
                        n = this.layerPointToLatLng(e);
                    this.fire(d, { latlng: n, layerPoint: e, containerPoint: c, originalEvent: a });
                }
            }
        },
        _onTileLayerLoad: function () {
            this._tileLayersToLoad--;
            this._tileLayersNum && !this._tileLayersToLoad && this.fire("tilelayersload");
        },
        _clearHandlers: function () {
            for (var a = 0, d = this._handlers.length; d > a; a++) this._handlers[a].disable();
        },
        whenReady: function (a, d) {
            return this._loaded ? a.call(d || this, this) : this.on("load", a, d), this;
        },
        _layerAdd: function (a) {
            a.onAdd(this);
            this.fire("layeradd", { layer: a });
        },
        _getMapPanePos: function () {
            return b.DomUtil.getPosition(this._mapPane);
        },
        _moved: function () {
            var a = this._getMapPanePos();
            return a && !a.equals([0, 0]);
        },
        _getTopLeftPoint: function () {
            return this.getPixelOrigin().subtract(this._getMapPanePos());
        },
        _getNewTopLeftPoint: function (a, d) {
            var b = this.getSize()._divideBy(2);
            return this.project(a, d)._subtract(b)._round();
        },
        _latLngToNewLayerPoint: function (a, d, b) {
            b = this._getNewTopLeftPoint(b, d).add(this._getMapPanePos());
            return this.project(a, d)._subtract(b);
        },
        _getCenterLayerPoint: function () {
            return this.containerPointToLayerPoint(this.getSize()._divideBy(2));
        },
        _getCenterOffset: function (a) {
            return this.latLngToLayerPoint(a).subtract(this._getCenterLayerPoint());
        },
        _limitCenter: function (a, d, c) {
            if (!c) return a;
            a = this.project(a, d);
            var e = this.getSize().divideBy(2),
                e = new b.Bounds(a.subtract(e), a.add(e));
            c = this._getBoundsOffset(e, c, d);
            return this.unproject(a.add(c), d);
        },
        _limitOffset: function (a, d) {
            if (!d) return a;
            var c = this.getPixelBounds(),
                c = new b.Bounds(c.min.add(a), c.max.add(a));
            return a.add(this._getBoundsOffset(c, d));
        },
        _getBoundsOffset: function (a, d, c) {
            var e = this.project(d.getNorthWest(), c).subtract(a.min);
            d = this.project(d.getSouthEast(), c).subtract(a.max);
            a = this._rebound(e.x, -d.x);
            e = this._rebound(e.y, -d.y);
            return new b.Point(a, e);
        },
        _rebound: function (a, d) {
            return 0 < a + d ? Math.round(a - d) / 2 : Math.max(0, Math.ceil(a)) - Math.max(0, Math.floor(d));
        },
        _limitZoom: function (a) {
            var d = this.getMinZoom(),
                b = this.getMaxZoom();
            return Math.max(d, Math.min(b, a));
        },
    });
    b.map = function (a, d) {
        return new b.Map(a, d);
    };
    b.Projection.Mercator = {
        MAX_LATITUDE: 85.0840591556,
        R_MINOR: 6356752.314245179,
        R_MAJOR: 6378137,
        project: function (a) {
            var d = b.LatLng.DEG_TO_RAD,
                c = this.MAX_LATITUDE,
                e = this.R_MAJOR,
                n = a.lng * d * e;
            a = Math.max(Math.min(c, a.lat), -c) * d;
            d = this.R_MINOR / e;
            d = Math.sqrt(1 - d * d);
            c = d * Math.sin(a);
            c = Math.pow((1 - c) / (1 + c), 0.5 * d);
            return (a = -e * Math.log(Math.tan(0.5 * (0.5 * Math.PI - a)) / c)), new b.Point(n, a);
        },
        unproject: function (a) {
            var d,
                c = b.LatLng.RAD_TO_DEG,
                e = this.R_MAJOR,
                n = (a.x * c) / e,
                f = this.R_MINOR / e,
                f = Math.sqrt(1 - f * f);
            a = Math.exp(-a.y / e);
            var e = Math.PI / 2 - 2 * Math.atan(a),
                l = 15;
            for (d = 0.1; 1e-7 < Math.abs(d) && 0 < --l; ) (d = f * Math.sin(e)), (d = Math.PI / 2 - 2 * Math.atan(a * Math.pow((1 - d) / (1 + d), 0.5 * f)) - e), (e += d);
            return new b.LatLng(e * c, n);
        },
    };
    b.CRS.EPSG3395 = b.extend({}, b.CRS, {
        code: "EPSG:3395",
        projection: b.Projection.Mercator,
        transformation: (function () {
            var a = 0.5 / (Math.PI * b.Projection.Mercator.R_MAJOR);
            return new b.Transformation(a, 0.5, -a, 0.5);
        })(),
    });
    b.TileLayer = b.Class.extend({
        includes: b.Mixin.Events,
        options: { minZoom: 0, maxZoom: 18, tileSize: 256, subdomains: "abc", errorTileUrl: "", attribution: "", zoomOffset: 0, opacity: 1, unloadInvisibleTiles: b.Browser.mobile, updateWhenIdle: b.Browser.mobile },
        initialize: function (a, d) {
            d = b.setOptions(this, d);
            d.detectRetina && b.Browser.retina && 0 < d.maxZoom && ((d.tileSize = Math.floor(d.tileSize / 2)), d.zoomOffset++, 0 < d.minZoom && d.minZoom--, this.options.maxZoom--);
            d.bounds && (d.bounds = b.latLngBounds(d.bounds));
            this._url = a;
            a = this.options.subdomains;
            "string" == typeof a && (this.options.subdomains = a.split(""));
        },
        onAdd: function (a) {
            this._map = a;
            this._animated = a._zoomAnimated;
            this._initContainer();
            a.on({ viewreset: this._reset, moveend: this._update }, this);
            this._animated && a.on({ zoomanim: this._animateZoom, zoomend: this._endZoomAnim }, this);
            this.options.updateWhenIdle || ((this._limitedUpdate = b.Util.limitExecByInterval(this._update, 150, this)), a.on("move", this._limitedUpdate, this));
            this._reset();
            this._update();
        },
        addTo: function (a) {
            return a.addLayer(this), this;
        },
        onRemove: function (a) {
            this._container.parentNode.removeChild(this._container);
            a.off({ viewreset: this._reset, moveend: this._update }, this);
            this._animated && a.off({ zoomanim: this._animateZoom, zoomend: this._endZoomAnim }, this);
            this.options.updateWhenIdle || a.off("move", this._limitedUpdate, this);
            this._map = this._container = null;
        },
        bringToFront: function () {
            var a = this._map._panes.tilePane;
            return this._container && (a.appendChild(this._container), this._setAutoZIndex(a, Math.max)), this;
        },
        bringToBack: function () {
            var a = this._map._panes.tilePane;
            return this._container && (a.insertBefore(this._container, a.firstChild), this._setAutoZIndex(a, Math.min)), this;
        },
        getAttribution: function () {
            return this.options.attribution;
        },
        getContainer: function () {
            return this._container;
        },
        setOpacity: function (a) {
            return (this.options.opacity = a), this._map && this._updateOpacity(), this;
        },
        setZIndex: function (a) {
            return (this.options.zIndex = a), this._updateZIndex(), this;
        },
        setUrl: function (a, d) {
            return (this._url = a), d || this.redraw(), this;
        },
        redraw: function () {
            return this._map && (this._reset({ hard: !0 }), this._update()), this;
        },
        _updateZIndex: function () {
            this._container && this.options.zIndex !== c && (this._container.style.zIndex = this.options.zIndex);
        },
        _setAutoZIndex: function (a, d) {
            var b,
                c,
                e = a.children,
                f = -d(1 / 0, -1 / 0);
            a = 0;
            for (c = e.length; c > a; a++) e[a] !== this._container && ((b = parseInt(e[a].style.zIndex, 10)), isNaN(b) || (f = d(f, b)));
            this.options.zIndex = this._container.style.zIndex = (isFinite(f) ? f : 0) + d(1, -1);
        },
        _updateOpacity: function () {
            var a,
                d = this._tiles;
            if (b.Browser.ielt9) for (a in d) b.DomUtil.setOpacity(d[a], this.options.opacity);
            else b.DomUtil.setOpacity(this._container, this.options.opacity);
        },
        _initContainer: function () {
            var a = this._map._panes.tilePane;
            this._container ||
                (((this._container = b.DomUtil.create("div", "leaflet-layer")), this._updateZIndex(), this._animated)
                    ? ((this._bgBuffer = b.DomUtil.create("div", "leaflet-tile-container", this._container)), (this._tileContainer = b.DomUtil.create("div", "leaflet-tile-container", this._container)))
                    : (this._tileContainer = this._container),
                a.appendChild(this._container),
                1 > this.options.opacity && this._updateOpacity());
        },
        _reset: function (a) {
            for (var d in this._tiles) this.fire("tileunload", { tile: this._tiles[d] });
            this._tiles = {};
            this._tilesToLoad = 0;
            this.options.reuseTiles && (this._unusedTiles = []);
            this._tileContainer.innerHTML = "";
            this._animated && a && a.hard && this._clearBgBuffer();
            this._initContainer();
        },
        _getTileSize: function () {
            var a = this._map,
                d = a.getZoom() + this.options.zoomOffset,
                b = this.options.maxNativeZoom,
                c = this.options.tileSize;
            return b && d > b && (c = Math.round((a.getZoomScale(d) / a.getZoomScale(b)) * c)), c;
        },
        _update: function () {
            if (this._map) {
                var a = this._map,
                    d = a.getPixelBounds(),
                    a = a.getZoom(),
                    c = this._getTileSize();
                a > this.options.maxZoom ||
                    a < this.options.minZoom ||
                    ((d = b.bounds(d.min.divideBy(c)._floor(), d.max.divideBy(c)._floor())), this._addTilesFromCenterOut(d), (this.options.unloadInvisibleTiles || this.options.reuseTiles) && this._removeOtherTiles(d));
            }
        },
        _addTilesFromCenterOut: function (a) {
            var d,
                c,
                e,
                n = [],
                f = a.getCenter();
            for (d = a.min.y; d <= a.max.y; d++) for (c = a.min.x; c <= a.max.x; c++) (e = new b.Point(c, d)), this._tileShouldBeLoaded(e) && n.push(e);
            a = n.length;
            if (0 !== a) {
                n.sort(function (a, d) {
                    return a.distanceTo(f) - d.distanceTo(f);
                });
                d = g.createDocumentFragment();
                this._tilesToLoad || this.fire("loading");
                this._tilesToLoad += a;
                for (c = 0; a > c; c++) this._addTile(n[c], d);
                this._tileContainer.appendChild(d);
            }
        },
        _tileShouldBeLoaded: function (a) {
            if (a.x + ":" + a.y in this._tiles) return !1;
            var d = this.options;
            if (!d.continuousWorld) {
                var b = this._getWrapTileNum();
                if ((d.noWrap && (0 > a.x || a.x >= b.x)) || 0 > a.y || a.y >= b.y) return !1;
            }
            return d.bounds &&
                ((b = d.tileSize), (a = a.multiplyBy(b)), (b = a.add([b, b])), (a = this._map.unproject(a)), (b = this._map.unproject(b)), d.continuousWorld || d.noWrap || ((a = a.wrap()), (b = b.wrap())), !d.bounds.intersects([a, b]))
                ? !1
                : !0;
        },
        _removeOtherTiles: function (a) {
            var d, b, c;
            for (c in this._tiles) (d = c.split(":")), (b = parseInt(d[0], 10)), (d = parseInt(d[1], 10)), (b < a.min.x || b > a.max.x || d < a.min.y || d > a.max.y) && this._removeTile(c);
        },
        _removeTile: function (a) {
            var d = this._tiles[a];
            this.fire("tileunload", { tile: d, url: d.src });
            this.options.reuseTiles ? (b.DomUtil.removeClass(d, "leaflet-tile-loaded"), this._unusedTiles.push(d)) : d.parentNode === this._tileContainer && this._tileContainer.removeChild(d);
            b.Browser.android || ((d.onload = null), (d.src = b.Util.emptyImageUrl));
            delete this._tiles[a];
        },
        _addTile: function (a, d) {
            var c = this._getTilePos(a),
                e = this._getTile();
            b.DomUtil.setPosition(e, c, b.Browser.chrome);
            this._tiles[a.x + ":" + a.y] = e;
            this._loadTile(e, a);
            e.parentNode !== this._tileContainer && d.appendChild(e);
        },
        _getZoomForUrl: function () {
            var a = this.options,
                d = this._map.getZoom();
            return a.zoomReverse && (d = a.maxZoom - d), (d += a.zoomOffset), a.maxNativeZoom ? Math.min(d, a.maxNativeZoom) : d;
        },
        _getTilePos: function (a) {
            var d = this._map.getPixelOrigin(),
                b = this._getTileSize();
            return a.multiplyBy(b).subtract(d);
        },
        getTileUrl: function (a) {
            return b.Util.template(this._url, b.extend({ s: this._getSubdomain(a), z: a.z, x: a.x, y: a.y }, this.options));
        },
        _getWrapTileNum: function () {
            return this._map.options.crs.getSize(this._map.getZoom()).divideBy(this._getTileSize())._floor();
        },
        _adjustTilePoint: function (a) {
            var d = this._getWrapTileNum();
            this.options.continuousWorld || this.options.noWrap || (a.x = ((a.x % d.x) + d.x) % d.x);
            this.options.tms && (a.y = d.y - a.y - 1);
            a.z = this._getZoomForUrl();
        },
        _getSubdomain: function (a) {
            return this.options.subdomains[Math.abs(a.x + a.y) % this.options.subdomains.length];
        },
        _getTile: function () {
            if (this.options.reuseTiles && 0 < this._unusedTiles.length) {
                var a = this._unusedTiles.pop();
                return this._resetTile(a), a;
            }
            return this._createTile();
        },
        _resetTile: function () {},
        _createTile: function () {
            var a = b.DomUtil.create("img", "leaflet-tile");
            return (
                (a.style.width = a.style.height = this._getTileSize() + "px"),
                (a.galleryimg = "no"),
                (a.onselectstart = a.onmousemove = b.Util.falseFn),
                b.Browser.ielt9 && this.options.opacity !== c && b.DomUtil.setOpacity(a, this.options.opacity),
                b.Browser.mobileWebkit3d && (a.style.WebkitBackfaceVisibility = "hidden"),
                a
            );
        },
        _loadTile: function (a, d) {
            a._layer = this;
            a.onload = this._tileOnLoad;
            a.onerror = this._tileOnError;
            this._adjustTilePoint(d);
            a.src = this.getTileUrl(d);
            this.fire("tileloadstart", { tile: a, url: a.src });
        },
        _tileLoaded: function () {
            this._tilesToLoad--;
            this._animated && b.DomUtil.addClass(this._tileContainer, "leaflet-zoom-animated");
            this._tilesToLoad || (this.fire("load"), this._animated && (clearTimeout(this._clearBgBufferTimer), (this._clearBgBufferTimer = setTimeout(b.bind(this._clearBgBuffer, this), 500))));
        },
        _tileOnLoad: function () {
            var a = this._layer;
            this.src !== b.Util.emptyImageUrl && (b.DomUtil.addClass(this, "leaflet-tile-loaded"), a.fire("tileload", { tile: this, url: this.src }));
            a._tileLoaded();
        },
        _tileOnError: function () {
            var a = this._layer;
            a.fire("tileerror", { tile: this, url: this.src });
            var d = a.options.errorTileUrl;
            d && (this.src = d);
            a._tileLoaded();
        },
    });
    b.tileLayer = function (a, d) {
        return new b.TileLayer(a, d);
    };
    b.TileLayer.WMS = b.TileLayer.extend({
        defaultWmsParams: { service: "WMS", request: "GetMap", version: "1.1.1", layers: "", styles: "", format: "image/jpeg", transparent: !1 },
        initialize: function (a, d) {
            this._url = a;
            a = b.extend({}, this.defaultWmsParams);
            var c = d.tileSize || this.options.tileSize;
            a.width = a.height = d.detectRetina && b.Browser.retina ? 2 * c : c;
            for (var e in d) this.options.hasOwnProperty(e) || "crs" === e || (a[e] = d[e]);
            this.wmsParams = a;
            b.setOptions(this, d);
        },
        onAdd: function (a) {
            this._crs = this.options.crs || a.options.crs;
            this._wmsVersion = parseFloat(this.wmsParams.version);
            this.wmsParams[1.3 <= this._wmsVersion ? "crs" : "srs"] = this._crs.code;
            b.TileLayer.prototype.onAdd.call(this, a);
        },
        getTileUrl: function (a) {
            var d = this._map,
                c = this.options.tileSize,
                e = a.multiplyBy(c),
                c = e.add([c, c]),
                e = this._crs.project(d.unproject(e, a.z)),
                d = this._crs.project(d.unproject(c, a.z)),
                d = 1.3 <= this._wmsVersion && this._crs === b.CRS.EPSG4326 ? [d.y, e.x, e.y, d.x].join() : [e.x, d.y, d.x, e.y].join();
            a = b.Util.template(this._url, { s: this._getSubdomain(a) });
            return a + b.Util.getParamString(this.wmsParams, a, !0) + "&BBOX=" + d;
        },
        setParams: function (a, d) {
            return b.extend(this.wmsParams, a), d || this.redraw(), this;
        },
    });
    b.tileLayer.wms = function (a, d) {
        return new b.TileLayer.WMS(a, d);
    };
    b.TileLayer.Canvas = b.TileLayer.extend({
        options: { async: !1 },
        initialize: function (a) {
            b.setOptions(this, a);
        },
        redraw: function () {
            this._map && (this._reset({ hard: !0 }), this._update());
            for (var a in this._tiles) this._redrawTile(this._tiles[a]);
            return this;
        },
        _redrawTile: function (a) {
            this.drawTile(a, a._tilePoint, this._map._zoom);
        },
        _createTile: function () {
            var a = b.DomUtil.create("canvas", "leaflet-tile");
            return (a.width = a.height = this.options.tileSize), (a.onselectstart = a.onmousemove = b.Util.falseFn), a;
        },
        _loadTile: function (a, d) {
            a._layer = this;
            a._tilePoint = d;
            this._redrawTile(a);
            this.options.async || this.tileDrawn(a);
        },
        drawTile: function () {},
        tileDrawn: function (a) {
            this._tileOnLoad.call(a);
        },
    });
    b.tileLayer.canvas = function (a) {
        return new b.TileLayer.Canvas(a);
    };
    b.ImageOverlay = b.Class.extend({
        includes: b.Mixin.Events,
        options: { opacity: 1 },
        initialize: function (a, d, c) {
            this._url = a;
            this._bounds = b.latLngBounds(d);
            b.setOptions(this, c);
        },
        onAdd: function (a) {
            this._map = a;
            this._image || this._initImage();
            a._panes.overlayPane.appendChild(this._image);
            a.on("viewreset", this._reset, this);
            a.options.zoomAnimation && b.Browser.any3d && a.on("zoomanim", this._animateZoom, this);
            this._reset();
        },
        onRemove: function (a) {
            a.getPanes().overlayPane.removeChild(this._image);
            a.off("viewreset", this._reset, this);
            a.options.zoomAnimation && a.off("zoomanim", this._animateZoom, this);
        },
        addTo: function (a) {
            return a.addLayer(this), this;
        },
        setOpacity: function (a) {
            return (this.options.opacity = a), this._updateOpacity(), this;
        },
        bringToFront: function () {
            return this._image && this._map._panes.overlayPane.appendChild(this._image), this;
        },
        bringToBack: function () {
            var a = this._map._panes.overlayPane;
            return this._image && a.insertBefore(this._image, a.firstChild), this;
        },
        setUrl: function (a) {
            this._url = a;
            this._image.src = this._url;
        },
        getAttribution: function () {
            return this.options.attribution;
        },
        _initImage: function () {
            this._image = b.DomUtil.create("img", "leaflet-image-layer");
            this._map.options.zoomAnimation && b.Browser.any3d ? b.DomUtil.addClass(this._image, "leaflet-zoom-animated") : b.DomUtil.addClass(this._image, "leaflet-zoom-hide");
            this._updateOpacity();
            b.extend(this._image, { galleryimg: "no", onselectstart: b.Util.falseFn, onmousemove: b.Util.falseFn, onload: b.bind(this._onImageLoad, this), src: this._url });
        },
        _animateZoom: function (a) {
            var d = this._map,
                c = this._image,
                e = d.getZoomScale(a.zoom),
                n = this._bounds.getNorthWest(),
                f = this._bounds.getSouthEast(),
                n = d._latLngToNewLayerPoint(n, a.zoom, a.center);
            a = d._latLngToNewLayerPoint(f, a.zoom, a.center)._subtract(n);
            a = n._add(a._multiplyBy(0.5 * (1 - 1 / e)));
            c.style[b.DomUtil.TRANSFORM] = b.DomUtil.getTranslateString(a) + " scale(" + e + ") ";
        },
        _reset: function () {
            var a = this._image,
                d = this._map.latLngToLayerPoint(this._bounds.getNorthWest()),
                c = this._map.latLngToLayerPoint(this._bounds.getSouthEast())._subtract(d);
            b.DomUtil.setPosition(a, d);
            a.style.width = c.x + "px";
            a.style.height = c.y + "px";
        },
        _onImageLoad: function () {
            this.fire("load");
        },
        _updateOpacity: function () {
            b.DomUtil.setOpacity(this._image, this.options.opacity);
        },
    });
    b.imageOverlay = function (a, d, c) {
        return new b.ImageOverlay(a, d, c);
    };
    b.Icon = b.Class.extend({
        options: { className: "" },
        initialize: function (a) {
            b.setOptions(this, a);
        },
        createIcon: function (a) {
            return this._createIcon("icon", a);
        },
        createShadow: function (a) {
            return this._createIcon("shadow", a);
        },
        _createIcon: function (a, d) {
            var b = this._getIconUrl(a);
            if (!b) {
                if ("icon" === a) throw Error("iconUrl not set in Icon options (see the docs).");
                return null;
            }
            var c;
            return (c = d && "IMG" === d.tagName ? this._createImg(b, d) : this._createImg(b)), this._setIconStyles(c, a), c;
        },
        _setIconStyles: function (a, d) {
            var c,
                e = this.options,
                n = b.point(e[d + "Size"]);
            c = b.point("shadow" === d ? e.shadowAnchor || e.iconAnchor : e.iconAnchor);
            !c && n && (c = n.divideBy(2, !0));
            a.className = "leaflet-marker-" + d + " " + e.className;
            c && ((a.style.marginLeft = -c.x + "px"), (a.style.marginTop = -c.y + "px"));
            n && ((a.style.width = n.x + "px"), (a.style.height = n.y + "px"));
        },
        _createImg: function (a, d) {
            return (d = d || g.createElement("img")), (d.src = a), d;
        },
        _getIconUrl: function (a) {
            return b.Browser.retina && this.options[a + "RetinaUrl"] ? this.options[a + "RetinaUrl"] : this.options[a + "Url"];
        },
    });
    b.icon = function (a) {
        return new b.Icon(a);
    };
    b.Icon.Default = b.Icon.extend({
        options: { iconSize: [25, 41], iconAnchor: [12, 41], popupAnchor: [1, -34], shadowSize: [41, 41] },
        _getIconUrl: function (a) {
            var d = a + "Url";
            if (this.options[d]) return this.options[d];
            b.Browser.retina && "icon" === a && (a += "-2x");
            d = b.Icon.Default.imagePath;
            if (!d) throw Error("Couldn't autodetect L.Icon.Default.imagePath, set it manually.");
            return d + "/marker-" + a + ".png";
        },
    });
    b.Icon.Default.imagePath = (function () {
        var a,
            d,
            b,
            c,
            e = g.getElementsByTagName("script"),
            f = /[\/^]leaflet[\-\._]?([\w\-\._]*)\.js\??/;
        a = 0;
        for (d = e.length; d > a; a++) if (((b = e[a].src), b.match(f))) return (c = b.split(f)[0]), (c ? c + "/" : "") + "images";
    })();
    b.Marker = b.Class.extend({
        includes: b.Mixin.Events,
        options: { icon: new b.Icon.Default(), title: "", alt: "", clickable: !0, draggable: !1, keyboard: !0, zIndexOffset: 0, opacity: 1, riseOnHover: !1, riseOffset: 250 },
        initialize: function (a, d) {
            b.setOptions(this, d);
            this._latlng = b.latLng(a);
        },
        onAdd: function (a) {
            this._map = a;
            a.on("viewreset", this.update, this);
            this._initIcon();
            this.update();
            this.fire("add");
            a.options.zoomAnimation && a.options.markerZoomAnimation && a.on("zoomanim", this._animateZoom, this);
        },
        addTo: function (a) {
            return a.addLayer(this), this;
        },
        onRemove: function (a) {
            this.dragging && this.dragging.disable();
            this._removeIcon();
            this._removeShadow();
            this.fire("remove");
            a.off({ viewreset: this.update, zoomanim: this._animateZoom }, this);
            this._map = null;
        },
        getLatLng: function () {
            return this._latlng;
        },
        setLatLng: function (a) {
            return (this._latlng = b.latLng(a)), this.update(), this.fire("move", { latlng: this._latlng });
        },
        setZIndexOffset: function (a) {
            return (this.options.zIndexOffset = a), this.update(), this;
        },
        setIcon: function (a) {
            return (this.options.icon = a), this._map && (this._initIcon(), this.update()), this._popup && this.bindPopup(this._popup), this;
        },
        update: function () {
            if (this._icon) {
                var a = this._map.latLngToLayerPoint(this._latlng).round();
                this._setPos(a);
            }
            return this;
        },
        _initIcon: function () {
            var a = this.options,
                d = this._map,
                c = d.options.zoomAnimation && d.options.markerZoomAnimation ? "leaflet-zoom-animated" : "leaflet-zoom-hide",
                e = a.icon.createIcon(this._icon),
                d = !1;
            e !== this._icon && (this._icon && this._removeIcon(), (d = !0), a.title && (e.title = a.title), a.alt && (e.alt = a.alt));
            b.DomUtil.addClass(e, c);
            a.keyboard && (e.tabIndex = "0");
            this._icon = e;
            this._initInteraction();
            a.riseOnHover && b.DomEvent.on(e, "mouseover", this._bringToFront, this).on(e, "mouseout", this._resetZIndex, this);
            var e = a.icon.createShadow(this._shadow),
                n = !1;
            e !== this._shadow && (this._removeShadow(), (n = !0));
            e && b.DomUtil.addClass(e, c);
            this._shadow = e;
            1 > a.opacity && this._updateOpacity();
            a = this._map._panes;
            d && a.markerPane.appendChild(this._icon);
            e && n && a.shadowPane.appendChild(this._shadow);
        },
        _removeIcon: function () {
            this.options.riseOnHover && b.DomEvent.off(this._icon, "mouseover", this._bringToFront).off(this._icon, "mouseout", this._resetZIndex);
            this._map._panes.markerPane.removeChild(this._icon);
            this._icon = null;
        },
        _removeShadow: function () {
            this._shadow && this._map._panes.shadowPane.removeChild(this._shadow);
            this._shadow = null;
        },
        _setPos: function (a) {
            b.DomUtil.setPosition(this._icon, a);
            this._shadow && b.DomUtil.setPosition(this._shadow, a);
            this._zIndex = a.y + this.options.zIndexOffset;
            this._resetZIndex();
        },
        _updateZIndex: function (a) {
            this._icon.style.zIndex = this._zIndex + a;
        },
        _animateZoom: function (a) {
            a = this._map._latLngToNewLayerPoint(this._latlng, a.zoom, a.center).round();
            this._setPos(a);
        },
        _initInteraction: function () {
            if (this.options.clickable) {
                var a = this._icon,
                    d = ["dblclick", "mousedown", "mouseover", "mouseout", "contextmenu"];
                b.DomUtil.addClass(a, "leaflet-clickable");
                b.DomEvent.on(a, "click", this._onMouseClick, this);
                b.DomEvent.on(a, "keypress", this._onKeyPress, this);
                for (var c = 0; c < d.length; c++) b.DomEvent.on(a, d[c], this._fireMouseEvent, this);
                b.Handler.MarkerDrag && ((this.dragging = new b.Handler.MarkerDrag(this)), this.options.draggable && this.dragging.enable());
            }
        },
        _onMouseClick: function (a) {
            var d = this.dragging && this.dragging.moved();
            (this.hasEventListeners(a.type) || d) && b.DomEvent.stopPropagation(a);
            d || (((this.dragging && this.dragging._enabled) || !this._map.dragging || !this._map.dragging.moved()) && this.fire(a.type, { originalEvent: a, latlng: this._latlng }));
        },
        _onKeyPress: function (a) {
            13 === a.keyCode && this.fire("click", { originalEvent: a, latlng: this._latlng });
        },
        _fireMouseEvent: function (a) {
            this.fire(a.type, { originalEvent: a, latlng: this._latlng });
            "contextmenu" === a.type && this.hasEventListeners(a.type) && b.DomEvent.preventDefault(a);
            "mousedown" !== a.type ? b.DomEvent.stopPropagation(a) : b.DomEvent.preventDefault(a);
        },
        setOpacity: function (a) {
            return (this.options.opacity = a), this._map && this._updateOpacity(), this;
        },
        _updateOpacity: function () {
            b.DomUtil.setOpacity(this._icon, this.options.opacity);
            this._shadow && b.DomUtil.setOpacity(this._shadow, this.options.opacity);
        },
        _bringToFront: function () {
            this._updateZIndex(this.options.riseOffset);
        },
        _resetZIndex: function () {
            this._updateZIndex(0);
        },
    });
    b.marker = function (a, d) {
        return new b.Marker(a, d);
    };
    b.DivIcon = b.Icon.extend({
        options: { iconSize: [12, 12], className: "leaflet-div-icon", html: !1 },
        createIcon: function (a) {
            a = a && "DIV" === a.tagName ? a : g.createElement("div");
            var d = this.options;
            return (a.innerHTML = !1 !== d.html ? d.html : ""), d.bgPos && (a.style.backgroundPosition = -d.bgPos.x + "px " + -d.bgPos.y + "px"), this._setIconStyles(a, "icon"), a;
        },
        createShadow: function () {
            return null;
        },
    });
    b.divIcon = function (a) {
        return new b.DivIcon(a);
    };
    b.Map.mergeOptions({ closePopupOnClick: !0 });
    b.Popup = b.Class.extend({
        includes: b.Mixin.Events,
        options: { minWidth: 50, maxWidth: 300, autoPan: !0, closeButton: !0, offset: [0, 7], autoPanPadding: [5, 5], keepInView: !1, className: "", zoomAnimation: !0 },
        initialize: function (a, d) {
            b.setOptions(this, a);
            this._source = d;
            this._animated = b.Browser.any3d && this.options.zoomAnimation;
            this._isOpen = !1;
        },
        onAdd: function (a) {
            this._map = a;
            this._container || this._initLayout();
            var d = a.options.fadeAnimation;
            d && b.DomUtil.setOpacity(this._container, 0);
            a._panes.popupPane.appendChild(this._container);
            a.on(this._getEvents(), this);
            this.update();
            d && b.DomUtil.setOpacity(this._container, 1);
            this.fire("open");
            a.fire("popupopen", { popup: this });
            this._source && this._source.fire("popupopen", { popup: this });
        },
        addTo: function (a) {
            return a.addLayer(this), this;
        },
        openOn: function (a) {
            return a.openPopup(this), this;
        },
        onRemove: function (a) {
            a._panes.popupPane.removeChild(this._container);
            b.Util.falseFn(this._container.offsetWidth);
            a.off(this._getEvents(), this);
            a.options.fadeAnimation && b.DomUtil.setOpacity(this._container, 0);
            this._map = null;
            this.fire("close");
            a.fire("popupclose", { popup: this });
            this._source && this._source.fire("popupclose", { popup: this });
        },
        getLatLng: function () {
            return this._latlng;
        },
        setLatLng: function (a) {
            return (this._latlng = b.latLng(a)), this._map && (this._updatePosition(), this._adjustPan()), this;
        },
        getContent: function () {
            return this._content;
        },
        setContent: function (a) {
            return (this._content = a), this.update(), this;
        },
        update: function () {
            this._map && ((this._container.style.visibility = "hidden"), this._updateContent(), this._updateLayout(), this._updatePosition(), (this._container.style.visibility = ""), this._adjustPan());
        },
        _getEvents: function () {
            var a = { viewreset: this._updatePosition };
            return (
                this._animated && (a.zoomanim = this._zoomAnimation),
                ("closeOnClick" in this.options ? this.options.closeOnClick : this._map.options.closePopupOnClick) && (a.preclick = this._close),
                this.options.keepInView && (a.moveend = this._adjustPan),
                a
            );
        },
        _close: function () {
            this._map && this._map.closePopup(this);
        },
        _initLayout: function () {
            var a,
                d = (this._container = b.DomUtil.create("div", "leaflet-popup " + this.options.className + " leaflet-zoom-" + (this._animated ? "animated" : "hide")));
            this.options.closeButton &&
                ((a = this._closeButton = b.DomUtil.create("a", "leaflet-popup-close-button", d)),
                (a.href = "#close"),
                (a.innerHTML = "&#215;"),
                b.DomEvent.disableClickPropagation(a),
                b.DomEvent.on(a, "click", this._onCloseButtonClick, this));
            a = this._wrapper = b.DomUtil.create("div", "leaflet-popup-content-wrapper", d);
            b.DomEvent.disableClickPropagation(a);
            this._contentNode = b.DomUtil.create("div", "leaflet-popup-content", a);
            b.DomEvent.disableScrollPropagation(this._contentNode);
            b.DomEvent.on(a, "contextmenu", b.DomEvent.stopPropagation);
            this._tipContainer = b.DomUtil.create("div", "leaflet-popup-tip-container", d);
            this._tip = b.DomUtil.create("div", "leaflet-popup-tip", this._tipContainer);
        },
        _updateContent: function () {
            if (this._content) {
                if ("string" == typeof this._content) this._contentNode.innerHTML = this._content;
                else {
                    for (; this._contentNode.hasChildNodes(); ) this._contentNode.removeChild(this._contentNode.firstChild);
                    this._contentNode.appendChild(this._content);
                }
                this.fire("contentupdate");
            }
        },
        _updateLayout: function () {
            var a = this._contentNode,
                d = a.style;
            d.width = "";
            d.whiteSpace = "nowrap";
            var c = a.offsetWidth,
                c = Math.min(c, this.options.maxWidth),
                c = Math.max(c, this.options.minWidth);
            d.width = c + 1 + "px";
            d.whiteSpace = "";
            d.height = "";
            var c = a.offsetHeight,
                e = this.options.maxHeight;
            e && c > e ? ((d.height = e + "px"), b.DomUtil.addClass(a, "leaflet-popup-scrolled")) : b.DomUtil.removeClass(a, "leaflet-popup-scrolled");
            this._containerWidth = this._container.offsetWidth;
        },
        _updatePosition: function () {
            if (this._map) {
                var a = this._map.latLngToLayerPoint(this._latlng),
                    d = this._animated,
                    c = b.point(this.options.offset);
                d && b.DomUtil.setPosition(this._container, a);
                this._containerBottom = -c.y - (d ? 0 : a.y);
                this._containerLeft = -Math.round(this._containerWidth / 2) + c.x + (d ? 0 : a.x);
                this._container.style.bottom = this._containerBottom + "px";
                this._container.style.left = this._containerLeft + "px";
            }
        },
        _zoomAnimation: function (a) {
            a = this._map._latLngToNewLayerPoint(this._latlng, a.zoom, a.center);
            b.DomUtil.setPosition(this._container, a);
        },
        _adjustPan: function () {
            if (this.options.autoPan) {
                var a = this._map,
                    d = this._container.offsetHeight,
                    c = this._containerWidth,
                    e = new b.Point(this._containerLeft, -d - this._containerBottom);
                this._animated && e._add(b.DomUtil.getPosition(this._container));
                var e = a.layerPointToContainerPoint(e),
                    n = b.point(this.options.autoPanPadding),
                    f = b.point(this.options.autoPanPaddingTopLeft || n),
                    n = b.point(this.options.autoPanPaddingBottomRight || n),
                    l = a.getSize(),
                    m = 0,
                    p = 0;
                e.x + c + n.x > l.x && (m = e.x + c - l.x + n.x);
                0 > e.x - m - f.x && (m = e.x - f.x);
                e.y + d + n.y > l.y && (p = e.y + d - l.y + n.y);
                0 > e.y - p - f.y && (p = e.y - f.y);
                (m || p) && a.fire("autopanstart").panBy([m, p]);
            }
        },
        _onCloseButtonClick: function (a) {
            this._close();
            b.DomEvent.stop(a);
        },
    });
    b.popup = function (a, d) {
        return new b.Popup(a, d);
    };
    b.Map.include({
        openPopup: function (a, d, c) {
            (this.closePopup(), a instanceof b.Popup) || (a = new b.Popup(c).setLatLng(d).setContent(a));
            return (a._isOpen = !0), (this._popup = a), this.addLayer(a);
        },
        closePopup: function (a) {
            return (a && a !== this._popup) || ((a = this._popup), (this._popup = null)), a && (this.removeLayer(a), (a._isOpen = !1)), this;
        },
    });
    b.Marker.include({
        openPopup: function () {
            return this._popup && this._map && !this._map.hasLayer(this._popup) && (this._popup.setLatLng(this._latlng), this._map.openPopup(this._popup)), this;
        },
        closePopup: function () {
            return this._popup && this._popup._close(), this;
        },
        togglePopup: function () {
            return this._popup && (this._popup._isOpen ? this.closePopup() : this.openPopup()), this;
        },
        bindPopup: function (a, d) {
            var c = b.point(this.options.icon.options.popupAnchor || [0, 0]);
            return (
                (c = c.add(b.Popup.prototype.options.offset)),
                d && d.offset && (c = c.add(d.offset)),
                (d = b.extend({ offset: c }, d)),
                this._popupHandlersAdded || (this.on("click", this.togglePopup, this).on("remove", this.closePopup, this).on("move", this._movePopup, this), (this._popupHandlersAdded = !0)),
                a instanceof b.Popup ? (b.setOptions(a, d), (this._popup = a)) : (this._popup = new b.Popup(d, this).setContent(a)),
                this
            );
        },
        setPopupContent: function (a) {
            return this._popup && this._popup.setContent(a), this;
        },
        unbindPopup: function () {
            return this._popup && ((this._popup = null), this.off("click", this.togglePopup, this).off("remove", this.closePopup, this).off("move", this._movePopup, this), (this._popupHandlersAdded = !1)), this;
        },
        getPopup: function () {
            return this._popup;
        },
        _movePopup: function (a) {
            this._popup.setLatLng(a.latlng);
        },
    });
    b.LayerGroup = b.Class.extend({
        initialize: function (a) {
            this._layers = {};
            var d, b;
            if (a) for (d = 0, b = a.length; b > d; d++) this.addLayer(a[d]);
        },
        addLayer: function (a) {
            var d = this.getLayerId(a);
            return (this._layers[d] = a), this._map && this._map.addLayer(a), this;
        },
        removeLayer: function (a) {
            a = a in this._layers ? a : this.getLayerId(a);
            return this._map && this._layers[a] && this._map.removeLayer(this._layers[a]), delete this._layers[a], this;
        },
        hasLayer: function (a) {
            return a ? a in this._layers || this.getLayerId(a) in this._layers : !1;
        },
        clearLayers: function () {
            return this.eachLayer(this.removeLayer, this), this;
        },
        invoke: function (a) {
            var d,
                b,
                c = Array.prototype.slice.call(arguments, 1);
            for (d in this._layers) (b = this._layers[d]), b[a] && b[a].apply(b, c);
            return this;
        },
        onAdd: function (a) {
            this._map = a;
            this.eachLayer(a.addLayer, a);
        },
        onRemove: function (a) {
            this.eachLayer(a.removeLayer, a);
            this._map = null;
        },
        addTo: function (a) {
            return a.addLayer(this), this;
        },
        eachLayer: function (a, d) {
            for (var b in this._layers) a.call(d, this._layers[b]);
            return this;
        },
        getLayer: function (a) {
            return this._layers[a];
        },
        getLayers: function () {
            var a = [],
                d;
            for (d in this._layers) a.push(this._layers[d]);
            return a;
        },
        setZIndex: function (a) {
            return this.invoke("setZIndex", a);
        },
        getLayerId: function (a) {
            return b.stamp(a);
        },
    });
    b.layerGroup = function (a) {
        return new b.LayerGroup(a);
    };
    b.FeatureGroup = b.LayerGroup.extend({
        includes: b.Mixin.Events,
        statics: { EVENTS: "click dblclick mouseover mouseout mousemove contextmenu popupopen popupclose" },
        addLayer: function (a) {
            return this.hasLayer(a)
                ? this
                : ("on" in a && a.on(b.FeatureGroup.EVENTS, this._propagateEvent, this),
                  b.LayerGroup.prototype.addLayer.call(this, a),
                  this._popupContent && a.bindPopup && a.bindPopup(this._popupContent, this._popupOptions),
                  this.fire("layeradd", { layer: a }));
        },
        removeLayer: function (a) {
            return this.hasLayer(a)
                ? (a in this._layers && (a = this._layers[a]),
                  a.off(b.FeatureGroup.EVENTS, this._propagateEvent, this),
                  b.LayerGroup.prototype.removeLayer.call(this, a),
                  this._popupContent && this.invoke("unbindPopup"),
                  this.fire("layerremove", { layer: a }))
                : this;
        },
        bindPopup: function (a, d) {
            return (this._popupContent = a), (this._popupOptions = d), this.invoke("bindPopup", a, d);
        },
        openPopup: function (a) {
            for (var d in this._layers) {
                this._layers[d].openPopup(a);
                break;
            }
            return this;
        },
        setStyle: function (a) {
            return this.invoke("setStyle", a);
        },
        bringToFront: function () {
            return this.invoke("bringToFront");
        },
        bringToBack: function () {
            return this.invoke("bringToBack");
        },
        getBounds: function () {
            var a = new b.LatLngBounds();
            return (
                this.eachLayer(function (d) {
                    a.extend(d instanceof b.Marker ? d.getLatLng() : d.getBounds());
                }),
                a
            );
        },
        _propagateEvent: function (a) {
            a = b.extend({ layer: a.target, target: this }, a);
            this.fire(a.type, a);
        },
    });
    b.featureGroup = function (a) {
        return new b.FeatureGroup(a);
    };
    b.Path = b.Class.extend({
        includes: [b.Mixin.Events],
        statics: { CLIP_PADDING: Math.max(0, Math.min(0.5, ((b.Browser.mobile ? 1280 : 2e3) / Math.max(f.outerWidth, f.outerHeight) - 1) / 2)) },
        options: { stroke: !0, color: "#0033ff", dashArray: null, lineCap: null, lineJoin: null, weight: 5, opacity: 0.5, fill: !1, fillColor: null, fillOpacity: 0.2, clickable: !0 },
        initialize: function (a) {
            b.setOptions(this, a);
        },
        onAdd: function (a) {
            this._map = a;
            this._container || (this._initElements(), this._initEvents());
            this.projectLatlngs();
            this._updatePath();
            this._container && this._map._pathRoot.appendChild(this._container);
            this.fire("add");
            a.on({ viewreset: this.projectLatlngs, moveend: this._updatePath }, this);
        },
        addTo: function (a) {
            return a.addLayer(this), this;
        },
        onRemove: function (a) {
            a._pathRoot.removeChild(this._container);
            this.fire("remove");
            this._map = null;
            b.Browser.vml && ((this._container = null), (this._stroke = null), (this._fill = null));
            a.off({ viewreset: this.projectLatlngs, moveend: this._updatePath }, this);
        },
        projectLatlngs: function () {},
        setStyle: function (a) {
            return b.setOptions(this, a), this._container && this._updateStyle(), this;
        },
        redraw: function () {
            return this._map && (this.projectLatlngs(), this._updatePath()), this;
        },
    });
    b.Map.include({
        _updatePathViewport: function () {
            var a = b.Path.CLIP_PADDING,
                d = this.getSize(),
                c = b.DomUtil.getPosition(this._mapPane).multiplyBy(-1)._subtract(d.multiplyBy(a)._round()),
                a = c.add(d.multiplyBy(1 + 2 * a)._round());
            this._pathViewport = new b.Bounds(c, a);
        },
    });
    b.Path.SVG_NS = "http://www.w3.org/2000/svg";
    b.Browser.svg = !(!g.createElementNS || !g.createElementNS(b.Path.SVG_NS, "svg").createSVGRect);
    b.Path = b.Path.extend({
        statics: { SVG: b.Browser.svg },
        bringToFront: function () {
            var a = this._map._pathRoot,
                d = this._container;
            return d && a.lastChild !== d && a.appendChild(d), this;
        },
        bringToBack: function () {
            var a = this._map._pathRoot,
                d = this._container,
                b = a.firstChild;
            return d && b !== d && a.insertBefore(d, b), this;
        },
        getPathString: function () {},
        _createElement: function (a) {
            return g.createElementNS(b.Path.SVG_NS, a);
        },
        _initElements: function () {
            this._map._initPathRoot();
            this._initPath();
            this._initStyle();
        },
        _initPath: function () {
            this._container = this._createElement("g");
            this._path = this._createElement("path");
            this.options.className && b.DomUtil.addClass(this._path, this.options.className);
            this._container.appendChild(this._path);
        },
        _initStyle: function () {
            this.options.stroke && (this._path.setAttribute("stroke-linejoin", "round"), this._path.setAttribute("stroke-linecap", "round"));
            this.options.fill && this._path.setAttribute("fill-rule", "evenodd");
            this.options.pointerEvents && this._path.setAttribute("pointer-events", this.options.pointerEvents);
            this.options.clickable || this.options.pointerEvents || this._path.setAttribute("pointer-events", "none");
            this._updateStyle();
        },
        _updateStyle: function () {
            this.options.stroke
                ? (this._path.setAttribute("stroke", this.options.color),
                  this._path.setAttribute("stroke-opacity", this.options.opacity),
                  this._path.setAttribute("stroke-width", this.options.weight),
                  this.options.dashArray ? this._path.setAttribute("stroke-dasharray", this.options.dashArray) : this._path.removeAttribute("stroke-dasharray"),
                  this.options.lineCap && this._path.setAttribute("stroke-linecap", this.options.lineCap),
                  this.options.lineJoin && this._path.setAttribute("stroke-linejoin", this.options.lineJoin))
                : this._path.setAttribute("stroke", "none");
            this.options.fill ? (this._path.setAttribute("fill", this.options.fillColor || this.options.color), this._path.setAttribute("fill-opacity", this.options.fillOpacity)) : this._path.setAttribute("fill", "none");
        },
        _updatePath: function () {
            var a = this.getPathString();
            a || (a = "M0 0");
            this._path.setAttribute("d", a);
        },
        _initEvents: function () {
            if (this.options.clickable) {
                (!b.Browser.svg && b.Browser.vml) || b.DomUtil.addClass(this._path, "leaflet-clickable");
                b.DomEvent.on(this._container, "click", this._onMouseClick, this);
                for (var a = "dblclick mousedown mouseover mouseout mousemove contextmenu".split(" "), d = 0; d < a.length; d++) b.DomEvent.on(this._container, a[d], this._fireMouseEvent, this);
            }
        },
        _onMouseClick: function (a) {
            (this._map.dragging && this._map.dragging.moved()) || this._fireMouseEvent(a);
        },
        _fireMouseEvent: function (a) {
            if (this.hasEventListeners(a.type)) {
                var d = this._map,
                    c = d.mouseEventToContainerPoint(a),
                    e = d.containerPointToLayerPoint(c),
                    d = d.layerPointToLatLng(e);
                this.fire(a.type, { latlng: d, layerPoint: e, containerPoint: c, originalEvent: a });
                "contextmenu" === a.type && b.DomEvent.preventDefault(a);
                "mousemove" !== a.type && b.DomEvent.stopPropagation(a);
            }
        },
    });
    b.Map.include({
        _initPathRoot: function () {
            this._pathRoot ||
                ((this._pathRoot = b.Path.prototype._createElement("svg")),
                this._panes.overlayPane.appendChild(this._pathRoot),
                this.options.zoomAnimation && b.Browser.any3d
                    ? (b.DomUtil.addClass(this._pathRoot, "leaflet-zoom-animated"), this.on({ zoomanim: this._animatePathZoom, zoomend: this._endPathZoom }))
                    : b.DomUtil.addClass(this._pathRoot, "leaflet-zoom-hide"),
                this.on("moveend", this._updateSvgViewport),
                this._updateSvgViewport());
        },
        _animatePathZoom: function (a) {
            var d = this.getZoomScale(a.zoom);
            a = this._getCenterOffset(a.center)._multiplyBy(-d)._add(this._pathViewport.min);
            this._pathRoot.style[b.DomUtil.TRANSFORM] = b.DomUtil.getTranslateString(a) + " scale(" + d + ") ";
            this._pathZooming = !0;
        },
        _endPathZoom: function () {
            this._pathZooming = !1;
        },
        _updateSvgViewport: function () {
            if (!this._pathZooming) {
                this._updatePathViewport();
                var a = this._pathViewport,
                    d = a.min,
                    c = a.max,
                    a = c.x - d.x,
                    c = c.y - d.y,
                    e = this._pathRoot,
                    f = this._panes.overlayPane;
                b.Browser.mobileWebkit && f.removeChild(e);
                b.DomUtil.setPosition(e, d);
                e.setAttribute("width", a);
                e.setAttribute("height", c);
                e.setAttribute("viewBox", [d.x, d.y, a, c].join(" "));
                b.Browser.mobileWebkit && f.appendChild(e);
            }
        },
    });
    b.Path.include({
        bindPopup: function (a, d) {
            return (
                a instanceof b.Popup ? (this._popup = a) : ((!this._popup || d) && (this._popup = new b.Popup(d, this)), this._popup.setContent(a)),
                this._popupHandlersAdded || (this.on("click", this._openPopup, this).on("remove", this.closePopup, this), (this._popupHandlersAdded = !0)),
                this
            );
        },
        unbindPopup: function () {
            return this._popup && ((this._popup = null), this.off("click", this._openPopup).off("remove", this.closePopup), (this._popupHandlersAdded = !1)), this;
        },
        openPopup: function (a) {
            return this._popup && ((a = a || this._latlng || this._latlngs[Math.floor(this._latlngs.length / 2)]), this._openPopup({ latlng: a })), this;
        },
        closePopup: function () {
            return this._popup && this._popup._close(), this;
        },
        _openPopup: function (a) {
            this._popup.setLatLng(a.latlng);
            this._map.openPopup(this._popup);
        },
    });
    b.Browser.vml =
        !b.Browser.svg &&
        (function () {
            try {
                var a = g.createElement("div");
                a.innerHTML = '<v:shape adj="1"/>';
                var d = a.firstChild;
                return (d.style.behavior = "url(#default#VML)"), d && "object" == typeof d.adj;
            } catch (b) {
                return !1;
            }
        })();
    b.Path =
        b.Browser.svg || !b.Browser.vml
            ? b.Path
            : b.Path.extend({
                  statics: { VML: !0, CLIP_PADDING: 0.02 },
                  _createElement: (function () {
                      try {
                          return (
                              g.namespaces.add("lvml", "urn:schemas-microsoft-com:vml"),
                              function (a) {
                                  return g.createElement("<lvml:" + a + ' class="lvml">');
                              }
                          );
                      } catch (a) {
                          return function (a) {
                              return g.createElement("<" + a + ' xmlns="urn:schemas-microsoft.com:vml" class="lvml">');
                          };
                      }
                  })(),
                  _initPath: function () {
                      var a = (this._container = this._createElement("shape"));
                      b.DomUtil.addClass(a, "leaflet-vml-shape" + (this.options.className ? " " + this.options.className : ""));
                      this.options.clickable && b.DomUtil.addClass(a, "leaflet-clickable");
                      a.coordsize = "1 1";
                      this._path = this._createElement("path");
                      a.appendChild(this._path);
                      this._map._pathRoot.appendChild(a);
                  },
                  _initStyle: function () {
                      this._updateStyle();
                  },
                  _updateStyle: function () {
                      var a = this._stroke,
                          d = this._fill,
                          c = this.options,
                          e = this._container;
                      e.stroked = c.stroke;
                      e.filled = c.fill;
                      c.stroke
                          ? (a || ((a = this._stroke = this._createElement("stroke")), (a.endcap = "round"), e.appendChild(a)),
                            (a.weight = c.weight + "px"),
                            (a.color = c.color),
                            (a.opacity = c.opacity),
                            (a.dashStyle = c.dashArray ? (b.Util.isArray(c.dashArray) ? c.dashArray.join(" ") : c.dashArray.replace(/( *, *)/g, " ")) : ""),
                            c.lineCap && (a.endcap = c.lineCap.replace("butt", "flat")),
                            c.lineJoin && (a.joinstyle = c.lineJoin))
                          : a && (e.removeChild(a), (this._stroke = null));
                      c.fill ? (d || ((d = this._fill = this._createElement("fill")), e.appendChild(d)), (d.color = c.fillColor || c.color), (d.opacity = c.fillOpacity)) : d && (e.removeChild(d), (this._fill = null));
                  },
                  _updatePath: function () {
                      var a = this._container.style;
                      a.display = "none";
                      this._path.v = this.getPathString() + " ";
                      a.display = "";
                  },
              });
    b.Map.include(
        b.Browser.svg || !b.Browser.vml
            ? {}
            : {
                  _initPathRoot: function () {
                      if (!this._pathRoot) {
                          var a = (this._pathRoot = g.createElement("div"));
                          a.className = "leaflet-vml-container";
                          this._panes.overlayPane.appendChild(a);
                          this.on("moveend", this._updatePathViewport);
                          this._updatePathViewport();
                      }
                  },
              }
    );
    b.Browser.canvas = !!g.createElement("canvas").getContext;
    b.Path =
        (b.Path.SVG && !f.L_PREFER_CANVAS) || !b.Browser.canvas
            ? b.Path
            : b.Path.extend({
                  statics: { CANVAS: !0, SVG: !1 },
                  redraw: function () {
                      return this._map && (this.projectLatlngs(), this._requestUpdate()), this;
                  },
                  setStyle: function (a) {
                      return b.setOptions(this, a), this._map && (this._updateStyle(), this._requestUpdate()), this;
                  },
                  onRemove: function (a) {
                      a.off("viewreset", this.projectLatlngs, this).off("moveend", this._updatePath, this);
                      this.options.clickable && (this._map.off("click", this._onClick, this), this._map.off("mousemove", this._onMouseMove, this));
                      this._requestUpdate();
                      this.fire("remove");
                      this._map = null;
                  },
                  _requestUpdate: function () {
                      this._map && !b.Path._updateRequest && (b.Path._updateRequest = b.Util.requestAnimFrame(this._fireMapMoveEnd, this._map));
                  },
                  _fireMapMoveEnd: function () {
                      b.Path._updateRequest = null;
                      this.fire("moveend");
                  },
                  _initElements: function () {
                      this._map._initPathRoot();
                      this._ctx = this._map._canvasCtx;
                  },
                  _updateStyle: function () {
                      var a = this.options;
                      a.stroke && ((this._ctx.lineWidth = a.weight), (this._ctx.strokeStyle = a.color));
                      a.fill && (this._ctx.fillStyle = a.fillColor || a.color);
                  },
                  _drawPath: function () {
                      var a, d, c, e, f, k;
                      this._ctx.beginPath();
                      a = 0;
                      for (c = this._parts.length; c > a; a++) {
                          d = 0;
                          for (e = this._parts[a].length; e > d; d++) (f = this._parts[a][d]), (k = (0 === d ? "move" : "line") + "To"), this._ctx[k](f.x, f.y);
                          this instanceof b.Polygon && this._ctx.closePath();
                      }
                  },
                  _checkIfEmpty: function () {
                      return !this._parts.length;
                  },
                  _updatePath: function () {
                      if (!this._checkIfEmpty()) {
                          var a = this._ctx,
                              d = this.options;
                          this._drawPath();
                          a.save();
                          this._updateStyle();
                          d.fill && ((a.globalAlpha = d.fillOpacity), a.fill());
                          d.stroke && ((a.globalAlpha = d.opacity), a.stroke());
                          a.restore();
                      }
                  },
                  _initEvents: function () {
                      this.options.clickable && (this._map.on("mousemove", this._onMouseMove, this), this._map.on("click", this._onClick, this));
                  },
                  _onClick: function (a) {
                      this._containsPoint(a.layerPoint) && this.fire("click", a);
                  },
                  _onMouseMove: function (a) {
                      this._map &&
                          !this._map._animatingZoom &&
                          (this._containsPoint(a.layerPoint)
                              ? ((this._ctx.canvas.style.cursor = "pointer"), (this._mouseInside = !0), this.fire("mouseover", a))
                              : this._mouseInside && ((this._ctx.canvas.style.cursor = ""), (this._mouseInside = !1), this.fire("mouseout", a)));
                  },
              });
    b.Map.include(
        (b.Path.SVG && !f.L_PREFER_CANVAS) || !b.Browser.canvas
            ? {}
            : {
                  _initPathRoot: function () {
                      var a,
                          d = this._pathRoot;
                      d ||
                          ((d = this._pathRoot = g.createElement("canvas")),
                          (d.style.position = "absolute"),
                          (a = this._canvasCtx = d.getContext("2d")),
                          (a.lineCap = "round"),
                          (a.lineJoin = "round"),
                          this._panes.overlayPane.appendChild(d),
                          this.options.zoomAnimation && ((this._pathRoot.className = "leaflet-zoom-animated"), this.on("zoomanim", this._animatePathZoom), this.on("zoomend", this._endPathZoom)),
                          this.on("moveend", this._updateCanvasViewport),
                          this._updateCanvasViewport());
                  },
                  _updateCanvasViewport: function () {
                      if (!this._pathZooming) {
                          this._updatePathViewport();
                          var a = this._pathViewport,
                              d = a.min,
                              a = a.max.subtract(d),
                              c = this._pathRoot;
                          b.DomUtil.setPosition(c, d);
                          c.width = a.x;
                          c.height = a.y;
                          c.getContext("2d").translate(-d.x, -d.y);
                      }
                  },
              }
    );
    b.LineUtil = {
        simplify: function (a, d) {
            if (!d || !a.length) return a.slice();
            d *= d;
            return (a = this._reducePoints(a, d)), this._simplifyDP(a, d);
        },
        pointToSegmentDistance: function (a, d, b) {
            return Math.sqrt(this._sqClosestPointOnSegment(a, d, b, !0));
        },
        closestPointOnSegment: function (a, d, b) {
            return this._sqClosestPointOnSegment(a, d, b);
        },
        _simplifyDP: function (a, d) {
            var b = a.length,
                e = new (typeof Uint8Array != c + "" ? Uint8Array : Array)(b);
            e[0] = e[b - 1] = 1;
            this._simplifyDPStep(a, e, d, 0, b - 1);
            var f = [];
            for (d = 0; b > d; d++) e[d] && f.push(a[d]);
            return f;
        },
        _simplifyDPStep: function (a, d, b, c, e) {
            var f,
                l,
                m,
                p = 0;
            for (l = c + 1; e - 1 >= l; l++) (m = this._sqClosestPointOnSegment(a[l], a[c], a[e], !0)), m > p && ((f = l), (p = m));
            p > b && ((d[f] = 1), this._simplifyDPStep(a, d, b, c, f), this._simplifyDPStep(a, d, b, f, e));
        },
        _reducePoints: function (a, d) {
            for (var b = [a[0]], c = 1, e = 0, f = a.length; f > c; c++) this._sqDist(a[c], a[e]) > d && (b.push(a[c]), (e = c));
            return f - 1 > e && b.push(a[f - 1]), b;
        },
        clipSegment: function (a, d, b, c) {
            var e,
                f,
                l = c ? this._lastCode : this._getBitCode(a, b),
                m = this._getBitCode(d, b);
            for (this._lastCode = m; ; ) {
                if (!(l | m)) return [a, d];
                if (l & m) return !1;
                c = l || m;
                e = this._getEdgeIntersection(a, d, c, b);
                f = this._getBitCode(e, b);
                c === l ? ((a = e), (l = f)) : ((d = e), (m = f));
            }
        },
        _getEdgeIntersection: function (a, d, c, e) {
            var f = d.x - a.x;
            d = d.y - a.y;
            var k = e.min;
            e = e.max;
            return 8 & c
                ? new b.Point(a.x + (f * (e.y - a.y)) / d, e.y)
                : 4 & c
                ? new b.Point(a.x + (f * (k.y - a.y)) / d, k.y)
                : 2 & c
                ? new b.Point(e.x, a.y + (d * (e.x - a.x)) / f)
                : 1 & c
                ? new b.Point(k.x, a.y + (d * (k.x - a.x)) / f)
                : void 0;
        },
        _getBitCode: function (a, d) {
            var b = 0;
            return a.x < d.min.x ? (b |= 1) : a.x > d.max.x && (b |= 2), a.y < d.min.y ? (b |= 4) : a.y > d.max.y && (b |= 8), b;
        },
        _sqDist: function (a, d) {
            var b = d.x - a.x;
            a = d.y - a.y;
            return b * b + a * a;
        },
        _sqClosestPointOnSegment: function (a, d, c, e) {
            var f,
                k = d.x;
            d = d.y;
            var l = c.x - k,
                m = c.y - d,
                p = l * l + m * m;
            return 0 < p && ((f = ((a.x - k) * l + (a.y - d) * m) / p), 1 < f ? ((k = c.x), (d = c.y)) : 0 < f && ((k += l * f), (d += m * f))), (l = a.x - k), (m = a.y - d), e ? l * l + m * m : new b.Point(k, d);
        },
    };
    b.Polyline = b.Path.extend({
        initialize: function (a, d) {
            b.Path.prototype.initialize.call(this, d);
            this._latlngs = this._convertLatLngs(a);
        },
        options: { smoothFactor: 1, noClip: !1 },
        projectLatlngs: function () {
            this._originalPoints = [];
            for (var a = 0, b = this._latlngs.length; b > a; a++) this._originalPoints[a] = this._map.latLngToLayerPoint(this._latlngs[a]);
        },
        getPathString: function () {
            for (var a = 0, b = this._parts.length, c = ""; b > a; a++) c += this._getPathPartStr(this._parts[a]);
            return c;
        },
        getLatLngs: function () {
            return this._latlngs;
        },
        setLatLngs: function (a) {
            return (this._latlngs = this._convertLatLngs(a)), this.redraw();
        },
        addLatLng: function (a) {
            return this._latlngs.push(b.latLng(a)), this.redraw();
        },
        spliceLatLngs: function () {
            var a = [].splice.apply(this._latlngs, arguments);
            return this._convertLatLngs(this._latlngs, !0), this.redraw(), a;
        },
        closestLayerPoint: function (a) {
            for (var d, c, e = 1 / 0, f = this._parts, k = null, l = 0, m = f.length; m > l; l++)
                for (var p = f[l], g = 1, r = p.length; r > g; g++) {
                    d = p[g - 1];
                    c = p[g];
                    var t = b.LineUtil._sqClosestPointOnSegment(a, d, c, !0);
                    e > t && ((e = t), (k = b.LineUtil._sqClosestPointOnSegment(a, d, c)));
                }
            return k && (k.distance = Math.sqrt(e)), k;
        },
        getBounds: function () {
            return new b.LatLngBounds(this.getLatLngs());
        },
        _convertLatLngs: function (a, d) {
            var c,
                e = d ? a : [];
            d = 0;
            for (c = a.length; c > d; d++) {
                if (b.Util.isArray(a[d]) && "number" != typeof a[d][0]) return;
                e[d] = b.latLng(a[d]);
            }
            return e;
        },
        _initEvents: function () {
            b.Path.prototype._initEvents.call(this);
        },
        _getPathPartStr: function (a) {
            for (var d, c = b.Path.VML, e = 0, f = a.length, k = ""; f > e; e++) (d = a[e]), c && d._round(), (k += (e ? "L" : "M") + d.x + " " + d.y);
            return k;
        },
        _clipPoints: function () {
            var a,
                d,
                c,
                e = this._originalPoints,
                f = e.length;
            if (this.options.noClip) return void (this._parts = [e]);
            var k = (this._parts = []),
                l = this._map._pathViewport,
                m = b.LineUtil;
            for (d = a = 0; f - 1 > a; a++) (c = m.clipSegment(e[a], e[a + 1], l, a)) && ((k[d] = k[d] || []), k[d].push(c[0]), (c[1] !== e[a + 1] || a === f - 2) && (k[d].push(c[1]), d++));
        },
        _simplifyPoints: function () {
            for (var a = this._parts, d = b.LineUtil, c = 0, e = a.length; e > c; c++) a[c] = d.simplify(a[c], this.options.smoothFactor);
        },
        _updatePath: function () {
            this._map && (this._clipPoints(), this._simplifyPoints(), b.Path.prototype._updatePath.call(this));
        },
    });
    b.polyline = function (a, d) {
        return new b.Polyline(a, d);
    };
    b.PolyUtil = {};
    b.PolyUtil.clipPolygon = function (a, d) {
        var c,
            e,
            f,
            k,
            l,
            m,
            p,
            g,
            r = [1, 4, 2, 8],
            t = b.LineUtil;
        e = 0;
        for (m = a.length; m > e; e++) a[e]._code = t._getBitCode(a[e], d);
        for (k = 0; 4 > k; k++) {
            p = r[k];
            c = [];
            e = 0;
            m = a.length;
            for (f = m - 1; m > e; f = e++)
                (l = a[e]),
                    (f = a[f]),
                    l._code & p
                        ? f._code & p || ((g = t._getEdgeIntersection(f, l, p, d)), (g._code = t._getBitCode(g, d)), c.push(g))
                        : (f._code & p && ((g = t._getEdgeIntersection(f, l, p, d)), (g._code = t._getBitCode(g, d)), c.push(g)), c.push(l));
            a = c;
        }
        return a;
    };
    b.Polygon = b.Polyline.extend({
        options: { fill: !0 },
        initialize: function (a, d) {
            b.Polyline.prototype.initialize.call(this, a, d);
            this._initWithHoles(a);
        },
        _initWithHoles: function (a) {
            var d, c;
            if (a && b.Util.isArray(a[0]) && "number" != typeof a[0][0])
                for (this._latlngs = this._convertLatLngs(a[0]), this._holes = a.slice(1), a = 0, d = this._holes.length; d > a; a++) (c = this._holes[a] = this._convertLatLngs(this._holes[a])), c[0].equals(c[c.length - 1]) && c.pop();
            a = this._latlngs;
            2 <= a.length && a[0].equals(a[a.length - 1]) && a.pop();
        },
        projectLatlngs: function () {
            if ((b.Polyline.prototype.projectLatlngs.call(this), (this._holePoints = []), this._holes)) {
                var a, d, c, e;
                a = 0;
                for (c = this._holes.length; c > a; a++) for (this._holePoints[a] = [], d = 0, e = this._holes[a].length; e > d; d++) this._holePoints[a][d] = this._map.latLngToLayerPoint(this._holes[a][d]);
            }
        },
        setLatLngs: function (a) {
            return a && b.Util.isArray(a[0]) && "number" != typeof a[0][0] ? (this._initWithHoles(a), this.redraw()) : b.Polyline.prototype.setLatLngs.call(this, a);
        },
        _clipPoints: function () {
            var a = [];
            if (((this._parts = [this._originalPoints].concat(this._holePoints)), !this.options.noClip)) {
                for (var d = 0, c = this._parts.length; c > d; d++) {
                    var e = b.PolyUtil.clipPolygon(this._parts[d], this._map._pathViewport);
                    e.length && a.push(e);
                }
                this._parts = a;
            }
        },
        _getPathPartStr: function (a) {
            return b.Polyline.prototype._getPathPartStr.call(this, a) + (b.Browser.svg ? "z" : "x");
        },
    });
    b.polygon = function (a, d) {
        return new b.Polygon(a, d);
    };
    (function () {
        function a(a) {
            return b.FeatureGroup.extend({
                initialize: function (a, b) {
                    this._layers = {};
                    this._options = b;
                    this.setLatLngs(a);
                },
                setLatLngs: function (b) {
                    var c = 0,
                        e = b.length;
                    for (
                        this.eachLayer(function (a) {
                            e > c ? a.setLatLngs(b[c++]) : this.removeLayer(a);
                        }, this);
                        e > c;

                    )
                        this.addLayer(new a(b[c++], this._options));
                    return this;
                },
                getLatLngs: function () {
                    var a = [];
                    return (
                        this.eachLayer(function (b) {
                            a.push(b.getLatLngs());
                        }),
                        a
                    );
                },
            });
        }
        b.MultiPolyline = a(b.Polyline);
        b.MultiPolygon = a(b.Polygon);
        b.multiPolyline = function (a, c) {
            return new b.MultiPolyline(a, c);
        };
        b.multiPolygon = function (a, c) {
            return new b.MultiPolygon(a, c);
        };
    })();
    b.Rectangle = b.Polygon.extend({
        initialize: function (a, d) {
            b.Polygon.prototype.initialize.call(this, this._boundsToLatLngs(a), d);
        },
        setBounds: function (a) {
            this.setLatLngs(this._boundsToLatLngs(a));
        },
        _boundsToLatLngs: function (a) {
            return (a = b.latLngBounds(a)), [a.getSouthWest(), a.getNorthWest(), a.getNorthEast(), a.getSouthEast()];
        },
    });
    b.rectangle = function (a, d) {
        return new b.Rectangle(a, d);
    };
    b.Circle = b.Path.extend({
        initialize: function (a, d, c) {
            b.Path.prototype.initialize.call(this, c);
            this._latlng = b.latLng(a);
            this._mRadius = d;
        },
        options: { fill: !0 },
        setLatLng: function (a) {
            return (this._latlng = b.latLng(a)), this.redraw();
        },
        setRadius: function (a) {
            return (this._mRadius = a), this.redraw();
        },
        projectLatlngs: function () {
            var a = this._getLngRadius(),
                b = this._latlng,
                a = this._map.latLngToLayerPoint([b.lat, b.lng - a]);
            this._point = this._map.latLngToLayerPoint(b);
            this._radius = Math.max(this._point.x - a.x, 1);
        },
        getBounds: function () {
            var a = this._getLngRadius(),
                d = (this._mRadius / 40075017) * 360,
                c = this._latlng;
            return new b.LatLngBounds([c.lat - d, c.lng - a], [c.lat + d, c.lng + a]);
        },
        getLatLng: function () {
            return this._latlng;
        },
        getPathString: function () {
            var a = this._point,
                d = this._radius;
            return this._checkIfEmpty()
                ? ""
                : b.Browser.svg
                ? "M" + a.x + "," + (a.y - d) + "A" + d + "," + d + ",0,1,1," + (a.x - 0.1) + "," + (a.y - d) + " z"
                : (a._round(), (d = Math.round(d)), "AL " + a.x + "," + a.y + " " + d + "," + d + " 0,23592600");
        },
        getRadius: function () {
            return this._mRadius;
        },
        _getLatRadius: function () {
            return (this._mRadius / 40075017) * 360;
        },
        _getLngRadius: function () {
            return this._getLatRadius() / Math.cos(b.LatLng.DEG_TO_RAD * this._latlng.lat);
        },
        _checkIfEmpty: function () {
            if (!this._map) return !1;
            var a = this._map._pathViewport,
                b = this._radius,
                c = this._point;
            return c.x - b > a.max.x || c.y - b > a.max.y || c.x + b < a.min.x || c.y + b < a.min.y;
        },
    });
    b.circle = function (a, d, c) {
        return new b.Circle(a, d, c);
    };
    b.CircleMarker = b.Circle.extend({
        options: { radius: 10, weight: 2 },
        initialize: function (a, d) {
            b.Circle.prototype.initialize.call(this, a, null, d);
            this._radius = this.options.radius;
        },
        projectLatlngs: function () {
            this._point = this._map.latLngToLayerPoint(this._latlng);
        },
        _updateStyle: function () {
            b.Circle.prototype._updateStyle.call(this);
            this.setRadius(this.options.radius);
        },
        setLatLng: function (a) {
            return b.Circle.prototype.setLatLng.call(this, a), this._popup && this._popup._isOpen && this._popup.setLatLng(a), this;
        },
        setRadius: function (a) {
            return (this.options.radius = this._radius = a), this.redraw();
        },
        getRadius: function () {
            return this._radius;
        },
    });
    b.circleMarker = function (a, d) {
        return new b.CircleMarker(a, d);
    };
    b.Polyline.include(
        b.Path.CANVAS
            ? {
                  _containsPoint: function (a, d) {
                      var c,
                          e,
                          f,
                          k,
                          l,
                          m,
                          g,
                          r = this.options.weight / 2;
                      b.Browser.touch && (r += 10);
                      c = 0;
                      for (k = this._parts.length; k > c; c++) for (g = this._parts[c], e = 0, l = g.length, f = l - 1; l > e; f = e++) if ((d || 0 !== e) && ((m = b.LineUtil.pointToSegmentDistance(a, g[f], g[e])), r >= m)) return !0;
                      return !1;
                  },
              }
            : {}
    );
    b.Polygon.include(
        b.Path.CANVAS
            ? {
                  _containsPoint: function (a) {
                      var d,
                          c,
                          e,
                          f,
                          k,
                          l,
                          m,
                          g = !1;
                      if (b.Polyline.prototype._containsPoint.call(this, a, !0)) return !0;
                      f = 0;
                      for (l = this._parts.length; l > f; f++)
                          for (d = this._parts[f], k = 0, m = d.length, e = m - 1; m > k; e = k++) (c = d[k]), (e = d[e]), c.y > a.y != e.y > a.y && a.x < ((e.x - c.x) * (a.y - c.y)) / (e.y - c.y) + c.x && (g = !g);
                      return g;
                  },
              }
            : {}
    );
    b.Circle.include(
        b.Path.CANVAS
            ? {
                  _drawPath: function () {
                      var a = this._point;
                      this._ctx.beginPath();
                      this._ctx.arc(a.x, a.y, this._radius, 0, 2 * Math.PI, !1);
                  },
                  _containsPoint: function (a) {
                      var b = this.options.stroke ? this.options.weight / 2 : 0;
                      return a.distanceTo(this._point) <= this._radius + b;
                  },
              }
            : {}
    );
    b.CircleMarker.include(
        b.Path.CANVAS
            ? {
                  _updateStyle: function () {
                      b.Path.prototype._updateStyle.call(this);
                  },
              }
            : {}
    );
    b.GeoJSON = b.FeatureGroup.extend({
        initialize: function (a, d) {
            b.setOptions(this, d);
            this._layers = {};
            a && this.addData(a);
        },
        addData: function (a) {
            var d,
                c,
                e = b.Util.isArray(a) ? a : a.features;
            if (e) {
                a = 0;
                for (d = e.length; d > a; a++) (c = e[a]), (c.geometries || c.geometry || c.features || c.coordinates) && this.addData(e[a]);
                return this;
            }
            e = this.options;
            if (!e.filter || e.filter(a))
                return (
                    (d = b.GeoJSON.geometryToLayer(a, e.pointToLayer, e.coordsToLatLng, e)),
                    (d.feature = b.GeoJSON.asFeature(a)),
                    (d.defaultOptions = d.options),
                    this.resetStyle(d),
                    e.onEachFeature && e.onEachFeature(a, d),
                    this.addLayer(d)
                );
        },
        resetStyle: function (a) {
            var d = this.options.style;
            d && (b.Util.extend(a.options, a.defaultOptions), this._setLayerStyle(a, d));
        },
        setStyle: function (a) {
            this.eachLayer(function (b) {
                this._setLayerStyle(b, a);
            }, this);
        },
        _setLayerStyle: function (a, b) {
            "function" == typeof b && (b = b(a.feature));
            a.setStyle && a.setStyle(b);
        },
    });
    b.extend(b.GeoJSON, {
        geometryToLayer: function (a, d, c, e) {
            var f,
                k,
                l,
                m = "Feature" === a.type ? a.geometry : a,
                g = m.coordinates,
                r = [];
            switch (((c = c || this.coordsToLatLng), m.type)) {
                case "Point":
                    return (f = c(g)), d ? d(a, f) : new b.Marker(f);
                case "MultiPoint":
                    k = 0;
                    for (l = g.length; l > k; k++) (f = c(g[k])), r.push(d ? d(a, f) : new b.Marker(f));
                    return new b.FeatureGroup(r);
                case "LineString":
                    return (k = this.coordsToLatLngs(g, 0, c)), new b.Polyline(k, e);
                case "Polygon":
                    if (2 === g.length && !g[1].length) throw Error("Invalid GeoJSON object.");
                    return (k = this.coordsToLatLngs(g, 1, c)), new b.Polygon(k, e);
                case "MultiLineString":
                    return (k = this.coordsToLatLngs(g, 1, c)), new b.MultiPolyline(k, e);
                case "MultiPolygon":
                    return (k = this.coordsToLatLngs(g, 2, c)), new b.MultiPolygon(k, e);
                case "GeometryCollection":
                    k = 0;
                    for (l = m.geometries.length; l > k; k++) r.push(this.geometryToLayer({ geometry: m.geometries[k], type: "Feature", properties: a.properties }, d, c, e));
                    return new b.FeatureGroup(r);
                default:
                    throw Error("Invalid GeoJSON object.");
            }
        },
        coordsToLatLng: function (a) {
            return new b.LatLng(a[1], a[0], a[2]);
        },
        coordsToLatLngs: function (a, b, c) {
            var e,
                f,
                k,
                l = [];
            f = 0;
            for (k = a.length; k > f; f++) (e = b ? this.coordsToLatLngs(a[f], b - 1, c) : (c || this.coordsToLatLng)(a[f])), l.push(e);
            return l;
        },
        latLngToCoords: function (a) {
            var b = [a.lng, a.lat];
            return a.alt !== c && b.push(a.alt), b;
        },
        latLngsToCoords: function (a) {
            for (var d = [], c = 0, e = a.length; e > c; c++) d.push(b.GeoJSON.latLngToCoords(a[c]));
            return d;
        },
        getFeature: function (a, d) {
            return a.feature ? b.extend({}, a.feature, { geometry: d }) : b.GeoJSON.asFeature(d);
        },
        asFeature: function (a) {
            return "Feature" === a.type ? a : { type: "Feature", properties: {}, geometry: a };
        },
    });
    var r = {
        toGeoJSON: function () {
            return b.GeoJSON.getFeature(this, { type: "Point", coordinates: b.GeoJSON.latLngToCoords(this.getLatLng()) });
        },
    };
    b.Marker.include(r);
    b.Circle.include(r);
    b.CircleMarker.include(r);
    b.Polyline.include({
        toGeoJSON: function () {
            return b.GeoJSON.getFeature(this, { type: "LineString", coordinates: b.GeoJSON.latLngsToCoords(this.getLatLngs()) });
        },
    });
    b.Polygon.include({
        toGeoJSON: function () {
            var a,
                d,
                c,
                e = [b.GeoJSON.latLngsToCoords(this.getLatLngs())];
            if ((e[0].push(e[0][0]), this._holes)) for (a = 0, d = this._holes.length; d > a; a++) (c = b.GeoJSON.latLngsToCoords(this._holes[a])), c.push(c[0]), e.push(c);
            return b.GeoJSON.getFeature(this, { type: "Polygon", coordinates: e });
        },
    });
    (function () {
        function a(a) {
            return function () {
                var c = [];
                return (
                    this.eachLayer(function (a) {
                        c.push(a.toGeoJSON().geometry.coordinates);
                    }),
                    b.GeoJSON.getFeature(this, { type: a, coordinates: c })
                );
            };
        }
        b.MultiPolyline.include({ toGeoJSON: a("MultiLineString") });
        b.MultiPolygon.include({ toGeoJSON: a("MultiPolygon") });
        b.LayerGroup.include({
            toGeoJSON: function () {
                var d,
                    c = this.feature && this.feature.geometry,
                    e = [];
                if (c && "MultiPoint" === c.type) return a("MultiPoint").call(this);
                var f = c && "GeometryCollection" === c.type;
                return (
                    this.eachLayer(function (a) {
                        a.toGeoJSON && ((d = a.toGeoJSON()), e.push(f ? d.geometry : b.GeoJSON.asFeature(d)));
                    }),
                    f ? b.GeoJSON.getFeature(this, { geometries: e, type: "GeometryCollection" }) : { type: "FeatureCollection", features: e }
                );
            },
        });
    })();
    b.geoJson = function (a, d) {
        return new b.GeoJSON(a, d);
    };
    b.DomEvent = {
        addListener: function (a, d, c, e) {
            var f,
                k,
                l,
                m = b.stamp(c),
                g = "_leaflet_" + d + m;
            return a[g]
                ? this
                : ((f = function (d) {
                      return c.call(e || a, d || b.DomEvent._getEvent());
                  }),
                  b.Browser.pointer && 0 === d.indexOf("touch")
                      ? this.addPointerListener(a, d, f, m)
                      : (b.Browser.touch && "dblclick" === d && this.addDoubleTapListener && this.addDoubleTapListener(a, f, m),
                        "addEventListener" in a
                            ? "mousewheel" === d
                                ? (a.addEventListener("DOMMouseScroll", f, !1), a.addEventListener(d, f, !1))
                                : "mouseenter" === d || "mouseleave" === d
                                ? ((k = f),
                                  (l = "mouseenter" === d ? "mouseover" : "mouseout"),
                                  (f = function (d) {
                                      return b.DomEvent._checkMouse(a, d) ? k(d) : void 0;
                                  }),
                                  a.addEventListener(l, f, !1))
                                : "click" === d && b.Browser.android
                                ? ((k = f),
                                  (f = function (a) {
                                      return b.DomEvent._filterClick(a, k);
                                  }),
                                  a.addEventListener(d, f, !1))
                                : a.addEventListener(d, f, !1)
                            : "attachEvent" in a && a.attachEvent("on" + d, f),
                        (a[g] = f),
                        this));
        },
        removeListener: function (a, d, c) {
            c = b.stamp(c);
            var e = "_leaflet_" + d + c,
                f = a[e];
            return f
                ? (b.Browser.pointer && 0 === d.indexOf("touch")
                      ? this.removePointerListener(a, d, c)
                      : b.Browser.touch && "dblclick" === d && this.removeDoubleTapListener
                      ? this.removeDoubleTapListener(a, c)
                      : "removeEventListener" in a
                      ? "mousewheel" === d
                          ? (a.removeEventListener("DOMMouseScroll", f, !1), a.removeEventListener(d, f, !1))
                          : "mouseenter" === d || "mouseleave" === d
                          ? a.removeEventListener("mouseenter" === d ? "mouseover" : "mouseout", f, !1)
                          : a.removeEventListener(d, f, !1)
                      : "detachEvent" in a && a.detachEvent("on" + d, f),
                  (a[e] = null),
                  this)
                : this;
        },
        stopPropagation: function (a) {
            return a.stopPropagation ? a.stopPropagation() : (a.cancelBubble = !0), b.DomEvent._skipped(a), this;
        },
        disableScrollPropagation: function (a) {
            var d = b.DomEvent.stopPropagation;
            return b.DomEvent.on(a, "mousewheel", d).on(a, "MozMousePixelScroll", d);
        },
        disableClickPropagation: function (a) {
            for (var d = b.DomEvent.stopPropagation, c = b.Draggable.START.length - 1; 0 <= c; c--) b.DomEvent.on(a, b.Draggable.START[c], d);
            return b.DomEvent.on(a, "click", b.DomEvent._fakeStop).on(a, "dblclick", d);
        },
        preventDefault: function (a) {
            return a.preventDefault ? a.preventDefault() : (a.returnValue = !1), this;
        },
        stop: function (a) {
            return b.DomEvent.preventDefault(a).stopPropagation(a);
        },
        getMousePosition: function (a, d) {
            if (!d) return new b.Point(a.clientX, a.clientY);
            var c = d.getBoundingClientRect();
            return new b.Point(a.clientX - c.left - d.clientLeft, a.clientY - c.top - d.clientTop);
        },
        getWheelDelta: function (a) {
            var b = 0;
            return a.wheelDelta && (b = a.wheelDelta / 120), a.detail && (b = -a.detail / 3), b;
        },
        _skipEvents: {},
        _fakeStop: function (a) {
            b.DomEvent._skipEvents[a.type] = !0;
        },
        _skipped: function (a) {
            var b = this._skipEvents[a.type];
            return (this._skipEvents[a.type] = !1), b;
        },
        _checkMouse: function (a, b) {
            b = b.relatedTarget;
            if (!b) return !0;
            try {
                for (; b && b !== a; ) b = b.parentNode;
            } catch (c) {
                return !1;
            }
            return b !== a;
        },
        _getEvent: function () {
            var a = f.event;
            if (!a) for (var b = arguments.callee.caller; b && ((a = b.arguments[0]), !a || f.Event !== a.constructor); ) b = b.caller;
            return a;
        },
        _filterClick: function (a, d) {
            var c = a.timeStamp || a.originalEvent.timeStamp,
                e = b.DomEvent._lastClick && c - b.DomEvent._lastClick;
            return (e && 100 < e && 500 > e) || (a.target._simulatedClick && !a._simulated) ? void b.DomEvent.stop(a) : ((b.DomEvent._lastClick = c), d(a));
        },
    };
    b.DomEvent.on = b.DomEvent.addListener;
    b.DomEvent.off = b.DomEvent.removeListener;
    b.Draggable = b.Class.extend({
        includes: b.Mixin.Events,
        statics: {
            START: b.Browser.touch ? ["touchstart", "mousedown"] : ["mousedown"],
            END: { mousedown: "mouseup", touchstart: "touchend", pointerdown: "touchend", MSPointerDown: "touchend" },
            MOVE: { mousedown: "mousemove", touchstart: "touchmove", pointerdown: "touchmove", MSPointerDown: "touchmove" },
        },
        initialize: function (a, b) {
            this._element = a;
            this._dragStartTarget = b || a;
        },
        enable: function () {
            if (!this._enabled) {
                for (var a = b.Draggable.START.length - 1; 0 <= a; a--) b.DomEvent.on(this._dragStartTarget, b.Draggable.START[a], this._onDown, this);
                this._enabled = !0;
            }
        },
        disable: function () {
            if (this._enabled) {
                for (var a = b.Draggable.START.length - 1; 0 <= a; a--) b.DomEvent.off(this._dragStartTarget, b.Draggable.START[a], this._onDown, this);
                this._moved = this._enabled = !1;
            }
        },
        _onDown: function (a) {
            if (
                ((this._moved = !1),
                !(a.shiftKey || (1 !== a.which && 1 !== a.button && !a.touches) || (b.DomEvent.stopPropagation(a), b.Draggable._disabled || (b.DomUtil.disableImageDrag(), b.DomUtil.disableTextSelection(), this._moving))))
            ) {
                var d = a.touches ? a.touches[0] : a;
                this._startPoint = new b.Point(d.clientX, d.clientY);
                this._startPos = this._newPos = b.DomUtil.getPosition(this._element);
                b.DomEvent.on(g, b.Draggable.MOVE[a.type], this._onMove, this).on(g, b.Draggable.END[a.type], this._onUp, this);
            }
        },
        _onMove: function (a) {
            if (a.touches && 1 < a.touches.length) return void (this._moved = !0);
            var d = a.touches && 1 === a.touches.length ? a.touches[0] : a,
                d = new b.Point(d.clientX, d.clientY).subtract(this._startPoint);
            (d.x || d.y) &&
                ((b.Browser.touch && 3 > Math.abs(d.x) + Math.abs(d.y)) ||
                    (b.DomEvent.preventDefault(a),
                    this._moved ||
                        (this.fire("dragstart"),
                        (this._moved = !0),
                        (this._startPos = b.DomUtil.getPosition(this._element).subtract(d)),
                        b.DomUtil.addClass(g.body, "leaflet-dragging"),
                        (this._lastTarget = a.target || a.srcElement),
                        b.DomUtil.addClass(this._lastTarget, "leaflet-drag-target")),
                    (this._newPos = this._startPos.add(d)),
                    (this._moving = !0),
                    b.Util.cancelAnimFrame(this._animRequest),
                    (this._animRequest = b.Util.requestAnimFrame(this._updatePosition, this, !0, this._dragStartTarget))));
        },
        _updatePosition: function () {
            this.fire("predrag");
            b.DomUtil.setPosition(this._element, this._newPos);
            this.fire("drag");
        },
        _onUp: function () {
            b.DomUtil.removeClass(g.body, "leaflet-dragging");
            this._lastTarget && (b.DomUtil.removeClass(this._lastTarget, "leaflet-drag-target"), (this._lastTarget = null));
            for (var a in b.Draggable.MOVE) b.DomEvent.off(g, b.Draggable.MOVE[a], this._onMove).off(g, b.Draggable.END[a], this._onUp);
            b.DomUtil.enableImageDrag();
            b.DomUtil.enableTextSelection();
            this._moved && this._moving && (b.Util.cancelAnimFrame(this._animRequest), this.fire("dragend", { distance: this._newPos.distanceTo(this._startPos) }));
            this._moving = !1;
        },
    });
    b.Handler = b.Class.extend({
        initialize: function (a) {
            this._map = a;
        },
        enable: function () {
            this._enabled || ((this._enabled = !0), this.addHooks());
        },
        disable: function () {
            this._enabled && ((this._enabled = !1), this.removeHooks());
        },
        enabled: function () {
            return !!this._enabled;
        },
    });
    b.Map.mergeOptions({ dragging: !0, inertia: !b.Browser.android23, inertiaDeceleration: 3400, inertiaMaxSpeed: 1 / 0, inertiaThreshold: b.Browser.touch ? 32 : 18, easeLinearity: 0.25, worldCopyJump: !1 });
    b.Map.Drag = b.Handler.extend({
        addHooks: function () {
            if (!this._draggable) {
                var a = this._map;
                this._draggable = new b.Draggable(a._mapPane, a._container);
                this._draggable.on({ dragstart: this._onDragStart, drag: this._onDrag, dragend: this._onDragEnd }, this);
                a.options.worldCopyJump && (this._draggable.on("predrag", this._onPreDrag, this), a.on("viewreset", this._onViewReset, this), a.whenReady(this._onViewReset, this));
            }
            this._draggable.enable();
        },
        removeHooks: function () {
            this._draggable.disable();
        },
        moved: function () {
            return this._draggable && this._draggable._moved;
        },
        _onDragStart: function () {
            var a = this._map;
            a._panAnim && a._panAnim.stop();
            a.fire("movestart").fire("dragstart");
            a.options.inertia && ((this._positions = []), (this._times = []));
        },
        _onDrag: function () {
            if (this._map.options.inertia) {
                var a = (this._lastTime = +new Date()),
                    b = (this._lastPos = this._draggable._newPos);
                this._positions.push(b);
                this._times.push(a);
                200 < a - this._times[0] && (this._positions.shift(), this._times.shift());
            }
            this._map.fire("move").fire("drag");
        },
        _onViewReset: function () {
            var a = this._map.getSize()._divideBy(2);
            this._initialWorldOffset = this._map.latLngToLayerPoint([0, 0]).subtract(a).x;
            this._worldWidth = this._map.project([0, 180]).x;
        },
        _onPreDrag: function () {
            var a = this._worldWidth,
                b = Math.round(a / 2),
                c = this._initialWorldOffset,
                e = this._draggable._newPos.x,
                f = ((e - b + c) % a) + b - c,
                a = ((e + b + c) % a) - b - c;
            this._draggable._newPos.x = Math.abs(f + c) < Math.abs(a + c) ? f : a;
        },
        _onDragEnd: function (a) {
            var d = this._map,
                c = d.options,
                e = +new Date() - this._lastTime,
                f = !c.inertia || e > c.inertiaThreshold || !this._positions[0];
            if ((d.fire("dragend", a), f)) d.fire("moveend");
            else {
                a = this._lastPos.subtract(this._positions[0]);
                var g = c.easeLinearity;
                a = a.multiplyBy(g / ((this._lastTime + e - this._times[0]) / 1e3));
                f = a.distanceTo([0, 0]);
                e = Math.min(c.inertiaMaxSpeed, f);
                a = a.multiplyBy(e / f);
                var l = e / (c.inertiaDeceleration * g),
                    m = a.multiplyBy(-l / 2).round();
                m.x && m.y
                    ? ((m = d._limitOffset(m, d.options.maxBounds)),
                      b.Util.requestAnimFrame(function () {
                          d.panBy(m, { duration: l, easeLinearity: g, noMoveStart: !0 });
                      }))
                    : d.fire("moveend");
            }
        },
    });
    b.Map.addInitHook("addHandler", "dragging", b.Map.Drag);
    b.Map.mergeOptions({ doubleClickZoom: !0 });
    b.Map.DoubleClickZoom = b.Handler.extend({
        addHooks: function () {
            this._map.on("dblclick", this._onDoubleClick, this);
        },
        removeHooks: function () {
            this._map.off("dblclick", this._onDoubleClick, this);
        },
        _onDoubleClick: function (a) {
            var b = this._map,
                c = b.getZoom() + (a.originalEvent.shiftKey ? -1 : 1);
            "center" === b.options.doubleClickZoom ? b.setZoom(c) : b.setZoomAround(a.containerPoint, c);
        },
    });
    b.Map.addInitHook("addHandler", "doubleClickZoom", b.Map.DoubleClickZoom);
    b.Map.mergeOptions({ scrollWheelZoom: !0 });
    b.Map.ScrollWheelZoom = b.Handler.extend({
        addHooks: function () {
            b.DomEvent.on(this._map._container, "mousewheel", this._onWheelScroll, this);
            b.DomEvent.on(this._map._container, "MozMousePixelScroll", b.DomEvent.preventDefault);
            this._delta = 0;
        },
        removeHooks: function () {
            b.DomEvent.off(this._map._container, "mousewheel", this._onWheelScroll);
            b.DomEvent.off(this._map._container, "MozMousePixelScroll", b.DomEvent.preventDefault);
        },
        _onWheelScroll: function (a) {
            var d = b.DomEvent.getWheelDelta(a);
            this._delta += d;
            this._lastMousePos = this._map.mouseEventToContainerPoint(a);
            this._startTime || (this._startTime = +new Date());
            d = Math.max(40 - (+new Date() - this._startTime), 0);
            clearTimeout(this._timer);
            this._timer = setTimeout(b.bind(this._performZoom, this), d);
            b.DomEvent.preventDefault(a);
            b.DomEvent.stopPropagation(a);
        },
        _performZoom: function () {
            var a = this._map,
                b = this._delta,
                c = a.getZoom(),
                b = 0 < b ? Math.ceil(b) : Math.floor(b),
                b = Math.max(Math.min(b, 4), -4),
                b = a._limitZoom(c + b) - c;
            this._delta = 0;
            this._startTime = null;
            b && ("center" === a.options.scrollWheelZoom ? a.setZoom(c + b) : a.setZoomAround(this._lastMousePos, c + b));
        },
    });
    b.Map.addInitHook("addHandler", "scrollWheelZoom", b.Map.ScrollWheelZoom);
    b.extend(b.DomEvent, {
        _touchstart: b.Browser.msPointer ? "MSPointerDown" : b.Browser.pointer ? "pointerdown" : "touchstart",
        _touchend: b.Browser.msPointer ? "MSPointerUp" : b.Browser.pointer ? "pointerup" : "touchend",
        addDoubleTapListener: function (a, d, c) {
            function e(a) {
                var d;
                if ((b.Browser.pointer ? (t.push(a.pointerId), (d = t.length)) : (d = a.touches.length), !(1 < d))) {
                    d = Date.now();
                    var c = d - (k || d);
                    l = a.touches ? a.touches[0] : a;
                    m = 0 < c && r >= c;
                    k = d;
                }
            }
            function f(a) {
                if (b.Browser.pointer) {
                    a = t.indexOf(a.pointerId);
                    if (-1 === a) return;
                    t.splice(a, 1);
                }
                if (m) {
                    if (b.Browser.pointer) {
                        var c = {},
                            e;
                        for (e in l) (a = l[e]), (c[e] = "function" == typeof a ? a.bind(l) : a);
                        l = c;
                    }
                    l.type = "dblclick";
                    d(l);
                    k = null;
                }
            }
            var k,
                l,
                m = !1,
                r = 250,
                D = this._touchstart,
                x = this._touchend,
                t = [];
            a["_leaflet_" + D + c] = e;
            a["_leaflet_" + x + c] = f;
            c = b.Browser.pointer ? g.documentElement : a;
            return a.addEventListener(D, e, !1), c.addEventListener(x, f, !1), b.Browser.pointer && c.addEventListener(b.DomEvent.POINTER_CANCEL, f, !1), this;
        },
        removeDoubleTapListener: function (a, d) {
            return (
                a.removeEventListener(this._touchstart, a["_leaflet_" + this._touchstart + d], !1),
                (b.Browser.pointer ? g.documentElement : a).removeEventListener(this._touchend, a["_leaflet_" + this._touchend + d], !1),
                b.Browser.pointer && g.documentElement.removeEventListener(b.DomEvent.POINTER_CANCEL, a["_leaflet_" + this._touchend + d], !1),
                this
            );
        },
    });
    b.extend(b.DomEvent, {
        POINTER_DOWN: b.Browser.msPointer ? "MSPointerDown" : "pointerdown",
        POINTER_MOVE: b.Browser.msPointer ? "MSPointerMove" : "pointermove",
        POINTER_UP: b.Browser.msPointer ? "MSPointerUp" : "pointerup",
        POINTER_CANCEL: b.Browser.msPointer ? "MSPointerCancel" : "pointercancel",
        _pointers: [],
        _pointerDocumentListener: !1,
        addPointerListener: function (a, b, c, e) {
            switch (b) {
                case "touchstart":
                    return this.addPointerListenerStart(a, b, c, e);
                case "touchend":
                    return this.addPointerListenerEnd(a, b, c, e);
                case "touchmove":
                    return this.addPointerListenerMove(a, b, c, e);
                default:
                    throw "Unknown touch event type";
            }
        },
        addPointerListenerStart: function (a, d, c, e) {
            var f = this._pointers;
            d = function (a) {
                b.DomEvent.preventDefault(a);
                for (var d = !1, e = 0; e < f.length; e++)
                    if (f[e].pointerId === a.pointerId) {
                        d = !0;
                        break;
                    }
                d || f.push(a);
                a.touches = f.slice();
                a.changedTouches = [a];
                c(a);
            };
            ((a["_leaflet_touchstart" + e] = d), a.addEventListener(this.POINTER_DOWN, d, !1), this._pointerDocumentListener) ||
                ((a = function (a) {
                    for (var b = 0; b < f.length; b++)
                        if (f[b].pointerId === a.pointerId) {
                            f.splice(b, 1);
                            break;
                        }
                }),
                g.documentElement.addEventListener(this.POINTER_UP, a, !1),
                g.documentElement.addEventListener(this.POINTER_CANCEL, a, !1),
                (this._pointerDocumentListener = !0));
            return this;
        },
        addPointerListenerMove: function (a, b, c, e) {
            function f(a) {
                if ((a.pointerType !== a.MSPOINTER_TYPE_MOUSE && "mouse" !== a.pointerType) || 0 !== a.buttons) {
                    for (var b = 0; b < g.length; b++)
                        if (g[b].pointerId === a.pointerId) {
                            g[b] = a;
                            break;
                        }
                    a.touches = g.slice();
                    a.changedTouches = [a];
                    c(a);
                }
            }
            var g = this._pointers;
            return (a["_leaflet_touchmove" + e] = f), a.addEventListener(this.POINTER_MOVE, f, !1), this;
        },
        addPointerListenerEnd: function (a, b, c, e) {
            var f = this._pointers;
            b = function (a) {
                for (var b = 0; b < f.length; b++)
                    if (f[b].pointerId === a.pointerId) {
                        f.splice(b, 1);
                        break;
                    }
                a.touches = f.slice();
                a.changedTouches = [a];
                c(a);
            };
            return (a["_leaflet_touchend" + e] = b), a.addEventListener(this.POINTER_UP, b, !1), a.addEventListener(this.POINTER_CANCEL, b, !1), this;
        },
        removePointerListener: function (a, b, c) {
            c = a["_leaflet_" + b + c];
            switch (b) {
                case "touchstart":
                    a.removeEventListener(this.POINTER_DOWN, c, !1);
                    break;
                case "touchmove":
                    a.removeEventListener(this.POINTER_MOVE, c, !1);
                    break;
                case "touchend":
                    a.removeEventListener(this.POINTER_UP, c, !1), a.removeEventListener(this.POINTER_CANCEL, c, !1);
            }
            return this;
        },
    });
    b.Map.mergeOptions({ touchZoom: b.Browser.touch && !b.Browser.android23, bounceAtZoomLimits: !0 });
    b.Map.TouchZoom = b.Handler.extend({
        addHooks: function () {
            b.DomEvent.on(this._map._container, "touchstart", this._onTouchStart, this);
        },
        removeHooks: function () {
            b.DomEvent.off(this._map._container, "touchstart", this._onTouchStart, this);
        },
        _onTouchStart: function (a) {
            var d = this._map;
            if (a.touches && 2 === a.touches.length && !d._animatingZoom && !this._zooming) {
                var c = d.mouseEventToLayerPoint(a.touches[0]),
                    e = d.mouseEventToLayerPoint(a.touches[1]),
                    f = d._getCenterLayerPoint();
                this._startCenter = c.add(e)._divideBy(2);
                this._startDist = c.distanceTo(e);
                this._moved = !1;
                this._zooming = !0;
                this._centerOffset = f.subtract(this._startCenter);
                d._panAnim && d._panAnim.stop();
                b.DomEvent.on(g, "touchmove", this._onTouchMove, this).on(g, "touchend", this._onTouchEnd, this);
                b.DomEvent.preventDefault(a);
            }
        },
        _onTouchMove: function (a) {
            var d = this._map;
            if (a.touches && 2 === a.touches.length && this._zooming) {
                var c = d.mouseEventToLayerPoint(a.touches[0]),
                    e = d.mouseEventToLayerPoint(a.touches[1]);
                this._scale = c.distanceTo(e) / this._startDist;
                this._delta = c._add(e)._divideBy(2)._subtract(this._startCenter);
                1 === this._scale ||
                    (!d.options.bounceAtZoomLimits && ((d.getZoom() === d.getMinZoom() && 1 > this._scale) || (d.getZoom() === d.getMaxZoom() && 1 < this._scale))) ||
                    (this._moved || (b.DomUtil.addClass(d._mapPane, "leaflet-touching"), d.fire("movestart").fire("zoomstart"), (this._moved = !0)),
                    b.Util.cancelAnimFrame(this._animRequest),
                    (this._animRequest = b.Util.requestAnimFrame(this._updateOnMove, this, !0, this._map._container)),
                    b.DomEvent.preventDefault(a));
            }
        },
        _updateOnMove: function () {
            var a = this._map,
                b = this._getScaleOrigin(),
                b = a.layerPointToLatLng(b),
                c = a.getScaleZoom(this._scale);
            a._animateZoom(b, c, this._startCenter, this._scale, this._delta, !1, !0);
        },
        _onTouchEnd: function () {
            if (!this._moved || !this._zooming) return void (this._zooming = !1);
            var a = this._map;
            this._zooming = !1;
            b.DomUtil.removeClass(a._mapPane, "leaflet-touching");
            b.Util.cancelAnimFrame(this._animRequest);
            b.DomEvent.off(g, "touchmove", this._onTouchMove).off(g, "touchend", this._onTouchEnd);
            var d = this._getScaleOrigin(),
                c = a.layerPointToLatLng(d),
                e = a.getZoom(),
                f = a.getScaleZoom(this._scale) - e,
                e = a._limitZoom(e + (0 < f ? Math.ceil(f) : Math.floor(f))),
                f = a.getZoomScale(e) / this._scale;
            a._animateZoom(c, e, d, f);
        },
        _getScaleOrigin: function () {
            var a = this._centerOffset.subtract(this._delta).divideBy(this._scale);
            return this._startCenter.add(a);
        },
    });
    b.Map.addInitHook("addHandler", "touchZoom", b.Map.TouchZoom);
    b.Map.mergeOptions({ tap: !0, tapTolerance: 15 });
    b.Map.Tap = b.Handler.extend({
        addHooks: function () {
            b.DomEvent.on(this._map._container, "touchstart", this._onDown, this);
        },
        removeHooks: function () {
            b.DomEvent.off(this._map._container, "touchstart", this._onDown, this);
        },
        _onDown: function (a) {
            if (a.touches) {
                if ((b.DomEvent.preventDefault(a), (this._fireClick = !0), 1 < a.touches.length)) return (this._fireClick = !1), void clearTimeout(this._holdTimeout);
                var d = a.touches[0];
                a = d.target;
                this._startPos = this._newPos = new b.Point(d.clientX, d.clientY);
                a.tagName && "a" === a.tagName.toLowerCase() && b.DomUtil.addClass(a, "leaflet-active");
                this._holdTimeout = setTimeout(
                    b.bind(function () {
                        this._isTapValid() && ((this._fireClick = !1), this._onUp(), this._simulateEvent("contextmenu", d));
                    }, this),
                    1e3
                );
                b.DomEvent.on(g, "touchmove", this._onMove, this).on(g, "touchend", this._onUp, this);
            }
        },
        _onUp: function (a) {
            if ((clearTimeout(this._holdTimeout), b.DomEvent.off(g, "touchmove", this._onMove, this).off(g, "touchend", this._onUp, this), this._fireClick && a && a.changedTouches)) {
                a = a.changedTouches[0];
                var d = a.target;
                d && d.tagName && "a" === d.tagName.toLowerCase() && b.DomUtil.removeClass(d, "leaflet-active");
                this._isTapValid() && this._simulateEvent("click", a);
            }
        },
        _isTapValid: function () {
            return this._newPos.distanceTo(this._startPos) <= this._map.options.tapTolerance;
        },
        _onMove: function (a) {
            a = a.touches[0];
            this._newPos = new b.Point(a.clientX, a.clientY);
        },
        _simulateEvent: function (a, b) {
            var c = g.createEvent("MouseEvents");
            c._simulated = !0;
            b.target._simulatedClick = !0;
            c.initMouseEvent(a, !0, !0, f, 1, b.screenX, b.screenY, b.clientX, b.clientY, !1, !1, !1, !1, 0, null);
            b.target.dispatchEvent(c);
        },
    });
    b.Browser.touch && !b.Browser.pointer && b.Map.addInitHook("addHandler", "tap", b.Map.Tap);
    b.Map.mergeOptions({ boxZoom: !0 });
    b.Map.BoxZoom = b.Handler.extend({
        initialize: function (a) {
            this._map = a;
            this._container = a._container;
            this._pane = a._panes.overlayPane;
            this._moved = !1;
        },
        addHooks: function () {
            b.DomEvent.on(this._container, "mousedown", this._onMouseDown, this);
        },
        removeHooks: function () {
            b.DomEvent.off(this._container, "mousedown", this._onMouseDown);
            this._moved = !1;
        },
        moved: function () {
            return this._moved;
        },
        _onMouseDown: function (a) {
            return (
                (this._moved = !1),
                !a.shiftKey || (1 !== a.which && 1 !== a.button)
                    ? !1
                    : (b.DomUtil.disableTextSelection(),
                      b.DomUtil.disableImageDrag(),
                      (this._startLayerPoint = this._map.mouseEventToLayerPoint(a)),
                      void b.DomEvent.on(g, "mousemove", this._onMouseMove, this).on(g, "mouseup", this._onMouseUp, this).on(g, "keydown", this._onKeyDown, this))
            );
        },
        _onMouseMove: function (a) {
            this._moved || ((this._box = b.DomUtil.create("div", "leaflet-zoom-box", this._pane)), b.DomUtil.setPosition(this._box, this._startLayerPoint), (this._container.style.cursor = "crosshair"), this._map.fire("boxzoomstart"));
            var d = this._startLayerPoint,
                c = this._box,
                e = this._map.mouseEventToLayerPoint(a);
            a = e.subtract(d);
            d = new b.Point(Math.min(e.x, d.x), Math.min(e.y, d.y));
            b.DomUtil.setPosition(c, d);
            this._moved = !0;
            c.style.width = Math.max(0, Math.abs(a.x) - 4) + "px";
            c.style.height = Math.max(0, Math.abs(a.y) - 4) + "px";
        },
        _finish: function () {
            this._moved && (this._pane.removeChild(this._box), (this._container.style.cursor = ""));
            b.DomUtil.enableTextSelection();
            b.DomUtil.enableImageDrag();
            b.DomEvent.off(g, "mousemove", this._onMouseMove).off(g, "mouseup", this._onMouseUp).off(g, "keydown", this._onKeyDown);
        },
        _onMouseUp: function (a) {
            this._finish();
            var d = this._map;
            a = d.mouseEventToLayerPoint(a);
            this._startLayerPoint.equals(a) || ((a = new b.LatLngBounds(d.layerPointToLatLng(this._startLayerPoint), d.layerPointToLatLng(a))), d.fitBounds(a), d.fire("boxzoomend", { boxZoomBounds: a }));
        },
        _onKeyDown: function (a) {
            27 === a.keyCode && this._finish();
        },
    });
    b.Map.addInitHook("addHandler", "boxZoom", b.Map.BoxZoom);
    b.Map.mergeOptions({ keyboard: !0, keyboardPanOffset: 80, keyboardZoomOffset: 1 });
    b.Map.Keyboard = b.Handler.extend({
        keyCodes: { left: [37], right: [39], down: [40], up: [38], zoomIn: [187, 107, 61, 171], zoomOut: [189, 109, 173] },
        initialize: function (a) {
            this._map = a;
            this._setPanOffset(a.options.keyboardPanOffset);
            this._setZoomOffset(a.options.keyboardZoomOffset);
        },
        addHooks: function () {
            var a = this._map._container;
            -1 === a.tabIndex && (a.tabIndex = "0");
            b.DomEvent.on(a, "focus", this._onFocus, this).on(a, "blur", this._onBlur, this).on(a, "mousedown", this._onMouseDown, this);
            this._map.on("focus", this._addHooks, this).on("blur", this._removeHooks, this);
        },
        removeHooks: function () {
            this._removeHooks();
            var a = this._map._container;
            b.DomEvent.off(a, "focus", this._onFocus, this).off(a, "blur", this._onBlur, this).off(a, "mousedown", this._onMouseDown, this);
            this._map.off("focus", this._addHooks, this).off("blur", this._removeHooks, this);
        },
        _onMouseDown: function () {
            if (!this._focused) {
                var a = g.body,
                    b = g.documentElement,
                    c = a.scrollTop || b.scrollTop,
                    a = a.scrollLeft || b.scrollLeft;
                this._map._container.focus();
                f.scrollTo(a, c);
            }
        },
        _onFocus: function () {
            this._focused = !0;
            this._map.fire("focus");
        },
        _onBlur: function () {
            this._focused = !1;
            this._map.fire("blur");
        },
        _setPanOffset: function (a) {
            var b,
                c,
                e = (this._panKeys = {}),
                f = this.keyCodes;
            b = 0;
            for (c = f.left.length; c > b; b++) e[f.left[b]] = [-1 * a, 0];
            b = 0;
            for (c = f.right.length; c > b; b++) e[f.right[b]] = [a, 0];
            b = 0;
            for (c = f.down.length; c > b; b++) e[f.down[b]] = [0, a];
            b = 0;
            for (c = f.up.length; c > b; b++) e[f.up[b]] = [0, -1 * a];
        },
        _setZoomOffset: function (a) {
            var b,
                c,
                e = (this._zoomKeys = {}),
                f = this.keyCodes;
            b = 0;
            for (c = f.zoomIn.length; c > b; b++) e[f.zoomIn[b]] = a;
            b = 0;
            for (c = f.zoomOut.length; c > b; b++) e[f.zoomOut[b]] = -a;
        },
        _addHooks: function () {
            b.DomEvent.on(g, "keydown", this._onKeyDown, this);
        },
        _removeHooks: function () {
            b.DomEvent.off(g, "keydown", this._onKeyDown, this);
        },
        _onKeyDown: function (a) {
            var d = a.keyCode,
                c = this._map;
            if (d in this._panKeys) {
                if (c._panAnim && c._panAnim._inProgress) return;
                c.panBy(this._panKeys[d]);
                c.options.maxBounds && c.panInsideBounds(c.options.maxBounds);
            } else {
                if (!(d in this._zoomKeys)) return;
                c.setZoom(c.getZoom() + this._zoomKeys[d]);
            }
            b.DomEvent.stop(a);
        },
    });
    b.Map.addInitHook("addHandler", "keyboard", b.Map.Keyboard);
    b.Handler.MarkerDrag = b.Handler.extend({
        initialize: function (a) {
            this._marker = a;
        },
        addHooks: function () {
            var a = this._marker._icon;
            this._draggable || (this._draggable = new b.Draggable(a, a));
            this._draggable.on("dragstart", this._onDragStart, this).on("drag", this._onDrag, this).on("dragend", this._onDragEnd, this);
            this._draggable.enable();
            b.DomUtil.addClass(this._marker._icon, "leaflet-marker-draggable");
        },
        removeHooks: function () {
            this._draggable.off("dragstart", this._onDragStart, this).off("drag", this._onDrag, this).off("dragend", this._onDragEnd, this);
            this._draggable.disable();
            b.DomUtil.removeClass(this._marker._icon, "leaflet-marker-draggable");
        },
        moved: function () {
            return this._draggable && this._draggable._moved;
        },
        _onDragStart: function () {
            this._marker.closePopup().fire("movestart").fire("dragstart");
        },
        _onDrag: function () {
            var a = this._marker,
                d = a._shadow,
                c = b.DomUtil.getPosition(a._icon),
                e = a._map.layerPointToLatLng(c);
            d && b.DomUtil.setPosition(d, c);
            a._latlng = e;
            a.fire("move", { latlng: e }).fire("drag");
        },
        _onDragEnd: function (a) {
            this._marker.fire("moveend").fire("dragend", a);
        },
    });
    b.Control = b.Class.extend({
        options: { position: "topright" },
        initialize: function (a) {
            b.setOptions(this, a);
        },
        getPosition: function () {
            return this.options.position;
        },
        setPosition: function (a) {
            var b = this._map;
            return b && b.removeControl(this), (this.options.position = a), b && b.addControl(this), this;
        },
        getContainer: function () {
            return this._container;
        },
        addTo: function (a) {
            this._map = a;
            var d = (this._container = this.onAdd(a)),
                c = this.getPosition();
            a = a._controlCorners[c];
            return b.DomUtil.addClass(d, "leaflet-control"), -1 !== c.indexOf("bottom") ? a.insertBefore(d, a.firstChild) : a.appendChild(d), this;
        },
        removeFrom: function (a) {
            var b = this.getPosition();
            return a._controlCorners[b].removeChild(this._container), (this._map = null), this.onRemove && this.onRemove(a), this;
        },
        _refocusOnMap: function () {
            this._map && this._map.getContainer().focus();
        },
    });
    b.control = function (a) {
        return new b.Control(a);
    };
    b.Map.include({
        addControl: function (a) {
            return a.addTo(this), this;
        },
        removeControl: function (a) {
            return a.removeFrom(this), this;
        },
        _initControlPos: function () {
            function a(a, f) {
                d[a + f] = b.DomUtil.create("div", c + a + " " + c + f, e);
            }
            var d = (this._controlCorners = {}),
                c = "leaflet-",
                e = (this._controlContainer = b.DomUtil.create("div", c + "control-container", this._container));
            a("top", "left");
            a("top", "right");
            a("bottom", "left");
            a("bottom", "right");
        },
        _clearControlPos: function () {
            this._container.removeChild(this._controlContainer);
        },
    });
    b.Control.Zoom = b.Control.extend({
        options: { position: "topleft", zoomInText: "+", zoomInTitle: "Zoom in", zoomOutText: "-", zoomOutTitle: "Zoom out" },
        onAdd: function (a) {
            var d = b.DomUtil.create("div", "leaflet-control-zoom leaflet-bar");
            return (
                (this._map = a),
                (this._zoomInButton = this._createButton(this.options.zoomInText, this.options.zoomInTitle, "leaflet-control-zoom-in", d, this._zoomIn, this)),
                (this._zoomOutButton = this._createButton(this.options.zoomOutText, this.options.zoomOutTitle, "leaflet-control-zoom-out", d, this._zoomOut, this)),
                this._updateDisabled(),
                a.on("zoomend zoomlevelschange", this._updateDisabled, this),
                d
            );
        },
        onRemove: function (a) {
            a.off("zoomend zoomlevelschange", this._updateDisabled, this);
        },
        _zoomIn: function (a) {
            this._map.zoomIn(a.shiftKey ? 3 : 1);
        },
        _zoomOut: function (a) {
            this._map.zoomOut(a.shiftKey ? 3 : 1);
        },
        _createButton: function (a, d, c, e, f, g) {
            c = b.DomUtil.create("a", c, e);
            c.innerHTML = a;
            c.href = "#";
            c.title = d;
            a = b.DomEvent.stopPropagation;
            return b.DomEvent.on(c, "click", a).on(c, "mousedown", a).on(c, "dblclick", a).on(c, "click", b.DomEvent.preventDefault).on(c, "click", f, g).on(c, "click", this._refocusOnMap, g), c;
        },
        _updateDisabled: function () {
            var a = this._map;
            b.DomUtil.removeClass(this._zoomInButton, "leaflet-disabled");
            b.DomUtil.removeClass(this._zoomOutButton, "leaflet-disabled");
            a._zoom === a.getMinZoom() && b.DomUtil.addClass(this._zoomOutButton, "leaflet-disabled");
            a._zoom === a.getMaxZoom() && b.DomUtil.addClass(this._zoomInButton, "leaflet-disabled");
        },
    });
    b.Map.mergeOptions({ zoomControl: !0 });
    b.Map.addInitHook(function () {
        this.options.zoomControl && ((this.zoomControl = new b.Control.Zoom()), this.addControl(this.zoomControl));
    });
    b.control.zoom = function (a) {
        return new b.Control.Zoom(a);
    };
    b.Control.Attribution = b.Control.extend({
        options: { position: "bottomright", prefix: '<a href="http://leafletjs.com" title="A JS library for interactive maps">Leaflet</a>' },
        initialize: function (a) {
            b.setOptions(this, a);
            this._attributions = {};
        },
        onAdd: function (a) {
            this._container = b.DomUtil.create("div", "leaflet-control-attribution");
            b.DomEvent.disableClickPropagation(this._container);
            for (var d in a._layers) a._layers[d].getAttribution && this.addAttribution(a._layers[d].getAttribution());
            return a.on("layeradd", this._onLayerAdd, this).on("layerremove", this._onLayerRemove, this), this._update(), this._container;
        },
        onRemove: function (a) {
            a.off("layeradd", this._onLayerAdd).off("layerremove", this._onLayerRemove);
        },
        setPrefix: function (a) {
            return (this.options.prefix = a), this._update(), this;
        },
        addAttribution: function (a) {
            return a ? (this._attributions[a] || (this._attributions[a] = 0), this._attributions[a]++, this._update(), this) : void 0;
        },
        removeAttribution: function (a) {
            return a ? (this._attributions[a] && (this._attributions[a]--, this._update()), this) : void 0;
        },
        _update: function () {
            if (this._map) {
                var a = [],
                    b;
                for (b in this._attributions) this._attributions[b] && a.push(b);
                b = [];
                this.options.prefix && b.push(this.options.prefix);
                a.length && b.push(a.join(", "));
                this._container.innerHTML = b.join(" | ");
            }
        },
        _onLayerAdd: function (a) {
            a.layer.getAttribution && this.addAttribution(a.layer.getAttribution());
        },
        _onLayerRemove: function (a) {
            a.layer.getAttribution && this.removeAttribution(a.layer.getAttribution());
        },
    });
    b.Map.mergeOptions({ attributionControl: !0 });
    b.Map.addInitHook(function () {
        this.options.attributionControl && (this.attributionControl = new b.Control.Attribution().addTo(this));
    });
    b.control.attribution = function (a) {
        return new b.Control.Attribution(a);
    };
    b.Control.Scale = b.Control.extend({
        options: { position: "bottomleft", maxWidth: 100, metric: !0, imperial: !0, updateWhenIdle: !1 },
        onAdd: function (a) {
            this._map = a;
            var d = b.DomUtil.create("div", "leaflet-control-scale"),
                c = this.options;
            return this._addScales(c, "leaflet-control-scale", d), a.on(c.updateWhenIdle ? "moveend" : "move", this._update, this), a.whenReady(this._update, this), d;
        },
        onRemove: function (a) {
            a.off(this.options.updateWhenIdle ? "moveend" : "move", this._update, this);
        },
        _addScales: function (a, d, c) {
            a.metric && (this._mScale = b.DomUtil.create("div", d + "-line", c));
            a.imperial && (this._iScale = b.DomUtil.create("div", d + "-line", c));
        },
        _update: function () {
            var a = this._map.getBounds(),
                b = a.getCenter().lat,
                a = (6378137 * Math.PI * Math.cos((b * Math.PI) / 180) * (a.getNorthEast().lng - a.getSouthWest().lng)) / 180,
                b = this._map.getSize(),
                c = this.options,
                e = 0;
            0 < b.x && (e = (c.maxWidth / b.x) * a);
            this._updateScales(c, e);
        },
        _updateScales: function (a, b) {
            a.metric && b && this._updateMetric(b);
            a.imperial && b && this._updateImperial(b);
        },
        _updateMetric: function (a) {
            var b = this._getRoundNum(a);
            this._mScale.style.width = this._getScaleWidth(b / a) + "px";
            this._mScale.innerHTML = 1e3 > b ? b + " m" : b / 1e3 + " km";
        },
        _updateImperial: function (a) {
            var b, c, e;
            a *= 3.2808399;
            var f = this._iScale;
            5280 < a
                ? ((b = a / 5280), (c = this._getRoundNum(b)), (f.style.width = this._getScaleWidth(c / b) + "px"), (f.innerHTML = c + " mi"))
                : ((e = this._getRoundNum(a)), (f.style.width = this._getScaleWidth(e / a) + "px"), (f.innerHTML = e + " ft"));
        },
        _getScaleWidth: function (a) {
            return Math.round(this.options.maxWidth * a) - 10;
        },
        _getRoundNum: function (a) {
            var b = Math.pow(10, (Math.floor(a) + "").length - 1);
            a /= b;
            return (a = 10 <= a ? 10 : 5 <= a ? 5 : 3 <= a ? 3 : 2 <= a ? 2 : 1), b * a;
        },
    });
    b.control.scale = function (a) {
        return new b.Control.Scale(a);
    };
    b.Control.Layers = b.Control.extend({
        options: { collapsed: !0, position: "topright", autoZIndex: !0 },
        initialize: function (a, c, e) {
            b.setOptions(this, e);
            this._layers = {};
            this._lastZIndex = 0;
            this._handlingClick = !1;
            for (var f in a) this._addLayer(a[f], f);
            for (f in c) this._addLayer(c[f], f, !0);
        },
        onAdd: function (a) {
            return this._initLayout(), this._update(), a.on("layeradd", this._onLayerChange, this).on("layerremove", this._onLayerChange, this), this._container;
        },
        onRemove: function (a) {
            a.off("layeradd", this._onLayerChange, this).off("layerremove", this._onLayerChange, this);
        },
        addBaseLayer: function (a, b) {
            return this._addLayer(a, b), this._update(), this;
        },
        addOverlay: function (a, b) {
            return this._addLayer(a, b, !0), this._update(), this;
        },
        removeLayer: function (a) {
            a = b.stamp(a);
            return delete this._layers[a], this._update(), this;
        },
        _initLayout: function () {
            var a = (this._container = b.DomUtil.create("div", "leaflet-control-layers"));
            a.setAttribute("aria-haspopup", !0);
            b.Browser.touch ? b.DomEvent.on(a, "click", b.DomEvent.stopPropagation) : b.DomEvent.disableClickPropagation(a).disableScrollPropagation(a);
            var c = (this._form = b.DomUtil.create("form", "leaflet-control-layers-list"));
            if (this.options.collapsed) {
                b.Browser.android || b.DomEvent.on(a, "mouseover", this._expand, this).on(a, "mouseout", this._collapse, this);
                var e = (this._layersLink = b.DomUtil.create("a", "leaflet-control-layers-toggle", a));
                e.href = "#";
                e.title = "Layers";
                b.Browser.touch ? b.DomEvent.on(e, "click", b.DomEvent.stop).on(e, "click", this._expand, this) : b.DomEvent.on(e, "focus", this._expand, this);
                b.DomEvent.on(
                    c,
                    "click",
                    function () {
                        setTimeout(b.bind(this._onInputClick, this), 0);
                    },
                    this
                );
                this._map.on("click", this._collapse, this);
            } else this._expand();
            this._baseLayersList = b.DomUtil.create("div", "leaflet-control-layers-base", c);
            this._separator = b.DomUtil.create("div", "leaflet-control-layers-separator", c);
            this._overlaysList = b.DomUtil.create("div", "leaflet-control-layers-overlays", c);
            a.appendChild(c);
        },
        _addLayer: function (a, c, e) {
            var f = b.stamp(a);
            this._layers[f] = { layer: a, name: c, overlay: e };
            this.options.autoZIndex && a.setZIndex && (this._lastZIndex++, a.setZIndex(this._lastZIndex));
        },
        _update: function () {
            if (this._container) {
                this._baseLayersList.innerHTML = "";
                this._overlaysList.innerHTML = "";
                var a,
                    b,
                    c = !1,
                    e = !1;
                for (a in this._layers) (b = this._layers[a]), this._addItem(b), (e = e || b.overlay), (c = c || !b.overlay);
                this._separator.style.display = e && c ? "" : "none";
            }
        },
        _onLayerChange: function (a) {
            var c = this._layers[b.stamp(a.layer)];
            c && (this._handlingClick || this._update(), (a = c.overlay ? ("layeradd" === a.type ? "overlayadd" : "overlayremove") : "layeradd" === a.type ? "baselayerchange" : null) && this._map.fire(a, c));
        },
        _createRadioElement: function (a, b) {
            a = '<input type="radio" class="leaflet-control-layers-selector" name="' + a + '"';
            b && (a += ' checked="checked"');
            a += "/>";
            b = g.createElement("div");
            return (b.innerHTML = a), b.firstChild;
        },
        _addItem: function (a) {
            var c,
                e = g.createElement("label"),
                f = this._map.hasLayer(a.layer);
            a.overlay ? ((c = g.createElement("input")), (c.type = "checkbox"), (c.className = "leaflet-control-layers-selector"), (c.defaultChecked = f)) : (c = this._createRadioElement("leaflet-base-layers", f));
            c.layerId = b.stamp(a.layer);
            b.DomEvent.on(c, "click", this._onInputClick, this);
            f = g.createElement("span");
            f.innerHTML = " " + a.name;
            e.appendChild(c);
            e.appendChild(f);
            return (a.overlay ? this._overlaysList : this._baseLayersList).appendChild(e), e;
        },
        _onInputClick: function () {
            var a,
                b,
                c,
                e = this._form.getElementsByTagName("input"),
                f = e.length;
            this._handlingClick = !0;
            for (a = 0; f > a; a++) (b = e[a]), (c = this._layers[b.layerId]), b.checked && !this._map.hasLayer(c.layer) ? this._map.addLayer(c.layer) : !b.checked && this._map.hasLayer(c.layer) && this._map.removeLayer(c.layer);
            this._handlingClick = !1;
            this._refocusOnMap();
        },
        _expand: function () {
            b.DomUtil.addClass(this._container, "leaflet-control-layers-expanded");
        },
        _collapse: function () {
            this._container.className = this._container.className.replace(" leaflet-control-layers-expanded", "");
        },
    });
    b.control.layers = function (a, c, e) {
        return new b.Control.Layers(a, c, e);
    };
    b.PosAnimation = b.Class.extend({
        includes: b.Mixin.Events,
        run: function (a, c, e, f) {
            this.stop();
            this._el = a;
            this._inProgress = !0;
            this._newPos = c;
            this.fire("start");
            a.style[b.DomUtil.TRANSITION] = "all " + (e || 0.25) + "s cubic-bezier(0,0," + (f || 0.5) + ",1)";
            b.DomEvent.on(a, b.DomUtil.TRANSITION_END, this._onTransitionEnd, this);
            b.DomUtil.setPosition(a, c);
            b.Util.falseFn(a.offsetWidth);
            this._stepTimer = setInterval(b.bind(this._onStep, this), 50);
        },
        stop: function () {
            this._inProgress && (b.DomUtil.setPosition(this._el, this._getPos()), this._onTransitionEnd(), b.Util.falseFn(this._el.offsetWidth));
        },
        _onStep: function () {
            var a = this._getPos();
            return a ? ((this._el._leaflet_pos = a), void this.fire("step")) : void this._onTransitionEnd();
        },
        _transformRe: /([-+]?(?:\d*\.)?\d+)\D*, ([-+]?(?:\d*\.)?\d+)\D*\)/,
        _getPos: function () {
            var a,
                c,
                e = f.getComputedStyle(this._el);
            if (b.Browser.any3d) {
                if (((c = e[b.DomUtil.TRANSFORM].match(this._transformRe)), !c)) return;
                a = parseFloat(c[1]);
                c = parseFloat(c[2]);
            } else (a = parseFloat(e.left)), (c = parseFloat(e.top));
            return new b.Point(a, c, !0);
        },
        _onTransitionEnd: function () {
            b.DomEvent.off(this._el, b.DomUtil.TRANSITION_END, this._onTransitionEnd, this);
            this._inProgress && ((this._inProgress = !1), (this._el.style[b.DomUtil.TRANSITION] = ""), (this._el._leaflet_pos = this._newPos), clearInterval(this._stepTimer), this.fire("step").fire("end"));
        },
    });
    b.Map.include({
        setView: function (a, d, e) {
            if (((d = d === c ? this._zoom : this._limitZoom(d)), (a = this._limitCenter(b.latLng(a), d, this.options.maxBounds)), (e = e || {}), this._panAnim && this._panAnim.stop(), this._loaded && !e.reset && !0 !== e))
                if (
                    (e.animate !== c && ((e.zoom = b.extend({ animate: e.animate }, e.zoom)), (e.pan = b.extend({ animate: e.animate }, e.pan))),
                    this._zoom !== d ? this._tryAnimatedZoom && this._tryAnimatedZoom(a, d, e.zoom) : this._tryAnimatedPan(a, e.pan))
                )
                    return clearTimeout(this._sizeTimer), this;
            return this._resetView(a, d), this;
        },
        panBy: function (a, c) {
            if (((a = b.point(a).round()), (c = c || {}), !a.x && !a.y)) return this;
            (this._panAnim || ((this._panAnim = new b.PosAnimation()), this._panAnim.on({ step: this._onPanTransitionStep, end: this._onPanTransitionEnd }, this)), c.noMoveStart || this.fire("movestart"), !1 !== c.animate)
                ? (b.DomUtil.addClass(this._mapPane, "leaflet-pan-anim"), (a = this._getMapPanePos().subtract(a)), this._panAnim.run(this._mapPane, a, c.duration || 0.25, c.easeLinearity))
                : (this._rawPanBy(a), this.fire("move").fire("moveend"));
            return this;
        },
        _onPanTransitionStep: function () {
            this.fire("move");
        },
        _onPanTransitionEnd: function () {
            b.DomUtil.removeClass(this._mapPane, "leaflet-pan-anim");
            this.fire("moveend");
        },
        _tryAnimatedPan: function (a, b) {
            a = this._getCenterOffset(a)._floor();
            return !0 === (b && b.animate) || this.getSize().contains(a) ? (this.panBy(a, b), !0) : !1;
        },
    });
    b.PosAnimation = b.DomUtil.TRANSITION
        ? b.PosAnimation
        : b.PosAnimation.extend({
              run: function (a, c, e, f) {
                  this.stop();
                  this._el = a;
                  this._inProgress = !0;
                  this._duration = e || 0.25;
                  this._easeOutPower = 1 / Math.max(f || 0.5, 0.2);
                  this._startPos = b.DomUtil.getPosition(a);
                  this._offset = c.subtract(this._startPos);
                  this._startTime = +new Date();
                  this.fire("start");
                  this._animate();
              },
              stop: function () {
                  this._inProgress && (this._step(), this._complete());
              },
              _animate: function () {
                  this._animId = b.Util.requestAnimFrame(this._animate, this);
                  this._step();
              },
              _step: function () {
                  var a = +new Date() - this._startTime,
                      b = 1e3 * this._duration;
                  b > a ? this._runFrame(this._easeOut(a / b)) : (this._runFrame(1), this._complete());
              },
              _runFrame: function (a) {
                  a = this._startPos.add(this._offset.multiplyBy(a));
                  b.DomUtil.setPosition(this._el, a);
                  this.fire("step");
              },
              _complete: function () {
                  b.Util.cancelAnimFrame(this._animId);
                  this._inProgress = !1;
                  this.fire("end");
              },
              _easeOut: function (a) {
                  return 1 - Math.pow(1 - a, this._easeOutPower);
              },
          });
    b.Map.mergeOptions({ zoomAnimation: !0, zoomAnimationThreshold: 4 });
    b.DomUtil.TRANSITION &&
        b.Map.addInitHook(function () {
            (this._zoomAnimated = this.options.zoomAnimation && b.DomUtil.TRANSITION && b.Browser.any3d && !b.Browser.android23 && !b.Browser.mobileOpera) &&
                b.DomEvent.on(this._mapPane, b.DomUtil.TRANSITION_END, this._catchTransitionEnd, this);
        });
    b.Map.include(
        b.DomUtil.TRANSITION
            ? {
                  _catchTransitionEnd: function (a) {
                      this._animatingZoom && 0 <= a.propertyName.indexOf("transform") && this._onZoomTransitionEnd();
                  },
                  _nothingToAnimate: function () {
                      return !this._container.getElementsByClassName("leaflet-zoom-animated").length;
                  },
                  _tryAnimatedZoom: function (a, b, c) {
                      if (this._animatingZoom) return !0;
                      if (((c = c || {}), !this._zoomAnimated || !1 === c.animate || this._nothingToAnimate() || Math.abs(b - this._zoom) > this.options.zoomAnimationThreshold)) return !1;
                      var e = this.getZoomScale(b),
                          f = this._getCenterOffset(a)._divideBy(1 - 1 / e),
                          g = this._getCenterLayerPoint()._add(f);
                      return !0 === c.animate || this.getSize().contains(f) ? (this.fire("movestart").fire("zoomstart"), this._animateZoom(a, b, g, e, null, !0), !0) : !1;
                  },
                  _animateZoom: function (a, c, e, f, g, r, l) {
                      l || (this._animatingZoom = !0);
                      b.DomUtil.addClass(this._mapPane, "leaflet-zoom-anim");
                      this._animateToCenter = a;
                      this._animateToZoom = c;
                      b.Draggable && (b.Draggable._disabled = !0);
                      b.Util.requestAnimFrame(function () {
                          this.fire("zoomanim", { center: a, zoom: c, origin: e, scale: f, delta: g, backwards: r });
                      }, this);
                  },
                  _onZoomTransitionEnd: function () {
                      this._animatingZoom = !1;
                      b.DomUtil.removeClass(this._mapPane, "leaflet-zoom-anim");
                      this._resetView(this._animateToCenter, this._animateToZoom, !0, !0);
                      b.Draggable && (b.Draggable._disabled = !1);
                  },
              }
            : {}
    );
    b.TileLayer.include({
        _animateZoom: function (a) {
            this._animating || ((this._animating = !0), this._prepareBgBuffer());
            var c = this._bgBuffer,
                e = b.DomUtil.TRANSFORM,
                f = a.delta ? b.DomUtil.getTranslateString(a.delta) : c.style[e],
                g = b.DomUtil.getScaleString(a.scale, a.origin);
            c.style[e] = a.backwards ? g + " " + f : f + " " + g;
        },
        _endZoomAnim: function () {
            var a = this._tileContainer,
                c = this._bgBuffer;
            a.style.visibility = "";
            a.parentNode.appendChild(a);
            b.Util.falseFn(c.offsetWidth);
            this._animating = !1;
        },
        _clearBgBuffer: function () {
            var a = this._map;
            !a || a._animatingZoom || a.touchZoom._zooming || ((this._bgBuffer.innerHTML = ""), (this._bgBuffer.style[b.DomUtil.TRANSFORM] = ""));
        },
        _prepareBgBuffer: function () {
            var a = this._tileContainer,
                c = this._bgBuffer,
                e = this._getLoadedTilesPercentage(c),
                f = this._getLoadedTilesPercentage(a);
            return c && 0.5 < e && 0.5 > f
                ? ((a.style.visibility = "hidden"), void this._stopLoadingImages(a))
                : ((c.style.visibility = "hidden"), (c.style[b.DomUtil.TRANSFORM] = ""), (this._tileContainer = c), (c = this._bgBuffer = a), this._stopLoadingImages(c), void clearTimeout(this._clearBgBufferTimer));
        },
        _getLoadedTilesPercentage: function (a) {
            var b,
                c = a.getElementsByTagName("img"),
                e = 0;
            a = 0;
            for (b = c.length; b > a; a++) c[a].complete && e++;
            return e / b;
        },
        _stopLoadingImages: function (a) {
            var c,
                e,
                f = Array.prototype.slice.call(a.getElementsByTagName("img"));
            a = 0;
            for (c = f.length; c > a; a++) (e = f[a]), e.complete || ((e.onload = b.Util.falseFn), (e.onerror = b.Util.falseFn), (e.src = b.Util.emptyImageUrl), e.parentNode.removeChild(e));
        },
    });
    b.Map.include({
        _defaultLocateOptions: { watch: !1, setView: !1, maxZoom: 1 / 0, timeout: 1e4, maximumAge: 0, enableHighAccuracy: !1 },
        locate: function (a) {
            if (((a = this._locateOptions = b.extend(this._defaultLocateOptions, a)), !navigator.geolocation)) return this._handleGeolocationError({ code: 0, message: "Geolocation not supported." }), this;
            var c = b.bind(this._handleGeolocationResponse, this),
                e = b.bind(this._handleGeolocationError, this);
            return a.watch ? (this._locationWatchId = navigator.geolocation.watchPosition(c, e, a)) : navigator.geolocation.getCurrentPosition(c, e, a), this;
        },
        stopLocate: function () {
            return navigator.geolocation && navigator.geolocation.clearWatch(this._locationWatchId), this._locateOptions && (this._locateOptions.setView = !1), this;
        },
        _handleGeolocationError: function (a) {
            var b = a.code;
            a = a.message || (1 === b ? "permission denied" : 2 === b ? "position unavailable" : "timeout");
            this._locateOptions.setView && !this._loaded && this.fitWorld();
            this.fire("locationerror", { code: b, message: "Geolocation error: " + a + "." });
        },
        _handleGeolocationResponse: function (a) {
            var c = a.coords.latitude,
                e = a.coords.longitude,
                f = new b.LatLng(c, e),
                g = (180 * a.coords.accuracy) / 40075017,
                r = g / Math.cos(b.LatLng.DEG_TO_RAD * c),
                c = b.latLngBounds([c - g, e - r], [c + g, e + r]),
                e = this._locateOptions;
            e.setView && ((e = Math.min(this.getBoundsZoom(c), e.maxZoom)), this.setView(f, e));
            var f = { latlng: f, bounds: c, timestamp: a.timestamp },
                l;
            for (l in a.coords) "number" == typeof a.coords[l] && (f[l] = a.coords[l]);
            this.fire("locationfound", f);
        },
    });
})(window, document);
L.Control.Measure = L.Control.extend({
    options: { position: "topleft" },
    onAdd: function (f) {
        f = L.DomUtil.create("div", "leaflet-control-zoom leaflet-bar leaflet-control");
        //this._createButton("", "Measure", "leaflet-control-measure leaflet-bar-part leaflet-bar-part-top fa fa-arrows-h", f, this._toggleMeasure, this);
        //this.hideCalibration || this._createButton("", "Calibrate", "viewer-custom-buttons leaflet-disabled leaflet-control-measure leaflet-bar-part leaflet-bar-bottom fa fa-sliders btnCalibrateMMPerPixels", f, this._calibrate, this);
        return f;
    },
    _calibrate: function (f) {
        console.log("_calibrate = " + this.lastMeasurement);
        this.mmPerPixel = this.onCalibration(this.lastMeasurement);
    },
    _disableCalibration: function () {
        $(".btnCalibrateMMPerPixels").addClass("leaflet-disabled");
    },
    _enableCalibration: function () {
        $(".btnCalibrateMMPerPixels").removeClass("leaflet-disabled");
    },
    _createButton: function (f, g, c, e, b, r) {
        c = L.DomUtil.create("a", c, e);
        c.innerHTML = f;
        c.href = "#";
        c.title = g;
        L.DomEvent.on(c, "click", L.DomEvent.stopPropagation).on(c, "click", L.DomEvent.preventDefault).on(c, "click", b, r).on(c, "dblclick", L.DomEvent.stopPropagation);
        return c;
    },
    _toggleMeasure: function () {
        (this._measuring = !this._measuring) ? (L.DomUtil.addClass(this._container, "leaflet-control-measure-on"), this._startMeasuring()) : (L.DomUtil.removeClass(this._container, "leaflet-control-measure-on"), this._stopMeasuring());
        console.log("_toggleMeasure " + this._pixelDistance);
    },
    _startMeasuring: function () {
        this._oldCursor = this._map._container.style.cursor;
        this._map._container.style.cursor = "crosshair";
        this._doubleClickZoom = this._map.doubleClickZoom.enabled();
        this._map.doubleClickZoom.disable();
        L.DomEvent.on(this._map, "mousemove", this._mouseMove, this).on(this._map, "click", this._mouseClick, this).on(this._map, "dblclick", this._finishPath, this).on(document, "keydown", this._onKeyDown, this);
        this._layerPaint || (this._layerPaint = L.layerGroup().addTo(this._map));
        console.log("_startMeasuring " + this._pixelDistance);
    },
    _stopMeasuring: function () {
        this._map._container.style.cursor = this._oldCursor;
        L.DomEvent.off(document, "keydown", this._onKeyDown, this).off(this._map, "mousemove", this._mouseMove, this).off(this._map, "click", this._mouseClick, this).off(this._map, "dblclick", this._mouseClick, this);
        this._doubleClickZoom && this._map.doubleClickZoom.enable();
        this._layerPaint && this._layerPaint.clearLayers();
        this._restartPath();
        this._disableCalibration();
        console.log("_stopMeasuring " + this._pixelDistance);
        0 < this._pixelDistance && (this.lastMeasurement = this._pixelDistance);
    },
    _mouseMove: function (f) {
        f.latlng &&
            this._lastPoint &&
            (this._layerPaintPathTemp
                ? this._layerPaintPathTemp.spliceLatLngs(0, 2, this._lastPoint, f.latlng)
                : (this._layerPaintPathTemp = L.polyline([this._lastPoint, f.latlng], { color: "black", weight: 1.5, clickable: !1, dashArray: "6,3" }).addTo(this._layerPaint)),
            this._tooltip &&
                (this._distance || (this._distance = 0), this._updateTooltipPosition(f.latlng), (f = this._getPixelPoint(f.latlng)), (f = this._lastPixelPoint.pixelDistanceTo(f)), this._updateTooltipDistance(this._pixelDistance + f, f)));
    },
    _mouseClick: function (f) {
        if (f.latlng) {
            if (this._lastPoint && this._tooltip) {
                this._distance || (this._distance = 0);
                this._pixelDistance || (this._pixelDistance = 0);
                this._updateTooltipPosition(f.latlng);
                var g = this._getPixelPoint(f.latlng).pixelDistanceTo(this._lastPixelPoint);
                this._pixelDistance += g;
                var c = f.latlng.distanceTo(this._lastPoint);
                this._updateTooltipDistance(this._pixelDistance, g);
                this._distance += c;
                this._enableCalibration();
            }
            this._createTooltip(f.latlng);
            this._lastPoint && !this._layerPaintPath && (this._layerPaintPath = L.polyline([this._lastPoint], { color: "black", weight: 2, clickable: !1 }).addTo(this._layerPaint));
            this._layerPaintPath && this._layerPaintPath.addLatLng(f.latlng);
            this._lastCircle && this._layerPaint.removeLayer(this._lastCircle);
            this._lastCircle = new L.CircleMarker(f.latlng, { color: "black", opacity: 1, weight: 1, fill: !0, fillOpacity: 1, radius: 2, clickable: this._lastCircle ? !0 : !1 }).addTo(this._layerPaint);
            this._lastCircle.on(
                "click",
                function () {
                    this._finishPath();
                },
                this
            );
            this._lastPoint = f.latlng;
            this._lastPixelPoint = this._getPixelPoint(f.latlng);
            console.log("_mouseClick " + this._pixelDistance);
            this.lastMeasurement = this._pixelDistance;
        }
    },
    _finishPath: function () {
        this._lastCircle && this._layerPaint.removeLayer(this._lastCircle);
        this._tooltip && this._layerPaint.removeLayer(this._tooltip);
        this._layerPaint && this._layerPaintPathTemp && this._layerPaint.removeLayer(this._layerPaintPathTemp);
        this._restartPath();
    },
    _restartPath: function () {
        this._distance = 0;
        this._layerPaintPathTemp = this._layerPaintPath = this._lastPoint = this._lastCircle = this._tooltip = void 0;
        this._pixelDistance = 0;
        this._lastPixelPoint = void 0;
    },
    _createTooltip: function (f) {
        var g = L.divIcon({ className: "leaflet-measure-tooltip", iconAnchor: [-5, -5] });
        this._tooltip = L.marker(f, { icon: g, clickable: !1 }).addTo(this._layerPaint);
    },
    _updateTooltipPosition: function (f) {
        this._tooltip.setLatLng(f);
    },
    _updateTooltipDistance: function (f, g) {
        f || (f = g);
        var c = " pixels";
        this.mmPerPixel && 0 != this.mmPerPixel && ((c = " mm"), (f *= this.mmPerPixel), (g *= this.mmPerPixel));
        var e = '<div class="leaflet-measure-tooltip-total">' + f.toFixed(2) + c + "</div>";
        0 < g && f != g && (e += '<div class="leaflet-measure-tooltip-difference">(+' + g.toFixed(2) + c + ")</div>");
        this._tooltip._icon.innerHTML = e;
    },
    _onKeyDown: function (f) {
        27 == f.keyCode && (this._lastPoint ? this._finishPath() : this._toggleMeasure());
    },
    _getPixelPoint: function (f) {
        return {
            x: Math.round(f.lng * this.imageScaleFactor),
            y: this.imageHeight - Math.round(f.lat * this.imageScaleFactor),
            pixelDistanceTo: function (f) {
                return f ? Math.sqrt(Math.pow(f.x - this.x, 2) + Math.pow(f.y - this.y, 2)) : 0;
            },
        };
    },
    mmPerPixel: 0,
    lastMeasurement: 0,
    imageScaleFactor: 0,
    imageHeight: 0,
    imageWidth: 0,
    hideCalibration: !1,
    onCalibration: function (f) {
        console.log("onCalibration = " + this._pixelDistance);
        return this._pixelDistance;
    },
});
L.Map.mergeOptions({ measureControl: !1 });
L.Map.addInitHook(function () {
    if (this.options.measureControl) {
        this.measureControl = new L.Control.Measure();
        if ("object" === typeof this.options.measureControl) {
            var f = this.options.measureControl;
            f.mmPerPixel && (this.measureControl.mmPerPixel = f.mmPerPixel);
            f.imageScaleFactor && (this.measureControl.imageScaleFactor = f.imageScaleFactor);
            f.imageHeight && (this.measureControl.imageHeight = f.imageHeight);
            f.imageWidth && (this.measureControl.imageWidth = f.imageWidth);
            f.onCalibration && (this.measureControl.onCalibration = f.onCalibration);
            f.hideCalibration && (this.measureControl.hideCalibration = f.hideCalibration);
        }
        this.addControl(this.measureControl);
    }
});
L.control.measure = function (f) {
    return new L.Control.Measure(f);
};
(function () {
    L.Control.FullScreen = L.Control.extend({
        options: { position: "topleft", title: "Full Screen", forceSeparateButton: !1, forcePseudoFullscreen: !1 },
        onAdd: function (b) {
            var c;
            c = b.zoomControl && !this.options.forceSeparateButton ? b.zoomControl._container : L.DomUtil.create("div", "leaflet-bar");
            this._createButton(this.options.title, "leaflet-control-zoom-fullscreen", c, this.toogleFullScreen, b);
            return c;
        },
        _createButton: function (b, c, a, d, e) {
            c = L.DomUtil.create("a", c, a);
            c.href = "#";
            c.title = b;
            L.DomEvent.addListener(c, "click", L.DomEvent.stopPropagation).addListener(c, "click", L.DomEvent.preventDefault).addListener(c, "click", d, e);
            L.DomEvent.addListener(a, f.fullScreenEventName, L.DomEvent.stopPropagation).addListener(a, f.fullScreenEventName, L.DomEvent.preventDefault).addListener(a, f.fullScreenEventName, this._handleEscKey, e);
            L.DomEvent.addListener(document, f.fullScreenEventName, L.DomEvent.stopPropagation).addListener(document, f.fullScreenEventName, L.DomEvent.preventDefault).addListener(document, f.fullScreenEventName, this._handleEscKey, e);
            return c;
        },
        toogleFullScreen: function () {
            this._exitFired = !1;
            var b = this._container;
            this._isFullscreen
                ? (f.supportsFullScreen && !this.options.forcePseudoFullscreen ? f.cancelFullScreen(b) : L.DomUtil.removeClass(b, "leaflet-pseudo-fullscreen"),
                  this.invalidateSize(),
                  this.fire("exitFullscreen"),
                  (this._exitFired = !0),
                  (this._isFullscreen = !1))
                : (f.supportsFullScreen && !this.options.forcePseudoFullscreen ? f.requestFullScreen(b) : L.DomUtil.addClass(b, "leaflet-pseudo-fullscreen"), this.invalidateSize(), this.fire("enterFullscreen"), (this._isFullscreen = !0));
        },
        _handleEscKey: function () {
            f.isFullScreen(this) || this._exitFired || (this.fire("exitFullscreen"), (this._exitFired = !0), (this._isFullscreen = !1));
        },
    });
    L.Map.addInitHook(function () {
        this.options.fullscreenControl && ((this.fullscreenControl = L.control.fullscreen(this.options.fullscreenControlOptions)), this.addControl(this.fullscreenControl));
    });
    L.control.fullscreen = function (b) {
        return new L.Control.FullScreen(b);
    };
    var f = {
            supportsFullScreen: !1,
            isFullScreen: function () {
                return !1;
            },
            requestFullScreen: function () {},
            cancelFullScreen: function () {},
            fullScreenEventName: "",
            prefix: "",
        },
        g = ["webkit", "moz", "o", "ms", "khtml"];
    if ("undefined" != typeof document.exitFullscreen) f.supportsFullScreen = !0;
    else
        for (var c = 0, e = g.length; c < e; c++)
            if (((f.prefix = g[c]), "undefined" != typeof document[f.prefix + "CancelFullScreen"])) {
                f.supportsFullScreen = !0;
                break;
            }
    f.supportsFullScreen &&
        ((f.fullScreenEventName = f.prefix + "fullscreenchange"),
        (f.isFullScreen = function () {
            switch (this.prefix) {
                case "":
                    return document.fullScreen;
                case "webkit":
                    return document.webkitIsFullScreen;
                default:
                    return document[this.prefix + "FullScreen"];
            }
        }),
        (f.requestFullScreen = function (b) {
            return "" === this.prefix ? b.requestFullscreen() : b[this.prefix + "RequestFullScreen"]();
        }),
        (f.cancelFullScreen = function (b) {
            return "" === this.prefix ? document.exitFullscreen() : document[this.prefix + "CancelFullScreen"]();
        }));
    "undefined" != typeof jQuery &&
        (jQuery.fn.requestFullScreen = function () {
            return this.each(function () {
                var b = jQuery(this);
                f.supportsFullScreen && f.requestFullScreen(b);
            });
        });
    window.fullScreenApi = f;
})();
/*!(function (f, g) {
    L.drawVersion = "0.2.4-dev";
    L.drawLocal = {
        draw: {
            toolbar: {
                actions: { title: "Cancel drawing", text: "Cancel" },
                undo: { title: "Delete last point drawn", text: "Delete last point" },
                buttons: { polyline: "Draw a polyline", polygon: "Draw a polygon", rectangle: "Draw a rectangle", circle: "Draw a circle", marker: "Draw a marker" },
            },
            handlers: {
                circle: { tooltip: { start: "Click and drag to draw circle." } },
                marker: { tooltip: { start: "Click map to place marker." } },
                polygon: { tooltip: { start: "Click to start drawing shape.", cont: "Click to continue drawing shape.", end: "Click first point to close this shape." } },
                polyline: { error: "<strong>Error:</strong> shape edges cannot cross!", tooltip: { start: "Click to start drawing line.", cont: "Click to continue drawing line.", end: "Click last point to finish line." } },
                rectangle: { tooltip: { start: "Click and drag to draw rectangle." } },
                simpleshape: { tooltip: { end: "Release mouse to finish drawing." } },
            },
        },
        edit: {
            toolbar: {
                actions: { save: { title: "Save changes.", text: "Save" }, cancel: { title: "Cancel editing, discards all changes.", text: "Cancel" } },
                buttons: { edit: "Edit layers.", editDisabled: "No layers to edit.", remove: "Delete layers.", removeDisabled: "No layers to delete." },
            },
            handlers: { edit: { tooltip: { text: "Drag handles, or marker to edit feature.", subtext: "Click cancel to undo changes." } }, remove: { tooltip: { text: "Click on a feature to remove" } } },
        },
    };
    L.Draw = {};
    L.Draw.Feature = L.Handler.extend({
        includes: L.Mixin.Events,
        initialize: function (c, e) {
            this._map = c;
            this._container = c._container;
            this._overlayPane = c._panes.overlayPane;
            this._popupPane = c._panes.popupPane;
            e && e.shapeOptions && (e.shapeOptions = L.Util.extend({}, this.options.shapeOptions, e.shapeOptions));
            L.setOptions(this, e);
        },
        enable: function () {
            this._enabled || (this.fire("enabled", { handler: this.type }), this._map.fire("draw:drawstart", { layerType: this.type }), L.Handler.prototype.enable.call(this));
        },
        disable: function () {
            this._enabled && (L.Handler.prototype.disable.call(this), this._map.fire("draw:drawstop", { layerType: this.type }), this.fire("disabled", { handler: this.type }));
        },
        addHooks: function () {
            var c = this._map;
            c && (L.DomUtil.disableTextSelection(), c.getContainer().focus(), (this._tooltip = new L.Tooltip(this._map)), L.DomEvent.on(this._container, "keyup", this._cancelDrawing, this));
        },
        removeHooks: function () {
            this._map && (L.DomUtil.enableTextSelection(), this._tooltip.dispose(), (this._tooltip = null), L.DomEvent.off(this._container, "keyup", this._cancelDrawing, this));
        },
        setOptions: function (c) {
            L.setOptions(this, c);
        },
        _fireCreatedEvent: function (c) {
            this._map.fire("draw:created", { layer: c, layerType: this.type });
        },
        _cancelDrawing: function (c) {
            27 === c.keyCode && this.disable();
        },
    });
    L.Draw.Polyline = L.Draw.Feature.extend({
        statics: { TYPE: "polyline" },
        Poly: L.Polyline,
        options: {
            allowIntersection: !0,
            repeatMode: !1,
            drawError: { color: "#b00b00", timeout: 2500 },
            icon: new L.DivIcon({ iconSize: new L.Point(8, 8), className: "leaflet-div-icon leaflet-editing-icon" }),
            guidelineDistance: 20,
            maxGuideLineLength: 4e3,
            shapeOptions: { stroke: !0, color: "#f06eaa", weight: 4, opacity: 0.5, fill: !1, clickable: !0 },
            metric: !0,
            showLength: !0,
            zIndexOffset: 2e3,
        },
        initialize: function (c, e) {
            this.options.drawError.message = L.drawLocal.draw.handlers.polyline.error;
            e && e.drawError && (e.drawError = L.Util.extend({}, this.options.drawError, e.drawError));
            this.type = L.Draw.Polyline.TYPE;
            L.Draw.Feature.prototype.initialize.call(this, c, e);
        },
        addHooks: function () {
            L.Draw.Feature.prototype.addHooks.call(this);
            this._map &&
                ((this._markers = []),
                (this._markerGroup = new L.LayerGroup()),
                this._map.addLayer(this._markerGroup),
                (this._poly = new L.Polyline([], this.options.shapeOptions)),
                this._tooltip.updateContent(this._getTooltipText()),
                this._mouseMarker ||
                    (this._mouseMarker = L.marker(this._map.getCenter(), { icon: L.divIcon({ className: "leaflet-mouse-marker", iconAnchor: [20, 20], iconSize: [40, 40] }), opacity: 0, zIndexOffset: this.options.zIndexOffset })),
                this._mouseMarker.on("mousedown", this._onMouseDown, this).addTo(this._map),
                this._map.on("mousemove", this._onMouseMove, this).on("mouseup", this._onMouseUp, this).on("zoomend", this._onZoomEnd, this));
        },
        removeHooks: function () {
            L.Draw.Feature.prototype.removeHooks.call(this);
            this._clearHideErrorTimeout();
            this._cleanUpShape();
            this._map.removeLayer(this._markerGroup);
            delete this._markerGroup;
            delete this._markers;
            this._map.removeLayer(this._poly);
            delete this._poly;
            this._mouseMarker.off("mousedown", this._onMouseDown, this).off("mouseup", this._onMouseUp, this);
            this._map.removeLayer(this._mouseMarker);
            delete this._mouseMarker;
            this._clearGuides();
            this._map.off("mousemove", this._onMouseMove, this).off("zoomend", this._onZoomEnd, this);
        },
        deleteLastVertex: function () {
            if (!(1 >= this._markers.length)) {
                var c = this._markers.pop(),
                    e = this._poly,
                    b = this._poly.spliceLatLngs(e.getLatLngs().length - 1, 1)[0];
                this._markerGroup.removeLayer(c);
                2 > e.getLatLngs().length && this._map.removeLayer(e);
                this._vertexChanged(b, !1);
            }
        },
        addVertex: function (c) {
            return 0 < this._markers.length && !this.options.allowIntersection && this._poly.newLatLngIntersects(c)
                ? (this._showErrorTooltip(), void 0)
                : (this._errorShown && this._hideErrorTooltip(),
                  this._markers.push(this._createMarker(c)),
                  this._poly.addLatLng(c),
                  2 === this._poly.getLatLngs().length && this._map.addLayer(this._poly),
                  this._vertexChanged(c, !0),
                  void 0);
        },
        _finishShape: function () {
            var c = this._poly.newLatLngIntersects(this._poly.getLatLngs()[0], !0);
            return (!this.options.allowIntersection && c) || !this._shapeIsValid() ? (this._showErrorTooltip(), void 0) : (this._fireCreatedEvent(), this.disable(), this.options.repeatMode && this.enable(), void 0);
        },
        _shapeIsValid: function () {
            return !0;
        },
        _onZoomEnd: function () {
            this._updateGuide();
        },
        _onMouseMove: function (c) {
            var e = c.layerPoint,
                b = c.latlng;
            this._currentLatLng = b;
            this._updateTooltip(b);
            this._updateGuide(e);
            this._mouseMarker.setLatLng(b);
            L.DomEvent.preventDefault(c.originalEvent);
        },
        _vertexChanged: function (c, e) {
            this._updateFinishHandler();
            this._updateRunningMeasure(c, e);
            this._clearGuides();
            this._updateTooltip();
        },
        _onMouseDown: function (c) {
            c = c.originalEvent;
            this._mouseDownOrigin = L.point(c.clientX, c.clientY);
        },
        _onMouseUp: function (c) {
            if (this._mouseDownOrigin) {
                var e = L.point(c.originalEvent.clientX, c.originalEvent.clientY).distanceTo(this._mouseDownOrigin);
                Math.abs(e) < 9 * (f.devicePixelRatio || 1) && this.addVertex(c.latlng);
            }
            this._mouseDownOrigin = null;
        },
        _updateFinishHandler: function () {
            var c = this._markers.length;
            1 < c && this._markers[c - 1].on("click", this._finishShape, this);
            2 < c && this._markers[c - 2].off("click", this._finishShape, this);
        },
        _createMarker: function (c) {
            c = new L.Marker(c, { icon: this.options.icon, zIndexOffset: 2 * this.options.zIndexOffset });
            return this._markerGroup.addLayer(c), c;
        },
        _updateGuide: function (c) {
            var e = this._markers.length;
            0 < e && ((c = c || this._map.latLngToLayerPoint(this._currentLatLng)), this._clearGuides(), this._drawGuide(this._map.latLngToLayerPoint(this._markers[e - 1].getLatLng()), c));
        },
        _updateTooltip: function (c) {
            var e = this._getTooltipText();
            c && this._tooltip.updatePosition(c);
            this._errorShown || this._tooltip.updateContent(e);
        },
        _drawGuide: function (c, e) {
            var b,
                f,
                a = Math.floor(Math.sqrt(Math.pow(e.x - c.x, 2) + Math.pow(e.y - c.y, 2))),
                d = this.options.guidelineDistance;
            b = this.options.maxGuideLineLength;
            d = a > b ? a - b : d;
            for (this._guidesContainer || (this._guidesContainer = L.DomUtil.create("div", "leaflet-draw-guides", this._overlayPane)); a > d; d += this.options.guidelineDistance)
                (b = d / a),
                    (b = { x: Math.floor(c.x * (1 - b) + b * e.x), y: Math.floor(c.y * (1 - b) + b * e.y) }),
                    (f = L.DomUtil.create("div", "leaflet-draw-guide-dash", this._guidesContainer)),
                    (f.style.backgroundColor = this._errorShown ? this.options.drawError.color : this.options.shapeOptions.color),
                    L.DomUtil.setPosition(f, b);
        },
        _updateGuideColor: function (c) {
            if (this._guidesContainer) for (var e = 0, b = this._guidesContainer.childNodes.length; b > e; e++) this._guidesContainer.childNodes[e].style.backgroundColor = c;
        },
        _clearGuides: function () {
            if (this._guidesContainer) for (; this._guidesContainer.firstChild; ) this._guidesContainer.removeChild(this._guidesContainer.firstChild);
        },
        _getTooltipText: function () {
            var c,
                e,
                b = this.options.showLength;
            return (
                0 === this._markers.length
                    ? (c = { text: L.drawLocal.draw.handlers.polyline.tooltip.start })
                    : ((e = b ? this._getMeasurementString() : ""),
                      (c = 1 === this._markers.length ? { text: L.drawLocal.draw.handlers.polyline.tooltip.cont, subtext: e } : { text: L.drawLocal.draw.handlers.polyline.tooltip.end, subtext: e })),
                c
            );
        },
        _updateRunningMeasure: function (c, e) {
            var b,
                f,
                a = this._markers.length;
            1 === this._markers.length ? (this._measurementRunningTotal = 0) : ((b = a - (e ? 2 : 1)), (f = c.distanceTo(this._markers[b].getLatLng())), (this._measurementRunningTotal += f * (e ? 1 : -1)));
        },
        _getMeasurementString: function () {
            var c,
                e = this._currentLatLng,
                b = this._markers[this._markers.length - 1].getLatLng();
            return (c = this._measurementRunningTotal + e.distanceTo(b)), L.GeometryUtil.readableDistance(c, this.options.metric);
        },
        _showErrorTooltip: function () {
            this._errorShown = !0;
            this._tooltip.showAsError().updateContent({ text: this.options.drawError.message });
            this._updateGuideColor(this.options.drawError.color);
            this._poly.setStyle({ color: this.options.drawError.color });
            this._clearHideErrorTimeout();
            this._hideErrorTimeout = setTimeout(L.Util.bind(this._hideErrorTooltip, this), this.options.drawError.timeout);
        },
        _hideErrorTooltip: function () {
            this._errorShown = !1;
            this._clearHideErrorTimeout();
            this._tooltip.removeError().updateContent(this._getTooltipText());
            this._updateGuideColor(this.options.shapeOptions.color);
            this._poly.setStyle({ color: this.options.shapeOptions.color });
        },
        _clearHideErrorTimeout: function () {
            this._hideErrorTimeout && (clearTimeout(this._hideErrorTimeout), (this._hideErrorTimeout = null));
        },
        _cleanUpShape: function () {
            1 < this._markers.length && this._markers[this._markers.length - 1].off("click", this._finishShape, this);
        },
        _fireCreatedEvent: function () {
            var c = new this.Poly(this._poly.getLatLngs(), this.options.shapeOptions);
            L.Draw.Feature.prototype._fireCreatedEvent.call(this, c);
        },
    });
    L.Draw.Polygon = L.Draw.Polyline.extend({
        statics: { TYPE: "polygon" },
        Poly: L.Polygon,
        options: { showArea: !1, shapeOptions: { stroke: !0, color: "#f06eaa", weight: 4, opacity: 0.5, fill: !0, fillColor: null, fillOpacity: 0.2, clickable: !0 } },
        initialize: function (c, e) {
            L.Draw.Polyline.prototype.initialize.call(this, c, e);
            this.type = L.Draw.Polygon.TYPE;
        },
        _updateFinishHandler: function () {
            var c = this._markers.length;
            1 === c && this._markers[0].on("click", this._finishShape, this);
            2 < c && (this._markers[c - 1].on("dblclick", this._finishShape, this), 3 < c && this._markers[c - 2].off("dblclick", this._finishShape, this));
        },
        _getTooltipText: function () {
            var c, e;
            return (
                0 === this._markers.length
                    ? (c = L.drawLocal.draw.handlers.polygon.tooltip.start)
                    : 3 > this._markers.length
                    ? (c = L.drawLocal.draw.handlers.polygon.tooltip.cont)
                    : ((c = L.drawLocal.draw.handlers.polygon.tooltip.end), (e = this._getMeasurementString())),
                { text: c, subtext: e }
            );
        },
        _getMeasurementString: function () {
            var c = this._area;
            return c ? L.GeometryUtil.readableArea(c, this.options.metric) : null;
        },
        _shapeIsValid: function () {
            return 3 <= this._markers.length;
        },
        _vertexChanged: function (c, e) {
            var b;
            !this.options.allowIntersection && this.options.showArea && ((b = this._poly.getLatLngs()), (this._area = L.GeometryUtil.geodesicArea(b)));
            L.Draw.Polyline.prototype._vertexChanged.call(this, c, e);
        },
        _cleanUpShape: function () {
            var c = this._markers.length;
            0 < c && (this._markers[0].off("click", this._finishShape, this), 2 < c && this._markers[c - 1].off("dblclick", this._finishShape, this));
        },
    });
    L.SimpleShape = {};
    L.Draw.SimpleShape = L.Draw.Feature.extend({
        options: { repeatMode: !1 },
        initialize: function (c, e) {
            this._endLabelText = L.drawLocal.draw.handlers.simpleshape.tooltip.end;
            L.Draw.Feature.prototype.initialize.call(this, c, e);
        },
        addHooks: function () {
            L.Draw.Feature.prototype.addHooks.call(this);
            this._map &&
                ((this._mapDraggable = this._map.dragging.enabled()),
                this._mapDraggable && this._map.dragging.disable(),
                (this._container.style.cursor = "crosshair"),
                this._tooltip.updateContent({ text: this._initialLabelText }),
                this._map.on("mousedown", this._onMouseDown, this).on("mousemove", this._onMouseMove, this));
        },
        removeHooks: function () {
            L.Draw.Feature.prototype.removeHooks.call(this);
            this._map &&
                (this._mapDraggable && this._map.dragging.enable(),
                (this._container.style.cursor = ""),
                this._map.off("mousedown", this._onMouseDown, this).off("mousemove", this._onMouseMove, this),
                L.DomEvent.off(g, "mouseup", this._onMouseUp, this),
                this._shape && (this._map.removeLayer(this._shape), delete this._shape));
            this._isDrawing = !1;
        },
        _onMouseDown: function (c) {
            this._isDrawing = !0;
            this._startLatLng = c.latlng;
            L.DomEvent.on(g, "mouseup", this._onMouseUp, this).preventDefault(c.originalEvent);
        },
        _onMouseMove: function (c) {
            c = c.latlng;
            this._tooltip.updatePosition(c);
            this._isDrawing && (this._tooltip.updateContent({ text: this._endLabelText }), this._drawShape(c));
        },
        _onMouseUp: function () {
            this._shape && this._fireCreatedEvent();
            this.disable();
            this.options.repeatMode && this.enable();
        },
    });
    L.Draw.Rectangle = L.Draw.SimpleShape.extend({
        statics: { TYPE: "rectangle" },
        options: { shapeOptions: { stroke: !0, color: "#f06eaa", weight: 4, opacity: 0.5, fill: !0, fillColor: null, fillOpacity: 0.2, clickable: !0 } },
        initialize: function (c, e) {
            this.type = L.Draw.Rectangle.TYPE;
            this._initialLabelText = L.drawLocal.draw.handlers.rectangle.tooltip.start;
            L.Draw.SimpleShape.prototype.initialize.call(this, c, e);
        },
        _drawShape: function (c) {
            this._shape ? this._shape.setBounds(new L.LatLngBounds(this._startLatLng, c)) : ((this._shape = new L.Rectangle(new L.LatLngBounds(this._startLatLng, c), this.options.shapeOptions)), this._map.addLayer(this._shape));
        },
        _fireCreatedEvent: function () {
            var c = new L.Rectangle(this._shape.getBounds(), this.options.shapeOptions);
            L.Draw.SimpleShape.prototype._fireCreatedEvent.call(this, c);
        },
    });
    L.Draw.Circle = L.Draw.SimpleShape.extend({
        statics: { TYPE: "circle" },
        options: { shapeOptions: { stroke: !0, color: "#f06eaa", weight: 4, opacity: 0.5, fill: !0, fillColor: null, fillOpacity: 0.2, clickable: !0 }, showRadius: !0, metric: !0 },
        initialize: function (c, e) {
            this.type = L.Draw.Circle.TYPE;
            this._initialLabelText = L.drawLocal.draw.handlers.circle.tooltip.start;
            L.Draw.SimpleShape.prototype.initialize.call(this, c, e);
        },
        _drawShape: function (c) {
            this._shape ? this._shape.setRadius(this._startLatLng.distanceTo(c)) : ((this._shape = new L.Circle(this._startLatLng, this._startLatLng.distanceTo(c), this.options.shapeOptions)), this._map.addLayer(this._shape));
        },
        _fireCreatedEvent: function () {
            var c = new L.Circle(this._startLatLng, this._shape.getRadius(), this.options.shapeOptions);
            L.Draw.SimpleShape.prototype._fireCreatedEvent.call(this, c);
        },
        _onMouseMove: function (c) {
            var e;
            c = c.latlng;
            var b = this.options.showRadius,
                f = this.options.metric;
            this._tooltip.updatePosition(c);
            this._isDrawing && (this._drawShape(c), (e = this._shape.getRadius().toFixed(1)), this._tooltip.updateContent({ text: this._endLabelText, subtext: b ? "Radius: " + L.GeometryUtil.readableDistance(e, f) : "" }));
        },
    });
    L.Draw.Marker = L.Draw.Feature.extend({
        statics: { TYPE: "marker" },
        options: { icon: new L.Icon.Default(), repeatMode: !1, zIndexOffset: 2e3 },
        initialize: function (c, e) {
            this.type = L.Draw.Marker.TYPE;
            L.Draw.Feature.prototype.initialize.call(this, c, e);
        },
        addHooks: function () {
            L.Draw.Feature.prototype.addHooks.call(this);
            this._map &&
                (this._tooltip.updateContent({ text: L.drawLocal.draw.handlers.marker.tooltip.start }),
                this._mouseMarker ||
                    (this._mouseMarker = L.marker(this._map.getCenter(), { icon: L.divIcon({ className: "leaflet-mouse-marker", iconAnchor: [20, 20], iconSize: [40, 40] }), opacity: 0, zIndexOffset: this.options.zIndexOffset })),
                this._mouseMarker.on("click", this._onClick, this).addTo(this._map),
                this._map.on("mousemove", this._onMouseMove, this));
        },
        removeHooks: function () {
            L.Draw.Feature.prototype.removeHooks.call(this);
            this._map &&
                (this._marker && (this._marker.off("click", this._onClick, this), this._map.off("click", this._onClick, this).removeLayer(this._marker), delete this._marker),
                this._mouseMarker.off("click", this._onClick, this),
                this._map.removeLayer(this._mouseMarker),
                delete this._mouseMarker,
                this._map.off("mousemove", this._onMouseMove, this));
        },
        _onMouseMove: function (c) {
            c = c.latlng;
            this._tooltip.updatePosition(c);
            this._mouseMarker.setLatLng(c);
            this._marker
                ? ((c = this._mouseMarker.getLatLng()), this._marker.setLatLng(c))
                : ((this._marker = new L.Marker(c, { icon: this.options.icon, zIndexOffset: this.options.zIndexOffset })), this._marker.on("click", this._onClick, this), this._map.on("click", this._onClick, this).addLayer(this._marker));
        },
        _onClick: function () {
            this._fireCreatedEvent();
            this.disable();
            this.options.repeatMode && this.enable();
        },
        _fireCreatedEvent: function () {
            var c = new L.Marker(this._marker.getLatLng(), { icon: this.options.icon });
            L.Draw.Feature.prototype._fireCreatedEvent.call(this, c);
        },
    });
    L.Edit = L.Edit || {};
    L.Edit.Poly = L.Handler.extend({
        options: { icon: new L.DivIcon({ iconSize: new L.Point(8, 8), className: "leaflet-div-icon leaflet-editing-icon" }) },
        initialize: function (c, e) {
            this._poly = c;
            L.setOptions(this, e);
        },
        addHooks: function () {
            this._poly._map && (this._markerGroup || this._initMarkers(), this._poly._map.addLayer(this._markerGroup));
        },
        removeHooks: function () {
            this._poly._map && (this._poly._map.removeLayer(this._markerGroup), delete this._markerGroup, delete this._markers);
        },
        updateMarkers: function () {
            this._markerGroup.clearLayers();
            this._initMarkers();
        },
        _initMarkers: function () {
            this._markerGroup || (this._markerGroup = new L.LayerGroup());
            this._markers = [];
            var c,
                e,
                b,
                f = this._poly._latlngs;
            c = 0;
            for (b = f.length; b > c; c++) (e = this._createMarker(f[c], c)), e.on("click", this._onMarkerClick, this), this._markers.push(e);
            var a, d;
            c = 0;
            for (e = b - 1; b > c; e = c++) (0 !== c || (L.Polygon && this._poly instanceof L.Polygon)) && ((a = this._markers[e]), (d = this._markers[c]), this._createMiddleMarker(a, d), this._updatePrevNext(a, d));
        },
        _createMarker: function (c, e) {
            var b = new L.Marker(c, { draggable: !0, icon: this.options.icon });
            return (b._origLatLng = c), (b._index = e), b.on("drag", this._onMarkerDrag, this), b.on("dragend", this._fireEdit, this), this._markerGroup.addLayer(b), b;
        },
        _removeMarker: function (c) {
            var e = c._index;
            this._markerGroup.removeLayer(c);
            this._markers.splice(e, 1);
            this._poly.spliceLatLngs(e, 1);
            this._updateIndexes(e, -1);
            c.off("drag", this._onMarkerDrag, this).off("dragend", this._fireEdit, this).off("click", this._onMarkerClick, this);
        },
        _fireEdit: function () {
            this._poly.edited = !0;
            this._poly.fire("edit");
        },
        _onMarkerDrag: function (c) {
            c = c.target;
            L.extend(c._origLatLng, c._latlng);
            c._middleLeft && c._middleLeft.setLatLng(this._getMiddleLatLng(c._prev, c));
            c._middleRight && c._middleRight.setLatLng(this._getMiddleLatLng(c, c._next));
            this._poly.redraw();
        },
        _onMarkerClick: function (c) {
            c = c.target;
            this._poly._latlngs.length < (L.Polygon && this._poly instanceof L.Polygon ? 4 : 3) ||
                (this._removeMarker(c),
                this._updatePrevNext(c._prev, c._next),
                c._middleLeft && this._markerGroup.removeLayer(c._middleLeft),
                c._middleRight && this._markerGroup.removeLayer(c._middleRight),
                c._prev && c._next ? this._createMiddleMarker(c._prev, c._next) : c._prev ? c._next || (c._prev._middleRight = null) : (c._next._middleLeft = null),
                this._fireEdit());
        },
        _updateIndexes: function (c, e) {
            this._markerGroup.eachLayer(function (b) {
                b._index > c && (b._index += e);
            });
        },
        _createMiddleMarker: function (c, e) {
            var b,
                f,
                a,
                d = this._getMiddleLatLng(c, e),
                g = this._createMarker(d);
            g.setOpacity(0.6);
            c._middleRight = e._middleLeft = g;
            f = function () {
                var a = e._index;
                g._index = a;
                g.off("click", b, this).on("click", this._onMarkerClick, this);
                d.lat = g.getLatLng().lat;
                d.lng = g.getLatLng().lng;
                this._poly.spliceLatLngs(a, 0, d);
                this._markers.splice(a, 0, g);
                g.setOpacity(1);
                this._updateIndexes(a, 1);
                e._index++;
                this._updatePrevNext(c, g);
                this._updatePrevNext(g, e);
                this._poly.fire("editstart");
            };
            a = function () {
                g.off("dragstart", f, this);
                g.off("dragend", a, this);
                this._createMiddleMarker(c, g);
                this._createMiddleMarker(g, e);
            };
            b = function () {
                f.call(this);
                a.call(this);
                this._fireEdit();
            };
            g.on("click", b, this).on("dragstart", f, this).on("dragend", a, this);
            this._markerGroup.addLayer(g);
        },
        _updatePrevNext: function (c, e) {
            c && (c._next = e);
            e && (e._prev = c);
        },
        _getMiddleLatLng: function (c, e) {
            var b = this._poly._map;
            c = b.project(c.getLatLng());
            e = b.project(e.getLatLng());
            return b.unproject(c._add(e)._divideBy(2));
        },
    });
    L.Polyline.addInitHook(function () {
        this.editing ||
            (L.Edit.Poly && ((this.editing = new L.Edit.Poly(this)), this.options.editable && this.editing.enable()),
            this.on("add", function () {
                this.editing && this.editing.enabled() && this.editing.addHooks();
            }),
            this.on("remove", function () {
                this.editing && this.editing.enabled() && this.editing.removeHooks();
            }));
    });
    L.Edit = L.Edit || {};
    L.Edit.SimpleShape = L.Handler.extend({
        options: {
            moveIcon: new L.DivIcon({ iconSize: new L.Point(8, 8), className: "leaflet-div-icon leaflet-editing-icon leaflet-edit-move" }),
            resizeIcon: new L.DivIcon({ iconSize: new L.Point(8, 8), className: "leaflet-div-icon leaflet-editing-icon leaflet-edit-resize" }),
        },
        initialize: function (c, e) {
            this._shape = c;
            L.Util.setOptions(this, e);
        },
        addHooks: function () {
            this._shape._map && ((this._map = this._shape._map), this._markerGroup || this._initMarkers(), this._map.addLayer(this._markerGroup));
        },
        removeHooks: function () {
            if (this._shape._map) {
                this._unbindMarker(this._moveMarker);
                for (var c = 0, e = this._resizeMarkers.length; e > c; c++) this._unbindMarker(this._resizeMarkers[c]);
                this._resizeMarkers = null;
                this._map.removeLayer(this._markerGroup);
                delete this._markerGroup;
            }
            this._map = null;
        },
        updateMarkers: function () {
            this._markerGroup.clearLayers();
            this._initMarkers();
        },
        _initMarkers: function () {
            this._markerGroup || (this._markerGroup = new L.LayerGroup());
            this._createMoveMarker();
            this._createResizeMarker();
        },
        _createMoveMarker: function () {},
        _createResizeMarker: function () {},
        _createMarker: function (c, e) {
            c = new L.Marker(c, { draggable: !0, icon: e, zIndexOffset: 10 });
            return this._bindMarker(c), this._markerGroup.addLayer(c), c;
        },
        _bindMarker: function (c) {
            c.on("dragstart", this._onMarkerDragStart, this).on("drag", this._onMarkerDrag, this).on("dragend", this._onMarkerDragEnd, this);
        },
        _unbindMarker: function (c) {
            c.off("dragstart", this._onMarkerDragStart, this).off("drag", this._onMarkerDrag, this).off("dragend", this._onMarkerDragEnd, this);
        },
        _onMarkerDragStart: function (c) {
            c.target.setOpacity(0);
            this._shape.fire("editstart");
        },
        _fireEdit: function () {
            this._shape.edited = !0;
            this._shape.fire("edit");
        },
        _onMarkerDrag: function (c) {
            c = c.target;
            var e = c.getLatLng();
            c === this._moveMarker ? this._move(e) : this._resize(e);
            this._shape.redraw();
        },
        _onMarkerDragEnd: function (c) {
            c.target.setOpacity(1);
            this._fireEdit();
        },
        _move: function () {},
        _resize: function () {},
    });
    L.Edit = L.Edit || {};
    L.Edit.Rectangle = L.Edit.SimpleShape.extend({
        _createMoveMarker: function () {
            var c = this._shape.getBounds().getCenter();
            this._moveMarker = this._createMarker(c, this.options.moveIcon);
        },
        _createResizeMarker: function () {
            var c = this._getCorners();
            this._resizeMarkers = [];
            for (var e = 0, b = c.length; b > e; e++) this._resizeMarkers.push(this._createMarker(c[e], this.options.resizeIcon)), (this._resizeMarkers[e]._cornerIndex = e);
        },
        _onMarkerDragStart: function (c) {
            L.Edit.SimpleShape.prototype._onMarkerDragStart.call(this, c);
            var e = this._getCorners();
            c = c.target._cornerIndex;
            this._oppositeCorner = e[(c + 2) % 4];
            this._toggleCornerMarkers(0, c);
        },
        _onMarkerDragEnd: function (c) {
            var e,
                b,
                f = c.target;
            f === this._moveMarker && ((e = this._shape.getBounds()), (b = e.getCenter()), f.setLatLng(b));
            this._toggleCornerMarkers(1);
            this._repositionCornerMarkers();
            L.Edit.SimpleShape.prototype._onMarkerDragEnd.call(this, c);
        },
        _move: function (c) {
            for (var e, b = this._shape.getLatLngs(), f = this._shape.getBounds().getCenter(), a = [], d = 0, g = b.length; g > d; d++) (e = [b[d].lat - f.lat, b[d].lng - f.lng]), a.push([c.lat + e[0], c.lng + e[1]]);
            this._shape.setLatLngs(a);
            this._repositionCornerMarkers();
        },
        _resize: function (c) {
            this._shape.setBounds(L.latLngBounds(c, this._oppositeCorner));
            c = this._shape.getBounds();
            this._moveMarker.setLatLng(c.getCenter());
        },
        _getCorners: function () {
            var c = this._shape.getBounds(),
                e = c.getNorthWest(),
                b = c.getNorthEast(),
                f = c.getSouthEast(),
                c = c.getSouthWest();
            return [e, b, f, c];
        },
        _toggleCornerMarkers: function (c) {
            for (var e = 0, b = this._resizeMarkers.length; b > e; e++) this._resizeMarkers[e].setOpacity(c);
        },
        _repositionCornerMarkers: function () {
            for (var c = this._getCorners(), e = 0, b = this._resizeMarkers.length; b > e; e++) this._resizeMarkers[e].setLatLng(c[e]);
        },
    });
    L.Rectangle.addInitHook(function () {
        L.Edit.Rectangle && ((this.editing = new L.Edit.Rectangle(this)), this.options.editable && this.editing.enable());
    });
    L.Edit = L.Edit || {};
    L.Edit.Circle = L.Edit.SimpleShape.extend({
        _createMoveMarker: function () {
            var c = this._shape.getLatLng();
            this._moveMarker = this._createMarker(c, this.options.moveIcon);
        },
        _createResizeMarker: function () {
            var c = this._shape.getLatLng(),
                c = this._getResizeMarkerPoint(c);
            this._resizeMarkers = [];
            this._resizeMarkers.push(this._createMarker(c, this.options.resizeIcon));
        },
        _getResizeMarkerPoint: function (c) {
            var e = this._shape._radius * Math.cos(Math.PI / 4);
            c = this._map.project(c);
            return this._map.unproject([c.x + e, c.y - e]);
        },
        _move: function (c) {
            var e = this._getResizeMarkerPoint(c);
            this._resizeMarkers[0].setLatLng(e);
            this._shape.setLatLng(c);
        },
        _resize: function (c) {
            c = this._moveMarker.getLatLng().distanceTo(c);
            this._shape.setRadius(c);
        },
    });
    L.Circle.addInitHook(function () {
        L.Edit.Circle && ((this.editing = new L.Edit.Circle(this)), this.options.editable && this.editing.enable());
        this.on("add", function () {
            this.editing && this.editing.enabled() && this.editing.addHooks();
        });
        this.on("remove", function () {
            this.editing && this.editing.enabled() && this.editing.removeHooks();
        });
    });
    L.LatLngUtil = {
        cloneLatLngs: function (c) {
            for (var e = [], b = 0, f = c.length; f > b; b++) e.push(this.cloneLatLng(c[b]));
            return e;
        },
        cloneLatLng: function (c) {
            return L.latLng(c.lat, c.lng);
        },
    };
    L.GeometryUtil = L.extend(L.GeometryUtil || {}, {
        geodesicArea: function (c) {
            var e,
                b,
                f = c.length,
                a = 0,
                d = L.LatLng.DEG_TO_RAD;
            if (2 < f) {
                for (var g = 0; f > g; g++) (e = c[g]), (b = c[(g + 1) % f]), (a += (b.lng - e.lng) * d * (2 + Math.sin(e.lat * d) + Math.sin(b.lat * d)));
                a = (40680631590769 * a) / 2;
            }
            return Math.abs(a);
        },
        readableArea: function (c, e) {
            var b;
            return (
                e
                    ? (b = 1e4 <= c ? (1e-4 * c).toFixed(2) + " ha" : c.toFixed(2) + " m&sup2;")
                    : ((c *= 0.836127), (b = 3097600 <= c ? (c / 3097600).toFixed(2) + " mi&sup2;" : 4840 <= c ? (c / 4840).toFixed(2) + " acres" : Math.ceil(c) + " yd&sup2;")),
                b
            );
        },
        readableDistance: function (c, e) {
            var b;
            return e ? (b = 1e3 < c ? (c / 1e3).toFixed(2) + " km" : Math.ceil(c) + " m") : ((c *= 1.09361), (b = 1760 < c ? (c / 1760).toFixed(2) + " miles" : Math.ceil(c) + " yd")), b;
        },
    });
    L.Util.extend(L.LineUtil, {
        segmentsIntersect: function (c, e, b, f) {
            return this._checkCounterclockwise(c, b, f) !== this._checkCounterclockwise(e, b, f) && this._checkCounterclockwise(c, e, b) !== this._checkCounterclockwise(c, e, f);
        },
        _checkCounterclockwise: function (c, e, b) {
            return (b.y - c.y) * (e.x - c.x) > (e.y - c.y) * (b.x - c.x);
        },
    });
    L.Polyline.include({
        intersects: function () {
            var c,
                e,
                b,
                f = this._originalPoints;
            c = f ? f.length : 0;
            if (this._tooFewPointsForIntersection()) return !1;
            for (--c; 3 <= c; c--) if (((e = f[c - 1]), (b = f[c]), this._lineSegmentsIntersectsRange(e, b, c - 2))) return !0;
            return !1;
        },
        newLatLngIntersects: function (c, e) {
            return this._map ? this.newPointIntersects(this._map.latLngToLayerPoint(c), e) : !1;
        },
        newPointIntersects: function (c, e) {
            var b = this._originalPoints,
                f = b ? b.length : 0,
                b = b ? b[f - 1] : null,
                f = f - 2;
            return this._tooFewPointsForIntersection(1) ? !1 : this._lineSegmentsIntersectsRange(b, c, f, e ? 1 : 0);
        },
        _tooFewPointsForIntersection: function (c) {
            var e = this._originalPoints,
                e = e ? e.length : 0;
            return (e += c || 0), !this._originalPoints || 3 >= e;
        },
        _lineSegmentsIntersectsRange: function (c, e, b, f) {
            var a,
                d,
                g = this._originalPoints;
            for (f = f || 0; b > f; b--) if (((a = g[b - 1]), (d = g[b]), L.LineUtil.segmentsIntersect(c, e, a, d))) return !0;
            return !1;
        },
    });
    L.Polygon.include({
        intersects: function () {
            var c,
                e,
                b,
                f,
                a = this._originalPoints;
            return this._tooFewPointsForIntersection() ? !1 : L.Polyline.prototype.intersects.call(this) ? !0 : ((c = a.length), (e = a[0]), (b = a[c - 1]), (f = c - 2), this._lineSegmentsIntersectsRange(b, e, f, 1));
        },
    });
    L.Control.Draw = L.Control.extend({
        options: { position: "topleft", draw: {}, edit: !1 },
        initialize: function (c) {
            if ("0.7" > L.version) throw Error("Leaflet.draw 0.2.3+ requires Leaflet 0.7.0+. Download latest from https://github.com/Leaflet/Leaflet/");
            L.Control.prototype.initialize.call(this, c);
            var e, b;
            this._toolbars = {};
            L.DrawToolbar && this.options.draw && ((b = new L.DrawToolbar(this.options.draw)), (e = L.stamp(b)), (this._toolbars[e] = b), this._toolbars[e].on("enable", this._toolbarEnabled, this));
            L.EditToolbar && this.options.edit && ((b = new L.EditToolbar(this.options.edit)), (e = L.stamp(b)), (this._toolbars[e] = b), this._toolbars[e].on("enable", this._toolbarEnabled, this));
        },
        onAdd: function (c) {
            var e,
                b = L.DomUtil.create("div", "leaflet-draw"),
                f = !1,
                a;
            for (a in this._toolbars)
                this._toolbars.hasOwnProperty(a) &&
                    ((e = this._toolbars[a].addToolbar(c)), e && (f || (L.DomUtil.hasClass(e, "leaflet-draw-toolbar-top") || L.DomUtil.addClass(e.childNodes[0], "leaflet-draw-toolbar-top"), (f = !0)), b.appendChild(e)));
            return b;
        },
        onRemove: function () {
            for (var c in this._toolbars) this._toolbars.hasOwnProperty(c) && this._toolbars[c].removeToolbar();
        },
        setDrawingOptions: function (c) {
            for (var e in this._toolbars) this._toolbars[e] instanceof L.DrawToolbar && this._toolbars[e].setOptions(c);
        },
        _toolbarEnabled: function (c) {
            c = "" + L.stamp(c.target);
            for (var e in this._toolbars) this._toolbars.hasOwnProperty(e) && e !== c && this._toolbars[e].disable();
        },
    });
    L.Map.mergeOptions({ drawControlTooltips: !0, drawControl: !1 });
    L.Map.addInitHook(function () {
        this.options.drawControl && ((this.drawControl = new L.Control.Draw()), this.addControl(this.drawControl));
    });
    L.Toolbar = L.Class.extend({
        includes: [L.Mixin.Events],
        initialize: function (c) {
            L.setOptions(this, c);
            this._modes = {};
            this._actionButtons = [];
            this._activeMode = null;
        },
        enabled: function () {
            return null !== this._activeMode;
        },
        disable: function () {
            this.enabled() && this._activeMode.handler.disable();
        },
        addToolbar: function (c) {
            var e = L.DomUtil.create("div", "leaflet-draw-section"),
                b = 0,
                f = this._toolbarClass || "",
                a = this.getModeHandlers(c);
            this._toolbarContainer = L.DomUtil.create("div", "leaflet-draw-toolbar leaflet-bar");
            this._map = c;
            for (c = 0; c < a.length; c++) a[c].enabled && this._initModeHandler(a[c].handler, this._toolbarContainer, b++, f, a[c].title);
            return b ? ((this._lastButtonIndex = --b), (this._actionsContainer = L.DomUtil.create("ul", "leaflet-draw-actions")), e.appendChild(this._toolbarContainer), e.appendChild(this._actionsContainer), e) : void 0;
        },
        removeToolbar: function () {
            for (var c in this._modes)
                this._modes.hasOwnProperty(c) &&
                    (this._disposeButton(this._modes[c].button, this._modes[c].handler.enable, this._modes[c].handler),
                    this._modes[c].handler.disable(),
                    this._modes[c].handler.off("enabled", this._handlerActivated, this).off("disabled", this._handlerDeactivated, this));
            this._modes = {};
            c = 0;
            for (var e = this._actionButtons.length; e > c; c++) this._disposeButton(this._actionButtons[c].button, this._actionButtons[c].callback, this);
            this._actionButtons = [];
            this._actionsContainer = null;
        },
        _initModeHandler: function (c, e, b, f, a) {
            var d = c.type;
            this._modes[d] = {};
            this._modes[d].handler = c;
            this._modes[d].button = this._createButton({ title: a, className: f + "-" + d, container: e, callback: this._modes[d].handler.enable, context: this._modes[d].handler });
            this._modes[d].buttonIndex = b;
            this._modes[d].handler.on("enabled", this._handlerActivated, this).on("disabled", this._handlerDeactivated, this);
        },
        _createButton: function (c) {
            var e = L.DomUtil.create("a", c.className || "", c.container);
            return (
                (e.href = "#"),
                c.text && (e.innerHTML = c.text),
                c.title && (e.title = c.title),
                L.DomEvent.on(e, "click", L.DomEvent.stopPropagation)
                    .on(e, "mousedown", L.DomEvent.stopPropagation)
                    .on(e, "dblclick", L.DomEvent.stopPropagation)
                    .on(e, "click", L.DomEvent.preventDefault)
                    .on(e, "click", c.callback, c.context),
                e
            );
        },
        _disposeButton: function (c, e) {
            L.DomEvent.off(c, "click", L.DomEvent.stopPropagation).off(c, "mousedown", L.DomEvent.stopPropagation).off(c, "dblclick", L.DomEvent.stopPropagation).off(c, "click", L.DomEvent.preventDefault).off(c, "click", e);
        },
        _handlerActivated: function (c) {
            this.disable();
            this._activeMode = this._modes[c.handler];
            L.DomUtil.addClass(this._activeMode.button, "leaflet-draw-toolbar-button-enabled");
            this._showActionsToolbar();
            this.fire("enable");
        },
        _handlerDeactivated: function () {
            this._hideActionsToolbar();
            L.DomUtil.removeClass(this._activeMode.button, "leaflet-draw-toolbar-button-enabled");
            this._activeMode = null;
            this.fire("disable");
        },
        _createActions: function (c) {
            var e,
                b,
                f,
                a,
                d = this._actionsContainer;
            c = this.getActions(c);
            var g = c.length;
            b = 0;
            for (f = this._actionButtons.length; f > b; b++) this._disposeButton(this._actionButtons[b].button, this._actionButtons[b].callback);
            for (this._actionButtons = []; d.firstChild; ) d.removeChild(d.firstChild);
            for (b = 0; g > b; b++)
                ("enabled" in c[b] && !c[b].enabled) ||
                    ((e = L.DomUtil.create("li", "", d)),
                    (a = this._createButton({ title: c[b].title, text: c[b].text, container: e, callback: c[b].callback, context: c[b].context })),
                    this._actionButtons.push({ button: a, callback: c[b].callback }));
        },
        _showActionsToolbar: function () {
            var c = this._activeMode.buttonIndex,
                e = this._lastButtonIndex,
                b = this._activeMode.button.offsetTop - 1;
            this._createActions(this._activeMode.handler);
            this._actionsContainer.style.top = b + "px";
            0 === c && (L.DomUtil.addClass(this._toolbarContainer, "leaflet-draw-toolbar-notop"), L.DomUtil.addClass(this._actionsContainer, "leaflet-draw-actions-top"));
            c === e && (L.DomUtil.addClass(this._toolbarContainer, "leaflet-draw-toolbar-nobottom"), L.DomUtil.addClass(this._actionsContainer, "leaflet-draw-actions-bottom"));
            this._actionsContainer.style.display = "block";
        },
        _hideActionsToolbar: function () {
            this._actionsContainer.style.display = "none";
            L.DomUtil.removeClass(this._toolbarContainer, "leaflet-draw-toolbar-notop");
            L.DomUtil.removeClass(this._toolbarContainer, "leaflet-draw-toolbar-nobottom");
            L.DomUtil.removeClass(this._actionsContainer, "leaflet-draw-actions-top");
            L.DomUtil.removeClass(this._actionsContainer, "leaflet-draw-actions-bottom");
        },
    });
    L.Tooltip = L.Class.extend({
        initialize: function (c) {
            this._map = c;
            this._popupPane = c._panes.popupPane;
            this._container = c.options.drawControlTooltips ? L.DomUtil.create("div", "leaflet-draw-tooltip", this._popupPane) : null;
            this._singleLineLabel = !1;
        },
        dispose: function () {
            this._container && (this._popupPane.removeChild(this._container), (this._container = null));
        },
        updateContent: function (c) {
            return this._container
                ? ((c.subtext = c.subtext || ""),
                  0 !== c.subtext.length || this._singleLineLabel
                      ? 0 < c.subtext.length && this._singleLineLabel && (L.DomUtil.removeClass(this._container, "leaflet-draw-tooltip-single"), (this._singleLineLabel = !1))
                      : (L.DomUtil.addClass(this._container, "leaflet-draw-tooltip-single"), (this._singleLineLabel = !0)),
                  (this._container.innerHTML = (0 < c.subtext.length ? '<span class="leaflet-draw-tooltip-subtext">' + c.subtext + "</span><br />" : "") + "<span>" + c.text + "</span>"),
                  this)
                : this;
        },
        updatePosition: function (c) {
            c = this._map.latLngToLayerPoint(c);
            var e = this._container;
            return this._container && ((e.style.visibility = "inherit"), L.DomUtil.setPosition(e, c)), this;
        },
        showAsError: function () {
            return this._container && L.DomUtil.addClass(this._container, "leaflet-error-draw-tooltip"), this;
        },
        removeError: function () {
            return this._container && L.DomUtil.removeClass(this._container, "leaflet-error-draw-tooltip"), this;
        },
    });
    L.DrawToolbar = L.Toolbar.extend({
        options: { polyline: {}, polygon: {}, rectangle: {}, circle: {}, marker: {} },
        initialize: function (c) {
            for (var e in this.options) this.options.hasOwnProperty(e) && c[e] && (c[e] = L.extend({}, this.options[e], c[e]));
            this._toolbarClass = "leaflet-draw-draw";
            L.Toolbar.prototype.initialize.call(this, c);
        },
        getModeHandlers: function (c) {
            return [
                { enabled: this.options.polyline, handler: new L.Draw.Polyline(c, this.options.polyline), title: L.drawLocal.draw.toolbar.buttons.polyline },
                { enabled: this.options.polygon, handler: new L.Draw.Polygon(c, this.options.polygon), title: L.drawLocal.draw.toolbar.buttons.polygon },
                { enabled: this.options.rectangle, handler: new L.Draw.Rectangle(c, this.options.rectangle), title: L.drawLocal.draw.toolbar.buttons.rectangle },
                { enabled: this.options.circle, handler: new L.Draw.Circle(c, this.options.circle), title: L.drawLocal.draw.toolbar.buttons.circle },
                { enabled: this.options.marker, handler: new L.Draw.Marker(c, this.options.marker), title: L.drawLocal.draw.toolbar.buttons.marker },
            ];
        },
        getActions: function (c) {
            return [
                { enabled: c.deleteLastVertex, title: L.drawLocal.draw.toolbar.undo.title, text: L.drawLocal.draw.toolbar.undo.text, callback: c.deleteLastVertex, context: c },
                { title: L.drawLocal.draw.toolbar.actions.title, text: L.drawLocal.draw.toolbar.actions.text, callback: this.disable, context: this },
            ];
        },
        setOptions: function (c) {
            L.setOptions(this, c);
            for (var e in this._modes) this._modes.hasOwnProperty(e) && c.hasOwnProperty(e) && this._modes[e].handler.setOptions(c[e]);
        },
    });
    L.EditToolbar = L.Toolbar.extend({
        options: { edit: { selectedPathOptions: { color: "#fe57a1", opacity: 0.6, dashArray: "10, 10", fill: !0, fillColor: "#fe57a1", fillOpacity: 0.1 } }, remove: {}, featureGroup: null },
        initialize: function (c) {
            c.edit && ("undefined" == typeof c.edit.selectedPathOptions && (c.edit.selectedPathOptions = this.options.edit.selectedPathOptions), (c.edit = L.extend({}, this.options.edit, c.edit)));
            c.remove && (c.remove = L.extend({}, this.options.remove, c.remove));
            this._toolbarClass = "leaflet-draw-edit";
            L.Toolbar.prototype.initialize.call(this, c);
            this._selectedFeatureCount = 0;
        },
        getModeHandlers: function (c) {
            var e = this.options.featureGroup;
            return [
                { enabled: this.options.edit, handler: new L.EditToolbar.Edit(c, { featureGroup: e, selectedPathOptions: this.options.edit.selectedPathOptions }), title: L.drawLocal.edit.toolbar.buttons.edit },
                { enabled: this.options.remove, handler: new L.EditToolbar.Delete(c, { featureGroup: e }), title: L.drawLocal.edit.toolbar.buttons.remove },
            ];
        },
        getActions: function () {
            return [
                { title: L.drawLocal.edit.toolbar.actions.save.title, text: L.drawLocal.edit.toolbar.actions.save.text, callback: this._save, context: this },
                { title: L.drawLocal.edit.toolbar.actions.cancel.title, text: L.drawLocal.edit.toolbar.actions.cancel.text, callback: this.disable, context: this },
            ];
        },
        addToolbar: function (c) {
            c = L.Toolbar.prototype.addToolbar.call(this, c);
            return this._checkDisabled(), this.options.featureGroup.on("layeradd layerremove", this._checkDisabled, this), c;
        },
        removeToolbar: function () {
            this.options.featureGroup.off("layeradd layerremove", this._checkDisabled, this);
            L.Toolbar.prototype.removeToolbar.call(this);
        },
        disable: function () {
            this.enabled() && (this._activeMode.handler.revertLayers(), L.Toolbar.prototype.disable.call(this));
        },
        _save: function () {
            this._activeMode.handler.save();
            this._activeMode.handler.disable();
        },
        _checkDisabled: function () {
            var c,
                e = 0 !== this.options.featureGroup.getLayers().length;
            this.options.edit &&
                ((c = this._modes[L.EditToolbar.Edit.TYPE].button),
                e ? L.DomUtil.removeClass(c, "leaflet-disabled") : L.DomUtil.addClass(c, "leaflet-disabled"),
                c.setAttribute("title", e ? L.drawLocal.edit.toolbar.buttons.edit : L.drawLocal.edit.toolbar.buttons.editDisabled));
            this.options.remove &&
                ((c = this._modes[L.EditToolbar.Delete.TYPE].button),
                e ? L.DomUtil.removeClass(c, "leaflet-disabled") : L.DomUtil.addClass(c, "leaflet-disabled"),
                c.setAttribute("title", e ? L.drawLocal.edit.toolbar.buttons.remove : L.drawLocal.edit.toolbar.buttons.removeDisabled));
        },
    });
    L.EditToolbar.Edit = L.Handler.extend({
        statics: { TYPE: "edit" },
        includes: L.Mixin.Events,
        initialize: function (c, e) {
            if ((L.Handler.prototype.initialize.call(this, c), (this._selectedPathOptions = e.selectedPathOptions), (this._featureGroup = e.featureGroup), !(this._featureGroup instanceof L.FeatureGroup)))
                throw Error("options.featureGroup must be a L.FeatureGroup");
            this._uneditedLayerProps = {};
            this.type = L.EditToolbar.Edit.TYPE;
        },
        enable: function () {
            !this._enabled &&
                this._hasAvailableLayers() &&
                (this.fire("enabled", { handler: this.type }),
                this._map.fire("draw:editstart", { handler: this.type }),
                L.Handler.prototype.enable.call(this),
                this._featureGroup.on("layeradd", this._enableLayerEdit, this).on("layerremove", this._disableLayerEdit, this));
        },
        disable: function () {
            this._enabled &&
                (this._featureGroup.off("layeradd", this._enableLayerEdit, this).off("layerremove", this._disableLayerEdit, this),
                L.Handler.prototype.disable.call(this),
                this._map.fire("draw:editstop", { handler: this.type }),
                this.fire("disabled", { handler: this.type }));
        },
        addHooks: function () {
            var c = this._map;
            c &&
                (c.getContainer().focus(),
                this._featureGroup.eachLayer(this._enableLayerEdit, this),
                (this._tooltip = new L.Tooltip(this._map)),
                this._tooltip.updateContent({ text: L.drawLocal.edit.handlers.edit.tooltip.text, subtext: L.drawLocal.edit.handlers.edit.tooltip.subtext }),
                this._map.on("mousemove", this._onMouseMove, this));
        },
        removeHooks: function () {
            this._map && (this._featureGroup.eachLayer(this._disableLayerEdit, this), (this._uneditedLayerProps = {}), this._tooltip.dispose(), (this._tooltip = null), this._map.off("mousemove", this._onMouseMove, this));
        },
        revertLayers: function () {
            this._featureGroup.eachLayer(function (c) {
                this._revertLayer(c);
            }, this);
        },
        save: function () {
            var c = new L.LayerGroup();
            this._featureGroup.eachLayer(function (e) {
                e.edited && (c.addLayer(e), (e.edited = !1));
            });
            this._map.fire("draw:edited", { layers: c });
        },
        _backupLayer: function (c) {
            var e = L.Util.stamp(c);
            this._uneditedLayerProps[e] ||
                (c instanceof L.Polyline || c instanceof L.Polygon || c instanceof L.Rectangle
                    ? (this._uneditedLayerProps[e] = { latlngs: L.LatLngUtil.cloneLatLngs(c.getLatLngs()) })
                    : c instanceof L.Circle
                    ? (this._uneditedLayerProps[e] = { latlng: L.LatLngUtil.cloneLatLng(c.getLatLng()), radius: c.getRadius() })
                    : c instanceof L.Marker && (this._uneditedLayerProps[e] = { latlng: L.LatLngUtil.cloneLatLng(c.getLatLng()) }));
        },
        _revertLayer: function (c) {
            var e = L.Util.stamp(c);
            c.edited = !1;
            this._uneditedLayerProps.hasOwnProperty(e) &&
                (c instanceof L.Polyline || c instanceof L.Polygon || c instanceof L.Rectangle
                    ? c.setLatLngs(this._uneditedLayerProps[e].latlngs)
                    : c instanceof L.Circle
                    ? (c.setLatLng(this._uneditedLayerProps[e].latlng), c.setRadius(this._uneditedLayerProps[e].radius))
                    : c instanceof L.Marker && c.setLatLng(this._uneditedLayerProps[e].latlng));
        },
        _toggleMarkerHighlight: function (c) {
            c._icon &&
                ((c = c._icon),
                (c.style.display = "none"),
                L.DomUtil.hasClass(c, "leaflet-edit-marker-selected")
                    ? (L.DomUtil.removeClass(c, "leaflet-edit-marker-selected"), this._offsetMarker(c, -4))
                    : (L.DomUtil.addClass(c, "leaflet-edit-marker-selected"), this._offsetMarker(c, 4)),
                (c.style.display = ""));
        },
        _offsetMarker: function (c, e) {
            var b = parseInt(c.style.marginTop, 10) - e;
            e = parseInt(c.style.marginLeft, 10) - e;
            c.style.marginTop = b + "px";
            c.style.marginLeft = e + "px";
        },
        _enableLayerEdit: function (c) {
            var e;
            c = c.layer || c.target || c;
            var b = c instanceof L.Marker;
            (!b || c._icon) &&
                (this._backupLayer(c),
                this._selectedPathOptions &&
                    ((e = L.Util.extend({}, this._selectedPathOptions)),
                    b
                        ? this._toggleMarkerHighlight(c)
                        : ((c.options.previousOptions = L.Util.extend({ dashArray: null }, c.options)), c instanceof L.Circle || c instanceof L.Polygon || c instanceof L.Rectangle || (e.fill = !1), c.setStyle(e))),
                b ? (c.dragging.enable(), c.on("dragend", this._onMarkerDragEnd)) : c.editing.enable());
        },
        _disableLayerEdit: function (c) {
            c = c.layer || c.target || c;
            c.edited = !1;
            this._selectedPathOptions && (c instanceof L.Marker ? this._toggleMarkerHighlight(c) : (c.setStyle(c.options.previousOptions), delete c.options.previousOptions));
            c instanceof L.Marker ? (c.dragging.disable(), c.off("dragend", this._onMarkerDragEnd, this)) : c.editing.disable();
        },
        _onMarkerDragEnd: function (c) {
            c.target.edited = !0;
        },
        _onMouseMove: function (c) {
            this._tooltip.updatePosition(c.latlng);
        },
        _hasAvailableLayers: function () {
            return 0 !== this._featureGroup.getLayers().length;
        },
    });
    L.EditToolbar.Delete = L.Handler.extend({
        statics: { TYPE: "remove" },
        includes: L.Mixin.Events,
        initialize: function (c, e) {
            if ((L.Handler.prototype.initialize.call(this, c), L.Util.setOptions(this, e), (this._deletableLayers = this.options.featureGroup), !(this._deletableLayers instanceof L.FeatureGroup)))
                throw Error("options.featureGroup must be a L.FeatureGroup");
            this.type = L.EditToolbar.Delete.TYPE;
        },
        enable: function () {
            !this._enabled &&
                this._hasAvailableLayers() &&
                (this.fire("enabled", { handler: this.type }),
                this._map.fire("draw:deletestart", { handler: this.type }),
                L.Handler.prototype.enable.call(this),
                this._deletableLayers.on("layeradd", this._enableLayerDelete, this).on("layerremove", this._disableLayerDelete, this));
        },
        disable: function () {
            this._enabled &&
                (this._deletableLayers.off("layeradd", this._enableLayerDelete, this).off("layerremove", this._disableLayerDelete, this),
                L.Handler.prototype.disable.call(this),
                this._map.fire("draw:deletestop", { handler: this.type }),
                this.fire("disabled", { handler: this.type }));
        },
        addHooks: function () {
            var c = this._map;
            c &&
                (c.getContainer().focus(),
                this._deletableLayers.eachLayer(this._enableLayerDelete, this),
                (this._deletedLayers = new L.layerGroup()),
                (this._tooltip = new L.Tooltip(this._map)),
                this._tooltip.updateContent({ text: L.drawLocal.edit.handlers.remove.tooltip.text }),
                this._map.on("mousemove", this._onMouseMove, this));
        },
        removeHooks: function () {
            this._map && (this._deletableLayers.eachLayer(this._disableLayerDelete, this), (this._deletedLayers = null), this._tooltip.dispose(), (this._tooltip = null), this._map.off("mousemove", this._onMouseMove, this));
        },
        revertLayers: function () {
            this._deletedLayers.eachLayer(function (c) {
                this._deletableLayers.addLayer(c);
            }, this);
        },
        save: function () {
            this._map.fire("draw:deleted", { layers: this._deletedLayers });
        },
        _enableLayerDelete: function (c) {
            (c.layer || c.target || c).on("click", this._removeLayer, this);
        },
        _disableLayerDelete: function (c) {
            c = c.layer || c.target || c;
            c.off("click", this._removeLayer, this);
            this._deletedLayers.removeLayer(c);
        },
        _removeLayer: function (c) {
            c = c.layer || c.target || c;
            this._deletableLayers.removeLayer(c);
            this._deletedLayers.addLayer(c);
        },
        _onMouseMove: function (c) {
            this._tooltip.updatePosition(c.latlng);
        },
        _hasAvailableLayers: function () {
            return 0 !== this._deletableLayers.getLayers().length;
        },
    });
})(window, document);*/
(function () {
    function f(c) {
        c.Control.Loading = c.Control.extend({
            options: { position: "topleft", separate: !1, zoomControl: null, spinjs: !1, spin: { lines: 7, length: 3, width: 3, radius: 5, rotate: 13, top: "83%" } },
            initialize: function (e) {
                c.setOptions(this, e);
                this._dataLoaders = {};
                null !== this.options.zoomControl && (this.zoomControl = this.options.zoomControl);
            },
            onAdd: function (e) {
                if (this.options.spinjs && "function" !== typeof Spinner) return g.error("Leaflet.loading cannot load because you didn't load spin.js (http://fgnass.github.io/spin.js/), even though you set it in options.");
                this._addLayerListeners(e);
                this._addMapListeners(e);
                this.options.separate || this.zoomControl || (e.zoomControl ? (this.zoomControl = e.zoomControl) : e.zoomsliderControl && (this.zoomControl = e.zoomsliderControl));
                e = "leaflet-control-loading";
                var b;
                this.zoomControl && !this.options.separate
                    ? ((b = this.zoomControl._container), (e += " leaflet-bar-part-bottom leaflet-bar-part last"), c.DomUtil.addClass(this._getLastControlButton(), "leaflet-bar-part-bottom"))
                    : (b = c.DomUtil.create("div", "leaflet-control-zoom leaflet-bar"));
                this._indicator = c.DomUtil.create("a", e, b);
                this.options.spinjs && ((this._spinner = new Spinner(this.options.spin).spin()), this._indicator.appendChild(this._spinner.el));
                return b;
            },
            onRemove: function (c) {
                this._removeLayerListeners(c);
                this._removeMapListeners(c);
            },
            removeFrom: function (e) {
                return this.zoomControl && !this.options.separate ? (this._container.removeChild(this._indicator), (this._map = null), this.onRemove(e), this) : c.Control.prototype.removeFrom.call(this, e);
            },
            addLoader: function (c) {
                this._dataLoaders[c] = !0;
                this.updateIndicator();
            },
            removeLoader: function (c) {
                delete this._dataLoaders[c];
                this.updateIndicator();
            },
            updateIndicator: function () {
                this.isLoading() ? this._showIndicator() : this._hideIndicator();
            },
            isLoading: function () {
                return 0 < this._countLoaders();
            },
            _countLoaders: function () {
                var c = 0,
                    b;
                for (b in this._dataLoaders) this._dataLoaders.hasOwnProperty(b) && c++;
                return c;
            },
            _showIndicator: function () {
                c.DomUtil.addClass(this._indicator, "is-loading");
                this.options.separate ||
                    (this.zoomControl instanceof c.Control.Zoom
                        ? c.DomUtil.removeClass(this._getLastControlButton(), "leaflet-bar-part-bottom")
                        : "function" === typeof c.Control.Zoomslider && this.zoomControl instanceof c.Control.Zoomslider && c.DomUtil.removeClass(this.zoomControl._ui.zoomOut, "leaflet-bar-part-bottom"));
            },
            _hideIndicator: function () {
                c.DomUtil.removeClass(this._indicator, "is-loading");
                this.options.separate ||
                    (this.zoomControl instanceof c.Control.Zoom
                        ? c.DomUtil.addClass(this._getLastControlButton(), "leaflet-bar-part-bottom")
                        : "function" === typeof c.Control.Zoomslider && this.zoomControl instanceof c.Control.Zoomslider && c.DomUtil.addClass(this.zoomControl._ui.zoomOut, "leaflet-bar-part-bottom"));
            },
            _getLastControlButton: function () {
                for (var c = this.zoomControl._container, b = c.children.length - 1; 0 < b; ) {
                    var f = c.children[b];
                    if (this._indicator !== f && 0 !== f.offsetWidth && 0 !== f.offsetHeight) break;
                    b--;
                }
                return c.children[b];
            },
            _handleLoading: function (c) {
                this.addLoader(this.getEventId(c));
            },
            _handleLoad: function (c) {
                this.removeLoader(this.getEventId(c));
            },
            getEventId: function (c) {
                return c.id ? c.id : c.layer ? c.layer._leaflet_id : c.target._leaflet_id;
            },
            _layerAdd: function (c) {
                if (c.layer && c.layer.on)
                    try {
                        c.layer.on({ loading: this._handleLoading, load: this._handleLoad }, this);
                    } catch (b) {
                        g.warn("L.Control.Loading: Tried and failed to add  event handlers to layer", c.layer), g.warn("L.Control.Loading: Full details", b);
                    }
            },
            _addLayerListeners: function (c) {
                c.eachLayer(function (b) {
                    if (b.on) b.on({ loading: this._handleLoading, load: this._handleLoad }, this);
                }, this);
                c.on("layeradd", this._layerAdd, this);
            },
            _removeLayerListeners: function (c) {
                c.eachLayer(function (b) {
                    b.off && b.off({ loading: this._handleLoading, load: this._handleLoad }, this);
                }, this);
                c.off("layeradd", this._layerAdd, this);
            },
            _addMapListeners: function (c) {
                c.on({ dataloading: this._handleLoading, dataload: this._handleLoad, layerremove: this._handleLoad }, this);
            },
            _removeMapListeners: function (c) {
                c.off({ dataloading: this._handleLoading, dataload: this._handleLoad, layerremove: this._handleLoad }, this);
            },
        });
        c.Map.addInitHook(function () {
            this.options.loadingControl && ((this.loadingControl = new c.Control.Loading()), this.addControl(this.loadingControl));
        });
        c.Control.loading = function (e) {
            return new c.Control.Loading(e);
        };
    }
    var g = window.console || { error: function () {}, warn: function () {} };
    "function" === typeof define && define.amd
        ? define(["leaflet"], function (c) {
              f(c);
          })
        : f(L);
})();
Array.prototype.find ||
    Object.defineProperty(Array.prototype, "find", {
        value: function (f, g) {
            if (null == this) throw new TypeError('"this" is null or not defined');
            var c = Object(this),
                e = c.length >>> 0;
            if ("function" !== typeof f) throw new TypeError("predicate must be a function");
            for (var b = 0; b < e; ) {
                var r = c[b];
                if (f.call(g, r, b, c)) return r;
                b++;
            }
        },
    });
var imgvwr = {};
(function (f) {
    function g(a, b, c) {
        b = { target: b, imageId: c };
        $.extend(b, z, a);
        return b;
    }
    function c(a, b, c, d) {
        var e = $.Deferred(),
            f = d + "/ws/species?guid=" + a + "&dr=" + c;
        $.ajax({
            dataType: "json",
            url: f,
            type: "get",
            timeout: 8e3,
            success: function (a) {
                a
                    ? ((a = a.find(function (a) {
                          return a.kvpValues.find(function (a) {
                              return "imageId" == a.key && a.value == b;
                          });
                      })),
                      e.resolve(void 0 != a))
                    : e.resolve(!1);
            },
            error: function (a, b, c) {
                console.error("Error when calling " + f + " (" + c + ")");
                e.reject(a, b, c);
                return e;
            },
        });
        return e;
    }
    function e(a, b, c) {
        var d = $.Deferred();
        $.ajax({
            dataType: "json",
            url: c,
            type: "get",
            timeout: 8e3,
            success: function (c) {
                c
                    ? ((c = c.find(function (c) {
                          return c.name === a && c.imageId === b;
                      })),
                      d.resolve(void 0 != c))
                    : d.resolve(!1);
            },
            error: function (a, b, e) {
                console.error("Error when calling " + c + "(" + e + ")");
                d.reject(a, b, e);
                return d;
            },
        });
        return d;
    }
    function b(a) {
        $.ajax({ dataType: "jsonp", url: t + "/ws/image/" + l, crossDomain: !0 })
            .done(function (b) {
                b.success ? r(a, b) : alert("Unable to load image from " + t + "/ws/image/" + l);
            })
            .fail(function () {
                alert("Unable to load image from " + t + "/ws/image/" + l);
            });
    }
    function r(b, c) {
        l = b.imageId;
        var e = c.tileZoomLevels ? c.tileZoomLevels - 1 : 0;
        p = Math.pow(2, e);
        D = c.height;
        var g = c.width / 2 / p,
            r = c.height / 2 / p,
            w = L.latLng(c.height / p, 0),
            J = L.latLng(0, c.width / p),
            w = new L.latLngBounds(w, J),
            J = !1,
            E = new L.FeatureGroup();
        k = new L.FeatureGroup();
        b.addCalibration &&
            (J = {
                mmPerPixel: c.mmPerPixel,
                imageScaleFactor: p,
                imageWidth: c.width,
                imageHeight: c.height,
                hideCalibration: !b.addCalibration,
                onCalibration: function (a) {
                    f.showModal({ url: q + "/imageClient/calibrateImage?id=" + l + "&pixelLength=" + Math.round(a) + "&callback=calibrationCallback", title: "Calibrate image scale" });
                },
            });
        var G = $(b.target).get(0);
        x[G] && (x[G].remove(), delete x[G]);
        var v = L.map(G, { fullscreenControl: !0, measureControl: J, minZoom: 2, maxZoom: e, zoom: getInitialZoomLevel(b.initialZoom, b.zoomFudgeFactor, e, c, b.target), center: new L.LatLng(r, g), crs: L.CRS.Simple });
        n = v;
        v.addLayer(E);
        x[G] = v;
        L.tileLayer(c.tileUrlPattern, { maxNativeZoom: e, continuousWorld: !0, tms: !0, noWrap: !0, bounds: w, attribution: b.organisationName ? b.organisationName : "Atlas of Living Australia" }).addTo(v);
        b.addImageInfo &&
            ((e = L.Control.extend({
                options: { position: "bottomleft", title: "Image details" },
                onAdd: function (a) {
                    a = L.DomUtil.create("div", "leaflet-bar");
                    var b = t + "/image/" + l;
                    $(a).html("<a href='" + b + "' title='" + this.options.title + "' target='_blank'><span class='fa fa-external-link'></span></a>");
                    return a;
                },
            })),
            v.addControl(new e()));
        b.auxDataUrl &&
            ((e = L.Control.extend({
                options: { position: "topleft", title: "Auxiliary data" },
                onAdd: function (a) {
                    a = L.DomUtil.create("div", "leaflet-bar");
                    $(a).html("<a id='btnImageAuxInfo'  title='" + b.auxDataTitle + "' href='#'><span class='fa fa-info'></span></a>");
                    $(a)
                        .find("#btnImageAuxInfo")
                        .click(function (a) {
                            a.preventDefault();
                            $.ajax({ dataType: "jsonp", url: b.auxDataUrl, crossDomain: !0 }).done(function (a) {
                                var b = "";
                                if (a.data) {
                                    var b = '<table class="table table-condensed table-striped table-bordered">',
                                        c;
                                    for (c in a.data) b += "<tr><td>" + c + "</td><td>" + a.data[c] + "</td></tr>";
                                    b += "</table>";
                                }
                                a.link && a.linkText ? (b += '<div><a class="btn btn-primary" href="' + a.link + '">' + a.linkText + "</a>") : a.link && (b += '<div><a class="btn btn-primary" href="' + a.link + '">' + a.link + "</a>");
                                f.showModal({ title: a.title ? a.title : "Image " + l, content: b, width: 800 });
                            });
                        });
                    return a;
                },
            })),
            v.addControl(new e()));
        b.addDownloadButton &&
            ((e = L.Control.extend({
                options: { position: "topleft", title: "Download button" },
                onAdd: function (a) {
                    a = L.DomUtil.create("div", "leaflet-bar");
                    $(a).html("<a id='btnDownload' title='Download this image' href='#'><span class='fa fa-download'></span></a>");
                    $(a)
                        .find("#btnDownload")
                        .click(function (a) {
                            a.preventDefault();
                            window.location.href = t + "/image/proxyImage/" + l + "?contentDisposition=true";
                        });
                    return a;
                },
            })),
            v.addControl(new e()));
        b.addDrawer &&
            ((e = new L.Control.Draw({ edit: { featureGroup: E }, draw: { position: "topleft", circle: !1, rectangle: { shapeOptions: { weight: 1, color: "blue" } }, marker: !1, polyline: !1, polygon: !1 } })),
            v.addControl(e),
            $(".leaflet-draw-toolbar").last().append('<a id="btnCreateSubimage" class="viewer-custom-buttons leaflet-disabled fa fa-picture-o" href="#" title="Draw a rectangle to create a sub image"></a>'),
            $("#btnCreateSubimage").click(function (a) {
                a.preventDefault();
                a = E.getLayers();
                if (!(0 >= a.length)) {
                    a = a[0].getLatLngs();
                    for (var b = c.width, d = c.height, e = 0, g = 0, m = 0; m < a.length; ++m) {
                        var h = Math.round(c.height - a[m].lat * p),
                            k = Math.round(a[m].lng * p);
                        h < d && (d = h);
                        h > g && (g = h);
                        k < b && (b = k);
                        k > e && (e = k);
                    }
                    f.showModal({
                        title: "Create subimage",
                        url: q + "/imageClient/createSubImage?id=" + l + "&x=" + b + "&y=" + d + "&width=" + (e - b) + "&height=" + (g - d) + "&callback=createSubImageCallback",
                        onClose: function () {
                            E.clearLayers();
                        },
                    });
                }
            }),
            v.on("draw:created", function (a) {
                a = a.layer;
                E.clearLayers();
                E.addLayer(a);
                $("#btnCreateSubimage").removeClass("leaflet-disabled");
                $("#btnCreateSubimage").attr("title", "Create a subimage from the currently drawn rectangle");
            }),
            v.on("draw:deleted", function (a) {
                a = $("#btnCreateSubimage");
                a.addClass("leaflet-disabled");
                a.attr("title", "Draw a rectangle to create a subimage");
            }));
/*        b.addSubImageToggle &&
            (v.addLayer(k),
            (e = L.Control.extend({
                options: { position: "topright", title: "View subimages button" },
                onAdd: function (a) {
                    a = L.DomUtil.create("div", "leaflet-bar");
                    $(a).html("<a id='btnViewSubimages' data-switch='off' title='View subimages' href='#' style='width:110px;'>Show&nbsp;subimages</a>");
                    $(a)
                        .find("#btnViewSubimages")
                        .click(function (a) {
                            a.preventDefault();
                            f.toggleSubimages();
                        });
                    return a;
                },
            })),
            v.addControl(new e()));*/
        b.addPreferenceButton &&
            ((e = L.Control.extend({
                options: { position: "topleft", title: "Add image to ALA Preferred Species Images List", preferredImageStatus: m, savePreferredSpeciesListUrl: b.savePreferredSpeciesListUrl },
                onAdd: function (b) {
                    var c = this,
                        e = L.DomUtil.create("div", "leaflet-bar");
                    $(e).html("<a id='btnPreferredImage' title='Add image to Preferred Species Images List' href='#'><span class='fa fa-star'></span></a>");
                    $(e)
                        .find("#btnPreferredImage")
                        .click(function (b) {
                            b.preventDefault();
                            c.options.preferredImageStatus
                                ? a("You cannot add this images as it has already been added to ALA Preferred Species Image List")
                                : $.ajax({
                                      url: c.options.savePreferredSpeciesListUrl,
                                      success: function (b) {
                                          200 == b.status
                                              ? (d(e), 1 == c.options.preferredImageStatus, a("This Image has been successfully added to ALA Preferred Species Image List"))
                                              : a("An error has occurred: Status " + b.status + " Reason: " + b.text);
                                      },
                                      error: function (b) {
                                          a("An error occurred while saving metadata to image. Status: " + b.status + " Reason: " + b.responseText);
                                      },
                                  });
                        });
                    c.options.preferredImageStatus && d(e);
                    return e;
                },
            })),
            v.addControl(new e()));
        if (b.addLikeDislikeButton) {
            var B,
                e = L.Control.extend({
                    options: { position: "topleft", title: "Close this dialog", disableButtons: b.disableLikeDislikeButton, likeUrl: b.likeUrl, dislikeUrl: b.dislikeUrl, userRatingUrl: b.userRatingUrl },
                    onAdd: function (a) {
                        var c = this;
                        a = L.DomUtil.create("div", "leaflet-bar leaflet-control");
                        $(a).html(
                            '<a id="leafletLikeButton" href="#" class="fa fa-thumbs-o-up fa-2" aria-hidden="true"></i><a id="leafletDislikeButton" href="#" class="fa fa-thumbs-o-down fa-2" aria-hidden="true"></a><a id="leafletLikeDislikeHelpButton" href="#" class="fa fa-question fa-2" aria-hidden="true" title="Show help text"></a>'
                        );
                        $(a)
                            .find("#leafletLikeButton")
                            .on("click", function (a) {
                                a.preventDefault();
                                c.options.disableButtons ||
                                    (C.addLoader("like"),
                                    $.ajax({
                                        url: c.options.likeUrl,
                                        success: function (a) {
                                            a.content.success && (h(), C.removeLoader("like"));
                                        },
                                        error: function () {
                                            C.removeLoader("like");
                                        },
                                    }));
                            });
                        $(a)
                            .find("#leafletDislikeButton")
                            .on("click", function (a) {
                                a.preventDefault();
                                c.options.disableButtons ||
                                    (C.addLoader("dislike"),
                                    $.ajax({
                                        url: c.options.dislikeUrl,
                                        success: function (a) {
                                            a.content.success && (u(), C.removeLoader("dislike"));
                                        },
                                        error: function () {
                                            C.removeLoader("dislike");
                                        },
                                    }));
                            });
                        $(a)
                            .find("#leafletLikeDislikeHelpButton")
                            .on("click", function (a) {
                                B && (v.removeControl(B), (B = null));
                                B = new (L.Control.extend({
                                    options: { position: "topleft", userRatingHelpText: b.userRatingHelpText },
                                    onAdd: function (a) {
                                        this.container = a = L.DomUtil.create("div", "leaflet-control-layers");
                                        var b = this.options.userRatingHelpText || z.userRatingHelpText;
                                        $(a).html(
                                            '<div style="padding:10px; width: 200px;"><a href="#" class="user-rating-help-text-dialog pull-right" style="padding-left:10px;"><b style="color:black"><i class="fa fa-times"></i></b></a>' +
                                                b +
                                                "</div>"
                                        );
                                        $(a)
                                            .find(".user-rating-help-text-dialog")
                                            .on("click", function (a) {
                                                v.removeControl(B);
                                                B = null;
                                                a.preventDefault();
                                            });
                                        return a;
                                    },
                                }))();
                                v.addControl(B);
                                a.preventDefault();
                            });
                        this.options.disableButtons
                            ? ($(a).find("#leafletLikeButton").addClass("leaflet-disabled").attr("title", "You must be logged in"),
                              $(a).find("#leafletDislikeButton").addClass("leaflet-disabled").attr("title", "You must be logged in"),
                              $(a).attr("title", "You must be logged in"))
                            : this.options.userRatingUrl &&
                              $.ajax({
                                  url: this.options.userRatingUrl,
                                  success: function (a) {
                                      switch (a.success) {
                                          case "LIKE":
                                              h();
                                              break;
                                          case "DISLIKE":
                                              u();
                                      }
                                  },
                              });
                        return a;
                    },
                });
            v.addControl(new e());
        }
        if (b.addLoading) {
            var C = L.Control.loading({ separate: !0 });
            v.addControl(C);
        }
        b.addCloseButton &&
            ((e = L.Control.extend({
                options: { position: "topright", title: "Close this dialog" },
                onAdd: function (a) {
                    a = L.DomUtil.create("div", "leaflet-bar");
                    var b = $(a);
                    b.html('<a id="closeLeafletModalButton" href="#" class="">&times;</a>');
                    b.attr("title", "Close this dialog");
                    b.find("#closeLeafletModalButton").click(function (a) {
                        a.preventDefault();
                        $(this).parents(".modal").modal("hide");
                    });
                    return a;
                },
            })),
            v.addControl(new e()));
        b.addAttribution &&
            ((e = L.Control.extend({
                options: { position: "bottomright", title: "Show attribution", attribution: b.attribution },
                onAdd: function (a) {
                    this.container = a = L.DomUtil.create("div", "leaflet-control-layers");
                    $(a).html("<div style='padding:10px'>" + this.options.attribution + "</div>");
                    return a;
                },
            })),
            v.addControl(new e()));
        b.galleryOptions.enableGalleryMode &&
            (b.galleryOptions.closeControlContent &&
                ((e = L.Control.extend({
                    options: { position: "topright", title: "Close gallery", content: b.galleryOptions.closeControlContent },
                    onAdd: function (a) {
                        a = this.options;
                        var b = window.fullScreenApi.isFullScreen() ? "hidden" : "",
                            b = L.DomUtil.create("div", "leaflet-control-close-popup leaflet-bar leaflet-control " + b),
                            c = L.DomUtil.create("a", "", b);
                        c.innerHTML = a.content;
                        c.href = "#";
                        b.title = a.title;
                        L.DomEvent.on(b, "click", function () {
                            $(".leaflet-control-close-popup i").click();
                        });
                        return b;
                    },
                })),
                v.addControl(new e())),
            b.galleryOptions.showFullScreenControls &&
                ((e = L.Control.extend({
                    options: { position: "topright" },
                    onAdd: function () {
                        var a = window.fullScreenApi.isFullScreen() ? "" : "hidden",
                            a = L.DomUtil.create("div", "leaflet-gallery-control-bar leaflet-bar leaflet-control leaflet-bar-horizontal " + a, this._control),
                            b = L.DomUtil.create("a", "leaflet-control-previous", a);
                        b.innerHTML = '<i class="fa fa-arrow-left" style="line-height:1.65;"></i>';
                        b.title = "Got to previous image";
                        b = L.DomUtil.create("a", "leaflet-control-next", a);
                        b.innerHTML = '<i class="fa fa-arrow-right" style="line-height:1.65;"></i>';
                        b.title = "Got to next image";
                        return a;
                    },
                })),
                v.addControl(new e())),
            $(document).off(window.fullScreenApi.fullScreenEventName),
            $(document).on(window.fullScreenApi.fullScreenEventName, function (a) {
                window.fullScreenApi.isFullScreen()
                    ? ($(".leaflet-control-close-popup").addClass("hidden"), $(".leaflet-gallery-control-bar").removeClass("hidden"))
                    : ($(".leaflet-control-close-popup").removeClass("hidden"), $(".leaflet-gallery-control-bar").addClass("hidden"));
            }),
            $(document).off("click", "a.leaflet-control-previous"),
            $(document).on("click", "a.leaflet-control-previous", function () {
                var a = jQuery.Event("keydown");
                a.which = 37;
                $(document).trigger(a);
            }),
            $(document).off("click", "a.leaflet-control-next"),
            $(document).on("click", "a.leaflet-control-next", function () {
                var a = jQuery.Event("keydown");
                a.which = 39;
                $(document).trigger(a);
            }));
    }
    function a(a) {
        $("#alertModal").length && $("#alertContent").length ? ($("#alertContent").text(a), $("#alertModal").modal("show")) : bootbox.alert(a);
    }
    function d(a) {
        $(a).find("#btnPreferredImage").css("color", "orange").attr("title", "You have added this image to ALA Preferred Image Species List");
    }
    function h() {
        $("#leafletLikeButton").removeClass("fa-thumbs-o-up").addClass("fa-thumbs-up").css("color", "green").attr("title", "You up voted this image");
        $("#leafletDislikeButton").addClass("fa-thumbs-o-down").removeClass("fa-thumbs-down").css("color", "black").attr("title", "Click to down vote this image");
    }
    function u() {
        $("#leafletLikeButton").removeClass("fa-thumbs-up").addClass("fa-thumbs-o-up").css("color", "black").attr("title", "Click to up vote this image");
        $("#leafletDislikeButton").addClass("fa-thumbs-down").removeClass("fa-thumbs-o-down").css("color", "red").attr("title", "You down voted this image!");
    }
    var n,
        k,
        l,
        m,
        p,
        D,
        x = {},
        t = "https://images.ala.org.au",
        q = "",
        z = {
            auxDataUrl: "",
            auxDataTitle: "View more information about this image",
            attribution: null,
            initialZoom: "auto",
            zoomFudgeFactor: 1,
            disableLikeDislikeButton: !1,
            addDownloadButton: !0,
            addDrawer: !0,
            addSubImageToggle: !0,
            addCalibration: !0,
            addCloseButton: !1,
            addImageInfo: !0,
            addLoading: !0,
            addAttribution: !1,
            addPreferenceButton: !1,
            addLikeDislikeButton: !1,
            dislikeUrl: "",
            likeUrl: "",
            userRatingUrl: "",
            userRatingHelpText:
                '<div><b>Up vote (<i class="fa fa-thumbs-o-up" aria-hidden="true"></i>) an image:</b> Image supports the identification of the species or is representative of the species.\u2002\u2002Subject is clearly visible including identifying features.<br/><br/><b>Down vote (<i class="fa fa-thumbs-o-down" aria-hidden="true"></i>) an image:</b> Image does not support the identification of the species, subject is unclear and identifying features are difficult to see or not visible.<br/></div>',
            savePreferredSpeciesListUrl: "",
            getPreferredSpeciesListUrl: "",
            druid: "",
            galleryOptions: { enableGalleryMode: !1, closeControlContent: null, showFullScreenControls: !1 },
        };
    f.getImageClientBaseUrl = function () {
        return q;
    };
    f.getImageServiceBaseUrl = function () {
        return t;
    };
    f.setImageClientBaseUrl = function (a) {
        q = a;
    };
    f.setImageServiceBaseUrl = function (a) {
        t = a;
    };
    f.setPixelLength = function (a) {
        n.measureControl.mmPerPixel = a;
    };
    f.viewImage = function (a, d, h, k, p) {
        l = d;
        m = !1;
        p.imageServiceBaseUrl && f.setImageServiceBaseUrl(p.imageServiceBaseUrl);
        p.imageClientBaseUrl && f.setImageClientBaseUrl(p.imageClientBaseUrl);
        if (p.addPreferenceButton) {
            var n = null;
            void 0 != k ? (n = c(k, d, p.druid, p.getPreferredSpeciesListUrl)) : void 0 != h && (n = e(h, d, p.getPreferredSpeciesListUrl));
            $.when(n).then(
                function (c) {
                    void 0 != c && (m = c);
                    c = g(p, a, d);
                    b(c);
                },
                function (c) {
                    p.addPreferenceButton = !1;
                    c = g(p, a, d);
                    b(c);
                }
            );
        } else (h = g(p, a, d)), b(h);
    };
    f.resizeViewer = function (a) {
        a = $(a).get(0);
        x[a].invalidateSize();
    };
    f.removeCurrentImage = function () {
        n &&
            n.eachLayer(function (a) {
                n.removeLayer(a);
            });
    };
    f.getViewerInstance = function () {
        return n;
    };
    f.showSubimages = function () {
        k.clearLayers();
        $.ajax(t + "/ws/getSubimageRectangles/" + l).done(function (a) {
            if (a.success) {
                for (var b in a.subimages) {
                    var c = a.subimages[b],
                        d = c.imageId,
                        c = L.rectangle(
                            [
                                [(D - c.y) / p, c.x / p],
                                [(D - (c.y + c.height)) / p, (c.x + c.width) / p],
                            ],
                            { color: "#ff7800", weight: 1, imageId: d, className: d }
                        );
                    c.addTo(k);
                    c.on("click", function (a) {
                        if ((a = a.target.options.imageId)) window.location = t + "/image/" + a;
                    });
                    c.on("mouseover", function (a) {
                        var b = L.popup()
                            .setLatLng(a.latlng)
                            .setContent("<p>Loading.." + a.target.options.imageId + ".</p>")
                            .openOn(n);
                        console.log("Loading - " + t + "/image/imageTooltipFragment?imageId=" + a.target.options.imageId);
                        $.ajax(t + "/image/imageTooltipFragment?imageId=" + a.target.options.imageId).then(
                            function (a) {
                                b.setContent(a);
                            },
                            function (a, b, c) {
                                console.log(b + ": " + c);
                            }
                        );
                    });
                    c.on("mouseout", function (a) {
                        this.closePopup();
                    });
                }
                $(".subimage-path").each(function () {
                    var a = $(this).attr("class"),
                        a = $.trim(a).split(" ");
                    for (index in a) if (a[index].match(/imageId[-](.*)/)) break;
                });
            }
            $("#btnViewSubimages").html("Hide&nbsp;subimages");
            $.data($("#btnViewSubimages"), "switch", "on");
        });
    };
    f.hideSubimages = function () {
        k.clearLayers();
    };
    f.toggleSubimages = function () {
        "off" == $(this).data("switch") || void 0 === $(this).data("switch")
            ? (f.showSubimages(), $("#btnViewSubimages").html("Hide&nbsp;subimages"), $.data(this, "switch", "on"))
            : (f.hideSubimages(), $("#btnViewSubimages").html("Show&nbsp;subimages"), $.data(this, "switch", "off"));
    };
    getInitialZoomLevel = function (a, b, c, d, e) {
        var f = c;
        if ("auto" == a)
            if (((a = $(e).width()), (e = $(e).height()), (c = d.width), (d = d.height), c > d)) for (; a < c * b && 0 < f; ) f--, (c /= 2);
            else for (; e < d * b && 0 < f; ) f--, (d /= 2);
        else $.isNumeric(a) && Math.abs(a) <= c && (f = Math.abs(a));
        return f;
    };
    f.showModal = function (a) {
        var b = a.url ? a.url : !1,
            c = a.id ? a.id : "modal_element_id",
            d = a.title ? a.title : "Modal Title",
            e = a.hideHeader ? a.hideHeader : !1;
        a = a.content;
        $("body")
            .find("#" + c)
            .remove();
        var f;
        f = "<div id='" + c + "' class='modal fade' role='dialog' tabindex='-1'><div class=\"modal-dialog\" role=\"document\"><div class=\"modal-content\"><div class='modal-header'>";
        e || (f += "<h3 id='modal_label_" + c + "' class='modal-title'>" + d + "</h3>");
        f = f + "</div>" + ("<div  id='modal_content_" + c + "' class='modal-body' >" + (a ? a : "Loading...") + "</div>");
        f += '<div class="modal-footer"><button type="button" class="btn btn-default" data-dismiss="modal">Close</button></div></div></div></div>';
        $("body").append(f);
        $("#modal_content_" + c).load(b);
        $("#" + c).modal("show");
    };
    f.hideModal = function () {
        $("#modal_element_id").modal("hide");
    };
    f.areYouSureOptions = {};
    f.areYouSure = function (a) {
        a.title || (a.title = "Are you sure?");
        a.message || (a.message = a.title);
        var b = { url: t + "/dialog/areYouSureFragment?message=" + encodeURIComponent(a.message), title: a.title };
        f.areYouSureOptions.affirmativeAction = a.affirmativeAction;
        f.areYouSureOptions.negativeAction = a.negativeAction;
        f.showModal(b);
    };
    f.onAlbumSelected = null;
    f.selectAlbum = function (a) {
        var b = { title: "Select an album", url: t + "/album/selectAlbumFragment" };
        f.onAlbumSelected = function (b) {
            f.hideModal();
            a && a(b);
        };
        f.showModal(b);
    };
    f.onTagSelected = null;
    f.onTagCreated = null;
    f.selectTag = function (a) {
        var b = { width: 700, title: "Select a tag", url: t + "/tag/selectTagFragment" };
        f.onTagSelected = function (b) {
            f.hideModal();
            a && a(b);
        };
        f.showModal(b);
    };
    f.createNewTag = function (a, b) {
        a = { title: "Create new tag from path", url: t + "/tag/createTagFragment?parentTagId=" + a };
        f.onTagCreated = function (a) {
            f.hideModal();
            b && b(a);
        };
        f.showModal(a);
    };
    f.onAddMetadata = null;
    f.promptForMetadata = function (a) {
        var b = { title: "Add meta data item", url: t + "/dialog/addUserMetadataFragment" };
        f.onAddMetadata = function (b, c) {
            f.hideModal();
            a && a(b, c);
        };
        f.showModal(b);
    };
    f.bindImageTagTooltips = function () {
        $(".image-tags-button").each(function () {
            var a = $(this).closest("[imageId]").attr("imageId");
            a &&
                $(this).qtip({
                    content: {
                        text: function (b, c) {
                            $.ajax(t + "/image/imageTagsTooltipFragment/" + a).then(
                                function (a) {
                                    c.set("content.text", a);
                                },
                                function (a, b, d) {
                                    c.set("content.text", b + ": " + d);
                                }
                            );
                        },
                    },
                });
        });
    };
    f.htmlEscape = function (a) {
        return String(a).replace(/&/g, "&amp;").replace(/"/g, "&quot;").replace(/'/g, "&#39;").replace(/</g, "&lt;").replace(/>/g, "&gt;");
    };
    f.htmlUnescape = function (a) {
        return String(a)
            .replace(/&quot;/g, '"')
            .replace(/&#39;/g, "'")
            .replace(/&lt;/g, "<")
            .replace(/&gt;/g, ">")
            .replace(/&amp;/g, "&");
    };
    f.showSpinner = function (a) {
        var b = $(".spinner");
        a ? b.attr("title", a) : b.attr("title", "");
        b.css("display", "block");
    };
    f.hideSpinner = function () {
        $(".spinner").css("display", "none");
    };
    f.bindTooltips = function (a, b) {
        a || (a = "a.fieldHelp");
        b || (b = 300);
        $(a).each(function () {
            var a = $(this).attr("tooltipPosition");
            a || (a = "bottomRight");
            var c = $(this).attr("targetPosition");
            c || (c = "topMiddle");
            var d = $(this).attr("tipPosition");
            d || (d = "bottomRight");
            var e = $(this).attr("width");
            e && (b = e);
            $(this)
                .qtip({
                    tip: !0,
                    position: { corner: { target: c, tooltip: a } },
                    style: { width: b, padding: 8, background: "white", color: "black", textAlign: "left", border: { width: 4, radius: 5, color: "#E66542" }, tip: d, name: "light" },
                })
                .bind("click", function (a) {
                    a.preventDefault();
                    return !1;
                });
        });
    };
})(imgvwr);
var bootbox =
    window.bootbox ||
    (function (f, g) {
        function c(a, d) {
            return "undefined" == typeof d && (d = e), "string" == typeof k[d][a] ? k[d][a] : d != b ? c(a, b) : a;
        }
        var e = "en",
            b = "en",
            r = !0,
            a = "static",
            d = "",
            h = {},
            u = {},
            n = {
                setLocale: function (a) {
                    for (var b in k) if (b == a) return (e = a), void 0;
                    throw Error("Invalid locale: " + a);
                },
                addLocale: function (a, b) {
                    "undefined" == typeof k[a] && (k[a] = {});
                    for (var c in b) k[a][c] = b[c];
                },
                setIcons: function (a) {
                    u = a;
                    ("object" == typeof u && null !== u) || (u = {});
                },
                setBtnClasses: function (a) {
                    h = a;
                    ("object" == typeof h && null !== h) || (h = {});
                },
                alert: function () {
                    var a,
                        b = c("OK"),
                        d = null;
                    switch (arguments.length) {
                        case 1:
                            a = arguments[0];
                            break;
                        case 2:
                            a = arguments[0];
                            "function" == typeof arguments[1] ? (d = arguments[1]) : (b = arguments[1]);
                            break;
                        case 3:
                            a = arguments[0];
                            b = arguments[1];
                            d = arguments[2];
                            break;
                        default:
                            throw Error("Incorrect number of arguments: expected 1-3");
                    }
                    return n.dialog(a, { label: b, icon: u.OK, class: h.OK, callback: d }, { onEscape: d || !0 });
                },
                confirm: function () {
                    var a = "",
                        b = c("CANCEL"),
                        d = c("CONFIRM"),
                        e = null;
                    switch (arguments.length) {
                        case 1:
                            a = arguments[0];
                            break;
                        case 2:
                            a = arguments[0];
                            "function" == typeof arguments[1] ? (e = arguments[1]) : (b = arguments[1]);
                            break;
                        case 3:
                            a = arguments[0];
                            b = arguments[1];
                            "function" == typeof arguments[2] ? (e = arguments[2]) : (d = arguments[2]);
                            break;
                        case 4:
                            a = arguments[0];
                            b = arguments[1];
                            d = arguments[2];
                            e = arguments[3];
                            break;
                        default:
                            throw Error("Incorrect number of arguments: expected 1-4");
                    }
                    var f = function () {
                        return "function" == typeof e ? e(!1) : void 0;
                    };
                    return n.dialog(
                        a,
                        [
                            { label: b, icon: u.CANCEL, class: h.CANCEL, callback: f },
                            {
                                label: d,
                                icon: u.CONFIRM,
                                class: h.CONFIRM,
                                callback: function () {
                                    return "function" == typeof e ? e(!0) : void 0;
                                },
                            },
                        ],
                        { onEscape: f }
                    );
                },
                prompt: function () {
                    var a = "",
                        b = c("CANCEL"),
                        d = c("CONFIRM"),
                        e = null,
                        f = "";
                    switch (arguments.length) {
                        case 1:
                            a = arguments[0];
                            break;
                        case 2:
                            a = arguments[0];
                            "function" == typeof arguments[1] ? (e = arguments[1]) : (b = arguments[1]);
                            break;
                        case 3:
                            a = arguments[0];
                            b = arguments[1];
                            "function" == typeof arguments[2] ? (e = arguments[2]) : (d = arguments[2]);
                            break;
                        case 4:
                            a = arguments[0];
                            b = arguments[1];
                            d = arguments[2];
                            e = arguments[3];
                            break;
                        case 5:
                            a = arguments[0];
                            b = arguments[1];
                            d = arguments[2];
                            e = arguments[3];
                            f = arguments[4];
                            break;
                        default:
                            throw Error("Incorrect number of arguments: expected 1-5");
                    }
                    var k = g("<form></form>");
                    k.append("<input class='input-block-level' autocomplete=off type=text value='" + f + "' />");
                    var f = function () {
                            return "function" == typeof e ? e(null) : void 0;
                        },
                        q = n.dialog(
                            k,
                            [
                                { label: b, icon: u.CANCEL, class: h.CANCEL, callback: f },
                                {
                                    label: d,
                                    icon: u.CONFIRM,
                                    class: h.CONFIRM,
                                    callback: function () {
                                        return "function" == typeof e ? e(k.find("input[type=text]").val()) : void 0;
                                    },
                                },
                            ],
                            { header: a, show: !1, onEscape: f }
                        );
                    return (
                        q.on("shown", function () {
                            k.find("input[type=text]").focus();
                            k.on("submit", function (a) {
                                a.preventDefault();
                                q.find(".btn-primary").click();
                            });
                        }),
                        q.modal("show"),
                        q
                    );
                },
                dialog: function (b, c, e) {
                    function h() {
                        var a = null;
                        "function" == typeof e.onEscape && (a = e.onEscape());
                        !1 !== a && w.modal("hide");
                    }
                    var k = "",
                        n = [];
                    e || (e = {});
                    "undefined" == typeof c ? (c = []) : "undefined" == typeof c.length && (c = [c]);
                    for (var q = c.length; q--; ) {
                        var u = null,
                            A = null,
                            y = null,
                            F = "",
                            H = null;
                        if ("undefined" == typeof c[q].label && "undefined" == typeof c[q]["class"] && "undefined" == typeof c[q].callback) {
                            var u = 0,
                                A = null,
                                I;
                            for (I in c[q]) if (((A = I), 1 < ++u)) break;
                            1 == u && "function" == typeof c[q][I] && ((c[q].label = A), (c[q].callback = c[q][I]));
                        }
                        "function" == typeof c[q].callback && (H = c[q].callback);
                        c[q]["class"] ? (y = c[q]["class"]) : q == c.length - 1 && 2 >= c.length && (y = "btn-primary");
                        !0 !== c[q].link && (y = "btn " + y);
                        u = c[q].label ? c[q].label : "Option " + (q + 1);
                        c[q].icon && (F = "<i class='" + c[q].icon + "'></i> ");
                        A = c[q].href ? c[q].href : "javascript:;";
                        k = "<a data-handler='" + q + "' class='" + y + "' href='" + A + "'>" + F + u + "</a>" + k;
                        n[q] = H;
                    }
                    q = ["<div class='bootbox modal' tabindex='-1' style='overflow:hidden;'>"];
                    e.header &&
                        ((y = ""),
                        ("undefined" == typeof e.headerCloseButton || e.headerCloseButton) && (y = "<a href='javascript:;' class='close'>&times;</a>"),
                        q.push("<div class='modal-header'>" + y + "<h3>" + e.header + "</h3></div>"));
                    q.push("<div class='modal-body'></div>");
                    k && q.push("<div class='modal-footer'>" + k + "</div>");
                    q.push("</div>");
                    var w = g(q.join("\n"));
                    ("undefined" == typeof e.animate ? r : e.animate) && w.addClass("fade");
                    k = "undefined" == typeof e.classes ? d : e.classes;
                    return (
                        k && w.addClass(k),
                        w.find(".modal-body").html(b),
                        w.on("keyup.dismiss.modal", function (a) {
                            27 === a.which && e.onEscape && h("escape");
                        }),
                        w.on("click", "a.close", function (a) {
                            a.preventDefault();
                            h("close");
                        }),
                        w.on("shown", function () {
                            w.find("a.btn-primary:first").focus();
                        }),
                        w.on("hidden", function (a) {
                            a.target === this && w.remove();
                        }),
                        w.on("click", ".modal-footer a", function (a) {
                            var b = g(this).data("handler"),
                                d = n[b],
                                e = null;
                            ("undefined" != typeof b && "undefined" != typeof c[b].href) || (a.preventDefault(), "function" == typeof d && (e = d(a)), !1 === e || w.modal("hide"));
                        }),
                        g("body").append(w),
                        w.modal({ backdrop: "undefined" == typeof e.backdrop ? a : e.backdrop, keyboard: !1, show: !1 }),
                        w.on("show", function () {
                            g(f).off("focusin.modal");
                        }),
                        ("undefined" == typeof e.show || !0 === e.show) && w.modal("show"),
                        w
                    );
                },
                modal: function () {
                    var b,
                        c,
                        d,
                        e = { onEscape: null, keyboard: !0, backdrop: a };
                    switch (arguments.length) {
                        case 1:
                            b = arguments[0];
                            break;
                        case 2:
                            b = arguments[0];
                            "object" == typeof arguments[1] ? (d = arguments[1]) : (c = arguments[1]);
                            break;
                        case 3:
                            b = arguments[0];
                            c = arguments[1];
                            d = arguments[2];
                            break;
                        default:
                            throw Error("Incorrect number of arguments: expected 1-3");
                    }
                    return (e.header = c), (d = "object" == typeof d ? g.extend(e, d) : e), n.dialog(b, [], d);
                },
                hideAll: function () {
                    g(".bootbox").modal("hide");
                },
                animate: function (a) {
                    r = a;
                },
                backdrop: function (b) {
                    a = b;
                },
                classes: function (a) {
                    d = a;
                },
            },
            k = {
                br: { OK: "OK", CANCEL: "Cancelar", CONFIRM: "Sim" },
                da: { OK: "OK", CANCEL: "Annuller", CONFIRM: "Accepter" },
                de: { OK: "OK", CANCEL: "Abbrechen", CONFIRM: "Akzeptieren" },
                en: { OK: "OK", CANCEL: "Cancel", CONFIRM: "OK" },
                es: { OK: "OK", CANCEL: "Cancelar", CONFIRM: "Aceptar" },
                fr: { OK: "OK", CANCEL: "Annuler", CONFIRM: "D'accord" },
                it: { OK: "OK", CANCEL: "Annulla", CONFIRM: "Conferma" },
                nl: { OK: "OK", CANCEL: "Annuleren", CONFIRM: "Accepteren" },
                pl: { OK: "OK", CANCEL: "Anuluj", CONFIRM: "Potwierd\u017a" },
                ru: { OK: "OK", CANCEL: "\u041e\u0442\u043c\u0435\u043d\u0430", CONFIRM: "\u041f\u0440\u0438\u043c\u0435\u043d\u0438\u0442\u044c" },
                zh_CN: { OK: "OK", CANCEL: "\u53d6\u6d88", CONFIRM: "\u786e\u8ba4" },
                zh_TW: { OK: "OK", CANCEL: "\u53d6\u6d88", CONFIRM: "\u78ba\u8a8d" },
            };
        return n;
    })(document, window.jQuery);
window.bootbox = bootbox;
