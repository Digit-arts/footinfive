(function($) {
	var browser = $.browser;
	if (!browser) {
		function uaMatch(e) {
			e = e.toLowerCase();
			var t = /(chrome)[ \/]([\w.]+)/.exec(e)
					|| /(webkit)[ \/]([\w.]+)/.exec(e)
					|| /(opera)(?:.*version|)[ \/]([\w.]+)/.exec(e)
					|| /(msie) ([\w.]+)/.exec(e) || e.indexOf("compatible") < 0
					&& /(mozilla)(?:.*? rv:([\w.]+)|)/.exec(e) || [];
			return {
				browser : t[1] || "",
				version : t[2] || "0"
			}
		}
		var matched = uaMatch(navigator.userAgent);
		browser = {};
		if (matched.browser) {
			browser[matched.browser] = true;
			browser.version = matched.version
		}
		if (browser.chrome) {
			browser.webkit = true
		} else if (browser.webkit) {
			browser.safari = true
		}
	}
	if (typeof $.support.selectstart != "function") {
		$.support.selectstart = "onselectstart" in document
				.createElement("div")
	}
	if (typeof $.fn.disableSelection != "function") {
		$.fn.disableSelection = function() {
			return this.bind(($.support.selectstart ? "selectstart"
					: "mousedown")
					+ ".ui-disableSelection", function(e) {
				e.preventDefault()
			})
		}
	}
	$.addFlex = function(t, p) {
		if (t.grid)
			return false;
		p = $
				.extend(
						{
							height : 200,
							width : "auto",
							striped : true,
							novstripe : false,
							minwidth : 30,
							minheight : 80,
							resizable : true,
							url : false,
							method : "POST",
							dataType : "xml",
							errormsg : "Connection Error",
							usepager : false,
							nowrap : true,
							page : 1,
							total : 1,
							useRp : true,
							rp : 15,
							rpOptions : [ 10, 15, 20, 30, 50, 100 ],
							title : false,
							idProperty : "id",
							pagestat : "Affiche de {from} &agrave; {to} sur {total} enregistrements",
							pagetext : "Page",
							outof : "sur",
							findtext : "Chercher",
							params : [],
							procmsg : "Récupération des données...",
							query : "",
							qtype : "",
							nomsg : "Aucun enregistrement",
							minColToggle : 1,
							showToggleBtn : true,
							hideOnSubmit : true,
							autoload : true,
							blockOpacity : .5,
							preProcess : false,
							addTitleToCell : false,
							dblClickResize : false,
							onDragCol : false,
							onToggleCol : false,
							onChangeSort : false,
							onDoubleClick : false,
							onSuccess : false,
							onError : false,
							onSubmit : false,
							__mw : {
								datacol : function(e, t, n) {
									var r = typeof e.datacol[t] == "function" ? e.datacol[t]
											(n)
											: n;
									if (typeof e.datacol["*"] == "function") {
										return e.datacol["*"](r)
									} else {
										return r
									}
								}
							},
							getGridClass : function(e) {
								return e
							},
							datacol : {},
							colResize : true,
							colMove : true
						}, p);
		$(t).show().attr({
			cellPadding : 0,
			cellSpacing : 0,
			border : 0
		}).removeAttr("width");
		var g = {
			hset : {},
			rePosDrag : function() {
				var e = 0 - this.hDiv.scrollLeft;
				if (this.hDiv.scrollLeft > 0)
					e -= Math.floor(p.cgwidth / 2);
				$(g.cDrag).css({
					top : g.hDiv.offsetTop + 1
				});
				var t = this.cdpad;
				var n = 0;
				$("div", g.cDrag).hide();
				$("thead tr:first th:visible", this.hDiv).each(function() {
					var r = $("thead tr:first th:visible", g.hDiv).index(this);
					var i = parseInt($("div", this).width());
					if (e == 0)
						e -= Math.floor(p.cgwidth / 2);
					i = i + e + t;
					if (isNaN(i)) {
						i = 0
					}
					$("div:eq(" + r + ")", g.cDrag).css({
						left : (!browser.mozilla ? i - n : i) + "px"
					}).show();
					e = i;
					n++
				})
			},
			fixHeight : function(e) {
				e = false;
				if (!e)
					e = $(g.bDiv).height();
				var t = $(this.hDiv).height();
				$("div", this.cDrag).each(function() {
					$(this).height(e + t)
				});
				var n = parseInt($(g.nDiv).height(), 10);
				if (n > e)
					$(g.nDiv).height(e).width(200);
				else
					$(g.nDiv).height("auto").width("auto");
				$(g.block).css({
					height : e,
					marginBottom : e * -1
				});
				var r = g.bDiv.offsetTop + e;
				if (p.height != "auto" && p.resizable)
					r = g.vDiv.offsetTop;
				$(g.rDiv).css({
					height : r
				})
			},
			dragStart : function(e, t, n) {
				if (e == "colresize" && p.colResize === true) {
					$(g.nDiv).hide();
					$(g.nBtn).hide();
					var r = $("div", this.cDrag).index(n);
					var i = $("th:visible div:eq(" + r + ")", this.hDiv)
							.width();
					$(n).addClass("dragging").siblings().hide();
					$(n).prev().addClass("dragging").show();
					this.colresize = {
						startX : t.pageX,
						ol : parseInt(n.style.left, 10),
						ow : i,
						n : r
					};
					$("body").css("cursor", "col-resize")
				} else if (e == "vresize") {
					var s = false;
					$("body").css("cursor", "row-resize");
					if (n) {
						s = true;
						$("body").css("cursor", "col-resize")
					}
					this.vresize = {
						h : p.height,
						sy : t.pageY,
						w : p.width,
						sx : t.pageX,
						hgo : s
					}
				} else if (e == "colMove") {
					$(t.target).disableSelection();
					if (p.colMove === true) {
						$(g.nDiv).hide();
						$(g.nBtn).hide();
						this.hset = $(this.hDiv).offset();
						this.hset.right = this.hset.left
								+ $("table", this.hDiv).width();
						this.hset.bottom = this.hset.top
								+ $("table", this.hDiv).height();
						this.dcol = n;
						this.dcoln = $("th", this.hDiv).index(n);
						this.colCopy = document.createElement("div");
						this.colCopy.className = "colCopy";
						this.colCopy.innerHTML = n.innerHTML;
						if (browser.msie) {
							this.colCopy.className = "colCopy ie"
						}
						$(this.colCopy).css({
							position : "absolute",
							"float" : "left",
							display : "none",
							textAlign : n.align
						});
						$("body").append(this.colCopy);
						$(this.cDrag).hide()
					}
				}
				$("body").noSelect()
			},
			dragMove : function(e) {
				if (this.colresize) {
					var t = this.colresize.n;
					var n = e.pageX - this.colresize.startX;
					var r = this.colresize.ol + n;
					var i = this.colresize.ow + n;
					if (i > p.minwidth) {
						$("div:eq(" + t + ")", this.cDrag).css("left", r);
						this.colresize.nw = i
					}
				} else if (this.vresize) {
					var s = this.vresize;
					var o = e.pageY;
					var n = o - s.sy;
					if (!p.defwidth)
						p.defwidth = p.width;
					if (p.width != "auto" && !p.nohresize && s.hgo) {
						var u = e.pageX;
						var a = u - s.sx;
						var f = s.w + a;
						if (f > p.defwidth) {
							this.gDiv.style.width = f + "px";
							p.width = f
						}
					}
					var l = s.h + n;
					if ((l > p.minheight || p.height < p.minheight) && !s.hgo) {
						this.bDiv.style.height = l + "px";
						p.height = l;
						this.fixHeight(l)
					}
					s = null
				} else if (this.colCopy) {
					$(this.dcol).addClass("thMove").removeClass("thOver");
					if (e.pageX > this.hset.right || e.pageX < this.hset.left
							|| e.pageY > this.hset.bottom
							|| e.pageY < this.hset.top) {
						$("body").css("cursor", "move")
					} else {
						$("body").css("cursor", "pointer")
					}
					$(this.colCopy).css({
						top : e.pageY + 10,
						left : e.pageX + 20,
						display : "block"
					})
				}
			},
			dragEnd : function() {
				if (this.colresize) {
					var e = this.colresize.n;
					var t = this.colresize.nw;
					$("th:visible div:eq(" + e + ")", this.hDiv)
							.css("width", t);
					$("tr", this.bDiv).each(function() {
						var n = $("td:visible div:eq(" + e + ")", this);
						n.css("width", t);
						g.addTitleToCell(n)
					});
					this.hDiv.scrollLeft = this.bDiv.scrollLeft;
					$("div:eq(" + e + ")", this.cDrag).siblings().show();
					$(".dragging", this.cDrag).removeClass("dragging");
					this.rePosDrag();
					this.fixHeight();
					this.colresize = false;
					if ($.cookies) {
						var n = p.colModel[e].name;
						$.cookie("flexiwidths/" + n, t)
					}
				} else if (this.vresize) {
					this.vresize = false
				} else if (this.colCopy) {
					$(this.colCopy).remove();
					if (this.dcolt !== null) {
						if (this.dcoln > this.dcolt)
							$("th:eq(" + this.dcolt + ")", this.hDiv).before(
									this.dcol);
						else
							$("th:eq(" + this.dcolt + ")", this.hDiv).after(
									this.dcol);
						this.switchCol(this.dcoln, this.dcolt);
						$(this.cdropleft).remove();
						$(this.cdropright).remove();
						this.rePosDrag();
						if (p.onDragCol) {
							p.onDragCol(this.dcoln, this.dcolt)
						}
					}
					this.dcol = null;
					this.hset = null;
					this.dcoln = null;
					this.dcolt = null;
					this.colCopy = null;
					$(".thMove", this.hDiv).removeClass("thMove");
					$(this.cDrag).show()
				}
				$("body").css("cursor", "default");
				$("body").noSelect(false)
			},
			toggleCol : function(e, n) {
				var r = $("th[axis='col" + e + "']", this.hDiv)[0];
				var i = $("thead th", g.hDiv).index(r);
				var s = $("input[value=" + e + "]", g.nDiv)[0];
				if (n == null) {
					n = r.hidden
				}
				if ($("input:checked", g.nDiv).length < p.minColToggle && !n) {
					return false
				}
				if (n) {
					r.hidden = false;
					$(r).show();
					s.checked = true
				} else {
					r.hidden = true;
					$(r).hide();
					s.checked = false
				}
				$("tbody tr", t).each(function() {
					if (n) {
						$("td:eq(" + i + ")", this).show()
					} else {
						$("td:eq(" + i + ")", this).hide()
					}
				});
				this.rePosDrag();
				if (p.onToggleCol) {
					p.onToggleCol(e, n)
				}
				return n
			},
			switchCol : function(e, n) {
				$("tbody tr", t).each(
						function() {
							if (e > n)
								$("td:eq(" + n + ")", this).before(
										$("td:eq(" + e + ")", this));
							else
								$("td:eq(" + n + ")", this).after(
										$("td:eq(" + e + ")", this))
						});
				if (e > n) {
					$("tr:eq(" + n + ")", this.nDiv).before(
							$("tr:eq(" + e + ")", this.nDiv))
				} else {
					$("tr:eq(" + n + ")", this.nDiv).after(
							$("tr:eq(" + e + ")", this.nDiv))
				}
				if (browser.msie && browser.version < 7) {
					$("tr:eq(" + n + ") input", this.nDiv)[0].checked = true
				}
				this.hDiv.scrollLeft = this.bDiv.scrollLeft
			},
			scroll : function() {
				this.hDiv.scrollLeft = this.bDiv.scrollLeft;
				this.rePosDrag()
			},
			addData : function(e) {
				if (p.dataType == "json") {
					e = $.extend({
						rows : [],
						page : 0,
						total : 0
					}, e)
				}
				if (p.preProcess) {
					e = p.preProcess(e)
				}
				$(".pReload", this.pDiv).removeClass("loading");
				this.loading = false;
				if (!e) {
					$(".pPageStat", this.pDiv).html(p.errormsg);
					if (p.onSuccess)
						p.onSuccess(this);
					return false
				}
				if (p.dataType == "xml") {
					p.total = +$("rows total", e).text()
				} else {
					p.total = e.total
				}
				if (p.total === 0) {
					$("tr, a, td, div", t).unbind();
					$(t).empty();
					p.pages = 1;
					p.page = 1;
					this.buildpager();
					$(".pPageStat", this.pDiv).html(p.nomsg);
					if (p.onSuccess)
						p.onSuccess(this);
					return false
				}
				p.pages = Math.ceil(p.total / p.rp);
				if (p.dataType == "xml") {
					p.page = +$("rows page", e).text()
				} else {
					p.page = e.page
				}
				this.buildpager();
				var n = document.createElement("tbody");
				if (p.dataType == "json") {
					$
							.each(
									e.rows,
									function(e, t) {
										var r = document.createElement("tr");
										var i = $(r);
										if (t.name)
											r.name = t.name;
										if (t.color) {
											i.css("background", t.color)
										} else {
											if (e % 2 && p.striped)
												r.className = "erow"
										}
										if (t[p.idProperty]) {
											r.id = "row" + t[p.idProperty];
											i.attr("data-id", t[p.idProperty])
										}
										$("thead tr:first th", g.hDiv)
												.each(
														function() {
															var e = document
																	.createElement("td");
															var n = $(this)
																	.attr(
																			"axis")
																	.substr(3);
															e.align = this.align;
															if (typeof t.cell == "undefined") {
																e.innerHTML = t[p.colModel[n].name]
															} else {
																var i = "";
																if (typeof t.cell[n] != "undefined") {
																	i = t.cell[n] !== null ? t.cell[n]
																			: ""
																} else {
																	i = t.cell[p.colModel[n].name]
																}
																e.innerHTML = p.__mw
																		.datacol(
																				p,
																				$(
																						this)
																						.attr(
																								"abbr"),
																				i)
															}
															var s = e.innerHTML
																	.indexOf("<BGCOLOR=");
															if (s > 0) {
																$(e)
																		.css(
																				"background",
																				text
																						.substr(
																								s + 7,
																								7))
															}
															$(e)
																	.attr(
																			"abbr",
																			$(
																					this)
																					.attr(
																							"abbr"));
															$(r).append(e);
															e = null
														});
										if ($("thead", this.gDiv).length < 1) {
											for (idx = 0; idx < t.cell.length; idx++) {
												var s = document
														.createElement("td");
												if (typeof t.cell[idx] != "undefined") {
													s.innerHTML = t.cell[idx] != null ? t.cell[idx]
															: ""
												} else {
													s.innerHTML = t.cell[p.colModel[idx].name]
												}
												$(r).append(s);
												s = null
											}
										}
										$(n).append(r);
										r = null
									})
				} else if (p.dataType == "xml") {
					var r = 1;
					$("rows row", e).each(
							function() {
								r++;
								var e = document.createElement("tr");
								if ($(this).attr("name"))
									e.name = $(this).attr("name");
								if ($(this).attr("color")) {
									$(e).css("background", $(this).attr("id"))
								} else {
									if (r % 2 && p.striped)
										e.className = "erow"
								}
								var t = $(this).attr("id");
								if (t) {
									e.id = "row" + t
								}
								t = null;
								var s = this;
								$("thead tr:first th", g.hDiv).each(
										function() {
											var t = document
													.createElement("td");
											var n = $(this).attr("axis")
													.substr(3);
											t.align = this.align;
											var r = $("cell:eq(" + n + ")", s)
													.text();
											var i = r.indexOf("<BGCOLOR=");
											if (i > 0) {
												$(t).css("background",
														r.substr(i + 7, 7))
											}
											t.innerHTML = p.__mw.datacol(p, $(
													this).attr("abbr"), r);
											$(t).attr("abbr",
													$(this).attr("abbr"));
											$(e).append(t);
											t = null
										});
								if ($("thead", this.gDiv).length < 1) {
									$("cell", this).each(function() {
										var t = document.createElement("td");
										t.innerHTML = $(this).text();
										$(e).append(t);
										t = null
									})
								}
								$(n).append(e);
								e = null;
								s = null
							})
				}
				$("tr", t).unbind();
				$(t).empty();
				$(t).append(n);
				this.addCellProp();
				this.addRowProp();
				this.rePosDrag();
				n = null;
				e = null;
				r = null;
				if (p.onSuccess) {
					p.onSuccess(this)
				}
				if (p.hideOnSubmit) {
					$(g.block).remove()
				}
				this.hDiv.scrollLeft = this.bDiv.scrollLeft;
				if (browser.opera) {
					$(t).css("visibility", "visible")
				}
			},
			changeSort : function(e) {
				if (this.loading) {
					return true
				}
				$(g.nDiv).hide();
				$(g.nBtn).hide();
				if (p.sortname == $(e).attr("abbr")) {
					if (p.sortorder == "asc") {
						p.sortorder = "desc"
					} else {
						p.sortorder = "asc"
					}
				}
				$(e).addClass("sorted").siblings().removeClass("sorted");
				$(".sdesc", this.hDiv).removeClass("sdesc");
				$(".sasc", this.hDiv).removeClass("sasc");
				$("div", e).addClass("s" + p.sortorder);
				p.sortname = $(e).attr("abbr");
				if (p.onChangeSort) {
					p.onChangeSort(p.sortname, p.sortorder)
				} else {
					this.populate()
				}
			},
			buildpager : function() {
				$(".pcontrol input", this.pDiv).val(p.page);
				$(".pcontrol span", this.pDiv).html(p.pages);
				var e = (p.page - 1) * p.rp + 1;
				var t = e + p.rp - 1;
				if (p.total < t) {
					t = p.total
				}
				var n = p.pagestat;
				n = n.replace(/{from}/, e);
				n = n.replace(/{to}/, t);
				n = n.replace(/{total}/, p.total);
				$(".pPageStat", this.pDiv).html(n)
			},
			populate : function() {
				if (this.loading) {
					return true
				}
				if (p.onSubmit) {
					var e = p.onSubmit();
					if (!e) {
						return false
					}
				}
				this.loading = true;
				if (!p.url) {
					return false
				}
				$(".pPageStat", this.pDiv).html(p.procmsg);
				$(".pReload", this.pDiv).addClass("loading");
				$(g.block).css({
					top : g.bDiv.offsetTop
				});
				if (p.hideOnSubmit) {
					$(this.gDiv).prepend(g.block)
				}
				if (browser.opera) {
					$(t).css("visibility", "hidden")
				}
				if (!p.newp) {
					p.newp = 1
				}
				if (p.page > p.pages) {
					p.page = p.pages
				}
				var n = [ {
					name : "page",
					value : p.newp
				}, {
					name : "rp",
					value : p.rp
				}, {
					name : "sortname",
					value : p.sortname
				}, {
					name : "sortorder",
					value : p.sortorder
				}, {
					name : "query",
					value : p.query
				}, {
					name : "qtype",
					value : p.qtype
				} ];
				if (p.params.length) {
					for (var r = 0; r < p.params.length; r++) {
						n[n.length] = p.params[r]
					}
				}
				$.ajax({
					type : p.method,
					url : p.url,
					data : n,
					dataType : p.dataType,
					success : function(e) {
						g.addData(e)
					},
					error : function(e, t, n) {
						try {
							if (p.onError)
								p.onError(e, t, n)
						} catch (r) {
						}
					}
				})
			},
			doSearch : function() {
				p.query = $("input[name=q]", g.sDiv).val();
				p.qtype = $("select[name=qtype]", g.sDiv).val();
				p.newp = 1;
				this.populate()
			},
			changePage : function(e) {
				if (this.loading) {
					return true
				}
				switch (e) {
				case "first":
					p.newp = 1;
					break;
				case "prev":
					if (p.page > 1) {
						p.newp = parseInt(p.page, 10) - 1
					}
					break;
				case "next":
					if (p.page < p.pages) {
						p.newp = parseInt(p.page, 10) + 1
					}
					break;
				case "last":
					p.newp = p.pages;
					break;
				case "input":
					var t = parseInt($(".pcontrol input", this.pDiv).val(), 10);
					if (isNaN(t)) {
						t = 1
					}
					if (t < 1) {
						t = 1
					} else if (t > p.pages) {
						t = p.pages
					}
					$(".pcontrol input", this.pDiv).val(t);
					p.newp = t;
					break
				}
				if (p.newp == p.page) {
					return false
				}
				if (p.onChangePage) {
					p.onChangePage(p.newp)
				} else {
					this.populate()
				}
			},
			addCellProp : function() {
				$("tbody tr td", g.bDiv).each(function() {
					var e = document.createElement("div");
					var t = $("td", $(this).parent()).index(this);
					var n = $("th:eq(" + t + ")", g.hDiv).get(0);
					if (n != null) {
						if (p.sortname == $(n).attr("abbr") && p.sortname) {
							this.className = "sorted"
						}
						$(e).css({
							textAlign : n.align,
							width : $("div:first", n)[0].style.width
						});
						if (n.hidden) {
							$(this).css("display", "none")
						}
					}
					if (p.nowrap == false) {
						$(e).css("white-space", "normal")
					}
					if (this.innerHTML == "") {
						this.innerHTML = " "
					}
					e.innerHTML = this.innerHTML;
					var r = $(this).parent()[0];
					var i = false;
					if (r.id) {
						i = r.id.substr(3)
					}
					if (n != null) {
						if (n.process)
							n.process(e, i)
					}
					$(this).empty().append(e).removeAttr("width");
					g.addTitleToCell(e)
				})
			},
			getCellDim : function(e) {
				var t = parseInt($(e).height(), 10);
				var n = parseInt($(e).parent().height(), 10);
				var r = parseInt(e.style.width, 10);
				var i = parseInt($(e).parent().width(), 10);
				var s = e.offsetParent.offsetTop;
				var o = e.offsetParent.offsetLeft;
				var u = parseInt($(e).css("paddingLeft"), 10);
				var a = parseInt($(e).css("paddingTop"), 10);
				return {
					ht : t,
					wt : r,
					top : s,
					left : o,
					pdl : u,
					pdt : a,
					pht : n,
					pwt : i
				}
			},
			addRowProp : function() {
				$("tbody tr", g.bDiv).on("click", function(e) {
					var t = e.target || e.srcElement;
					if (t.href || t.type)
						return true;
					if (e.ctrlKey || e.metaKey) {
						return
					}
					$(this).toggleClass("trSelected");
					if (p.singleSelect && !g.multisel) {
						$(this).siblings().removeClass("trSelected")
					}
				}).on("mousedown", function(e) {
					if (e.shiftKey) {
						$(this).toggleClass("trSelected");
						g.multisel = true;
						this.focus();
						$(g.gDiv).noSelect()
					}
					if (e.ctrlKey || e.metaKey) {
						$(this).toggleClass("trSelected");
						g.multisel = true;
						this.focus()
					}
				}).on("mouseup", function(e) {
					if (g.multisel && !(e.ctrlKey || e.metaKey)) {
						g.multisel = false;
						$(g.gDiv).noSelect(false)
					}
				}).on("dblclick", function() {
					$(this).addClass("trSelected");
					if (p.onDoubleClick) {
						p.onDoubleClick(this, g, p)
					}
				}).hover(function(e) {
					if (g.multisel && e.shiftKey) {
						$(this).toggleClass("trSelected")
					}
				}, function() {
				});
				if (browser.msie && browser.version < 7) {
					$(this).hover(function() {
						$(this).addClass("trOver")
					}, function() {
						$(this).removeClass("trOver")
					})
				}
			},
			combo_flag : true,
			combo_resetIndex : function(e) {
				if (this.combo_flag) {
					e.selectedIndex = 0
				}
				this.combo_flag = true
			},
			combo_doSelectAction : function(selObj) {
				eval(selObj.options[selObj.selectedIndex].value);
				selObj.selectedIndex = 0;
				this.combo_flag = false
			},
			addTitleToCell : function(e) {
				if (p.addTitleToCell) {
					var t = $("<span />").css("display", "none"), n = e instanceof jQuery ? e
							: $(e), r = n.outerWidth(), i = 0;
					$("body").children(":first").before(t);
					t.html(n.html());
					t.css("font-size", "" + n.css("font-size"));
					t.css("padding-left", "" + n.css("padding-left"));
					i = t.innerWidth();
					t.remove();
					if (i > r) {
						n.attr("title", n.text())
					} else {
						n.removeAttr("title")
					}
				}
			},
			autoResizeColumn : function(e) {
				if (!p.dblClickResize) {
					return
				}
				var t = $("div", this.cDrag).index(e), n = $(
						"th:visible div:eq(" + t + ")", this.hDiv), r = parseInt(
						e.style.left, 10), i = n.width(), s = 0, o = 0, u = $("<span />");
				$("body").children(":first").before(u);
				u.html(n.html());
				u.css("font-size", "" + n.css("font-size"));
				u.css("padding-left", "" + n.css("padding-left"));
				u.css("padding-right", "" + n.css("padding-right"));
				s = u.width();
				$("tr", this.bDiv).each(function() {
					var e = $("td:visible div:eq(" + t + ")", this), n = 0;
					u.html(e.html());
					u.css("font-size", "" + e.css("font-size"));
					u.css("padding-left", "" + e.css("padding-left"));
					u.css("padding-right", "" + e.css("padding-right"));
					n = u.width();
					s = n > s ? n : s
				});
				u.remove();
				s = p.minWidth > s ? p.minWidth : s;
				o = r + (s - i);
				$("div:eq(" + t + ")", this.cDrag).css("left", o);
				this.colresize = {
					nw : s,
					n : t
				};
				g.dragEnd()
			},
			pager : 0
		};
		g = p.getGridClass(g);
		if (p.colModel) {
			thead = document.createElement("thead");
			var tr = document.createElement("tr");
			for (var i = 0; i < p.colModel.length; i++) {
				var cm = p.colModel[i];
				var th = document.createElement("th");
				$(th).attr("axis", "col" + i);
				if (cm) {
					if ($.cookies) {
						var cookie_width = "flexiwidths/" + cm.name;
						if ($.cookie(cookie_width) != undefined) {
							cm.width = $.cookie(cookie_width)
						}
					}
					if (cm.display != undefined) {
						th.innerHTML = cm.display
					}
					if (cm.name && cm.sortable) {
						$(th).attr("abbr", cm.name)
					}
					if (cm.align) {
						th.align = cm.align
					}
					if (cm.width) {
						$(th).attr("width", cm.width)
					}
					if ($(cm).attr("hide")) {
						th.hidden = true
					}
					if (cm.process) {
						th.process = cm.process
					}
				} else {
					th.innerHTML = "";
					$(th).attr("width", 30)
				}
				$(tr).append(th)
			}
			$(thead).append(tr);
			$(t).prepend(thead)
		}
		g.gDiv = document.createElement("div");
		g.mDiv = document.createElement("div");
		g.hDiv = document.createElement("div");
		g.bDiv = document.createElement("div");
		g.vDiv = document.createElement("div");
		g.rDiv = document.createElement("div");
		g.cDrag = document.createElement("div");
		g.block = document.createElement("div");
		g.nDiv = document.createElement("div");
		g.nBtn = document.createElement("div");
		g.iDiv = document.createElement("div");
		g.tDiv = document.createElement("div");
		g.sDiv = document.createElement("div");
		g.pDiv = document.createElement("div");
		if (p.colResize === false) {
			$(g.cDrag).css("display", "none")
		}
		if (!p.usepager) {
			g.pDiv.style.display = "none"
		}
		g.hTable = document.createElement("table");
		g.gDiv.className = "flexigrid";
		if (p.width != "auto") {
			g.gDiv.style.width = p.width + (isNaN(p.width) ? "" : "px")
		}
		if (browser.msie) {
			$(g.gDiv).addClass("ie")
		}
		if (p.novstripe) {
			$(g.gDiv).addClass("novstripe")
		}
		$(t).before(g.gDiv);
		$(g.gDiv).append(t);
		if (p.buttons) {
			g.tDiv.className = "tDiv";
			var tDiv2 = document.createElement("div");
			tDiv2.className = "tDiv2";
			for (var i = 0; i < p.buttons.length; i++) {
				var btn = p.buttons[i];
				if (!btn.separator) {
					var btnDiv = document.createElement("div");
					btnDiv.className = "fbutton";
					btnDiv.innerHTML = "<div><span>"
							+ (btn.hidename ? " " : btn.name) + "</span></div>";
					if (btn.bclass)
						$("span", btnDiv).addClass(btn.bclass).css({
							paddingLeft : 20
						});
					if (btn.bimage)
						$("span", btnDiv)
								.css(
										"background",
										"url(" + btn.bimage
												+ ") no-repeat center left");
					$("span", btnDiv).css("paddingLeft", 20);
					if (btn.tooltip)
						$("span", btnDiv)[0].title = btn.tooltip;
					btnDiv.onpress = btn.onpress;
					btnDiv.name = btn.name;
					if (btn.id) {
						btnDiv.id = btn.id
					}
					if (btn.onpress) {
						$(btnDiv).click(function() {
							this.onpress(this.id || this.name, g.gDiv)
						})
					}
					$(tDiv2).append(btnDiv);
					if (browser.msie && browser.version < 7) {
						$(btnDiv).hover(function() {
							$(this).addClass("fbOver")
						}, function() {
							$(this).removeClass("fbOver")
						})
					}
				} else {
					$(tDiv2).append("<div class='btnseparator'></div>")
				}
			}
			$(g.tDiv).append(tDiv2);
			$(g.tDiv).append("<div style='clear:both'></div>");
			$(g.gDiv).prepend(g.tDiv)
		}
		g.hDiv.className = "hDiv";
		if (p.combobuttons && $(g.tDiv2)) {
			var btnDiv = document.createElement("div");
			btnDiv.className = "fbutton";
			var tSelect = document.createElement("select");
			$(tSelect).change(function() {
				g.combo_doSelectAction(tSelect)
			});
			$(tSelect).click(function() {
				g.combo_resetIndex(tSelect)
			});
			tSelect.className = "cselect";
			$(btnDiv).append(tSelect);
			for (i = 0; i < p.combobuttons.length; i++) {
				var btn = p.combobuttons[i];
				if (!btn.separator) {
					var btnOpt = document.createElement("option");
					btnOpt.innerHTML = btn.name;
					if (btn.bclass)
						$(btnOpt).addClass(btn.bclass).css({
							paddingLeft : 20
						});
					if (btn.bimage)
						$(btnOpt)
								.css(
										"background",
										"url(" + btn.bimage
												+ ") no-repeat center left");
					$(btnOpt).css("paddingLeft", 20);
					if (btn.tooltip)
						$(btnOpt)[0].title = btn.tooltip;
					if (btn.onpress) {
						btnOpt.value = btn.onpress
					}
					$(tSelect).append(btnOpt)
				}
			}
			$(".tDiv2").append(btnDiv)
		}
		$(t).before(g.hDiv);
		g.hTable.cellPadding = 0;
		g.hTable.cellSpacing = 0;
		$(g.hDiv).append('<div class="hDivBox"></div>');
		$("div", g.hDiv).append(g.hTable);
		var thead = $("thead:first", t).get(0);
		if (thead)
			$(g.hTable).append(thead);
		thead = null;
		if (!p.colmodel)
			var ci = 0;
		$("thead tr:first th", g.hDiv)
				.each(
						function() {
							var e = document.createElement("div");
							if ($(this).attr("abbr")) {
								$(this).click(function(e) {
									if (!$(this).hasClass("thOver"))
										return false;
									var t = e.target || e.srcElement;
									if (t.href || t.type)
										return true;
									g.changeSort(this)
								});
								if ($(this).attr("abbr") == p.sortname) {
									this.className = "sorted";
									e.className = "s" + p.sortorder
								}
							}
							if (this.hidden) {
								$(this).hide()
							}
							if (!p.colmodel) {
								$(this).attr("axis", "col" + ci++)
							}
							if (this.width == "") {
								this.width = 100
							}
							$(e).css({
								textAlign : this.align,
								width : this.width + "px"
							});
							e.innerHTML = this.innerHTML;
							$(this)
									.empty()
									.append(e)
									.removeAttr("width")
									.mousedown(function(e) {
										g.dragStart("colMove", e, this)
									})
									.hover(
											function() {
												if (!g.colresize
														&& !$(this).hasClass(
																"thMove")
														&& !g.colCopy) {
													$(this).addClass("thOver")
												}
												if ($(this).attr("abbr") != p.sortname
														&& !g.colCopy
														&& !g.colresize
														&& $(this).attr("abbr")) {
													$("div", this).addClass(
															"s" + p.sortorder)
												} else if ($(this).attr("abbr") == p.sortname
														&& !g.colCopy
														&& !g.colresize
														&& $(this).attr("abbr")) {
													var e = p.sortorder == "asc" ? "desc"
															: "asc";
													$("div", this).removeClass(
															"s" + p.sortorder)
															.addClass("s" + e)
												}
												if (g.colCopy) {
													var t = $("th", g.hDiv)
															.index(this);
													if (t == g.dcoln) {
														return false
													}
													if (t < g.dcoln) {
														$(this).append(
																g.cdropleft)
													} else {
														$(this).append(
																g.cdropright)
													}
													g.dcolt = t
												} else if (!g.colresize) {
													var n = $("th:visible",
															g.hDiv).index(this);
													var r = parseInt(
															$(
																	"div:eq("
																			+ n
																			+ ")",
																	g.cDrag)
																	.css("left"),
															10);
													var i = jQuery(g.nBtn)
															.outerWidth();
													var s = r
															- i
															+ Math
																	.floor(p.cgwidth / 2);
													$(g.nDiv).hide();
													$(g.nBtn).hide();
													$(g.nBtn).css({
														left : s,
														top : g.hDiv.offsetTop
													}).show();
													var o = parseInt($(g.nDiv)
															.width(), 10);
													$(g.nDiv).css({
														top : g.bDiv.offsetTop
													});
													if (s + o > $(g.gDiv)
															.width()) {
														$(g.nDiv).css("left",
																r - o + 1)
													} else {
														$(g.nDiv)
																.css("left", s)
													}
													if ($(this).hasClass(
															"sorted")) {
														$(g.nBtn).addClass(
																"srtd")
													} else {
														$(g.nBtn).removeClass(
																"srtd")
													}
												}
											},
											function() {
												$(this).removeClass("thOver");
												if ($(this).attr("abbr") != p.sortname) {
													$("div", this).removeClass(
															"s" + p.sortorder)
												} else if ($(this).attr("abbr") == p.sortname) {
													var e = p.sortorder == "asc" ? "desc"
															: "asc";
													$("div", this).addClass(
															"s" + p.sortorder)
															.removeClass(
																	"s" + e)
												}
												if (g.colCopy) {
													$(g.cdropleft).remove();
													$(g.cdropright).remove();
													g.dcolt = null
												}
											})
						});
		g.bDiv.className = "bDiv";
		$(t).before(g.bDiv);
		$(g.bDiv).css({
			height : p.height == "auto" ? "auto" : p.height + "px"
		}).scroll(function(e) {
			g.scroll()
		}).append(t);
		if (p.height == "auto") {
			$("table", g.bDiv).addClass("autoht")
		}
		g.addCellProp();
		g.addRowProp();
		if (p.colResize === true) {
			var cdcol = $("thead tr:first th:first", g.hDiv).get(0);
			if (cdcol !== null) {
				g.cDrag.className = "cDrag";
				g.cdpad = 0;
				g.cdpad += isNaN(parseInt($("div", cdcol)
						.css("borderLeftWidth"), 10)) ? 0 : parseInt($("div",
						cdcol).css("borderLeftWidth"), 10);
				g.cdpad += isNaN(parseInt($("div", cdcol).css(
						"borderRightWidth"), 10)) ? 0 : parseInt(
						$("div", cdcol).css("borderRightWidth"), 10);
				g.cdpad += isNaN(parseInt($("div", cdcol).css("paddingLeft"),
						10)) ? 0 : parseInt($("div", cdcol).css("paddingLeft"),
						10);
				g.cdpad += isNaN(parseInt($("div", cdcol).css("paddingRight"),
						10)) ? 0 : parseInt(
						$("div", cdcol).css("paddingRight"), 10);
				g.cdpad += isNaN(parseInt($(cdcol).css("borderLeftWidth"), 10)) ? 0
						: parseInt($(cdcol).css("borderLeftWidth"), 10);
				g.cdpad += isNaN(parseInt($(cdcol).css("borderRightWidth"), 10)) ? 0
						: parseInt($(cdcol).css("borderRightWidth"), 10);
				g.cdpad += isNaN(parseInt($(cdcol).css("paddingLeft"), 10)) ? 0
						: parseInt($(cdcol).css("paddingLeft"), 10);
				g.cdpad += isNaN(parseInt($(cdcol).css("paddingRight"), 10)) ? 0
						: parseInt($(cdcol).css("paddingRight"), 10);
				$(g.bDiv).before(g.cDrag);
				var cdheight = $(g.bDiv).height();
				var hdheight = $(g.hDiv).height();
				$(g.cDrag).css({
					top : -hdheight + "px"
				});
				$("thead tr:first th", g.hDiv).each(function() {
					var e = document.createElement("div");
					$(g.cDrag).append(e);
					if (!p.cgwidth) {
						p.cgwidth = $(e).width()
					}
					$(e).css({
						height : cdheight + hdheight
					}).mousedown(function(e) {
						g.dragStart("colresize", e, this)
					}).dblclick(function(e) {
						g.autoResizeColumn(this)
					});
					if (browser.msie && browser.version < 7) {
						g.fixHeight($(g.gDiv).height());
						$(e).hover(function() {
							g.fixHeight();
							$(this).addClass("dragging")
						}, function() {
							if (!g.colresize) {
								$(this).removeClass("dragging")
							}
						})
					}
				})
			}
		}
		if (p.striped) {
			$("tbody tr:odd", g.bDiv).addClass("erow")
		}
		if (p.resizable && p.height != "auto") {
			g.vDiv.className = "vGrip";
			$(g.vDiv).mousedown(function(e) {
				g.dragStart("vresize", e)
			}).html("<span></span>");
			$(g.bDiv).after(g.vDiv)
		}
		if (p.resizable && p.width != "auto" && !p.nohresize) {
			g.rDiv.className = "hGrip";
			$(g.rDiv).mousedown(function(e) {
				g.dragStart("vresize", e, true)
			}).html("<span></span>").css("height", $(g.gDiv).height());
			if (browser.msie && browser.version < 7) {
				$(g.rDiv).hover(function() {
					$(this).addClass("hgOver")
				}, function() {
					$(this).removeClass("hgOver")
				})
			}
			$(g.gDiv).append(g.rDiv)
		}
		if (p.usepager) {
			g.pDiv.className = "pDiv";
			g.pDiv.innerHTML = '<div class="pDiv2"></div>';
			$(g.bDiv).after(g.pDiv);
			var html = ' <div class="pGroup"> <div class="pFirst pButton"><span></span></div><div class="pPrev pButton"><span></span></div> </div> <div class="btnseparator"></div> <div class="pGroup"><span class="pcontrol">'
					+ p.pagetext
					+ ' <input type="text" size="4" value="1" /> '
					+ p.outof
					+ ' <span> 1 </span></span></div> <div class="btnseparator"></div> <div class="pGroup"> <div class="pNext pButton"><span></span></div><div class="pLast pButton"><span></span></div> </div> <div class="btnseparator"></div> <div class="pGroup"> <div class="pReload pButton"><span></span></div> </div> <div class="btnseparator"></div> <div class="pGroup"><span class="pPageStat"></span></div>';
			$("div", g.pDiv).html(html);
			$(".pReload", g.pDiv).click(function() {
				g.populate()
			});
			$(".pFirst", g.pDiv).click(function() {
				g.changePage("first")
			});
			$(".pPrev", g.pDiv).click(function() {
				g.changePage("prev")
			});
			$(".pNext", g.pDiv).click(function() {
				g.changePage("next")
			});
			$(".pLast", g.pDiv).click(function() {
				g.changePage("last")
			});
			$(".pcontrol input", g.pDiv).keydown(function(e) {
				if (e.keyCode == 13) {
					g.changePage("input")
				}
			});
			if (browser.msie && browser.version < 7)
				$(".pButton", g.pDiv).hover(function() {
					$(this).addClass("pBtnOver")
				}, function() {
					$(this).removeClass("pBtnOver")
				});
			if (p.useRp) {
				var opt = "", sel = "";
				for (var nx = 0; nx < p.rpOptions.length; nx++) {
					if (p.rp == p.rpOptions[nx])
						sel = 'selected="selected"';
					else
						sel = "";
					opt += "<option value='" + p.rpOptions[nx] + "' " + sel
							+ " >" + p.rpOptions[nx] + "  </option>"
				}
				$(".pDiv2", g.pDiv)
						.prepend(
								"<div class='pGroup'><select name='rp'>"
										+ opt
										+ "</select></div> <div class='btnseparator'></div>");
				$("select", g.pDiv).change(function() {
					if (p.onRpChange) {
						p.onRpChange(+this.value)
					} else {
						p.newp = 1;
						p.rp = +this.value;
						g.populate()
					}
				})
			}
			if (p.searchitems) {
				$(".pDiv2", g.pDiv)
						.prepend(
								"<div class='pGroup'> <div class='pSearch pButton'><span></span></div> </div>  <div class='btnseparator'></div>");
				$(".pSearch", g.pDiv).click(function() {
					$(g.sDiv).slideToggle("fast", function() {
						$(".sDiv:visible input:first", g.gDiv).trigger("focus")
					})
				});
				g.sDiv.className = "sDiv";
				var sitems = p.searchitems;
				var sopt = "", sel = "";
				for (var s = 0; s < sitems.length; s++) {
					if (p.qtype === "" && sitems[s].isdefault === true) {
						p.qtype = sitems[s].name;
						sel = 'selected="selected"'
					} else {
						sel = ""
					}
					sopt += "<option value='" + sitems[s].name + "' " + sel
							+ " >" + sitems[s].display + "  </option>"
				}
				if (p.qtype === "") {
					p.qtype = sitems[0].name
				}
				$(g.sDiv).append(
						"<div class='sDiv2'>" + p.findtext
								+ " <input type='text' value='" + p.query
								+ "' size='30' name='q' class='qsbox' /> "
								+ " <select name='qtype'>" + sopt
								+ "</select></div>");
				$("input[name=q]", g.sDiv).keydown(function(e) {
					if (e.keyCode == 13) {
						g.doSearch()
					}
				});
				$("select[name=qtype]", g.sDiv).keydown(function(e) {
					if (e.keyCode == 13) {
						g.doSearch()
					}
				});
				$("input[value=Clear]", g.sDiv).click(function() {
					$("input[name=q]", g.sDiv).val("");
					p.query = "";
					g.doSearch()
				});
				$(g.bDiv).after(g.sDiv)
			}
		}
		$(g.pDiv, g.sDiv).append("<div style='clear:both'></div>");
		if (p.title) {
			g.mDiv.className = "mDiv";
			g.mDiv.innerHTML = '<div class="ftitle">' + p.title + "</div>";
			$(g.gDiv).prepend(g.mDiv);
			if (p.showTableToggleBtn) {
				$(g.mDiv)
						.append(
								'<div class="ptogtitle" title="Minimize/Maximize Table"><span></span></div>');
				$("div.ptogtitle", g.mDiv).click(function() {
					$(g.gDiv).toggleClass("hideBody");
					$(this).toggleClass("vsble")
				})
			}
		}
		g.cdropleft = document.createElement("span");
		g.cdropleft.className = "cdropleft";
		g.cdropright = document.createElement("span");
		g.cdropright.className = "cdropright";
		g.block.className = "gBlock";
		var gh = $(g.bDiv).height();
		var gtop = g.bDiv.offsetTop;
		$(g.block).css({
			width : g.bDiv.style.width,
			height : gh,
			background : "white",
			position : "relative",
			marginBottom : gh * -1,
			zIndex : 1,
			top : gtop,
			left : "0px"
		});
		$(g.block).fadeTo(0, p.blockOpacity);
		if ($("th", g.hDiv).length) {
			g.nDiv.className = "nDiv";
			g.nDiv.innerHTML = "<table cellpadding='0' cellspacing='0'><tbody></tbody></table>";
			$(g.nDiv).css({
				marginBottom : gh * -1,
				display : "none",
				top : gtop
			}).noSelect();
			var cn = 0;
			$("th div", g.hDiv).each(
					function() {
						var e = $("th[axis='col" + cn + "']", g.hDiv)[0];
						var t = 'checked="checked"';
						if (e.style.display == "none") {
							t = ""
						}
						$("tbody", g.nDiv).append(
								'<tr><td class="ndcol1"><input type="checkbox" '
										+ t + ' class="togCol" value="' + cn
										+ '" /></td><td class="ndcol2">'
										+ this.innerHTML + "</td></tr>");
						cn++
					});
			if (browser.msie && browser.version < 7)
				$("tr", g.nDiv).hover(function() {
					$(this).addClass("ndcolover")
				}, function() {
					$(this).removeClass("ndcolover")
				});
			$("td.ndcol2", g.nDiv).click(
					function() {
						if ($("input:checked", g.nDiv).length <= p.minColToggle
								&& $(this).prev().find("input")[0].checked)
							return false;
						return g.toggleCol($(this).prev().find("input").val())
					});
			$("input.togCol", g.nDiv).click(
					function() {
						if ($("input:checked", g.nDiv).length < p.minColToggle
								&& this.checked === false)
							return false;
						$(this).parent().next().trigger("click")
					});
			$(g.gDiv).prepend(g.nDiv);
			$(g.nBtn).addClass("nBtn").html("<div></div>").attr("title",
					"Cacher/Afficher des colonnes").click(function() {
				$(g.nDiv).toggle();
				return true
			});
			if (p.showToggleBtn) {
				$(g.gDiv).prepend(g.nBtn)
			}
		}
		$(g.iDiv).addClass("iDiv").css({
			display : "none"
		});
		$(g.bDiv).append(g.iDiv);
		$(g.bDiv).hover(function() {
			$(g.nDiv).hide();
			$(g.nBtn).hide()
		}, function() {
			if (g.multisel) {
				g.multisel = false
			}
		});
		$(g.gDiv).hover(function() {
		}, function() {
			$(g.nDiv).hide();
			$(g.nBtn).hide()
		});
		$(document).mousemove(function(e) {
			g.dragMove(e)
		}).mouseup(function(e) {
			g.dragEnd()
		}).hover(function() {
		}, function() {
			g.dragEnd()
		});
		if (browser.msie && browser.version < 7) {
			$(".hDiv,.bDiv,.mDiv,.pDiv,.vGrip,.tDiv, .sDiv", g.gDiv).css({
				width : "100%"
			});
			$(g.gDiv).addClass("ie6");
			if (p.width != "auto") {
				$(g.gDiv).addClass("ie6fullwidthbug")
			}
		}
		g.rePosDrag();
		g.fixHeight();
		t.p = p;
		t.grid = g;
		if (p.url && p.autoload) {
			g.populate()
		}
		return t
	};
	var docloaded = false;
	$(document).ready(function() {
		docloaded = true
	});
	$.fn.flexigrid = function(e) {
		return this.each(function() {
			if (!docloaded) {
				$(this).hide();
				var t = this;
				$(document).ready(function() {
					$.addFlex(t, e)
				})
			} else {
				$.addFlex(this, e)
			}
		})
	};
	$.fn.flexReload = function(e) {
		return this.each(function() {
			if (this.grid && this.p.url)
				this.grid.populate()
		})
	};
	$.fn.flexOptions = function(e) {
		return this.each(function() {
			if (this.grid)
				$.extend(this.p, e)
		})
	};
	$.fn.flexToggleCol = function(e, t) {
		return this.each(function() {
			if (this.grid)
				this.grid.toggleCol(e, t)
		})
	};
	$.fn.flexAddData = function(e) {
		return this.each(function() {
			if (this.grid)
				this.grid.addData(e)
		})
	};
	$.fn.noSelect = function(e) {
		var t = e === null ? true : e;
		if (t) {
			return this.each(function() {
				if (browser.msie || browser.safari)
					$(this).bind("selectstart", function() {
						return false
					});
				else if (browser.mozilla) {
					$(this).css("MozUserSelect", "none");
					$("body").trigger("focus")
				} else if (browser.opera)
					$(this).bind("mousedown", function() {
						return false
					});
				else
					$(this).attr("unselectable", "on")
			})
		} else {
			return this.each(function() {
				if (browser.msie || browser.safari)
					$(this).unbind("selectstart");
				else if (browser.mozilla)
					$(this).css("MozUserSelect", "inherit");
				else if (browser.opera)
					$(this).unbind("mousedown");
				else
					$(this).removeAttr("unselectable", "on")
			})
		}
	};
	$.fn.flexSearch = function(e) {
		return this.each(function() {
			if (this.grid && this.p.searchitems)
				this.grid.doSearch()
		})
	};
	$.fn.selectedRows = function(e) {
		var t = [];
		var n = [];
		var r = $(this.selector + " .trSelected");
		$(r).each(function(e, r) {
			n = [];
			var i = $(r).data("id");
			$.each(r.cells, function(e, t) {
				var r = t.abbr;
				var s = t.firstChild.innerHTML;
				if (s == " ")
					s = "";
				var o = t.cellIndex;
				n.push({
					Column : r,
					Value : s,
					CellIndex : o,
					RowIdentifier : i
				})
			});
			t.push(n)
		});
		return t
	}
})(jQuery)
