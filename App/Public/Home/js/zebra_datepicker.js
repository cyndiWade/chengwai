﻿(function(c) {
	c.Zebra_DatePicker = function($, F) {
		var na = {
			always_show_clear: !1,
			always_visible: !1,
			days: "日 一 二 三 四 五 六".split(" "),
			days_abbr: !1,
			direction: 0,
			disabled_dates: !1,
			enabled_dates: !1,
			first_day_of_week: 1,
			format: "Y-m-d",
			inside: !0,
			lang_clear_date: "清空",
			months: "1月 2月 3月 4月 5月 6月 7月 8月 9月 10月 11月 12月".split(" "),
			months_abbr: !1,
			offset: [5, -5],
			pair: !1,
			readonly_element: !0,
			select_other_months: !1,
			show_icon: !0,
			show_other_months: !0,
			show_week_number: !1,
			start_date: !1,
			view: "days",
			weekend_days: [0, 6],
			zero_pad: !1,
			onChange: null,
			onClear: null,
			onSelect: null
		}, u, q, g, B, D, G, H, Q, aa, V, da, r, t, z, w, n, W, J, K, R, E, X, v, y, Y, L, S, ga, ha, ia, A, ea, a = this;
		a.settings = {};
		var m = c($),
			ka = function(d) {
				d || (a.settings = c.extend({}, na, F));
				a.settings.readonly_element && m.attr("readonly", "readonly");
				var b = {
					days: ["d", "j", "D"],
					months: ["F", "m", "M", "n", "t"],
					years: ["o", "Y", "y"]
				}, h = !1,
					f = !1,
					k = !1;
				for (type in b) c.each(b[type], function(c, b) {
						-1 < a.settings.format.indexOf(b) && ("days" == type ? h = !0 : "months" == type ? f = !0 : "years" == type && (k = !0))
					});
				A = h && f && k ? ["years", "months", "days"] : !h && f && k ? ["years", "months"] : !h && !f && k ? ["years"] : !h && f && !k ? ["months"] : ["years", "months", "days"]; - 1 == c.inArray(a.settings.view, A) && (a.settings.view = A[A.length - 1]);
				E = [];
				R = [];
				for (var l = 0; 2 > l; l++) b = 0 == l ? a.settings.disabled_dates : a.settings.enabled_dates, c.isArray(b) && 0 < b.length && c.each(b, function() {
						for (var a = this.split(" "), b = 0; 4 > b; b++) {
							a[b] || (a[b] = "*");
							a[b] = -1 < a[b].indexOf(",") ? a[b].split(",") : Array(a[b]);
							for (var d = 0; d < a[b].length; d++) if (-1 < a[b][d].indexOf("-")) {
									var f = a[b][d].match(/^([0-9]+)\-([0-9]+)/);
									if (null != f) {
										for (var e = p(f[1]); e <= p(f[2]); e++) - 1 == c.inArray(e, a[b]) && a[b].push(e + "");
										a[b].splice(d, 1)
									}
								}
							for (d = 0; d < a[b].length; d++) a[b][d] = isNaN(p(a[b][d])) ? a[b][d] : p(a[b][d])
						}
						0 == l ? E.push(a) : R.push(a)
					});
				var b = new Date,
					e = !a.settings.reference_date ? m.data("zdp_reference_date") && void 0 != m.data("zdp_reference_date") ? m.data("zdp_reference_date") : b : a.settings.reference_date,
					j, s;
				y = v = void 0;
				r = e.getMonth();
				aa = b.getMonth();
				t = e.getFullYear();
				V = b.getFullYear();
				z = e.getDate();
				da = b.getDate();
				if (!0 === a.settings.direction) v = e;
				else if (!1 === a.settings.direction) y = e, S = y.getMonth(), L = y.getFullYear(), Y = y.getDate();
				else if (!c.isArray(a.settings.direction) && T(a.settings.direction) && 0 < p(a.settings.direction) || c.isArray(a.settings.direction) && ((j = Z(a.settings.direction[0])) || !0 === a.settings.direction[0] || T(a.settings.direction[0]) && 0 < a.settings.direction[0]) && ((s = Z(a.settings.direction[1])) || !1 === a.settings.direction[1] || T(a.settings.direction[1]) && 0 <= a.settings.direction[1])) v = j ? j : new Date(t, r, z + (!c.isArray(a.settings.direction) ? p(a.settings.direction) : p(!0 === a.settings.direction[0] ? 0 : a.settings.direction[0]))), r = v.getMonth(), t = v.getFullYear(), z = v.getDate(), s && +s >= +v ? y = s : !s && (!1 !== a.settings.direction[1] && c.isArray(a.settings.direction)) && (y = new Date(t, r, z + p(a.settings.direction[1]))), y && (S = y.getMonth(), L = y.getFullYear(), Y = y.getDate());
				else if (!c.isArray(a.settings.direction) && T(a.settings.direction) && 0 > p(a.settings.direction) || c.isArray(a.settings.direction) && (!1 === a.settings.direction[0] || T(a.settings.direction[0]) && 0 > a.settings.direction[0]) && ((j = Z(a.settings.direction[1])) || T(a.settings.direction[1]) && 0 <= a.settings.direction[1])) y = new Date(t, r, z + (!c.isArray(a.settings.direction) ? p(a.settings.direction) : p(!1 === a.settings.direction[0] ? 0 : a.settings.direction[0]))), S = y.getMonth(), L = y.getFullYear(), Y = y.getDate(), j && +j < +y ? v = j : !j && c.isArray(a.settings.direction) && (v = new Date(L, S, Y - p(a.settings.direction[1]))), v && (r = v.getMonth(), t = v.getFullYear(), z = v.getDate());
				else if (c.isArray(a.settings.disabled_dates) && 0 < a.settings.disabled_dates.length) for (var U in E) if ("*" == E[U][0] && "*" == E[U][1] && "*" == E[U][2] && "*" == E[U][3]) {
							var ba = [];
							c.each(R, function() {
								"*" != this[2][0] && ba.push(parseInt(this[2][0] + ("*" == this[1][0] ? "12" : x(this[1][0], 2)) + ("*" == this[0][0] ? "*" == this[1][0] ? "31" : (new Date(this[2][0], this[1][0], 0)).getDate() : x(this[0][0], 2)), 10))
							});
							ba.sort();
							if (0 < ba.length) {
								var M = (ba[0] + "").match(/([0-9]{4})([0-9]{2})([0-9]{2})/);
								t = parseInt(M[1], 10);
								r = parseInt(M[2], 10) - 1;
								z = parseInt(M[3], 10)
							}
							break
						}
				if (C(t, r, z)) {
					for (; C(t);) v ? (t++, r = 0) : (t--, r = 11);
					for (; C(t, r);) v ? (r++, z = 1) : (r--, z = (new Date(t, r + 1, 0)).getDate()), 11 < r ? (t++, r = 0, z = 1) : 0 > r && (t--, r = 11, z = (new Date(t, r + 1, 0)).getDate());
					for (; C(t, r, z);) v ? z++ : z--;
					b = new Date(t, r, z);
					t = b.getFullYear();
					r = b.getMonth();
					z = b.getDate()
				}(j = Z(m.val() || (a.settings.start_date ? a.settings.start_date : ""))) && C(j.getFullYear(), j.getMonth(), j.getDate()) && m.val("");
				ja(j);
				if (!a.settings.always_visible && (d || (a.settings.show_icon ? (j = '<button type="button" class="Zebra_DatePicker_Icon' + ("disabled" == m.attr("disabled") ? " Zebra_DatePicker_Icon_Disabled" : "") + '"></button>', g = c(j), a.icon = g, ea = g.add(m)) : ea = m, ea.bind("click", function(b) {
					b.preventDefault();
					m.attr("disabled") || ("none" != q.css("display") ? a.hide() : a.show())
				}), void 0 != g && g.insertAfter($)), void 0 != g)) {
					g.attr("style", "");
					a.settings.inside && g.addClass("Zebra_DatePicker_Icon_Inside");
					j = m.position();
					s = m.outerWidth(!1);
					U = m.outerHeight(!1);
					var b = parseInt(m.css("marginTop"), 10) || 0,
						e = parseInt(m.css("marginRight"), 10) || 0,
						N = g.position(),
						oa = g.outerWidth(!0),
						pa = g.outerHeight(!0),
						qa = parseInt(g.css("marginLeft"), 10) || 0;
					a.settings.inside ? g.css({
						marginLeft: (N.left <= j.left + s ? j.left + s - N.left : 0) - (e + oa)
					}) : g.css({
						marginLeft: (N.left <= j.left + s ? j.left + s - N.left : 0) - e + qa
					});
					g.css({
						marginRight: -parseInt(g.css("marginLeft"), 10)
					});
					g.css({
						marginTop: (N.top > j.top ? j.top - N.top : N.top - j.top) + b + (U - pa) / 2
					})
				}
				void 0 != g && (m.is(":visible") ? g.show() : g.hide());
				d || (j = '<div class="Zebra_DatePicker"><table class="dp_header"><tr><td class="dp_previous">&#171;</td><td class="dp_caption">&#032;</td><td class="dp_next">&#187;</td></tr></table><table class="dp_daypicker"></table><table class="dp_monthpicker"></table><table class="dp_yearpicker"></table><table class="dp_footer"><tr><td>' + a.settings.lang_clear_date + "</td></tr></table></div>", q = c(j), a.datepicker = q, B = c("table.dp_header", q), D = c("table.dp_daypicker", q), G = c("table.dp_monthpicker", q), H = c("table.dp_yearpicker", q), Q = c("table.dp_footer", q), a.settings.always_visible ? m.attr("disabled") || (a.settings.always_visible.append(q), a.show()) : c("body").append(q), q.delegate("td:not(.dp_disabled, .dp_weekend_disabled, .dp_not_in_month, .dp_blocked, .dp_week_number)", "mouseover", function() {
					c(this).addClass("dp_hover")
				}).delegate("td:not(.dp_disabled, .dp_weekend_disabled, .dp_not_in_month, .dp_blocked, .dp_week_number)", "mouseout", function() {
					c(this).removeClass("dp_hover")
				}), d = c("td", B), "firefox" == O.name ? d.css("MozUserSelect", "none") : "explorer" == O.name ? d.bind("selectstart", function() {
					return !1
				}) : d.mousedown(function() {
					return !1
				}), c(".dp_previous", B).bind("click", function() {
					c(this).hasClass("dp_blocked") || ("months" == u ? n-- : "years" == u ? n -= 12 : 0 > --w && (w = 11, n--), P())
				}), c(".dp_caption", B).bind("click", function() {
					u = "days" == u ? -1 < c.inArray("months", A) ? "months" : -1 < c.inArray("years", A) ? "years" : "days" : "months" == u ? -1 < c.inArray("years", A) ? "years" : -1 < c.inArray("days", A) ? "days" : "months" : -1 < c.inArray("days", A) ? "days" : -1 < c.inArray("months", A) ? "months" : "years";
					P()
				}), c(".dp_next", B).bind("click", function() {
					c(this).hasClass("dp_blocked") || ("months" == u ? n++ : "years" == u ? n += 12 : 12 == ++w && (w = 0, n++), P())
				}), D.delegate("td:not(.dp_disabled, .dp_weekend_disabled, .dp_not_in_month, .dp_week_number)", "click", function() {
					a.settings.select_other_months && null != (M = c(this).attr("class").match(/date\_([0-9]{4})(0[1-9]|1[012])(0[1-9]|[12][0-9]|3[01])/)) ? ca(M[1], M[2], M[3], "days", c(this)) : ca(n, w, p(c(this).html()), "days", c(this))
				}), G.delegate("td:not(.dp_disabled)", "click", function() {
					var b = c(this).attr("class").match(/dp\_month\_([0-9]+)/);
					w = p(b[1]); - 1 == c.inArray("days", A) ? ca(n, w, 1, "months", c(this)) : (u = "days", a.settings.always_visible && m.val(""), P())
				}), H.delegate("td:not(.dp_disabled)", "click", function() {
					n = p(c(this).html()); - 1 == c.inArray("months", A) ? ca(n, 1, 1, "years", c(this)) : (u = "months", a.settings.always_visible && m.val(""), P())
				}), c("td", Q).bind("click", function(b) {
					b.preventDefault();
					m.val("");
					a.settings.always_visible || (n = w = K = J = W = null, Q.css("display", "none"));
					a.hide();
					if (a.settings.onClear && "function" == typeof a.settings.onClear) a.settings.onClear(m)
				}), a.settings.always_visible || c(document).bind({
					mousedown: a._mousedown,
					keyup: a._keyup
				}), P());
			};

		a.hide = function() {
			a.settings.always_visible || (la("hide"), q.css("display", "none"))
		};
		a.show = function() {
			u = a.settings.view;
			var d = Z(m.val() || (a.settings.start_date ? a.settings.start_date : ""));
			d ? (J = d.getMonth(), w = d.getMonth(), K = d.getFullYear(), n = d.getFullYear(), W = d.getDate(), C(K, J, W) && (m.val(""), w = r, n = t)) : (w = r, n = t);
			P();
			if (a.settings.always_visible) q.css("display", "block");
			else {
				var d = q.outerWidth(),
					b = q.outerHeight(),
					h = (void 0 != g ? g.offset().left + g.outerWidth(!0) : m.offset().left + m.outerWidth(!0)) + a.settings.offset[0],
					f = (void 0 != g ? g.offset().top : m.offset().top) - b + a.settings.offset[1],
					k = c(window).width(),
					l = c(window).height(),
					e = c(window).scrollTop(),
					j = c(window).scrollLeft();
				h + d > j + k && (h = j + k - d);
				h < j && (h = j);
				f + b > e + l && (f = e + l - b);
				f < e && (f = e);
				q.css({
					left: 0,
					top: 15 + 'px'
				});
				q.fadeIn("explorer" == O.name && 9 > O.version ? 0 : 150, "linear");
				la()
			}
		};
		a.update = function(d) {
			a.original_direction && (a.original_direction = a.direction);
			a.settings = c.extend(a.settings, d);
			ka(!0)
		};
		var Z = function(d) {
			d += "";
			if ("" != c.trim(d)) {
				for (var b = a.settings.format.replace(/([-.,*+?^${}()|[\]\/\\])/g, "\\$1"), h = "dDjlNSwFmMnYy".split(""), f = [], k = [], l = 0; l < h.length; l++) - 1 < (position = b.indexOf(h[l])) && f.push({
					character: h[l],
					position: position
				});
				f.sort(function(a, b) {
					return a.position - b.position
				});
				c.each(f, function(a, b) {
					switch (b.character) {
						case "d":
							k.push("0[1-9]|[12][0-9]|3[01]");
							break;
						case "D":
							k.push("[a-z]{3}");
							break;
						case "j":
							k.push("[1-9]|[12][0-9]|3[01]");
							break;
						case "l":
							k.push("[a-z]+");
							break;
						case "N":
							k.push("[1-7]");
							break;
						case "S":
							k.push("st|nd|rd|th");
							break;
						case "w":
							k.push("[0-6]");
							break;
						case "F":
							k.push("[a-z]+");
							break;
						case "m":
							k.push("0[1-9]|1[012]+");
							break;
						case "M":
							k.push("[a-z]{3}");
							break;
						case "n":
							k.push("[1-9]|1[012]");
							break;
						case "Y":
							k.push("[0-9]{4}");
							break;
						case "y":
							k.push("[0-9]{2}")
					}
				});
				if (k.length && (f.reverse(), c.each(f, function(a, d) {
					b = b.replace(d.character, "(" + k[k.length - a - 1] + ")")
				}), k = RegExp("^" + b + "$", "ig"), segments = k.exec(d))) {
					d = new Date;
					var e = d.getDate(),
						j = d.getMonth() + 1,
						s = d.getFullYear(),
						n = "日 一 二 三 四 五 六".split(" "),
						m = "1月 2月 3月 4月 5月 6月 7月 8月 9月 10月 11月 12月".split(" "),
						q, g = !0;
					f.reverse();
					c.each(f, function(b, d) {
						if (!g) return !0;
						switch (d.character) {
							case "m":
							case "n":
								j = p(segments[b + 1]);
								break;
							case "d":
							case "j":
								e = p(segments[b + 1]);
								break;
							case "D":
							case "l":
							case "F":
							case "M":
								q = "D" == d.character || "l" == d.character ? a.settings.days : a.settings.months;
								g = !1;
								c.each(q, function(a, c) {
									if (g) return !0;
									if (segments[b + 1].toLowerCase() == c.substring(0, "D" == d.character || "M" == d.character ? 3 : c.length).toLowerCase()) {
										switch (d.character) {
											case "D":
												segments[b + 1] = n[a].substring(0, 3);
												break;
											case "l":
												segments[b + 1] = n[a];
												break;
											case "F":
												segments[b + 1] = m[a];
												j = a + 1;
												break;
											case "M":
												segments[b + 1] = m[a].substring(0, 3), j = a + 1
										}
										g = !0
									}
								});
								break;
							case "Y":
								s = p(segments[b + 1]);
								break;
							case "y":
								s = "19" + p(segments[b + 1])
						}
					});
					if (g && (f = new Date(s, (j || 1) - 1, e || 1), f.getFullYear() == s && f.getDate() == (e || 1) && f.getMonth() == (j || 1) - 1)) return f
				}
				return !1
			}
		}, ma = function() {
				var d = (new Date(n, w + 1, 0)).getDate(),
					b = (new Date(n, w, 1)).getDay(),
					h = (new Date(n, w, 0)).getDate(),
					b = b - a.settings.first_day_of_week,
					b = 0 > b ? 7 + b : b;
				fa(n+"年 "+a.settings.months[w]);
				var f = "<tr>";
				a.settings.show_week_number && (f += "<th>" + a.settings.show_week_number + "</th>");
				for (var k = 0; 7 > k; k++) f += "<th>" + (c.isArray(a.settings.days_abbr) && void 0 != a.settings.days_abbr[(a.settings.first_day_of_week + k) % 7] ? a.settings.days_abbr[(a.settings.first_day_of_week + k) % 7] : a.settings.days[(a.settings.first_day_of_week + k) % 7].substr(0, 2)) + "</th>";
				f += "</tr><tr>";
				for (k = 0; 42 > k; k++) {
					0 < k && 0 == k % 7 && (f += "</tr><tr>");
					if (0 == k % 7 && a.settings.show_week_number) {
						var l = new Date(n, w, k - b + 1),
							e = l.getFullYear(),
							j = l.getMonth() + 1,
							l = l.getDate(),
							s = void 0,
							m = void 0,
							g = void 0,
							p = g = void 0,
							q = void 0,
							g = m = s = void 0;
						3 > j ? (s = e - 1, m = (s / 4 | 0) - (s / 100 | 0) + (s / 400 | 0), g = ((s - 1) / 4 | 0) - ((s - 1) / 100 | 0) + ((s - 1) / 400 | 0), g = m - g, p = 0, q = l - 1 + 31 * (j - 1)) : (s = e, m = (s / 4 | 0) - (s / 100 | 0) + (s / 400 | 0), g = ((s - 1) / 4 | 0) - ((s - 1) / 100 | 0) + ((s - 1) / 400 | 0), g = m - g, p = g + 1, q = l + ((153 * (j - 3) + 2) / 5 | 0) + 58 + g);
						s = (s + m) % 7;
						l = (q + s - p) % 7;
						m = q + 3 - l;
						g = 0 > m ? 53 - ((s - g) / 5 | 0) : m > 364 + g ? 1 : (m / 7 | 0) + 1;
						f += '<td class="dp_week_number">' + g + "</td>"
					}
					e = k - b + 1;
					if (a.settings.select_other_months && (k < b || e > d)) var r = new Date(n, w, e),
					t = r.getFullYear(), u = r.getMonth(), v = r.getDate(), r = t + x(u, 2) + x(v, 2);
					k < b ? f += '<td class="' + (a.settings.select_other_months && !C(t, u, v) ? "dp_not_in_month_selectable date_" + r : "dp_not_in_month") + '">' + (a.settings.select_other_months || a.settings.show_other_months ? x(h - b + k + 1, a.settings.zero_pad ? 2 : 0) : "&#032;") + "</td>" : e > d ? f += '<td class="' + (a.settings.select_other_months && !C(t, u, v) ? "dp_not_in_month_selectable date_" + r : "dp_not_in_month") + '">' + (a.settings.select_other_months || a.settings.show_other_months ? x(e - d, a.settings.zero_pad ? 2 : 0) : "&#032;") + "</td>" : (j = (a.settings.first_day_of_week + k) % 7, l = "", C(n, w, e) ? (l = -1 < c.inArray(j, a.settings.weekend_days) ? "dp_weekend_disabled" : l + " dp_disabled", w == aa && (n == V && da == e) && (l += " dp_disabled_current")) : (-1 < c.inArray(j, a.settings.weekend_days) && (l = "dp_weekend"), w == J && (n == K && W == e) && (l += " dp_selected"), w == aa && (n == V && da == e) && (l += " dp_current")), f += "<td" + ("" != l ? ' class="' + c.trim(l) + '"' : "") + ">" + (a.settings.zero_pad ? x(e, 2) : e) + "</td>")
				}
				D.html(c(f + "</tr>"));
				a.settings.always_visible && (ga = c("td:not(.dp_disabled, .dp_weekend_disabled, .dp_not_in_month, .dp_blocked, .dp_week_number)", D));
				D.css("display", "")
			}, la = function(a) {
				if ("explorer" == O.name && 6 == O.version) {
					if (!X) {
						var b = p(q.css("zIndex")) - 1;
						X = jQuery("<iframe>", {
							src: 'javascript:document.write("")',
							scrolling: "no",
							frameborder: 0,
							allowtransparency: "true",
							css: {
								zIndex: b,
								position: "absolute",
								top: 0,
								left: 0,
								width: q.outerWidth(),
								height: q.outerHeight(),
								filter: "progid:DXImageTransform.Microsoft.Alpha(opacity=0)",
								display: "none"
							}
						});
						c("body").append(X)
					}
					switch (a) {
						case "hide":
							X.css("display", "none");
							break;
						default:
							a = q.offset(), X.css({
								top: a.top,
								left: a.left,
								display: "block"
							})
					}
				}
			}, C = function(d, b, h) {
				if ((void 0 == d || isNaN(d)) && (void 0 == b || isNaN(b)) && (void 0 == h || isNaN(h))) return !1;
				if (c.isArray(a.settings.direction) || 0 !== p(a.settings.direction)) {
					var f = p(I(d, "undefined" != typeof b ? x(b, 2) : "", "undefined" != typeof h ? x(h, 2) : "")),
						k = (f + "").length;
					if (8 == k && ("undefined" != typeof v && f < p(I(t, x(r, 2), x(z, 2))) || "undefined" != typeof y && f > p(I(L, x(S, 2), x(Y, 2)))) || 6 == k && ("undefined" != typeof v && f < p(I(t, x(r, 2))) || "undefined" != typeof y && f > p(I(L, x(S, 2)))) || 4 == k && ("undefined" != typeof v && f < t || "undefined" != typeof y && f > L)) return !0
				}
				"undefined" != typeof b && (b += 1);
				var l = !1,
					e = !1;
				E && c.each(E, function() {
					if (!l && (-1 < c.inArray(d, this[2]) || -1 < c.inArray("*", this[2]))) if ("undefined" != typeof b && -1 < c.inArray(b, this[1]) || -1 < c.inArray("*", this[1])) if ("undefined" != typeof h && -1 < c.inArray(h, this[0]) || -1 < c.inArray("*", this[0])) {
								if ("*" == this[3]) return l = !0;
								var a = (new Date(d, b - 1, h)).getDay();
								if (-1 < c.inArray(a, this[3])) return l = !0
							}
				});
				R && c.each(R, function() {
					if (!e && (-1 < c.inArray(d, this[2]) || -1 < c.inArray("*", this[2]))) if (e = !0, "undefined" != typeof b) if (e = !0, -1 < c.inArray(b, this[1]) || -1 < c.inArray("*", this[1])) {
								if ("undefined" != typeof h) {
									e = !0;
									if (-1 < c.inArray(h, this[0]) || -1 < c.inArray("*", this[0])) {
										if ("*" == this[3]) return e = !0;
										var a = (new Date(d, b - 1, h)).getDay();
										if (-1 < c.inArray(a, this[3])) return e = !0
									}
									e = !1
								}
							} else e = !1
				});
				return (!R || !e) && E && l ? !0 : !1
			}, T = function(a) {
				return (a + "").match(/^\-?[0-9]+$/) ? !0 : !1
			}, fa = function(d) {
				c(".dp_caption", B).html(d);
				if (c.isArray(a.settings.direction) || 0 !== p(a.settings.direction) || 1 == A.length && "months" == A[0]) {
					d = n;
					var b = w,
						h, f;
					"days" == u ? (f = 0 > b - 1 ? I(d - 1, "11") : I(d, x(b - 1, 2)), h = 11 < b + 1 ? I(d + 1, "00") : I(d, x(b + 1, 2))) : "months" == u ? (f = d - 1, h = d + 1) : "years" == u && (f = d - 7, h = d + 7);
					1 == A.length && "months" == A[0] || C(f) ? (c(".dp_previous", B).addClass("dp_blocked"), c(".dp_previous", B).removeClass("dp_hover")) : c(".dp_previous", B).removeClass("dp_blocked");
					1 == A.length && "months" == A[0] || C(h) ? (c(".dp_next", B).addClass("dp_blocked"), c(".dp_next", B).removeClass("dp_hover")) : c(".dp_next", B).removeClass("dp_blocked")
				}
			}, P = function() {
				if ("" == D.text() || "days" == u) {
					if ("" == D.text()) {
						a.settings.always_visible || q.css("left", -1E3);
						q.css({
							display: "block"
						});
						ma();
						var d = D.outerWidth(),
							b = D.outerHeight();
						B.css("width", d);
						G.css({
							width: d,
							height: b
						});
						H.css({
							width: d,
							height: b
						});
						Q.css("width", d);
						q.css({
							display: "none"
						})
					} else ma();
					G.css("display", "none");
					H.css("display", "none")
				} else if ("months" == u) {
					fa(n);
					d = "<tr>";
					for (b = 0; 12 > b; b++) {
						0 < b && 0 == b % 3 && (d += "</tr><tr>");
						var h = "dp_month_" + b;
						C(n, b) ? h += " dp_disabled" : !1 !== J && J == b ? h += " dp_selected" : aa == b && V == n && (h += " dp_current");
						d += '<td class="' + c.trim(h) + '">' + (c.isArray(a.settings.months_abbr) && void 0 != a.settings.months_abbr[b] ? a.settings.months_abbr[b] : a.settings.months[b].substr(0, 3)) + "</td>"
					}
					G.html(c(d + "</tr>"));
					a.settings.always_visible && (ha = c("td:not(.dp_disabled)", G));
					G.css("display", "");
					D.css("display", "none");
					H.css("display", "none")
				} else if ("years" == u) {
					fa(n - 7 + " - " + (n + 4));
					d = "<tr>";
					for (b = 0; 12 > b; b++) 0 < b && 0 == b % 3 && (d += "</tr><tr>"), h = "", C(n - 7 + b) ? h += " dp_disabled" : K && K == n - 7 + b ? h += " dp_selected" : V == n - 7 + b && (h += " dp_current"), d += "<td" + ("" != c.trim(h) ? ' class="' + c.trim(h) + '"' : "") + ">" + (n - 7 + b) + "</td>";
					H.html(c(d + "</tr>"));
					a.settings.always_visible && (ia = c("td:not(.dp_disabled)", H));
					H.css("display", "");
					D.css("display", "none");
					G.css("display", "none")
				}
				a.settings.onChange && ("function" == typeof a.settings.onChange && void 0 != u) && (d = "days" == u ? D.find("td:not(.dp_disabled, .dp_weekend_disabled, .dp_not_in_month, .dp_blocked)") : "months" == u ? G.find("td:not(.dp_disabled, .dp_weekend_disabled, .dp_not_in_month, .dp_blocked)") : H.find("td:not(.dp_disabled, .dp_weekend_disabled, .dp_not_in_month, .dp_blocked)"), d.each(function() {
					if ("days" == u) c(this).data("date", n + "-" + x(w + 1, 2) + "-" + x(p(c(this).text()), 2));
					else if ("months" == u) {
						var a = c(this).attr("class").match(/dp\_month\_([0-9]+)/);
						c(this).data("date", n + "-" + x(p(a[1]) + 1, 2))
					} else c(this).data("date", p(c(this).text()))
				}), a.settings.onChange(u, d, m));
				a.settings.always_show_clear || a.settings.always_visible || "" != m.val() ? Q.css("display", "") : Q.css("display", "none")
			}, ca = function(d, b, h, f, k) {
				var l = new Date(d, b, h, 12, 0, 0);
				f = "days" == f ? ga : "months" == f ? ha : ia;
				var e;
				e = "";
				for (var j = l.getDate(), g = l.getDay(), q = a.settings.days[g], p = l.getMonth() + 1, r = a.settings.months[p - 1], t = l.getFullYear() + "", u = 0; u < a.settings.format.length; u++) {
					var v = a.settings.format.charAt(u);
					switch (v) {
						case "y":
							t = t.substr(2);
						case "Y":
							e += t;
							break;
						case "m":
							p = x(p, 2);
						case "n":
							e += p;
							break;
						case "M":
							r = c.isArray(a.settings.months_abbr) && void 0 != a.settings.months_abbr[p - 1] ? a.settings.months_abbr[p - 1] : a.settings.months[p - 1].substr(0, 3);
						case "F":
							e += r;
							break;
						case "d":
							j = x(j, 2);
						case "j":
							e += j;
							break;
						case "D":
							q = c.isArray(a.settings.days_abbr) && void 0 != a.settings.days_abbr[g] ? a.settings.days_abbr[g] : a.settings.days[g].substr(0, 3);
						case "l":
							e += q;
							break;
						case "N":
							g++;
						case "w":
							e += g;
							break;
						case "S":
							e = 1 == j % 10 && "11" != j ? e + "st" : 2 == j % 10 && "12" != j ? e + "nd" : 3 == j % 10 && "13" != j ? e + "rd" : e + "th";
							break;
						default:
							e += v
					}
				}
				m.val(e);
				a.settings.always_visible && (J = l.getMonth(), w = l.getMonth(), K = l.getFullYear(), n = l.getFullYear(), W = l.getDate(), f.removeClass("dp_selected"), k.addClass("dp_selected"));
				a.hide();
				ja(l);
				if (a.settings.onSelect && "function" == typeof a.settings.onSelect) a.settings.onSelect(e, d + "-" + x(b + 1, 2) + "-" + x(h, 2), l, m);
				m.focus()
			}, I = function() {
				for (var a = "", b = 0; b < arguments.length; b++) a += arguments[b] + "";
				return a
			}, x = function(a, b) {
				for (a += ""; a.length < b;) a = "0" + a;
				return a
			}, p = function(a) {
				return parseInt(a, 10)
			}, ja = function(d) {
				a.settings.pair && c.each(a.settings.pair, function() {
					var a = c(this);
					!a.data || !a.data("Zebra_DatePicker") ? a.data("zdp_reference_date", d) : (a = a.data("Zebra_DatePicker"), a.update({
						reference_date: d,
						direction: 0 == a.settings.direction ? 1 : a.settings.direction
					}), a.settings.always_visible && a.show())
				})
			};
		a._keyup = function(c) {
			("block" == q.css("display") || 27 == c.which) && a.hide();
			return !0
		};
		a._mousedown = function(d) {
			if ("block" == q.css("display")) {
				if (a.settings.show_icon && c(d.target).get(0) === g.get(0)) return !0;
				0 == c(d.target).parents().filter(".Zebra_DatePicker").length && a.hide()
			}
			return !0
		};
		var O = {
			init: function() {
				this.name = this.searchString(this.dataBrowser) || "";
				this.version = this.searchVersion(navigator.userAgent) || this.searchVersion(navigator.appVersion) || ""
			},
			searchString: function(a) {
				for (var b = 0; b < a.length; b++) {
					var c = a[b].string,
						f = a[b].prop;
					this.versionSearchString = a[b].versionSearch || a[b].identity;
					if (c) {
						if (-1 != c.indexOf(a[b].subString)) return a[b].identity
					} else if (f) return a[b].identity
				}
			},
			searchVersion: function(a) {
				var b = a.indexOf(this.versionSearchString);
				if (-1 != b) return parseFloat(a.substring(b + this.versionSearchString.length + 1))
			},
			dataBrowser: [{
					string: navigator.userAgent,
					subString: "Firefox",
					identity: "firefox"
				}, {
					string: navigator.userAgent,
					subString: "MSIE",
					identity: "explorer",
					versionSearch: "MSIE"
				}
			]
		};
		O.init();
		ka()
	};
	c.fn.Zebra_DatePicker = function($) {
		return this.each(function() {
			if (void 0 != c(this).data("Zebra_DatePicker")) {
				var F = c(this).data("Zebra_DatePicker");
				void 0 != F.icon && F.icon.remove();
				F.datepicker.remove();
				c(document).unbind("keyup", F._keyup);
				c(document).unbind("mousedown", F._mousedown)
			}
			F = new c.Zebra_DatePicker(this, $);
			c(this).data("Zebra_DatePicker", F)
		})
	}
})(jQuery);