if (!Ext.exporter) {
    Ext.exporter = {}
}
if (!Ext.exporter.data) {
    Ext.exporter.data = {}
}
if (!Ext.exporter.excel) {
    Ext.exporter.excel = {}
}
if (!Ext.exporter.file) {
    Ext.exporter.file = {}
}
if (!Ext.exporter.file.excel) {
    Ext.exporter.file.excel = {}
}
if (!Ext.exporter.file.html) {
    Ext.exporter.file.html = {}
}
if (!Ext.exporter.file.ooxml) {
    Ext.exporter.file.ooxml = {}
}
if (!Ext.exporter.file.ooxml.excel) {
    Ext.exporter.file.ooxml.excel = {}
}
if (!Ext.exporter.file.zip) {
    Ext.exporter.file.zip = {}
}
if (!Ext.exporter.text) {
    Ext.exporter.text = {}
}

(function (M) {
    var P, F = ["constructor", "toString", "valueOf", "toLocaleString"], L = {}, B = {}, N = 0, E, H, z, J, Q, K, D, O,
        I, A = function () {
            var a, b;
            H = Ext.Base;
            z = Ext.ClassManager;
            for (a = F.length; a-- > 0;) {
                b = (1 << a);
                B[L[b] = F[a]] = b
            }
            for (a in B) {
                N |= B[a]
            }
            N = ~N;
            Function.prototype.$isFunction = 1;
            I = !!(z && z.addAlias);
            J = Ext.Class.getPreprocessor("config").fn;
            Q = Ext.Class.getPreprocessor("cachedConfig") && Ext.Class.getPreprocessor("cachedConfig").fn;
            K = Ext.Class.getPreprocessor("platformConfig") && Ext.Class.getPreprocessor("platformConfig").fn;
            O = Ext.Class.getPreprocessor("privates") && Ext.Class.getPreprocessor("privates").fn;
            D = Ext.ClassManager.postprocessors.deprecated && Ext.ClassManager.postprocessors.deprecated.fn;
            P = H.$staticMembers;
            if (!P) {
                P = [];
                for (E in H) {
                    if (H.hasOwnProperty(E)) {
                        P.push(E)
                    }
                }
            }
            M.derive = G;
            return G.apply(this, arguments)
        }, y = function (h, a, j) {
            var d = j.enumerableMembers, m = h.prototype, b, l, c, e, g;
            if (!a) {
                return
            }
            if (I) {
                h.addMembers(a)
            } else {
                for (b in a) {
                    e = a[b];
                    if (e && e.$isFunction && !e.$isClass && e !== Ext.emptyFn && e !== Ext.identityFn) {
                        g = m.hasOwnProperty(b) && m[b];
                        if (g) {
                            e.$previous = g
                        }
                        m[b] = l = e;
                        l.$owner = h;
                        l.$name = b
                    } else {
                        m[b] = e
                    }
                }
                for (c = 1; d; c <<= 1) {
                    if (d & c) {
                        d &= ~c;
                        b = L[c];
                        m[b] = l = a[b];
                        l.$owner = h;
                        l.$name = b
                    }
                }
            }
        }, C = function (c) {
            var e = function d() {
                return c.apply(this, arguments) || null
            }, a, b;
            e.prototype = Ext.Object.chain(c.prototype);
            for (a = P.length; a-- > 0;) {
                b = P[a];
                e[b] = H[b]
            }
            return e
        }, G = function (ak, ah, ar, x, ai, n, aj, b, am, l, t) {
            var u = function w() {
                    return this.constructor.apply(this, arguments) || null
                }, at = u, an = {enumerableMembers: x & N, onCreated: t, onBeforeCreated: y, aliases: b},
                q = ar.alternateClassName || [], d = Ext.global, j, e, c, r, g, ao, ap, al, h, af, a, m, s, aq,
                p = z.alternateToName || z.maps.alternateToName, ag = z.nameToAlternates || z.maps.nameToAlternates;
            for (c = P.length; c-- > 0;) {
                ap = P[c];
                u[ap] = H[ap]
            }
            if (ar.$isFunction) {
                ar = ar(u)
            }
            an.data = ar;
            af = ar.statics;
            delete ar.statics;
            ar.$className = ak;
            if ("$className" in ar) {
                u.$className = ar.$className
            }
            u.extend(ah);
            h = u.prototype;
            if (ai) {
                u.xtype = ar.xtype = ai[0];
                h.xtypes = ai
            }
            h.xtypesChain = n;
            h.xtypesMap = aj;
            ar.alias = b;
            at.triggerExtended(u, ar, an);
            if (ar.onClassExtended) {
                u.onExtended(ar.onClassExtended, u);
                delete ar.onClassExtended
            }
            if (ar.privates && O) {
                O.call(Ext.Class, u, ar)
            }
            if (af) {
                if (I) {
                    u.addStatics(af)
                } else {
                    for (a in af) {
                        if (af.hasOwnProperty(a)) {
                            aq = af[a];
                            if (aq && aq.$isFunction && !aq.$isClass && aq !== Ext.emptyFn && aq !== Ext.identityFn) {
                                u[a] = s = aq;
                                s.$owner = u;
                                s.$name = a
                            }
                            u[a] = aq
                        }
                    }
                }
            }
            if (ar.inheritableStatics) {
                u.addInheritableStatics(ar.inheritableStatics);
                delete ar.inheritableStatics
            }
            if (h.onClassExtended) {
                at.onExtended(h.onClassExtended, at);
                delete h.onClassExtended
            }
            if (ar.platformConfig && K) {
                K.call(Ext.Class, u, ar);
                delete ar.platformConfig
            }
            if (ar.config) {
                J.call(Ext.Class, u, ar)
            }
            if (ar.cachedConfig && Q) {
                Q.call(Ext.Class, u, ar);
                delete ar.cachedConfig
            }
            if (ar.deprecated && D) {
                D.call(Ext.ClassManager, ak, u, ar)
            }
            an.onBeforeCreated(u, an.data, an);
            for (c = 0, g = am && am.length; c < g; ++c) {
                u.mixin.apply(u, am[c])
            }
            for (c = 0, g = b.length; c < g; c++) {
                j = b[c];
                z.setAlias ? z.setAlias(u, j) : z.addAlias(u, j)
            }
            if (ar.singleton) {
                at = new u()
            }
            if (!(q instanceof Array)) {
                q = [q]
            }
            m = z.getName(at);
            for (c = 0, r = q.length; c < r; c++) {
                e = q[c];
                z.classes[e] = at;
                if (I) {
                    z.addAlternate(u, e)
                } else {
                    if (m) {
                        p[e] = m;
                        q = ag[m] || (ag[m] = []);
                        q.push(e)
                    }
                }
            }
            for (c = 0, g = l.length; c < g; c += 2) {
                ao = l[c];
                if (!ao) {
                    ao = d
                }
                ao[l[c + 1]] = at
            }
            z.classes[ak] = at;
            if (!I) {
                if (m && m !== ak) {
                    p[ak] = m;
                    q = ag[m] || (ag[m] = []);
                    q.push(ak)
                }
            }
            delete h.alternateClassName;
            if (an.onCreated) {
                an.onCreated.call(at, at)
            }
            if (ak) {
                z.triggerCreated(ak)
            }
            return at
        };
    M.derive = A
}(Ext.cmd = {}));

Ext.define("Ext.overrides.exporter.util.Format", {
    override: "Ext.util.Format", decToHex: function (h, g) {
        var j = "", e;
        for (e = 0; e < g; e++) {
            j += String.fromCharCode(h & 255);
            h = h >>> 8
        }
        return j
    }
});

(Ext.cmd.derive("Ext.exporter.data.Base", Ext.Base, {
    config: {idPrefix: "id", id: null},
    internalCols: null,
    constructor: function (d) {
        var c = this;
        c.internalCols = [];
        c.initConfig(d);
        if (!c.getId()) {
            c.setId("")
        }
        return c.callParent([d])
    },
    destroy: function () {
        var h = this.internalCols, g = h.length, j, e;
        for (j = 0; j < g; j++) {
            e = h[j];
            Ext.destroy(e.items, e)
        }
        h.length = 0;
        this.internalCols = null;
        this.callParent()
    },
    applyId: function (d, c) {
        if (Ext.isEmpty(c)) {
            c = Ext.id(null, this.getIdPrefix())
        }
        if (!Ext.isEmpty(d)) {
            c = d
        }
        return c
    },
    checkCollection: function (g, e, d) {
        if (g && !e) {
            e = this.constructCollection(d)
        }
        if (g) {
            e.add(g)
        }
        return e
    },
    constructCollection: function (c) {
        var d = new Ext.util.Collection({decoder: this.getCollectionDecoder(c), keyFn: this.getCollectionItemKey});
        this.internalCols.push(d);
        return d
    },
    getCollectionDecoder: function (b) {
        return function (a) {
            return (a && a.isInstance) ? a : Ext.create(b, a || {})
        }
    },
    getCollectionItemKey: function (b) {
        return b.getKey ? b.getKey() : b.getId()
    }
}, 1, 0, 0, 0, 0, 0, [Ext.exporter.data, "Base"], 0));
(Ext.cmd.derive("Ext.exporter.data.Column", Ext.exporter.data.Base, {
    config: {
        table: null,
        text: null,
        style: null,
        width: null,
        mergeAcross: null,
        mergeDown: null,
        level: 0,
        index: null,
        columns: null
    }, destroy: function () {
        this.setTable(null);
        this.setColumns(null);
        Ext.exporter.data.Base.prototype.destroy.call(this)
    }, updateTable: function (j) {
        var h = this.getColumns(), g, e;
        if (h) {
            e = h.length;
            for (g = 0; g < e; g++) {
                h.getAt(g).setTable(j)
            }
        }
    }, applyColumns: function (c, d) {
        return this.checkCollection(c, d, "Ext.exporter.data.Column")
    }, updateColumns: function (d, g) {
        var e = this;
        if (g) {
            g.un({add: e.onColumnAdd, remove: e.onColumnRemove, scope: e});
            Ext.destroy(g.items, g)
        }
        if (d) {
            d.on({add: e.onColumnAdd, remove: e.onColumnRemove, scope: e});
            e.onColumnAdd(d, {items: d.getRange()})
        }
    }, sync: function (n) {
        var q = this, r = q.getColumnCount() - 1, p = q.getColumns(), l, j, m;
        if (p) {
            j = p.length;
            for (l = 0; l < j; l++) {
                p.getAt(l).sync(n)
            }
            q.setMergeDown(null)
        } else {
            m = n - this.getLevel();
            q.setMergeDown(m > 0 ? m : null)
        }
        q.setMergeAcross(r > 0 ? r : null)
    }, onColumnAdd: function (p, t) {
        var m = t.items, q = m.length, n = this.getLevel(), r = this.getTable(), l, s;
        for (l = 0; l < q; l++) {
            s = m[l];
            s.setLevel(n + 1);
            s.setTable(r)
        }
        if (r) {
            r.syncColumns()
        }
    }, onColumnRemove: function (g, e) {
        var d = this.getTable();
        Ext.destroy(e.items);
        if (d) {
            d.syncColumns()
        }
    }, getColumnCount: function (e) {
        var j = 0, h;
        if (!e) {
            e = this.getColumns();
            if (!e) {
                return 1
            }
        }
        for (var g = 0; g < e.length; g++) {
            h = e.getAt(g).getColumns();
            if (!h) {
                j += 1
            } else {
                j += this.getColumnCount(h)
            }
        }
        return j
    }, addColumn: function (b) {
        if (!this.getColumns()) {
            this.setColumns([])
        }
        return this.getColumns().add(b || {})
    }, getColumn: function (b) {
        return this.getColumns().get(b)
    }
}, 0, 0, 0, 0, 0, 0, [Ext.exporter.data, "Column"], 0));
(Ext.cmd.derive("Ext.exporter.data.Cell", Ext.exporter.data.Base, {config: {value: null}}, 0, 0, 0, 0, 0, 0, [Ext.exporter.data, "Cell"], 0));
(Ext.cmd.derive("Ext.exporter.data.Row", Ext.exporter.data.Base, {
    config: {cells: null}, destroy: function () {
        this.setCells(null);
        Ext.exporter.data.Base.prototype.destroy.call(this)
    }, applyCells: function (c, d) {
        return this.checkCollection(c, d, "Ext.exporter.data.Cell")
    }, addCell: function (b) {
        if (!this.getCells()) {
            this.setCells([])
        }
        return this.getCells().add(b || {})
    }, getCell: function (b) {
        if (!this.getCells()) {
            return null
        }
        return this.getCells().get(b)
    }
}, 0, 0, 0, 0, 0, 0, [Ext.exporter.data, "Row"], 0));
(Ext.cmd.derive("Ext.exporter.data.Group", Ext.exporter.data.Base, {
    config: {
        text: null,
        rows: null,
        summaries: null,
        summary: null,
        groups: null
    }, destroy: function () {
        var b = this;
        b.setRows(null);
        b.setSummaries(null);
        b.setGroups(null);
        Ext.exporter.data.Base.prototype.destroy.call(this)
    }, applyRows: function (c, d) {
        return this.checkCollection(c, d, "Ext.exporter.data.Row")
    }, addRow: function (b) {
        if (!this.getRows()) {
            this.setRows([])
        }
        return this.getRows().add(b || {})
    }, getRow: function (b) {
        if (!this.getRows()) {
            return null
        }
        return this.getRows().get(b)
    }, applyGroups: function (c, d) {
        return this.checkCollection(c, d, "Ext.exporter.data.Group")
    }, addGroup: function (b) {
        if (!this.getGroups()) {
            this.setGroups([])
        }
        return this.getGroups().add(b || {})
    }, getGroup: function (c) {
        var d = this.getGroups();
        if (!d) {
            return null
        }
        return d.get(c)
    }, applySummaries: function (c, d) {
        return this.checkCollection(c, d, "Ext.exporter.data.Row")
    }, applySummary: function (b) {
        if (b) {
            this.addSummary(b)
        }
        return null
    }, addSummary: function (b) {
        if (!this.getSummaries()) {
            this.setSummaries([])
        }
        return this.getSummaries().add(b || {})
    }, getSummary: function (c) {
        var d = this.getSummaries();
        return d ? d.get(c) : null
    }
}, 0, 0, 0, 0, 0, 0, [Ext.exporter.data, "Group"], 0));
(Ext.cmd.derive("Ext.exporter.data.Table", Ext.exporter.data.Base, {
    isDataTable: true,
    config: {columns: null, groups: null},
    destroy: function () {
        this.setColumns(null);
        this.setGroups(null);
        Ext.exporter.data.Base.prototype.destroy.call(this)
    },
    applyColumns: function (c, d) {
        return this.checkCollection(c, d, "Ext.exporter.data.Column")
    },
    updateColumns: function (d, g) {
        var e = this;
        if (g) {
            g.un({add: e.onColumnAdd, remove: e.onColumnRemove, scope: e});
            Ext.destroy(g.items, g)
        }
        if (d) {
            d.on({add: e.onColumnAdd, remove: e.onColumnRemove, scope: e});
            e.onColumnAdd(d, {items: d.getRange()});
            e.syncColumns()
        }
    },
    syncColumns: function () {
        var r = this.getColumns(), x = this.getColDepth(r, -1), j = {}, w, y, A, s, q, u, z, t;
        if (!r) {
            return
        }
        A = r.length;
        for (w = 0; w < A; w++) {
            r.getAt(w).sync(x)
        }
        this.getColumnLevels(r, x, j);
        q = Ext.Object.getKeys(j);
        A = q.length;
        for (w = 0; w < A; w++) {
            u = j[q[w]];
            s = u.length;
            for (y = 0; y < s; y++) {
                if (y === 0) {
                    t = 1
                } else {
                    if (u[y - 1]) {
                        z = u[y - 1].getConfig();
                        t += (z.mergeAcross ? z.mergeAcross + 1 : 1)
                    } else {
                        t++
                    }
                }
                if (u[y]) {
                    u[y].setIndex(t)
                }
            }
        }
    },
    getLeveledColumns: function () {
        var d = this.getColumns(), g = this.getColDepth(d, -1), e = {};
        this.getColumnLevels(d, g, e, true);
        return e
    },
    getBottomColumns: function () {
        var d = this.getLeveledColumns(), g, e;
        g = Ext.Object.getKeys(d);
        e = g.length;
        return e ? d[g[g.length - 1]] : []
    },
    getColumnLevels: function (x, u, j, s) {
        var y, t, w, r, z, A, q;
        if (!x) {
            return
        }
        r = x.length;
        for (t = 0; t < r; t++) {
            y = x.getAt(t);
            A = y.getLevel();
            q = y.getColumns();
            z = "s" + A;
            j[z] = j[z] || [];
            j[z].push(y);
            if (!q) {
                for (w = A + 1; w <= u; w++) {
                    z = "s" + w;
                    j[z] = j[z] || [];
                    j[z].push(s ? y : null)
                }
            } else {
                this.getColumnLevels(q, u, j, s)
            }
        }
    },
    onColumnAdd: function (l, p) {
        var j = p.items, m = j.length, h, n;
        for (h = 0; h < m; h++) {
            n = j[h];
            n.setTable(this);
            n.setLevel(0)
        }
        this.syncColumns()
    },
    onColumnRemove: function (c, d) {
        Ext.destroy(d.items);
        this.syncColumns()
    },
    getColumnCount: function () {
        var h = this.getColumns(), e = 0, g, j;
        if (h) {
            j = h.length;
            for (g = 0; g < j; g++) {
                e += h.getAt(g).getColumnCount()
            }
        }
        return e
    },
    getColDepth: function (j, h) {
        var g = 0;
        if (!j) {
            return h
        }
        for (var e = 0; e < j.length; e++) {
            g = Math.max(g, this.getColDepth(j.getAt(e).getColumns(), h + 1))
        }
        return g
    },
    addColumn: function (b) {
        if (!this.getColumns()) {
            this.setColumns([])
        }
        return this.getColumns().add(b || {})
    },
    getColumn: function (c) {
        var d = this.getColumns();
        if (!d) {
            return null
        }
        return d.get(c)
    },
    applyGroups: function (c, d) {
        return this.checkCollection(c, d, "Ext.exporter.data.Group")
    },
    addGroup: function (b) {
        if (!this.getGroups()) {
            this.setGroups([])
        }
        return this.getGroups().add(b || {})
    },
    getGroup: function (c) {
        var d = this.getGroups();
        if (!d) {
            return null
        }
        return d.get(c)
    }
}, 0, 0, 0, 0, 0, 0, [Ext.exporter.data, "Table"], 0));
(Ext.cmd.derive("Ext.exporter.file.Base", Ext.exporter.data.Base, {
    tpl: null, destroy: function () {
        this.tpl = null;
        Ext.exporter.data.Base.prototype.destroy.call(this)
    }, render: function () {
        return this.tpl ? Ext.XTemplate.getTpl(this, "tpl").apply(this.getRenderData()) : ""
    }, getRenderData: function () {
        return this.getConfig()
    }
}, 0, 0, 0, 0, 0, 0, [Ext.exporter.file, "Base"], 0));
(Ext.cmd.derive("Ext.exporter.file.Style", Ext.exporter.file.Base, {
    config: {
        name: null,
        alignment: null,
        font: null,
        interior: null,
        format: null,
        borders: null,
        checks: {
            alignment: {
                horizontal: ["Automatic", "Left", "Center", "Right", "Justify"],
                readingOrder: ["LeftToRight", "RightToLeft", "Context"],
                vertical: ["Automatic", "Top", "Bottom", "Center"]
            },
            font: {
                bold: [true, false],
                italic: [true, false],
                strikeThrough: [true, false],
                underline: ["None", "Single"]
            },
            border: {position: ["Left", "Top", "Right", "Bottom"], lineStyle: ["None", "Continuous", "Dash", "Dot"]},
            interior: {pattern: ["None", "Solid"]}
        }
    },
    datePatterns: {
        "General Date": "Y-m-d H:i:s",
        "Long Date": "l, F d, Y",
        "Medium Date": "Y-m-d",
        "Short Date": "n/j/Y",
        "Long Time": "g:i:s A",
        "Medium Time": "H:i:s",
        "Short Time": "g:i A"
    },
    numberPatterns: {"General Number": "0", Fixed: "0.00", Standard: "0.00"},
    booleanPatterns: {"Yes/No": ["Yes", "No"], "True/False": ["True", "False"], "On/Off": ["On", "Off"]},
    constructor: function (b) {
        Ext.exporter.file.Base.prototype.constructor.call(this, this.uncapitalizeKeys(b))
    },
    uncapitalizeKeys: function (q) {
        var r = q, m, l, n, p, j;
        if (Ext.isObject(q)) {
            r = {};
            m = Ext.Object.getAllKeys(q);
            l = m.length;
            for (n = 0; n < l; n++) {
                p = m[n];
                r[Ext.String.uncapitalize(p)] = this.uncapitalizeKeys(q[p])
            }
        } else {
            if (Ext.isArray(q)) {
                r = [];
                l = q.length;
                for (n = 0; n < l; n++) {
                    r.push(this.uncapitalizeKeys(q[n]))
                }
            }
        }
        return r
    },
    destroy: function () {
        var b = this;
        b.setAlignment(null);
        b.setFont(null);
        b.setInterior(null);
        b.setBorders(null);
        b.setChecks(null);
        Ext.exporter.file.Base.prototype.destroy.call(this)
    },
    updateAlignment: function (b) {
        this.checkAttribute(b, "alignment")
    },
    updateFont: function (b) {
        this.checkAttribute(b, "font")
    },
    updateInterior: function (b) {
        this.checkAttribute(b, "interior")
    },
    applyBorders: function (c, d) {
        if (!c) {
            return c
        }
        c = Ext.Array.from(c);
        return c
    },
    updateBorders: function (b) {
        this.checkAttribute(b, "border")
    },
    checkAttribute: function (A, x) {
        var D = this.getChecks(), s, j, w, z, C, y, t, B, u, E;
        if (!A || !D || !D[x]) {
            return
        }
        s = Ext.Array.from(A);
        u = s.length;
        for (z = 0; z < u; z++) {
            B = s[z];
            j = Ext.Object.getKeys(B || {});
            w = j.length;
            for (C = 0; C < w; C++) {
                t = j[C];
                if (y = D[x][t] && B[t]) {
                    E = (Ext.isArray(y) ? Ext.Array.indexOf(y, B[t]) : y === B[t]);
                    if (!E) {
                        delete (B[t])
                    }
                }
            }
        }
    },
    getFormattedValue: function (g) {
        var l = this, j = l.getFormat(), m = g, h = Ext.util.Format;
        if (!j || j === "General" || Ext.isEmpty(g)) {
            return m
        }
        if (j === "Currency") {
            return h.currency(g)
        } else {
            if (j === "Euro Currency") {
                return h.currency(g, "€")
            } else {
                if (j === "Percent") {
                    return h.number(g * 100, "0.00") + "%"
                } else {
                    if (j === "Scientific") {
                        return Number(g).toExponential()
                    } else {
                        if (l.datePatterns[j]) {
                            return h.date(g, l.datePatterns[j])
                        } else {
                            if (l.numberPatterns[j]) {
                                return h.number(g, l.numberPatterns[j])
                            } else {
                                if (l.booleanPatterns[j]) {
                                    return g ? l.booleanPatterns[j][0] : l.booleanPatterns[j][1]
                                } else {
                                    if (Ext.isFunction(j)) {
                                        return j(g)
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }
        return h.number(g, j)
    }
}, 1, 0, 0, 0, 0, 0, [Ext.exporter.file, "Style"], 0));
(Ext.cmd.derive("Ext.exporter.File", Ext.Base, {
    singleton: true,
    textPopupWait: "You may close this window after the file is downloaded!",
    textPopupBlocker: "The file was not saved because pop-up blocker might be enabled! Please check your browser settings.",
    url: "https://exporter.sencha.com",
    forceDownload: false,
    requiresPopup: function () {
        var b = Ext.platformTags;
        return this.forceDownload || Ext.isSafari || b.phone || b.tablet
    },
    initializePopup: function (h) {
        var g = this, j = g.requiresPopup(), e;
        if (!j && h) {
            j = !g.saveBlobAs
        }
        g.popup = null;
        if (j) {
            e = window.open("", "_blank");
            if (e) {
                g.popup = e;
                e.document.write(Ext.dom.Helper.markup({
                    tag: "html",
                    children: [{tag: "head"}, {tag: "body", children: [{tag: "p", html: g.textPopupWait}]}]
                }))
            }
        }
    },
    saveBinaryAs: function (n, h, l, m) {
        var p = this, j = p.downloadBinaryAs;
        if (!p.requiresPopup() && p.saveBlobAs) {
            j = p.saveBlobAs
        }
        return j.call(p, n, h, l, m)
    },
    downloadBinaryAs: function (q, r, m, n) {
        var j = new Ext.Deferred(), l, p;
        l = Ext.dom.Helper.markup({
            tag: "html",
            children: [{tag: "head"}, {
                tag: "body",
                children: [{
                    tag: "form",
                    method: "POST",
                    action: this.url,
                    children: [{
                        tag: "input",
                        type: "hidden",
                        name: "content",
                        value: Ext.util.Base64.encode(q)
                    }, {tag: "input", type: "hidden", name: "filename", value: r}, {
                        tag: "input",
                        type: "hidden",
                        name: "charset",
                        value: m || "UTF-8"
                    }, {tag: "input", type: "hidden", name: "mime", value: n || "application/octet-stream"}]
                }, {
                    tag: "script",
                    type: "text/javascript",
                    children: 'document.getElementsByTagName("form")[0].submit();'
                }]
            }]
        });
        p = this.popup || window.open("", "_blank");
        if (p) {
            p.document.write(l);
            j.resolve()
        } else {
            j.reject(this.textPopupBlocker)
        }
        this.popup = null;
        return j.promise
    }
}, 0, 0, 0, 0, 0, 0, [Ext.exporter, "File"], function (j) {
    /*! @source http://purl.eligrey.com/github/FileSaver.js/blob/master/FileSaver.js */
    var e = window.navigator, g = window.saveAs || (function (J) {
        if (typeof e !== "undefined" && /MSIE [1-9]\./.test(e.userAgent)) {
            return
        }
        var G = J.document, C = function () {
                return J.URL || J.webkitURL || J
            }, c = G.createElementNS("http://www.w3.org/1999/xhtml", "a"), L = "download" in c, E = function (l) {
                var m = new MouseEvent("click");
                l.dispatchEvent(m)
            }, P = /Version\/[\d\.]+.*Safari/.test(e.userAgent), b = J.webkitRequestFileSystem,
            a = J.requestFileSystem || b || J.mozRequestFileSystem, d = function (l) {
                (J.setImmediate || J.setTimeout)(function () {
                    throw l
                }, 0)
            }, O = "application/octet-stream", D = 0, H = 1000 * 40, F = function (l) {
                var m = function () {
                    if (typeof l === "string") {
                        C().revokeObjectURL(l)
                    } else {
                        l.remove()
                    }
                };
                setTimeout(m, H)
            }, I = function (q, r, m) {
                r = [].concat(r);
                var n = r.length;
                while (n--) {
                    var l = q["on" + r[n]];
                    if (typeof l === "function") {
                        try {
                            l.call(q, m || q)
                        } catch (p) {
                            d(p)
                        }
                    }
                }
            }, K = function (l) {
                if (/^\s*(?:text\/\S*|application\/xml|\S*\/\S*\+xml)\s*;.*charset\s*=\s*utf-8/i.test(l.type)) {
                    return new Blob(["\ufeff", l], {type: l.type})
                }
                return l
            }, M = function (m, l, z) {
                if (!z) {
                    m = K(m)
                }
                var y = this, r = m.type, n = false, w, x, s = function () {
                    I(y, "writestart progress write writeend".split(" "))
                }, p = function () {
                    if (x && P && typeof FileReader !== "undefined") {
                        var B = new FileReader();
                        B.onloadend = function () {
                            var S = B.result;
                            x.location.href = "data:attachment/file" + S.slice(S.search(/[,;]/));
                            y.readyState = y.DONE;
                            s()
                        };
                        B.readAsDataURL(m);
                        y.readyState = y.INIT;
                        return
                    }
                    if (n || !w) {
                        w = C().createObjectURL(m)
                    }
                    if (x) {
                        x.location.href = w
                    } else {
                        var A = J.open(w, "_blank");
                        if (A === undefined && P) {
                            J.location.href = w
                        }
                    }
                    y.readyState = y.DONE;
                    s();
                    F(w)
                }, t = function (A) {
                    return function () {
                        if (y.readyState !== y.DONE) {
                            return A.apply(this, arguments)
                        }
                    }
                }, u = {create: true, exclusive: false}, q;
                y.readyState = y.INIT;
                if (!l) {
                    l = "download"
                }
                if (L) {
                    w = C().createObjectURL(m);
                    setTimeout(function () {
                        c.href = w;
                        c.download = l;
                        E(c);
                        s();
                        F(w);
                        y.readyState = y.DONE
                    });
                    return
                }
                if (J.chrome && r && r !== O) {
                    q = m.slice || m.webkitSlice;
                    m = q.call(m, 0, m.size, O);
                    n = true
                }
                if (b && l !== "download") {
                    l += ".download"
                }
                if (r === O || b) {
                    x = J
                }
                if (!a) {
                    p();
                    return
                }
                D += m.size;
                a(J.TEMPORARY, D, t(function (A) {
                    A.root.getDirectory("saved", u, t(function (S) {
                        var B = function () {
                            S.getFile(l, u, t(function (R) {
                                R.createWriter(t(function (U) {
                                    U.onwriteend = function (T) {
                                        x.location.href = R.toURL();
                                        y.readyState = y.DONE;
                                        I(y, "writeend", T);
                                        F(R)
                                    };
                                    U.onerror = function () {
                                        var T = U.error;
                                        if (T.code !== T.ABORT_ERR) {
                                            p()
                                        }
                                    };
                                    "writestart progress write abort".split(" ").forEach(function (T) {
                                        U["on" + T] = y["on" + T]
                                    });
                                    U.write(m);
                                    y.abort = function () {
                                        U.abort();
                                        y.readyState = y.DONE
                                    };
                                    y.readyState = y.WRITING
                                }), p)
                            }), p)
                        };
                        S.getFile(l, {create: false}, t(function (R) {
                            R.remove();
                            B()
                        }), t(function (R) {
                            if (R.code === R.NOT_FOUND_ERR) {
                                B()
                            } else {
                                p()
                            }
                        }))
                    }), p)
                }), p)
            }, N = M.prototype, Q = function (m, l, n) {
                return new M(m, l, n)
            };
        if (typeof e !== "undefined" && e.msSaveOrOpenBlob) {
            return function (m, l, n) {
                if (!n) {
                    m = K(m)
                }
                return e.msSaveOrOpenBlob(m, l || "download")
            }
        }
        N.abort = function () {
            var l = this;
            l.readyState = l.DONE;
            I(l, "abort")
        };
        N.readyState = N.INIT = 0;
        N.WRITING = 1;
        N.DONE = 2;
        N.error = N.onwritestart = N.onprogress = N.onwrite = N.onabort = N.onerror = N.onwriteend = null;
        return Q
    }(typeof self !== "undefined" && self || typeof window !== "undefined" && window || this.content));
    if (typeof module !== "undefined" && module.exports) {
        module.exports.saveAs = g
    } else {
        if ((typeof define !== "undefined" && define !== null) && (define.amd !== null)) {
            define([], function () {
                return g
            })
        }
    }
    var h = window.saveTextAs || (function (p, r, a) {
        r = r || "download.txt";
        a = a || "utf-8";
        p = (p || "").replace(/\r?\n/g, "\r\n");
        if (g && Blob) {
            var q = new Blob([p], {type: "text/plain;charset=" + a});
            g(q, r);
            return true
        } else {
            var d = window.frames.saveTxtWindow;
            if (!d) {
                d = document.createElement("iframe");
                d.id = "saveTxtWindow";
                d.style.display = "none";
                document.body.insertBefore(d, null);
                d = window.frames.saveTxtWindow;
                if (!d) {
                    d = j.popup || window.open("", "_temp", "width=100,height=100");
                    if (!d) {
                        return false
                    }
                }
            }
            var c = d.document;
            c.open("text/html", "replace");
            c.charset = a;
            c.write(p);
            c.close();
            var b = c.execCommand("SaveAs", null, r);
            d.close();
            return b
        }
    });
    j.saveAs = function (c, d, a, b) {
        var m;
        if (this.requiresPopup()) {
            return this.downloadBinaryAs(c, d, a || "UTF-8", b || "text/plain")
        } else {
            m = new Ext.Deferred();
            if (h(c, d, a)) {
                m.resolve()
            } else {
                m.reject()
            }
            return m.promise
        }
    };
    if (g && Blob) {
        j.saveBlobAs = function (d, u, w, x) {
            var a = new Ext.Deferred();
            var c = new Uint8Array(d.length), b = c.length, s = {type: x || "application/octet-stream"}, y, t;
            for (t = 0; t < b; t++) {
                c[t] = d.charCodeAt(t)
            }
            y = new Blob([c], s);
            g(y, u);
            a.resolve();
            return a.promise
        }
    }
}));
(Ext.cmd.derive("Ext.exporter.Base", Ext.Base, {
    config: {
        data: null,
        showSummary: true,
        title: null,
        author: "Sencha",
        fileName: "export.txt",
        charset: "UTF-8",
        mimeType: "text/plain",
        binary: false
    }, constructor: function (b) {
        this.initConfig(b || {});
        Ext.exporter.File.initializePopup(this.getBinary());
        return this.callParent([b])
    }, destroy: function () {
        this.setData(Ext.destroy(this.getData()));
        this.callParent()
    }, getContent: Ext.identityFn, saveAs: function () {
        var c = this, d = new Ext.Deferred();
        Ext.asap(c.delayedSave, c, [d]);
        return d.promise
    }, delayedSave: function (g) {
        var j = this, e = j.getBinary() ? "saveBinaryAs" : "saveAs",
            h = Ext.exporter.File[e](j.getContent(), j.getFileName(), j.getCharset(), j.getMimeType());
        h.then(function () {
            g.resolve()
        }, function (a) {
            g.reject(a)
        })
    }, getColumnCount: function (d) {
        var g = 0;
        if (!d) {
            return g
        }
        for (var e = 0; e < d.length; e++) {
            if (!d[e].columns) {
                g += 1
            } else {
                g += this.getColumnCount(d[e].columns)
            }
        }
        return g
    }, applyData: function (b) {
        if (!b || b.isDataTable) {
            return b
        }
        return new Ext.exporter.data.Table(b)
    }
}, 1, 0, 0, 0, ["exporter.base"], [[Ext.mixin.Factoryable.prototype.mixinId || Ext.mixin.Factoryable.$className, Ext.mixin.Factoryable]], [Ext.exporter, "Base"], 0));
(Ext.cmd.derive("Ext.exporter.file.ooxml.Relationship", Ext.exporter.file.Base, {
    isRelationship: true,
    config: {idPrefix: "rId", schema: "", target: ""},
    tpl: ['<Relationship Id="{id}" Type="{schema}" Target="{target}"/>']
}, 0, 0, 0, 0, 0, 0, [Ext.exporter.file.ooxml, "Relationship"], 0));
(Ext.cmd.derive("Ext.exporter.file.ooxml.ContentType", Ext.exporter.file.Base, {
    isContentType: true,
    config: {tag: "Override", partName: "", contentType: "", extension: ""},
    tpl: ["<{tag}", '<tpl if="extension"> Extension="{extension}"</tpl>', '<tpl if="partName"> PartName="{partName}"</tpl>', '<tpl if="contentType"> ContentType="{contentType}"</tpl>', "/>"]
}, 0, 0, 0, 0, 0, 0, [Ext.exporter.file.ooxml, "ContentType"], 0));
(Ext.cmd.derive("Ext.exporter.file.ooxml.Base", Ext.exporter.file.Base, {
    config: {
        path: "",
        relationship: null,
        contentType: null
    }, destroy: function () {
        var b = this;
        b.setRelationship(Ext.destroy(b.getRelationship()));
        b.setContentType(Ext.destroy(b.getContentType()));
        Ext.exporter.file.Base.prototype.destroy.call(this)
    }, applyRelationship: function (b) {
        if (!b || b.isRelationship) {
            return b
        }
        return new Ext.exporter.file.ooxml.Relationship(b)
    }, applyContentType: function (b) {
        if (!b || b.isContentType) {
            return b
        }
        return new Ext.exporter.file.ooxml.ContentType(b)
    }, collectFiles: Ext.emptyFn
}, 0, 0, 0, 0, 0, 0, [Ext.exporter.file.ooxml, "Base"], 0));
(Ext.cmd.derive("Ext.exporter.file.zip.File", Ext.Base, {
    config: {path: "", data: null, dateTime: null, folder: false},
    constructor: function (d) {
        var c = this;
        c.initConfig(d);
        if (!c.getDateTime()) {
            c.setDateTime(new Date())
        }
        return c.callParent([d])
    },
    getId: function () {
        return this.getPath()
    },
    crc32: function (p, s) {
        var b = this.self.crcTable, n = 0, r = 0, q = 0, t;
        if (typeof p === "undefined" || !p.length) {
            return 0
        }
        t = (typeof p !== "string");
        if (typeof(s) == "undefined") {
            s = 0
        }
        s = s ^ (-1);
        for (var u = 0, w = p.length; u < w; u++) {
            q = t ? p[u] : p.charCodeAt(u);
            r = (s ^ q) & 255;
            n = b[r];
            s = (s >>> 8) ^ n
        }
        return s ^ (-1)
    },
    getHeader: function (C) {
        var B = this.getData(), t = this.getPath(), z = Ext.util.Base64._utf8_encode(t), F = z !== t,
            s = this.getDateTime(), w = "", G = "", D = Ext.util.Format.decToHex, A = "", x, y, E, u;
        x = s.getHours();
        x = x << 6;
        x = x | s.getMinutes();
        x = x << 5;
        x = x | s.getSeconds() / 2;
        y = s.getFullYear() - 1980;
        y = y << 4;
        y = y | (s.getMonth() + 1);
        y = y << 5;
        y = y | s.getDate();
        if (F) {
            G = D(1, 1) + D(this.crc32(z), 4) + z;
            w += "up" + D(G.length, 2) + G
        }
        A += "\n\x00";
        A += F ? "\x00\b" : "\x00\x00";
        A += "\x00\x00";
        A += D(x, 2);
        A += D(y, 2);
        A += D(B ? this.crc32(B) : 0, 4);
        A += D(B ? B.length : 0, 4);
        A += D(B ? B.length : 0, 4);
        A += D(z.length, 2);
        A += D(w.length, 2);
        E = "PK\x03\x04" + A + z + w;
        u = "PK\x01\x02\x14\x00" + A + "\x00\x00\x00\x00\x00\x00" + (this.getFolder() === true ? "\x10\x00\x00\x00" : "\x00\x00\x00\x00") + D(C, 4) + z + w;
        return {fileHeader: E, dirHeader: u, data: B || ""}
    }
}, 1, 0, 0, 0, 0, 0, [Ext.exporter.file.zip, "File"], function (h) {
    var j, m = [];
    for (var l = 0; l < 256; l++) {
        j = l;
        for (var c = 0; c < 8; c++) {
            j = ((j & 1) ? (3988292384 ^ (j >>> 1)) : (j >>> 1))
        }
        m[l] = j
    }
    h.crcTable = m
}));
(Ext.cmd.derive("Ext.exporter.file.zip.Folder", Ext.exporter.file.zip.File, {folder: true}, 0, 0, 0, 0, 0, 0, [Ext.exporter.file.zip, "Folder"], 0));
(Ext.cmd.derive("Ext.exporter.file.zip.Archive", Ext.exporter.file.Base, {
    config: {folders: [], files: []},
    destroy: function () {
        this.setFolders(null);
        this.setFiles(null);
        Ext.exporter.file.Base.prototype.destroy.call(this)
    },
    applyFolders: function (c, d) {
        return this.checkCollection(c, d, "Ext.exporter.file.zip.Folder")
    },
    applyFiles: function (c, d) {
        return this.checkCollection(c, d, "Ext.exporter.file.zip.File")
    },
    updateFiles: function (d, g) {
        var e = this;
        if (g) {
            g.un({add: e.onFileAdd, remove: e.onFileRemove, scope: e})
        }
        if (d) {
            d.on({add: e.onFileAdd, remove: e.onFileRemove, scope: e});
            e.onFileAdd(d, {items: d.getRange()})
        }
    },
    onFileAdd: function (n, s) {
        var t = this.getFolders(), m = s.items, p = m.length, l, q, r;
        for (l = 0; l < p; l++) {
            q = m[l];
            r = this.getParentFolder(q.getPath());
            if (r) {
                t.add({path: r})
            }
        }
    },
    onFileRemove: function (c, d) {
        Ext.destroy(d.items)
    },
    getParentFolder: function (c) {
        var d;
        if (c.slice(-1) == "/") {
            c = c.substring(0, c.length - 1)
        }
        d = c.lastIndexOf("/");
        return (d > 0) ? c.substring(0, d + 1) : ""
    },
    addFile: function (b) {
        return this.getFiles().add(b || {})
    },
    removeFile: function (b) {
        return this.getFiles().remove(b)
    },
    getContent: function () {
        var p = "", w = "", t = 0, z = 0, u = Ext.util.Format.decToHex,
            A = Ext.Array.merge(this.getFolders().getRange(), this.getFiles().getRange()), q = A.length, y, s, x, r;
        for (s = 0; s < q; s++) {
            x = A[s];
            r = x.getHeader(t);
            t += r.fileHeader.length + r.data.length;
            z += r.dirHeader.length;
            p += r.fileHeader + r.data;
            w += r.dirHeader
        }
        y = "PK\x05\x06\x00\x00\x00\x00" + u(q, 2) + u(q, 2) + u(z, 4) + u(t, 4) + "\x00\x00";
        p += w + y;
        return p
    }
}, 0, 0, 0, 0, 0, 0, [Ext.exporter.file.zip, "Archive"], 0));
(Ext.cmd.derive("Ext.exporter.file.ooxml.Relationships", Ext.exporter.file.ooxml.Base, {
    isRelationships: true,
    config: {relationships: []},
    contentType: {contentType: "application/vnd.openxmlformats-package.relationships+xml", partName: "/_rels/.rels"},
    tpl: ['<?xml version="1.0" encoding="UTF-8" standalone="yes"?>', '<Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships">', '<tpl if="relationships"><tpl for="relationships.getRange()">{[values.render()]}</tpl></tpl>', "</Relationships>"],
    destroy: function () {
        this.setRelationships(null);
        Ext.exporter.file.ooxml.Base.prototype.destroy.call(this)
    },
    collectFiles: function (b) {
        if (this.getRelationships().length) {
            b[this.getPath()] = this.render()
        }
    },
    applyRelationships: function (c, d) {
        return this.checkCollection(c, d, "Ext.exporter.file.ooxml.Relationship")
    },
    addRelationship: function (b) {
        return this.getRelationships().add(b || {})
    },
    removeRelationship: function (b) {
        return this.getRelationships().remove(b)
    }
}, 0, 0, 0, 0, 0, 0, [Ext.exporter.file.ooxml, "Relationships"], 0));
(Ext.cmd.derive("Ext.exporter.file.ooxml.excel.Sheet", Ext.exporter.file.ooxml.Base, {
    config: {
        index: 1,
        name: null,
        folder: "sheet",
        fileName: "sheet",
        relationships: {contentType: {contentType: "application/vnd.openxmlformats-package.relationships+xml"}},
        workbook: null
    }, contentType: {}, relationship: {}, destroy: function () {
        var b = this;
        b.setRelationships(Ext.destroy(b.getRelationships()));
        b.setWorkbook(null);
        Ext.exporter.file.ooxml.Base.prototype.destroy.call(this)
    }, collectFiles: function (l) {
        var m = this, j = m.getFolder() + "/", g = m.getFileName() + m.getIndex() + ".xml", h = m.getRelationships();
        m.getRelationship().setTarget(j + g);
        m.setPath("/xl/" + j + g);
        m.getContentType().setPartName("/xl/" + j + g);
        h.getContentType().setPartName("/xl/" + j + "_rels/" + g + ".rels");
        h.setPath("/xl/" + j + "_rels/" + g + ".rels");
        m.getRelationships().collectFiles(l);
        l[m.getPath()] = m.render()
    }, applyRelationships: function (b) {
        if (!b || b.isRelationships) {
            return b
        }
        return new Ext.exporter.file.ooxml.Relationships(b)
    }
}, 0, 0, 0, 0, 0, 0, [Ext.exporter.file.ooxml.excel, "Sheet"], 0));
(Ext.cmd.derive("Ext.exporter.file.ooxml.excel.Column", Ext.exporter.file.ooxml.Base, {
    config: {
        min: 1,
        max: 1,
        width: 10,
        autoFitWidth: false,
        hidden: false,
        styleId: null
    },
    tpl: ["<col ", 'min="{min}" ', 'max="{max}" ', 'width="{width}"', '<tpl if="styleId"> style="{styleId}"</tpl>', '<tpl if="hidden"> hidden="1"</tpl>', '<tpl if="autoFitWidth"> bestFit="1"</tpl>', '<tpl if="width != 10"> customWidth="1"</tpl>', "/>"]
}, 0, 0, 0, 0, 0, 0, [Ext.exporter.file.ooxml.excel, "Column"], 0));
(Ext.cmd.derive("Ext.exporter.file.ooxml.excel.Cell", Ext.exporter.file.Base, {
    isCell: true,
    config: {
        row: null,
        dataType: "s",
        showPhonetic: null,
        index: null,
        styleId: null,
        mergeAcross: null,
        mergeDown: null,
        value: ""
    },
    isMergedCell: false,
    tpl: ['<c r="{cellNotation}" t="{dataType}"', '<tpl if="showPhonetic"> ph="1"</tpl>', '<tpl if="styleId"> s="{styleId}"</tpl>', "><v>{value}</v></c>"],
    destroy: function () {
        this.setRow(null);
        Ext.exporter.file.Base.prototype.destroy.call(this)
    },
    getRenderData: function () {
        var b = this;
        return Ext.apply(Ext.exporter.file.Base.prototype.getRenderData.apply(this, arguments), {cellNotation: b.getNotation(b.getIndex()) + b.getRow().getIndex()})
    },
    applyValue: function (d) {
        var c;
        if (typeof d === "boolean") {
            c = "b"
        } else {
            if (typeof d === "number") {
                c = "n"
            } else {
                if (d instanceof Date) {
                    c = "d";
                    d = Ext.Date.format(d, "Y-m-d\\TH:i:s.u")
                } else {
                    if (d === "") {
                        c = "inlineStr";
                        d = Ext.util.Base64._utf8_encode(d)
                    } else {
                        c = "s";
                        d = Ext.util.Format.stripTags(d)
                    }
                }
            }
        }
        this.setDataType(c);
        return d
    },
    updateRow: function (b) {
        if (this.getDataType() === "s" && b) {
            this._value = b.getWorksheet().getWorkbook().getSharedStrings().addString(this.getValue())
        }
    },
    updateMergeAcross: function (b) {
        this.isMergedCell = (b || this.getMergeDown())
    },
    updateMergeDown: function (b) {
        this.isMergedCell = (b || this.getMergeAcross())
    },
    getMergedCellRef: function () {
        var n = this, m = n.getIndex(), l = n.getRow().getIndex(), j = n.getMergeAcross(), h = n.getMergeDown(),
            p = n.getNotation(m) + l + ":";
        if (j) {
            m += j
        }
        if (h) {
            l += h
        }
        p += n.getNotation(m) + l;
        return p
    },
    getNotation: function (j) {
        var n = 65, p = 26, m = String.fromCharCode, h, l;
        if (j <= 0) {
            j = 1
        }
        l = Math.floor(j / p);
        h = j % p;
        if (l === 0 || j === p) {
            return m(n + j - 1)
        } else {
            if (l < p) {
                return m(n + l - 1) + m(n + h - 1)
            } else {
                return this.getNotation(l) + m(n + h - 1)
            }
        }
    }
}, 0, 0, 0, 0, 0, 0, [Ext.exporter.file.ooxml.excel, "Cell"], 0));
(Ext.cmd.derive("Ext.exporter.file.ooxml.excel.Row", Ext.exporter.file.Base, {
    config: {
        collapsed: null,
        hidden: null,
        height: null,
        outlineLevel: null,
        showPhonetic: null,
        index: null,
        styleId: null,
        worksheet: null,
        cells: []
    },
    tpl: ["<row", '<tpl if="index"> r="{index}"</tpl>', '<tpl if="collapsed"> collapsed="{collapsed}"</tpl>', '<tpl if="hidden"> hidden="1"</tpl>', '<tpl if="height"> ht="{height}" customHeight="1"</tpl>', '<tpl if="outlineLevel"> outlineLevel="{outlineLevel}"</tpl>', '<tpl if="styleId"> s="{styleId}" customFormat="1"</tpl>', ">", '<tpl if="cells"><tpl for="cells.getRange()">{[values.render()]}</tpl></tpl>', "</row>"],
    destroy: function () {
        this.setWorksheet(null);
        this.setCells(null);
        Ext.exporter.file.Base.prototype.destroy.call(this)
    },
    applyCells: function (c, d) {
        return this.checkCollection(c, d, "Ext.exporter.file.ooxml.excel.Cell")
    },
    updateCells: function (d, g) {
        var e = this;
        if (g) {
            d.un({add: e.onCellAdd, remove: e.onCellRemove, scope: e})
        }
        if (d) {
            d.on({add: e.onCellAdd, remove: e.onCellRemove, scope: e});
            e.onCellAdd(d, {items: d.getRange()})
        }
    },
    onCellAdd: function (l, p) {
        var j = p.items, m = j.length, h, n;
        for (h = 0; h < m; h++) {
            n = j[h];
            n.setRow(this)
        }
        this.updateCellIndexes()
    },
    onCellRemove: function (c, d) {
        Ext.destroy(d.items);
        this.updateCellIndexes()
    },
    updateCellIndexes: function () {
        var j = this.getCells(), h, e, g;
        if (!j) {
            return
        }
        e = j.length;
        for (h = 0; h < e; h++) {
            g = j.getAt(h);
            if (!g.getIndex()) {
                g.setIndex(h + 1)
            }
        }
    },
    addCell: function (b) {
        return this.getCells().add(b)
    },
    getCell: function (b) {
        return this.getCells().get(b)
    }
}, 0, 0, 0, 0, 0, 0, [Ext.exporter.file.ooxml.excel, "Row"], 0));
(Ext.cmd.derive("Ext.exporter.file.ooxml.excel.Worksheet", Ext.exporter.file.ooxml.excel.Sheet, {
    isWorksheet: true,
    config: {columns: null, rows: [], drawings: null, tables: null, mergeCells: null},
    folder: "worksheets",
    fileName: "sheet",
    contentType: {contentType: "application/vnd.openxmlformats-officedocument.spreadsheetml.worksheet+xml"},
    relationship: {schema: "http://schemas.openxmlformats.org/officeDocument/2006/relationships/worksheet"},
    tpl: ['<?xml version="1.0" encoding="UTF-8" standalone="yes"?>', '<worksheet xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main" ', 'xmlns:r="http://schemas.openxmlformats.org/officeDocument/2006/relationships">', '<tpl if="columns">', "<cols>", '<tpl for="columns.getRange()">{[values.render()]}</tpl>', "</cols>", "</tpl>", "<sheetData>", '<tpl if="rows"><tpl for="rows.getRange()">{[values.render()]}</tpl></tpl>', "</sheetData>", '<tpl if="rows">', "<mergeCells>", '<tpl for="rows.getRange()">', '<tpl for="_cells.items">', '<tpl if="isMergedCell"><mergeCell ref="{[values.getMergedCellRef()]}"/></tpl>', "</tpl>", "</tpl>", "</mergeCells>", "</tpl>", "</worksheet>"],
    destroy: function () {
        var b = this;
        b.setRows(null);
        b.setTables(null);
        b.setDrawings(null);
        Ext.exporter.file.ooxml.excel.Sheet.prototype.destroy.call(this)
    },
    applyColumns: function (c, d) {
        return this.checkCollection(c, d, "Ext.exporter.file.ooxml.excel.Column")
    },
    applyRows: function (c, d) {
        return this.checkCollection(c, d, "Ext.exporter.file.ooxml.excel.Row")
    },
    updateRows: function (d, g) {
        var e = this;
        if (g) {
            g.un({add: e.onRowAdd, remove: e.onRowRemove, scope: e})
        }
        if (d) {
            d.on({add: e.onRowAdd, remove: e.onRowRemove, scope: e});
            e.onRowAdd(d, {items: d.getRange()})
        }
    },
    onRowAdd: function (l, p) {
        var j = p.items, m = j.length, h, n;
        for (h = 0; h < m; h++) {
            n = j[h];
            n.setWorksheet(this)
        }
        this.updateRowIndexes()
    },
    onRowRemove: function (c, d) {
        Ext.destroy(d.items)
    },
    updateRowIndexes: function () {
        var j = this.getRows(), e, g, h;
        if (!j) {
            return
        }
        g = j.length;
        for (e = 0; e < g; e++) {
            h = j.getAt(e);
            if (!h.getIndex()) {
                h.setIndex(e + 1)
            }
        }
    },
    updateDrawings: function (c) {
        var d = this.getRelationships();
        if (oldData && d) {
            d.removeRelationship(oldData.getRelationship())
        }
        if (c && d) {
            d.addRelationship(c.getRelationship())
        }
    },
    updateTables: function (c) {
        var d = this.getRelationships();
        if (oldData && d) {
            d.removeRelationship(oldData.getRelationship())
        }
        if (c && d) {
            d.addRelationship(c.getRelationship())
        }
    },
    addColumn: function (b) {
        if (!this.getColumns()) {
            this.setColumns([])
        }
        return this.getColumns().add(b || {})
    },
    addRow: function (b) {
        return this.getRows().add(b || {})
    },
    getRow: function (b) {
        return this.getRows().get(b)
    }
}, 0, 0, 0, 0, 0, 0, [Ext.exporter.file.ooxml.excel, "Worksheet"], 0));
(Ext.cmd.derive("Ext.exporter.file.ooxml.excel.Font", Ext.exporter.file.ooxml.Base, {
    config: {
        size: 10,
        fontName: "",
        family: null,
        charset: null,
        bold: false,
        italic: false,
        underline: false,
        outline: false,
        strikeThrough: false,
        color: null,
        verticalAlign: null
    },
    mappings: {family: {Automatic: 0, Roman: 1, Swiss: 2, Modern: 3, Script: 4, Decorative: 5}},
    tpl: ["<font>", '<tpl if="size"><sz val="{size}"/></tpl>', '<tpl if="fontName"><name val="{fontName}"/></tpl>', '<tpl if="family"><family val="{family}"/></tpl>', '<tpl if="charset"><charset val="{charset}"/></tpl>', '<tpl if="bold"><b/></tpl>', '<tpl if="italic"><i/></tpl>', '<tpl if="underline"><u/></tpl>', '<tpl if="outline"><outline/></tpl>', '<tpl if="strikeThrough"><strike/></tpl>', '<tpl if="color"><color rgb="{color}"/></tpl>', '<tpl if="verticalAlign"><vertAlign val="{verticalAlign}"/></tpl>', "</font>"],
    constructor: function (m) {
        var g = {}, j = Ext.Object.getKeys(m || {}), h = j.length, l;
        if (m) {
            for (l = 0; l < h; l++) {
                g[Ext.String.uncapitalize(j[l])] = m[j[l]]
            }
        }
        Ext.exporter.file.ooxml.Base.prototype.constructor.call(this, g)
    },
    applyFamily: function (b) {
        if (typeof b === "string") {
            return this.mappings.family[b]
        }
        return b
    },
    applyBold: function (b) {
        return !!b
    },
    applyItalic: function (b) {
        return !!b
    },
    applyStrikeThrough: function (b) {
        return !!b
    },
    applyUnderline: function (b) {
        return !!b
    },
    applyOutline: function (b) {
        return !!b
    },
    applyColor: function (c) {
        var d;
        if (!c) {
            return c
        }
        d = String(c);
        return d.indexOf("#") >= 0 ? d.replace("#", "") : d
    },
    applyVerticalAlign: function (b) {
        return Ext.util.Format.lowercase(b)
    }
}, 1, 0, 0, 0, 0, 0, [Ext.exporter.file.ooxml.excel, "Font"], 0));
(Ext.cmd.derive("Ext.exporter.file.ooxml.excel.NumberFormat", Ext.exporter.file.ooxml.Base, {
    config: {
        isDate: false,
        numFmtId: null,
        formatCode: ""
    },
    tpl: ['<numFmt numFmtId="{numFmtId}" formatCode="{formatCode}"/>'],
    spaceRe: /(,| )/g,
    getRenderData: function () {
        var c = Ext.exporter.file.ooxml.Base.prototype.getRenderData.call(this), d = c.formatCode;
        d = (d && c.isDate) ? d.replace(this.spaceRe, "\\$1") : d;
        c.formatCode = d;
        return c
    },
    applyFormatCode: function (b) {
        return b ? Ext.util.Format.htmlEncode(b) : b
    },
    getKey: function () {
        return this.getFormatCode()
    }
}, 0, 0, 0, 0, 0, 0, [Ext.exporter.file.ooxml.excel, "NumberFormat"], 0));
(Ext.cmd.derive("Ext.exporter.file.ooxml.excel.Fill", Ext.exporter.file.ooxml.Base, {
    config: {
        patternType: "none",
        fgColor: null,
        bgColor: null
    },
    tpl: ["<fill>", '<patternFill patternType="{patternType}">', '<tpl if="fgColor"><fgColor rgb="{fgColor}"></fgColor></tpl>', '<tpl if="bgColor"><bgColor rgb="{bgColor}"></bgColor></tpl>', "</patternFill>", "</fill>"],
    constructor: function (c) {
        var d = {};
        if (c) {
            d.id = c.id;
            d.bgColor = c.Color || null;
            d.patternType = c.Pattern || null
        }
        Ext.exporter.file.ooxml.Base.prototype.constructor.call(this, d)
    },
    formatColor: function (c) {
        var d;
        if (!c) {
            return c
        }
        d = String(c);
        return d.indexOf("#") >= 0 ? d.replace("#", "") : d
    },
    applyFgColor: function (b) {
        return this.formatColor(b)
    },
    applyBgColor: function (b) {
        return this.formatColor(b)
    },
    applyPatternType: function (g) {
        var d = ["none", "solid", "mediumGray", "darkGray", "lightGray", "darkHorizontal", "darkVertical", "darkDown", "darkUp", "darkGrid", "darkTrellis", "lightHorizontal", "lightVertical", "lightDown", "lightUp", "lightGrid", "lightTrellis", "gray125", "gray0625"],
            e = Ext.util.Format.uncapitalize(g);
        return Ext.Array.indexOf(d, e) >= 0 ? e : "none"
    }
}, 1, 0, 0, 0, 0, 0, [Ext.exporter.file.ooxml.excel, "Fill"], 0));
(Ext.cmd.derive("Ext.exporter.file.ooxml.excel.BorderPr", Ext.exporter.file.ooxml.Base, {
    isBorderPr: true,
    config: {tag: "left", color: null, lineStyle: "none"},
    mappings: {
        lineStyle: {
            None: "none",
            Continuous: "thin",
            Dash: "dashed",
            Dot: "dotted",
            DashDot: "dashDot",
            DashDotDot: "dashDotDot",
            SlantDashDot: "slantDashDot",
            Double: "double"
        }
    },
    tpl: ['<{tag} style="{lineStyle}">', '<tpl if="color"><color rgb="{color}"/></tpl>', "</{tag}>"],
    applyColor: function (c) {
        var d;
        if (!c) {
            return c
        }
        d = String(c);
        return d.indexOf("#") >= 0 ? d.replace("#", "") : d
    },
    applyLineStyle: function (c) {
        var d = ["none", "thin", "medium", "dashed", "dotted", "thick", "double", "hair", "mediumDashed", "dashDot", "mediumDashDot", "dashDotDot", "mediumDashDotDot", "slantDashDot"];
        return Ext.Array.indexOf(d, c) >= 0 ? c : (this.mappings.lineStyle[c] || "none")
    }
}, 0, 0, 0, 0, 0, 0, [Ext.exporter.file.ooxml.excel, "BorderPr"], 0));
(Ext.cmd.derive("Ext.exporter.file.ooxml.excel.Border", Ext.exporter.file.ooxml.Base, {
    config: {
        left: null,
        right: null,
        top: null,
        bottom: null
    },
    tpl: ["<border>", '<tpl if="left">{[values.left.render()]}</tpl>', '<tpl if="right">{[values.right.render()]}</tpl>', '<tpl if="top">{[values.top.render()]}</tpl>', '<tpl if="bottom">{[values.bottom.render()]}</tpl>', "</border>"],
    applyLeft: function (b) {
        if (b && !b.isBorderPr) {
            return new Ext.exporter.file.ooxml.excel.BorderPr(b)
        }
        return b
    },
    applyTop: function (b) {
        if (b && !b.isBorderPr) {
            return new Ext.exporter.file.ooxml.excel.BorderPr(b)
        }
        return b
    },
    applyRight: function (b) {
        if (b && !b.isBorderPr) {
            return new Ext.exporter.file.ooxml.excel.BorderPr(b)
        }
        return b
    },
    applyBottom: function (b) {
        if (b && !b.isBorderPr) {
            return new Ext.exporter.file.ooxml.excel.BorderPr(b)
        }
        return b
    },
    updateLeft: function (b) {
        if (b) {
            b.setTag("left")
        }
    },
    updateTop: function (b) {
        if (b) {
            b.setTag("top")
        }
    },
    updateRight: function (b) {
        if (b) {
            b.setTag("right")
        }
    },
    updateBottom: function (b) {
        if (b) {
            b.setTag("bottom")
        }
    }
}, 0, 0, 0, 0, 0, 0, [Ext.exporter.file.ooxml.excel, "Border"], 0));
(Ext.cmd.derive("Ext.exporter.file.ooxml.excel.CellAlignment", Ext.exporter.file.ooxml.Base, {
    isCellAlignment: true,
    config: {
        horizontal: "general",
        vertical: "top",
        rotate: null,
        wrapText: false,
        indent: null,
        relativeIndent: null,
        justifyLastLine: false,
        shrinkToFit: false,
        readingOrder: null
    },
    mappings: {
        horizontal: {
            Automatic: "general",
            CenterAcrossSelection: "centerContinuous",
            JustifyDistributed: "distributed"
        },
        vertical: {Automatic: "top", JustifyDistributed: "distributed"},
        readingOrder: {Context: 0, LeftToRight: 1, RightToLeft: 2}
    },
    tpl: ["<alignment", '<tpl if="horizontal"> horizontal="{horizontal}"</tpl>', '<tpl if="vertical"> vertical="{vertical}"</tpl>', '<tpl if="rotate"> textRotation="{rotate}"</tpl>', '<tpl if="wrapText"> wrapText="{wrapText}"</tpl>', '<tpl if="indent"> indent="{indent}"</tpl>', '<tpl if="relativeIndent"> relativeIndent="{relativeIndent}"</tpl>', '<tpl if="justifyLastLine"> justifyLastLine="{justifyLastLine}"</tpl>', '<tpl if="shrinkToFit"> shrinkToFit="{shrinkToFit}"</tpl>', '<tpl if="readingOrder"> readingOrder="{readingOrder}"</tpl>', "/>"],
    constructor: function (m) {
        var g = {}, j = Ext.Object.getKeys(m || {}), h = j.length, l;
        if (m) {
            for (l = 0; l < h; l++) {
                g[Ext.String.uncapitalize(j[l])] = m[j[l]]
            }
        }
        Ext.exporter.file.ooxml.Base.prototype.constructor.call(this, g)
    },
    applyHorizontal: function (g) {
        var d = ["general", "left", "center", "right", "fill", "justify", "centerContinuous", "distributed"],
            e = Ext.util.Format.uncapitalize(g);
        return Ext.Array.indexOf(d, e) >= 0 ? e : (this.mappings.horizontal[g] || "general")
    },
    applyVertical: function (g) {
        var d = ["top", "center", "bottom", "justify", "distributed"], e = Ext.util.Format.uncapitalize(g);
        return Ext.Array.indexOf(d, e) >= 0 ? e : (this.mappings.vertical[g] || "top")
    },
    applyReadingOrder: function (b) {
        if (typeof b === "string") {
            return this.mappings.readingOrder[b] || 0
        }
        return b
    }
}, 1, 0, 0, 0, 0, 0, [Ext.exporter.file.ooxml.excel, "CellAlignment"], 0));
(Ext.cmd.derive("Ext.exporter.file.ooxml.excel.CellStyleXf", Ext.exporter.file.ooxml.Base, {
    config: {
        numFmtId: 0,
        fontId: 0,
        fillId: 0,
        borderId: 0,
        alignment: null
    },
    tpl: ['<xf numFmtId="{numFmtId}" fontId="{fontId}" fillId="{fillId}" borderId="{borderId}" <tpl if="fontId"> applyFont="1"</tpl><tpl if="alignment"> applyAlignment="1"</tpl>>', '<tpl if="alignment">{[values.alignment.render()]}</tpl>', "</xf>"],
    applyAlignment: function (b) {
        if (b && !b.isCellAlignment) {
            return new Ext.exporter.file.ooxml.excel.CellAlignment(b)
        }
        return b
    }
}, 0, 0, 0, 0, 0, 0, [Ext.exporter.file.ooxml.excel, "CellStyleXf"], 0));
(Ext.cmd.derive("Ext.exporter.file.ooxml.excel.CellXf", Ext.exporter.file.ooxml.excel.CellStyleXf, {
    config: {xfId: 0},
    tpl: ['<xf numFmtId="{numFmtId}" fontId="{fontId}" fillId="{fillId}" borderId="{borderId}" xfId="{xfId}"', '<tpl if="numFmtId"> applyNumberFormat="1"</tpl>', '<tpl if="fillId"> applyFill="1"</tpl>', '<tpl if="borderId"> applyBorder="1"</tpl>', '<tpl if="fontId"> applyFont="1"</tpl>', '<tpl if="alignment"> applyAlignment="1"</tpl>>', '<tpl if="alignment">{[values.alignment.render()]}</tpl>', "</xf>"]
}, 0, 0, 0, 0, 0, 0, [Ext.exporter.file.ooxml.excel, "CellXf"], 0));
(Ext.cmd.derive("Ext.exporter.file.ooxml.excel.Stylesheet", Ext.exporter.file.ooxml.Base, {
    isStylesheet: true,
    config: {
        fonts: [{fontName: "Arial", size: 10, family: 2}],
        numberFormats: null,
        fills: [{patternType: "none"}],
        borders: [{left: {}, top: {}, right: {}, bottom: {}}],
        cellStyleXfs: [{}],
        cellXfs: [{}]
    },
    contentType: {
        contentType: "application/vnd.openxmlformats-officedocument.spreadsheetml.styles+xml",
        partName: "/xl/styles.xml"
    },
    relationship: {
        schema: "http://schemas.openxmlformats.org/officeDocument/2006/relationships/styles",
        target: "styles.xml"
    },
    path: "/xl/styles.xml",
    tpl: ['<?xml version="1.0" encoding="UTF-8" standalone="yes"?>', '<styleSheet xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main">', '<tpl if="numberFormats"><numFmts count="{numberFormats.length}"><tpl for="numberFormats.getRange()">{[values.render()]}</tpl></numFmts></tpl>', '<tpl if="fonts"><fonts count="{fonts.length}"><tpl for="fonts.getRange()">{[values.render()]}</tpl></fonts></tpl>', '<tpl if="fills"><fills count="{fills.length}"><tpl for="fills.getRange()">{[values.render()]}</tpl></fills></tpl>', '<tpl if="borders"><borders count="{borders.length}"><tpl for="borders.getRange()">{[values.render()]}</tpl></borders></tpl>', '<tpl if="cellStyleXfs"><cellStyleXfs count="{cellStyleXfs.length}"><tpl for="cellStyleXfs.getRange()">{[values.render()]}</tpl></cellStyleXfs></tpl>', '<tpl if="cellXfs"><cellXfs count="{cellXfs.length}"><tpl for="cellXfs.getRange()">{[values.render()]}</tpl></cellXfs></tpl>', "</styleSheet>"],
    lastNumberFormatId: 164,
    datePatterns: {
        "General Date": "[$-F800]dddd, mmmm dd, yyyy",
        "Long Date": "[$-F800]dddd, mmmm dd, yyyy",
        "Medium Date": "mm/dd/yy;@",
        "Short Date": "m/d/yy;@",
        "Long Time": "h:mm:ss;@",
        "Medium Time": "[$-409]h:mm AM/PM;@",
        "Short Time": "h:mm;@"
    },
    numberPatterns: {
        "General Number": 1,
        Fixed: 2,
        Standard: 2,
        Percent: 10,
        Scientific: 11,
        Currency: '"$"#,##0.00',
        "Euro Currency": '"€"#,##0.00'
    },
    booleanPatterns: {"Yes/No": '"Yes";-;"No"', "True/False": '"True";-;"False"', "On/Off": '"On";-;"Off"'},
    destroy: function () {
        var b = this;
        b.setFonts(null);
        b.setNumberFormats(null);
        b.setFills(null);
        b.setBorders(null);
        b.setCellXfs(null);
        Ext.exporter.file.ooxml.Base.prototype.destroy.call(this)
    },
    applyFonts: function (c, d) {
        return this.checkCollection(c, d, "Ext.exporter.file.ooxml.excel.Font")
    },
    applyNumberFormats: function (c, d) {
        return this.checkCollection(c, d, "Ext.exporter.file.ooxml.excel.NumberFormat")
    },
    applyFills: function (c, d) {
        return this.checkCollection(c, d, "Ext.exporter.file.ooxml.excel.Fill")
    },
    applyBorders: function (c, d) {
        return this.checkCollection(c, d, "Ext.exporter.file.ooxml.excel.Border")
    },
    applyCellXfs: function (c, d) {
        return this.checkCollection(c, d, "Ext.exporter.file.ooxml.excel.CellXf")
    },
    applyCellStyleXfs: function (c, d) {
        return this.checkCollection(c, d, "Ext.exporter.file.ooxml.excel.CellStyleXf")
    },
    addFont: function (g) {
        var d = this.getFonts(), e;
        if (!d) {
            this.setFonts([]);
            d = this.getFonts()
        }
        e = d.add(g);
        return d.indexOf(e)
    },
    addNumberFormat: function (h) {
        var j = this.getNumberFormats(), e, g;
        if (!j) {
            this.setNumberFormats([]);
            j = this.getNumberFormats()
        }
        g = new Ext.exporter.file.ooxml.excel.NumberFormat(h);
        e = j.get(g.getKey());
        if (!e) {
            e = g;
            j.add(e);
            e.setNumFmtId(this.lastNumberFormatId++)
        }
        return e.getNumFmtId()
    },
    addFill: function (g) {
        var d = this.getFills(), e;
        if (!d) {
            this.setFills([]);
            d = this.getFills()
        }
        e = d.add(g);
        return d.indexOf(e)
    },
    addBorder: function (g) {
        var d = this.getBorders(), e;
        if (!d) {
            this.setBorders([]);
            d = this.getBorders()
        }
        e = d.add(g);
        return d.indexOf(e)
    },
    addCellXf: function (g) {
        var d = this.getCellXfs(), e;
        if (!d) {
            this.setCellXfs([]);
            d = this.getCellXfs()
        }
        e = d.add(g);
        return d.indexOf(e)
    },
    addCellStyleXf: function (g) {
        var d = this.getCellStyleXfs(), e;
        if (!d) {
            this.setCellStyleXfs([]);
            d = this.getCellStyleXfs()
        }
        e = d.add(g);
        return d.indexOf(e)
    },
    getStyleParams: function (w) {
        var p = this, m = new Ext.exporter.file.Style(w), q = m.getConfig(), t = 0, s = 0, n = 0, r = 0, u = 0;
        q.parentId = w ? w.parentId : null;
        if (q.font) {
            s = p.addFont(q.font)
        }
        if (q.format) {
            t = p.getNumberFormatId(q.format)
        }
        if (q.interior) {
            n = p.addFill(q.interior)
        }
        if (q.borders) {
            r = p.getBorderId(q.borders)
        }
        if (q.parentId) {
            u = q.parentId
        }
        return {numFmtId: t, fontId: s, fillId: n, borderId: r, xfId: u, alignment: q.alignment || null}
    },
    addStyle: function (b) {
        return this.addCellStyleXf(this.getStyleParams(b))
    },
    addCellStyle: function (b) {
        return this.addCellXf(this.getStyleParams(b))
    },
    getNumberFormatId: function (l) {
        var m = this, h = !!m.datePatterns[l], j, g;
        if (l === "General") {
            return 0
        }
        g = m.datePatterns[l] || m.booleanPatterns[l] || m.numberPatterns[l];
        if (Ext.isNumeric(g)) {
            j = g
        } else {
            if (!g) {
                g = l
            }
        }
        return j || m.addNumberFormat({isDate: h, formatCode: g})
    },
    getBorderId: function (j) {
        var n = {}, p = j.length, l, b, m;
        for (l = 0; l < p; l++) {
            b = j[l];
            m = Ext.util.Format.lowercase(b.position);
            delete (b.position);
            n[m] = b
        }
        return this.addBorder(n)
    }
}, 0, 0, 0, 0, 0, 0, [Ext.exporter.file.ooxml.excel, "Stylesheet"], 0));
(Ext.cmd.derive("Ext.exporter.file.ooxml.excel.SharedStrings", Ext.exporter.file.ooxml.Base, {
    isSharedStrings: true,
    config: {strings: []},
    contentType: {
        contentType: "application/vnd.openxmlformats-officedocument.spreadsheetml.sharedStrings+xml",
        partName: "/xl/sharedStrings.xml"
    },
    relationship: {
        schema: "http://schemas.openxmlformats.org/officeDocument/2006/relationships/sharedStrings",
        target: "sharedStrings.xml"
    },
    path: "/xl/sharedStrings.xml",
    tpl: ['<?xml version="1.0" encoding="UTF-8" standalone="yes"?>', '<sst xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main">', '<tpl for="strings"><si><t>{.:this.utf8}</t></si></tpl>', "</sst>", {
        utf8: function (b) {
            return Ext.util.Base64._utf8_encode(b)
        }
    }],
    destroy: function () {
        this.setStrings(null);
        Ext.exporter.file.ooxml.Base.prototype.destroy.call(this)
    },
    addString: function (h) {
        var g = Ext.util.Format.htmlEncode(h), j = this.getStrings(), e = Ext.Array.indexOf(j, g);
        if (e < 0) {
            j.push(g);
            e = j.length - 1
        }
        return e
    }
}, 0, 0, 0, 0, 0, 0, [Ext.exporter.file.ooxml.excel, "SharedStrings"], 0));
(Ext.cmd.derive("Ext.exporter.file.ooxml.excel.Workbook", Ext.exporter.file.ooxml.Base, {
    isWorkbook: true,
    currentIndex: 1,
    config: {
        relationships: {
            contentType: {
                contentType: "application/vnd.openxmlformats-package.relationships+xml",
                partName: "/xl/_rels/workbook.xml.rels"
            }, path: "/xl/_rels/workbook.xml.rels"
        }, stylesheet: {}, sharedStrings: {}, sheets: []
    },
    contentType: {
        contentType: "application/vnd.openxmlformats-officedocument.spreadsheetml.sheet.main+xml",
        partName: "/xl/workbook.xml"
    },
    relationship: {
        schema: "http://schemas.openxmlformats.org/officeDocument/2006/relationships/officeDocument",
        target: "xl/workbook.xml"
    },
    path: "/xl/workbook.xml",
    tpl: ['<?xml version="1.0" encoding="UTF-8" standalone="yes"?>', '<workbook xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main" ', 'xmlns:r="http://schemas.openxmlformats.org/officeDocument/2006/relationships">', "<sheets>", '<tpl if="sheets"><tpl for="sheets.getRange()"><sheet name="{[values.getName()]}" sheetId="{[xindex]}" state="visible" r:id="{[values.getRelationship().getId()]}"/></tpl></tpl>', "</sheets>", "</workbook>"],
    destroy: function () {
        var b = this;
        b.setRelationships(Ext.destroy(b.getRelationships()));
        b.setStylesheet(Ext.destroy(b.getStylesheet()));
        b.setSharedStrings(Ext.destroy(b.getSharedStrings()));
        b.setSheets(null);
        Ext.exporter.file.ooxml.Base.prototype.destroy.call(this)
    },
    collectFiles: function (m) {
        var n = this, q = n.getStylesheet(), l = n.getSharedStrings(), j, r, p;
        m[n.getPath()] = n.render();
        m[q.getPath()] = q.render();
        m[l.getPath()] = l.render();
        j = n.getSheets();
        p = j.length;
        for (r = 0; r < p; r++) {
            j.getAt(r).collectFiles(m)
        }
        n.getRelationships().collectFiles(m)
    },
    addWorksheet: function (m) {
        var g = Ext.Array.from(m), j = g.length, l, h;
        for (l = 0; l < j; l++) {
            h = g[l];
            if (h && !h.isWorksheet) {
                g[l] = new Ext.exporter.file.ooxml.excel.Worksheet(h)
            }
        }
        return this.getSheets().add(g)
    },
    removeWorksheet: function (b) {
        return this.getSheets().remove(b)
    },
    getContentTypes: function () {
        var j = this, m = [], h, g, l;
        m.push(j.getContentType());
        m.push(j.getStylesheet().getContentType());
        m.push(j.getSharedStrings().getContentType());
        h = j.getSheets();
        l = h.length;
        for (g = 0; g < l; g++) {
            m.push(h.getAt(g).getContentType())
        }
        return m
    },
    applyRelationships: function (b) {
        if (!b || b.isRelationships) {
            return b
        }
        return new Ext.exporter.file.ooxml.Relationships(b)
    },
    applyStylesheet: function (b) {
        if (!b || b.isStylesheet) {
            return b
        }
        return new Ext.exporter.file.ooxml.excel.Stylesheet()
    },
    updateStylesheet: function (d, g) {
        var e = this.getRelationships();
        if (g && e) {
            e.removeRelationship(g.getRelationship())
        }
        if (d && e) {
            e.addRelationship(d.getRelationship())
        }
    },
    applySharedStrings: function (b) {
        if (!b || b.isSharedStrings) {
            return b
        }
        return new Ext.exporter.file.ooxml.excel.SharedStrings()
    },
    updateSharedStrings: function (d, g) {
        var e = this.getRelationships();
        if (g && e) {
            e.removeRelationship(g.getRelationship())
        }
        if (d) {
            e.addRelationship(d.getRelationship())
        }
    },
    applySheets: function (c, d) {
        return this.checkCollection(c, d, "Ext.exporter.file.ooxml.excel.Sheet")
    },
    updateSheets: function (d, g) {
        var e = this;
        if (g) {
            g.un({add: e.onSheetAdd, remove: e.onSheetRemove, scope: e})
        }
        if (d) {
            d.on({add: e.onSheetAdd, remove: e.onSheetRemove, scope: e})
        }
    },
    onSheetAdd: function (l, p) {
        var j = this.getRelationships(), m = p.items.length, h, n;
        for (h = 0; h < m; h++) {
            n = p.items[h];
            n.setIndex(this.currentIndex++);
            n.setWorkbook(this);
            j.addRelationship(n.getRelationship())
        }
    },
    onSheetRemove: function (l, p) {
        var j = this.getRelationships(), m = p.items.length, h, n;
        for (h = 0; h < m; h++) {
            n = p.items[h];
            j.removeRelationship(n.getRelationship());
            Ext.destroy(n)
        }
    },
    addStyle: function (b) {
        return this.getStylesheet().addStyle(b)
    }
}, 0, 0, 0, 0, 0, 0, [Ext.exporter.file.ooxml.excel, "Workbook"], 0));
(Ext.cmd.derive("Ext.exporter.file.ooxml.ContentTypes", Ext.exporter.file.ooxml.Base, {
    isContentTypes: true,
    config: {
        contentTypes: [{
            tag: "Default",
            contentType: "application/vnd.openxmlformats-package.relationships+xml",
            extension: "rels"
        }, {tag: "Default", contentType: "application/xml", extension: "xml"}]
    },
    tpl: ['<?xml version="1.0" encoding="UTF-8" standalone="yes"?>', '<Types xmlns="http://schemas.openxmlformats.org/package/2006/content-types">', '<tpl if="contentTypes"><tpl for="contentTypes.getRange()">{[values.render()]}</tpl></tpl>', "</Types>"],
    path: "/[Content_Types].xml",
    destroy: function () {
        this.setContentTypes(null);
        Ext.exporter.file.ooxml.Base.prototype.destroy.call(this)
    },
    applyContentTypes: function (c, d) {
        return this.checkCollection(c, d, "Ext.exporter.file.ooxml.ContentType")
    },
    addContentType: function (b) {
        return this.getContentTypes().add(b || {})
    }
}, 0, 0, 0, 0, 0, 0, [Ext.exporter.file.ooxml, "ContentTypes"], 0));
(Ext.cmd.derive("Ext.exporter.file.ooxml.CoreProperties", Ext.exporter.file.ooxml.Base, {
    isCoreProperties: true,
    config: {title: "Workbook", author: "Sencha", subject: ""},
    contentType: {
        contentType: "application/vnd.openxmlformats-package.core-properties+xml",
        partName: "/docProps/core.xml"
    },
    relationship: {
        schema: "http://schemas.openxmlformats.org/package/2006/relationships/metadata/core-properties",
        target: "docProps/core.xml"
    },
    path: "/docProps/core.xml",
    tpl: ['<coreProperties xmlns="http://schemas.openxmlformats.org/package/2006/metadata/core-properties" ', 'xmlns:dcterms="http://purl.org/dc/terms/" ', 'xmlns:dc="http://purl.org/dc/elements/1.1/" ', 'xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance">', "   <dc:creator>{author}</dc:creator>", "   <dc:title>{title}</dc:title>", "   <dc:subject>{subject}</dc:subject>", "</coreProperties>"]
}, 0, 0, 0, 0, 0, 0, [Ext.exporter.file.ooxml, "CoreProperties"], 0));
(Ext.cmd.derive("Ext.exporter.file.ooxml.Excel", Ext.exporter.file.ooxml.Base, {
    config: {
        relationships: {path: "/_rels/.rels"},
        properties: null,
        workbook: {}
    }, tpl: [], constructor: function (c) {
        var d = Ext.exporter.file.ooxml.Base.prototype.constructor.call(this, c);
        if (!this.getWorkbook()) {
            this.setWorkbook({})
        }
        return d
    }, destroy: function () {
        var h = this, g, j, e;
        g = h.getWorkbook();
        j = h.getProperties();
        e = h.getRelationships();
        h.setWorkbook(null);
        h.setProperties(null);
        h.setRelationships(null);
        Ext.destroy(g, j, e);
        Ext.exporter.file.ooxml.Base.prototype.destroy.call(this)
    }, render: function () {
        var p = {}, m, n, q, j, l, r;
        this.collectFiles(p);
        m = Ext.Object.getKeys(p);
        l = m.length;
        if (!l) {
            return
        }
        r = new Ext.exporter.file.zip.Archive();
        for (j = 0; j < l; j++) {
            n = m[j];
            q = p[n];
            n = n.substr(1);
            if (n.indexOf(".xml") !== -1 || n.indexOf(".rel") !== -1) {
                r.addFile({path: n, data: q})
            }
        }
        q = r.getContent();
        r = Ext.destroy(r);
        return q
    }, collectFiles: function (j) {
        var g = new Ext.exporter.file.ooxml.ContentTypes(), h = this.getWorkbook(), e = this.getProperties();
        h.collectFiles(j);
        if (e) {
            g.addContentType(e.getContentType());
            j[e.getPath()] = e.render()
        }
        g.addContentType(h.getContentTypes());
        j[g.getPath()] = g.render();
        Ext.destroy(g);
        this.getRelationships().collectFiles(j)
    }, applyProperties: function (b) {
        if (!b || b.isCoreProperties) {
            return b
        }
        return new Ext.exporter.file.ooxml.CoreProperties(b)
    }, updateProperties: function (d, g) {
        var e = this.getRelationships();
        if (g) {
            e.removeRelationship(g.getRelationship())
        }
        if (d) {
            e.addRelationship(d.getRelationship())
        }
    }, applyRelationships: function (b) {
        if (!b || b.isRelationships) {
            return b
        }
        return new Ext.exporter.file.ooxml.Relationships(b)
    }, applyWorkbook: function (b) {
        if (!b || b.isWorkbook) {
            return b
        }
        return new Ext.exporter.file.ooxml.excel.Workbook(b)
    }, updateWorkbook: function (d, g) {
        var e = this.getRelationships();
        if (g) {
            e.removeRelationship(g.getRelationship())
        }
        if (d) {
            e.addRelationship(d.getRelationship())
        }
    }, addWorksheet: function (b) {
        return this.getWorkbook().addWorksheet(b)
    }, addStyle: function (b) {
        return this.getWorkbook().getStylesheet().addStyle(b)
    }, addCellStyle: function (b) {
        return this.getWorkbook().getStylesheet().addCellStyle(b)
    }
}, 1, 0, 0, 0, 0, 0, [Ext.exporter.file.ooxml, "Excel"], 0));
(Ext.cmd.derive("Ext.exporter.excel.Xlsx", Ext.exporter.Base, {
    alternateClassName: "Ext.exporter.Excel",
    config: {
        defaultStyle: {
            alignment: {vertical: "Top"},
            font: {fontName: "Arial", family: "Swiss", size: 11, color: "#000000"}
        },
        titleStyle: {
            alignment: {horizontal: "Center", vertical: "Center"},
            font: {fontName: "Arial", family: "Swiss", size: 18, color: "#1F497D"}
        },
        groupHeaderStyle: {borders: [{position: "Bottom", lineStyle: "Continuous", weight: 1, color: "#4F81BD"}]},
        groupFooterStyle: {borders: [{position: "Top", lineStyle: "Continuous", weight: 1, color: "#4F81BD"}]},
        tableHeaderStyle: {
            alignment: {horizontal: "Center", vertical: "Center"},
            borders: [{position: "Bottom", lineStyle: "Continuous", weight: 1, color: "#4F81BD"}],
            font: {fontName: "Arial", family: "Swiss", size: 11, color: "#1F497D"}
        }
    },
    fileName: "export.xlsx",
    charset: "ascii",
    mimeType: "application/zip",
    binary: true,
    destroy: function () {
        var b = this;
        b.excel = b.worksheet = Ext.destroy(b.excel, b.worksheet);
        Ext.exporter.Base.prototype.destroy.call(this)
    },
    getContent: function () {
        var e = this, g = this.getConfig(), j = g.data, h;
        e.excel = new Ext.exporter.file.ooxml.Excel({properties: {title: g.title, author: g.author}});
        e.worksheet = e.excel.addWorksheet({name: g.title});
        e.tableHeaderStyleId = e.excel.addCellStyle(g.tableHeaderStyle);
        h = j ? j.getColumnCount() : 1;
        e.addTitle(g, h);
        if (j) {
            e.buildHeader();
            e.buildRows(j.getGroups(), h, 0)
        }
        e.columnStylesNormal = e.columnStylesNormalId = e.columnStylesFooter = e.columnStylesFooterId = null;
        return e.excel.render()
    },
    addTitle: function (d, c) {
        if (!Ext.isEmpty(d.title)) {
            this.worksheet.addRow({height: 22.5}).addCell({
                mergeAcross: c - 1,
                value: d.title,
                styleId: this.excel.addCellStyle(d.titleStyle)
            })
        }
    },
    buildRows: function (F, H, G) {
        var j = this, B = j.getShowSummary(), A, I, L, K, J, C, D, E, g, y, M, z;
        if (!F) {
            return
        }
        L = j.excel.addCellStyle(Ext.applyIf({alignment: {Indent: G > 0 ? G : 0}}, j.getGroupHeaderStyle()));
        K = j.excel.addCellStyle(Ext.applyIf({alignment: {Indent: G > 0 ? G : 0}}, j.columnStylesFooter[0]));
        g = F.length;
        for (C = 0; C < g; C++) {
            A = F.getAt(C).getConfig();
            z = (!A.groups && !A.rows);
            if (B !== false && !Ext.isEmpty(A.text) && !z) {
                j.worksheet.addRow({styleId: L}).addCell({mergeAcross: H - 1, value: A.text, styleId: L})
            }
            j.buildRows(A.groups, H, G + 1);
            j.buildGroupRows(A.rows);
            if (A.summaries && (B || z)) {
                y = A.summaries.length;
                for (E = 0; E < y; E++) {
                    I = j.worksheet.addRow();
                    J = A.summaries.getAt(E).getCells();
                    M = J.length;
                    for (D = 0; D < M; D++) {
                        I.addCell(J.getAt(D).getConfig()).setStyleId(z ? j.columnStylesNormalId[D] : (D === 0 ? K : j.columnStylesFooterId[D]))
                    }
                }
            }
        }
    },
    buildGroupRows: function (p) {
        var n, t, s, j, r, q, m;
        if (!p) {
            return
        }
        r = p.length;
        for (s = 0; s < r; s++) {
            n = this.worksheet.addRow();
            t = p.getAt(s).getCells();
            q = t.length;
            for (j = 0; j < q; j++) {
                m = t.getAt(j).getConfig();
                m.styleId = this.columnStylesNormalId[j];
                n.addCell(m)
            }
        }
    },
    buildHeader: function () {
        var u = this, w = {}, A = u.getData(), j, G, z, B, x, t, F, y, D, C, E;
        u.buildHeaderRows(A.getColumns(), w);
        j = Ext.Object.getKeys(w);
        x = j.length;
        for (z = 0; z < x; z++) {
            G = u.worksheet.addRow({height: 20.25, autoFitHeight: 1, styleId: u.tableHeaderStyleId});
            y = w[j[z]];
            t = y.length;
            for (B = 0; B < t; B++) {
                G.addCell(y[B]).setStyleId(u.tableHeaderStyleId)
            }
        }
        y = A.getBottomColumns();
        t = y.length;
        u.columnStylesNormal = [];
        u.columnStylesNormalId = [];
        u.columnStylesFooter = [];
        u.columnStylesFooterId = [];
        D = u.getGroupFooterStyle();
        for (B = 0; B < t; B++) {
            C = y[B];
            E = {style: C.getStyle(), width: C.getWidth()};
            F = Ext.applyIf({parentId: 0}, D);
            F = Ext.merge(F, E.style);
            u.columnStylesFooter.push(F);
            u.columnStylesFooterId.push(u.excel.addCellStyle(F));
            F = Ext.applyIf({parentId: 0}, E.style);
            u.columnStylesNormal.push(F);
            E.styleId = u.excel.addCellStyle(F);
            u.columnStylesNormalId.push(E.styleId);
            E.min = E.max = B + 1;
            E.style = null;
            if (E.width) {
                E.width = E.width / 10
            }
            u.worksheet.addColumn(E)
        }
    },
    buildHeaderRows: function (n, j) {
        var q, m, p, l, r;
        if (!n) {
            return
        }
        l = n.length;
        for (p = 0; p < l; p++) {
            q = n.getAt(p).getConfig();
            q.value = q.text;
            m = q.columns;
            delete (q.columns);
            delete (q.table);
            r = "s" + q.level;
            j[r] = j[r] || [];
            j[r].push(q);
            this.buildHeaderRows(m, j)
        }
    }
}, 0, 0, 0, 0, ["exporter.excel", "exporter.excel07", "exporter.xlsx"], 0, [Ext.exporter.excel, "Xlsx", Ext.exporter, "Excel"], 0));
(Ext.cmd.derive("Ext.exporter.Plugin", Ext.AbstractPlugin, {
    init: function (c) {
        var d = this;
        c.saveDocumentAs = Ext.bind(d.saveDocumentAs, d);
        c.getDocumentData = Ext.bind(d.getDocumentData, d);
        d.cmp = c;
        return Ext.plugin.Abstract.prototype.init.call(this, c)
    }, destroy: function () {
        var b = this;
        b.cmp.saveDocumentAs = b.cmp.getDocumentData = b.cmp = null;
        Ext.plugin.Abstract.prototype.destroy.call(this)
    }, saveDocumentAs: function (e) {
        var j = this.cmp, g = new Ext.Deferred(), h = this.getExporter(e);
        j.fireEvent("beforedocumentsave", j);
        Ext.asap(this.delayedSave, this, [h, e, g]);
        return g.promise
    }, delayedSave: function (h, e, g) {
        var j = this.cmp;
        if (this.disabled || !j) {
            Ext.destroy(h);
            g.reject();
            return
        }
        h.setData(this.prepareData(e));
        j.fireEvent("dataready", j, h.getData());
        h.saveAs().then(function () {
            g.resolve(e)
        }, function (a) {
            g.reject(a)
        }).always(function () {
            Ext.destroy(h);
            if (j) {
                j.fireEvent("documentsave", j)
            }
        })
    }, getDocumentData: function (d) {
        var g, e;
        if (this.disabled) {
            return
        }
        g = this.getExporter(d);
        e = g.getContent();
        Ext.destroy(g);
        return e
    }, getExporter: function (c) {
        var d = Ext.apply({type: "excel"}, c);
        return Ext.Factory.exporter(d)
    }, getExportStyle: function (m, h) {
        var n = (h && h.type), p, l, j;
        if (Ext.isArray(m)) {
            p = Ext.Array.pluck(m, "type");
            j = Ext.Array.indexOf(p, undefined);
            if (j >= 0) {
                l = m[j]
            }
            j = Ext.Array.indexOf(p, n);
            return j >= 0 ? m[j] : l
        } else {
            return m
        }
    }, prepareData: Ext.emptyFn
}, 0, 0, 0, 0, ["plugin.exporterplugin"], 0, [Ext.exporter, "Plugin"], 0));
(Ext.cmd.derive("Ext.exporter.file.excel.Worksheet", Ext.exporter.file.Base, {
    config: {
        name: "Sheet",
        protection: null,
        rightToLeft: null,
        showGridLines: true,
        tables: []
    },
    tpl: ['   <Worksheet ss:Name="{name:htmlEncode}"', '<tpl if="this.exists(protection)"> ss:Protected="{protection:this.toNumber}"</tpl>', '<tpl if="this.exists(rightToLeft)"> ss:RightToLeft="{rightToLeft:this.toNumber}"</tpl>', ">\n", '<tpl if="tables"><tpl for="tables.getRange()">{[values.render()]}</tpl></tpl>', '       <WorksheetOptions xmlns="urn:schemas-microsoft-com:office:excel">\n', "          <PageSetup>\n", '              <Layout x:CenterHorizontal="1" x:Orientation="Portrait" />\n', '              <Header x:Margin="0.3" />\n', '              <Footer x:Margin="0.3" x:Data="Page &amp;P of &amp;N" />\n', '              <PageMargins x:Bottom="0.75" x:Left="0.7" x:Right="0.7" x:Top="0.75" />\n', "          </PageSetup>\n", "          <FitToPage />\n", "          <Print>\n", "              <PrintErrors>Blank</PrintErrors>\n", "              <FitWidth>1</FitWidth>\n", "              <FitHeight>32767</FitHeight>\n", "              <ValidPrinterInfo />\n", "              <VerticalResolution>600</VerticalResolution>\n", "          </Print>\n", "          <Selected />\n", '<tpl if="!showGridLines">', "          <DoNotDisplayGridlines />\n", "</tpl>", "          <ProtectObjects>False</ProtectObjects>\n", "          <ProtectScenarios>False</ProtectScenarios>\n", "      </WorksheetOptions>\n", "   </Worksheet>\n", {
        exists: function (b) {
            return !Ext.isEmpty(b)
        }, toNumber: function (b) {
            return Number(Boolean(b))
        }
    }],
    destroy: function () {
        this.setTables(null);
        Ext.exporter.file.Base.prototype.destroy.call(this)
    },
    applyTables: function (c, d) {
        return this.checkCollection(c, d, "Ext.exporter.file.excel.Table")
    },
    addTable: function (b) {
        return this.getTables().add(b || {})
    },
    getTable: function (b) {
        return this.getTables().get(b)
    },
    applyName: function (b) {
        return Ext.String.ellipsis(String(b), 31)
    }
}, 0, 0, 0, 0, 0, 0, [Ext.exporter.file.excel, "Worksheet"], 0));
(Ext.cmd.derive("Ext.exporter.file.excel.Table", Ext.exporter.file.Base, {
    config: {
        expandedColumnCount: null,
        expandedRowCount: null,
        fullColumns: 1,
        fullRows: 1,
        defaultColumnWidth: 48,
        defaultRowHeight: 12.75,
        styleId: null,
        leftCell: 1,
        topCell: 1,
        columns: [],
        rows: []
    },
    tpl: ['       <Table x:FullColumns="{fullColumns}" x:FullRows="{fullRows}"', '<tpl if="this.exists(expandedRowCount)"> ss:ExpandedRowCount="{expandedRowCount}"</tpl>', '<tpl if="this.exists(expandedColumnCount)"> ss:ExpandedColumnCount="{expandedColumnCount}"</tpl>', '<tpl if="this.exists(defaultRowHeight)"> ss:DefaultRowHeight="{defaultRowHeight}"</tpl>', '<tpl if="this.exists(defaultColumnWidth)"> ss:DefaultColumnWidth="{defaultColumnWidth}"</tpl>', '<tpl if="this.exists(leftCell)"> ss:LeftCell="{leftCell}"</tpl>', '<tpl if="this.exists(topCell)"> ss:TopCell="{topCell}"</tpl>', '<tpl if="this.exists(styleId)"> ss:StyleID="{styleId}"</tpl>', ">\n", '<tpl if="columns"><tpl for="columns.getRange()">{[values.render()]}</tpl></tpl>', '<tpl if="rows">', '<tpl for="rows.getRange()">{[values.render()]}</tpl>', '<tpl else>         <Row ss:AutoFitHeight="0"/>\n</tpl>', "       </Table>\n", {
        exists: function (b) {
            return !Ext.isEmpty(b)
        }
    }],
    destroy: function () {
        this.setColumns(null);
        this.setRows(null);
        Ext.exporter.file.Base.prototype.destroy.call(this)
    },
    applyColumns: function (c, d) {
        return this.checkCollection(c, d, "Ext.exporter.file.excel.Column")
    },
    applyRows: function (c, d) {
        return this.checkCollection(c, d, "Ext.exporter.file.excel.Row")
    },
    addColumn: function (b) {
        return this.getColumns().add(b || {})
    },
    getColumn: function (b) {
        return this.getColumns().get(b)
    },
    addRow: function (b) {
        return this.getRows().add(b || {})
    },
    getRow: function (b) {
        return this.getRows().get(b)
    }
}, 0, 0, 0, 0, 0, 0, [Ext.exporter.file.excel, "Table"], 0));
(Ext.cmd.derive("Ext.exporter.file.excel.Style", Ext.exporter.file.Style, {
    config: {parentId: null, protection: null},
    checks: {
        alignment: {
            horizontal: ["Automatic", "Left", "Center", "Right", "Fill", "Justify", "CenterAcrossSelection", "Distributed", "JustifyDistributed"],
            shrinkToFit: [true, false],
            vertical: ["Automatic", "Top", "Bottom", "Center", "Justify", "Distributed", "JustifyDistributed"],
            verticalText: [true, false],
            wrapText: [true, false]
        },
        font: {
            family: ["Automatic", "Decorative", "Modern", "Roman", "Script", "Swiss"],
            outline: [true, false],
            shadow: [true, false],
            underline: ["None", "Single", "Double", "SingleAccounting", "DoubleAccounting"],
            verticalAlign: ["None", "Subscript", "Superscript"]
        },
        border: {
            position: ["Left", "Top", "Right", "Bottom", "DiagonalLeft", "DiagonalRight"],
            lineStyle: ["None", "Continuous", "Dash", "Dot", "DashDot", "DashDotDot", "SlantDashDot", "Double"],
            weight: [0, 1, 2, 3]
        },
        interior: {pattern: ["None", "Solid", "Gray75", "Gray50", "Gray25", "Gray125", "Gray0625", "HorzStripe", "VertStripe", "ReverseDiagStripe", "DiagStripe", "DiagCross", "ThickDiagCross", "ThinHorzStripe", "ThinVertStripe", "ThinReverseDiagStripe", "ThinDiagStripe", "ThinHorzCross", "ThinDiagCross"]},
        protection: {"protected": [true, false], hideFormula: [true, false]}
    },
    tpl: ['       <Style ss:ID="{id}"', '<tpl if="this.exists(parentId)"> ss:Parent="{parentId}"</tpl>', '<tpl if="this.exists(name)"> ss:Name="{name}"</tpl>', ">\n", '<tpl if="this.exists(alignment)">           <Alignment{[this.getAttributes(values.alignment, "alignment")]}/>\n</tpl>', '<tpl if="this.exists(borders)">', "           <Borders>\n", '<tpl for="borders">               <Border{[this.getAttributes(values, "border")]}/>\n</tpl>', "           </Borders>\n", "</tpl>", '<tpl if="this.exists(font)">           <Font{[this.getAttributes(values.font, "font")]}/>\n</tpl>', '<tpl if="this.exists(interior)">           <Interior{[this.getAttributes(values.interior, "interior")]}/>\n</tpl>', '<tpl if="this.exists(format)">           <NumberFormat ss:Format="{format}"/>\n</tpl>', '<tpl if="this.exists(protection)">           <Protection{[this.getAttributes(values.protection, "protection")]}/>\n</tpl>', "       </Style>\n", {
        exists: function (b) {
            return !Ext.isEmpty(b)
        }, getAttributes: function (p, n) {
            var r = ' ss:{0}="{1}"', q = Ext.Object.getKeys(p || {}), m = q.length, s = "", t, l;
            for (t = 0; t < m; t++) {
                l = q[t];
                s += Ext.String.format(r, Ext.String.capitalize(l), Ext.isBoolean(p[l]) ? Number(p[l]) : p[l])
            }
            return s
        }
    }]
}, 0, 0, 0, 0, 0, 0, [Ext.exporter.file.excel, "Style"], 0));
(Ext.cmd.derive("Ext.exporter.file.excel.Row", Ext.exporter.file.Base, {
    config: {
        autoFitHeight: false,
        caption: null,
        cells: [],
        height: null,
        index: null,
        span: null,
        styleId: null
    },
    tpl: ["           <Row", '<tpl if="this.exists(index)"> ss:Index="{index}"</tpl>', '<tpl if="this.exists(caption)"> c:Caption="{caption}"</tpl>', '<tpl if="this.exists(autoFitHeight)"> ss:AutoFitHeight="{autoFitHeight:this.toNumber}"</tpl>', '<tpl if="this.exists(span)"> ss:Span="{span}"</tpl>', '<tpl if="this.exists(height)"> ss:Height="{height}"</tpl>', '<tpl if="this.exists(styleId)"> ss:StyleID="{styleId}"</tpl>', ">\n", '<tpl if="cells"><tpl for="cells.getRange()">{[values.render()]}</tpl></tpl>', "           </Row>\n", {
        exists: function (b) {
            return !Ext.isEmpty(b)
        }, toNumber: function (b) {
            return Number(Boolean(b))
        }
    }],
    destroy: function () {
        this.setCells(null);
        Ext.exporter.file.Base.prototype.destroy.call(this)
    },
    applyCells: function (c, d) {
        return this.checkCollection(c, d, "Ext.exporter.file.excel.Cell")
    },
    addCell: function (b) {
        return this.getCells().add(b || {})
    },
    getCell: function (b) {
        return this.getCells().get(b)
    }
}, 0, 0, 0, 0, 0, 0, [Ext.exporter.file.excel, "Row"], 0));
(Ext.cmd.derive("Ext.exporter.file.excel.Column", Ext.exporter.file.Base, {
    config: {
        autoFitWidth: false,
        caption: null,
        hidden: null,
        index: null,
        span: null,
        styleId: null,
        width: null
    },
    tpl: ["<Column", '<tpl if="this.exists(index)"> ss:Index="{index}"</tpl>', '<tpl if="this.exists(caption)"> c:Caption="{caption}"</tpl>', '<tpl if="this.exists(styleId)"> ss:StyleID="{styleId}"</tpl>', '<tpl if="this.exists(hidden)"> ss:Hidden="{hidden}"</tpl>', '<tpl if="this.exists(span)"> ss:Span="{span}"</tpl>', '<tpl if="this.exists(width)"> ss:Width="{width}"</tpl>', '<tpl if="this.exists(autoFitWidth)"> ss:AutoFitWidth="{autoFitWidth:this.toNumber}"</tpl>', "/>\n", {
        exists: function (b) {
            return !Ext.isEmpty(b)
        }, toNumber: function (b) {
            return Number(Boolean(b))
        }
    }]
}, 0, 0, 0, 0, 0, 0, [Ext.exporter.file.excel, "Column"], 0));
(Ext.cmd.derive("Ext.exporter.file.excel.Cell", Ext.exporter.file.Base, {
    config: {
        dataType: "String",
        formula: null,
        index: null,
        styleId: null,
        mergeAcross: null,
        mergeDown: null,
        value: ""
    },
    tpl: ["               <Cell", '<tpl if="this.exists(index)"> ss:Index="{index}"</tpl>', '<tpl if="this.exists(styleId)"> ss:StyleID="{styleId}"</tpl>', '<tpl if="this.exists(mergeAcross)"> ss:MergeAcross="{mergeAcross}"</tpl>', '<tpl if="this.exists(mergeDown)"> ss:MergeDown="{mergeDown}"</tpl>', '<tpl if="this.exists(formula)"> ss:Formula="{formula}"</tpl>', ">\n", '                   <Data ss:Type="{dataType}">{value}</Data>\n', "               </Cell>\n", {
        exists: function (b) {
            return !Ext.isEmpty(b)
        }
    }],
    applyValue: function (e) {
        var d = "String", g = Ext.util.Format;
        if (e instanceof Date) {
            d = "DateTime";
            e = Ext.Date.format(e, "Y-m-d\\TH:i:s.u")
        } else {
            if (Ext.isNumber(e)) {
                d = "Number"
            } else {
                if (Ext.isBoolean(e)) {
                    d = "Boolean"
                } else {
                    e = g.htmlEncode(g.htmlDecode(e))
                }
            }
        }
        this.setDataType(d);
        return e
    }
}, 0, 0, 0, 0, 0, 0, [Ext.exporter.file.excel, "Cell"], 0));
(Ext.cmd.derive("Ext.exporter.file.excel.Workbook", Ext.exporter.file.Base, {
    config: {
        title: "Workbook",
        author: "Sencha",
        windowHeight: 9000,
        windowWidth: 50000,
        protectStructure: false,
        protectWindows: false,
        styles: [],
        worksheets: []
    },
    tpl: ['<?xml version="1.0" encoding="utf-8"?>\n', '<?mso-application progid="Excel.Sheet"?>\n', "<Workbook ", 'xmlns="urn:schemas-microsoft-com:office:spreadsheet" ', 'xmlns:o="urn:schemas-microsoft-com:office:office" ', 'xmlns:x="urn:schemas-microsoft-com:office:excel" ', 'xmlns:ss="urn:schemas-microsoft-com:office:spreadsheet" ', 'xmlns:html="http://www.w3.org/TR/REC-html40">\n', '   <DocumentProperties xmlns="urn:schemas-microsoft-com:office:office">\n', "       <Title>{title:htmlEncode}</Title>\n", "       <Author>{author:htmlEncode}</Author>\n", "       <Created>{createdAt}</Created>\n", "   </DocumentProperties>\n", '   <ExcelWorkbook xmlns="urn:schemas-microsoft-com:office:excel">\n', "       <WindowHeight>{windowHeight}</WindowHeight>\n", "       <WindowWidth>{windowWidth}</WindowWidth>\n", "       <ProtectStructure>{protectStructure}</ProtectStructure>\n", "       <ProtectWindows>{protectWindows}</ProtectWindows>\n", "   </ExcelWorkbook>\n", "   <Styles>\n", '<tpl if="styles"><tpl for="styles.getRange()">{[values.render()]}</tpl></tpl>', "   </Styles>\n", '<tpl if="worksheets"><tpl for="worksheets.getRange()">{[values.render()]}</tpl></tpl>', "</Workbook>"],
    destroy: function () {
        this.setStyles(null);
        this.setWorksheets(null);
        Ext.exporter.file.Base.prototype.destroy.call(this)
    },
    applyStyles: function (c, d) {
        return this.checkCollection(c, d, "Ext.exporter.file.excel.Style")
    },
    applyWorksheets: function (c, d) {
        return this.checkCollection(c, d, "Ext.exporter.file.excel.Worksheet")
    },
    addStyle: function (b) {
        return this.getStyles().add(b || {})
    },
    getStyle: function (b) {
        return this.getStyles().get(b)
    },
    addWorksheet: function (b) {
        return this.getWorksheets().add(b || {})
    },
    getWorksheet: function (b) {
        return this.getWorksheets().get(b)
    }
}, 0, 0, 0, 0, 0, 0, [Ext.exporter.file.excel, "Workbook"], 0));
(Ext.cmd.derive("Ext.exporter.excel.Xml", Ext.exporter.Base, {
    config: {
        windowHeight: 9000,
        windowWidth: 50000,
        protectStructure: false,
        protectWindows: false,
        defaultStyle: {
            alignment: {vertical: "Top"},
            font: {fontName: "Calibri", family: "Swiss", size: 11, color: "#000000"}
        },
        titleStyle: {
            name: "Title",
            parentId: "Default",
            alignment: {horizontal: "Center", vertical: "Center"},
            font: {fontName: "Cambria", family: "Swiss", size: 18, color: "#1F497D"}
        },
        groupHeaderStyle: {
            name: "Group Header",
            parentId: "Default",
            borders: [{position: "Bottom", lineStyle: "Continuous", weight: 1, color: "#4F81BD"}]
        },
        groupFooterStyle: {borders: [{position: "Top", lineStyle: "Continuous", weight: 1, color: "#4F81BD"}]},
        tableHeaderStyle: {
            name: "Heading 1",
            parentId: "Default",
            alignment: {horizontal: "Center", vertical: "Center"},
            borders: [{position: "Bottom", lineStyle: "Continuous", weight: 1, color: "#4F81BD"}],
            font: {fontName: "Calibri", family: "Swiss", size: 11, color: "#1F497D"}
        }
    }, fileName: "export.xml", mimeType: "application/vnd.ms-excel", destroy: function () {
        var b = this;
        b.workbook = b.table = b.columnStylesFooter = b.columnStylesNormal = Ext.destroy(b.workbook);
        Ext.exporter.Base.prototype.destroy.call(this)
    }, applyDefaultStyle: function (b) {
        return Ext.applyIf({id: "Default", name: "Normal"}, b || {})
    }, getContent: function () {
        var e = this, g = this.getConfig(), j = g.data, h;
        e.workbook = new Ext.exporter.file.excel.Workbook({
            title: g.title,
            author: g.author,
            windowHeight: g.windowHeight,
            windowWidth: g.windowWidth,
            protectStructure: g.protectStructure,
            protectWindows: g.protectWindows
        });
        e.table = e.workbook.addWorksheet({name: g.title}).addTable();
        e.workbook.addStyle(g.defaultStyle);
        e.tableHeaderStyleId = e.workbook.addStyle(g.tableHeaderStyle).getId();
        e.groupHeaderStyleId = e.workbook.addStyle(g.groupHeaderStyle).getId();
        h = j ? j.getColumnCount() : 1;
        e.addTitle(g, h);
        if (j) {
            e.buildHeader();
            e.buildRows(j.getGroups(), h, 0)
        }
        return e.workbook.render()
    }, addTitle: function (d, c) {
        if (!Ext.isEmpty(d.title)) {
            this.table.addRow({
                autoFitHeight: 1,
                height: 22.5,
                styleId: this.workbook.addStyle(d.titleStyle).getId()
            }).addCell({mergeAcross: c - 1, value: d.title})
        }
    }, buildRows: function (F, H, G) {
        var j = this, B = j.getShowSummary(), A, I, L, K, J, C, D, E, g, y, M, z;
        if (!F) {
            return
        }
        L = j.workbook.addStyle({parentId: j.groupHeaderStyleId, alignment: {Indent: G > 0 ? G - 1 : 0}}).getId();
        K = j.workbook.addStyle({parentId: j.columnStylesFooter[0], alignment: {Indent: G > 0 ? G - 1 : 0}}).getId();
        g = F.length;
        for (C = 0; C < g; C++) {
            A = F.getAt(C).getConfig();
            z = (!A.groups && !A.rows);
            if (B !== false && !Ext.isEmpty(A.text) && !z) {
                j.table.addRow().addCell({mergeAcross: H - 1, value: A.text, styleId: L})
            }
            j.buildRows(A.groups, H, G + 1);
            j.buildGroupRows(A.rows);
            if (A.summaries && (B || z)) {
                y = A.summaries.length;
                for (E = 0; E < y; E++) {
                    I = j.table.addRow();
                    J = A.summaries.getAt(E).getCells();
                    M = J.length;
                    for (D = 0; D < M; D++) {
                        I.addCell(J.getAt(D).getConfig()).setStyleId(z ? j.columnStylesNormal[D] : (D === 0 ? K : j.columnStylesFooter[D]))
                    }
                }
            }
        }
    }, buildGroupRows: function (p) {
        var n, t, s, j, r, q, m;
        if (!p) {
            return
        }
        r = p.length;
        for (s = 0; s < r; s++) {
            n = this.table.addRow();
            t = p.getAt(s).getCells();
            q = t.length;
            for (j = 0; j < q; j++) {
                m = t.getAt(j).getConfig();
                m.styleId = this.columnStylesNormal[j];
                n.addCell(m)
            }
        }
    }, buildHeader: function () {
        var t = this, u = {}, z = t.getData(), r, j, y, A, w, s, C, x, B;
        t.buildHeaderRows(z.getColumns(), u);
        r = Ext.Object.getKeys(u);
        w = r.length;
        for (y = 0; y < w; y++) {
            j = t.table.addRow({height: 20.25, autoFitHeight: 1});
            x = u[r[y]];
            s = x.length;
            for (A = 0; A < s; A++) {
                j.addCell(x[A]).setStyleId(t.tableHeaderStyleId)
            }
        }
        x = z.getBottomColumns();
        s = x.length;
        t.columnStylesNormal = [];
        t.columnStylesFooter = [];
        B = t.getGroupFooterStyle();
        for (A = 0; A < s; A++) {
            C = Ext.applyIf({parentId: "Default"}, B);
            C = Ext.merge(C, x[A].getStyle());
            C.id = null;
            t.columnStylesFooter.push(t.workbook.addStyle(C).getId());
            C = Ext.merge({parentId: "Default"}, x[A].getStyle());
            t.columnStylesNormal.push(t.workbook.addStyle(C).getId())
        }
    }, buildHeaderRows: function (n, j) {
        var q, m, p, l, r;
        if (!n) {
            return
        }
        l = n.length;
        for (p = 0; p < l; p++) {
            q = n.getAt(p).getConfig();
            q.value = q.text;
            m = q.columns;
            delete (q.columns);
            delete (q.table);
            r = "s" + q.level;
            j[r] = j[r] || [];
            j[r].push(q);
            this.buildHeaderRows(m, j)
        }
    }
}, 0, 0, 0, 0, ["exporter.excel03"], 0, [Ext.exporter.excel, "Xml"], 0));
(Ext.cmd.derive("Ext.exporter.file.html.Style", Ext.exporter.file.Style, {
    mappings: {
        readingOrder: {
            LeftToRight: "ltr",
            RightToLeft: "rtl",
            Context: "initial",
            Automatic: "initial"
        },
        horizontal: {Automatic: "initial", Left: "left", Center: "center", Right: "right", Justify: "justify"},
        vertical: {Top: "top", Bottom: "bottom", Center: "middle", Automatic: "baseline"},
        lineStyle: {None: "none", Continuous: "solid", Dash: "dashed", Dot: "dotted"}
    }, render: function () {
        var s = this.getConfig(), z = this.mappings, p = "", r = s.alignment, w = s.font, q = s.borders, x = s.interior,
            t, y, A, u;
        if (r) {
            if (r.horizontal) {
                p += "text-align: " + z.horizontal[r.horizontal] + ";\n"
            }
            if (r.readingOrder) {
                p += "direction: " + z.readingOrder[r.readingOrder] + ";\n"
            }
            if (r.vertical) {
                p += "vertical-align: " + z.vertical[r.vertical] + ";\n"
            }
            if (r.indent) {
                p += "padding-left: " + r.indent + "px;\n"
            }
        }
        if (w) {
            if (w.size) {
                p += "font-size: " + w.size + "px;\n"
            }
            if (w.bold) {
                p += "font-weight: bold;\n"
            }
            if (w.italic) {
                p += "font-style: italic;\n"
            }
            if (w.strikeThrough) {
                p += "text-decoration: line-through;\n"
            }
            if (w.underline === "Single") {
                p += "text-decoration: underline;\n"
            }
            if (w.color) {
                p += "color: " + w.color + ";\n"
            }
        }
        if (x && x.color) {
            p += "background-color: " + x.color + ";\n"
        }
        if (q) {
            y = q.length;
            for (t = 0; t < y; t++) {
                u = q[t];
                A = "border-" + u.position.toLowerCase();
                p += A + "-width: " + (u.weight || 0) + "px;\n";
                p += A + "-style: " + (z.lineStyle[u.lineStyle] || "initial") + ";\n";
                p += A + "-color: " + (u.color || "initial") + ";\n"
            }
        }
        return s.name + "{\n" + p + "}\n"
    }
}, 0, 0, 0, 0, 0, 0, [Ext.exporter.file.html, "Style"], 0));
(Ext.cmd.derive("Ext.exporter.file.html.Doc", Ext.exporter.file.Base, {
    config: {
        title: "Title",
        author: "Sencha",
        charset: "UTF-8",
        styles: [],
        table: null
    }, destroy: function () {
        this.setStyles(null);
        this.setTable(null);
        Ext.exporter.file.Base.prototype.destroy.call(this)
    }, applyStyles: function (c, d) {
        return this.checkCollection(c, d, "Ext.exporter.file.html.Style")
    }, addStyle: function (b) {
        return this.getStyles().add(b || {})
    }, getStyle: function (b) {
        return this.getStyles().get(b)
    }
}, 0, 0, 0, 0, 0, 0, [Ext.exporter.file.html, "Doc"], 0));
(Ext.cmd.derive("Ext.exporter.text.CSV", Ext.exporter.Base, {
    fileName: "export.csv", getHelper: function () {
        return Ext.util.CSV
    }, getContent: function () {
        var d = this, e = [], g = d.getData();
        if (g) {
            d.buildHeader(e);
            d.buildRows(g.getGroups(), e, g.getColumnCount());
            d.columnStyles = Ext.destroy(d.columnStyles)
        }
        return d.getHelper().encode(e)
    }, buildHeader: function (l) {
        var p = this, s = {}, n = p.getData(), m, t, r, q;
        p.buildHeaderRows(n.getColumns(), s);
        l.push.apply(l, Ext.Object.getValues(s));
        m = n.getBottomColumns();
        t = m.length;
        p.columnStyles = [];
        for (r = 0; r < t; r++) {
            q = m[r].getStyle() || {};
            if (!q.id) {
                q.id = "c" + r
            }
            q.name = "." + q.id;
            p.columnStyles.push(new Ext.exporter.file.Style(q))
        }
    }, buildHeaderRows: function (u, j) {
        var w, s, r, x, q, p, t, y;
        if (!u) {
            return
        }
        r = u.length;
        for (s = 0; s < r; s++) {
            w = u.getAt(s);
            q = w.getMergeAcross();
            p = w.getMergeDown();
            y = w.getLevel();
            x = "s" + y;
            j[x] = j[x] || [];
            j[x].push(w.getText());
            for (t = 1; t <= q; t++) {
                j[x].push("")
            }
            for (t = 1; t <= p; t++) {
                x = "s" + (y + t);
                j[x] = j[x] || [];
                j[x].push("")
            }
            this.buildHeaderRows(w.getColumns(), j)
        }
    }, buildRows: function (D, G, E) {
        var z = this.getShowSummary(), r, A, I, g, B, K, C, M, H, J, j, L, F;
        if (!D) {
            return
        }
        g = D.length;
        for (A = 0; A < g; A++) {
            r = D.getAt(A).getConfig();
            j = (!r.groups && !r.rows);
            if (!Ext.isEmpty(r.text) && !j) {
                I = [];
                I.length = E;
                I[r.level || 0] = r.text;
                G.push(I)
            }
            this.buildRows(r.groups, G, E);
            if (r.rows) {
                K = r.rows.length;
                for (B = 0; B < K; B++) {
                    I = [];
                    H = r.rows.getAt(B);
                    J = H.getCells();
                    M = J.length;
                    for (C = 0; C < M; C++) {
                        L = J.getAt(C).getConfig();
                        F = this.columnStyles[C];
                        L = F ? F.getFormattedValue(L.value) : L.value;
                        I.push(L)
                    }
                    G.push(I)
                }
            }
            if (r.summaries && (z || j)) {
                K = r.summaries.length;
                for (B = 0; B < K; B++) {
                    I = [];
                    H = r.summaries.getAt(B);
                    J = H.getCells();
                    M = J.length;
                    for (C = 0; C < M; C++) {
                        L = J.getAt(C).getConfig();
                        F = this.columnStyles[C];
                        L = F ? F.getFormattedValue(L.value) : L.value;
                        I.push(L)
                    }
                    G.push(I)
                }
            }
        }
    }
}, 0, 0, 0, 0, ["exporter.csv"], 0, [Ext.exporter.text, "CSV"], 0));
(Ext.cmd.derive("Ext.exporter.text.Html", Ext.exporter.Base, {
    config: {
        tpl: ["<!DOCTYPE html>\n", "<html>\n", "   <head>\n", '       <meta charset="{charset}">\n', "       <title>{title}</title>\n", "       <style>\n", "       table { border-collapse: collapse; border-spacing: 0; }\n", '<tpl if="styles"><tpl for="styles.getRange()">{[values.render()]}</tpl></tpl>', "       </style>\n", "   </head>\n", "   <body>\n", "       <h1>{title}</h1>\n", "       <table>\n", "           <thead>\n", '<tpl for="table.columns">', "               <tr>\n", '<tpl for=".">', '                   <th<tpl if="width"> width="{width}"</tpl><tpl if="mergeAcross"> colSpan="{mergeAcross}"</tpl><tpl if="mergeDown"> rowSpan="{mergeDown}"</tpl>>{text}</th>\n', "</tpl>", "               </tr>\n", "</tpl>", "           </thead>\n", "           <tbody>\n", '<tpl for="table.rows">', '               <tr<tpl if="cls"> class="{cls}"</tpl>>\n', '<tpl for="cells">', '                   <td<tpl if="cls"> class="{cls}"</tpl><tpl if="mergeAcross"> colSpan="{mergeAcross}"</tpl><tpl if="mergeDown"> rowSpan="{mergeDown}"</tpl>>{value}</td>\n', "</tpl>", "               </tr>\n", "</tpl>", "           </tbody>\n", "           <tfoot>\n", "               <tr>\n", '                   <th<tpl if="table.columnsCount"> colSpan="{table.columnsCount}"</tpl>>&nbsp;</th>\n', "               </tr>\n", "           </tfoot>\n", "       </table>\n", "   </body>\n", "</html>"],
        defaultStyle: {
            name: "table tbody td, table thead th",
            alignment: {vertical: "Top"},
            font: {fontName: "Arial", size: 12, color: "#000000"},
            borders: [{position: "Left", lineStyle: "Continuous", weight: 1, color: "#4F81BD"}, {
                position: "Right",
                lineStyle: "Continuous",
                weight: 1,
                color: "#4F81BD"
            }]
        },
        titleStyle: {name: "h1", font: {fontName: "Arial", size: 18, color: "#1F497D"}},
        groupHeaderStyle: {
            name: ".groupHeader td",
            borders: [{position: "Top", lineStyle: "Continuous", weight: 1, color: "#4F81BD"}, {
                position: "Bottom",
                lineStyle: "Continuous",
                weight: 1,
                color: "#4F81BD"
            }]
        },
        groupFooterStyle: {
            name: ".groupFooter td",
            borders: [{position: "Bottom", lineStyle: "Continuous", weight: 1, color: "#4F81BD"}]
        },
        tableHeaderStyle: {
            name: "table thead th",
            alignment: {horizontal: "Center", vertical: "Center"},
            borders: [{position: "Top", lineStyle: "Continuous", weight: 1, color: "#4F81BD"}, {
                position: "Bottom",
                lineStyle: "Continuous",
                weight: 1,
                color: "#4F81BD"
            }],
            font: {fontName: "Arial", size: 12, color: "#1F497D"}
        },
        tableFooterStyle: {
            name: "table tfoot th",
            borders: [{position: "Top", lineStyle: "Continuous", weight: 1, color: "#4F81BD"}]
        }
    }, fileName: "export.html", mimeType: "text/html", getContent: function () {
        var n = this, j = n.getConfig(), m = j.data, p = {columnsCount: 0, columns: [], rows: []}, l, h;
        n.doc = new Ext.exporter.file.html.Doc({
            title: j.title,
            author: j.author,
            tpl: j.tpl,
            styles: [j.defaultStyle, j.titleStyle, j.groupHeaderStyle, j.groupFooterStyle, j.tableHeaderStyle, j.tableFooterStyle]
        });
        if (m) {
            l = m.getColumnCount();
            Ext.apply(p, {
                columnsCount: m.getColumnCount(),
                columns: n.buildHeader(),
                rows: n.buildRows(m.getGroups(), l, 0)
            })
        }
        n.doc.setTable(p);
        h = n.doc.render();
        n.doc = n.columnStyles = Ext.destroy(n.doc);
        return h
    }, buildRows: function (g, H, Q) {
        var G = this, M = G.getShowSummary(), I = [], r, L, C, D, E, J, P, j, O, F, N, K, B;
        if (g) {
            G.doc.addStyle({
                name: ".levelHeader" + Q,
                alignment: {Horizontal: "Left", Indent: (Q > 0 ? Q - 1 : 0) * 5}
            });
            G.doc.addStyle({
                name: ".levelFooter" + Q,
                alignment: {Horizontal: "Left", Indent: (Q > 0 ? Q - 1 : 0) * 5}
            });
            J = g.length;
            for (C = 0; C < J; C++) {
                r = g.getAt(C).getConfig();
                K = (!r.groups && !r.rows);
                if (!Ext.isEmpty(r.text) && !K) {
                    I.push({cls: "groupHeader", cells: [{value: r.text, mergeAcross: H, cls: "levelHeader" + Q}]})
                }
                I = Ext.Array.merge(I, G.buildRows(r.groups, H, Q + 1));
                if (r.rows) {
                    P = r.rows.length;
                    for (D = 0; D < P; D++) {
                        L = [];
                        F = r.rows.getAt(D);
                        N = F.getCells();
                        j = N.length;
                        for (E = 0; E < j; E++) {
                            O = N.getAt(E).getConfig();
                            B = G.columnStyles[E];
                            if (B) {
                                O.cls = B.getId();
                                O.value = B.getFormattedValue(O.value)
                            }
                            L.push(O)
                        }
                        I.push({cells: L})
                    }
                }
                if (r.summaries && (M || K)) {
                    P = r.summaries.length;
                    for (D = 0; D < P; D++) {
                        L = [];
                        F = r.summaries.getAt(D);
                        N = F.getCells();
                        j = N.length;
                        for (E = 0; E < j; E++) {
                            O = N.getAt(E).getConfig();
                            B = G.columnStyles[E];
                            O.cls = (E === 0 ? "levelFooter" + Q : "");
                            if (B) {
                                O.cls += " " + B.getId();
                                O.value = B.getFormattedValue(O.value)
                            }
                            L.push(O)
                        }
                        I.push({cls: "groupFooter" + (K ? " groupHeader" : ""), cells: L})
                    }
                }
            }
        }
        return I
    }, buildHeader: function () {
        var n = this, r = {}, m = n.getData(), l, j, q, p;
        n.buildHeaderRows(m.getColumns(), r);
        l = m.getBottomColumns();
        j = l.length;
        n.columnStyles = [];
        for (q = 0; q < j; q++) {
            p = l[q].getStyle() || {};
            if (!p.id) {
                p.id = Ext.id()
            }
            p.name = "." + p.id;
            n.columnStyles.push(n.doc.addStyle(p))
        }
        return Ext.Object.getValues(r)
    }, buildHeaderRows: function (n, j) {
        var q, p, l, r, m;
        if (!n) {
            return
        }
        l = n.length;
        for (p = 0; p < l; p++) {
            q = n.getAt(p).getConfig();
            r = "s" + q.level;
            j[r] = j[r] || [];
            if (q.mergeAcross) {
                q.mergeAcross++
            }
            if (q.mergeDown) {
                q.mergeDown++
            }
            j[r].push(q);
            this.buildHeaderRows(q.columns, j)
        }
    }
}, 0, 0, 0, 0, ["exporter.html"], 0, [Ext.exporter.text, "Html"], 0));
(Ext.cmd.derive("Ext.exporter.text.TSV", Ext.exporter.text.CSV, {
    getHelper: function () {
        return Ext.util.TSV
    }
}, 0, 0, 0, 0, ["exporter.tsv"], 0, [Ext.exporter.text, "TSV"], 0));
(Ext.cmd.derive("Ext.grid.plugin.Exporter", Ext.exporter.Plugin, {
    lockableScope: "top", prepareData: function (j) {
        var n = this, h = n.cmp, p = new Ext.exporter.data.Table(), l, m;
        m = p.addGroup({text: ""});
        n.extractGroups(m, h.getColumnManager().getColumns());
        if (h.lockedGrid) {
            l = Ext.Array.merge(n.getColumnHeaders(h.lockedGrid.headerCt.items, j), n.getColumnHeaders(h.normalGrid.headerCt.items, j))
        } else {
            l = n.getColumnHeaders(h.headerCt.items, j)
        }
        p.addGroup(m);
        p.setColumns(l);
        return p
    }, getColumnHeaders: function (n, h) {
        var l = [], p, m, j;
        for (p = 0; p < n.length; p++) {
            j = n.get(p);
            if (!j.ignoreExport) {
                m = {text: j.text, width: j.getWidth(), style: this.getExportStyle(j.exportStyle, h)};
                if (j.isGroupHeader) {
                    m.columns = this.getColumnHeaders(j.items, h);
                    if (m.columns.length === 0) {
                        m = null
                    }
                }
                if (m) {
                    l.push(m)
                }
            }
        }
        return l
    }, extractGroups: function (r, A) {
        var t = this.cmp.getStore(), w = t.getCount(), B = A.length, y, z, x, j, C, u, s;
        for (y = 0; y < w; y++) {
            x = t.getAt(y);
            j = r.addRow();
            for (z = 0; z < B; z++) {
                C = A[z];
                if (!C.ignoreExport) {
                    u = !Ext.isEmpty(C.initialConfig.formatter) && Ext.isEmpty(C.formatter) && !C.exportStyle && (C.exportStyle && !C.exportStyle.format);
                    s = x.get(C.dataIndex) || "";
                    j.addCell({value: u ? C.renderer(s) : s})
                }
            }
        }
        return r
    }
}, 0, 0, 0, 0, ["plugin.gridexporter"], 0, [Ext.grid.plugin, "Exporter"], 0));